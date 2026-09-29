<?php

use backend\config\Constant;
use backend\config\LeaveTheme;
use backend\models\Leave;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap4\LinkPager;

/** @var yii\web\View $this */
/** @var backend\models\LeaveSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var bool  $showAll  all-employees view (true) vs personal view (false) */
/** @var bool  $myMode   personal "Request Leave" view (?mode=my) */
/** @var int   $totalCount */
/** @var int   $draftCount */
/** @var int   $pendingCount */
/** @var int   $approvedCount */
/** @var int   $rejectedCount */
/** @var array $actingRequests  leave rows where current user is acting officer and status=draft */
/** @var float $casualDays */
/** @var float $annualDays */
/** @var int   $noPayDays */
/** @var int   $shortCount */
/** @var int   $dutyDays */
/** @var int   $halfDayCount */

// Backward-compatible defaults if a caller omits the new flags
$showAll = $showAll ?? false;
$myMode  = $myMode  ?? false;
$shortThisMonth = $shortThisMonth ?? 0;

// Route used by the search form, reset link, "New Leave Request" button,
// and grid pagination/sort so the personal view stays in mode=my.
$indexRoute = $myMode ? ['index', 'mode' => 'my'] : ['index'];

$this->title = $showAll
    ? Yii::t('app', 'All Employee Leave Requests')
    : Yii::t('app', 'My Leave Requests');

$this->params['breadcrumbs'][] = $this->title;

$CASUAL_MAX = Leave::CASUAL_MAX;   // 21
$ANNUAL_MAX = Leave::ANNUAL_MAX;   // 24 (Vacation Leave)
$SHORT_MAX  = Leave::SHORT_MONTHLY_LIMIT;

/**
 * The six leave types are two different kinds of thing, so they are shown
 * as two different blocks.
 *
 * ENTITLEMENTS have a ceiling — a bar and a "remaining" figure mean
 * something. OTHER LEAVE has no annual limit, so a card with an empty
 * progress area below it was just structural whitespace; those become a
 * compact counter row instead.
 *
 * Colours come from LeaveTheme, which also drives the grid badges — so a
 * leave type is the same hue on this page and in the table below it.
 */
$entitlements = [
    [
        'type'      => Leave::LEAVE_TYPE_CASUAL,
        'label'     => Yii::t('app', 'Casual'),
        'taken'     => $casualDays,
        'max'       => $CASUAL_MAX,
        'remaining' => max(0, $CASUAL_MAX - $casualDays),
        'unit'      => Yii::t('app', 'days'),
        'warnAt'    => 3,
        'note'      => null,
    ],
    [
        'type'      => Leave::LEAVE_TYPE_ANNUAL,
        'label'     => Yii::t('app', 'Vacation'),
        'taken'     => $annualDays,
        'max'       => $ANNUAL_MAX,
        'remaining' => max(0, $ANNUAL_MAX - $annualDays),
        'unit'      => Yii::t('app', 'days'),
        'warnAt'    => 3,
        'note'      => null,
    ],
    [
        'type'      => Leave::LEAVE_TYPE_SHORT,
        'label'     => Yii::t('app', 'Short'),
        'taken'     => $shortThisMonth,
        'max'       => $SHORT_MAX,
        'remaining' => max(0, $SHORT_MAX - $shortThisMonth),
        'unit'      => Yii::t('app', 'this month'),
        'warnAt'    => 0,
        // Was a page-wide banner; it belongs on the card it describes.
        'note'      => Yii::t('app', 'Counts as 1 day, capped at 2 per month, and does not deduct from your Casual or Vacation balance.'),
    ],
];

$otherLeave = [
    [
        'type'  => Leave::LEAVE_TYPE_NO_PAY,
        'label' => Yii::t('app', 'No pay'),
        'value' => $noPayDays,
        'note'  => null,
    ],
    [
        'type'  => Leave::LEAVE_TYPE_DUTY,
        'label' => Yii::t('app', 'Duty'),
        'value' => $dutyDays,
        'note'  => null,
    ],
    [
        'type'  => Leave::LEAVE_TYPE_HALF_DAY,
        'label' => Yii::t('app', 'Half day'),
        'value' => $halfDayCount,   // count of requests, not days
        'note'  => Yii::t('app', 'Deducts 0.5 day from the bucket you select (Casual or Vacation).'),
    ],
];

