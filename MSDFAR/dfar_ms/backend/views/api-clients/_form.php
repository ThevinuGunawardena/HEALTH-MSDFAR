<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ApiClients $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="api-clients-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_code')->textInput([
        'maxlength' => true,
    ]) ?>

    <?= $form->field($model, 'client_name')->textInput([
        'maxlength' => true,
    ]) ?>

    <?= $form->field($model, 'contact_email')->input('email', [
        'maxlength' => true,
    ]) ?>

    <?= $form->field($model, 'status')->dropDownList([
        1 => 'Active',
        0 => 'Inactive',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('app', 'Save'),
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>