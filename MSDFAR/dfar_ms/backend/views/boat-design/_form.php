<?php

use backend\config\Constant;
use backend\services\CommonService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BoatDesign $model */
/** @var yii\widgets\ActiveForm $form */
?>


<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => [
                'class' => 'userform'
            ]]); ?>

            <?= $form->field($model, 'yard')->dropDownList($yardsList, ["prompt" => "Please Select"]) ?>

            <?= $form->field($model, 'boat_type')->dropDownList($boatCategories, ["prompt" => "Please Select"]) ?>

            <?= $form->field($model, 'hull_material')->dropDownList(Constant::$hullMaterials, ["prompt" => "Please Select"]) ?>

            <?= $form->field($model, 'engin_type')->dropDownList(Constant::$engineTypes, ["prompt" => "Please Select"]) ?>

            <?= $form->field($model, 'fi_district')->dropDownList(CommonService::getFIDistrictArray(), ["prompt" => "Please Select"]) ?>

            <?= $form->field($model, 'design_notation')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'length')->textInput() ?>

            <?= $form->field($model, 'width')->textInput() ?>

            <?= $form->field($model, 'height')->textInput() ?>

            <?= $form->field($model, 'draft')->textInput() ?>

            <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>


            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
