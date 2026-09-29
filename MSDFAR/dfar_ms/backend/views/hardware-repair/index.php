<?php

use app\models\HardwareRepair;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\HardwareRepairSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Hardware Repairs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hardware-repair-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Hardware Repair', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'Name',
            'Email:email',
            'Office',
            'Brand_name',
            'Received_date',
            [
                'attribute' => 'Status',
                'filter' => [
                    'Open' => 'Open',
                    'In Progress' => 'In Progress',
                    'Completed' => 'Completed',
                ],
                'value' => function ($model) {
                        return $model->Status;
                    },
            ],
            [
                'class' => ActionColumn::className(),
                'header' => 'Actions',
                'template' => '{view} {update} {delete}', // Button templates
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                            return Html::a('<i class="fas fa-eye"></i>', $url, [
                                'class' => 'btn btn-info btn-sm',
                                'title' => 'View',
                                'data-pjax' => '0',
                            ]);
                        },
                    'update' => function ($url, $model, $key) {
                            return Html::a('<i class="fas fa-edit"></i>', $url, [
                                'class' => 'btn btn-primary btn-sm',
                                'title' => 'Edit',
                                'data-pjax' => '0',
                            ]);
                        },
                ],
            ],
        ],
    ]); ?>

</div>