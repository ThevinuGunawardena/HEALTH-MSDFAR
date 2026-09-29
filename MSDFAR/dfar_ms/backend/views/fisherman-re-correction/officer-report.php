<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Officers by Ignore Count';
?>
<div class="officer-report-index">



    <div class="panel panel-primary">
        <div class="panel-heading">
            <i class="fa fa-trophy"></i> Officers Ranked by Total Ignores (Most to Least)
        </div>
        <div class="panel-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover'],
                'emptyText' => 'No records found.',
                'columns' => [
                    [
                        'label' => 'Rank',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index) use ($dataProvider) {
                                        $rank = $dataProvider->pagination->offset + $index + 1;
                                        $class = 'default';
                                        if ($rank === 1) {
                                            $class = 'warning'; // gold-ish
                                        } elseif ($rank === 2) {
                                            $class = 'info';
                                        } elseif ($rank === 3) {
                                            $class = 'success';
                                        }
                                        return Html::tag('span', '#' . $rank, ['class' => "label label-{$class}"]);
                                    },
                    ],
                    [
                        'label' => 'Name',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        $name = trim($model['first_name'] . ' ' . $model['last_name']);
                                        return Html::tag('strong', Html::encode($name));
                                    },
                    ],
                    [
                        'attribute' => 'mobile_phone',
                        'label' => 'Phone Number',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        if (empty($model['mobile_phone'])) {
                                            return '<span class="text-muted">-</span>';
                                        }
                                        return Html::encode($model['mobile_phone']);
                                    },
                    ],
                    [
                        'attribute' => 'total_ignores',
                        'label' => 'Total Ignores',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        return Html::tag('span', Html::encode($model['total_ignores']), ['class' => 'badge']);
                                    },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                            return Html::a('<i class="fa fa-eye"></i> View', ['/fisherman-re-correction/view', 'mainId' => $model['main_id']], [
                                                'class' => 'btn btn-sm btn-primary',
                                            ]);
                                        },
                        ],
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>