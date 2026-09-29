<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\RenewBoatRegistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="renew-boat-registration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?php // $form->field($model, 'id') ?>

    <?php // $form->field($model, 'reg_id') ?>

    <?php // $form->field($model, 'notes') ?>

    <?php // $form->field($model, 'status') ?>

    <?php // $form->field($model, 'approval_statge') ?>
    <div class="row">
        <div class="col-xl-6">
            <?= $form->field($model, 'reg_id') ?>
        </div>
        <div class="col-xl-6">
        </div>
    </div>
    <div class="row">

        <div class="col-xl-6">
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
