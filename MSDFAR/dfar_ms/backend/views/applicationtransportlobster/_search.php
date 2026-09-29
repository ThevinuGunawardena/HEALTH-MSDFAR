<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ApplicationtransportlobsterSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationtransportlobster-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'full_name') ?>

    <?= $form->field($model, 'permanent_address') ?>

    <?= $form->field($model, 'applicant_address') ?>

    <?= $form->field($model, 'telephone') ?>

    <?= $form->field($model, 'fax_number') ?>

    <?php // echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'quantity_kg') ?>

    <?php // echo $form->field($model, 'quantity_pieces') ?>

    <?php // echo $form->field($model, 'transport_methods') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
