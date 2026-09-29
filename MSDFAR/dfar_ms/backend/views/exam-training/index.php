<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Exam/Training Details';
$this->params['breadcrumbs'][] = ['label' => 'Profile Officers', 'url' => ['profile-officer/index']];
$this->params['breadcrumbs'][] = ['label' => 'Exam/Training', 'url' => ['index', 'profile_officer_id' => $profile_officer_id]];
?>

<div class="exam-training-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Exam/Training', ['create', 'profile_officer_id' => $profile_officer_id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Back to Profile Officer', ['profile-officer/view', 'id' => $profile_officer_id], ['class' => 'btn btn-secondary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'exam_training_name',
            'exam_training_year',
            'exam_training_institute',
            [
                'attribute' => 'results_certificate',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->results_certificate ? Html::a('View', Yii::getAlias('@web') . $model->results_certificate) : 'N/A';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('View', ['view', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-secondary btn-sm']);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('Delete', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ],
        ],
    ]) ?>

</div>