// Status tiles for the metric strip, each linking to itself as a filter.
$metrics = [
    ['label' => Yii::t('app', 'Total'),    'count' => $totalCount,    'icon' => 'fas fa-calendar-alt',
     'bg' => '#eef2ff', 'fg' => '#4338ca', 'accent' => null,      'status' => null],
    ['label' => Yii::t('app', 'Draft'),    'count' => $draftCount,    'icon' => 'fas fa-pencil-alt',
     'bg' => '#f1f3f9', 'fg' => '#5a5c69', 'accent' => null,      'status' => Leave::STATUS_DRAFT],
    ['label' => Yii::t('app', 'Pending'),  'count' => $pendingCount,  'icon' => 'fas fa-clock',
     'bg' => '#fdf6ec', 'fg' => '#b45309', 'accent' => '#f59e0b', 'status' => Leave::STATUS_PENDING],
    ['label' => Yii::t('app', 'Approved'), 'count' => $approvedCount, 'icon' => 'fas fa-check-circle',
     'bg' => '#e6f6ef', 'fg' => '#0f8b62', 'accent' => null,      'status' => Leave::STATUS_APPROVED],
    ['label' => Yii::t('app', 'Rejected'), 'count' => $rejectedCount, 'icon' => 'fas fa-times-circle',
     'bg' => '#fdeef2', 'fg' => '#cc2b5e', 'accent' => null,      'status' => Leave::STATUS_REJECTED],
];

// Optional — set by the controller for the personal view only.
$inProgress     = $inProgress     ?? null;
$inProgressMore = $inProgressMore ?? 0;

LeaveTheme::registerButtonCss();
// Below 768px every table in this module collapses into one card per row.
LeaveTheme::registerTableCss();

