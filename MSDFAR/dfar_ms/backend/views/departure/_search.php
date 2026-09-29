<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\DepartureRequestsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>
<hr>
<div class="departure-requests-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'boat_no') ?>

        </div>

        <div class="col-lg-3">
            <?php echo $form->field($model, 'email') ?>

        </div>
        <div class="col-lg-3">
            <?php echo $form->field($model, 'approve') ?>

        </div>
        <div class="col-lg-3">
            <?php echo $form->field($model, 'action_date') ?>

        </div>
        <div class="col-lg-3">
            <?php echo $form->field($model, 'skipper_nic') ?>

        </div>
        <div class="col-lg-3">
            <?php echo $form->field($model, 'harbor') ?>

        </div>
    </div>

    <!--    --><?php //= $form->field($model, 'boat_name') ?>


    <!--    --><?php //= $form->field($model, 'contact_no') ?>


    <?php // echo $form->field($model, 'skipper') ?>

    <?php // echo $form->field($model, 'skipper_no') ?>

    <!--    --><?php // echo $form->field($model, 'skipper_nic') ?>

    <?php // echo $form->field($model, 'district') ?>

    <!--    --><?php // echo $form->field($model, 'harbor') ?>

    <?php // echo $form->field($model, 'fishing_area') ?>

    <?php // echo $form->field($model, 'length_longline') ?>

    <?php // echo $form->field($model, 'length_gillnet') ?>

    <?php // echo $form->field($model, 'length_ringnet') ?>

    <?php // echo $form->field($model, 'longline_hooks') ?>

    <?php // echo $form->field($model, 'mesh_gillnet') ?>

    <?php // echo $form->field($model, 'mesh_ringnet') ?>

    <?php // echo $form->field($model, 'crew1') ?>

    <?php // echo $form->field($model, 'crew1_id') ?>

    <?php // echo $form->field($model, 'crew2') ?>

    <?php // echo $form->field($model, 'crew2_id') ?>

    <?php // echo $form->field($model, 'crew3') ?>

    <?php // echo $form->field($model, 'crew3_id') ?>

    <?php // echo $form->field($model, 'crew4') ?>

    <?php // echo $form->field($model, 'crew4_id') ?>

    <?php // echo $form->field($model, 'crew5') ?>

    <?php // echo $form->field($model, 'crew5_id') ?>

    <?php // echo $form->field($model, 'crew6') ?>

    <?php // echo $form->field($model, 'crew6_id') ?>

    <?php // echo $form->field($model, 'crew7') ?>

    <?php // echo $form->field($model, 'crew7_id') ?>

    <?php // echo $form->field($model, 'crew8') ?>

    <?php // echo $form->field($model, 'crew8_id') ?>

    <?php // echo $form->field($model, 'national_license_no') ?>

    <?php // echo $form->field($model, 'hs_license_no') ?>

    <?php // echo $form->field($model, 'vms') ?>

    <?php // echo $form->field($model, 'agree') ?>

    <?php // echo $form->field($model, 'req_date_time') ?>

    <?php // echo $form->field($model, 'user') ?>



    <?php // echo $form->field($model, 'remarks') ?>

    <?php // echo $form->field($model, 'water_bot') ?>

    <?php // echo $form->field($model, 'mcs') ?>

    <?php // echo $form->field($model, 'frequency') ?>

    <?php // echo $form->field($model, 'vms_code') ?>

    <?php // echo $form->field($model, 'manual') ?>

    <?php // echo $form->field($model, 'arrivalPort') ?>

    <?php // echo $form->field($model, 'arrivalDate') ?>

    <?php // echo $form->field($model, 'arrTime') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>