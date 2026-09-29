<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\TransportregistrationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="transportregistration-search">

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

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'reg_number') ?>

    <?php // echo $form->field($model, 'nid_number') ?>

    <?php // echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'mail_address') ?>

    <?php // echo $form->field($model, 'permit_type') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'area') ?>

    <?php // echo $form->field($model, 'vehicle_number') ?>

    <?php // echo $form->field($model, 'destination_district') ?>

    <?php // echo $form->field($model, 'product_storing_area') ?>

    <?php // echo $form->field($model, 'finalstoreplace_address') ?>

    <?php // echo $form->field($model, 'import_country') ?>

    <?php // echo $form->field($model, 'document') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
