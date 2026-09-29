<?php

namespace backend\models;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use Yii;

/**
 * This is the model class for table "leave".
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $nic
 * @property string      $email
 * @property string      $user_type
 * @property string|null $ministry_dept
 * @property string|null $first_appointment_date
 * @property string|null $leave_address
 * @property string|null $acting_officer
 * @property int|null    $acting_officer_user_id
 * @property string|null $acting_officer_agreed_at
 * @property float       $taken_casual
 * @property float       $taken_vacation
 * @property float       $taken_other
 * @property string|null $resume_date
 * @property string      $leave_type
 * @property string|null $session
 * @property string|null $deducted_from
 * @property string      $start_date
 * @property string      $end_date
 * @property float|null  $total_days
 * @property string      $reason
 * @property string      $status
 * @property string|null $remarks
 * @property int|null    $approved_by
 * @property string|null $approved_at
 * @property string      $created_at
 * @property string      $updated_at
 */
class Leave extends \yii\db\ActiveRecord
{
    // ---------------------------------------------------------------
    // Status constants
    // ---------------------------------------------------------------
    const STATUS_DRAFT      = 'draft';        // waiting for acting officer to agree
    const STATUS_DECLINED   = 'declined';     // acting officer or CC declined → applicant must restart
    const STATUS_PENDING_CC = 'pending_cc';   // acting officer agreed → waiting for CC to recommend
    const STATUS_PENDING    = 'pending';      // CC recommended → waiting for Director
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED  = 'cancelled';   // withdrawn by the applicant before anyone acted

    // ---------------------------------------------------------------
    // Leave type constants
    // ---------------------------------------------------------------
    const LEAVE_TYPE_CASUAL   = 'CASUAL';
    const LEAVE_TYPE_ANNUAL   = 'ANNUAL';
    const LEAVE_TYPE_NO_PAY   = 'NO_PAY';
    const LEAVE_TYPE_SHORT    = 'SHORT';
    const LEAVE_TYPE_DUTY     = 'DUTY';
    const LEAVE_TYPE_HALF_DAY = 'HALF_DAY';

    // ---------------------------------------------------------------
    // Leave-bucket limits (days)
    // ---------------------------------------------------------------
    const CASUAL_MAX = 21;
    const ANNUAL_MAX = 24;

    const SHORT_HALF_DAY_CHARGE = 0.5;
    const SHORT_MONTHLY_LIMIT   = 2;

    // ---------------------------------------------------------------
    // Workflow helpers
    // ---------------------------------------------------------------

    /**
     * True if THIS request skips the CC (supervising officer) stage.
     *
     * The Additional Director (3) and IT Director (15) sit above the
     * CC/Director chain, so their leave goes straight from the acting
     * officer's confirmation to the Director General.
     *
     * @return bool
     */
    /**
     * Can the applicant still EDIT this request?
     *
     * The window closes the moment the acting officer agrees, because from
     * that point someone else has committed to covering these exact dates.
     * DRAFT is precisely "submitted, waiting on the acting officer".
     *
     * @param int|null $userId  defaults to the logged-in user
     * @return bool
     */
    public function canBeUpdatedBy($userId = null)
    {
        $userId = $userId ?? Yii::$app->user->id;

        return (int) $this->user_id === (int) $userId
            && $this->status === self::STATUS_DRAFT;
    }

    /**
     * Can the applicant still CANCEL (withdraw) this request?
     *
     * Same window as editing, plus one exception: Short Leave has no acting
     * officer at all, so it is created straight into PENDING_CC and would
     * otherwise never be cancellable. For that case the window runs until
     * the CC acts.
     *
     * @param int|null $userId  defaults to the logged-in user
     * @return bool
     */
    public function canBeCancelledBy($userId = null)
    {
        $userId = $userId ?? Yii::$app->user->id;

        if ((int) $this->user_id !== (int) $userId) {
            return false;
        }

        if ($this->status === self::STATUS_DRAFT) {
            return true;
        }

        // No acting-officer stage exists to close the window (Short Leave, or
        // a KKS applicant), so it runs until the CC acts instead.
        return $this->status === self::STATUS_PENDING_CC
            && !$this->requiresActingOfficer();
    }

