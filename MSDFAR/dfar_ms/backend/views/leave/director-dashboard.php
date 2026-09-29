<?php

use backend\models\Leave;
use backend\config\Constant;
use backend\config\LeaveTheme;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap4\LinkPager;

/** @var yii\web\View $this */
/** @var backend\models\LeaveSearch $searchModel */
/** @var yii\data\ActiveDataProvider $pendingDataProvider */
/** @var yii\data\ActiveDataProvider $historyDataProvider */
/** @var int   $totalCount */
/** @var int   $pendingCount */
/** @var int   $approvedCount */
/** @var int   $rejectedCount */
/** @var array $leaveTypeBreakdown */
/** @var string $dashboardTitle */
/** @var int|null $year */
/** @var array $coverageGaps    districts with no AD — DG only */
/** @var int   $unroutableCount requests whose applicant has no district */
/** @var bool  $isDG */

$this->title = $dashboardTitle;
$this->params['breadcrumbs'][] = $this->title;

// The dashboard has always been year-scoped; now it says so on screen.
$year = $year ?? (int) date('Y');

// Optional — only the DG's dashboard receives these.
$coverageGaps    = $coverageGaps    ?? [];
$unroutableCount = $unroutableCount ?? 0;
$isDG            = $isDG            ?? false;
$adDistricts     = $isDG ? LeaveTheme::adDistricts() : null;

// Below 768px every table in this module collapses into one card per row.
LeaveTheme::registerTableCss();

