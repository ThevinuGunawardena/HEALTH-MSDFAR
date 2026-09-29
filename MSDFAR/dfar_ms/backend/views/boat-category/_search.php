<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\MBoatCategorySearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="mboat-category-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?php //  $form->field($model, 'id') ?>

    <?php //  $form->field($model, 'boat_type') ?>

    <?php // $form->field($model, 'code') ?>

    <?php // $form->field($model, 'status') ?>
    <div class="row">
        <div class="col-xl-6">
            <?= $form->field($model, 'boat_type') ?>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'code') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
