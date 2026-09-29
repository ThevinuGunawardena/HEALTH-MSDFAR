<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Salary $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="salary-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'profile_officer_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'current_basic_salary')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'salary_increment_date')->textInput(["type" => 'date']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
