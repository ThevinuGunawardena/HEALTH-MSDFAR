<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\controllers\SkipperController;
use backend\models\Skipper;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\SkipperSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Skippers List');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render(
    '../common/stac',
    ['counts' => SkipperController::getStacs()]
) ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <div class="col-lg-4 offset-lg-8 mb-3">
                <?= Html::a(
                    Yii::t('app', 'Add new Skipper'),
                    ['/skipper/create'],
                    ['class' => 'btn btn-success btn-block']
                ) ?>
            </div>

            <?= $this->render(
                '_search',
                ['model' => $searchModel]
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
                        'attribute' => 'id',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Reference'),
                        'value' => static function (Skipper $model): string {
                            return 'SKP-' . sprintf('%05d', (int) $model->id);
                        },
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Fisherman ID'),
                        'value' => static function (Skipper $model): string {
                            return (string) ($model->fisherman->fisherman_uid ?? '');
                        },
                    ],
                    [
                        'attribute' => 'highest_education_qualification',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Highest Education Qualification'),
                        'value' => static function (Skipper $model): string {
                            return (string) (
                                $model->highestEducationQualification->description
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'other_qualifications',
                        'format' => 'text',
                    ],
                    [
                        'attribute' => 'fisheries_district',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Fisheries District'),
                        'value' => static function (Skipper $model): string {
                            return (string) ($model->fisheriesDistrict->name ?? '');
                        },
                    ],
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'value' => static function (Skipper $model): string {
                            return (string) (
                                Constant::$userTypes[$model->approval_stage]['name']
                                ?? $model->approval_stage
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => static function (Skipper $model): string {
                            return (string) (
                                Constant::$licenseStatus[$model->status]
                                ?? Yii::t('app', 'Unknown')
                            );
                        },
                    ],
                    [
                        'label' => Yii::t('app', 'Action'),
                        'format' => 'raw',
                        'value' => static function (Skipper $model): string {
                            return Html::a(
                                Yii::t('app', 'View'),
                                [
                                    '/skipper/view',
                                    'token' => SecurityHelper::encryptId(
                                        Skipper::class,
                                        $model->id
                                    ),
                                ],
                                ['class' => 'btn btn-sm btn-primary']
                            );
                        },
                    ],
                ],
            ]) ?>

        </div>
    </div>
</div>
