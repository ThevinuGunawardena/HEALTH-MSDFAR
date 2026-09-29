<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
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
    <?php if (!UserTypeUtil::hasType(Constant::FISHERMAN) && UserTypeUtil::hasType(Constant::CALL_SIGN)) { ?>
        <!--    --><?php //= $form->field($model, 'boat_number_id')->textInput() ?>
        <div class="row">

            <div class="col-lg-6">
                <?= $form->field($model, 'call_sign_no')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'log_book_no')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'imo_no')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'ircs')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'iotc_record')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'mmsi_no_for_ais')->textInput(['maxlength' => true]) ?>
            </div>


        </div>

    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
