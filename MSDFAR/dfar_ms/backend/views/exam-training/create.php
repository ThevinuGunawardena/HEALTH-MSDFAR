<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Create Exam/Training';
$this->params['breadcrumbs'][] = ['label' => 'Profile Officers', 'url' => ['profile-officer/index']];
$this->params['breadcrumbs'][] = ['label' => 'Exam/Training', 'url' => ['index', 'profile_officer_id' => $model->profile_officer_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <div class="exam-training-form">

                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                <?= $form->field($model, 'exam_training_name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'exam_training_year')->textInput(['type' => 'number']) ?>

                <?= $form->field($model, 'exam_training_institute')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'results_certificate')->fileInput() ?>

                <div class="form-group">
                    <?= Html::submitButton('Create', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>

        </div>
    </div>
</div>