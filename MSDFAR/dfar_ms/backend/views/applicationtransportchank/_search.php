<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ApplicationtransportchankSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationtransportchank-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'full_name') ?>

    <?= $form->field($model, 'permanent_address') ?>

    <?= $form->field($model, 'telephone_no') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'national_id') ?>

    <?php // echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'business_registration_no') ?>

    <?php // echo $form->field($model, 'purchasing_district') ?>

    <?php // echo $form->field($model, 'processing_district') ?>

    <?php // echo $form->field($model, 'final_storing_district') ?>

    <?php // echo $form->field($model, 'store_place') ?>

    <?php // echo $form->field($model, 'final_store_place') ?>

    <?php // echo $form->field($model, 'supporting_document') ?>

    <?php // echo $form->field($model, 'vehicle_number') ?>

    <?php // echo $form->field($model, 'boat_number') ?>

    <?php // echo $form->field($model, 'quantity_kg') ?>

    <?php // echo $form->field($model, 'quantity_pieces') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
