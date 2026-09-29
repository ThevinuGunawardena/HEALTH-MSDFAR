<?php

namespace backend\components;

use backend\config\Constant;
use backend\models\Leave;
use backend\models\ProfileOfficers;
use common\models\User;
use Yii;

/**
 * LeaveMailer — centralised email notifications for the leave workflow.
 *
 * One static method per workflow event. Each builds a styled HTML email
 * from the shared template (mail/leave/_layout) and sends it via the
 * configured Yii mailer component (Yii::$app->mailer).
 *
 * Recipient emails are read from the `user` table (User->email).
 *
 * All sends are wrapped in try/catch so a mail failure never blocks the
 * leave action itself — a failed email is logged, not fatal.
 *
 * Usage from a controller:
 *   \backend\components\LeaveMailer::leaveRequested($model);
 *   \backend\components\LeaveMailer::actingAgreed($model);
 *   ... etc.
 */
class LeaveMailer
{
    /**
     * The "from" address shown on every email.
     * Adjust to your department's address once SMTP is configured.
     */
    const FROM_EMAIL = 'departure1@fisheriesdept.gov.lk';
    const FROM_NAME  = 'DFAR Leave Management System';

    /**
     * Fallback site root for the links in these emails.
     *
     * Override per environment in config/params.php:
     *
     *     'leaveBaseUrl' => 'https://msdfar.com',                        // live
     *     'leaveBaseUrl' => 'http://localhost/dfar_ms/backend/web',      // local
     *
     * Deliberately NOT built with Url::to([...], true): an absolute URL needs
     * urlManager.hostInfo, and mail sent from a console command or cron has no
     * request to infer the host from. A plain param is predictable everywhere.
     */
    const DEFAULT_BASE_URL = 'https://msdfar.com';

    // Badge colors (light background / dark text)
    const COLOR_GREEN_BG   = '#dcfce7';
    const COLOR_GREEN_TEXT = '#15803d';
    const COLOR_RED_BG     = '#fee2e2';
    const COLOR_RED_TEXT   = '#b91c1c';
    const COLOR_BLUE_BG    = '#eef2ff';
    const COLOR_BLUE_TEXT  = '#1a56db';

    // ---------------------------------------------------------------
    // Links
    // ---------------------------------------------------------------

    /**
     * Site root, without a trailing slash.
     *
     * @return string
     */
    protected static function baseUrl()
    {
        $base = Yii::$app->params['leaveBaseUrl'] ?? self::DEFAULT_BASE_URL;

        return rtrim((string) $base, '/');
    }

    /**
     * Absolute URL for a path inside the system.
     *
     * Links point at the DESTINATION rather than at /login. Every leave action
     * is behind AccessControl with roles ['@'], so a signed-out recipient is
     * bounced to the login page with returnUrl remembered, and lands on the
     * request itself once they sign in — one tap from the email to the
     * decision screen instead of email → login → dashboard → hunt for it.
     *
     * @param string $path e.g. '/leave/view?id=15'
     * @return string
     */
    protected static function url($path)
    {
        return self::baseUrl() . '/' . ltrim($path, '/');
    }

    /** Link to one request's page. */
    protected static function requestUrl(Leave $model)
    {
        return self::url('leave/view?id=' . (int) $model->id);
    }

    /**
     * Link to the personal leave page.
     *
     * The acting-officer and CC queues are panels on THIS page, not on the
     * request page — so anyone who has to act from a queue is sent here.
     */
    protected static function queueUrl()
    {
        return self::url('leave/index?mode=my');
    }

    // ---------------------------------------------------------------
    // Public event methods
    // ---------------------------------------------------------------

    /**
     * Event 1 — A new leave request was submitted (DRAFT).
     * Notify the selected acting officer that their confirmation is needed.
     */
    public static function leaveRequested(Leave $model)
    {
        $actingEmail = self::userEmail($model->acting_officer_user_id);
        if (!$actingEmail) {
            return;
        }

        self::send(
            $actingEmail,
            Yii::t('app', 'Acting Officer Confirmation Needed — Leave Request'),
            'You have been selected as the acting officer for the leave request below. '
            . 'Please log in to the system to confirm or decline.',
            $model,
            [self::blueBadge('Action needed:'), self::greenBadge('Agree'), self::orRedBadge('Decline')],
            self::queueUrl(),
            Yii::t('app', 'Open the request')
        );
    }

