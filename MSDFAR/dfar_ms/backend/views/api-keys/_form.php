<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ApiKeys $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="api-keys-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->dropDownList(
    \yii\helpers\ArrayHelper::map(
        \backend\models\ApiClients::find()->where(['status' => 1])->all(),
        'id',
        'client_code'
    ),
    ['prompt' => 'Select Client']
) ?>
    <?= $form->field($model, 'key_name')->textInput(['maxlength' => true]) ?>

    <!-- <?= $form->field($model, 'api_key_hash')->textInput(['maxlength' => true]) ?> -->

    <?= $form->field($model, 'status')->dropDownList([
    1 => 'Active',
    0 => 'Inactive',
]) ?>   
    <?= $form->field($model, 'expires_at')->textInput([
    'placeholder' => 'YYYY-MM-DD HH:MM:SS'
]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
