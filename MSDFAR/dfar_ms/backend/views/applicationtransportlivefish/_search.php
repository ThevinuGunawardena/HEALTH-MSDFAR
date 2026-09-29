<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ApplicationtransportlivefishSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationtransportlivefish-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'full_name') ?>

    <?= $form->field($model, 'permanent_address') ?>

    <?= $form->field($model, 'telephone') ?>

    <?= $form->field($model, 'fax_number') ?>

    <?= $form->field($model, 'nic_number') ?>

    <?php // echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'business_reg_number') ?>

    <?php // echo $form->field($model, 'purchase_places') ?>

    <?php // echo $form->field($model, 'transport_route') ?>

    <?php // echo $form->field($model, 'vehicle_number') ?>

    <?php // echo $form->field($model, 'boat_number') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