    /**
     * True when this request needs an acting officer to confirm cover.
     *
     * Two exemptions:
     *   - Short Leave: too brief for anyone to need to cover it.
     *   - KKS (21): no peer holds the post, so there is nobody to act.
     *
     * An exempt request skips the DRAFT stage entirely and is created
     * straight into CC review — or into the approver's queue when the
     * applicant is also CC-exempt.
     *
     * @return bool
     */
    public function requiresActingOfficer()
    {
        if ($this->leave_type === self::LEAVE_TYPE_SHORT) {
            return false;
        }

        return (int) $this->user_type !== Constant::KKS;
    }

    public function isCcExempt()
    {
        // AD and ITD sit above the CC chain — their leave goes straight to
        // the DG regardless of where they sit.
        if (in_array((int) $this->user_type, Constant::CC_EXEMPT_TYPES, true)) {
            return true;
        }

        // Everyone else: the CC stage now applies to HEAD OFFICE ONLY.
        // District-office staff go straight from their acting officer to the
        // AD of their district.
        return !$this->isHeadOffice();
    }

    /**
     * Human label for whoever gives the FINAL approval on this request.
     * Derived from the routing rules, so it stays correct as those change:
     * Director / AD / ITD applicants -> Director General, head-office
     * subordinates -> IT Director, everyone else -> Director.
     *
     * @return string
     */
    public function approverLabel()
    {
        $type = \backend\config\UserTypeUtil::approverTypeForLeave($this);

        if ($type === Constant::DG) {
            return Yii::t('app', 'Director General');
        }
        if ($type === Constant::ITD) {
            return Yii::t('app', 'IT Director');
        }
        if ($type === Constant::AD) {
            return Yii::t('app', 'Assistant Director');
        }

        // Nothing should reach here: approverTypeForLeave() returns DG, ITD
        // or AD, or null for the DG's own leave. Kept as a neutral catch so a
        // future routing change cannot silently print a wrong job title —
        // which is exactly what happened when the AD branch was missing and
        // district-office requests were labelled "Director".
        return Yii::t('app', 'approving officer');
    }

