<?php

// throw new \Exception('Testing custom error page');
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use yii\helpers\Html;

$this->title = Yii::t('app', 'E Log View');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$exportMenu = [
    [
        'attribute' => 'vessel_id',
        'label' => 'Vessel ID',
    ],
    [
        'attribute' => 'skipper_id',
        'label' => 'Skipper ID',
    ],
    [
        'attribute' => 'arrival_date',
        'label' => 'Arrival Date',
    ],
    [
        'attribute' => 'departure_date',
        'label' => 'Departure Date',
    ],
    [
        'attribute' => 'arrival_harbour_name',
        'label' => 'Arrival Harbour',
        'value' => function ($model) {
            return $model->arrivalHarbour->Name ?? 'N/A';
        },
    ],
    [
        'attribute' => 'departure_harbour_name',
        'label' => 'Departure Harbour',
        'value' => function ($model) {
            return $model->departureHarbour->Name ?? 'N/A';
        },
    ],
    [
        'attribute' => 'log_sheet_number',
        'label' => 'Log Book No',
    ],

    [
        'attribute' => 'log_book_no',
        'label' => 'Log Page No',
    ],
];
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <?php if (UserTypeUtil::hasType(Constant::AD_Highseas) || UserTypeUtil::hasType(Constant::ITD)) { ?>
    <?= Html::a('Report View', ['report'], ['class' => 'btn btn-secondary me-2 mb-4']) ?>
    <?= Html::a('Catch Report View', ['report-view'], ['class' => 'btn btn-secondary me-2 mb-4']) ?>
<?php } ?>
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $exportMenu,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
                'dropdownOptions' => [
                    'label' => 'Export All',
                    'class' => 'btn btn-outline-secondary btn-default'
                ]
            ]);
            ?>

            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,

                'columns' => [

                    [
                        'attribute' => 'log_sheet_number',
                        'label' => 'Log Book No',
                        'value' => function ($model) {
                            return $model->log_sheet_number;
                        }
                    ],

                    [
                        'attribute' => 'log_book_no',
                        'label' => 'Log Page No',
                        'value' => function ($model) {
                            return $model->log_book_no;
                        }
                    ],
                    [
                        'attribute' => 'vessel_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->vessel_id ?? 'N/A';
                        }
                    ],
                    [
                        'attribute' => 'skipper_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->skipper_id ?? 'N/A';
                        }
                    ],
                    [
                        'attribute' => 'arrival_date',
                        'filter' => \yii\helpers\Html::activeInput(
                            'date',
                            $searchModel,
                            'arrival_date',
                            ['class' => 'form-control']
                        ),
                    ],

                    [
                        'attribute' => 'departure_date',
                        'filter' => \yii\helpers\Html::activeInput(
                            'date',
                            $searchModel,
                            'departure_date',
                            ['class' => 'form-control']
                        ),
                    ],
                    [
                        'attribute' => 'arrival_harbour_name',
                        'label' => 'Arrival Harbour',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->arrivalHarbour->Name ?? 'N/A';
                            //                            ^^^^  capital N
                        }
                    ],
                    [
                        'attribute' => 'departure_harbour_name',
                        'label' => 'Departure Harbour',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->departureHarbour->Name ?? 'N/A';
                            //                              ^^^^  capital N
                        }
                    ],
                    [
                        'attribute' => 'approve',
                        'label' => 'Status',
                        'format' => 'raw',
                        'filter' => ['' => 'All', '1' => 'Approved', '0' => 'Not Approved'],
                        'value' => function ($model) {
                            return $model->approve
                                ? '<span class="badge badge-success">Approved</span>'
                                : '<span class="badge badge-secondary">Pending</span>';
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Action',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return \yii\helpers\Html::a(
                                    '<i class="fas fa-eye"></i> View',
                                    ['e-log-view/view', 'id' => $model->id],
                                    ['class' => 'btn btn-primary btn-sm']
                                );
                            },
                        ],
                    ],


                ],
            ]); ?>

        </div>
    </div>
</div>