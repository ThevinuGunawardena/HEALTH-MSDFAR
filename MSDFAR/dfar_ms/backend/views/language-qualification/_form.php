<?php

use backend\services\CommonService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\LanguageQualification $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="language-qualification-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'profile_officer_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'language')->dropDownList(['Sinhala' => 'Sinhala', 'Tamil' => 'Tamil', 'English' => 'English'],
        ['prompt' => 'Select Language']) ?>

    <?= $form->field($model, 'type')->dropDownList(['Oral' => 'Oral', 'Written' => 'Written'], ['prompt' => 'Select Type']) ?>

    <?= $form->field($model, 'year')->dropDownList(CommonService::getYearsArray(), ['prompt' => 'Select Year']) ?>

    <?= $form->field($model, 'results_certificate')->fileInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
