<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFishermanRenew $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="profile-fisherman-renew-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'fisherman_uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fisherman_id')->textInput() ?>

    <?= $form->field($model, 'district')->textInput() ?>

    <?= $form->field($model, 'division')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'approval_stage')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created')->textInput() ?>

    <?= $form->field($model, 'approved_time')->textInput() ?>

    <?= $form->field($model, 'expire_date')->textInput() ?>

    <?= $form->field($model, 'renew')->textInput() ?>

    <?= $form->field($model, 'printed')->textInput() ?>

    <?= $form->field($model, 'printed_date')->textInput() ?>

    <?= $form->field($model, 'privacy_policy')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
