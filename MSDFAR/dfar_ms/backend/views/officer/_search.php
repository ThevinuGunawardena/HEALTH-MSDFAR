<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProfileOfficerSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="profile-officer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?php // $form->field($model, 'id') ?>

    <?php // $form->field($model, 'first_name') ?>

    <?php // $form->field($model, 'last_name') ?>

    <?php // $form->field($model, 'district') ?>

    <?php // $form->field($model, 'status') ?>

    <div class="row">
        <div class="col-xl-6">
            <?= $form->field($model, 'first_name') ?>
        </div>
        <div class="col-xl-6">
        <?= $form->field($model, 'last_name') ?>
        </div>
    </div>
    <div class="row">

        <div class="col-xl-6">
            <?= $form->field($model, 'district') ?>
        </div>
        <div class="col-xl-6">
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
