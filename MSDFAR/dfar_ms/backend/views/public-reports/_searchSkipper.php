<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\DepartureSkipperSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="departure-skipper-search">

    <?php $form = ActiveForm::begin([
        'action' => ['skipper-status'],
        'method' => 'get',
    ]); ?>

    <!--    --><?php //= $form->field($model, 'id') ?>
    <div class="row">
        <div class="col-xl-4">
            <?= $form->field($model, 'skipper_name') ?>
        </div>

        <div class="col-xl-4">
            <?= $form->field($model, 'nic') ?>
        </div>
        <div class="col-xl-4">
            <?= $form->field($model, 'skipper_id') ?>
        </div>


        <?php // echo $form->field($model, 'approved_by') ?>

        <?php // echo $form->field($model, 'timestamp') ?>

        <?php // echo $form->field($model, 'harbor') ?>

        <?php // echo $form->field($model, 'served_vessel') ?>

        <?php // echo $form->field($model, 'dep_date') ?>

        <?php // echo $form->field($model, 'dep_id') ?>

        <?php // echo $form->field($model, 'dep_cancel_allow_by') ?>

        <?php // echo $form->field($model, 'dep_cancel_date') ?>

        <?php // echo $form->field($model, 'to_date') ?>

        <?php // echo $form->field($model, 'remarks') ?>

        <?php // echo $form->field($model, 'offence_reason') ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['skipper-status'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
