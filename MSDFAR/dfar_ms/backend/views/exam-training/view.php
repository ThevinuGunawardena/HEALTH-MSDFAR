<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->exam_training_name;
$this->params['breadcrumbs'][] = ['label' => 'Profile Officers', 'url' => ['profile-officer/index']];
$this->params['breadcrumbs'][] = ['label' => 'Exam/Training', 'url' => ['index', 'profile_officer_id' => $model->profile_officer_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="exam-training-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Back', ['index', 'profile_officer_id' => $model->profile_officer_id], ['class' => 'btn btn-secondary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'exam_training_name',
            'exam_training_year',
            'exam_training_institute',
            [
                'attribute' => 'results_certificate',
                'format' => 'raw',
                'value' => $model->results_certificate ? Html::a('View', Yii::getAlias('@web') . $model->results_certificate) : 'N/A',
            ],
        ],
    ]) ?>

</div>