<?php

use backend\config\Constant;
use backend\config\LeaveTheme;
use backend\config\UserTypeUtil;
use backend\models\Leave;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\LeaveSearch $model */
/** @var bool  $showAll        whether to show the all-employees filters (NIC + Role) */
/** @var array $action         route the form submits to, e.g. ['index'] or ['director-dashboard'] */
/** @var yii\data\ActiveDataProvider|null $dataProvider  optional — used for the result count */

// ── Defaults so the partial keeps working if a caller omits params ──
$showAll = isset($showAll)
    ? $showAll
    : (UserTypeUtil::isDG()
        || UserTypeUtil::hasType(Constant::ITD)
        || UserTypeUtil::hasType(Constant::AD));

$action       = isset($action) ? $action : ['index'];
$dataProvider = isset($dataProvider) ? $dataProvider : null;

// ── Current query, used to build the chip "remove" links ────────────
$query  = Yii::$app->request->queryParams;
$search = isset($query['LeaveSearch']) && is_array($query['LeaveSearch'])
    ? $query['LeaveSearch']
    : [];

/**
 * URL for the current page with ONE filter removed.
 * Pagination is dropped too — removing a filter should return to page 1.
 */
$removeUrl = function ($key) use ($action, $query) {
    if (isset($query['LeaveSearch'][$key])) {
        unset($query['LeaveSearch'][$key]);
    }
    if (empty($query['LeaveSearch'])) {
        unset($query['LeaveSearch']);
    }
    unset($query['page'], $query['history-page'], $query['pending-page']);

    return Url::to(array_merge($action, $query));
};

// "Clear all" keeps only the view mode, never the filters.
$clearParams = [];
if (isset($query['mode'])) {
    $clearParams['mode'] = $query['mode'];
}
$clearUrl = Url::to(array_merge($action, $clearParams));

// ── Build the active-filter chips ───────────────────────────────────
$typeLabels   = Leave::leaveTypeOptions();
$statusLabels = Leave::statusOptions();
$roleLabels   = array_map(function ($v) { return $v['name']; }, Constant::$userTypes);

$neutralChip = ['bg' => '#eaecf4', 'fg' => '#6e707e'];
$chips       = [];

if ($showAll && !empty($search['employee_nic'])) {
    $chips[] = [
        'label'  => Yii::t('app', 'Search') . ': ' . $search['employee_nic'],
        'colors' => $neutralChip,
        'url'    => $removeUrl('employee_nic'),
    ];
}

if ($showAll && (!empty($search['employee_type']) || (isset($search['employee_type']) && $search['employee_type'] !== ''))) {
    $roleKey = (string) $search['employee_type'];
    if ($roleKey !== '') {
        $chips[] = [
            'label'  => Yii::t('app', 'Role') . ': ' . ($roleLabels[$roleKey] ?? $roleKey),
            'colors' => $neutralChip,
            'url'    => $removeUrl('employee_type'),
        ];
    }
}

if (!empty($search['leave_type'])) {
    $c = LeaveTheme::type($search['leave_type']);
    $chips[] = [
        'label'  => Yii::t('app', 'Type') . ': '
                  . trim(preg_replace('/\s*leave\s*$/i', '', $typeLabels[$search['leave_type']] ?? $search['leave_type'])),
        'colors' => ['bg' => $c['bg'], 'fg' => $c['icon']],
        'url'    => $removeUrl('leave_type'),
    ];
}

if (!empty($search['status'])) {
    // Same palette as the status pill in the grid, so a chip and its rows match.
    $c = LeaveTheme::STATUSES[$search['status']] ?? $neutralChip;
    $chips[] = [
        'label'  => Yii::t('app', 'Status') . ': ' . ($statusLabels[$search['status']] ?? $search['status']),
        'colors' => $c,
        'url'    => $removeUrl('status'),
    ];
}

if (!empty($search['start_date'])) {
    $chips[] = [
        'label'  => Yii::t('app', 'From') . ': ' . $search['start_date'],
        'colors' => $neutralChip,
        'url'    => $removeUrl('start_date'),
    ];
}

