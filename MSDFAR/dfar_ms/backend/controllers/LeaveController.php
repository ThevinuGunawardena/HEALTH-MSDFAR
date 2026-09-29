<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Leave;
use backend\components\LeaveMailer;
use backend\models\LeaveApprovalHistory;
use backend\models\LeaveSearch;
use backend\models\ProfileOfficers;
use backend\services\CommonService;
use Yii;
use yii\data\ActiveDataProvider;

use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LeaveController implements the CRUD actions for Leave model.
 *
 * Access summary:
 *  - Employees → create / view / update / delete their own pending leaves
 *  - Director  → approves subordinate staff's leave; can also apply (approved by DG)
 *  - DG        → approves the Director's leave
 *  - Approve/reject is gated per-request by UserTypeUtil::approverTypeFor()
 *
 * The index action has two modes:
 *  - default        → approver sees their scope (all employees);
 *                     employee sees their own records
 *  - ?mode=my       → personal "My Leave Requests" view, scoped to the
 *                     current user's own records EVEN for an approver
 *                     (this is the "Request Leave" sidebar tab).
 */
class LeaveController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'cancel', 'approve', 'reject', 'receipt', 'acting-agree', 'acting-decline', 'dismiss-declined', 'cc-recommend', 'cc-reject', 'export-excel', 'export-pdf','director-dashboard'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'delete'         => ['POST'],
                    'cancel'         => ['POST'],
                    'approve'        => ['POST'],
                    'reject'         => ['POST'],
                    'acting-agree'   => ['POST'],
                    'acting-decline' => ['POST'],
                    'cc-recommend'   => ['POST'],
                    'cc-reject'      => ['POST'],
                    'dismiss-declined' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists Leave records.
     *
     *  Default mode:
     *   - DG       → only Director (999) requests
     *   - Director → everyone below (not DG, not Director)
     *   - employee → only their own
     *
     *  mode=my (personal view, for the "Request Leave" tab):
     *   - anyone   → only their own records, with personal balance cards
     *                and the "+ New Leave Request" button.
     *
     * Casual / Vacation breakdown counts APPROVED leaves, and includes
     * 0.5 day per SHORT / HALF_DAY leave deducted from each bucket.
     */
    public function actionIndex()
    {
        $myMode     = Yii::$app->request->get('mode') === 'my';
        // Approvers under workplace-based routing: the DG, the IT Director
        // (head office) and each district's AD. Directors no longer approve.
        $isApprover = UserTypeUtil::isDG()
            || UserTypeUtil::hasType(Constant::ITD)
            || UserTypeUtil::hasType(Constant::AD);

        // $showAll drives the whole view: all-employees grid vs personal view.
        $showAll = $isApprover && !$myMode;

        // ── Retired page: the all-employees list now lives on the
        //    Director Dashboard. Bounce any approver who lands here
        //    (old links, bookmarks, stat cards) to the dashboard,
        //    carrying through any filter params (e.g. LeaveSearch[status]).
        if ($showAll) {
            $params = Yii::$app->request->queryParams;
            unset($params['mode']);
            return $this->redirect(array_merge(['/leave/director-dashboard'], $params));
        }

        // ── Decide which leave rows this user may see (their "scope") ──
        // Approvers are redirected to the dashboard above, so in practice
        // this is the personal scope; the approver branch is kept (and
        // delegated to scopeForApprover()) so the two never drift apart.
        if ($myMode) {
            // Personal view — always the current user's own records.
            $scope = ['user_id' => Yii::$app->user->id];
        } elseif ($isApprover) {
            $scope = $this->scopeForApprover();
        } else {
            $scope = ['user_id' => Yii::$app->user->id];
        }

        $searchModel          = new LeaveSearch();
        $searchModel->ownOnly = !$showAll;   // force own-records scope in personal view
        $dataProvider         = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere($scope);

        // ── Status summary counts ────────────────────────────────────
        $baseQuery = Leave::find()->andWhere($scope);

        $totalCount    = (clone $baseQuery)->count();
        $draftCount    = (clone $baseQuery)->andWhere(['status' => Leave::STATUS_DRAFT])->count();
        $pendingCount  = (clone $baseQuery)->andWhere(['status' => Leave::STATUS_PENDING])->count();
        $approvedCount = (clone $baseQuery)->andWhere(['status' => Leave::STATUS_APPROVED])->count();
        $rejectedCount = (clone $baseQuery)->andWhere(['status' => Leave::STATUS_REJECTED])->count();

        // ── Acting Requests: DRAFT leaves where current user = acting officer ──
        // Shown in the personal view only, as a prominent "please confirm" section.
        $actingRequests = [];
        if (!$showAll) {
            $actingRequests = Leave::find()
                ->andWhere(['acting_officer_user_id' => Yii::$app->user->id])
                ->andWhere(['status' => Leave::STATUS_DRAFT])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
        }

        // ── Declined leaves belonging to the current applicant ──────────
        // Acting officer declined → applicant sees a notice and must restart.
        // Shown in the personal view only.
        $declinedLeaves = [];
        if (!$showAll) {
            $declinedLeaves = Leave::find()
                ->andWhere(['user_id' => Yii::$app->user->id])
                ->andWhere(['status' => Leave::STATUS_DECLINED])
                ->orderBy(['updated_at' => SORT_DESC])
                ->all();
        }

        // ── CC review queue: PENDING_CC leaves waiting for the CC officer ──
        // CC is a single fixed system-wide role (Constant::CC). Shown only
        // to the user who currently holds that role, on their personal view.
        $ccQueue = [];
        if (UserTypeUtil::hasType(Constant::CC)) {
            $ccQueue = Leave::find()
                ->andWhere(['status' => Leave::STATUS_PENDING_CC])
                ->orderBy(['acting_officer_agreed_at' => SORT_ASC])
                ->all();
        }

        // ── Balance cards: APPROVED ONLY ────────────────────────────────
        // These cards always render for the logged-in user's own records
        // (approvers are redirected to the dashboard above), so balances are
        // computed for the current user.
        $approved = [Leave::STATUS_APPROVED];

        // Casual / Vacation reset every Jan 1, so they MUST be scoped to the
        // current calendar year. We use the same canonical Leave::bucketUsage()
        // that the form hint (buildBalanceData) and the limit validator use,
        // so the card, the live hint, and validation can never drift apart.
        // bucketUsage() already folds in the 0.5/Half-Day-per-bucket charge.
        $userId = Yii::$app->user->id;
        $year   = (int) (new \DateTime('now', new \DateTimeZone('Asia/Colombo')))->format('Y');

        $casualDays = Leave::bucketUsage($userId, Leave::LEAVE_TYPE_CASUAL, $approved, 0, $year);
        $annualDays = Leave::bucketUsage($userId, Leave::LEAVE_TYPE_ANNUAL, $approved, 0, $year);

        // Other informational counts (APPROVED only). These leave types have no
        // annual reset cap, so they are intentionally NOT year-scoped here —
        // revisit if the "which leave types reset yearly" policy changes.
        $sumType = function ($leaveType, array $statuses) use ($scope) {
            return Leave::find()
                ->andWhere($scope)
                ->andWhere(['leave_type' => $leaveType])
                ->andWhere(['in', 'status', $statuses])
                ->sum('total_days') ?: 0;
        };

        $countType = function ($leaveType, array $statuses) use ($scope) {
            return Leave::find()
                ->andWhere($scope)
                ->andWhere(['leave_type' => $leaveType])
                ->andWhere(['in', 'status', $statuses])
                ->count();
        };

        $noPayDays    = $sumType('NO_PAY', $approved);
        $shortCount   = $countType('SHORT', $approved);
        $dutyDays     = $sumType('DUTY', $approved);
        $halfDayCount = $countType('HALF_DAY', $approved);

        // Short Leave is capped at 2 per calendar month. The CARD shows
        // APPROVED short leaves taken this month — a pending request is not
        // added to the card until an approver approves it. (The booking
        // LIMIT still counts approved + pending; see validateShortLeaveMonthlyLimit.)
        $firstOfMonth = date('Y-m-01');
        $lastOfMonth  = date('Y-m-t');
        $shortThisMonth = (int) Leave::find()
            ->andWhere($scope)
            ->andWhere(['leave_type' => 'SHORT'])
            ->andWhere(['status' => Leave::STATUS_APPROVED])
            ->andWhere(['between', 'start_date', $firstOfMonth, $lastOfMonth])
            ->count();

        // ── The applicant's oldest unfinished request ────────────────
        // Surfaced at the top of the personal view so an officer does not
        // have to hunt through the grid to find out where their request is.
        $inProgress     = null;
        $inProgressMore = 0;
        if (!$showAll) {
            $openQuery = Leave::find()
                ->alias('lv')
                ->where(['lv.user_id' => Yii::$app->user->id])
                ->andWhere(['in', 'lv.status', [
                    Leave::STATUS_DRAFT,
                    Leave::STATUS_PENDING_CC,
                    Leave::STATUS_PENDING,
                ]]);

            $openCount = (int) (clone $openQuery)->count();
            if ($openCount > 0) {
                // Oldest first — the one that has been waiting longest.
                $inProgress     = $openQuery->orderBy(['lv.created_at' => SORT_ASC])->one();
                $inProgressMore = $openCount - 1;
            }
        }

        return $this->render('index', [
            'searchModel'    => $searchModel,
            'dataProvider'   => $dataProvider,
            'showAll'        => $showAll,
            'myMode'         => $myMode,
            'totalCount'     => $totalCount,
            'draftCount'     => $draftCount,
            'pendingCount'   => $pendingCount,
            'approvedCount'  => $approvedCount,
            'rejectedCount'  => $rejectedCount,
            'actingRequests' => $actingRequests,
            'declinedLeaves' => $declinedLeaves,
            'ccQueue'        => $ccQueue,
            'casualDays'     => $casualDays,
            'annualDays'     => $annualDays,
            'noPayDays'      => $noPayDays,
            'shortCount'     => $shortCount,
            'shortThisMonth' => $shortThisMonth,
            'dutyDays'       => $dutyDays,
            'halfDayCount'   => $halfDayCount,
            'inProgress'     => $inProgress,
            'inProgressMore' => $inProgressMore,
        ]);
    }

    /**
     * Displays a single Leave model.
     * Viewable by the owner, or by an approver (Director / DG).
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Approvers under workplace-based routing: the DG, the IT Director
        // (head office) and each district's AD. Directors no longer approve.
        $isApprover = UserTypeUtil::isDG()
            || UserTypeUtil::hasType(Constant::ITD)
            || UserTypeUtil::hasType(Constant::AD);

        if (!$isApprover && $model->user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to view this leave request.'));
        }

        // ── Handle the "Director Decision" form (posts here, not to
        //    approve/reject). status-approval = 'approve' | 'reject'. ──
        if ($this->request->isPost && $this->request->post('status-approval') !== null) {
            $decision = $this->request->post('status-approval') === 'reject' ? 'reject' : 'approve';
            $this->recordDecision($model, $decision, $this->request->post('remarks-approval', ''));
            // After a decision, send the approver back to their dashboard.
            return $this->redirect(['/leave/director-dashboard']);
        }

        $approvalHistory = LeaveApprovalHistory::find()
            ->andWhere(['leave_id' => $model->id])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        // The approve/reject form shows only for the person who must approve
        // THIS request: Director for subordinates, DG for a Director's request.
        // The owner can never approve their own request (covers the Director
        // viewing their own leave: approverTypeFor() points to the DG).
        $canApprove = UserTypeUtil::canCurrentUserApprove($model);

        $showApproveBtn = $model->status === Leave::STATUS_PENDING && $canApprove;

        $validated = true;

        return $this->render('view', [
            'model'           => $model,
            'approvalHistory' => $approvalHistory,
            'showApproveBtn'  => $showApproveBtn,
            'validated'       => $validated,
        ]);
    }

    /**
     * Creates a new Leave model.
     * Anyone may apply for leave, including the Director (whose request the
     * DG approves). The DG sits at the top of the chain — see note in
     * UserTypeUtil::approverTypeFor().
     */
    public function actionCreate()
    {
        $model = new Leave();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Auto-fill acting_officer name from the selected user's profile
                if (!empty($model->acting_officer_user_id)) {
                    $actingUser = \common\models\User::findOne((int) $model->acting_officer_user_id);
                    if ($actingUser && $actingUser->profile_id) {
                        $actingProfile = ProfileOfficers::findOne($actingUser->profile_id);
                        if ($actingProfile) {
                            $model->acting_officer = trim(
                                $actingProfile->first_name . ' ' . $actingProfile->last_name
                            );
                        }
                    }
                }
                $this->stampProfileFields($model);
                if ($model->save()) {
                    // Clear any old DECLINED rows for this applicant — they've restarted.
                    Leave::deleteAll([
                        'user_id' => Yii::$app->user->id,
                        'status'  => Leave::STATUS_DECLINED,
                    ]);

                    if (!$model->requiresActingOfficer()) {
                        // No acting-officer stage — Short Leave, or a KKS
                        // applicant. For AD / ITD it is already PENDING (no CC
                        // stage) → notify the approver. For everyone else it is
                        // PENDING_CC → notify the CC officers.
                        if ($model->isCcExempt()) {
                            // Names the ACTUAL approver — the DG for an AD or
                            // ITD, the district AD for district-office staff.
                            // Hardcoding "Director General" here told a Galle
                            // clerk the DG had their request when it was
                            // sitting with Galle's AD.
                            LeaveMailer::sentToApprover($model, false);
                            Yii::$app->session->setFlash('success',
                                Yii::t('app', 'Leave request submitted. Sent to the {who} for approval.', [
                                    'who' => $model->approverLabel(),
                                ])
                            );
                        } else {
                            LeaveMailer::actingAgreed($model);
                            Yii::$app->session->setFlash('success',
                                Yii::t('app', 'Leave request submitted. Sent to the Supervising Officer (CC) for review.')
                            );
                        }
                    } else {
                        // Email the acting officer that their confirmation is needed.
                        LeaveMailer::leaveRequested($model);
                        Yii::$app->session->setFlash('success',
                            Yii::t('app', 'Leave request submitted. Waiting for the acting officer to confirm.')
                        );
                    }
                    return $this->redirect(['index', 'mode' => 'my']);
                }
            }
        } else {
            $model->loadDefaultValues();
            $model->fillUserDetails();
        }

        return $this->render('create', [
            'model'       => $model,
            'balanceData' => $this->buildBalanceData(Yii::$app->user->id),
        ]);
    }

    /**
     * Stamps the official-form fields onto the leave record from the
     * logged-in user's officer profile (profile_officer), linked via
     * User.profile_id. These fields are shown read-only on the form
     * (so they never POST); we copy a snapshot here at save time so the
     * leave row — and its receipt — keeps the values as of the request,
     * independent of later profile edits.
     *
     *  - ministry_dept          ← profile.ministry_dept
     *  - first_appointment_date ← profile.dfar_appointment_date
     *
     * @param Leave $model
     * @return void
     */
    protected function stampProfileFields(Leave $model)
    {
        $profileId = Yii::$app->user->identity->profile_id ?? null;
        if ($profileId && ($profile = ProfileOfficers::findOne($profileId)) !== null) {
            $model->ministry_dept          = $profile->ministry_dept;
            $model->first_appointment_date = $profile->dfar_appointment_date;
        }

        $this->stampLeaveTakenThisYear($model);
    }

    /**
     * Computes this applicant's APPROVED leave taken in the current request's
     * start-date year and stamps the read-only breakdown onto the record:
     *   - taken_casual   : APPROVED CASUAL days + 0.5 per Short/Half-Day
     *                      deducted from the Casual bucket
     *   - taken_vacation : APPROVED ANNUAL days + 0.5 per Short/Half-Day
     *                      deducted from the Vacation bucket
     *   - taken_other    : APPROVED days of every other leave type
     *                      (No Pay, Duty, etc.)
     *
     * "Current year" is the calendar year of this request's start_date.
     * These fields are shown read-only on the form, so they never POST —
     * they are snapshotted here at save time.
     *
     * @param Leave $model
     * @return void
     */
    protected function stampLeaveTakenThisYear(Leave $model)
    {
        $year = $model->start_date
            ? (int) date('Y', strtotime($model->start_date))
            : (int) date('Y');

        $userId   = $model->user_id ?: Yii::$app->user->id;
        $approved = Leave::STATUS_APPROVED;

        // Base: this user's APPROVED leave whose start_date falls in $year.
        $base = function () use ($userId, $approved, $year) {
            return Leave::find()
                ->andWhere(['user_id' => $userId, 'status' => $approved])
                ->andWhere(['between', 'start_date', "$year-01-01", "$year-12-31"]);
        };

        // Sum of total_days for the two paid buckets
        $casualSum = (float) ($base()->andWhere(['leave_type' => 'CASUAL'])->sum('total_days') ?: 0);
        $annualSum = (float) ($base()->andWhere(['leave_type' => 'ANNUAL'])->sum('total_days') ?: 0);

        // 0.5 per Half-Day deducted from each bucket (Short no longer deducts)
        $shortHalf = function ($bucket) use ($base) {
            return (int) $base()
                ->andWhere(['leave_type' => 'HALF_DAY'])
                ->andWhere(['deducted_from' => $bucket])
                ->count();
        };

        // Everything else (No Pay, Duty, …) — excludes the two paid buckets
        // and the Half-Day records already folded into them. Short Leave is
        // tracked separately (per-month cap) and not counted here.
        $otherSum = (float) ($base()
            ->andWhere(['not in', 'leave_type', ['CASUAL', 'ANNUAL', 'SHORT', 'HALF_DAY']])
            ->sum('total_days') ?: 0);

        $model->taken_casual   = $casualSum + (Leave::SHORT_HALF_DAY_CHARGE * $shortHalf('CASUAL'));
        $model->taken_vacation = $annualSum + (Leave::SHORT_HALF_DAY_CHARGE * $shortHalf('ANNUAL'));
        $model->taken_other    = $otherSum;
    }

    /**
     * Builds leave-balance data for the live hint on the form.
     * For each bucket: approved usage, pending usage, max, and remaining.
     * Scoped to a calendar year (balances reset every Jan 1).
     *
     * @param int      $userId
     * @param int|null $year   calendar year (null = current year)
     * @return array
     */
    protected function buildBalanceData($userId, $year = null)
    {
        if ($year === null) {
            $year = (int) (new \DateTime('now', new \DateTimeZone('Asia/Colombo')))->format('Y');
        }

        $buckets = [Leave::LEAVE_TYPE_CASUAL, Leave::LEAVE_TYPE_ANNUAL];
        $data    = [];

        foreach ($buckets as $bucket) {
            $approvedUsed = Leave::bucketUsage($userId, $bucket, [Leave::STATUS_APPROVED], 0, $year);
            $pendingUsed  = Leave::bucketUsage($userId, $bucket, [Leave::STATUS_PENDING], 0, $year);
            $max          = Leave::bucketMax($bucket);

            $data[$bucket] = [
                'approved'  => $approvedUsed,
                'pending'   => $pendingUsed,
                'max'       => $max,
                // Remaining considers approved + pending (the bookable amount)
                'remaining' => max(0, $max - $approvedUsed - $pendingUsed),
            ];
        }

        return $data;
    }

    /**
     * Updates an existing Leave model.
     * Only the owner can update their own pending leave.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to edit this leave request.'));
        }

        if ($model->status !== Leave::STATUS_DRAFT) {
            Yii::$app->session->setFlash('warning',
                Yii::t('app', 'Only draft leave requests (not yet sent for review) can be edited.')
            );
            return $this->redirect(['view', 'id' => $id]);
        }

        if ($this->request->isPost && $model->load($this->request->post())) {
            $this->stampProfileFields($model);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Leave request updated successfully.'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model'       => $model,
            'balanceData' => $this->buildBalanceData(
                $model->user_id,
                !empty($model->start_date) ? (int) date('Y', strtotime($model->start_date)) : null
            ),
        ]);
    }

    /**
     * Deletes an existing Leave model.
     * Only the owner can delete their own pending leave.
     */
    /**
     * Cancels (withdraws) the applicant's own leave request by DELETING it.
     *
     * The row is removed entirely, so a cancelled request leaves no trace in
     * the applicant's history or in anyone's queue. That is safe here only
     * because the window is narrow: cancelling is allowed just while nobody
     * has acted on the request, so no approval decision is ever destroyed.
     *
     * The window itself is defined by Leave::canBeCancelledBy() — before the
     * acting officer agrees, plus Short Leave (which has no acting officer)
     * up until the CC acts.
     *
     * The notification is sent BEFORE the delete, while the row still exists
     * to build the email from — otherwise the acting officer just watches an
     * entry disappear from their queue with no explanation.
     *
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionCancel($id)
    {
        $model = $this->findModel($id);

        if (!$model->canBeCancelledBy(Yii::$app->user->id)) {
            Yii::$app->session->setFlash('warning',
                Yii::t('app', 'This request can no longer be cancelled — it has already been actioned.')
            );
            return $this->redirect(['view', 'id' => $id]);
        }

        // Notify first: after the delete there is nothing left to send.
        LeaveMailer::requestCancelled($model);

        // Clear the audit rows first — a leave_approval_history row pointing
        // at a deleted leave would be an orphan. At this stage there should
        // be none, but a stray row would block the delete under a FK.
        LeaveApprovalHistory::deleteAll(['leave_id' => $model->id]);

        if ($model->delete()) {
            Yii::$app->session->setFlash('success',
                Yii::t('app', 'Leave request cancelled and removed.')
            );
        } else {
            Yii::$app->session->setFlash('danger',
                Yii::t('app', 'Could not cancel the leave request.')
            );
            return $this->redirect(['view', 'id' => $id]);
        }

        // The record no longer exists, so there is no view page to return to.
        return $this->redirect(['index', 'mode' => 'my']);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to delete this leave request.'));
        }

        if ($model->status !== Leave::STATUS_DRAFT) {
            Yii::$app->session->setFlash('danger',
                Yii::t('app', 'Only draft leave requests (not yet sent for review) can be deleted.')
            );
        } else {
            $model->delete();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Leave request deleted.'));
        }

        return $this->redirect(['index', 'mode' => 'my']);
    }

    /**
     * Approves a pending Leave request.
     * Allowed only for the user-type that must approve THIS request:
     * Director for subordinate staff, DG for a Director's request.
     * An approver can never act on their own request.
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        $this->recordDecision($model, 'approve', Yii::$app->request->post('remarks-approval', ''));
        return $this->redirect(['/leave/director-dashboard']);
    }

    /**
     * Rejects a pending Leave request.
     * Allowed only for the user-type that must approve THIS request:
     * Director for subordinate staff, DG for a Director's request.
     * An approver can never act on their own request.
     */
    public function actionReject($id)
    {
        $model = $this->findModel($id);
        $this->recordDecision($model, 'reject', Yii::$app->request->post('remarks-approval', ''));
        return $this->redirect(['/leave/director-dashboard']);
    }

    /**
     * Applies an approve/reject decision to a pending leave request and
     * records it in the approval history. Shared by actionApprove,
     * actionReject and the "Director Decision" form on the view page.
     *
     * Authorization: only the user-type that must approve THIS request
     * (Director for subordinates, DG for a Director's request), and never
     * the request's own owner.
     *
     * @param Leave  $model
     * @param string $decision 'approve' or 'reject'
     * @param string $remarks
     * @return bool true if the decision was applied
     * @throws ForbiddenHttpException
     */
    protected function recordDecision(Leave $model, $decision, $remarks)
    {
        if (!UserTypeUtil::canCurrentUserApprove($model)) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to act on this leave request.'));
        }

        // Approver type is used only to label the notification email below.
        $required = UserTypeUtil::approverTypeForLeave($model);

        if ($model->status !== Leave::STATUS_PENDING) {
            return false;
        }

        $isApprove = ($decision !== 'reject');

        // A rejection without a reason leaves the applicant nothing to act
        // on — they resubmit the same dates and get rejected again. The
        // remark is therefore required on reject, optional on approve.
        if (!$isApprove && trim((string) $remarks) === '') {
            Yii::$app->session->setFlash('error',
                Yii::t('app', 'Please give a reason when rejecting a leave request.')
            );
            return false;
        }

        $model->status      = $isApprove ? Leave::STATUS_APPROVED : Leave::STATUS_REJECTED;
        $model->approved_by = Yii::$app->user->id;
        $model->approved_at = $this->nowSL();
        $model->remarks     = (string) $remarks;
        $model->save(false);

        LeaveApprovalHistory::record(
            $model->id,
            Yii::$app->user->id,
            $isApprove ? 'approve' : 'reject',
            $model->remarks
        );

        // Email the applicant of the final decision, naming the authority
        // that made it.
        //
        // This used to repeat the DG / ITD / else ladder inline, and the
        // "else" printed "Director" — so once district staff started routing
        // to their AD, decision emails told them a Director had acted. One
        // source now: Leave::approverLabel().
        $approverLabel = $model->approverLabel();

        if ($isApprove) {
            LeaveMailer::leaveApproved($model, $approverLabel);
        } else {
            LeaveMailer::leaveRejected($model, $approverLabel);
        }

        Yii::$app->session->setFlash(
            $isApprove ? 'success' : 'danger',
            $isApprove
                ? Yii::t('app', 'Leave request approved.')
                : Yii::t('app', 'Leave request rejected.')
        );

        return true;
    }

    /**
     * Builds the scoped, filtered list of leave rows for export.
     * Mirrors actionIndex / DirectorController scoping so the export
     * matches exactly what the user currently sees (respecting filters).
     *
     * @param bool $personal  true = personal "my" view, false = approver scope
     * @return Leave[]
     */
    protected function exportRows($personal)
    {
        // Scope (same rules as actionIndex / DirectorController)
        if ($personal) {
            $scope = ['user_id' => Yii::$app->user->id];
        } elseif (UserTypeUtil::isDG() || UserTypeUtil::hasType(Constant::AD) || UserTypeUtil::hasType(Constant::ITD)) {
            // Same source of truth as the dashboard (district / head-office aware).
            $scope = $this->scopeForApprover();
        } else {
            $scope = ['user_id' => Yii::$app->user->id];
        }

        $searchModel          = new LeaveSearch();
        $searchModel->ownOnly = $personal;
        $dataProvider         = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere($scope);

        // Approver history excludes draft / declined / pending_cc (same as dashboard)
        if (!$personal) {
            $dataProvider->query->andWhere(['in', 'lv.status', [
                Leave::STATUS_PENDING,
                Leave::STATUS_APPROVED,
                Leave::STATUS_REJECTED,
            ]]);
        }

        // No pagination for export — return ALL matching rows.
        $dataProvider->pagination = false;
        return $dataProvider->getModels();
    }

    /**
     * Assembles the export table as a 2D array (header + rows).
     * Shared by Excel and PDF exporters so both have identical columns.
     *
     * @param Leave[] $rows
     * @param bool    $personal
     * @return array  ['headers' => [...], 'data' => [[...], ...]]
     */
    protected function exportTable(array $rows, $personal)
    {
        $leaveLabels = Leave::leaveTypeOptions();
        $statusLabels = Leave::statusOptions();

        // Personal view has no Officer Name / NIC / Role columns.
        if ($personal) {
            $headers = ['Leave Type', 'From', 'To', 'Days', 'Status', 'Requested At'];
        } else {
            $headers = ['Officer Name', 'Officer NIC', 'Role', 'Leave Type', 'From', 'To', 'Days', 'Status', 'Requested At'];
        }

        $data = [];
        foreach ($rows as $r) {
            $typeLabel = $leaveLabels[$r->leave_type] ?? $r->leave_type;
            if (in_array($r->leave_type, ['SHORT', 'HALF_DAY']) && !empty($r->deducted_from)) {
                $typeLabel .= ' (' . ($r->deducted_from === 'CASUAL' ? 'Casual' : 'Vacation') . ' 0.5)';
            }
            $days   = rtrim(rtrim(number_format((float) $r->total_days, 2), '0'), '.');
            $status = $statusLabels[$r->status] ?? ucfirst($r->status);
            $reqAt  = $r->created_at ? date('M d, Y h:i A', strtotime($r->created_at)) : '';
            $from   = $r->start_date ? date('M d, Y', strtotime($r->start_date)) : '';
            $to     = $r->end_date ? date('M d, Y', strtotime($r->end_date)) : '';

            if ($personal) {
                $data[] = [$typeLabel, $from, $to, $days, $status, $reqAt];
            } else {
                $oName = '—';
                if ($r->user && $r->user->profile_id) {
                    $p = \backend\models\ProfileOfficers::findOne($r->user->profile_id);
                    if ($p) {
                        $n = trim($p->first_name . ' ' . $p->last_name);
                        if ($n !== '') $oName = $n;
                    }
                }
                $oNic  = $r->user ? $r->user->nic : '—';
                $role  = isset(Constant::$userTypes[(int)$r->user_type]['name'])
                    ? Constant::$userTypes[(int)$r->user_type]['name']
                    : $r->user_type;
                $data[] = [$oName, $oNic, $role, $typeLabel, $from, $to, $days, $status, $reqAt];
            }
        }

        return ['headers' => $headers, 'data' => $data];
    }

    /**
     * Exports the leave history to a real .xlsx file.
     * URL: /leave/export-excel  (add ?mode=my for personal history)
     * All current search/filter params are honoured.
     */
    public function actionExportExcel()
    {
        $personal = Yii::$app->request->get('mode') === 'my';
        $rows     = $this->exportRows($personal);
        $table    = $this->exportTable($rows, $personal);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leave History');

        // Header row (bold)
        $col = 1;
        foreach ($table['headers'] as $h) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '1';
            $sheet->setCellValue($cell, $h);
            $col++;
        }
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($table['headers']));
        $sheet->getStyle('A1:' . $lastColLetter . '1')->getFont()->setBold(true);

        // Data rows
        $rowIdx = 2;
        foreach ($table['data'] as $line) {
            $col = 1;
            foreach ($line as $val) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $rowIdx;
                $sheet->setCellValueExplicit(
                    $cell, (string) $val,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
                $col++;
            }
            $rowIdx++;
        }

        // Auto-size columns
        for ($i = 1; $i <= count($table['headers']); $i++) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($letter)->setAutoSize(true);
        }

        $filename = 'leave_history_' . date('Ymd_His') . '.xlsx';

        // Write to a temp file first, then stream it with sendFile().
        // This is the most reliable way to deliver a binary .xlsx in Yii2 —
        // sendFile sets the correct Content-Type, Content-Length and
        // Content-Disposition so Windows/Excel recognises it (avoids the
        // "opens in Notepad" problem caused by mis-set headers).
        $tmpPath = tempnam(sys_get_temp_dir(), 'leavexlsx_');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tmpPath);

        // Free memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return Yii::$app->response->sendFile($tmpPath, $filename, [
            'mimeType'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'inline'    => false,
        ])->on(\yii\web\Response::EVENT_AFTER_SEND, function ($event) use ($tmpPath) {
            // Delete the temp file once the download has been sent.
            if (is_file($tmpPath)) {
                @unlink($tmpPath);
            }
        });
    }

    /**
     * Exports the leave history to a PDF (via mpdf).
     * URL: /leave/export-pdf  (add ?mode=my for personal history)
     * All current search/filter params are honoured.
     */
    public function actionExportPdf()
    {
        $personal = Yii::$app->request->get('mode') === 'my';
        $rows     = $this->exportRows($personal);
        $table    = $this->exportTable($rows, $personal);

        // Build a simple HTML table for mpdf
        $title = $personal
            ? Yii::t('app', 'My Leave History')
            : Yii::t('app', 'All Leave Requests');

        $html  = '<h2 style="font-family:sans-serif;">' . htmlspecialchars($title) . '</h2>';
        $html .= '<div style="font-family:sans-serif;font-size:10px;color:#555;margin-bottom:8px;">'
               . Yii::t('app', 'Generated') . ': ' . date('M d, Y h:i A')
               . ' &nbsp;|&nbsp; ' . count($table['data']) . ' ' . Yii::t('app', 'records')
               . '</div>';
        $html .= '<table border="1" cellspacing="0" cellpadding="5" width="100%" '
               . 'style="border-collapse:collapse;font-family:sans-serif;font-size:10px;">';

        // Header
        $html .= '<thead><tr style="background:#343a40;color:#fff;">';
        foreach ($table['headers'] as $h) {
            $html .= '<th style="text-align:left;">' . htmlspecialchars($h) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        // Rows (zebra striping)
        $i = 0;
        foreach ($table['data'] as $line) {
            $bg = ($i % 2 === 0) ? '#ffffff' : '#f2f2f2';
            $html .= '<tr style="background:' . $bg . ';">';
            foreach ($line as $val) {
                $html .= '<td>' . htmlspecialchars((string) $val) . '</td>';
            }
            $html .= '</tr>';
            $i++;
        }
        $html .= '</tbody></table>';

        $filename = 'leave_history_' . date('Ymd_His') . '.pdf';

        // Landscape for the wide approver table, portrait for personal
        $orientation = $personal ? 'P' : 'L';
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A4-' . $orientation,
            'margin_top'  => 12,
            'margin_left' => 10,
            'margin_right'=> 10,
        ]);
        $mpdf->WriteHTML($html);

        // Write to a temp file, then stream with sendFile() — same reliable
        // delivery approach as the Excel export.
        $tmpPath = tempnam(sys_get_temp_dir(), 'leavepdf_');
        $mpdf->Output($tmpPath, \Mpdf\Output\Destination::FILE);

        return Yii::$app->response->sendFile($tmpPath, $filename, [
            'mimeType' => 'application/pdf',
            'inline'   => false,
        ])->on(\yii\web\Response::EVENT_AFTER_SEND, function ($event) use ($tmpPath) {
            if (is_file($tmpPath)) {
                @unlink($tmpPath);
            }
        });
    }

    /**
     * Renders the printable leave receipt (physical-form overlay).
     *
     * Access + visibility rules:
     *   - Applicant (owner)        → can view once status is PENDING_CC or later.
     *   - CC officer                → can view once status is PENDING_CC or later
     *                                  (their own review queue).
     *   - Director / DG (approver) → can view once status is PENDING or later.
     *   - Anyone else                → forbidden.
     *
     * The view itself (receipt.php) decides how much of the back page to
     * reveal based on $model->status — CC sees only the acting-officer
     * section filled in; everyone sees the full back page once APPROVED.
     *
     * @param int $id  Leave primary key
     * @return string
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionReceipt($id)
    {
        $model = $this->findModel($id);

        $isOwner    = ((int) $model->user_id === (int) Yii::$app->user->id);
        $isCc       = UserTypeUtil::hasType(Constant::CC);
        // Approvers under workplace-based routing: the DG, the IT Director
        // (head office) and each district's AD. Directors no longer approve.
        $isApprover = UserTypeUtil::isDG()
            || UserTypeUtil::hasType(Constant::ITD)
            || UserTypeUtil::hasType(Constant::AD);

        // Earliest possible viewing stage: once the acting officer has
        // agreed, the request has left the applicant-only DRAFT stage.
        $stageReached = !in_array($model->status, [Leave::STATUS_DRAFT, Leave::STATUS_DECLINED]);

        if (!$stageReached) {
            Yii::$app->session->setFlash('warning',
                Yii::t('app', 'The receipt is only available once the acting officer has confirmed.')
            );
            return $this->redirect(['view', 'id' => $id]);
        }

        if (!$isOwner && !$isCc && !$isApprover) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'You are not allowed to view this receipt.')
            );
        }

        $this->layout = false; // print-friendly: no admin chrome

        return $this->render('receipt', [
            'model' => $model,
        ]);
    }

    /**
     * Returns the current datetime in Sri Lanka timezone (Asia/Colombo)
     * as a 'Y-m-d H:i:s' string suitable for MySQL DATETIME columns.
     *
     * @return string
     */
    protected function nowSL()
    {
        return (new \DateTime('now', new \DateTimeZone('Asia/Colombo')))
            ->format('Y-m-d H:i:s');
    }

    /**
     * Acting officer agrees to act for the applicant.
     * Only the selected acting officer may call this.
     * Moves leave from DRAFT to PENDING_CC (CC review queue) and stamps agreed_at.
     */
    public function actionActingAgree($id)
    {
        $model = $this->findModel($id);

        if ((int) $model->acting_officer_user_id !== (int) Yii::$app->user->id) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'You are not the acting officer for this leave request.')
            );
        }

        if ($model->status !== Leave::STATUS_DRAFT) {
            Yii::$app->session->setFlash('warning',
                Yii::t('app', 'This leave request is no longer awaiting your confirmation.')
            );
            return $this->redirect(['index', 'mode' => 'my']);
        }

        $model->acting_officer_agreed_at = $this->nowSL();

        // AD / ITD have no CC stage — the acting officer's agreement sends
        // the request straight to the DG. Everyone else goes to CC review.
        $ccExempt      = $model->isCcExempt();
        $model->status = $ccExempt ? Leave::STATUS_PENDING : Leave::STATUS_PENDING_CC;

        if ($model->save(false)) {
            if ($ccExempt) {
                // No CC stage: straight to whoever approves this request.
                LeaveMailer::sentToApprover($model);
                Yii::$app->session->setFlash('success',
                    Yii::t('app', 'You have agreed to act. The leave request has been sent to the {who} for approval.', [
                        'who' => $model->approverLabel(),
                    ])
                );
            } else {
                // Email all CC officers that a request awaits their review.
                LeaveMailer::actingAgreed($model);
                Yii::$app->session->setFlash('success',
                    Yii::t('app', 'You have agreed to act. The leave request has been sent for the supervising officer to review.')
                );
            }
        } else {
            Yii::$app->session->setFlash('error',
                Yii::t('app', 'Something went wrong. Please try again.')
            );
        }

        return $this->redirect(['index', 'mode' => 'my']);
    }

    /**
     * Acting officer declines to act for the applicant.
     * Resets acting officer fields; leave stays DRAFT so the applicant
     * can choose someone else.
     */
    public function actionActingDecline($id)
    {
        $model = $this->findModel($id);

        if ((int) $model->acting_officer_user_id !== (int) Yii::$app->user->id) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'You are not the acting officer for this leave request.')
            );
        }

        if ($model->status !== Leave::STATUS_DRAFT) {
            Yii::$app->session->setFlash('warning',
                Yii::t('app', 'This leave request is no longer awaiting your confirmation.')
            );
            return $this->redirect(['index', 'mode' => 'my']);
        }

        // Mark as DECLINED (kept briefly so the applicant sees the notice).
        // Clear acting officer so it leaves the acting officer's queue.
        // The remarks marker tells the applicant's banner WHO declined.
        $model->acting_officer_user_id   = null;
        $model->acting_officer           = null;
        $model->acting_officer_agreed_at = null;
        $model->status                   = Leave::STATUS_DECLINED;
        $model->remarks                  = 'declined_by_acting';

        if ($model->save(false)) {
            // Email the applicant that the acting officer declined.
            LeaveMailer::actingDeclined($model);
            Yii::$app->session->setFlash('warning',
                Yii::t('app', 'You have declined this request. The applicant has been notified to submit a new one.')
            );
        }

        return $this->redirect(['index', 'mode' => 'my']);
    }

    /**
     * CC officer recommends a leave request.
     * Only the system's single CC user (user_type = Constant::CC) may call this.
     * Moves leave from PENDING_CC to PENDING (Director queue).
     */
    public function actionCcRecommend($id)
    {
        $model = $this->findModel($id);

        if (!UserTypeUtil::hasType(Constant::CC)) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'Only the CC officer can recommend leave requests.')
            );
        }

        if ($model->status !== Leave::STATUS_PENDING_CC) {
            Yii::$app->session->setFlash('warning',
                Yii::t('app', 'This leave request is no longer awaiting CC review.')
            );
            return $this->redirect(['index', 'mode' => 'my']);
        }

        $model->cc_recommended_by = Yii::$app->user->id;
        $model->cc_recommended_at = $this->nowSL();
        $model->status            = Leave::STATUS_PENDING;

        if ($model->save(false)) {
            // Email the approver — the IT Director for head-office requests,
            // which is all a CC ever sees under workplace-based routing.
            LeaveMailer::ccRecommended($model);
            Yii::$app->session->setFlash('success',
                Yii::t('app', 'You have recommended this request. It has been sent to the {who} for approval.', [
                    'who' => $model->approverLabel(),
                ])
            );
        } else {
            Yii::$app->session->setFlash('error',
                Yii::t('app', 'Something went wrong. Please try again.')
            );
        }

        return $this->redirect(['index', 'mode' => 'my']);
    }

    /**
     * CC officer rejects a leave request before it reaches the approver.
     * Same outcome as an acting officer decline — applicant must restart.
     */
    public function actionCcReject($id)
    {
        $model = $this->findModel($id);

        if (!UserTypeUtil::hasType(Constant::CC)) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'Only the CC officer can reject leave requests.')
            );
        }

        if ($model->status !== Leave::STATUS_PENDING_CC) {
            Yii::$app->session->setFlash('warning',
                Yii::t('app', 'This leave request is no longer awaiting CC review.')
            );
            return $this->redirect(['index', 'mode' => 'my']);
        }

        // Mark as DECLINED — same notice/cleanup flow as acting officer decline.
        // The remarks marker tells the applicant's banner that CC declined.
        $model->cc_recommended_by = null;
        $model->cc_recommended_at = null;
        $model->status            = Leave::STATUS_DECLINED;
        $model->remarks           = 'declined_by_cc';

        if ($model->save(false)) {
            // Email the applicant that the CC rejected.
            LeaveMailer::ccRejected($model);
            Yii::$app->session->setFlash('warning',
                Yii::t('app', 'You have rejected this request. The applicant has been notified to submit a new one.')
            );
        }

        return $this->redirect(['index', 'mode' => 'my']);
    }
    public function actionDismissDeclined($id)
    {
        $model = $this->findModel($id);

        if ((int) $model->user_id !== (int) Yii::$app->user->id) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'You are not allowed to dismiss this request.')
            );
        }

        if ($model->status === Leave::STATUS_DECLINED) {
            $model->delete();
        }

        return $this->redirect(['index', 'mode' => 'my']);
    }

    /**
     * Finds the Leave model based on its primary key value.
     */
    protected function findModel($id)
    {
        if (($model = Leave::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionDirectorDashboard()
    {
        $isAD  = UserTypeUtil::hasType(Constant::AD);
        $isDG  = UserTypeUtil::isDG();
        $isITD = UserTypeUtil::hasType(Constant::ITD);

        // Only the DG, the IT Director or a district AD may open the dashboard.
        if (!$isAD && !$isDG && !$isITD) {
            throw new ForbiddenHttpException(Yii::t('app', 'You are not allowed to access this page.'));
        }

        // ── Scope: which requests this approver is responsible for ──
        $scope = $this->scopeForApprover();

        // ── (3) Pending queue — independent of the search form ──────
        // Its own pageParam so it never clashes with the history grid.
        $pendingDataProvider = new ActiveDataProvider([
            'query' => Leave::find()
                ->andWhere($scope)
                ->andWhere(['status' => Leave::STATUS_PENDING])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize'  => 10,
                'pageParam' => 'pending-page',
            ],
            'sort' => false,
        ]);

        // ── (4 + 5) Searchable history grid (scoped) ────────────────
        // Directors only ever see requests that have been confirmed by the
        // acting officer. DRAFT (awaiting acting officer) and DECLINED
        // (acting officer rejected) belong to the applicant only, so they
        // are excluded from every approver view.
        $visibleStatuses = [
            Leave::STATUS_PENDING,
            Leave::STATUS_APPROVED,
            Leave::STATUS_REJECTED,
        ];

        $searchModel          = new LeaveSearch();
        $searchModel->ownOnly = false;   // approver sees their full scope
        $historyDataProvider  = $searchModel->search(Yii::$app->request->queryParams);
        $historyDataProvider->query
            ->andWhere($scope)
            ->andWhere(['in', 'lv.status', $visibleStatuses]);

        // Distinct page/sort params so this grid and the pending queue
        // (and any other paginator on the page) don't collide.
        $historyDataProvider->pagination->pageParam = 'history-page';
        $historyDataProvider->sort->sortParam       = 'history-sort';

        // ── Status summary counts (scoped) ──────────────────────────
        // Total counts only approver-visible statuses (excludes DRAFT/DECLINED).
        $base          = Leave::find()->andWhere($scope)
                            ->andWhere(['in', 'status', $visibleStatuses]);
        $totalCount    = (clone $base)->count();
        $pendingCount  = (clone $base)->andWhere(['status' => Leave::STATUS_PENDING])->count();
        $approvedCount = (clone $base)->andWhere(['status' => Leave::STATUS_APPROVED])->count();
        $rejectedCount = (clone $base)->andWhere(['status' => Leave::STATUS_REJECTED])->count();

        // ── Approved leave type breakdown (scoped) ──────────────────
        // Current calendar year (Asia/Colombo). The breakdown is scoped to
        // this year so it resets every Jan 1, consistent with the per-user
        // balances and the limit checks.
        $year      = (int) (new \DateTime('now', new \DateTimeZone('Asia/Colombo')))->format('Y');
        $yearStart = "$year-01-01";
        $yearEnd   = "$year-12-31";

        $approvedQ = Leave::find()
            ->andWhere($scope)
            ->andWhere(['status' => Leave::STATUS_APPROVED])
            ->andWhere(['between', 'start_date', $yearStart, $yearEnd]);

        // Each figure is a COUNT of approved requests of that type (not a day
        // total), scoped to the current year via $approvedQ above.
        $leaveTypeBreakdown = [
            'CASUAL'   => (int) (clone $approvedQ)->andWhere(['leave_type' => 'CASUAL'])  ->count(),
            'ANNUAL'   => (int) (clone $approvedQ)->andWhere(['leave_type' => 'ANNUAL'])  ->count(),
            'NO_PAY'   => (int) (clone $approvedQ)->andWhere(['leave_type' => 'NO_PAY'])  ->count(),
            'SHORT'    => (int) (clone $approvedQ)->andWhere(['leave_type' => 'SHORT'])   ->count(),
            'DUTY'     => (int) (clone $approvedQ)->andWhere(['leave_type' => 'DUTY'])    ->count(),
            'HALF_DAY' => (int) (clone $approvedQ)->andWhere(['leave_type' => 'HALF_DAY'])->count(),
        ];

        // ── Coverage gaps (DG only) ─────────────────────────────────
        // A district with no AD account has its leave fall back to the DG.
        // That is a safety net, not a design: without an explanation the DG
        // just sees district staff appearing in a queue that should only
        // hold ADs, Directors and the ITD. This works out WHY, so the DG can
        // have an AD registered for those districts.
        $coverageGaps    = [];
        $unroutableCount = 0;

        if ($isDG) {
            $adDistricts  = UserTypeUtil::districtsWithAd();
            $dgTypes      = array_map('strval', Constant::DG_APPROVAL_TYPES);
            $subordinates = ['not in', 'user_type',
                array_merge([(string) Constant::DG], $dgTypes)];
            $districtPosting = ['or',
                ['applicant_workplace_type' => null],
                ['<>', 'applicant_workplace_type', UserTypeUtil::WORKPLACE_HEAD_OFFICE],
            ];
            $liveStatuses = [
                Leave::STATUS_DRAFT,
                Leave::STATUS_PENDING_CC,
                Leave::STATUS_PENDING,
            ];

            // Requests from districts that have no AD, grouped by district.
            $gapQuery = Leave::find()
                ->alias('lv')
                ->select([
                    'district' => 'lv.applicant_district',
                    'total'    => 'COUNT(*)',
                ])
                ->where($subordinates)
                ->andWhere($districtPosting)
                ->andWhere(['in', 'lv.status', $liveStatuses])
                ->andWhere(['not', ['lv.applicant_district' => null]]);

            if (!empty($adDistricts)) {
                $gapQuery->andWhere(['not in', 'lv.applicant_district', $adDistricts]);
            }

            $districtNames = CommonService::getFIDistrictArray();

            foreach ($gapQuery->groupBy('lv.applicant_district')->asArray()->all() as $row) {
                $coverageGaps[] = [
                    'district' => (int) $row['district'],
                    'name'     => $districtNames[$row['district']]
                        ?? Yii::t('app', 'District #{id}', ['id' => $row['district']]),
                    'total'    => (int) $row['total'],
                ];
            }

            // Separately: requests with no district at all. These are not a
            // missing-AD problem but an incomplete officer profile, and the
            // fix is different — so they are counted apart.
            $unroutableCount = (int) Leave::find()
                ->alias('lv')
                ->where($subordinates)
                ->andWhere($districtPosting)
                ->andWhere(['in', 'lv.status', $liveStatuses])
                ->andWhere(['lv.applicant_district' => null])
                ->count();
        }

        // Dashboard title based on who is logged in
        // Spelled out rather than abbreviated, to match the other two
        // dashboard titles and the labels used elsewhere in the module.
        $dashboardTitle = $isDG
            ? Yii::t('app', 'Director General Dashboard')
            : (UserTypeUtil::hasType(Constant::ITD)
                ? Yii::t('app', 'IT Director Dashboard')
                : Yii::t('app', 'Assistant Director Dashboard'));

        return $this->render('director-dashboard', [
            'searchModel'         => $searchModel,
            'pendingDataProvider' => $pendingDataProvider,
            'historyDataProvider' => $historyDataProvider,
            'totalCount'          => $totalCount,
            'pendingCount'        => $pendingCount,
            'approvedCount'       => $approvedCount,
            'rejectedCount'       => $rejectedCount,
            'leaveTypeBreakdown'  => $leaveTypeBreakdown,
            'dashboardTitle'      => $dashboardTitle,
            // The dashboard is year-scoped but never said so on screen.
            'year'                => $year,
            // DG only — explains why district staff appear in this queue.
            'coverageGaps'        => $coverageGaps,
            'unroutableCount'     => $unroutableCount,
            'isDG'                => $isDG,
        ]);
    }

    protected function scopeForApprover()
    {
        // Roles the DG personally approves: AD (3), IT Director (15), and
        // legacy Director (999) accounts.
        $dgTypes  = array_map('strval', Constant::DG_APPROVAL_TYPES);
        $headOffc = UserTypeUtil::WORKPLACE_HEAD_OFFICE;

        // Subordinate = anyone the DG does not personally approve.
        $subordinates = ['not in', 'user_type',
            array_merge([(string) Constant::DG], $dgTypes)];

        // Anything that is NOT a head-office posting. Rows created before the
        // snapshot column existed hold NULL, and the backfill sets those from
        // the profile — so NULL is treated as district office here.
        $districtPosting = ['or',
            ['applicant_workplace_type' => null],
            ['<>', 'applicant_workplace_type', $headOffc],
        ];

        // ── DG ──────────────────────────────────────────────────────
        if (UserTypeUtil::isDG()) {
            $adDistricts = UserTypeUtil::districtsWithAd();

            // A request from a district with no AD account falls back to the
            // DG, so it has to be VISIBLE here — otherwise it sits at pending
            // with nobody able to action it.
            $noAdDistrict = empty($adDistricts)
                ? ['not', ['id' => null]]          // no ADs anywhere: every district
                : ['or',
                    ['applicant_district' => null],
                    ['not in', 'applicant_district', $adDistricts],
                  ];

            return ['or',
                ['user_type' => $dgTypes],
                ['and', $subordinates, $districtPosting, $noAdDistrict],
            ];
        }

        // ── IT Director: every HEAD OFFICE subordinate ──────────────
        if (UserTypeUtil::hasType(Constant::ITD)) {
            return ['and',
                $subordinates,
                ['applicant_workplace_type' => $headOffc],
            ];
        }

        // ── Assistant Director: district-office staff of THEIR district ──
        // Division does not narrow this: an AD approves for the whole
        // district, whichever division the applicant sits in.
        if (UserTypeUtil::hasType(Constant::AD)) {
            $myDistrict = UserTypeUtil::currentUserDistrict();
            if ($myDistrict === null) {
                return '0=1';   // AD has no district set — fix the profile
            }

            return ['and',
                $subordinates,
                $districtPosting,
                ['applicant_district' => (int) $myDistrict],
            ];
        }

        // Directors no longer approve anyone under workplace-based routing;
        // they are applicants like everyone else.
        return '0=1';
    }
}