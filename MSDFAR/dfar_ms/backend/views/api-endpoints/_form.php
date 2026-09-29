<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ApiEndpoints $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="api-endpoints-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'api_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'api_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'route')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'method')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>



    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
