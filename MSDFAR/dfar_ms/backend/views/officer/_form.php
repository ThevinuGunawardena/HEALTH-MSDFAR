<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\services\CommonService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProfileOfficer $model */
/** @var yii\widgets\ActiveForm $form */

$webURL = Yii::getAlias('@web');

$script = <<< JS
    $("#profileofficer-district").change()
JS;
$this->registerJs($script);
?>

<div class="profile-officer-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'userform'
    ]]); ?>
    <?php if ((!UserTypeUtil::hasType(Constant::ADMIN))) { ?>
        <div class="row">
            <div class="col-xl-6">
                <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

            </div>
            <div class="col-xl-6">
                <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

            </div>
            <div class="col-xl-6">
                <?= $form->field($model, 'mobile_phone')->textInput(['maxlength' => true]) ?>

            </div>
            <div class="col-xl-6">
                <?= $form->field($model, 'personal_email')->textInput([
                    'maxlength'   => true,
                    'type'        => 'email',
                    'placeholder' => Yii::t('app', 'name@example.com'),
                ]) ?>

            </div>

        </div>
        <div class="row">
            <div class="col-xl-6">
                <?= !UserTypeUtil::hasType(Constant::DG) && !UserTypeUtil::hasType(Constant::DM) ? $form->field
                ($model, 'district')->dropDownList(CommonService::getFIDistrictArray(), ['prompt' => 'Select...', "onchange" => 'loadDivisionsAjaxOfficer($(this).val(),' . $model->division . ')']) : "" ?>

            </div>
            <div class="col-xl-6">
                <?= UserTypeUtil::hasType(Constant::FI) ? $form->field($model, 'division')->dropDownList([], ["prompt" => "Select"]) : "" ?>

            </div>
            <div class="col-xl-6">
                <?= UserTypeUtil::hasType(Constant::HARBOUR_OFFICER) ? $form->field($model, 'harbour')->dropDownList
                (Constant::$departureHarbours, ["prompt" => "Select"]) : "" ?>

            </div>

        </div>
        <hr>
        <div class="row">
            <div class="col-xl-6">
                <img style="max-width: 200px; max-height: 200px"
                     src="<?= Constant::$FILE_VIEW_PATH ?>officer/profile/<?= $model->profile_image ?>">
                <?= $form->field($model, 'profile_image')->fileInput() ?>
            </div>
            <?php

            if (!UserTypeUtil::hasType(Constant::FI)) {
                ?>
                <div class="col-xl-6">
                    <img style="max-width: 200px; max-height: 200px"
                         src="<?= Constant::$FILE_VIEW_PATH ?>officer/signature/<?= $model->signature ?>">
                    <?= $form->field($model, 'signature')->fileInput() ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="row">
            <div class="col-xl-6">
                <?= $form->field($model, 'agreement')->fileInput() ?>
            </div>
            <div class="col-xl-6">
                <?= $form->field($model, 'it_result_sheet')->fileInput() ?>
            </div>
            <div class="col-xl-6">
                <?= $form->field($model, 'cetificate')->fileInput() ?>
            </div>
        </div>
        <hr>
    <?php } else { ?>

        <div class="row">
            <div class="col-xl-6">
                <?= $form->field($model, 'device_serial')->textInput() ?>
            </div>
            <div class="col-xl-6">
                <?= $form->field($model, 'user_level')->dropDownList(Constant::$USER_RANKS, ["prompt" => "Select"]) ?>
            </div>

        </div>
    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>