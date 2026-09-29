<?php

use backend\config\Constant;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\SkipperSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Scientific Craft Report');
$this->params['breadcrumbs'][] = $this->title;


$exportMenu =
    [

        'scientifi_data_id', 'sampling_id', 'boat_number',
        'fishey_type', 'sub_category', 'engine_hp', 'departure_date', 'departure_time', 'depature_port_name', 'weather', 'arrival_date', 'remark',
        'crew_members_count', 'unloading_type',

        [
            'attribute' => 'gear_setting_time',
            'value' => function ($model) {
                return Constant::$gearSettingTime[$model->gear_setting_time] ?? "";
            }
        ],

        'days', 'hours',
        'fuel_qty', 'fuel_price', 'ice_qty', 'ice_price', 'bait_qty', 'bait_price', 'labour_cost', 'food_water', 'other', 'remark'


    ];
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <!--    --><?php //Pjax::begin(); ?>
            <?php echo $this->render('_search', ['from' => $from,
                'to' => $to,
                'action' => "scientific-sampling-craft",
                'district' => $district]); ?>
            <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $exportMenu,
                'batchSize' => 500,
                'target' => '_blank',
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
                'columns' => $exportMenu
            ]); ?>
        </div>
    </div>
</div>
