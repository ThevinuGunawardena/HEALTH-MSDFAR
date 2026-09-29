<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\models\DepartureRequests;
use backend\services\CommonService;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\DepartureRequestsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Departure Requests');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <!--            <h1>--><?php //= Html::encode($this->title) ?><!--</h1>-->

            <p>
                <?= CommonService::validateEditPermissionBoolean() ? Html::a(Yii::t('app', 'Create Departure Requests')
                    , ['create'], ['class' => 'btn btn-success']) : "" ?>
                <?= CommonService::validateEditPermissionBoolean() ? Html::a(Yii::t('app', 'Manual Departure Requests')
                        , ['manual-departure/create'], ['class' => 'btn btn-success']) : "" ?>
                <?= Html::a(Yii::t('app', 'My harbour'), ['index'], ['class' => 'btn 
                btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'All Island'), ['index', 'allIsland' => 1], ['class' => 'btn 
                btn-primary']) ?>

            </p>

            <!--            --><?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],

                'columns' => [
//                    ['class' => 'yii\grid\SerialColumn'],
                    'id',

                    [
                        'attribute' => 'boat_no',
                        'format' => 'text',
                        'value' => function ($model) {
                            return strtoupper($model->boat_no);
                        }
                    ],
                    [
                        'attribute' => 'fishing_area',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$fishingArea[$model->fishing_area] ?? $model->fishing_area;
                        }
                    ],
                    //                    'boat_name',
                    'owner',
                    'contact_no',
//                    'email:email',
                    //'skipper',
                    //'skipper_no',
                    'skipper_nic',
                    //'district',
                    'harbor',
                    //'fishing_area',
                    //'length_longline',
                    //'length_gillnet',
                    //'length_ringnet',
                    //'longline_hooks',
                    //'mesh_gillnet',
                    //'mesh_ringnet',
                    //'vms',
                    //'agree',
//                    'req_date_time',
                    //'user',
//                    'action_date',
                    [
                        'attribute' => 'action_date',
                        'filter' => Html::input(
                            'date',
                            'DepartureRequestsSearch[action_date]',
                            $searchModel->action_date,
                            ['class' => 'form-control']
                        ),
//                        'format' => ['datetime', 'php:Y-m-d H:i'],
                        'value' => function ($model) {
                            return $model->action_date ? date('Y-m-d H:i', strtotime($model->action_date)) : null;
                        },
                    ],
                    [
                        'attribute' => 'approve',
                        'filter' => ["A" => "Approve", "P" => "Pending", "R" =>
                            "Reject"],
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->approve == "A" ? "Approved" :
                                ($model->approve == "P" ? "Pending" :
                                    ($model->approve == "R" ? "Rejected" : "NA"));
                        }
                    ],                    //'remarks',
                    //'water_bot',
                    //'mcs',
                    //'frequency',
                    //'vms_code',
                    //'manual',
                    //'arrivalPort',
                    //'arrivalDate',
                    //'arrTime',
                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => static function (DepartureRequests $model): string {
                            $token = SecurityHelper::encryptId(
                                DepartureRequests::class,
                                (int) $model->id
                            );

                            return Html::a(
                                Yii::t('app', 'View'),
                                [
                                    '/departure/view',
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