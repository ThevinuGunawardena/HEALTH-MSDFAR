<?php

use backend\config\Constant;
use backend\config\LeaveTheme;
use backend\config\UserTypeUtil;
use backend\models\Leave;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Leave $model */
/** @var array $approvalHistory */
/** @var bool  $showApproveBtn */

$roleName = Constant::$userTypes[(int) $model->user_type]['name'] ?? '—';

$isDG    = UserTypeUtil::isDG();
$isOwner = $model->user_id === Yii::$app->user->id;

// ── Applicant's officer profile (Name / Designation / Signature) ──
// Ministry and first-appointment date come from the leave row itself
// (snapshotted at submit); name, designation and signature are live.
$ownerProfile   = null;
$ownerProfileId = $model->user->profile_id ?? null;
if ($ownerProfileId) {
    $ownerProfile = \backend\models\ProfileOfficers::findOne($ownerProfileId);
}

$applicantName = $ownerProfile
    ? trim($ownerProfile->first_name . ' ' . $ownerProfile->last_name)
    : ($model->user->name ?? $model->nic);

$designation = ($ownerProfile && !empty($ownerProfile->current_designation))
    ? $ownerProfile->current_designation
    : $roleName;

$signatureFile = $ownerProfile->signature ?? '';

// The layout already prints the title and breadcrumbs, so the view does
// not repeat it as a heading. The internal record id is left out: it means
// nothing to an officer reading the page.
$this->title = Yii::t('app', 'Leave Request — {name}', [
    'name' => $applicantName !== '' ? $applicantName : $model->nic,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Leave Requests'), 'url' => ['index', 'mode' => 'my']];
$this->params['breadcrumbs'][] = $this->title;

// ── Origin-aware "Back" target ──────────────────────────────────
// Own request → personal list. Reviewing someone else's (approver only)
// → the dashboard. The all-employees index is retired.
$backUrl = $isOwner ? ['index', 'mode' => 'my'] : ['/leave/director-dashboard'];

// ── State flags ─────────────────────────────────────────────────
$isApproved  = $model->status === Leave::STATUS_APPROVED;
$isRejected  = $model->status === Leave::STATUS_REJECTED;
$isDeclined  = $model->status === Leave::STATUS_DECLINED;
$isCancelled = $model->status === Leave::STATUS_CANCELLED;
$isFailed    = $isRejected || $isDeclined;

// The applicant's own editing window, resolved by the model so the view
// and the controller can never disagree about who may do what.
$canUpdate = $model->canBeUpdatedBy(Yii::$app->user->id);
$canCancel = $model->canBeCancelledBy(Yii::$app->user->id);

// "Locked" = the applicant owns it, it is still moving, but the acting
// officer has already committed — so neither button applies any more.
$isLocked = $isOwner && !$canUpdate && !$canCancel
    && in_array($model->status, [Leave::STATUS_PENDING_CC, Leave::STATUS_PENDING], true);

$typeColor  = LeaveTheme::type($model->leave_type);
$typeLabels = Leave::leaveTypeOptions();
$typeLabel  = trim(preg_replace('/\s*leave\s*$/i', '', $typeLabels[$model->leave_type] ?? $model->leave_type));

$fmtDate = function ($d, $withTime = false) {
    if (empty($d)) {
        return '—';
    }
    $ts = strtotime($d);
    return $ts ? date($withTime ? 'd M Y, h:i A' : 'd M Y', $ts) : $d;
};

$days = rtrim(rtrim(number_format((float) $model->total_days, 2), '0'), '.');

// ── Who ended it, and why ───────────────────────────────────────
// Surfaced as a callout rather than a table cell: when a request is
// refused, the reason is the only thing the applicant needs.
$failWho    = '';
$failReason = '';
$failAt     = '';
if ($isRejected) {
    $failWho    = Yii::t('app', 'Rejected by the {who}', ['who' => $model->approverLabel()]);
    $failReason = $model->remarks;
    $failAt     = $model->approved_at;
} elseif ($isDeclined) {
    if (empty($model->acting_officer_agreed_at)) {
        $failWho = Yii::t('app', 'Declined by the acting officer');
        $failAt  = $model->updated_at;
    } else {
        $failWho = Yii::t('app', 'Rejected by the supervising officer (CC)');
        $failAt  = $model->cc_recommended_at ?: $model->updated_at;
    }
    $failReason = $model->remarks;
}
// Fall back to the newest history remark if the leave row has none.
if ($isFailed && trim((string) $failReason) === '' && !empty($approvalHistory)) {
    foreach ($approvalHistory as $h) {
        if ($h->status === 'reject' && trim((string) $h->remark) !== '') {
            $failReason = $h->remark;
            break;
        }
    }
}

// ── Activity log ────────────────────────────────────────────────
// Built from the leave row's own timestamps, NOT only from
// leave_approval_history — that table is written in one place
// (recordDecision), so it holds the final decision and nothing else.
// Synthesising from the timestamps means old requests display a
// complete trail too, with no backfill and no schema change.
$activity = [];

$activity[] = [
    'name'   => $applicantName,
    'role'   => $roleName,
    'action' => Yii::t('app', 'submitted the request'),
    'badge'  => Yii::t('app', 'Submitted'),
    'tone'   => 'neutral',
    'remark' => null,
    'at'     => $model->created_at,
];

if ($model->leave_type !== Leave::LEAVE_TYPE_SHORT) {
    if (!empty($model->acting_officer_agreed_at)) {
        $activity[] = [
            'name'   => $model->acting_officer ?: LeaveTheme::userName($model->acting_officer_user_id),
            'role'   => Yii::t('app', 'Acting officer'),
            'action' => Yii::t('app', 'agreed to act'),
            'badge'  => Yii::t('app', 'Agreed'),
            'tone'   => 'good',
            'remark' => null,
            'at'     => $model->acting_officer_agreed_at,
        ];
    } elseif ($isDeclined) {
        $activity[] = [
            'name'   => $model->acting_officer ?: LeaveTheme::userName($model->acting_officer_user_id),
            'role'   => Yii::t('app', 'Acting officer'),
            'action' => Yii::t('app', 'declined to act'),
            'badge'  => Yii::t('app', 'Declined'),
            'tone'   => 'bad',
            'remark' => $failReason,
            'at'     => $model->updated_at,
        ];
    }
}

if (!$model->isCcExempt() && !empty($model->cc_recommended_at)) {
    $activity[] = [
        'name'   => LeaveTheme::userName($model->cc_recommended_by),
        'role'   => Yii::t('app', 'Supervising officer (CC)'),
        'action' => Yii::t('app', 'recommended the request'),
        'badge'  => Yii::t('app', 'Recommended'),
        'tone'   => 'good',
        'remark' => null,
        'at'     => $model->cc_recommended_at,
    ];
}

if ($isCancelled) {
    $activity[] = [
        'name'   => $applicantName,
        'role'   => $roleName,
        'action' => Yii::t('app', 'cancelled the request'),
        'badge'  => Yii::t('app', 'Cancelled'),
        'tone'   => 'neutral',
        'remark' => null,
        'at'     => $model->updated_at,
    ];
}

// Final decision rows come from the audit table, so remarks are real.
if (!empty($approvalHistory)) {
    foreach ($approvalHistory as $h) {
        $approved  = ($h->status === 'approve');
        $activity[] = [
            'name'   => LeaveTheme::userName($h->done_by),
            'role'   => $h->doneBy ? UserTypeUtil::getTypeNames($h->doneBy->type) : '—',
            'action' => $approved
                ? Yii::t('app', 'approved the request')
                : Yii::t('app', 'rejected the request'),
            'badge'  => $approved ? Yii::t('app', 'Approved') : Yii::t('app', 'Rejected'),
            'tone'   => $approved ? 'good' : 'bad',
            'remark' => $h->remark,
            'at'     => $h->date_time,
        ];
    }
}

// Oldest first — the log reads as a story.
usort($activity, function ($a, $b) {
    return strtotime($a['at'] ?: '0') <=> strtotime($b['at'] ?: '0');
});

$tones = [
    'good'    => ['bg' => '#e6f6ef', 'fg' => '#0f8b62', 'line' => '#1cc88a'],
    'bad'     => ['bg' => '#fdeef2', 'fg' => '#cc2b5e', 'line' => '#cc2b5e'],
    'neutral' => ['bg' => '#eaecf4', 'fg' => '#6e707e', 'line' => '#b7b9cc'],
];

// Pulls in .lv-btn-tint / .lv-btn-green / .lv-btn-cancel-outline, which
// carry the hover and focus states the old inline styles could not.
LeaveTheme::registerButtonCss();

$this->registerCss(<<<CSS
.lv-view-shell { display: grid; grid-template-columns: minmax(0, 1.5fr) minmax(240px, 1fr); gap: 1rem; align-items: start; }
@media (max-width: 991.98px) { .lv-view-shell { grid-template-columns: 1fr; } }

.lv-panel { background: #fff; border: 1px solid #e3e6f0; border-radius: .35rem; padding: 1rem; margin-bottom: .75rem; }
.lv-panel-label { font-size: .625rem; color: #858796; text-transform: uppercase; letter-spacing: .05em; font-weight: 700; margin-bottom: .7rem; }

.lv-kv { width: 100%; font-size: .82rem; }
.lv-kv td { padding: .3rem 0; vertical-align: top; }
.lv-kv td:first-child { color: #858796; width: 45%; }

.lv-fail { background: #fdeef2; border: 1px solid #f7c9d6; border-left: 3px solid #cc2b5e;
           border-radius: .35rem; padding: .85rem 1rem; display: flex; gap: .7rem; }

.lv-cancelled { background: #f8f9fc; border: 1px solid #e3e6f0; border-left: 3px solid #b7b9cc;
                border-radius: .35rem; padding: .85rem 1rem; display: flex; gap: .7rem; }

.lv-window { background: #fdf6ec; border: 1px solid #f5d9a8; border-left: 3px solid #f59e0b;
             border-radius: .35rem; padding: .8rem 1rem; display: flex; align-items: center;
             gap: .7rem; flex-wrap: wrap; }

.lv-locked { background: #f8f9fc; border: 1px solid #e3e6f0; border-radius: .35rem;
             padding: .7rem 1rem; display: flex; align-items: center; gap: .6rem; }

.lv-log-row { display: grid; grid-template-columns: 82px 1fr; gap: .6rem; padding: .75rem 0; border-bottom: 1px solid #eaecf4; }
.lv-log-row:first-child { padding-top: 0; }
.lv-log-row:last-child { border-bottom: 0; padding-bottom: 0; }
.lv-log-when { text-align: right; }
.lv-log-time { font-size: .78rem; font-weight: 700; color: #5a5c69; line-height: 1.2; }
.lv-log-date { font-size: .7rem; color: #858796; margin-top: .1rem; }
.lv-log-body { padding-left: .75rem; }
.lv-log-what { font-size: .85rem; color: #5a5c69; }
.lv-log-remark { font-size: .78rem; color: #6e707e; margin-top: .3rem; }
.lv-log-role { font-size: .7rem; color: #858796; margin-top: .15rem; }

/* Stack the time above the entry on very narrow screens. */
@media (max-width: 400px) {
    .lv-log-row { grid-template-columns: 1fr; gap: .2rem; }
    .lv-log-when { text-align: left; }
    .lv-log-body { padding-left: .6rem; }
}
CSS
);
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <!-- ── Action bar ─────────────────────────────────────────────── -->
    <!-- No title here: the layout already prints it above the
         breadcrumbs. No status badge either — the timeline card below
         shows it, and twice within two lines was the problem. -->
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-3">
        <?= Html::a(
            '<i class="fas fa-arrow-left mr-2"></i>' . Yii::t('app', 'Back to list'),
            $backUrl,
            ['class' => 'btn btn-outline-secondary mb-2 mb-sm-0']
        ) ?>

        <?php
        // Approved → printable receipt. Pending + approver → preview only.
        // Refused → nothing to print; the callout above already says so.
        if ($isApproved):
            echo Html::a(
                '<i class="fas fa-download mr-2"></i>' . Yii::t('app', 'Download Receipt'),
                ['receipt', 'id' => $model->id],
                [
                    'class'  => 'btn lv-btn lv-btn-tint',
                    'style'  => 'padding:.5rem 1.15rem; font-size:.95rem;',
                    'target' => '_blank',
                ]
            );
        elseif ($showApproveBtn):
            // Preview is for whoever is about to DECIDE, so it hangs off the
            // same flag as the decision form — canCurrentUserApprove(), which
            // already excludes the applicant. Testing roles instead meant an
            // ITD or AD saw "Preview Receipt" on their OWN pending request,
            // which is not theirs to approve.
            echo Html::a(
                '<i class="fas fa-eye mr-2"></i>' . Yii::t('app', 'Preview Receipt'),
                ['receipt', 'id' => $model->id],
                [
                    'class'  => 'btn btn-outline-secondary',
                    'style'  => 'padding:.5rem 1.15rem; font-size:.95rem;',
                    'target' => '_blank',
                    'title'  => Yii::t('app', 'Preview only — printing available after approval.'),
                ]
            );
        endif;
        ?>
    </div>

    <!-- ── Why it was refused ─────────────────────────────────────── -->
    <?php if ($isFailed): ?>
    <div class="lv-fail mb-3">
        <i class="fas fa-times-circle" style="color:#cc2b5e; font-size:1.05rem; flex:none; margin-top:.1rem;"></i>
        <div style="flex:1; min-width:0;">
            <div style="font-size:.85rem; font-weight:700; color:#a8214c;">
                <?= Html::encode($failWho) ?>
            </div>
            <?php if (trim((string) $failReason) !== ''): ?>
                <div style="font-size:.8rem; color:#a8214c; line-height:1.5; margin-top:.25rem;">
                    <?= nl2br(Html::encode($failReason)) ?>
                </div>
            <?php else: ?>
                <div style="font-size:.8rem; color:#b8547a; margin-top:.25rem;">
                    <?= Yii::t('app', 'No reason was recorded.') ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($failAt)): ?>
                <div style="font-size:.72rem; color:#b8547a; margin-top:.35rem;">
                    <?= Html::encode($fmtDate($failAt, true)) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Withdrawn by the applicant ─────────────────────────────── -->
    <?php if ($isCancelled): ?>
    <div class="lv-cancelled mb-3">
        <i class="fas fa-times-circle" style="color:#858796; font-size:1.05rem; flex:none; margin-top:.1rem;"></i>
        <div style="flex:1; min-width:0;">
            <div style="font-size:.85rem; font-weight:700; color:#5a5c69;">
                <?= $isOwner
                    ? Yii::t('app', 'Cancelled by you')
                    : Yii::t('app', 'Cancelled by the applicant') ?>
            </div>
            <div class="text-muted" style="font-size:.78rem; margin-top:.25rem;">
                <?= Html::encode($fmtDate($model->updated_at, true)) ?>
                <?php if (!empty($model->acting_officer)): ?>
                    &middot; <?= Yii::t('app', '{name} was notified.', ['name' => Html::encode($model->acting_officer)]) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Approval progress timeline ─────────────────────────────── -->
    <?= $this->render('_timeline', ['model' => $model]) ?>

    <?php
    // ── The applicant's editing window ──────────────────────────────
    // Open only until the acting officer agrees: from that moment someone
    // else has committed to covering these exact dates.
    if ($canUpdate || $canCancel):
    ?>
    <div class="lv-window mb-3">
        <i class="fas fa-pen" style="color:#b45309; font-size:1rem; flex:none;"></i>
        <div style="flex:1 1 190px; min-width:0;">
            <div style="font-size:.85rem; font-weight:700; color:#8a6d3b;">
                <?= Yii::t('app', 'You can still change this request') ?>
            </div>
            <div style="font-size:.78rem; color:#8a6d3b; margin-top:.15rem;">
                <?= !empty($model->acting_officer)
                    ? Yii::t('app', 'Editing and cancelling close once {name} agrees to act.', [
                        'name' => Html::encode($model->acting_officer),
                      ])
                    : Yii::t('app', 'Editing and cancelling close once the request is reviewed.') ?>
            </div>
        </div>
        <div class="d-flex flex-wrap" style="gap:.5rem; flex:none;">
            <?php if ($canUpdate): ?>
                <?= Html::a(
                    '<i class="fas fa-pen mr-1"></i>' . Yii::t('app', 'Update'),
                    ['update', 'id' => $model->id],
                    [
                        'class' => 'btn lv-btn lv-btn-tint',
                    ]
                ) ?>
            <?php endif; ?>
            <?php if ($canCancel): ?>
                <?= Html::a(
                    '<i class="fas fa-times mr-1"></i>' . Yii::t('app', 'Cancel Request'),
                    ['cancel', 'id' => $model->id],
                    [
                        'class' => 'btn lv-btn lv-btn-cancel-outline',
                        'data'  => [
                            'method'  => 'post',
                            'confirm' => Yii::t('app', 'Cancel this leave request? The record will be permanently deleted and cannot be recovered — you would need to submit a new one.'),
                        ],
                    ]
                ) ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
    elseif ($isLocked):
        // Say WHY the buttons are gone, or people assume it is a bug — but
        // point at the RIGHT person. AD / ITD / Director requests skip the CC
        // stage entirely, so telling those applicants to "contact your
        // supervising officer" names a stage their request never passes
        // through. Whoever is actually holding it is named instead.
        $lockedWho = $model->isCcExempt()
            ? $model->approverLabel()                 // e.g. Director General
            : Yii::t('app', 'supervising officer');

        if (!empty($model->acting_officer) && !empty($model->acting_officer_agreed_at)) {
            $lockedText = Yii::t('app', '{name} agreed to act on {date}. This request can no longer be edited or cancelled — contact the {who} if something needs to change.', [
                'name' => Html::encode($model->acting_officer),
                'date' => Html::encode($fmtDate($model->acting_officer_agreed_at)),
                'who'  => Html::encode($lockedWho),
            ]);
        } else {
            $lockedText = Yii::t('app', 'This request is under review and can no longer be edited or cancelled — contact the {who} if something needs to change.', [
                'who' => Html::encode($lockedWho),
            ]);
        }
    ?>
    <div class="lv-locked mb-3">
        <i class="fas fa-lock" style="color:#858796; font-size:.9rem; flex:none;"></i>
        <div style="font-size:.78rem; color:#6e707e;">
            <?= $lockedText ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Details ────────────────────────────────────────────────── -->
    <div class="lv-view-shell mb-3">

        <div class="lv-panel mb-0">
            <div class="lv-panel-label"><?= Yii::t('app', 'Leave Details') ?></div>

            <div class="d-flex align-items-baseline flex-wrap mb-3" style="gap:.6rem;">
                <?= LeaveTheme::typeBadge($model) ?>
                <span style="font-size:1.4rem; font-weight:700; line-height:1; color:#5a5c69;">
                    <?= Html::encode($days) ?><span class="text-muted" style="font-size:.75rem; font-weight:400; margin-left:.25rem;"><?= Yii::t('app', 'days') ?></span>
                </span>
                <span class="text-muted" style="font-size:.85rem;">
                    <?= Html::encode($fmtDate($model->start_date)) ?>
                    <?php if ($model->end_date && $model->end_date !== $model->start_date): ?>
                        &rarr; <?= Html::encode($fmtDate($model->end_date)) ?>
                    <?php endif; ?>
                </span>
            </div>

            <table class="lv-kv">
                <?php if ($model->leave_type === Leave::LEAVE_TYPE_HALF_DAY && !empty($model->session)): ?>
                <tr>
                    <td><?= Yii::t('app', 'Session') ?></td>
                    <td><?= $model->session === 'MORNING'
                            ? Yii::t('app', 'Morning (8:00 AM – 12:00 PM)')
                            : Yii::t('app', 'Afternoon (1:00 PM – 5:00 PM)') ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td><?= Yii::t('app', 'Resuming duties') ?></td>
                    <td><?= Html::encode($fmtDate($model->resume_date)) ?></td>
                </tr>
                <tr>
                    <td><?= Yii::t('app', 'Acting officer') ?></td>
                    <td><?= Html::encode($model->acting_officer ?: '—') ?></td>
                </tr>
                <tr>
                    <td><?= Yii::t('app', 'Address when on leave') ?></td>
                    <td><?= Html::encode($model->leave_address ?: '—') ?></td>
                </tr>
                <tr>
                    <td><?= Yii::t('app', 'Reason') ?></td>
                    <td><?= $model->reason ? nl2br(Html::encode($model->reason)) : '—' ?></td>
                </tr>
                <tr>
                    <td><?= Yii::t('app', 'Submitted') ?></td>
                    <td><?= Html::encode($fmtDate($model->created_at, true)) ?></td>
                </tr>
            </table>
        </div>

        <div>
            <div class="lv-panel">
                <div class="lv-panel-label"><?= Yii::t('app', 'Applicant') ?></div>
                <div class="d-flex align-items-center mb-3" style="gap:.6rem;">
                    <?= LeaveTheme::avatar($applicantName, $typeColor['bg'], $typeColor['icon'], 34) ?>
                    <div style="min-width:0;">
                        <div style="font-size:.85rem; font-weight:700; color:#5a5c69;"><?= Html::encode($applicantName) ?></div>
                        <div class="text-muted" style="font-size:.75rem;"><?= Html::encode($designation) ?></div>
                    </div>
                </div>
                <table class="lv-kv">
                    <tr><td><?= Yii::t('app', 'NIC') ?></td>
                        <td style="font-variant-numeric:tabular-nums;"><?= Html::encode($model->nic ?: '—') ?></td></tr>
                    <tr><td><?= Yii::t('app', 'Email') ?></td>
                        <td style="overflow-wrap:anywhere;"><?= Html::encode($model->email ?: '—') ?></td></tr>
                    <tr><td><?= Yii::t('app', 'Department') ?></td>
                        <td><?= Html::encode($model->ministry_dept ?: '—') ?></td></tr>
                    <tr><td><?= Yii::t('app', 'First appointed') ?></td>
                        <td><?= Html::encode($fmtDate($model->first_appointment_date)) ?></td></tr>
                </table>

                <?php if (!empty($signatureFile)): ?>
                    <div class="mt-3">
                        <div class="lv-panel-label mb-1"><?= Yii::t('app', 'Signature') ?></div>
                        <?= Html::img(
                            Constant::$FILE_VIEW_PATH . 'officer/signature/' . $signatureFile,
                            [
                                'alt'   => Yii::t('app', 'Signature'),
                                'class' => 'border rounded bg-light p-1',
                                'style' => 'max-height:48px; max-width:100%;',
                            ]
                        ) ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php
            // Balances snapshotted on the leave row at submit time.
            $bals = [
                ['name' => Yii::t('app', 'Casual'),   'type' => 'CASUAL', 'taken' => (float) $model->taken_casual,   'max' => Leave::CASUAL_MAX],
                ['name' => Yii::t('app', 'Vacation'), 'type' => 'ANNUAL', 'taken' => (float) $model->taken_vacation, 'max' => Leave::ANNUAL_MAX],
            ];
            ?>
            <div class="lv-panel mb-0">
                <div class="lv-panel-label"><?= Yii::t('app', 'Leave Taken (This Year)') ?></div>
                <?php foreach ($bals as $b):
                    $c   = LeaveTheme::type($b['type']);
                    $pct = $b['max'] > 0 ? min(100, round(($b['taken'] / $b['max']) * 100)) : 0;
                ?>
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-baseline" style="margin-bottom:.2rem;">
                        <span style="font-size:.65rem; font-weight:700; letter-spacing:.03em; text-transform:uppercase; color: <?= $c['icon'] ?>;">
                            <?= Html::encode($b['name']) ?>
                        </span>
                        <span class="text-muted" style="font-size:.75rem;">
                            <strong style="color:#5a5c69;"><?= rtrim(rtrim(number_format($b['taken'], 2), '0'), '.') ?></strong>
                            <?= Yii::t('app', 'of {max}', ['max' => $b['max']]) ?>
                        </span>
                    </div>
                    <div class="progress" style="height:5px;">
                        <div class="progress-bar" role="progressbar"
                             style="width: <?= $pct ?>%; background: <?= $c['border'] ?>;"
                             aria-valuenow="<?= $b['taken'] ?>" aria-valuemin="0" aria-valuemax="<?= $b['max'] ?>"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="d-flex justify-content-between align-items-baseline">
                    <span class="text-muted" style="font-size:.75rem;"><?= Yii::t('app', 'Other') ?></span>
                    <span class="text-muted" style="font-size:.75rem;">
                        <strong style="color:#5a5c69;"><?= rtrim(rtrim(number_format((float) $model->taken_other, 2), '0'), '.') ?></strong>
                        <?= Yii::t('app', 'days') ?>
                    </span>
                </div>
            </div>
        </div>

    </div>

    <?php Pjax::begin(['id' => 'leave-activity-log']); ?>

    <!-- ── Activity log ───────────────────────────────────────────── -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
            <span class="text-xs font-weight-bold text-uppercase text-muted">
                <i class="fas fa-history mr-1"></i><?= Yii::t('app', 'Activity Log') ?>
            </span>
        </div>
        <div class="card-body">
            <?php if (empty($activity)): ?>
                <div class="text-center text-muted py-3" style="font-size:.82rem;">
                    <?= Yii::t('app', 'Nothing has happened on this request yet.') ?>
                </div>
            <?php else: ?>
                <?php foreach ($activity as $a):
                    $tone = $tones[$a['tone']] ?? $tones['neutral'];
                    $ts   = $a['at'] ? strtotime($a['at']) : null;
                ?>
                <div class="lv-log-row">
                    <div class="lv-log-when">
                        <div class="lv-log-time"><?= $ts ? date('h:i A', $ts) : '—' ?></div>
                        <div class="lv-log-date"><?= $ts ? date('d M Y', $ts) : '' ?></div>
                    </div>
                    <div class="lv-log-body" style="border-left: 2px solid <?= $tone['line'] ?>;">
                        <div class="lv-log-what">
                            <strong><?= Html::encode($a['name']) ?></strong>
                            <span class="text-muted"><?= Html::encode($a['action']) ?></span>
                        </div>
                        <?php if (trim((string) $a['remark']) !== ''): ?>
                            <div class="lv-log-remark">&ldquo;<?= nl2br(Html::encode($a['remark'])) ?>&rdquo;</div>
                        <?php endif; ?>
                        <div class="lv-log-role"><?= Html::encode($a['role']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Approver decision form ─────────────────────────────────── -->
    <?php if ($showApproveBtn && !$isOwner): ?>
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white">
            <span class="text-xs font-weight-bold text-uppercase text-muted">
                <i class="fas fa-gavel mr-1"></i><?= Yii::t('app', 'Your Decision') ?>
            </span>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['class' => 'userform', 'id' => 'decision-form']]); ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label><?= Yii::t('app', 'Decision') ?></label>
                        <select id="status-approval" name="status-approval" class="form-control">
                            <option value="approve"><?= Yii::t('app', 'Approve') ?></option>
                            <option value="reject"><?= Yii::t('app', 'Reject') ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group">
                        <label>
                            <?= Yii::t('app', 'Remarks') ?>
                            <span id="remarks-required" class="text-danger" style="display:none;">*</span>
                        </label>
                        <textarea id="remarks-approval" name="remarks-approval" class="form-control" rows="2"
                                  placeholder="<?= Yii::t('app', 'Enter your remarks here...') ?>"></textarea>
                        <small id="remarks-help" class="text-muted" style="display:none;">
                            <?= Yii::t('app', 'A reason is required when rejecting — the applicant needs to know what to change.') ?>
                        </small>
                    </div>
                </div>
                <div class="col-12">
                    <input type="submit" id="status-approval-btn"
                           value="<?= Yii::t('app', 'Submit Decision') ?>"
                           class="btn btn-primary btn-block mt-2">
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php Pjax::end(); ?>

    <?php
    // ── Next step ───────────────────────────────────────────────────
    // Offered only when the request never reached a decision: the acting
    // officer declined, the CC rejected it, or the applicant withdrew it.
    // In those cases resubmitting is genuinely the next step.
    //
    // A REJECTED request is deliberately excluded. Reaching that status
    // means the approving officer — the last word on this request, whether
    // that is the Director, the IT Director or the DG — has formally
    // decided. Putting a "Submit a new request" button under that decision
    // invites the applicant to route around it immediately, which is not
    // ours to encourage; the callout at the top already carries the reason.
    if ($isOwner && ($isDeclined || $isCancelled)):
    ?>
    <div class="mb-5">
        <?= Html::a(
            '<i class="fas fa-plus mr-1"></i>' . Yii::t('app', 'Submit a new request'),
            ['create'],
            ['class' => 'btn lv-btn lv-btn-green']
        ) ?>
        <span class="text-muted ml-2" style="font-size:.78rem;">
            <?= $isCancelled
                ? Yii::t('app', 'This request was cancelled before anyone acted on it.')
                : Yii::t('app', 'This request did not reach the approving officer.') ?>
        </span>
    </div>
    <?php endif; ?>

</div>

<?php
// Reject requires a reason. Enforced server-side too (recordDecision);
// this only saves the approver a round trip.
$this->registerJs(<<<DECJS
(function () {
    var sel  = document.getElementById('status-approval');
    var box  = document.getElementById('remarks-approval');
    var star = document.getElementById('remarks-required');
    var help = document.getElementById('remarks-help');
    var form = document.getElementById('decision-form');
    if (!sel || !box || !form) { return; }

    function paint() {
        var rejecting = (sel.value === 'reject');
        if (star) { star.style.display = rejecting ? '' : 'none'; }
        if (help) { help.style.display = rejecting ? '' : 'none'; }
        box.required = rejecting;
    }

    sel.addEventListener('change', paint);
    form.addEventListener('submit', function (e) {
        if (sel.value === 'reject' && box.value.trim() === '') {
            e.preventDefault();
            box.focus();
            alert('Please give a reason for rejecting this request.');
        }
    });
    paint();
})();
DECJS
);
?>