    /**
     * Event 2 — Acting officer agreed → PENDING_CC.
     * Notify all CC officers that a request awaits their review.
     */
    public static function actingAgreed(Leave $model)
    {
        $ccEmails = self::emailsByType(Constant::CC);
        if (empty($ccEmails)) {
            return;
        }

        self::send(
            $ccEmails,
            Yii::t('app', 'Leave Request Awaiting Your Review'),
            'The acting officer has confirmed the leave request below. '
            . 'It now requires your review. Please log in to recommend or reject it.',
            $model,
            [self::blueBadge('Action needed:'), self::greenBadge('Recommend'), self::orRedBadge('Reject')],
            self::queueUrl(),
            Yii::t('app', 'Review the request')
        );
    }

    /**
     * Event 3 — Acting officer declined → DECLINED.
     * Notify the applicant.
     */
    public static function actingDeclined(Leave $model)
    {
        $to = self::applicantEmail($model);
        if (empty($to)) {
            return;
        }

        self::send(
            $to,
            Yii::t('app', 'Your Leave Request Was Declined'),
            'Your leave request was declined by the acting officer. '
            . 'Please submit a new leave request through a different acting officer.',
            $model,
            [self::redBadge('Declined by acting officer')],
            self::requestUrl($model),
            Yii::t('app', 'View details')
        );
    }

    /**
     * Event 4 — CC recommended → PENDING.
     * Notify all Director and DG users that a request awaits approval.
     */
    public static function ccRecommended(Leave $model)
    {
        // Was every Director plus the DG. Under workplace-based routing a
        // CC only ever handles head-office requests, so this goes to the IT
        // Director — resolved rather than hardcoded, so it stays correct.
        $approverEmails = self::approverEmails($model);
        if (empty($approverEmails)) {
            return;
        }

        self::send(
            $approverEmails,
            Yii::t('app', 'Leave Request Awaiting Approval'),
            'The supervising officer (CC) has recommended the leave request below. '
            . 'It now requires your approval. Please log in to approve or reject it.',
            $model,
            [self::blueBadge('Action needed:'), self::greenBadge('Approve'), self::orRedBadge('Reject')],
            self::requestUrl($model),
            Yii::t('app', 'Approve or reject')
        );
    }

    /**
     * Mailboxes of whoever will decide THIS request.
     *
     * Resolved through the same routing the approval screen uses, so the
     * notification can never disagree with who is actually allowed to act:
     *
     *     Head Office     -> the IT Director
     *     District Office -> the AD of that district
     *     AD / ITD        -> the Director General
     *
     * Previously this was hardcoded to the DG, which meant district ADs
     * were never told a request was waiting for them.
     *
     * @param Leave $model
     * @return string[]
     */
    protected static function approverEmails(Leave $model)
    {
        $type = \backend\config\UserTypeUtil::approverTypeForLeave($model);
        if ($type === null) {
            return [];   // the DG's own leave has no approver in this system
        }

        $emails = ($type === Constant::AD)
            ? self::emailsByType(Constant::AD, $model->applicant_district)
            : self::emailsByType($type);

        return array_values(array_unique(array_filter($emails)));
    }

    /**
     * Event 4b — A CC-exempt request (Additional Director / IT Director)
     * reached PENDING without ever passing through a CC.
     * Notify the DG only — no Director is involved in this chain.
     *
     * @param Leave $model
     * @param bool  $hasActingOfficer  false for Short Leave, which has none
     */
    public static function sentToApprover(Leave $model, $hasActingOfficer = true)
    {
        $to = self::approverEmails($model);
        if (empty($to)) {
            return;
        }

        $intro = $hasActingOfficer
            ? 'The acting officer has confirmed the leave request below. '
              . 'It now requires your approval. Please log in to approve or reject it.'
            : 'The short leave request below has been submitted and now requires '
              . 'your approval. Please log in to approve or reject it.';

        self::send(
            $to,
            Yii::t('app', 'Leave Request Awaiting Your Approval'),
            $intro,
            $model,
            [self::blueBadge('Action needed:'), self::greenBadge('Approve'), self::orRedBadge('Reject')],
            self::requestUrl($model),
            Yii::t('app', 'Approve or reject')
        );
    }

    /**
     * @deprecated Kept so any older call site still works. Use
     *             sentToApprover(), which routes to the real approver
     *             instead of always to the DG.
     */
    public static function sentToDg(Leave $model, $hasActingOfficer = true)
    {
        self::sentToApprover($model, $hasActingOfficer);
    }

