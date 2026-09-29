<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var string $officerName */
/** @var int $mainId */
/** @var yii\data\ArrayDataProvider $ignoreProvider */
/** @var yii\data\ArrayDataProvider $fishermanProvider */
/** @var yii\data\ArrayDataProvider $skipperProvider */

$this->title = 'Officer Activity - ' . $officerName;
?>
<div class="officer-view">

    <div class="d-flex justify-content-between align-items-center mb-3">
        
        <?= Html::a('<i class="fa fa-arrow-left"></i> Back to Report', ['officer-report'], [
            'class' => 'btn btn-primary ml-5',
        ]) ?>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <i class="fa fa-ban"></i> Ignores by Month / Division / District
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $ignoreProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover'],
                'emptyText' => 'No ignore records found.',
                'columns' => [
                    ['attribute' => 'month', 'label' => 'Month'],
                    ['attribute' => 'division', 'label' => 'Division'],
                    ['attribute' => 'district', 'label' => 'District'],
                    [
                        'attribute' => 'ignore_type',
                        'label' => 'Type',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $class = $model['ignore_type'] === 'Skipper' ? 'label-warning' : 'label-info';
                            return Html::tag('span', Html::encode($model['ignore_type']), ['class' => "label {$class}"]);
                        },
                    ],
                    [
                        'attribute' => 'total',
                        'label' => 'Total Ignores',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::tag('span', Html::encode($model['total']), ['class' => 'badge']);
                        },
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div class="panel panel-success">
        <div class="panel-heading">
            <i class="fa fa-id-card"></i> Fisherman Re-Corrections by Month / Division / District
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $fishermanProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover'],
                'emptyText' => 'No fisherman re-correction records found.',
                'columns' => [
                    ['attribute' => 'month', 'label' => 'Month'],
                    ['attribute' => 'division', 'label' => 'Division'],
                    ['attribute' => 'district', 'label' => 'District'],
                    [
                        'attribute' => 'total',
                        'label' => 'Total Corrections',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::tag('span', Html::encode($model['total']), ['class' => 'badge']);
                        },
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div class="panel panel-warning">
        <div class="panel-heading">
            <i class="fa fa-anchor"></i> Skipper Re-Corrections by Month / Division / District
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $skipperProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover'],
                'emptyText' => 'No skipper re-correction records found.',
                'columns' => [
                    ['attribute' => 'month', 'label' => 'Month'],
                    ['attribute' => 'division', 'label' => 'Division'],
                    ['attribute' => 'district', 'label' => 'District'],
                    [
                        'attribute' => 'total',
                        'label' => 'Total Corrections',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::tag('span', Html::encode($model['total']), ['class' => 'badge']);
                        },
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>