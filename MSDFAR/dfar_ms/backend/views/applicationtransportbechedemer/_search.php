<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ApplicationtransportbechedemerSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationtransportbechedemer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'full_name') ?>

    <?= $form->field($model, 'permanent_address') ?>

    <?= $form->field($model, 'mailing_address') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'telephone') ?>

    <?php // echo $form->field($model, 'nic_number') ?>

    <?php // echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'business_reg_number') ?>

    <?php // echo $form->field($model, 'purchasing_district') ?>

    <?php // echo $form->field($model, 'store_district') ?>

    <?php // echo $form->field($model, 'final_destination') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'vehicle_number') ?>

    <?php // echo $form->field($model, 'boat_number') ?>

    <?php // echo $form->field($model, 'store_places') ?>

    <?php // echo $form->field($model, 'final_store_place') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
