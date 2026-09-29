<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProfileYard $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="profile-yard-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'userform'
    ]]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'owner')->dropDownList($fishermanList, ["prompt" => "Select"]) ?>

    <?= $form->field($model, 'address')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'mobile_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'land_line')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'web')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fax')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'business_reg_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'business_reg_date')->textInput(["type"=>"date"]) ?>

    <?= $form->field($model, 'land_owner')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'deed_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ownership_get_date')->textInput(["type"=>"date"]) ?>

    <?= $form->field($model, 'land_area')->textInput() ?>

    <?= $form->field($model, 'land_area_under_roof')->textInput() ?>

    <?= $form->field($model, 'remark')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'admin_district')->dropDownList($districtList,['prompt'=>"Please Choose..."]) ?>

    <?= $form->field($model, 'fisheries_district')->dropDownList($districtList,['prompt'=>"Please Choose...","onchange"=>'loadDivisionsAjaxYard($(this).val(),'.$model->division.')']) ?>

    <?= $form->field($model, 'division')->dropDownList([]) ?>

    <?= $form->field($model, 'gps_latitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gps_longitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'transpotation_method')->textInput() ?>

    <?= $form->field($model, 'distance_rural_hospital')->textInput() ?>

    <?= $form->field($model, 'distance_district_hospital')->textInput() ?>

    <?= $form->field($model, 'distance_base_hospital')->textInput() ?>

    <?= $form->field($model, 'distance_teaching_hospital')->textInput() ?>

    <?= $form->field($model, 'distance_genaral_hospital')->textInput() ?>

    <?= $form->field($model, 'distance_fire_brigade')->textInput() ?>

    <?= $form->field($model, 'distance_police_station')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
