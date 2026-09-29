<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProfileYardSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="profile-yard-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?php //$form->field($model, 'id') ?>

    <?php //$form->field($model, 'name') ?>

    <?php //$form->field($model, 'owner') ?>

    <?php // $form->field($model, 'address') ?>

    <?php // $form->field($model, 'mobile_number') ?>

    <?php // echo $form->field($model, 'land_line') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'web') ?>

    <?php // echo $form->field($model, 'fax') ?>

    <?php // echo $form->field($model, 'business_reg_no') ?>

    <?php // echo $form->field($model, 'business_reg_date') ?>

    <?php // echo $form->field($model, 'land_owner') ?>

    <?php // echo $form->field($model, 'deed_number') ?>

    <?php // echo $form->field($model, 'ownership_get_date') ?>

    <?php // echo $form->field($model, 'land_area') ?>

    <?php // echo $form->field($model, 'land_area_under_roof') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'admin_district') ?>

    <?php // echo $form->field($model, 'fisheries_district') ?>

    <?php // echo $form->field($model, 'division') ?>

    <?php // echo $form->field($model, 'gps_latitude') ?>

    <?php // echo $form->field($model, 'gps_longitude') ?>

    <?php // echo $form->field($model, 'transpotation_method') ?>

    <?php // echo $form->field($model, 'distance_rural_hospital') ?>

    <?php // echo $form->field($model, 'distance_district_hospital') ?>

    <?php // echo $form->field($model, 'distance_base_hospital') ?>

    <?php // echo $form->field($model, 'distance_teaching_hospital') ?>

    <?php // echo $form->field($model, 'distance_genaral_hospital') ?>

    <?php // echo $form->field($model, 'distance_fire_brigade') ?>

    <?php // echo $form->field($model, 'distance_police_station') ?>
    <div class="row">
        <div class="col-xl-6">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'fisheries_district') ?>
        </div>
    </div>
    <div class="row">

        <div class="col-xl-6">
            <?= $form->field($model, 'owner') ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