    /**
     * Event 3b — The applicant withdrew the request → CANCELLED.
     *
     * Notify whoever it was sitting with: the acting officer if they had
     * not yet responded, otherwise the CC. Without this the request simply
     * disappears from their queue with no explanation.
     *
     * @param Leave $model
     */
    public static function requestCancelled(Leave $model)
    {
        $to = [];

        if (empty($model->acting_officer_agreed_at) && !empty($model->acting_officer_user_id)) {
            $email = self::userEmail($model->acting_officer_user_id);
            if ($email) {
                $to[] = $email;
            }
        } else {
            $to = self::emailsByType(Constant::CC);
        }

        $to = array_values(array_unique(array_filter($to)));
        if (empty($to)) {
            return;
        }

        self::send(
            $to,
            Yii::t('app', 'Leave Request Cancelled'),
            'The applicant has cancelled the leave request below. '
            . 'No action is needed from you — it has been removed from your queue.',
            $model,
            [self::redBadge('Cancelled by the applicant')],
            self::queueUrl(),
            Yii::t('app', 'Open the leave system')
        );
    }

    /**
     * Event 5 — CC rejected → DECLINED.
     * Notify the applicant.
     */
    public static function ccRejected(Leave $model)
    {
        $to = self::applicantEmail($model);
        if (empty($to)) {
            return;
        }

        self::send(
            $to,
            Yii::t('app', 'Your Leave Request Was Rejected'),
            'Your leave request was rejected by the supervising officer (CC). '
            . 'Please review and submit a new leave request.',
            $model,
            [self::redBadge('Rejected by supervising officer (CC)')],
            self::requestUrl($model),
            Yii::t('app', 'View details')
        );
    }

    /**
     * Event 6 — Director/DG approved → APPROVED.
     * Notify the applicant. $approverLabel names who approved
     * (e.g. "Director" or "Director General").
     */
    public static function leaveApproved(Leave $model, $approverLabel = 'approving officer')
    {
        $to = self::applicantEmail($model);
        if (empty($to)) {
            return;
        }

        self::send(
            $to,
            Yii::t('app', 'Your Leave Request Was Approved'),
            Yii::t('app',
                'Good news — your leave request has been approved by the {who}. You can download your leave receipt from the system.',
                ['who' => $approverLabel]
            ),
            $model,
            [self::greenBadge('Approved')],
            self::requestUrl($model),
            Yii::t('app', 'View your request')
        );
    }

    /**
     * Event 7 — Director/DG rejected → REJECTED.
     * Notify the applicant. $rejecterLabel names who rejected
     * (e.g. "Director" or "Director General").
     */
    public static function leaveRejected(Leave $model, $rejecterLabel = 'approving officer')
    {
        $to = self::applicantEmail($model);
        if (empty($to)) {
            return;
        }

        self::send(
            $to,
            Yii::t('app', 'Your Leave Request Was Rejected'),
            Yii::t('app',
                'Your leave request was rejected by the {who}. Please contact your department for further details.',
                ['who' => $rejecterLabel]
            ),
            $model,
            [self::redBadge('Rejected by ' . $rejecterLabel)],
            self::requestUrl($model),
            Yii::t('app', 'View your request')
        );
    }

    // ---------------------------------------------------------------
    // Internal helpers
    // ---------------------------------------------------------------

    /**
     * Look up a single user's email by user id.
     * Email is read from the officer's profile (profile_officer.personal_email),
     * resolved via user.profile_id.
     *
     * @param int|null $userId
     * @return string|null
     */
    protected static function userEmail($userId)
    {
        if (empty($userId)) {
            return null;
        }
        $user = User::findOne((int) $userId);
        if (!$user || empty($user->profile_id)) {
            return null;
        }
        $profile = ProfileOfficers::findOne($user->profile_id);
        return ($profile && !empty($profile->personal_email)) ? $profile->personal_email : null;
    }