    /**
     * Builds the approval-workflow steps for the progress timeline.
     *
     * The number of steps varies by request, and steps that never applied
     * are OMITTED rather than greyed out:
     *   - Short Leave has no acting officer      -> that step is dropped
     *   - AD / ITD skip the CC review stage      -> that step is dropped
     * So an ordinary officer sees 4 steps, an AD/ITD sees 3, a short leave
     * sees 3, and an AD/ITD short leave sees 2.
     *
     * Each step: [
     *   'label' => string,
     *   'state' => 'done' | 'current' | 'pending' | 'failed',
     *   'at'    => datetime string|null,
     *   'note'  => string|null,   // shown instead of the timestamp
     * ]
     *
     * @return array
     */
    public function workflowSteps()
    {
        $status    = $this->status;
        $declined  = ($status === self::STATUS_DECLINED);
        $cancelled = ($status === self::STATUS_CANCELLED);
        // "No acting officer required" counts as that stage being passed, so
        // the CC step below can still resolve to failed / cancelled for Short
        // Leave and KKS requests, which never populate acting_officer_agreed_at.
        $agreed    = !$this->requiresActingOfficer()
            || !empty($this->acting_officer_agreed_at);
        $steps     = [];

        // ── 1. Submitted — always complete ───────────────────────────
        $steps[] = [
            'label' => Yii::t('app', 'Submitted'),
            'state' => 'done',
            'at'    => $this->created_at,
            'note'  => null,
        ];

        // ── 2. Acting officer (skipped for Short Leave and for KKS) ──
        if ($this->requiresActingOfficer()) {
            if ($agreed) {
                $state = 'done';
                $note  = null;
            } elseif ($cancelled) {
                // Withdrawn before the acting officer ever responded.
                $state = 'cancelled';
                $note  = Yii::t('app', 'Cancelled');
            } elseif ($declined) {
                // Never agreed AND declined -> the acting officer refused.
                $state = 'failed';
                $note  = Yii::t('app', 'Declined');
            } elseif ($status === self::STATUS_DRAFT) {
                $state = 'current';
                $note  = Yii::t('app', 'Awaiting confirmation');
            } else {
                $state = 'pending';
                $note  = null;
            }

            $steps[] = [
                'label' => Yii::t('app', 'Acting Officer'),
                'state' => $state,
                'at'    => $this->acting_officer_agreed_at,
                'note'  => $note,
            ];
        }

        // ── 3. CC review (AD / ITD skip this stage entirely) ─────────
        if (!$this->isCcExempt()) {
            if (!empty($this->cc_recommended_at)) {
                $state = 'done';
                $note  = null;
            } elseif ($cancelled && $agreed) {
                $state = 'cancelled';
                $note  = Yii::t('app', 'Cancelled');
            } elseif ($declined && $agreed) {
                // Acting officer agreed, then it was declined -> CC rejected.
                $state = 'failed';
                $note  = Yii::t('app', 'Rejected');
            } elseif ($status === self::STATUS_PENDING_CC) {
                $state = 'current';
                $note  = Yii::t('app', 'Under review');
            } else {
                $state = 'pending';
                $note  = null;
            }

            $steps[] = [
                'label' => Yii::t('app', 'CC Review'),
                'state' => $state,
                'at'    => $this->cc_recommended_at,
                'note'  => $note,
            ];
        }

        // ── 4. Final approval ────────────────────────────────────────
        if ($status === self::STATUS_APPROVED) {
            $state = 'done';
            $note  = null;
        } elseif ($status === self::STATUS_REJECTED) {
            $state = 'failed';
            $note  = Yii::t('app', 'Rejected');
        } elseif ($status === self::STATUS_PENDING) {
            $state = 'current';
            // "Pending - 2 days" tells the applicant far more than the
            // status badge alone. Measured from the CC recommendation
            // where there was one, otherwise from the acting officer.
            $since = $this->cc_recommended_at ?: ($this->acting_officer_agreed_at ?: $this->created_at);
            $note  = $this->waitingNote($since);
        } else {
            $state = 'pending';
            $note  = null;
        }

        $steps[] = [
            'label' => $this->approverLabel(),
            'state' => $state,
            'at'    => in_array($status, [self::STATUS_APPROVED, self::STATUS_REJECTED], true)
                ? $this->approved_at
                : null,
            'note'  => $note,
        ];

        // A request that died at the CC never reached the approver, so the
        // steps after a failure must not sit there as innocent pending
        // circles implying they are still coming.
        $failed = false;
        foreach ($steps as $k => $s) {
            if ($failed && $s['state'] === 'pending') {
                $steps[$k]['note'] = Yii::t('app', 'Not reached');
            }
            if ($s['state'] === 'failed' || $s['state'] === 'cancelled') {
                $failed = true;
            }
        }

        return $steps;
    }

    /**
     * Whole days this request has been sitting at its CURRENT waiting stage.
     *
     * Measured from the moment it ARRIVED at that stage, not from creation,
     * so "waiting 6 days" means six days on the approver's desk — which is
     * the number that should prompt action. Returns null when the request
     * is not waiting on anyone (approved, rejected, declined).
     *
     * @return int|null
     */
    public function waitingDays()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                $since = $this->cc_recommended_at
                    ?: ($this->acting_officer_agreed_at ?: $this->created_at);
                break;

            case self::STATUS_PENDING_CC:
                $since = $this->acting_officer_agreed_at ?: $this->created_at;
                break;

            case self::STATUS_DRAFT:
                $since = $this->created_at;
                break;

