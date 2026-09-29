<?php

use backend\components\SecurityHelper;
use backend\models\DepartureBoats;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use backend\config\Constant;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\DepartureBoatsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Departure Boats');
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_search', ['model' => $searchModel]);

?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <!--            <h1>--><?php //= Html::encode($this->title) ?><!--</h1>-->

            <p style="font-weight: bold">
                * <span class="text-danger">Red</span> text indicates licenses that have expired or will expire within
                30
                days.
            </p>


            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
//                'filterModel' => $searchModel,
                'dataProvider' => $dataProvider,

                'columns' => [
//                    ['class' => 'yii\grid\SerialColumn'],

//                    'id',
                    [
                        'attribute' => 'boat_number_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boat->boat_number;
                        }
                    ],
                    
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisherman->preferred_name_for_id ?? 'N/A';
                        }
                    ],
                    [
                        'label' => 'NIC',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisherman->nic ?? 'N/A';
                        }
                    ],
                    [
                        'label' => 'Mobile',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisherman->mobile ?? 'N/A';
                        }
                    ],
                          [
                        'attribute' => 'status',
                        'contentOptions' => function ($model) {

                            if ($model->compulsory_service == 0) {

                                // Violation + compulsory service pending
                                if (!empty($model->status) && $model->status != 'Departure Allowed') {
                                    return ['style' => 'background-color:#ff7474;color:white'];
                                }

                                // Only compulsory service pending
                                return ['style' => 'background-color:#ffff91;'];
                            }

                            if ($model->status == 'Departure Allowed' || $model->status == '') {
                                return ['style' => 'background-color:#55c355;color:white'];
                            }

                            if ($model->status != 'Departure Allowed' && $model->status != '') {
                                return ['style' => 'background-color:#ff7474;color:white'];
                            }

                            return [];
                        },

                        'value' => function ($model) {

                            if ($model->compulsory_service == 0) {

                                if (!empty($model->status) && $model->status != 'Departure Allowed') {
                                    return $model->status . ' & Compulsory Service Pending';
                                }

                                return 'Compulsory Service Pending';
                            }

                            return empty($model->status) ? 'Departure Allowed' : $model->status;
                        }
                    ],
                    [
                        'label' => 'Latest VMS Event',
                        'value' => function ($model) {
                            $report = $model->latestBluetrakerReport;

                            if ($report === null) {
                                return '-';
                            }

                            return Constant::$BlueTrakerEventTypes[$report->Event]
                                ?? 'Unknown Event';
                        },
                    ],

                    // [
                    //     'attribute' => 'compulsory_service',
                    //     'contentOptions' => function ($model) {
                    //         if ($model->compulsory_service == 1) {
                    //             return ['style' => 'background-color:#55c355;color:white']; // yellow
                    //         }
                    //         if ($model->compulsory_service == 0  ) {
                    //             return ['style' => 'background-color:#ff7474;color:white']; // yellow
                    //         }
                    
                    //         return [];
                    //     },
                    //    'value' => function ($model) {
                    //         return $model->compulsory_service == 1
                    //             ? 'Service Done'
                    //             : 'Compulsory Service Pending';
                    //     },

                    // ],

                    [
//                        'attribute' => 'latest_highseas_expire',
                        'label' => 'Expire date boat registration',
//                        'format'    => 'date',   // still works — null dates become empty by default
                        'value' => function ($model) {
                            return $model->latestBoatRegExpire ?: null;
                            // or more explicit:
                            // return $model->latest_highseas_expire ? Yii::$app->formatter->asDate($model->latest_highseas_expire) : 'NA';
                        },
                        'contentOptions' => function ($model) {

                            $date = $model->latestBoatRegExpire;

                            if (!$date) {
                                return ['class' => 'text-muted']; // no date
                            }

                            $time = strtotime($date);
                            $today = time();
                            $daysLeft = floor(($time - $today) / 86400);

                            if ($time < $today) {
                                return ['class' => 'text-danger']; // expired
                            } elseif ($daysLeft <= 30) {
                                return ['class' => 'text-danger']; // within 30 days
                            } else {
                                return ['class' => 'text-success']; // safe
                            }
                        },
                    ],
                    [
//                        'attribute' => 'latest_highseas_expire',
                        'label' => 'Expire date EEZ licence',
//                        'format'    => 'date',   // still works — null dates become empty by default
                        'value' => function ($model) {
                            return $model->latestNationalExpire ?: null;
                            // or more explicit:
                            // return $model->latest_highseas_expire ? Yii::$app->formatter->asDate($model->latest_highseas_expire) : 'NA';
                        },
                        'contentOptions' => function ($model) {

                            $date = $model->latestNationalExpire;

                            if (!$date) {
                                return ['class' => 'text-muted']; // no date
                            }

                            $time = strtotime($date);
                            $today = time();
                            $daysLeft = floor(($time - $today) / 86400);

                            if ($time < $today) {
                                return ['class' => 'text-danger']; // expired
                            } elseif ($daysLeft <= 30) {
                                return ['class' => 'text-danger']; // within 30 days
                            } else {
                                return ['class' => 'text-success']; // safe
                            }
                        },
                    ],
                    [
//                        'attribute' => 'latest_highseas_expire',
                        'label' => 'Expire date high seas licence',
//                        'format'    => 'date',   // still works — null dates become empty by default
                        'value' => function ($model) {
                            return $model->latestHighseasExpire ?: null;
                            // or more explicit:
                            // return $model->latest_highseas_expire ? Yii::$app->formatter->asDate($model->latest_highseas_expire) : 'NA';
                        },
                        'contentOptions' => function ($model) {

                            $date = $model->latestHighseasExpire;

                            if (!$date) {
                                return ['class' => 'text-muted']; // no date
                            }

                            $time = strtotime($date);
                            $today = time();
                            $daysLeft = floor(($time - $today) / 86400);

                            if ($time < $today) {
                                return ['class' => 'text-danger']; // expired
                            } elseif ($daysLeft <= 30) {
                                return ['class' => 'text-danger']; // within 30 days
                            } else {
                                return ['class' => 'text-success']; // safe
                            }
                        },
                    ],
                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => static function (DepartureBoats $model): string {
                            $token = SecurityHelper::encryptId(
                                DepartureBoats::class,
                                (int) $model->id
                            );

                            return Html::a(
                                Yii::t('app', 'View'),
                                [
                                    '/departure-boat/view',
                                    'token' => $token,
                                ],
                                [
                                    'class' => 'btn btn-sm btn-primary',
                                ]
                            );
                        },
                    ],
                ],
            ]); ?>


        </div>
    </div>
</div>