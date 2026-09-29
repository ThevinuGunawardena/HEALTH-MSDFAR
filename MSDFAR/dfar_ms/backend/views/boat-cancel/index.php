<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\models\BoatNumberCancelRequests;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberCancelRequestsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t(
    'app',
    'Boat Cancellation Requests'
);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="alert alert-info">
                <?= Html::encode(
                    Yii::t(
                        'app',
                        'Start a cancellation request from the applicable Boat Number record.'
                    )
                ) ?>
            </div>

            <?php Pjax::begin([
                'id' => 'boat-cancel-index-pjax',
                'timeout' => 10000,
            ]); ?>

            <?= $this->render(
                '_search',
                [
                    'model' => $searchModel,
                ]
            ) ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' =>
                            'yii\\grid\\SerialColumn',
                    ],
                    [
                        'attribute' => 'boat_number_id',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Boat Number'
                        ),
                        'value' => static function (
                            BoatNumberCancelRequests $model
                        ): string {
                            return (string) (
                                $model->boatNumber->boat_number
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'repairable',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumberCancelRequests $model
                        ): string {
                            return (string) (
                                Constant::$yesNo[
                                    $model->repairable
                                ]
                                ?? Yii::t('app', 'Unknown')
                            );
                        },
                    ],
                    [
                        'attribute' =>
                            'parts_available_for_inspection',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumberCancelRequests $model
                        ): string {
                            return (string) (
                                Constant::$yesNo[
                                    $model
                                        ->parts_available_for_inspection
                                ]
                                ?? Yii::t('app', 'Unknown')
                            );
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumberCancelRequests $model
                        ): string {
                            return (string) (
                                Constant::$licenseStatus[
                                    $model->status
                                ]
                                ?? Yii::t('app', 'Unknown')
                            );
                        },
                    ],
                    [
                        'label' => Yii::t(
                            'app',
                            'Action'
                        ),
                        'format' => 'raw',
                        'value' => static function (
                            BoatNumberCancelRequests $model
                        ): string {
                            $token = SecurityHelper::encryptId(
                                BoatNumberCancelRequests::class,
                                (int) $model->id
                            );

                            return Html::a(
                                Yii::t('app', 'View'),
                                [
                                    '/boat-cancel/view',
                                    'token' => $token,
                                ],
                                [
                                    'class' =>
                                        'btn btn-sm btn-primary',
                                ]
                            );
                        },
                    ],
                ],
            ]) ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
