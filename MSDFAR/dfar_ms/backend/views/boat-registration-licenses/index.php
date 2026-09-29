<?php

use backend\config\Constant;
use backend\controllers\BoatRegistrationLicensesController;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use backend\components\SecurityHelper;
use backend\models\FishermanRegisterdBoatLicense;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoatSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Boat Registration Licenses');
$this->params['breadcrumbs'][] = $this->title;

$exportMenu = [

    [
        'attribute' => 'boat_number_id',
        'format' => 'text',
        'value' => function ($model) {
            return $model->boatNumber->boat_number;
        }
    ],
    [
        'attribute' => 'fisherman_id',
        'format' => 'text',
        'value' => function ($model) {
            return $model->fisherman->fisherman_uid ?? "";
        }
    ],
    [
        'attribute' => 'status',
        'format' => 'text',
        'label' => 'Status',
        'value' => function ($model) {
            return Constant::$licenseStatus[$model->status];
        }
    ],
    'mea_report',
    'created',
    'approved_time',
    'expire_date'
];
?>
<?php echo $licenseList == true ? $this->render('../common/stac', ["counts" => BoatRegistrationLicensesController::getStacs()]) : ""; ?>
<?php echo $licenseList == true ? $this->render('_search', ['model' => $searchModel]) : ""; ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">


            <div class="col-lg-4 offset-lg-8">
            </div>


            <!--            --><?php //Pjax::begin(); ?>

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
//        'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'boat_number_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boatNumber->boat_number;
                        }
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisherman->fisherman_uid ?? "";
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'label' => 'Status',
                        'value' => function ($model) {
                            return Constant::$licenseStatus[$model->status];
                        }
                    ],
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
                        }
                    ],
                    [
                    'label' => Yii::t('app', 'Action'),
                    'format' => 'raw',
                    'value' => static function (
                        FishermanRegisterdBoatLicense $model
                    ): string {
                        return Html::a(
                            Yii::t('app', 'View'),
                            [
                                '/boat-registration/view',
                                'token' => SecurityHelper::encryptId(
                                    FishermanRegisterdBoatLicense::class,
                                    $model->nid
                                ),
                            ],
                            [
                                'class' => 'btn btn-sm btn-primary',
                            ]
                        );
                    },
                ],

                ],
            ]); ?>

            <!--            --><?php //Pjax::end(); ?>
        </div>
    </div>
</div>
