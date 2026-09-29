<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ExportregistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="exportregistration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'application_name') ?>

    <?= $form->field($model, 'address') ?>

    <?= $form->field($model, 'mobile') ?>

    <?= $form->field($model, 'fax') ?>

    <?= $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'reg_no') ?>

    <?php // echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'permit_type') ?>

    <?php // echo $form->field($model, 'commercial_name') ?>

    <?php // echo $form->field($model, 'quantity_unit') ?>

    <?php // echo $form->field($model, 'total_weight') ?>

    <?php // echo $form->field($model, 'total_number') ?>

    <?php // echo $form->field($model, 'area') ?>

    <?php // echo $form->field($model, 'charge') ?>

    <?php // echo $form->field($model, 'country_export') ?>

    <?php // echo $form->field($model, 'information') ?>

    <?php // echo $form->field($model, 'document') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
