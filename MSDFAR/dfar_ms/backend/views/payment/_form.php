<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\PaymentLog $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="payment-log-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'userform'
    ]]); ?>


    <?= $form->field($model, 'amount')->textInput(["readOnly" => $model->amount != ""]) ?>


    <?= $form->field($model, 'ref')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'file')->fileInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
