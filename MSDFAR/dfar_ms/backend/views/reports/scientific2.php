<?php

use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\SkipperSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Scientific Catch Report');
$this->params['breadcrumbs'][] = $this->title;


$exportMenu =
    [
        [
            'attribute' => 'scientifi_data_id',
            "label" => "Scientific Data ID"
        ],
        [
            'attribute' => 'scientific_code',
            'format' => 'text',
            'label' => 'Scientific Code'
        ],
        'sampling_id',
        'boat_number',
        [
            'attribute' => 'name',
            "label" => "Specie name"
        ],
        [
            'attribute' => 'code',
            "label" => "Gear code"
        ],
        [
            'attribute' => 'description',
            "label" => "Gear description"
        ],

        'weight',
        [
            'attribute' => 'weight_code',
            'value' => function ($model) {
                return yii::$app->params['weightCode'][$model->weight_code] ?? "";
            }
        ],
        'trash',
        'export_qty', 'export_value', 'local_qty', 'local_value', 'dried_qty', 'dried_value', 'discard_qty', 'discard_value'


    ];
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <!--    --><?php //Pjax::begin(); ?>
            <?php echo $this->render('_search', ['from' => $from,
                'to' => $to,
                'action' => "scientific2",
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
