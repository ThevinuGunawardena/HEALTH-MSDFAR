<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\controllers\HighseasLicenseController;
use backend\models\Skipper;
use backend\services\CommonService;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\HighseasLicense $model */
/** @var yii\widgets\ActiveForm $form */

$fishermanList = $fishermanList ?? [];
$boatNumberList = $boatNumberList ?? [];
$errors = $errors ?? [];

$divisionGearData = HighseasLicenseController::getDivisionGears($model);

$ownerDat = Skipper::find()->andWhere(['id' => $model->skipper_id])->one();
//print_r($ownerDat);
//exit();
if (isset($ownerDat) && $ownerDat != null && $ownerDat->fisherman != null) {

    $ownerData[$ownerDat->id] = "ID-" . $ownerDat->fisherman->fisherman_uid . " - NIC: " . $ownerDat->fisherman->nic;

} else {
    $ownerData = [];
}
?>

<div class="highseas-license-form">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
        'options' => [
            'class' => 'userform',
        ],
    ]); ?>

    <?= $form->errorSummary($model, [
        'class' => 'alert alert-danger',
        'header' => '<strong>Please correct the following errors:</strong>',
    ]) ?>
    <?php if (!$model->renew && !UserTypeUtil::hasType(Constant::FISHERMAN)) { ?>
        <?php if (!UserTypeUtil::hasType(Constant::FISHERMAN) && !$update) { ?>
            <?= $form->field($model, 'fisherman_id')->dropDownList($fishermanList, ["prompt" => "Select", "disabled" => $model->renew]) ?>

            <?php Pjax::begin(['id' => 'boat_reg_no']); ?>

            <?= $form->field($model, 'boat_registration_id')->dropDownList($boatNumberList, ["prompt" => "Select", "disabled" => $model->renew]) ?>
            <?php Pjax::end(); ?>
        <?php } ?>
    <?php } ?>
    <?= $form->field($model, 'skipper_id')->widget(Select2::classname(), [
        'data' => $ownerData,

        'options' => ['placeholder' => 'Search for a Skipper ...'],
        'pluginOptions' => [
            'allowClear' => false,

            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                ],
            'ajax' => [
                'url' => "../skipper/search",
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(boat_number) { return boat_number.text; }'),
            'templateSelection' => new JsExpression('function (boat_number) { return boat_number.text; }'),
        ],
    ]); ?>
    <?php if (!UserTypeUtil::hasType(Constant::FISHERMAN)) { ?>
        <?= $form->field($model, 'prevouse_boat_flag')->dropDownList(Constant::$countries, ["prompt" => "Select"]) ?>
        <?= $form->field($model, 'landing_harbour')->dropDownList(CommonService::getHarboursArray(), ["prompt" => "Select"]) ?>
        <?= $form->field($model, 'unloading_sites')->checkboxList(CommonService::getLandingSitesArray($model->district), []) ?>

        <?= $form->field($model, 'no_if_crew_members')->textInput(["readOnly" => $model->renew]) ?>
    <?php } ?>
    <div class="row">
        <div class="col-lg-12">
            <h3 class="card-subtitle mb-2 text-muted">Gear Type(s)</h3>
        </div>
        <div class="col-lg-12">

            <table class="table table-responsive ">
                <thead class="table-dark">
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Division Gear Type</th>
                    <th scope="col">Sub Gear Type</th>
                    <th scope="col">Extra</th>
                    <th scope="col">Fishing Time Durations</th>
                    <th scope="col">Fish Species</th>
                </tr>
                </thead>
                <tbody>
                <?php


                if ($divisionGearData != null) foreach ($divisionGearData as $item) { ?>
                    <tr>
                        <th scope="row">
                            <!--                        <input type="checkbox" name="divisionGears[]"-->
                            <!--                                               value="-->
                            <?php //= $item['id'] ?><!--" --><?php //= $item['selected'] ?>
                            <!--                                               class="form-control">-->
                            <div class="custom-control custom-checkbox">
                                <?= Html::checkbox(
                                    'divisionGears[]',
                                    !empty($item['selected']),
                                    [
                                        'value' => (int) $item['id'],
                                        'uncheck' => null,
                                        'id' => 'division-gear-' . (int) $item['id'],
                                        'class' => 'custom-control-input',
                                    ]
                                ) ?>
                                <label class="custom-control-label" for="division-gear-<?= (int) $item['id'] ?>">
                                    <?= Html::encode($item['name'] ?? '') ?>
                                </label>
                            </div>
                        </th>
                        <th scope="row"><?= $item['subGear']; ?> </th>
                        <td><?= $item['extra'] ?>  </td>
                        <td><?= $item['times'] ?></td>
                        <td><?= $item['fishTypes'] ?></td>

                    </tr>
                <?php } ?>


                </tbody>
            </table>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Apply'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
