<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use backend\models\BoatNumbers;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\MHarbours;
use backend\models\MMainGearTypes;
use backend\config\Constant;


$boatData = ArrayHelper::map(
    BoatNumbers::find()
        ->select(['boat_numbers.id', 'boat_numbers.boat_number'])
        ->innerJoinWith('fishermanRegisterdBoatLicenses')
        ->where(['fisherman_registerd_boat_license.status' => 101])
        ->asArray()
        ->all(),
    'id',
    'boat_number'
);
$harbours = ArrayHelper::map(MHarbours::find()->select(['id', 'name'])->asArray()->all(),'id','name');
$geartypes = ArrayHelper::map(MMainGearTypes::find()->select(['id', 'description'])->asArray()->all(),'id','description');




?>

<div class="catch-data-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card mb-5 shadow-sm">
     <div class="card-body">

    <div class="row">

        <div class="col-md-6">
            <?= $form->field($model, 'landing_date')
                ->textInput(["type" => "date", "max" => date("Y-m-d")]) ?>
        </div>

       <div class="col-md-6">
    <?= $form->field($model, 'boat_registration_id')->widget(Select2::classname(), [
        'data' => $boatData,
        'options' => ['placeholder' => 'Select boat number...'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>
</div>

        <div class="col-md-6">
            <?= $form->field($model, 'unloading_harbour')->dropDownList(
                $harbours,
                ['prompt' => 'Select Harbour']
            ) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'fishing_gear_type')->dropDownList(Constant::$exportGearTypes, [
                        'prompt' => 'Select Gear Type'
                    ])
                     ?>
        </div>
       
         <div class="col-md-6">
            <?= $form->field($model, 'log_book_no')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'log_book_page_no')->textInput(['maxlength' => true]) ?>
        </div>

        

       

        <div class="col-12">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
</div>
</div>