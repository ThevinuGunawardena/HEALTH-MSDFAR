<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\controllers\BoatNumbersController;
use backend\models\BoatNumbers;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumbersSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Boat Numbers');
$this->params['breadcrumbs'][] = $this->title;

$exportColumns = [
    [
        'attribute' => 'boat_number',
        'format' => 'text',
    ],
    [
        'attribute' => 'boat_design',
        'format' => 'text',
        'value' => static function (BoatNumbers $model): string {
            return (string) (
                $model->boatDesign?->design_notation
                ?? ''
            );
        },
    ],
    [
        'attribute' => 'owner',
        'format' => 'text',
        'label' => Yii::t('app', 'Owner'),
        'value' => static function (BoatNumbers $model): string {
            $owner = $model->owner0;

            if ($owner === null) {
                return '';
            }

            $name = trim(
                (string) ($owner->first_name ?? '')
                . ' '
                . (string) ($owner->last_name ?? '')
            );

            $districtCode = (string) (
                $owner->district0?->code
                ?? ''
            );

            $result = (string) ($owner->nic ?? '');

            if ($name !== '') {
                $result .= ' (' . $name . ')';
            }

            if ($districtCode !== '') {
                $result .= ' [' . $districtCode . ']';
            }

            return $result;
        },
    ],
    [
        'attribute' => 'boat_type',
        'format' => 'text',
        'label' => Yii::t('app', 'Boat Category'),
        'value' => static function (BoatNumbers $model): string {
            return (string) ($model->boatType?->code ?? '');
        },
    ],
    [
        'attribute' => 'fisheries_district',
        'format' => 'text',
        'label' => Yii::t('app', 'Fisheries District'),
        'value' => static function (BoatNumbers $model): string {
            return (string) (
                $model->fisheriesDistrict?->name
                ?? ''
            );
        },
    ],
    [
        'attribute' => 'status',
        'format' => 'text',
        'value' => static function (BoatNumbers $model): string {
            return (string) (
                Constant::$licenseStatus[$model->status]
                ?? Yii::t('app', 'Unknown Status')
            );
        },
    ],
];
?>

<?= $this->render(
    '../common/stac',
    [
        'counts' => BoatNumbersController::getStacs(),
    ]
) ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <?= Html::a(
                    Yii::t('app', 'Create Boat Numbers'),
                    ['create'],
                    [
                        'class' => 'btn btn-success',
                    ]
                ) ?>
            </div>

            <?= $this->render(
                '_search',
                [
                    'model' => $searchModel,
                ]
            ) ?>

            <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $exportColumns,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
                'dropdownOptions' => [
                    'label' => Yii::t('app', 'Export All'),
                    'class' =>
                        'btn btn-outline-secondary btn-default',
                ],
            ]) ?>

            <?= GridView::widget([
                'pager' => [
                    'class' => LinkPager::class,
                    'firstPageLabel' => Yii::t('app', 'First'),
                    'lastPageLabel' => Yii::t('app', 'Last'),
                ],
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'boat_number',
                        'format' => 'text',
                    ],
                    [
                        'attribute' => 'boat_length',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            return (string) (
                                $model->boatDesign?->length
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'owner',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Owner'),
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            $owner = $model->owner0;

                            if ($owner === null) {
                                return '';
                            }

                            return trim(
                                (string) ($owner->nic ?? '')
                                . ' ('
                                . trim(
                                    (string) ($owner->first_name ?? '')
                                    . ' '
                                    . (string) ($owner->last_name ?? '')
                                )
                                . ')'
                            );
                        },
                    ],
                    [
                        'attribute' => 'boat_type',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Boat Category'),
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            return (string) (
                                $model->boatType?->code
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'fisheries_district',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Fisheries District'
                        ),
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            return (string) (
                                $model->fisheriesDistrict?->name
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            return (string) (
                                Constant::$licenseStatus[
                                    $model->status
                                ]
                                ?? Yii::t(
                                    'app',
                                    'Unknown Status'
                                )
                            );
                        },
                    ],
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumbers $model
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
                        'label' => Yii::t('app', 'Action'),
                        'format' => 'raw',
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            $token = SecurityHelper::encryptId(
                                BoatNumbers::class,
                                $model->id
                            );

                            return Html::a(
                                Yii::t('app', 'View'),
                                [
                                    '/boat-numbers/view',
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
