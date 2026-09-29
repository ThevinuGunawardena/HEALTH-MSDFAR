<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\DistrictGearTypesSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="district-gear-types-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?php //$form->field($model, 'id') ?>

    <?php // $form->field($model, 'gear_type') ?>

    <?php // $form->field($model, 'extra') ?>

    <?php // $form->field($model, 'fishing_time_periods') ?>

    <?php // $form->field($model, 'fishing_time_durations') ?>

    
    <?php // echo $form->field($model, 'fish_species') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'district_id') ?>
    <div class="row">
        <div class="col-xl-6">
        <?= $form->field($model, 'gear_type')?>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'fish_species') ?>

        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
