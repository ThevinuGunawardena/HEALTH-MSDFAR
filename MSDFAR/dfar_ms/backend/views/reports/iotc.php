<?php

use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\SkipperSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'IOTC Report');
$this->params['breadcrumbs'][] = $this->title;


$exportMenu =
    [
        'first_name',
        'preferred_name_for_id',
        'nic',
        'permanent_address',
        'current_address',
        'boat_number',
        'hull_number',
        'main_gear_types',
        'sub_gear_types',
        'license_number',
        'call_sign_no',
        'date_of_construction',
        'engine_make',
        'engine_name',
        'approved_time',
        'from_date',
        'expire_date',
        'description',
        'length',
        'width',
        'height',
        'Harbour_Name',
        'yard_name'

    ];
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <!--    --><?php //Pjax::begin(); ?>
            <?php echo $this->render('_search', ['from' => $from,
                'to' => $to,
                'district' => "NA",
                "action" => "iotc"]); ?>
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
