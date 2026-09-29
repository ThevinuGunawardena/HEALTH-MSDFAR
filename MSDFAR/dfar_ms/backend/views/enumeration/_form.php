<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ScientificEnumerationRequest $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="scientific-enumeration-request-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'userform'
    ]]); ?>

    <?= $form->field($model, 'user')->textInput() ?>

    <?= $form->field($model, 'request_date')->textInput() ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'can_continue')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reson')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'district')->textInput() ?>

    <?= $form->field($model, 'division')->textInput() ?>

    <?= $form->field($model, 'landing_site')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