    /**
     * Collect all active users' emails for a given user_type (role).
     * Emails come from each user's officer profile (personal_email).
     *
     * @param int|string $type  Constant::CC / DIRECTOR / DG
     * @return string[]
     */
    protected static function emailsByType($type, $district = null)
    {
        // `user`.`type` may hold a comma-separated list of roles
        // (e.g. "4,11"), so an exact match would silently miss a DG who
        // also holds another role. Match on membership instead, which is
        // consistent with UserTypeUtil::hasType().
        $users = User::find()
            ->where(new \yii\db\Expression(
                "FIND_IN_SET(:leaveMailerType, REPLACE(`type`, ' ', '')) > 0",
                [':leaveMailerType' => (string) $type]
            ))
            ->all();

        $emails = [];
        foreach ($users as $u) {
            if (empty($u->profile_id)) {
                continue;
            }
            $profile = ProfileOfficers::findOne($u->profile_id);
            if (!$profile || empty($profile->personal_email)) {
                continue;
            }
            // An AD covers one district, so a district-office request must
            // reach THAT district's AD and nobody else's.
            if ($district !== null && (int) $profile->district !== (int) $district) {
                continue;
            }
            $emails[] = $profile->personal_email;
        }
        return $emails;
    }

    /**
     * Resolve the applicant's email from their officer profile
     * (profile_officer.personal_email), via the leave's user.
     * Falls back to the email stored on the leave row if no profile email.
     *
     * @param Leave $model
     * @return string|null
     */
    protected static function applicantEmail(Leave $model)
    {
        if ($model->user && $model->user->profile_id) {
            $p = ProfileOfficers::findOne($model->user->profile_id);
            if ($p && !empty($p->personal_email)) {
                return $p->personal_email;
            }
        }
        return !empty($model->email) ? $model->email : null;
    }

    /**
     * Resolve the applicant's display name (for the email body).
     *
     * @param Leave $model
     * @return string
     */
    protected static function applicantName(Leave $model)
    {
        if ($model->user && $model->user->profile_id) {
            $p = ProfileOfficers::findOne($model->user->profile_id);
            if ($p) {
                $name = trim($p->first_name . ' ' . $p->last_name);
                if ($name !== '') {
                    return $name;
                }
            }
        }
        return $model->nic ?: '—';
    }

    /**
     * Build a green ("positive") badge.
     */
    protected static function greenBadge($label)
    {
        return ['label' => $label, 'bg' => self::COLOR_GREEN_BG, 'text' => self::COLOR_GREEN_TEXT];
    }

    /**
     * Build a red ("negative") badge.
     */
    protected static function redBadge($label)
    {
        return ['label' => $label, 'bg' => self::COLOR_RED_BG, 'text' => self::COLOR_RED_TEXT];
    }

    /**
     * Build a red badge preceded by an "or" separator word
     * (used between two action choices, e.g. Agree or Decline).
     */
    protected static function orRedBadge($label)
    {
        $b = self::redBadge($label);
        $b['sep'] = Yii::t('app', 'or');
        return $b;
    }

    /**
     * Build a blue ("info") badge.
     */
    protected static function blueBadge($label)
    {
        return ['label' => $label, 'bg' => self::COLOR_BLUE_BG, 'text' => self::COLOR_BLUE_TEXT];
    }

    /**
     * Build and send the email via the configured mailer.
     *
     * @param string|string[] $to       one address or a list
     * @param string          $subject
     * @param string          $intro    lead paragraph text
     * @param Leave           $model
     * @param array           $badges   list of badges to show in the header,
     *                                   each ['label' => string, 'bg' => hex, 'text' => hex]
     * @return bool  true if sent (or attempted) without throwing
     */
    protected static function send($to, $subject, $intro, Leave $model, array $badges,
                                   $ctaUrl = null, $ctaLabel = null)
    {
        try {
            $leaveLabels = Leave::leaveTypeOptions();
            $typeLabel   = $leaveLabels[$model->leave_type] ?? $model->leave_type;

            Yii::$app->mailer->compose(
                ['html' => 'leave/notification-html'],
                [
                    'model'         => $model,
                    'intro'         => $intro,
                    'badges'        => $badges,
                    'applicantName' => self::applicantName($model),
                    'typeLabel'     => $typeLabel,
                    'ctaUrl'        => $ctaUrl,
                    'ctaLabel'      => $ctaLabel,
                ]
            )
            ->setFrom([self::FROM_EMAIL => self::FROM_NAME])
            ->setTo($to)
            ->setSubject($subject)
            ->send();

            return true;
        } catch (\Throwable $e) {
            // Never let a mail failure break the leave action — just log it.
            Yii::error('LeaveMailer failed: ' . $e->getMessage(), 'leave-mailer');
            return false;
        }
    }
}