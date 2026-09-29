<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ReexportSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="reexport-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'applicant_name') ?>

    <?= $form->field($model, 'permanent_address') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'business_reg_number') ?>

    <?php // echo $form->field($model, 'permit_type') ?>

    <?php // echo $form->field($model, 'commercial_name') ?>

    <?php // echo $form->field($model, 'total_weight') ?>

    <?php // echo $form->field($model, 'export_country') ?>

    <?php // echo $form->field($model, 'document') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
