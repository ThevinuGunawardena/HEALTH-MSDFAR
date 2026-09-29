<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Promotion $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="promotion-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'profile_officer_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'promoted_as')->textInput(['maxlength' => true]) ?>



    <?= $form->field($model, 'promotion_date')->textInput(["type" => 'date']) ?>
    <?= $form->field($model, 'promotion_letter')->fileInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
