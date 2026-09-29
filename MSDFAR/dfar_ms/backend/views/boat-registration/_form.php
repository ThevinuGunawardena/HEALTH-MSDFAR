<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\services\CommonService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoat $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="fisherman-registerd-boat-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'userform'
    ]]); ?>
    <?= $form->field($model, 'fisherman_id')->hiddenInput()->label(false) ?>
    <?php if (!UserTypeUtil::hasType(Constant::FISHERMAN)) { ?>
        <!--    --><?php //= $form->field($model, 'boat_number_id')->textInput() ?>
        <div class="row">
            <div class="col-lg-12">
                <?= $form->field($model, 'insurance_no')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-12">
                <?= $form->field($model, 'call_sign_no')->textInput(['maxlength' => true]) ?>

            </div>
            <div class="col-lg-12">
                <?= $form->field($model, 'landing_site')->dropDownList(CommonService::getLandingSitesArray($model->district), ["prompt" => "Select"]) ?>

            </div>
            <div class="col-lg-6">    <?= $form->field($model, 'witness_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">    <?= $form->field($model, 'witness_nic')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-12">    <?= $form->field($model, 'witness_address')->textarea(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">    <?= $form->field($model, 'witness_singing_date')->textInput(['type' => 'date', "max" => date("Y-m-d"), 'onkeydown' => "return false"]) ?>
            </div>
            <div class="col-lg-6">    <?= $form->field($model, 'how_propelled')->dropDownList(Constant::$howPropelled, ["prompt" => "Select"]) ?>
            </div>
            <div class="col-lg-6">    <?= $form->field($model, 'engine_make')->dropDownList(Constant::$engineMake, ["prompt" => "Select"]) ?>
            </div>
            <div class="col-lg-6">    <?= $form->field($model, 'engine_type')->dropDownList(Constant::$engineType, ["prompt" => "Select"]) ?>
            </div>
            <div class="col-lg-6">    <?= $form->field($model, 'engine_horsepower')->textInput() ?>
            </div>
            <div class="col-lg-6">    <?= $form->field($model, 'fuel_type')->dropDownList(Constant::$fuelType, ["prompt" => "Select"]) ?>
            </div>
            <div class="col-lg-6">    <?= $form->field($model, 'engine_serial_number')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">       <?= $form->field($model, 'date_of_construction')->textInput(['type' => 'date', "max" => date("Y-m-d"), 'onkeydown' => "return false"]) ?>
            </div>
            <div class="col-lg-12">       <?= $form->field($model, 'date_of_first_registration')->textInput(['type' => 'date', "max" => date("Y-m-d"), 'onkeydown' => "return false"]) ?>
            </div>
            <div class="col-lg-12">       <?= $form->field($model, 'mea_report')->textInput() ?>
            </div>
            <div class="col-lg-12">    <?= $form->field($model, 'communication_equipment')->checkboxList(Constant::$communicationEquipment) ?>
            </div>
            <div class="col-lg-12">    <?= $form->field($model, 'fishing_equipment')->checkboxList(Constant::$fishingEquipment) ?>
            </div>
            <div class="col-lg-12">    <?= $form->field($model, 'navigation_equipment')->checkboxList(Constant::$navigationEquipment) ?>
            </div>
        </div>

    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
