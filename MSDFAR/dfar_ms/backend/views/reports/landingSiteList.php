<?php

use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\SkipperSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Landing Site List');
$this->params['breadcrumbs'][] = $this->title;


$exportMenu =
    [
        'id',
        'division_id',
        'name',

    ];
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <!--    --><?php //Pjax::begin(); ?>

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
