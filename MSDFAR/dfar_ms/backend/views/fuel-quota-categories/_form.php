<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\FuelQuotaCategories $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="fuel-quota-categories-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'category')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fuel_quota')->textInput() ?>

    <?= $form->field($model, 'time_frame')->textInput() ?>

    <?= $form->field($model, 'boat_type')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
