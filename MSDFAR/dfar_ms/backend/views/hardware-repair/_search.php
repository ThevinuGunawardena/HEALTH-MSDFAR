<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\HardwareRepairSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="hardware-repair-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'Name') ?>

    <?= $form->field($model, 'Phone_number') ?>

    <?= $form->field($model, 'Email') ?>

    <?= $form->field($model, 'Office') ?>

    <?php // echo $form->field($model, 'Serial_number') ?>

    <?php // echo $form->field($model, 'Brand_name') ?>

    <?php // echo $form->field($model, 'Issue') ?>

    <?php // echo $form->field($model, 'Received_date') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <?php // echo $form->field($model, 'Remarks') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
