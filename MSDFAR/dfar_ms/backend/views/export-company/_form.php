<?php

use backend\config\Constant;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ExportCompany $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="export-company-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //= $form->field($model, 'district')->textInput() ?>

    <?= $form->field($model, 'br')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'appication_types')->checkboxList(Constant::$exportLicenseTypes) ?>

    <?= $form->field($model, 'status')->dropDownList(Constant::$actDeactInt) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
