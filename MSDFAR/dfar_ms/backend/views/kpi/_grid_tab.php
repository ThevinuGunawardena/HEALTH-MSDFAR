<?php

use yii\grid\GridView;
use yii\widgets\LinkPager;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\KpiSearch */
/* @var $gridColumns array */
?>

<div class="table-responsive pt-3">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => LinkPager::class, 
            'firstPageLabel' => 'First', 
            'lastPageLabel' => 'Last',
            'options' => ['class' => 'pagination justify-content-end pt-3']
        ],
        'columns' => $gridColumns,
        'tableOptions' => ['class' => 'table table-striped table-bordered layout-fixed'],
    ]); ?>
</div>