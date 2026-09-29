<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataFishcatchSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="catch-data-fishcatch-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fish_type') ?>

    <?= $form->field($model, 'num_of_fish_log') ?>

    <?= $form->field($model, 'weight_of_fish_log') ?>

    <?= $form->field($model, 'num_of_fish_act') ?>

    <?php // echo $form->field($model, 'weight_of_fish_act') ?>

    <?php // echo $form->field($model, 'catchdata_req_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
