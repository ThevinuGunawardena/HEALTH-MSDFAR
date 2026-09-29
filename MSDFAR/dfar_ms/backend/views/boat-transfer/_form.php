<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberTransferRequest $model */
/** @var yii\widgets\ActiveForm $form */

$url = Url::to(['boat-numbers/boat-search']);

?>

<div class="boat-number-transfer-request-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'userform'
    ]]); ?>
    <div class="row">
        <div class="col-xl-12">
            <?= $form->field($model, 'boat_number')->widget(Select2::classname(), [
                'data' => $boatData,

                'options' => ['placeholder' => 'Search for a boat number ...'],
                'pluginOptions' => [
//        'allowClear' => true,
                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => $url,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(boat_number) { return boat_number.text; }'),
                    'templateSelection' => new JsExpression('function (boat_number) { return boat_number.text; }'),
                ],
            ]); ?>
        </div>
        <div class="col-xl-12">
            <div class="row currentOwner" style="display: none">
                <div class="col-lg-3">
                    <div class="image-card ownerImage">

                    </div>
                </div>
                <div class="col-lg-9">
                    <UL>
                        <li>Owner Name: <span class="ownerName"></span></li>
                        <li>Owner NIC: <span class="ownerNIC"></span></li>
                        <li>Owner District: <span class="ownerDistrict"></span></li>
                        <li>Owner Division: <span class="ownerDivision"></span></li>
                    </UL>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'new_owner')->widget(Select2::classname(), [
                'data' => $ownerData,

                'options' => ['placeholder' => 'Search ...'],
                'pluginOptions' => [
//        'allowClear' => true,
                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => "../fisherman/search-global",
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(boat_number) { return boat_number.text; }'),
                    'templateSelection' => new JsExpression('function (boat_number) { return boat_number.text; }'),
                ],
            ]); ?>

        </div>
    </div>


    <hr>
    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'witness_name')->textInput(['maxlength' => true]) ?>

        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'witness_nic')->textInput(['maxlength' => true]) ?>

        </div>
        <div class="col-lg-12">
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'witness_sign_date')->textInput(["type" => "date"]) ?>

        </div>
    </div>


    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'witness_address')->textarea(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'remark')->textarea(['maxlength' => true]) ?>

        </div>

    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Apply'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
