<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataFishcatch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="catch-data-fishcatch-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fish_type')->textInput() ?>

    <?= $form->field($model, 'num_of_fish_log')->textInput() ?>

    <?= $form->field($model, 'weight_of_fish_log')->textInput() ?>

    <?= $form->field($model, 'num_of_fish_act')->textInput() ?>

    <?= $form->field($model, 'weight_of_fish_act')->textInput() ?>

    <?= $form->field($model, 'catchdata_req_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
