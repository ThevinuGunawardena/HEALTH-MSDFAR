<?php

use backend\config\Constant;
use backend\controllers\BoatRegistrationRenewController;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

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
<?php echo $this->render('../common/stac', ["counts" => BoatRegistrationRenewController::getStacs()]); ?>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>

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
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<a href="../boat-registration/view?id=' . $model->nid . '" class="btn btn-sm btn-primary">View</a>';
                        }
                    ],
                ],
            ]); ?>

            <!--            --><?php //Pjax::end(); ?>
        </div>
    </div>
</div>
