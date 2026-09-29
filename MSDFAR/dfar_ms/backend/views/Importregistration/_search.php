<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ImportregistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="importregistration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'applicant_name') ?>

    <?= $form->field($model, 'address') ?>

    <?= $form->field($model, 'mobile_number') ?>

    <?= $form->field($model, 'fixed_number') ?>

    <?= $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'business_reg_no') ?>

    <?php // echo $form->field($model, 'permit_type') ?>

    <?php // echo $form->field($model, 'commercial_name') ?>

    <?php // echo $form->field($model, 'total_weight') ?>

    <?php // echo $form->field($model, 'imported_country') ?>

    <?php // echo $form->field($model, 'charge') ?>

    <?php // echo $form->field($model, 'information') ?>

    <?php // echo $form->field($model, 'document') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
