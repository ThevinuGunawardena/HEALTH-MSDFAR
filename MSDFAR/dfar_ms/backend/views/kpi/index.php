<?php

use backend\config\Constant;
use backend\controllers\KpiController;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use backend\models\ProfileOfficer;
use yii\bootstrap4\Tabs;

/** @var yii\web\View $this */
/** @var backend\models\KpiSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'KPIs');
$this->params['breadcrumbs'][] = $this->title;

// --- Setup Unique DataProviders for the explicit Tab Filters ---
$currentUserId = Yii::$app->user->id;

// 1. Supervising DataProvider Configuration
$supervisingDataProvider = clone $dataProvider;
$supervisingDataProvider->query->andWhere(['supervisor' => $currentUserId]);
// Isolate pagination and sorting namespaces
$supervisingDataProvider->pagination = [
    'pageParam' => 'supervising-page',
    'pageSizeParam' => 'supervising-per-page',
];
$supervisingDataProvider->sort = [
    'sortParam' => 'supervising-sort',
    'defaultOrder' => ['KPIId' => SORT_DESC],
];

// 2. Responsible DataProvider Configuration
$responsibleDataProvider = clone $dataProvider;
$responsibleDataProvider->query->andWhere(['responsibility' => $currentUserId]);
// Isolate pagination and sorting namespaces
$responsibleDataProvider->pagination = [
    'pageParam' => 'responsible-page',
    'pageSizeParam' => 'responsible-per-page',
];
$responsibleDataProvider->sort = [
    'sortParam' => 'responsible-sort',
    'defaultOrder' => ['KPIId' => SORT_DESC],
];

// 3. Main DataProvider Configuration (All KPIs tab)
$dataProvider->pagination = [
    'pageParam' => 'all-page',
    'pageSizeParam' => 'all-per-page',
];
$dataProvider->sort = [
    'sortParam' => 'all-sort',
    'defaultOrder' => ['KPIId' => SORT_DESC],
];

// ==========================================
// --- DASHBOARD METRICS AGGREGATION BLOCK ---
// ==========================================
$today = date('Y-m-d');
$fourDaysFromNow = date('Y-m-d', strtotime('+4 days'));

// -- My KPIs metrics --
$countSupervising = (int)(clone $supervisingDataProvider->query)->count();
$countAchievedSupervising = (int)(clone $supervisingDataProvider->query)->andWhere(['kpi.status' => 'Achieved'])->count();

$countResponsible = (int)(clone $responsibleDataProvider->query)->count();
$countAchievedResponsible = (int)(clone $responsibleDataProvider->query)->andWhere(['kpi.status' => 'Achieved'])->count();

// Responsible Overdue (Fail-safe condition)
$countOverdueResponsible = (int)(clone $responsibleDataProvider->query)
    ->andWhere([
        'or',
        ['kpi.status' => 'Overdue'],
        [
            'and',
            ['not in', 'kpi.status', ['Achieved']],
            ['<', 'kpi.targetDate', $today]
        ]
    ])
    ->count();

$countDueSoonResponsible = (int)(clone $responsibleDataProvider->query)
    ->andWhere(['between', 'kpi.targetDate', $today, $fourDaysFromNow])
    ->andWhere(['not in', 'kpi.status', ['Achieved']])
    ->count();

// Calculate Responsibility Completion Rate Safely
$responsibilityCompletionRate = $countResponsible > 0 ? round(($countAchievedResponsible / $countResponsible) * 100, 1) : 0.0;


// -- All KPIs metrics (Corporate Overview Section) --
$countAllKpis = (int)(clone $dataProvider->query)->count();
$countAllPending = (int)(clone $dataProvider->query)->andWhere(['or', ['kpi.status' => 'Pending'], ['kpi.status' => ''], ['kpi.status' => null]])->count();
$countAllInProgress = (int)(clone $dataProvider->query)->andWhere(['kpi.status' => 'In Progress'])->count();
$countAllAchieved = (int)(clone $dataProvider->query)->andWhere(['kpi.status' => 'Achieved'])->count();

// Global Corporate Overdue (Fail-safe condition applied here too)
$countAllOverdue = (int)(clone $dataProvider->query)
    ->andWhere([
        'or',
        ['kpi.status' => 'Overdue'],
        [
            'and',
            ['not in', 'kpi.status', ['Achieved']],
            ['<', 'kpi.targetDate', $today]
        ]
    ])
    ->count();

$allCompletionRate = $countAllKpis > 0 ? round(($countAllAchieved / $countAllKpis) * 100, 1) : 0.0;

// $isAuthorized = (!Yii::$app->user->isGuest && in_array(Yii::$app->user->identity->type, [15]));

// --- Strictly evaluate permission context using root namespace definitions ---
$isAuthorized = (!Yii::$app->user->isGuest && in_array((int)Yii::$app->user->identity->type, [
    \backend\config\Constant::ITD, // Evaluates explicitly to 15
    \backend\config\Constant::AD,   // Evaluates explicitly to 3
    \backend\config\Constant::DIRECTOR   // Evaluates explicitly to 19
]));


// Injecting the PHP variable securely into the browser console
$this->registerJs("
    console.log('Current User ID:', " . json_encode($currentUserId) . ");
", \yii\web\View::POS_READY);


// --- Core Shared Columns (Common across all tabs) ---
$baseGridColumns = [
    [
        'attribute' => 'KPIId',
        'filter' => Html::activeTextInput($searchModel, 'KPIId', ['class' => 'form-control']),
    ],
    [
        'attribute' => 'title',
        'filter' => Html::activeTextInput($searchModel, 'title', ['class' => 'form-control']),
    ],
    [
        'attribute' => 'indicator',
        'filter' => Html::activeTextInput($searchModel, 'indicator', ['class' => 'form-control']),
    ],
    [
        'attribute' => 'unit',
        'filter' => Html::activeTextInput($searchModel, 'unit', ['class' => 'form-control']),
    ],
    [
        'attribute' => 'target',
        'filter' => Html::activeTextInput($searchModel, 'target', ['class' => 'form-control']),
        'value' => function ($model) {
            return $model->target !== null ? number_format($model->target, 2) : null;
        },
    ],
    [
        'attribute' => 'createdDate',
        'filter' => Html::activeTextInput($searchModel, 'createdDate', ['class' => 'form-control', 'type' => 'date']),
    ],
    [
        'attribute' => 'assignedDate',
        'filter' => Html::activeTextInput($searchModel, 'assignedDate', ['class' => 'form-control', 'type' => 'date']),
    ],
    [
        'attribute' => 'targetDate',
        'filter' => Html::activeTextInput($searchModel, 'targetDate', ['class' => 'form-control', 'type' => 'date']),
    ],
    [
        'attribute' => 'divisionId',
        'label' => 'Division',
        'value' => function ($model) {
            return $model->division ? $model->division->divisionName : '(not set)';
        },
        'filter' => Html::activeTextInput($searchModel, 'divisionName', ['class' => 'form-control', 'placeholder' => 'Search Division...']),
    ],
    [
        'attribute' => 'progress',
        'filter' => Html::activeTextInput($searchModel, 'progress', ['class' => 'form-control']),
    ],
    [
        'attribute' => 'status',
        'filter' => Html::activeDropDownList($searchModel, 'status', [
            'Pending' => 'Pending',
            'In Progress' => 'In Progress',
            'Achieved' => 'Achieved',
            'Overdue' => 'Overdue',
        ], [
            'class' => 'form-control custom-select', 
            'prompt' => 'All Statuses'
        ]),
        'value' => function ($model) {
            return !empty($model->status) ? $model->status : 'Pending';
        },
    ],
    [
        'attribute' => 'supervisor',
        'label' => 'Supervisor Name',
        'value' => function ($model) {
            if (!empty($model->supervisor)) {
                $profile = ProfileOfficer::find()
                    ->alias('p')
                    ->innerJoin('user u', 'u.profile_id = p.id')
                    ->where(['u.id' => $model->supervisor])
                    ->one();
                return $profile ? $profile->first_name . ' ' . $profile->last_name : 'Unknown Officer';
            }
            return 'Not Assigned';
        },
        'filter' => Html::activeTextInput($searchModel, 'supervisorName', ['class' => 'form-control', 'placeholder' => 'Search Name...']),
    ],
    [
        'attribute' => 'responsibility',
        'label' => 'Responsibility Name',
        'value' => function ($model) {
            if (!empty($model->responsibility)) {
                $profile = ProfileOfficer::find()
                    ->alias('p')
                    ->innerJoin('user u', 'u.profile_id = p.id')
                    ->where(['u.id' => $model->responsibility])
                    ->one();
                return $profile ? $profile->first_name . ' ' . $profile->last_name : 'Unknown Officer';
            }
            return 'Not Assigned';
        },
        'filter' => Html::activeTextInput($searchModel, 'responsibilityName', ['class' => 'form-control', 'placeholder' => 'Search Name...']),
    ],
];

// --- 1. Columns for SUPERVISING Tab (Update Responsible Officer) ---
$supervisingGridColumns = $baseGridColumns;
$supervisingGridColumns[] = [
    'attribute' => 'Action',
    'format' => 'raw',
    'value' => function ($model) {
        return Html::a('<i class="fas fa-user-edit"></i> Assign', ['assign', 'id' => $model->KPIId, 'mode' => 'reassign'], [
            'class' => 'btn btn-sm btn-info text-white shadow-sm px-3',
            'title' => 'Update Accountable Officer for this KPI'
        ]);
    }
];

// --- 2. Columns for RESPONSIBLE Tab (Update Progress Only) ---
$responsibleGridColumns = $baseGridColumns;
$responsibleGridColumns[] = [
    'attribute' => 'Action',
    'format' => 'raw',
    'value' => function ($model) {
        return Html::a('<i class="fas fa-tasks"></i> Progress', ['progress', 'id' => $model->KPIId, 'mode' => 'progress'], [
            'class' => 'btn btn-sm btn-warning text-dark shadow-sm px-3',
            'title' => 'Log current completion progress metrics'
        ]);
    }
];

// --- 3. Columns for ALL KPIs Tab (Standard View Action) ---
$allGridColumns = $baseGridColumns;
$allGridColumns[] = [
    'attribute' => 'Action',
    'format' => 'raw',
    'value' => function ($model) {
        return Html::a('View', ['view', 'id' => $model->KPIId], ['class' => 'btn btn-sm btn-primary px-3']);
    }
];

// 1. Map dynamic tab data arrays
$tabItems = [
    [
        'label' => 'Supervising KPIs',
        'content' => $this->render('_grid_tab', [
            'dataProvider' => $supervisingDataProvider,
            'searchModel' => $searchModel,
            'gridColumns' => $supervisingGridColumns,
        ]),
        'active' => true,
    ],
    [
        'label' => 'Responsible KPIs',
        'content' => $this->render('_grid_tab', [
            'dataProvider' => $responsibleDataProvider,
            'searchModel' => $searchModel,
            'gridColumns' => $responsibleGridColumns,
        ]),
    ],
];

// 2. Conditionally inject the 'All KPIs' tab only if authorized
if ($isAuthorized) {
    $tabItems[] = [
        'label' => 'All KPIs',
        'content' => $this->render('_grid_tab', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'gridColumns' => $allGridColumns,
        ]),
    ];
}

?>

<div class="kpi-index container-fluid py-4">

    <div class="dashboard-metrics-wrapper mb-5">
        
        <h5 class="text-center font-weight-bold mb-3 text-dark text-uppercase tracking-wider">My KPIs</h5>
        <div class="card-deck text-center mb-4">
            <div class="card shadow-sm border-0 bg-white py-2">
                <div class="card-body p-2">
                    <p class="text-muted small font-weight-semibold mb-1 text-uppercase">Supervising KPIs</p>
                    <h2 class="font-weight-bold text-primary mb-0"><?= $countSupervising ?></h2>
                </div>
            </div>
            <div class="card shadow-sm border-0 bg-white py-2">
                <div class="card-body p-2">
                    <p class="text-muted small font-weight-semibold mb-1 text-uppercase">Achieved Supervising</p>
                    <h2 class="font-weight-bold text-success mb-0"><?= $countAchievedSupervising ?></h2>
                </div>
            </div>
            <div class="card shadow-sm border-0 bg-white py-2">
                <div class="card-body p-2">
                    <p class="text-muted small font-weight-semibold mb-1 text-uppercase">Responsible KPIs</p>
                    <h2 class="font-weight-bold text-info mb-0"><?= $countResponsible ?></h2>
                </div>
            </div>
            <div class="card shadow-sm border-0 bg-white py-2">
                <div class="card-body p-2">
                    <p class="text-muted small font-weight-semibold mb-1 text-uppercase">Due within 4 Days</p>
                    <h2 class="font-weight-bold text-warning mb-0"><?= $countDueSoonResponsible ?></h2>
                </div>
            </div>
            <div class="card shadow-sm py-2 bg-white rounded <?= $countOverdueResponsible > 0 ? 'border-2 border-danger alert-danger-card' : 'border-0' ?>">
                <div class="card-body p-2">
                    <p class="<?= $countOverdueResponsible > 0 ? 'text-danger font-weight-bold' : 'text-muted font-weight-semibold' ?> small mb-1 text-uppercase">Overdued</p>
                    <h2 class="font-weight-bold mb-1 <?= $countOverdueResponsible > 0 ? 'text-danger' : 'text-dark' ?>">
                        <?= $countOverdueResponsible ?>
                    </h2>
                    <?php if ($countOverdueResponsible > 0): ?>
                        <span class="badge badge-danger text-uppercase font-weight-bolder px-2 py-1 small-action-text">Action Required</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card shadow-sm border-0 bg-white py-2">
                <div class="card-body p-2">
                    <p class="text-muted small font-weight-semibold mb-1 text-uppercase">Achieved Responsible</p>
                    <h2 class="font-weight-bold text-success mb-0"><?= $countAchievedResponsible ?></h2>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4 bg-white py-2">
            <div class="card-body p-3 text-center">
                <p class="text-muted small font-weight-bold mb-1 text-uppercase">Responsibility Completion Rate</p>
                <h3 class="font-weight-bold text-primary mb-3"><?= $responsibilityCompletionRate ?>%</h3>
                <div class="progress rounded-pill shadow-inner mx-auto" style="height: 10px; max-width: 95%;">
                    <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated rounded-pill" 
                         role="progressbar" 
                         style="width: <?= $responsibilityCompletionRate ?>%" 
                         aria-valuenow="<?= $responsibilityCompletionRate ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>

        <?php if ($isAuthorized): ?>
            <hr class="my-4 border-gray-300">
            <h5 class="text-center font-weight-bold mb-3 text-dark text-uppercase tracking-wider">All KPIs Overview</h5>
            <div class="card-deck text-center mb-4">
                <div class="card shadow-sm border-0 bg-white py-2">
                    <div class="card-body p-2">
                        <p class="text-muted small font-weight-semibold mb-1 text-uppercase">Total Corporate KPIs</p>
                        <h2 class="font-weight-bold text-dark mb-0"><?= $countAllKpis ?></h2>
                    </div>
                </div>
                <div class="card shadow-sm border-0 bg-white py-2">
                    <div class="card-body p-2">
                        <p class="text-muted small font-weight-semibold mb-1 text-uppercase">All Pending</p>
                        <h2 class="font-weight-bold text-secondary mb-0"><?= $countAllPending ?></h2>
                    </div>
                </div>
                <div class="card shadow-sm border-0 bg-white py-2">
                    <div class="card-body p-2">
                        <p class="text-muted small font-weight-semibold mb-1 text-uppercase">In Progress</p>
                        <h2 class="font-weight-bold text-warning mb-0"><?= $countAllInProgress ?></h2>
                    </div>
                </div>
                
                <div class="card shadow-sm py-2 bg-white rounded <?= $countAllOverdue > 0 ? 'border-2 border-danger alert-danger-card' : 'border-0' ?>">
                    <div class="card-body p-2">
                        <p class="<?= $countAllOverdue > 0 ? 'text-danger font-weight-bold' : 'text-muted font-weight-semibold' ?> small mb-1 text-uppercase">Total Overdued</p>
                        <h2 class="font-weight-bold mb-0 <?= $countAllOverdue > 0 ? 'text-danger' : 'text-dark' ?>">
                            <?= $countAllOverdue ?>
                        </h2>
                    </div>
                </div>

                <div class="card shadow-sm border-0 bg-white py-2">
                    <div class="card-body p-2">
                        <p class="text-muted small font-weight-semibold mb-1 text-uppercase">Achieved</p>
                        <h2 class="font-weight-bold text-success mb-0"><?= $countAllAchieved ?></h2>
                    </div>
                </div>
                <div class="card shadow-sm border-0 bg-white py-2">
                    <div class="card-body p-2">
                        <p class="text-muted small font-weight-semibold mb-1 text-uppercase">Global Completion Rate</p>
                        <h2 class="font-weight-bold text-primary mb-0"><?= $allCompletionRate ?>%</h2>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800"><?= Html::encode($this->title) ?></h1>
        <div class="actions-wrapper">
            <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $allGridColumns,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
                'dropdownOptions' => [
                    'label' => 'Export All KPIs',
                    'class' => 'btn btn-outline-secondary px-3 shadow-sm mr-2'
                ]
            ]); ?>

            <?php if ($isAuthorized): ?>
                <?= Html::a(
                    '<i class="fas fa-users-cog"></i> ' . Yii::t('app', 'Officer Mappings'), 
                    ['officer-mapping/index'], 
                    ['class' => 'btn btn-outline-primary px-3 shadow-sm mr-2', 'title' => 'Link Officers to Master KPI Divisions']
                ) ?>
                <?= Html::a(Yii::t('app', 'Create New KPI'), ['create'], ['class' => 'btn btn-success px-4 shadow-sm']) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4 custom-tabs-container">
        
        <?= Tabs::widget([
            'navType' => 'nav-tabs card-header-tabs mb-4',
            'items' => $tabItems,
        ]); ?>

        </div>
    </div>
</div>

<?php
// --- Custom UX CSS Styling Injection ---
$customCss = "
    /* Dashboard & Tracking Layout Configurations */
    .tracking-wider { letter-spacing: 0.05em; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06); }
    .small-action-text { font-size: 65%; letter-spacing: 0.05em; }
    .border-2 { border-width: 2px !important; }
    
    .alert-danger-card {
        border-color: #e74a3b !important;
        background-color: #ffffff !important;
    }

    /* Base Nav Tab Enhancements */
    .custom-tabs-container .nav-tabs {
        border-bottom: 2px solid #e3e6f0;
    }
    
    .custom-tabs-container .nav-tabs .nav-link {
        font-weight: 600;
        color: #5a5c69;
        border: transparent;
        padding: 12px 20px;
        position: relative;
        transition: all 0.25s ease-in-out;
        border-radius: 4px 4px 0 0;
    }

    /* Hover Interaction State */
    .custom-tabs-container .nav-tabs .nav-link:hover {
        color: #4e73df;
        background-color: #aebadb;
        border-color: transparent;
    }

    /* Active Selection State Styles */
    .custom-tabs-container .nav-tabs .nav-item.show .nav-link, 
    .custom-tabs-container .nav-tabs .nav-link.active {
        color: #4e73df !important;
        background-color: #ffffff;
        border-color: transparent;
        font-weight: 700;
    }

    /* Custom Bottom Accent Slider Indicator Line */
    .custom-tabs-container .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background-color: #4e73df;
        border-radius: 3px;
        animation: slideIn 0.2s ease-out;
    }

    @keyframes slideIn {
        from { transform: scaleX(0); }
        to { transform: scaleX(1); }
    }
";
$this->registerCss($customCss);
?>

<?php
// Script to remember active tab state across reloads/filtering
$tabCookieScript = "
    $(document).ready(function() {
        $('.custom-tabs-container .nav-link').on('shown.bs.tab', function (e) {
            localStorage.setItem('activeKpiTab', $(e.target).attr('href'));
        });

        var activeTab = localStorage.getItem('activeKpiTab');
        if (activeTab) {
            $('.custom-tabs-container .nav-tabs a[href=\"' + activeTab + '\"]').tab('show');
        }
    });
";
$this->registerJs($tabCookieScript);
?>