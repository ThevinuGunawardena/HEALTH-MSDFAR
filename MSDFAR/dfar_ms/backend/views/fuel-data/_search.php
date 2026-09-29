<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\FishermanRegisterdBoat;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\ProfileFisherman;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;


/** @var yii\web\View $this */
/** @var backend\models\FuelDataSearch $model */
/** @var yii\widgets\ActiveForm $form */

$boatDat = FishermanRegisterdBoatLicense::find()->andWhere(['id' => $model->boat_registration_id])->one();
if (isset($boatDat)) {
    $boatDataS[$boatDat->id] = $boatDat->boatNumber->boat_number;

} else {
    $boatDataS = [];
}
?>

<div class="fuel-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

   <?php // echo $form->field($model, 'id') ?>


    <?php // echo $form->field($model, 'bank_code') ?>

    <?php // echo $form->field($model, 'bank_branch') ?>

   <?php // echo$form->field($model, 'account_number') ?>

    <?php // echo $form->field($model, 'fuel_quota_cat') ?>

    <?php // echo $form->field($model, 'renew_at') ?>

<div class="row">
    <div class="col-xl-6">
            <!--            --><?php //= $form->field($model, 'boat_registration_id')?>
             <?= $form->field($model, 'boat_registration_id')->widget(Select2::classname(), [
                'data' => $boatDataS,

                'options' => ['placeholder' => 'Search ...'],
                'pluginOptions' => [
                    'allowClear' => true,

                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => "../boat-registration/search-global",
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(boat_number) { return boat_number.text; }'),
                    'templateSelection' => new JsExpression('function (boat_number) { return boat_number.text; }'),
                ],
            ]); ?>
        </div>

        <div class="col-xl-6">
            <?= $form->field($model, 'status')->dropDownList(Constant::$licenseStatus, ["prompt" => "Select Status"]) ?>

        </div>
</div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
