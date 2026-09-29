<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\controllers\SkipperRenewController;
use backend\models\SkipperRenew;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SkipperRenewSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Skipper Renewal List');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render(
    '../common/stac',
    [
        'counts' => SkipperRenewController::getStacs(),
    ]
) ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= $this->render(
                '_search',
                [
                    'model' => $searchModel,
                ]
            ) ?>

            <?= GridView::widget([
                'pager' => [
                    'class' => LinkPager::class,
                    'firstPageLabel' => Yii::t('app', 'First'),
                    'lastPageLabel' => Yii::t('app', 'Last'),
                ],
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'skipper_uid',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Skipper ID'),
                        'value' => static function (
                            SkipperRenew $model
                        ): string {
                            return (string) (
                                $model->skipper_uid
                                ?? Yii::t('app', 'Not Generated')
                            );
                        },
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Fisherman ID'),
                        'value' => static function (
                            SkipperRenew $model
                        ): string {
                            return (string) (
                                $model->fisherman->fisherman_uid
                                ?? Yii::t('app', 'N/A')
                            );
                        },
                    ],
                    [
                        'attribute' =>
                            'highest_education_qualification',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Highest Education Qualification'
                        ),
                        'value' => static function (
                            SkipperRenew $model
                        ): string {
                            return (string) (
                                $model
                                    ->highestEducationQualification
                                    ->description
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'other_qualifications',
                        'format' => 'ntext',
                    ],
                    [
                        'attribute' => 'fisheries_district',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Fisheries District'
                        ),
                        'value' => static function (
                            SkipperRenew $model
                        ): string {
                            return (string) (
                                $model->fisheriesDistrict->name
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Approval Stage'
                        ),
                        'value' => static function (
                            SkipperRenew $model
                        ): string {
                            return (string) (
                                Constant::$userTypes[
                                    $model->approval_stage
                                ]['name']
                                ?? $model->approval_stage
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Status'),
                        'value' => static function (
                            SkipperRenew $model
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
                        'label' => Yii::t('app', 'Action'),
                        'format' => 'raw',
                        'value' => static function (
                            SkipperRenew $model
                        ): string {
                            $token = SecurityHelper::encryptId(
                                SkipperRenew::class,
                                $model->id
                            );

                            return Html::a(
                                Yii::t('app', 'View'),
                                [
                                    '/skipper-renew/view',
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

        </div>
    </div>
</div>