$this->registerCss(<<<CSS
.lv-page-desc { font-size: 1.05rem; font-weight: 600; color: #5a5c69; line-height: 1.35; }

/* ── Queue panels (acting officer / CC review) ─────────────────── */
/* The header bars were solid bg-info and bg-primary — the loudest thing
   on the page even holding a single row. Colour now sits on the icon and
   the count pill only. */
.lv-queue-head { display: flex; align-items: center; justify-content: space-between;
                 gap: .75rem; flex-wrap: wrap; background: #fff;
                 border-bottom: 1px solid #e3e6f0; padding: .75rem 1rem; }
.lv-queue-title { font-size: .85rem; font-weight: 700; color: #5a5c69; }
.lv-queue-count { font-size: .7rem; font-weight: 700; padding: .1rem .55rem;
                  border-radius: 99px; margin-left: .35rem; }
.lv-queue-hint { font-size: .75rem; color: #858796; }
.lv-queue-table thead th { font-size: .625rem; text-transform: uppercase; letter-spacing: .05em;
                           color: #858796; font-weight: 700; border-top: 0;
                           background: #f8f9fc; white-space: nowrap; }
.lv-queue-table tbody td { font-size: .8rem; vertical-align: middle; }
.lv-queue-name { font-weight: 600; color: #5a5c69; }
.lv-queue-nic { font-size: .7rem; color: #858796; font-variant-numeric: tabular-nums; }
.lv-page-desc .text-muted { font-weight: 500; }

.lv-metrics { display: flex; flex-wrap: wrap; border: 1px solid #e3e6f0; border-radius: .35rem; background: #fff; overflow: hidden; }
.lv-metric { flex: 1 1 0; min-width: 145px; padding: .8rem 1rem; border-right: 1px solid #e3e6f0;
             display: flex; align-items: center; gap: .7rem; text-decoration: none; position: relative; }
.lv-metric:last-child { border-right: 0; }
.lv-metric:hover { background: #f8f9fc; text-decoration: none; }
.lv-metric-text { flex: 1 1 auto; min-width: 0; }
.lv-metric-label { font-size: .625rem; color: #858796; text-transform: uppercase; letter-spacing: .05em; font-weight: 700; }
.lv-metric-value { font-size: 1.5rem; font-weight: 700; line-height: 1; color: #5a5c69; margin-top: .3rem; }
.lv-metric-icon { width: 36px; height: 36px; border-radius: 10px; flex: none;
                  display: flex; align-items: center; justify-content: center; font-size: .95rem; }
.lv-metric-accent { position: absolute; left: 0; top: 0; bottom: 0; width: 3px; }

.lv-section-label { font-size: .625rem; color: #858796; text-transform: uppercase; letter-spacing: .05em;
                    font-weight: 700; margin-bottom: .4rem; }
.lv-section-label span { text-transform: none; letter-spacing: 0; font-weight: 400; }

.lv-ent { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: .75rem; }
.lv-ent-card { background: #fff; border: 1px solid #e3e6f0; border-radius: .35rem; padding: .8rem .9rem; }
.lv-ent-head { display: flex; align-items: center; gap: .4rem; margin-bottom: .55rem; }
.lv-ent-name { font-size: .625rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
.lv-ent-value { font-size: 1.35rem; font-weight: 700; line-height: 1; }
.lv-ent-value small { font-size: .72rem; color: #858796; font-weight: 400; margin-left: .25rem; }
.lv-ent-sub { font-size: .7rem; margin-top: .4rem; }

.lv-other { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1px;
            background: #e3e6f0; border: 1px solid #e3e6f0; border-radius: .35rem; overflow: hidden; }
.lv-other-cell { background: #fff; padding: .6rem .9rem; display: flex; align-items: center; gap: .55rem; }
.lv-other-value { font-size: 1.1rem; font-weight: 700; line-height: 1; color: #5a5c69; }
.lv-other-label { font-size: .72rem; color: #858796; }

.lv-inprogress { background: #fdf6ec; border: 1px solid #f5d9a8; border-radius: .35rem;
                 padding: .75rem 1rem; display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; }

.lv-note { background: #f8f9fc; border: 1px solid #e3e6f0; border-left: 3px solid #6366f1;
           border-radius: .35rem; padding: .7rem .9rem; display: flex; gap: .6rem; align-items: flex-start; }
.lv-note-text { font-size: .78rem; color: #6e707e; line-height: 1.6; margin: 0; }
.lv-note-pill { font-size: .72rem; font-weight: 600; padding: .05rem .4rem; border-radius: 4px; white-space: nowrap; }
CSS
);
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-3">
        <?php // No heading here: the layout already prints the page title
              // above the breadcrumbs. Only the description survives, at
              // readable weight rather than as a caption under a heading. ?>
        <div class="mb-2 mb-md-0 lv-page-desc">
            <?= $showAll
                ? Yii::t('app', 'View and manage all employee leave requests')
                : Yii::t('app', 'Track your leave requests and history') ?>
            <span class="text-muted">&middot; <?= date('Y') ?></span>
        </div>
        <?php if (!$showAll): ?>
            <?= Html::a(
                '<i class="fas fa-plus mr-1"></i>' . Yii::t('app', 'New Leave Request'),
                ['create'],
                // Same light green as Submit Request on the form — both
                // buttons start the same job, so they look the same.
                ['class' => 'btn lv-btn lv-btn-green']
            ) ?>
        <?php else: ?>
            <?= Html::a(
                '<i class="fas fa-tachometer-alt mr-1"></i>' . Yii::t('app', 'Leave Dashboard'),
                ['/leave/director-dashboard'],
                ['class' => 'btn btn-primary']
            ) ?>
        <?php endif; ?>
    </div>

    <!-- ── Status metric strip ────────────────────────────────────── -->
    <div class="lv-metrics mb-4">
        <?php foreach ($metrics as $m):
            $url = $m['status']
                ? yii\helpers\Url::to(array_merge($indexRoute, ['LeaveSearch[status]' => $m['status']]))
                : yii\helpers\Url::to($indexRoute);
        ?>
        <a href="<?= $url ?>" class="lv-metric">
            <?php if ($m['accent']): ?>
                <span class="lv-metric-accent" style="background: <?= $m['accent'] ?>;"></span>
            <?php endif; ?>
            <div class="lv-metric-text">
                <div class="lv-metric-label"><?= Html::encode($m['label']) ?></div>
                <div class="lv-metric-value"
                     <?= ($m['accent'] && $m['count'] > 0) ? 'style="color:' . $m['fg'] . ';"' : '' ?>>
                    <?= (int) $m['count'] ?>
                </div>
            </div>
            <div class="lv-metric-icon" style="background: <?= $m['bg'] ?>; color: <?= $m['fg'] ?>;">
                <i class="<?= $m['icon'] ?>"></i>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <?php // ── In-progress request ──────────────────────────────────
    // Only rendered when something is actually unfinished, so the page
    // carries no permanently empty band once everything is settled.
    if (!$showAll && $inProgress !== null):
        $ipType   = LeaveTheme::type($inProgress->leave_type);
        $ipLabels = Leave::leaveTypeOptions();
        $ipName   = trim(preg_replace('/\s*leave\s*$/i', '', $ipLabels[$inProgress->leave_type] ?? $inProgress->leave_type));
        $ipDays   = $inProgress->waitingDays();

        // Name the stage it is actually sitting at.
        if ($inProgress->status === Leave::STATUS_DRAFT) {
            $ipWhere = Yii::t('app', 'Waiting on your acting officer');
        } elseif ($inProgress->status === Leave::STATUS_PENDING_CC) {
            $ipWhere = Yii::t('app', 'Waiting on the supervising officer (CC)');
        } else {
            $ipWhere = Yii::t('app', 'Waiting on the {who}', ['who' => $inProgress->approverLabel()]);
        }
    ?>
    <div class="lv-inprogress mb-4">
        <div class="lv-metric-icon" style="background:#fbeed3; color:#b45309;">
            <i class="fas fa-clock"></i>
        </div>
        <div style="flex:1 1 200px; min-width:0;">
            <div style="font-size:.85rem; font-weight:600; color:#5a5c69;">
                <?= Html::encode($ipName) ?> &middot;
                <?= Html::encode(date('d M', strtotime($inProgress->start_date))) ?>
                &ndash;
                <?= Html::encode(date('d M', strtotime($inProgress->end_date))) ?>
            </div>
            <div style="font-size:.75rem; color:#8a6d3b; margin-top:.15rem;">
                <?= Html::encode($ipWhere) ?>
                <?php if ($ipDays !== null && $ipDays >= 1): ?>
                    &middot; <?= Html::encode(LeaveTheme::agingLabel($ipDays)) ?>
                <?php endif; ?>
                <?php if ($inProgressMore > 0): ?>
                    &middot; <?= Yii::t('app', '+{n} more in progress', ['n' => (int) $inProgressMore]) ?>
                <?php endif; ?>
            </div>
        </div>
        <div style="flex:none;"><?= LeaveTheme::dots($inProgress) ?></div>
        <div style="flex:none;"><?= LeaveTheme::actionButton($inProgress, 'view') ?></div>
    </div>
    <?php endif; ?>

    <?php if (!$showAll): ?>
    <!-- ── Entitlements: the types that have a ceiling ────────────── -->
    <div class="lv-section-label"><?= Yii::t('app', 'Your entitlements') ?></div>
    <div class="lv-ent mb-4">
        <?php foreach ($entitlements as $e):
            $c        = LeaveTheme::type($e['type']);
            $low      = ($e['remaining'] <= $e['warnAt']);
            $barColor = $low ? '#cc2b5e' : $c['border'];
            $pct      = $e['max'] > 0 ? min(100, round(($e['taken'] / $e['max']) * 100)) : 0;
        ?>
        <div class="lv-ent-card" style="border-left: 3px solid <?= $c['border'] ?>;"
             <?= $e['note'] ? 'title="' . Html::encode($e['note']) . '"' : '' ?>>
            <div class="lv-ent-head">
                <i class="<?= $c['fa'] ?>" style="color: <?= $c['icon'] ?>; font-size:.85rem;"></i>
                <span class="lv-ent-name" style="color: <?= $c['icon'] ?>;"><?= Html::encode($e['label']) ?></span>
            </div>

            <?php // Remaining balance is what you check before requesting leave. ?>
            <div class="lv-ent-value" style="<?= $low ? 'color:#cc2b5e;' : '' ?>">
                <?= $e['remaining'] ?><small><?= Yii::t('app', 'left') ?></small>
            </div>

            <div class="progress mt-2" style="height:4px;">
                <div class="progress-bar" role="progressbar"
                     style="width:<?= $pct ?>%; background:<?= $barColor ?>;"
                     aria-valuenow="<?= $e['taken'] ?>" aria-valuemin="0" aria-valuemax="<?= $e['max'] ?>"></div>
            </div>

            <div class="lv-ent-sub" style="color: <?= $low ? '#cc2b5e' : '#858796' ?>;">
                <?= Yii::t('app', '{taken} of {max} {unit} taken', [
                    'taken' => $e['taken'],
                    'max'   => $e['max'],
                    'unit'  => $e['unit'],
                ]) ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- ── Other leave: no ceiling, so counts only ────────────────── -->
    <div class="lv-section-label">
        <?= Yii::t('app', 'Other leave taken') ?>
        <span>&mdash; <?= Yii::t('app', 'no annual limit') ?></span>
    </div>
    <div class="lv-other mb-4">
        <?php foreach ($otherLeave as $o):
            $c = LeaveTheme::type($o['type']);
        ?>
        <div class="lv-other-cell" <?= $o['note'] ? 'title="' . Html::encode($o['note']) . '"' : '' ?>>
            <i class="<?= $c['fa'] ?>" style="color: <?= $c['icon'] ?>; font-size:.85rem;"></i>
            <span class="lv-other-value"><?= $o['value'] ?></span>
            <span class="lv-other-label"><?= Html::encode($o['label']) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <?php
    // ── Leave rules note ─────────────────────────────────────────
    // The figures are tinted with their own leave type's colour, so
    // "0.5 day" matches the Half Day counter and "1 day" / "2 per month"
    // match the Short card above.
    $notePill = function ($text, $type) {
        $c = LeaveTheme::type($type);
        return '<span class="lv-note-pill" style="background:' . $c['bg']
             . '; color:' . $c['icon'] . ';">' . Html::encode($text) . '</span>';
    };
    ?>
    <div class="lv-note mb-4">
        <i class="fas fa-info-circle" style="color:#4338ca; font-size:.9rem; flex:none; margin-top:.15rem;"></i>
        <p class="lv-note-text">
            <?= Yii::t('app',
                'Half Day Leave deducts {halfDay} from the bucket you select (Casual or Vacation). Short Leave counts as {oneDay}, is capped at {cap}, and does not deduct from your Casual or Vacation balance.',
                [
                    'halfDay' => $notePill(Yii::t('app', '0.5 day'),     Leave::LEAVE_TYPE_HALF_DAY),
                    'oneDay'  => $notePill(Yii::t('app', '1 day'),       Leave::LEAVE_TYPE_SHORT),
                    'cap'     => $notePill(Yii::t('app', '2 per month'), Leave::LEAVE_TYPE_SHORT),
                ]
            ) ?>
        </p>
    </div>
    <?php endif; ?>


    <?php
    // ── Acting Requests: leaves where the current user was chosen as acting officer ──
    $actingRequests = $actingRequests ?? [];
    if (!empty($actingRequests)): ?>
    <div class="card shadow-sm mb-4">
        <div class="lv-queue-head">
            <span class="lv-queue-title">
                <i class="fas fa-user-check mr-2" style="color:#185fa5;"></i>
                <?= Yii::t('app', 'Acting Officer Requests') ?>
                <span class="lv-queue-count" style="background:#e8f1fb; color:#185fa5;">
                    <?= count($actingRequests) ?>
                </span>
            </span>
            <span class="lv-queue-hint">
                <?= Yii::t('app', 'Officers who chose you as their acting officer — confirm or decline.') ?>
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover mb-0 lv-queue-table lv-rtable">
                <thead>
                    <tr>
                        <th><?= Yii::t('app', 'Applicant') ?></th>
                        <th><?= Yii::t('app', 'Role') ?></th>
                        <th><?= Yii::t('app', 'Leave Type') ?></th>
                        <th><?= Yii::t('app', 'Dates') ?></th>
                        <th><?= Yii::t('app', 'Days') ?></th>
                        <th><?= Yii::t('app', 'Reason') ?></th>
                        <th><?= Yii::t('app', 'Requested') ?></th>
                        <th><?= Yii::t('app', 'Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($actingRequests as $ar):
                    // Load applicant name from profile
                    $arProfile = null;
                    if ($ar->user && $ar->user->profile_id) {
                        $arProfile = \backend\models\ProfileOfficers::findOne($ar->user->profile_id);
                    }
                    $arName = $arProfile
                        ? trim($arProfile->first_name . ' ' . $arProfile->last_name)
                        : ($ar->user->nic ?? '—');
                    $leaveLabels = Leave::leaveTypeOptions();
                    $arRoleName  = isset(\backend\config\Constant::$userTypes[(int)$ar->user_type]['name'])
                        ? \backend\config\Constant::$userTypes[(int)$ar->user_type]['name']
                        : $ar->user_type;
                ?>
                <tr>
                    <td>
                        <span class="d-inline-flex align-items-center" style="gap:.5rem;">
                            <?= LeaveTheme::avatar($arName, LeaveTheme::type($ar->leave_type)['bg'], LeaveTheme::type($ar->leave_type)['icon'], 28) ?>
                            <span>
                                <span class="lv-queue-name"><?= Html::encode($arName) ?></span><br>
                                <span class="lv-queue-nic"><?= Html::encode($ar->nic) ?></span>
                            </span>
                        </span>
                    </td>
                    <td><?= LeaveTheme::roleText($ar->user_type) ?></td>
                    <td><?= LeaveTheme::typeBadge($ar) ?></td>
                    <td>
                        <?= Html::encode(date('M d', strtotime($ar->start_date))) ?>
                        →
                        <?= Html::encode(date('M d, Y', strtotime($ar->end_date))) ?>
                    </td>
                    <td class="text-right"><?= LeaveTheme::daysCell($ar) ?></td>
                    <td><?= Html::encode(mb_strimwidth($ar->reason ?? '', 0, 40, '…')) ?></td>
                    <td>
                        <small><?= $ar->created_at ? date('M d, Y', strtotime($ar->created_at)) : '—' ?></small>
                    </td>
                    <td>
                        <div class="d-flex">
                            <!-- Agree (explicit POST form with CSRF token) -->
                            <?= Html::beginForm(['/leave/acting-agree', 'id' => $ar->id], 'post', [
                                'class' => 'mr-1',
                                'onsubmit' => 'return confirm('
                                    . json_encode(Yii::t('app',
                                        'Are you sure you want to act for {name} during their leave?',
                                        ['name' => $arName]
                                    )) . ');',
                            ]) ?>
                                <?= Html::submitButton(
                                    '<i class="fas fa-check mr-1"></i>' . Yii::t('app', 'Agree'),
                                    ['class' => 'btn btn-sm lv-btn lv-btn-green']
                                ) ?>
                            <?= Html::endForm() ?>

                            <!-- Decline (explicit POST form with CSRF token) -->
                            <?= Html::beginForm(['/leave/acting-decline', 'id' => $ar->id], 'post', [
                                'onsubmit' => 'return confirm('
                                    . json_encode(Yii::t('app', 'Are you sure you want to decline this request?'))
                                    . ');',
                            ]) ?>
                                <?= Html::submitButton(
                                    '<i class="fas fa-times mr-1"></i>' . Yii::t('app', 'Decline'),
                                    ['class' => 'btn btn-sm lv-btn lv-btn-cancel-outline']
                                ) ?>
                            <?= Html::endForm() ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php
    // ── Declined notice: acting officer or CC declined this applicant's request ──
    $declinedLeaves = $declinedLeaves ?? [];
    foreach ($declinedLeaves as $dl):
        // The remarks marker recorded at decline time tells us who rejected.
        $declinedByCc = ($dl->remarks === 'declined_by_cc');
        if ($declinedByCc) {
            $declineTitle = Yii::t('app', 'Your request has been rejected by the supervising officer (CC).');
            $declineBody  = Yii::t('app', 'Please review and submit a new leave request.');
        } else {
            $declineTitle = Yii::t('app', 'Your request has been rejected by the acting officer.');
            $declineBody  = Yii::t('app', 'Please submit a new leave request through a different acting officer.');
        }
    ?>
    <div class="alert alert-danger shadow-sm d-flex justify-content-between align-items-start" role="alert">
        <div>
            <h6 class="alert-heading mb-1">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <?= Html::encode($declineTitle) ?>
            </h6>
            <p class="mb-0">
                <?= Html::encode($declineBody) ?>
                <span class="text-muted">
                    (<?= Html::encode(Leave::leaveTypeOptions()[$dl->leave_type] ?? $dl->leave_type) ?>,
                    <?= Html::encode(date('M d', strtotime($dl->start_date))) ?>
                    &rarr; <?= Html::encode(date('M d, Y', strtotime($dl->end_date))) ?>)
                </span>
            </p>
        </div>
        <?= Html::beginForm(['/leave/dismiss-declined', 'id' => $dl->id], 'post', ['class' => 'ml-2']) ?>
            <?= Html::submitButton(
                '<i class="fas fa-times"></i>',
                [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'title' => Yii::t('app', 'Dismiss'),
                ]
            ) ?>
        <?= Html::endForm() ?>
    </div>
    <?php endforeach; ?>

    <?php
    // ── CC Review queue: leaves where the current user holds the CC role ──
    // Shown only to the system's CC officer. They check the request for
    // errors/shortcomings, then Recommend (→ the approver) or Reject
    // (→ applicant restarts).
    $ccQueue = $ccQueue ?? [];
    if (!empty($ccQueue)): ?>
    <div class="card shadow-sm mb-4">
        <div class="lv-queue-head">
            <span class="lv-queue-title">
                <i class="fas fa-clipboard-check mr-2" style="color:#4338ca;"></i>
                <?= Yii::t('app', 'Pending CC Review') ?>
                <span class="lv-queue-count" style="background:#eef2ff; color:#4338ca;">
                    <?= count($ccQueue) ?>
                </span>
            </span>
            <span class="lv-queue-hint">
                <?= Yii::t('app', 'Recommend or reject before it reaches the IT Director.') ?>
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover mb-0 lv-queue-table lv-rtable">
                <thead>
                    <tr>
                        <th><?= Yii::t('app', 'Applicant') ?></th>
                        <th><?= Yii::t('app', 'Role') ?></th>
                        <th><?= Yii::t('app', 'Leave Type') ?></th>
                        <th><?= Yii::t('app', 'Dates') ?></th>
                        <th><?= Yii::t('app', 'Days') ?></th>
                        <th><?= Yii::t('app', 'Acting Officer') ?></th>
                        <th><?= Yii::t('app', 'Confirmed') ?></th>
                        <th><?= Yii::t('app', 'Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($ccQueue as $cq):
                    $cqProfile = null;
                    if ($cq->user && $cq->user->profile_id) {
                        $cqProfile = \backend\models\ProfileOfficers::findOne($cq->user->profile_id);
                    }
                    $cqName = $cqProfile
                        ? trim($cqProfile->first_name . ' ' . $cqProfile->last_name)
                        : ($cq->user->nic ?? '—');
                    $leaveLabels  = Leave::leaveTypeOptions();
                    // Applicant role name from Constant::$userTypes
                    $cqRoleName = isset(\backend\config\Constant::$userTypes[(int)$cq->user_type]['name'])
                        ? \backend\config\Constant::$userTypes[(int)$cq->user_type]['name']
                        : $cq->user_type;
                    // Acting officer role — look up their user_type from the user record
                    $cqActingRoleName = '—';
                    if (!empty($cq->acting_officer_user_id)) {
                        $cqActingUser = \common\models\User::findOne((int) $cq->acting_officer_user_id);
                        if ($cqActingUser && isset($cqActingUser->type)) {
                            // user.type may be stored as string or int — try both
                            $actingTypeKey = (int) $cqActingUser->type;
                            if (isset(\backend\config\Constant::$userTypes[$actingTypeKey]['name'])) {
                                $cqActingRoleName = \backend\config\Constant::$userTypes[$actingTypeKey]['name'];
                            } elseif (isset(\backend\config\Constant::$userTypes[$cqActingUser->type]['name'])) {
                                $cqActingRoleName = \backend\config\Constant::$userTypes[$cqActingUser->type]['name'];
                            } else {
                                $cqActingRoleName = $cqActingUser->type;
                            }
                        }
                    }
                ?>
                <tr>
                    <td>
                        <span class="d-inline-flex align-items-center" style="gap:.5rem;">
                            <?= LeaveTheme::avatar($cqName, LeaveTheme::type($cq->leave_type)['bg'], LeaveTheme::type($cq->leave_type)['icon'], 28) ?>
                            <span>
                                <span class="lv-queue-name"><?= Html::encode($cqName) ?></span><br>
                                <span class="lv-queue-nic"><?= Html::encode($cq->nic) ?></span>
                            </span>
                        </span>
                    </td>
                    <td><?= LeaveTheme::roleText($cq->user_type) ?></td>
                    <td><?= LeaveTheme::typeBadge($cq) ?></td>
                    <td>
                        <?= Html::encode(date('M d', strtotime($cq->start_date))) ?>
                        →
                        <?= Html::encode(date('M d, Y', strtotime($cq->end_date))) ?>
                    </td>
                    <td class="text-right"><?= LeaveTheme::daysCell($cq) ?></td>
                    <td>
                        <?= Html::encode($cq->acting_officer ?: '—') ?><br>
                        <small class="text-muted">
                            <?= Html::encode($cqActingRoleName) ?>
                        </small>
                    </td>
                    <td>
                        <small>
                            <?= $cq->acting_officer_agreed_at
                                ? date('M d, Y', strtotime($cq->acting_officer_agreed_at))
                                : '—' ?>
                        </small>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap">
                            <?= Html::a(
                                '<i class="fas fa-file-alt mr-1"></i>' . Yii::t('app', 'Receipt'),
                                ['/leave/receipt', 'id' => $cq->id],
                                ['class' => 'btn btn-sm lv-btn lv-btn-tint mr-1 mb-1', 'target' => '_blank']
                            ) ?>

                            <!-- Recommend (explicit POST form with CSRF token) -->
                            <?= Html::beginForm(['/leave/cc-recommend', 'id' => $cq->id], 'post', [
                                'class' => 'mr-1 mb-1',
                                'onsubmit' => 'return confirm('
                                    . json_encode(Yii::t('app',
                                        'Recommend {name}\'s leave request to the IT Director?',
                                        ['name' => $cqName]
                                    )) . ');',
                            ]) ?>
                                <?= Html::submitButton(
                                    '<i class="fas fa-check mr-1"></i>' . Yii::t('app', 'Recommend'),
                                    ['class' => 'btn btn-sm lv-btn lv-btn-green']
                                ) ?>
                            <?= Html::endForm() ?>

                            <!-- Reject (explicit POST form with CSRF token) -->
                            <?= Html::beginForm(['/leave/cc-reject', 'id' => $cq->id], 'post', [
                                'class' => 'mb-1',
                                'onsubmit' => 'return confirm('
                                    . json_encode(Yii::t('app', 'Reject this leave request?'))
                                    . ');',
                            ]) ?>
                                <?= Html::submitButton(
                                    '<i class="fas fa-times mr-1"></i>' . Yii::t('app', 'Reject'),
                                    ['class' => 'btn btn-sm lv-btn lv-btn-cancel-outline']
                                ) ?>
                            <?= Html::endForm() ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <?php endif; ?>


    <?= $this->render('_search', [
        'model'        => $searchModel,
        'showAll'      => $showAll,
        'action'       => $indexRoute,
        // Lets the panel show "N results" and build the active-filter chips.
        'dataProvider' => $dataProvider,
    ]); ?>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong><i class="fas fa-list mr-1"></i><?= Yii::t('app', 'My Leave History') ?></strong>
            <span>
                <?php
                // Carry current search/filter params into the export (mode=my forced).
                $exportParams = Yii::$app->request->queryParams;
                $exportParams['mode'] = 'my';
                unset($exportParams['page'], $exportParams['sort']);
                ?>
                <div class="dropdown d-inline-block">
                    <button class="btn btn-sm btn-danger dropdown-toggle" type="button"
                            id="exportAllBtnMy" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-download mr-1"></i><?= Yii::t('app', 'Export All') ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportAllBtnMy">
                        <?= Html::a(
                            '<i class="fas fa-file-pdf mr-2 text-danger"></i>' . Yii::t('app', 'PDF'),
                            array_merge(['/leave/export-pdf'], $exportParams),
                            ['class' => 'dropdown-item', 'target' => '_blank']
                        ) ?>
                        <?= Html::a(
                            '<i class="fas fa-file-excel mr-2 text-success"></i>' . Yii::t('app', 'Excel'),
                            array_merge(['/leave/export-excel'], $exportParams),
                            ['class' => 'dropdown-item', 'target' => '_blank']
                        ) ?>
                    </div>
                </div>
            </span>
        </div>
        <div class="card-body">
            <?= GridView::widget([
                'pager'        => [
                    'class'          => LinkPager::class,
                    'firstPageLabel' => 'First',
                    'lastPageLabel'  => 'Last',
                ],
                'dataProvider' => $dataProvider,
                'options'      => ['class' => 'grid-view table-responsive'],
                'tableOptions' => ['class' => 'table table-hover lv-rtable'],
                'columns'      => array_filter([

                    $showAll ? [
                        'attribute' => 'user_id',
                        'label'     => Yii::t('app', 'Employee NIC'),
                        'value'     => function ($model) {
                            return $model->user ? $model->user->nic : '—';
                        },
                    ] : null,

                    $showAll ? [
                        'attribute' => 'user_type',
                        'label'     => Yii::t('app', 'Role'),
                        'format'    => 'raw',
                        'value'     => function ($model) {
                            return LeaveTheme::roleText($model->user_type);
                        },
                    ] : null,

                    [
                        'attribute' => 'leave_type',
                        'label'     => Yii::t('app', 'Leave Type'),
                        'format'    => 'raw',
                        // Every leave type was badge-info here, so Casual and
                        // Duty looked identical in the grid while being indigo
                        // and purple on the cards above. Now both read from
                        // LeaveTheme.
                        'value'     => function ($model) {
                            return LeaveTheme::typeBadge($model);
                        },
                    ],

                    [
                        'label'  => Yii::t('app', 'Dates'),
                        'format' => 'raw',
                        'value'  => function ($model) {
                            return Html::encode(
                                date('M d', strtotime($model->start_date))
                                . ' → '
                                . date('M d, Y', strtotime($model->end_date))
                            );
                        },
                    ],

                    [
                        'attribute'      => 'total_days',
                        'format'         => 'raw',
                        'headerOptions'  => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                        'value'          => function ($model) {
                            return LeaveTheme::daysCell($model);
                        },
                    ],

                    [
                        'attribute' => 'status',
                        'label'     => Yii::t('app', 'Status'),
                        'format'    => 'raw',
                        // Was ucfirst(strtolower(...)), which printed "Pending_cc".
                        'value'     => function ($model) {
                            return LeaveTheme::statusBadge($model);
                        },
                    ],

                    [
                        'attribute' => 'created_at',
                        'label'     => Yii::t('app', 'Requested At'),
                        'format'    => 'raw',
                        'value'     => function ($model) {
                            // Display the raw DB value (already in Asia/Colombo from beforeSave)
                            return $model->created_at
                                ? date('M d, Y h:i:s A', strtotime($model->created_at))
                                : '—';
                        },
                    ],

                    [
                        'label'  => Yii::t('app', 'Actions'),
                        'format' => 'raw',
                        'value'  => function ($model) use ($showAll) {
                            $btn = LeaveTheme::actionButton($model, 'view');

                            // Quick approve/reject only in the all-employees view,
                            // only on pending rows, and never on the approver's own row.
                            if ($showAll
                                && $model->status === Leave::STATUS_PENDING
                                && $model->user_id !== Yii::$app->user->id) {
                                $btn .= ' ' . Html::a(
                                    '<i class="fas fa-check"></i>',
                                    ['approve', 'id' => $model->id],
                                    [
                                        'class' => 'btn btn-sm btn-success',
                                        'title' => Yii::t('app', 'Quick Approve'),
                                        'data'  => [
                                            'method'  => 'post',
                                            'confirm' => Yii::t('app', 'Approve this leave request?'),
                                        ],
                                    ]
                                );
                                $btn .= ' ' . Html::a(
                                    '<i class="fas fa-times"></i>',
                                    ['reject', 'id' => $model->id],
                                    [
                                        'class' => 'btn btn-sm btn-danger',
                                        'title' => Yii::t('app', 'Quick Reject'),
                                        'data'  => [
                                            'method'  => 'post',
                                            'confirm' => Yii::t('app', 'Reject this leave request?'),
                                        ],
                                    ]
                                );
                            }
                            return $btn;
                        },
                    ],

                ]),
            ]); ?>
        </div>
    </div>

</div>