$this->registerCss(<<<CSS
.lv-page-desc { font-size: 1.05rem; font-weight: 600; color: #5a5c69; line-height: 1.35; }
.lv-page-desc .text-muted { font-weight: 500; }

/* ── Metric strip ─────────────────────────────────────────────── */
.lv-metrics { display: flex; flex-wrap: wrap; border: 1px solid #e3e6f0; border-radius: .35rem; background: #fff; overflow: hidden; }
.lv-metric { flex: 1 1 0; min-width: 150px; padding: .85rem 1rem; border-right: 1px solid #e3e6f0;
             display: flex; align-items: center; gap: .75rem; text-decoration: none; position: relative; }
.lv-metric:last-child { border-right: 0; }
.lv-metric:hover { background: #f8f9fc; text-decoration: none; }
.lv-metric-text { flex: 1 1 auto; min-width: 0; }
.lv-metric-label { font-size: .625rem; color: #858796; text-transform: uppercase; letter-spacing: .05em; font-weight: 700; }
.lv-metric-value { font-size: 1.6rem; font-weight: 700; line-height: 1; color: #5a5c69; margin-top: .35rem; }
.lv-metric-icon { width: 38px; height: 38px; border-radius: 10px; flex: none;
                  display: flex; align-items: center; justify-content: center; font-size: 1rem; }
.lv-metric-accent { position: absolute; left: 0; top: 0; bottom: 0; width: 3px; }

/* ── Leave-type cards ─────────────────────────────────────────── */
.lv-typebar { display: flex; height: 10px; border-radius: 5px; overflow: hidden; background: #eaecf4; }
.lv-typebar span { display: block; height: 100%; }
.lv-typecards { display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: .5rem; }
.lv-typecard { border-radius: 6px; padding: .6rem .7rem; }
.lv-typecard-value { font-size: 1.25rem; font-weight: 700; line-height: 1; margin-top: .3rem; }
.lv-typecard-label { font-size: .625rem; font-weight: 700; letter-spacing: .03em; text-transform: uppercase; margin-top: .25rem; }

/* ── Grids ────────────────────────────────────────────────────── */
.lv-grid table { margin-bottom: 0; }
.lv-grid thead th { font-size: .625rem; text-transform: uppercase; letter-spacing: .05em;
                    color: #858796; font-weight: 700; border-top: 0; background: #f8f9fc; white-space: nowrap; }
.lv-grid tbody td { font-size: .8rem; vertical-align: middle; white-space: nowrap; }

/* ── Coverage-gap notice ──────────────────────────────────────── */
.lv-gap { background: #fdf6ec; border: 1px solid #f5d9a8; border-left: 3px solid #f59e0b;
          border-radius: .35rem; padding: .85rem 1rem; display: flex; gap: .75rem; }
.lv-gap-title { font-size: .85rem; font-weight: 700; color: #8a6d3b; }
.lv-gap-body { font-size: .78rem; color: #8a6d3b; line-height: 1.55; }
.lv-gap-list { display: flex; flex-wrap: wrap; gap: .4rem; margin-top: .5rem; }
.lv-gap-chip { background: #fff; border: 1px solid #f5d9a8; border-radius: 99px;
               font-size: .72rem; font-weight: 600; color: #8a6d3b; padding: .15rem .65rem; }
.lv-gap-chip span { font-weight: 400; opacity: .8; }

@media (max-width: 575.98px) {
    .lv-metric { flex: 1 1 50%; border-bottom: 1px solid #e3e6f0; }
}
CSS
);

// ── Shared column builders ──────────────────────────────────────
$colOfficer = [
    'label'  => Yii::t('app', 'Officer Name'),
    'format' => 'raw',
    // On the DG's dashboard, flag the rows that are only here because their
    // district has no AD — otherwise they are indistinguishable from the
    // AD / ITD / Director requests the DG is supposed to approve.
    'value'  => function ($model) use ($isDG, $adDistricts) {
        $cell = LeaveTheme::officerCell($model);

        if ($isDG && $adDistricts !== null && LeaveTheme::isDgFallback($model, $adDistricts)) {
            $cell .= ' <span style="background:#fdf6ec; color:#b45309; font-size:.6rem;'
                   . ' font-weight:700; letter-spacing:.04em; text-transform:uppercase;'
                   . ' padding:.1rem .45rem; border-radius:99px; white-space:nowrap;"'
                   . ' title="' . Yii::t('app', 'This district has no Assistant Director, so the request falls back to you.') . '">'
                   . Yii::t('app', 'No AD') . '</span>';
        }

        return $cell;
    },
];

$colNic = [
    'attribute' => 'user_id',
    'label'     => Yii::t('app', 'Officer NIC'),
    'format'    => 'raw',
    'value'     => function ($model) {
        return '<span style="font-variant-numeric:tabular-nums; color:#858796;">'
             . Html::encode($model->user ? $model->user->nic : '—') . '</span>';
    },
];

$colRole = [
    'attribute' => 'user_type',
    'label'     => Yii::t('app', 'Role'),
    'format'    => 'raw',
    'value'     => function ($model) {
        return LeaveTheme::roleText($model->user_type);
    },
];

$colType = [
    'attribute' => 'leave_type',
    'label'     => Yii::t('app', 'Leave Type'),
    'format'    => 'raw',
    'value'     => function ($model) {
        return LeaveTheme::typeBadge($model);
    },
];

$colDates = [
    'label'  => Yii::t('app', 'Dates'),
    'format' => 'raw',
    'value'  => function ($model) {
        return '<span style="color:#858796;">' . Html::encode(
            date('M d', strtotime($model->start_date))
            . ' → '
            . date('M d, Y', strtotime($model->end_date))
        ) . '</span>';
    },
];

$colDays = [
    'attribute' => 'total_days',
    'label'     => Yii::t('app', 'Days'),
    'format'    => 'raw',
    'value'     => function ($model) {
        return LeaveTheme::daysCell($model);
    },
];

$colProgress = [
    'label'  => Yii::t('app', 'Progress'),
    'format' => 'raw',
    'value'  => function ($model) {
        return LeaveTheme::dots($model);
    },
];

$colRequestedAt = [
    'attribute' => 'created_at',
    'label'     => Yii::t('app', 'Requested At'),
    'format'    => 'raw',
    'value'     => function ($model) {
        return '<span style="color:#858796;">'
             . ($model->created_at ? date('M d, h:i A', strtotime($model->created_at)) : '—')
             . '</span>';
    },
];

$colActions = [
    'label'  => Yii::t('app', 'Actions'),
    'format' => 'raw',
    'value'  => function ($model) {
        return LeaveTheme::actionButton($model);
    },
];

// ── Pending queue columns ───────────────────────────────────────
// Status is omitted here on purpose: every row in this table is PENDING,
// so the column would repeat one word down the whole grid. "Waiting" sits
// in roughly the same space and actually differs per row.
$pendingColumns = [
    $colOfficer,
    $colNic,
    $colRole,
    $colType,
    $colDates,
    $colDays,
    $colProgress,
    [
        'label'  => Yii::t('app', 'Waiting'),
        'format' => 'raw',
        'value'  => function ($model) {
            return LeaveTheme::waitingCell($model);
        },
    ],
    $colRequestedAt,
    $colActions,
];

// ── History columns (Status matters here — it varies) ───────────
$historyColumns = [
    $colOfficer,
    $colNic,
    $colRole,
    $colType,
    $colDates,
    $colDays,
    [
        'attribute' => 'status',
        'label'     => Yii::t('app', 'Status'),
        'format'    => 'raw',
        // Was ucfirst(strtolower(...)), which printed "Pending_cc".
        'value'     => function ($model) {
            return LeaveTheme::statusBadge($model);
        },
    ],
    $colProgress,
    $colRequestedAt,
    $colActions,
];

// Carry the current filters into the export so the download matches
// exactly what is shown on screen (minus pagination).
$exportParams = Yii::$app->request->queryParams;
unset($exportParams['history-page'], $exportParams['history-sort'], $exportParams['pending-page']);

// Metric strip definition
$metrics = [
    [
        'label'  => Yii::t('app', 'Total'),
        'count'  => $totalCount,
        'icon'   => 'fas fa-calendar-alt',
        'bg'     => '#eef2ff',
        'fg'     => '#4338ca',
        'accent' => null,
        'status' => null,
    ],
    [
        'label'  => Yii::t('app', 'Awaiting you'),
        'count'  => $pendingCount,
        'icon'   => 'fas fa-clock',
        'bg'     => '#fdf6ec',
        'fg'     => '#b45309',
        'accent' => '#f59e0b',   // the only metric that represents work
        'status' => Leave::STATUS_PENDING,
    ],
    [
        'label'  => Yii::t('app', 'Approved'),
        'count'  => $approvedCount,
        'icon'   => 'fas fa-check-circle',
        'bg'     => '#e6f6ef',
        'fg'     => '#0f8b62',
        'accent' => null,
        'status' => Leave::STATUS_APPROVED,
    ],
    [
        'label'  => Yii::t('app', 'Rejected'),
        'count'  => $rejectedCount,
        'icon'   => 'fas fa-times-circle',
        'bg'     => '#fdeef2',
        'fg'     => '#cc2b5e',
        'accent' => null,
        'status' => Leave::STATUS_REJECTED,
    ],
];

$breakdownTotal = array_sum($leaveTypeBreakdown);
$typeLabels     = Leave::leaveTypeOptions();
?>

<div class="col-xl-12">

    <!-- ── Header ─────────────────────────────────────────────────── -->
    <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-3">
        <?php // No heading here: the layout already prints it (DG Dashboard /
              // IT Director Dashboard / Assistant Director Dashboard) above
              // the breadcrumbs. ?>
        <div class="mb-2 mb-md-0 lv-page-desc">
            <?= Yii::t('app', 'Review and action the leave requests in your queue') ?>
            <span class="text-muted">&middot; <?= $year ?></span>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                    id="exportAllBtn" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-download mr-1"></i><?= Yii::t('app', 'Export') ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="exportAllBtn">
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
    </div>

    <!-- ── 1. Metric strip ────────────────────────────────────────── -->
    <div class="lv-metrics mb-4">
        <?php foreach ($metrics as $m):
            $url = $m['status']
                ? Url::to(['/leave/director-dashboard', 'LeaveSearch[status]' => $m['status']])
                : Url::to(['/leave/director-dashboard']);
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

    <!-- ── 2. Approved requests by type: bar + cards ──────────────── -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-xs font-weight-bold text-uppercase text-muted">
                    <i class="fas fa-chart-bar mr-1"></i>
                    <?= Yii::t('app', 'Approved Requests by Type') ?>
                </span>
                <span class="text-muted" style="font-size:.78rem;">
                    <?= Yii::t('app', '{n} total', ['n' => (int) $breakdownTotal]) ?>
                </span>
            </div>

            <?php if ($breakdownTotal > 0): ?>
            <div class="lv-typebar mb-3">
                <?php foreach ($leaveTypeBreakdown as $type => $count):
                    if ($count <= 0) { continue; }
                    $c = LeaveTheme::type($type);
                    $w = round(($count / $breakdownTotal) * 100, 2);
                ?>
                    <span style="width: <?= $w ?>%; background: <?= $c['border'] ?>;"
                          title="<?= Html::encode(($typeLabels[$type] ?? $type) . ': ' . $count) ?>"></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="lv-typecards">
                <?php foreach ($leaveTypeBreakdown as $type => $count):
                    $c     = LeaveTheme::type($type);
                    $label = trim(preg_replace('/\s*leave\s*$/i', '', $typeLabels[$type] ?? $type));
                    $zero  = ((int) $count === 0);

                    // A zero type stays in place but goes grey, so the grid
                    // does not reflow as counts change month to month.
                    $bg     = $zero ? '#f8f9fc' : $c['bg'];
                    $accent = $zero ? '#dddfeb' : $c['border'];
                    $fg     = $zero ? '#a3a5b3' : $c['icon'];
                ?>
                <div class="lv-typecard" style="background: <?= $bg ?>; border-left: 3px solid <?= $accent ?>;">
                    <i class="<?= $c['fa'] ?>" style="color: <?= $fg ?>; font-size:.9rem;"></i>
                    <div class="lv-typecard-value" style="color: <?= $fg ?>;"><?= (int) $count ?></div>
                    <div class="lv-typecard-label" style="color: <?= $fg ?>; opacity: <?= $zero ? '1' : '.8' ?>;">
                        <?= Html::encode($label) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <?php
    // ── Why district staff are in the DG's queue ────────────────────
    // Shown only when there is actually a gap, so the dashboard carries no
    // permanent warning box once every district has an AD.
    if ($isDG && (!empty($coverageGaps) || $unroutableCount > 0)):
        $gapTotal = 0;
        foreach ($coverageGaps as $g) {
            $gapTotal += $g['total'];
        }
    ?>
    <div class="lv-gap mb-4">
        <i class="fas fa-exclamation-triangle" style="color:#b45309; font-size:1.05rem; flex:none; margin-top:.15rem;"></i>
        <div style="flex:1; min-width:0;">

            <?php if (!empty($coverageGaps)): ?>
                <div class="lv-gap-title">
                    <?= Yii::t('app', 'Some districts have no Assistant Director') ?>
                </div>
                <div class="lv-gap-body">
                    <?= Yii::t('app', 'Leave from a district office is normally approved by that district\'s AD. These districts have no AD account, so {n} request(s) have fallen through to you instead. Registering an AD for each district will route them correctly from then on.', [
                        'n' => $gapTotal,
                    ]) ?>
                </div>
                <div class="lv-gap-list">
                    <?php foreach ($coverageGaps as $g): ?>
                        <span class="lv-gap-chip">
                            <?= Html::encode($g['name']) ?>
                            <span>&middot; <?= (int) $g['total'] ?></span>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($unroutableCount > 0): ?>
                <div class="lv-gap-body <?= !empty($coverageGaps) ? 'mt-2' : '' ?>">
                    <strong><?= Yii::t('app', 'Incomplete profiles:') ?></strong>
                    <?= Yii::t('app', '{n} request(s) come from officers with no district on their profile, so they cannot be routed to any AD. Those officers need to complete their profile.', [
                        'n' => $unroutableCount,
                    ]) ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
    <?php endif; ?>

    <!-- ── 3. Pending queue ───────────────────────────────────────── -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong style="font-size:.9rem;">
                <i class="fas fa-clock mr-1 <?= $pendingCount > 0 ? 'text-warning' : 'text-muted' ?>"></i>
                <?= Yii::t('app', 'Awaiting Your Approval') ?>
            </strong>
            <?php if ($pendingCount > 0): ?>
                <span style="background:#fdf6ec; color:#b45309; font-size:.7rem; font-weight:700;
                             padding:.15rem .65rem; border-radius:99px;">
                    <?= (int) $pendingCount ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $pendingDataProvider,
                'filterModel'  => null,
                'pager'        => [
                    'class'          => LinkPager::class,
                    'firstPageLabel' => 'First',
                    'lastPageLabel'  => 'Last',
                ],
                'layout'       => "{items}\n<div class=\"px-3 py-2\">{pager}</div>",
                'options'      => ['class' => 'grid-view table-responsive lv-grid mb-0'],
                'tableOptions' => ['class' => 'table table-hover mb-0 lv-rtable'],
                'emptyText'    => '<div class="text-center text-muted py-4">'
                                . '<i class="fas fa-check-circle fa-2x mb-2 d-block" style="color:#9fd9c0;"></i>'
                                . Yii::t('app', 'Nothing is waiting on you right now.')
                                . '</div>',
                'emptyTextOptions' => ['tag' => 'div'],
                'columns'      => $pendingColumns,
            ]); ?>
        </div>
    </div>

    <!-- ── Search / Filter ────────────────────────────────────────── -->
    <?= $this->render('/leave/_search', [
        'model'        => $searchModel,
        'showAll'      => true,
        'action'       => ['director-dashboard'],
        // Lets the panel show "N results" and build the active-filter chips.
        'dataProvider' => $historyDataProvider,
    ]); ?>

    <!-- ── Full searchable history ────────────────────────────────── -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white">
            <strong style="font-size:.9rem;">
                <i class="fas fa-list mr-1 text-muted"></i><?= Yii::t('app', 'All Leave Requests') ?>
            </strong>
        </div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $historyDataProvider,
                'filterModel'  => null,
                'pager'        => [
                    'class'          => LinkPager::class,
                    'firstPageLabel' => 'First',
                    'lastPageLabel'  => 'Last',
                ],
                'layout'       => "{summary}\n{items}\n<div class=\"px-3 py-2\">{pager}</div>",
                'options'      => ['class' => 'grid-view table-responsive lv-grid mb-0'],
                'tableOptions' => ['class' => 'table table-hover mb-0 lv-rtable'],
                'summaryOptions' => ['class' => 'px-3 pt-3 text-muted', 'style' => 'font-size:.78rem;'],
                'emptyText'    => Yii::t('app', 'No leave requests found.'),
                'columns'      => $historyColumns,
            ]); ?>
        </div>
    </div>

</div>