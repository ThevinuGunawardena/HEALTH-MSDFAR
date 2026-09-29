<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoatLicense $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="fisherman-registerd-boat-license-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'boat_number_id')->textInput() ?>

    <?= $form->field($model, 'fisherman_id')->textInput() ?>

    <?= $form->field($model, 'insurance_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'call_sign_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'district')->textInput() ?>

    <?= $form->field($model, 'division')->textInput() ?>

    <?= $form->field($model, 'landing_site')->textInput() ?>

    <?= $form->field($model, 'witness_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'witness_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'witness_nic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'witness_singing_date')->textInput() ?>

    <?= $form->field($model, 'how_propelled')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'engine_make')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fuel_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'engine_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'engine_horsepower')->textInput() ?>

    <?= $form->field($model, 'engine_serial_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'communication_equipment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fishing_equipment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'navigation_equipment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date_of_construction')->textInput() ?>

    <?= $form->field($model, 'date_of_first_registration')->textInput() ?>

    <?= $form->field($model, 'mea_report')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'approval_stage')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created')->textInput() ?>

    <?= $form->field($model, 'approved_time')->textInput() ?>

    <?= $form->field($model, 'expire_date')->textInput() ?>

    <?= $form->field($model, 'renew')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