if (!empty($search['end_date'])) {
    $chips[] = [
        'label'  => Yii::t('app', 'To') . ': ' . $search['end_date'],
        'colors' => $neutralChip,
        'url'    => $removeUrl('end_date'),
    ];
}

// A filled field gets an indigo outline — a cue for which filters are set
// without having to read every dropdown.
$fieldCss = function ($filled) {
    return $filled ? 'lv-filter-input lv-filter-filled' : 'lv-filter-input';
};

$this->registerCss(<<<CSS
.lv-filter { border: 1px solid #e3e6f0; border-radius: .5rem; background: #fff; padding: 1rem 1.1rem; margin-bottom: 1.5rem; }
.lv-filter-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: .9rem; }
.lv-filter-title { font-size: .625rem; color: #858796; text-transform: uppercase; letter-spacing: .05em; font-weight: 700; }
.lv-filter-count { font-size: .78rem; color: #858796; }

.lv-filter-label { font-size: .625rem; color: #858796; text-transform: uppercase; letter-spacing: .04em; font-weight: 700; margin-bottom: .25rem; display: block; }
.lv-filter-input { width: 100%; border: 1px solid #d1d3e2; border-radius: .5rem; padding: .4rem .65rem;
                   font-size: .82rem; color: #5a5c69; background: #fff; height: auto; }
.lv-filter-input:focus { border-color: #6366f1; box-shadow: 0 0 0 .15rem rgba(99,102,241,.12); outline: 0; }
.lv-filter-filled { border-color: #b1bafa; }

.lv-filter-search { position: relative; margin-bottom: .7rem; }
.lv-filter-search .lv-filter-input { padding-left: 2.1rem; }
.lv-filter-search-icon { position: absolute; left: .7rem; top: 50%; transform: translateY(-50%);
                         color: #b7b9cc; font-size: .85rem; pointer-events: none; }

.lv-filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: .6rem; margin-bottom: .9rem; }
.lv-filter-range { display: flex; align-items: center; gap: .25rem; }
.lv-filter-range .lv-filter-input { flex: 1 1 0; min-width: 0; }
.lv-filter-range-sep { color: #b7b9cc; font-size: .8rem; flex: none; }

.lv-filter-foot { display: flex; align-items: center; flex-wrap: wrap; gap: .5rem;
                  padding-top: .75rem; border-top: 1px solid #eaecf4; }
.lv-filter-divider { width: 1px; height: 20px; background: #e3e6f0; margin: 0 .15rem; }
.lv-filter-active { font-size: .625rem; color: #858796; text-transform: uppercase; letter-spacing: .04em; font-weight: 700; }
.lv-chip { display: inline-flex; align-items: center; gap: .25rem; font-size: .7rem; font-weight: 600;
           letter-spacing: .02em; padding: .15rem .35rem .15rem .6rem; border-radius: 99px; text-decoration: none; }
.lv-chip:hover { text-decoration: none; opacity: .82; }
.lv-chip i { font-size: .7rem; }
CSS
);
?>
<div class="lv-filter">

    <div class="lv-filter-head">
        <span class="lv-filter-title">
            <i class="fas fa-sliders-h mr-1"></i>
            <?= Yii::t('app', 'Filters') ?>
        </span>
        <?php if ($dataProvider !== null): ?>
            <span class="lv-filter-count">
                <?= Yii::t('app', '{n} results', ['n' => (int) $dataProvider->getTotalCount()]) ?>
            </span>
        <?php endif; ?>
    </div>

    <?php $form = ActiveForm::begin([
        'action'  => $action,
        'method'  => 'get',
        'options' => ['data-pjax' => 1],
    ]); ?>

        <?php // Keep the personal/all view mode across a search submit. ?>
        <?php if (isset($query['mode'])): ?>
            <?= Html::hiddenInput('mode', $query['mode']) ?>
        <?php endif; ?>

        <?php if ($showAll): ?>
        <!-- ── Officer name or NIC: the field people actually use ── -->
        <div class="lv-filter-search">
            <i class="fas fa-search lv-filter-search-icon"></i>
            <?= $form->field($model, 'employee_nic', [
                    'template' => '{input}',
                    'options'  => ['class' => 'mb-0'],
                ])->textInput([
                    'class'       => $fieldCss(!empty($search['employee_nic'])),
                    'placeholder' => Yii::t('app', 'Search by officer name or NIC'),
                ])->label(false) ?>
        </div>
        <?php endif; ?>

        <div class="lv-filter-grid">

            <?php if ($showAll): ?>
            <div>
                <span class="lv-filter-label"><?= Yii::t('app', 'Role') ?></span>
                <?= $form->field($model, 'employee_type', [
                        'template' => '{input}',
                        'options'  => ['class' => 'mb-0'],
                    ])->dropDownList($roleLabels, [
                        'prompt' => Yii::t('app', 'All roles'),
                        'class'  => $fieldCss(!empty($search['employee_type'])),
                    ])->label(false) ?>
            </div>
            <?php endif; ?>

            <div>
                <span class="lv-filter-label"><?= Yii::t('app', 'Leave type') ?></span>
                <?= $form->field($model, 'leave_type', [
                        'template' => '{input}',
                        'options'  => ['class' => 'mb-0'],
                    ])->dropDownList(Leave::leaveTypeOptions(), [
                        'prompt' => Yii::t('app', 'All types'),
                        'class'  => $fieldCss(!empty($search['leave_type'])),
                    ])->label(false) ?>
            </div>

            <div>
                <span class="lv-filter-label"><?= Yii::t('app', 'Status') ?></span>
                <?= $form->field($model, 'status', [
                        'template' => '{input}',
                        'options'  => ['class' => 'mb-0'],
                    ])->dropDownList(Leave::statusOptions(), [
                        'prompt' => Yii::t('app', 'All statuses'),
                        'class'  => $fieldCss(!empty($search['status'])),
                    ])->label(false) ?>
            </div>

            <!-- ── One "Date range" slot instead of two full columns ── -->
            <div>
                <span class="lv-filter-label"><?= Yii::t('app', 'Date range') ?></span>
                <div class="lv-filter-range">
                    <?= $form->field($model, 'start_date', [
                            'template' => '{input}',
                            'options'  => ['class' => 'mb-0 flex-fill'],
                        ])->input('date', [
                            'class' => $fieldCss(!empty($search['start_date'])),
                            'title' => Yii::t('app', 'From date'),
                        ])->label(false) ?>
                    <span class="lv-filter-range-sep">&rarr;</span>
                    <?= $form->field($model, 'end_date', [
                            'template' => '{input}',
                            'options'  => ['class' => 'mb-0 flex-fill'],
                        ])->input('date', [
                            'class' => $fieldCss(!empty($search['end_date'])),
                            'title' => Yii::t('app', 'To date'),
                        ])->label(false) ?>
                </div>
            </div>

        </div>

        <div class="lv-filter-foot">
            <?= Html::submitButton(
                '<i class="fas fa-search mr-1"></i>' . Yii::t('app', 'Search'),
                ['class' => 'btn btn-primary btn-sm']
            ) ?>

            <?php if (!empty($chips)): ?>
                <span class="lv-filter-divider"></span>
                <span class="lv-filter-active"><?= Yii::t('app', 'Active') ?></span>

                <?php foreach ($chips as $chip): ?>
                    <?= Html::a(
                        Html::encode($chip['label']) . '<i class="fas fa-times"></i>',
                        $chip['url'],
                        [
                            'class' => 'lv-chip',
                            'style' => 'background:' . $chip['colors']['bg'] . '; color:' . $chip['colors']['fg'] . ';',
                            'title' => Yii::t('app', 'Remove this filter'),
                        ]
                    ) ?>
                <?php endforeach; ?>

                <?php // Only offered when there is actually something to clear. ?>
                <span class="ml-auto">
                    <?= Html::a(Yii::t('app', 'Clear all'), $clearUrl, ['class' => 'text-muted small']) ?>
                </span>
            <?php endif; ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>