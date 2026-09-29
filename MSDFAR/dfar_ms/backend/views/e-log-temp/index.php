<?php

use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use backend\models\ELogTemp;

$this->title = Yii::t('app', 'E Log Temp View');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,

                'columns' => [

                    [
                        'attribute' => 'vessel_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->vessel_id ?? 'N/A';
                        }
                    ],
                    [
                        'attribute' => 'gear_type',
                        'label' => 'Gear Type',
                        'format' => 'text',
                        'filter' => ELogTemp::gearTypeList(),
                        'value' => function ($model) {
                            return ELogTemp::gearTypeList()[$model->gear_type] ?? $model->gear_type;
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
                        }
                    ],
                    [
                        'attribute' => 'departure_harbour_name',
                        'label' => 'Departure Harbour',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->departureHarbour->Name ?? 'N/A';
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
                                return Html::a(
                                    '<i class="fas fa-eye"></i> View',
                                    ['e-log-temp/view', 'id' => $model->id],
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