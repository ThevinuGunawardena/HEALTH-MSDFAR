<?php

use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\SkipperSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Scientific Report');
$this->params['breadcrumbs'][] = $this->title;


$exportMenu =
    [

        [
            'attribute' => 'scientific_id',
            'format' => 'text',
            'label' => 'Scientific Data ID'

        ],
        [
            'attribute' => 'scientific_code',
            'format' => 'text',
            'label' => 'Scientific Code',
            'value' => function ($model) {
                return $model->scientificData->landingSite->scientific_code ?? '';
            }
        ],
        [
            'attribute' => 'id',
            'format' => 'text',
            'label' => 'Sampling Data ID'

        ],
        [
            'attribute' => 'boat_number',
            'format' => 'text',

        ],


    ];
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <!--    --><?php //Pjax::begin(); ?>
            <?php echo $this->render('_search', ['from' => $from,
                'to' => $to,
                'action' => "scientific-sampling-data",
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