            default:
                return null;   // finished — nobody is waiting
        }

        if (empty($since)) {
            return null;
        }

        $ts = strtotime($since);
        if ($ts === false) {
            return null;
        }

        return max(0, (int) floor((time() - $ts) / 86400));
    }

    /**
     * "Pending" / "Pending - 3 days" for a step that is currently waiting.
     *
     * @param string|null $since  datetime the wait started (unused; kept for
     *                            backward compatibility with earlier calls)
     * @return string
     */
    protected function waitingNote($since = null)
    {
        $pending = Yii::t('app', 'Pending');
        $days    = $this->waitingDays();

        if ($days === null || $days < 1) {
            return $pending;
        }

        $unit = $days === 1
            ? Yii::t('app', '1 day')
            : Yii::t('app', '{n} days', ['n' => $days]);

        return $pending . ' · ' . $unit;
    }

    public static function tableName()
    {
        return '`leave`';
    }

    public function rules()
    {
        return [
            // Required
            [['user_id', 'nic', 'email', 'user_type', 'leave_type', 'start_date', 'end_date', 'reason'], 'required'],

            // Integer
            [['user_id', 'approved_by', 'acting_officer_user_id', 'cc_recommended_by'], 'integer'],
            [['applicant_district', 'applicant_division'], 'integer'],
            [['applicant_workplace_type'], 'string', 'max' => 50],

            // Number
            [['total_days'], 'number'],

            // Safe
            [['start_date', 'end_date', 'approved_at', 'created_at', 'updated_at',
              'acting_officer_agreed_at', 'cc_recommended_at'], 'safe'],

            // String lengths
            [['nic', 'email'],           'string', 'max' => 255],
            [['session'],                'string', 'max' => 20],
            [['session'],                'in', 'range' => ['MORNING', 'AFTERNOON']],
            [['session'],                'default', 'value' => null],
            [['deducted_from'],          'string', 'max' => 20],
            [['deducted_from'],          'in', 'range' => [self::LEAVE_TYPE_CASUAL, self::LEAVE_TYPE_ANNUAL]],
            [['deducted_from'],          'default', 'value' => null],
            [['user_type'],              'string', 'max' => 20],
            [['ministry_dept'],          'string', 'max' => 255],
            [['leave_address'],          'string', 'max' => 255],
            [['acting_officer'],         'string', 'max' => 255],
            [['leave_address'],          'required'],
            [['taken_casual', 'taken_vacation', 'taken_other'], 'number'],
            [['resume_date'], 'validateResumeDate'],
            [['resume_date'],            'required'],
            [['first_appointment_date'], 'safe'],
            [['leave_type'],             'string', 'max' => 50],
            [['status'],                 'string', 'max' => 20],
            [['reason', 'remarks'],      'string'],
            ['email',                    'email'],

            // acting_officer_user_id is required on create — EXCEPT for Short
            // Leave and for KKS applicants, neither of which has an acting
            // officer stage. The client-side condition cannot see user_type,
            // so for KKS the rule is switched off outright.
            [['acting_officer_user_id'], 'required',
                'when' => function ($model) {
                    return $model->requiresActingOfficer();
                },
                'whenClient' => ((int) $this->user_type === Constant::KKS)
                    ? "function (attribute, value) { return false; }"
                    : "function (attribute, value) {
                        return document.getElementById('leave-leave-type').value !== 'SHORT';
                    }",
                'message' => Yii::t('app', 'Please select an acting officer.'),
            ],

            // Default status → DRAFT (acting officer must agree first)
            [['status'], 'default', 'value' => self::STATUS_DRAFT],

            // End date must be >= start date
            [['end_date'], 'compare', 'compareAttribute' => 'start_date',
             'operator' => '>=', 'type' => 'date'],

            // Leave type must be known
            [['leave_type'], 'in', 'range' => array_keys(self::leaveTypeOptions())],

            // deducted_from required for HALF_DAY
            [['deducted_from'], 'required',
                'when' => function ($model) {
                    return $model->leave_type === self::LEAVE_TYPE_HALF_DAY;
                },
                'whenClient' => "function (attribute, value) {
                    return document.getElementById('leave-leave-type').value === 'HALF_DAY';
                }",
                'message' => Yii::t('app', 'Please select which leave bucket this deducts from.'),
            ],

            // Validators
            [['leave_type'], 'validateLeaveLimit'],
            [['leave_type'], 'validateShortLeaveMonthlyLimit'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                       => Yii::t('app', 'ID'),
            'user_id'                  => Yii::t('app', 'Employee'),
            'nic'                      => Yii::t('app', 'NIC'),
            'email'                    => Yii::t('app', 'Email'),
            'user_type'                => Yii::t('app', 'Designation'),
            'ministry_dept'            => Yii::t('app', 'Ministry / Department'),
            'first_appointment_date'   => Yii::t('app', 'Date of First Appointment'),
            'leave_address'            => Yii::t('app', 'Address when on Leave'),
            'acting_officer'           => Yii::t('app', 'Acting Officer'),
            'acting_officer_user_id'   => Yii::t('app', 'Acting Officer'),
            'acting_officer_agreed_at' => Yii::t('app', 'Acting Officer Agreed At'),
            'applicant_district'       => Yii::t('app', 'Applicant District'),
            'applicant_division'       => Yii::t('app', 'Applicant Division'),
            'applicant_workplace_type' => Yii::t('app', 'Applicant Workplace Type'),
            'cc_recommended_by'       => Yii::t('app', 'Recommended By (CC)'),
            'cc_recommended_at'       => Yii::t('app', 'CC Recommended At'),
            'taken_casual'             => Yii::t('app', 'Casual Leave Taken (This Year)'),
            'taken_vacation'           => Yii::t('app', 'Vacation Leave Taken (This Year)'),
            'taken_other'              => Yii::t('app', 'Other Leave Taken (This Year)'),
            'resume_date'              => Yii::t('app', 'Date of Resuming Duties'),
            'leave_type'               => Yii::t('app', 'Leave Type'),
            'session'                  => Yii::t('app', 'Session'),
            'deducted_from'            => Yii::t('app', 'Deduct From'),
            'start_date'               => Yii::t('app', 'Start Date'),
            'end_date'                 => Yii::t('app', 'End Date'),
            'total_days'               => Yii::t('app', 'Total Days'),
            'reason'                   => Yii::t('app', 'Reason'),
            'status'                   => Yii::t('app', 'Status'),
            'remarks'                  => Yii::t('app', 'Remarks'),
            'approved_by'              => Yii::t('app', 'Approved By'),
            'approved_at'              => Yii::t('app', 'Approved At'),
            'created_at'               => Yii::t('app', 'Created At'),
            'updated_at'               => Yii::t('app', 'Updated At'),
        ];
    }

    // ---------------------------------------------------------------
    // Relations
    // ---------------------------------------------------------------

    public function getUser()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'user_id']);
    }

    public function getApprover()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'approved_by']);
    }

    public function getApprovalHistory()
    {
        return $this->hasMany(LeaveApprovalHistory::class, ['leave_id' => 'id'])
                    ->orderBy(['id' => SORT_ASC]);
    }

    /**
     * The User record of the selected acting officer.
     */
    public function getActingOfficerUser()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'acting_officer_user_id']);
    }

    /**
     * The ProfileOfficers record of the acting officer (for name + signature).
     */
    public function getActingOfficerProfile()
    {
        return $this->hasOne(ProfileOfficers::class, ['id' => 'profile_id'])
                    ->via('actingOfficerUser');
    }

    /**
     * The User record of the CC officer who recommended this leave.
     */
    public function getCcRecommendedByUser()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'cc_recommended_by']);
    }

    // ---------------------------------------------------------------
    // Options / Helpers
    // ---------------------------------------------------------------

    public static function leaveTypeOptions()
    {
        return [
            self::LEAVE_TYPE_CASUAL   => Yii::t('app', 'Casual Leave'),
            self::LEAVE_TYPE_ANNUAL   => Yii::t('app', 'Vacation Leave'),
            self::LEAVE_TYPE_NO_PAY   => Yii::t('app', 'No Pay Leave'),
            self::LEAVE_TYPE_SHORT    => Yii::t('app', 'Short Leave'),
            self::LEAVE_TYPE_DUTY     => Yii::t('app', 'Duty Leave'),
            self::LEAVE_TYPE_HALF_DAY => Yii::t('app', 'Half Day Leave'),
        ];
    }

    public static function statusOptions()
    {
        return [
            self::STATUS_DRAFT      => Yii::t('app', 'Draft'),
            self::STATUS_DECLINED   => Yii::t('app', 'Declined'),
            self::STATUS_PENDING_CC => Yii::t('app', 'Pending CC Review'),
            self::STATUS_PENDING    => Yii::t('app', 'Pending'),
            self::STATUS_APPROVED => Yii::t('app', 'Approved'),
            self::STATUS_REJECTED => Yii::t('app', 'Rejected'),
            self::STATUS_CANCELLED  => Yii::t('app', 'Cancelled'),
        ];
    }

    public function getUserTypeName()
    {
        $type = (int) $this->user_type;
        return Constant::$userTypes[$type]['name'] ?? 'Unknown';
    }

    public function getStatusBadge()
    {
        $map = [
            self::STATUS_DRAFT      => 'badge badge-secondary',
            self::STATUS_DECLINED   => 'badge badge-danger',
            self::STATUS_PENDING_CC => 'badge badge-info',
            self::STATUS_PENDING    => 'badge badge-warning',
            self::STATUS_APPROVED => 'badge badge-success',
            self::STATUS_REJECTED => 'badge badge-danger',
            self::STATUS_CANCELLED  => 'badge badge-secondary',
        ];
        return $map[$this->status] ?? 'badge badge-secondary';
    }

    // ---------------------------------------------------------------
    // Auto-fill user details
    // ---------------------------------------------------------------

    public function fillUserDetails()
    {
        $user = Yii::$app->user->identity;
        if ($user) {
            $this->user_id   = $user->id;
            $this->nic       = $user->nic;
            $this->email     = $user->email;
            $this->user_type = $user->type;

            $this->snapshotPosting($user->profile_id);
        }
    }

    /**
     * Copies WHERE the applicant works onto the leave row.
     *
     * Approval routing depends on this, and holding it here means the grids
     * can resolve an approver without joining profile_officer for every row
     * — and that a transfer mid-approval cannot move a pending request to
     * another district's AD.
     *
     * Called from beforeSave() on INSERT rather than relying on the form,
     * because these three fields have no hidden inputs: fillUserDetails()
     * runs only when the empty form is rendered, and user_id / nic / email /
     * user_type survive the POST only because the form echoes them back as
     * hidden fields. Anything not echoed back was silently lost, which left
     * every new request with a NULL posting — and so treated as district
     * office, skipping the CC stage and falling through to the DG.
     *
     * @param int|null $profileId  defaults to the logged-in user's profile
     * @return void
     */
    public function snapshotPosting($profileId = null)
    {
        if ($profileId === null) {
            // Prefer the applicant on the record over the logged-in user, so
            // this stays correct if a request is ever created on someone
            // else's behalf.
            $user = $this->user_id
                ? \common\models\User::findOne((int) $this->user_id)
                : Yii::$app->user->identity;

            $profileId = $user->profile_id ?? null;
        }

        if (empty($profileId)) {
            return;
        }

        $profile = ProfileOfficers::findOne($profileId);
        if (!$profile) {
            return;
        }

        $this->applicant_district       = $profile->district;
        $this->applicant_division       = $profile->division;
        $this->applicant_workplace_type = $profile->current_workplace_type;
    }

    // ---------------------------------------------------------------
    // Posting helpers
    // ---------------------------------------------------------------

    /**
     * True when this request was raised by a HEAD OFFICE officer.
     *
     * Head office is the only posting that still passes through the CC
     * stage, and the only one approved by the IT Director.
     *
     * @return bool
     */
    public function isHeadOffice()
    {
        return trim((string) $this->applicant_workplace_type)
            === UserTypeUtil::WORKPLACE_HEAD_OFFICE;
    }

    // ---------------------------------------------------------------
    // Calculate total days
    // ---------------------------------------------------------------

    public function calculateTotalDays()
    {
        if ($this->leave_type === self::LEAVE_TYPE_SHORT) {
            $this->total_days = 1;
            return;
        }
        if ($this->leave_type === self::LEAVE_TYPE_HALF_DAY) {
            $this->total_days = self::SHORT_HALF_DAY_CHARGE;
            return;
        }
        if ($this->start_date && $this->end_date) {
            $start            = new \DateTime($this->start_date);
            $end              = new \DateTime($this->end_date);
            $diff             = $start->diff($end);
            $this->total_days = $diff->days + 1;
        }
    }

    // ---------------------------------------------------------------
    // Bucket usage helper
    // ---------------------------------------------------------------

    /**
     * Total days used from a leave bucket (Casual / Vacation) for a user,
     * scoped to a single calendar YEAR.
     *
     * Balances reset every January 1st, so usage is counted only for leave
     * whose start_date falls within $year. If $year is null, the current
     * year is used.
     *
     * @param int        $userId
     * @param string     $bucket    LEAVE_TYPE_CASUAL or LEAVE_TYPE_ANNUAL
     * @param array      $statuses  e.g. [STATUS_APPROVED, STATUS_PENDING]
     * @param int        $excludeId  leave id to exclude (the one being edited)
     * @param int|null   $year       calendar year to scope to (null = current year)
     * @return float
     */
    public static function bucketUsage($userId, $bucket, array $statuses, $excludeId = 0, $year = null)
    {
        // Default to the current calendar year (Asia/Colombo).
        if ($year === null) {
            $year = (int) (new \DateTime('now', new \DateTimeZone('Asia/Colombo')))->format('Y');
        }
        $yearStart = sprintf('%04d-01-01', $year);
        $yearEnd   = sprintf('%04d-12-31', $year);

        $direct = self::find()
            ->where(['user_id' => $userId, 'leave_type' => $bucket])
            ->andWhere(['in', 'status', $statuses])
            ->andWhere(['<>', 'id', $excludeId ?: 0])
            ->andWhere(['between', 'start_date', $yearStart, $yearEnd])
            ->sum('total_days') ?: 0;

        $shortHalfCount = self::find()
            ->where(['user_id' => $userId, 'deducted_from' => $bucket])
            ->andWhere(['leave_type' => self::LEAVE_TYPE_HALF_DAY])
            ->andWhere(['in', 'status', $statuses])
            ->andWhere(['<>', 'id', $excludeId ?: 0])
            ->andWhere(['between', 'start_date', $yearStart, $yearEnd])
            ->count();

        return (float) $direct + (self::SHORT_HALF_DAY_CHARGE * (int) $shortHalfCount);
    }

    public static function bucketMax($bucket)
    {
        return ($bucket === self::LEAVE_TYPE_CASUAL) ? self::CASUAL_MAX : self::ANNUAL_MAX;
    }

    // ---------------------------------------------------------------
    // Validators
    // ---------------------------------------------------------------

    public function validateLeaveLimit($attribute, $params)
    {
        if (!$this->user_id) return;

        $bucket = null;
        $charge = 0;

        if ($this->leave_type === self::LEAVE_TYPE_CASUAL) {
            $bucket = self::LEAVE_TYPE_CASUAL;
            $charge = (float) ($this->total_days ?: 0);
        } elseif ($this->leave_type === self::LEAVE_TYPE_ANNUAL) {
            $bucket = self::LEAVE_TYPE_ANNUAL;
            $charge = (float) ($this->total_days ?: 0);
        } elseif ($this->leave_type === self::LEAVE_TYPE_HALF_DAY) {
            if (!$this->deducted_from) return;
            $bucket = $this->deducted_from;
            $charge = self::SHORT_HALF_DAY_CHARGE;
        } else {
            return;
        }

        // Year the leave is taken in (balances are per calendar year).
        $leaveYear = !empty($this->start_date)
            ? (int) date('Y', strtotime($this->start_date))
            : (int) (new \DateTime('now', new \DateTimeZone('Asia/Colombo')))->format('Y');

        $usedDays = self::bucketUsage(
            $this->user_id, $bucket,
            [self::STATUS_APPROVED, self::STATUS_PENDING],
            $this->id ?: 0,
            $leaveYear
        );

        $newTotal  = $usedDays + $charge;
        $max       = self::bucketMax($bucket);
        $bucketLbl = ($bucket === self::LEAVE_TYPE_CASUAL) ? 'Casual Leave' : 'Vacation Leave';

        if ($newTotal > $max) {
            $remaining = max(0, $max - $usedDays);
            $errAttr   = ($this->leave_type === self::LEAVE_TYPE_HALF_DAY) ? 'deducted_from' : $attribute;
            $this->addError($errAttr, Yii::t('app',
                '{bucket} limit exceeded. You have only {remaining} day(s) remaining out of {max} days (including pending requests).',
                ['bucket' => Yii::t('app', $bucketLbl), 'remaining' => $remaining, 'max' => $max]
            ));
        }
    }

    public function validateShortLeaveMonthlyLimit($attribute, $params)
    {
        if ($this->leave_type !== self::LEAVE_TYPE_SHORT || !$this->user_id || !$this->start_date) return;

        $year  = (int) date('Y', strtotime($this->start_date));
        $month = (int) date('m', strtotime($this->start_date));
        $first = sprintf('%04d-%02d-01', $year, $month);
        $last  = date('Y-m-t', strtotime($first));

        $count = (int) self::find()
            ->andWhere(['user_id' => $this->user_id, 'leave_type' => self::LEAVE_TYPE_SHORT])
            ->andWhere(['in', 'status', [self::STATUS_APPROVED, self::STATUS_PENDING]])
            ->andWhere(['between', 'start_date', $first, $last])
            ->andWhere(['<>', 'id', $this->id ?: 0])
            ->count();

        if ($count >= self::SHORT_MONTHLY_LIMIT) {
            $this->addError($attribute, Yii::t('app',
                'Short Leave limit reached: only {n} short leaves are allowed per month (including pending requests).',
                ['n' => self::SHORT_MONTHLY_LIMIT]
            ));
        }
    }

    // ---------------------------------------------------------------
    // Lifecycle hooks
    // ---------------------------------------------------------------

    /**
     * Compute total_days BEFORE the validators run.
     *
     * total_days is otherwise only set in beforeSave(), which runs AFTER
     * validation. validateLeaveLimit() charges the Casual / Vacation bucket
     * using $this->total_days, so without this hook the charge would be
     * whatever the form happened to post (often 0 / stale), letting a
     * full-day request slip past the limit. Computing it here makes the
     * value authoritative for the whole validation pass and overrides any
     * stale or tampered posted value before the limit is checked.
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }
        $this->calculateTotalDays();
        return true;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->calculateTotalDays();

            if ($this->leave_type !== self::LEAVE_TYPE_HALF_DAY) {
                $this->deducted_from = null;
                $this->session       = null;
            }

            $now = (new \DateTime('now', new \DateTimeZone('Asia/Colombo')))->format('Y-m-d H:i:s');

           if ($insert) {
                $this->created_at = $now;

                // Must happen BEFORE the status is chosen below: isCcExempt()
                // reads applicant_workplace_type to decide whether this
                // request passes through the CC stage at all.
                $this->snapshotPosting();
                // Short Leave has no acting officer — skip the DRAFT
                // (acting-officer confirmation) stage. It normally goes to
                // CC review, but AD / ITD skip CC entirely, so theirs lands
                // directly in the DG's pending queue. Everything else still
                // starts as DRAFT.
                if (!$this->requiresActingOfficer()) {
                    $this->status = $this->isCcExempt()
                        ? self::STATUS_PENDING
                        : self::STATUS_PENDING_CC;
                } else {
                    $this->status = self::STATUS_DRAFT;
                }
            }
            $this->updated_at = $now;
            return true;
        }
        return false;
    }

    public function validateResumeDate($attribute, $params)
{
    if (empty($this->resume_date) || empty($this->end_date)) {
        return;
    }

    // Half-day / short leave: resume the same day, so equal is allowed.
    $sameDayTypes = [self::LEAVE_TYPE_HALF_DAY, self::LEAVE_TYPE_SHORT];
    $mustBeAfter  = !in_array($this->leave_type, $sameDayTypes, true);

    if ($mustBeAfter && $this->resume_date <= $this->end_date) {
        $this->addError($attribute, Yii::t('app',
            'Resume date must be after the leave end date.'));
    } elseif (!$mustBeAfter && $this->resume_date < $this->end_date) {
        $this->addError($attribute, Yii::t('app',
            'Resume date cannot be before the leave end date.'));
    }
}
}