<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ApplicationexportlivefishSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationexportlivefish-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'full_name') ?>

    <?= $form->field($model, 'address') ?>

    <?= $form->field($model, 'telephone') ?>

    <?= $form->field($model, 'fax_number') ?>

    <?= $form->field($model, 'business_reg_number') ?>

    <?php // echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'fish_species') ?>

    <?php // echo $form->field($model, 'quantity_kg') ?>

    <?php // echo $form->field($model, 'quantity_pieces') ?>

    <?php // echo $form->field($model, 'capture_area') ?>

    <?php // echo $form->field($model, 'export_countries') ?>

    <?php // echo $form->field($model, 'month') ?>

    <?php // echo $form->field($model, 'fish_species_6month') ?>

    <?php // echo $form->field($model, 'locally_collected_fish_quantity_kg') ?>

    <?php // echo $form->field($model, 'locally_collected_fish_quantity_pieces') ?>

    <?php // echo $form->field($model, 'locally_breed_fish_quantity_kg') ?>

    <?php // echo $form->field($model, 'locally_breed_fish_quantity_pieces') ?>

    <?php // echo $form->field($model, 're_exported_fish_quantity_kg') ?>

    <?php // echo $form->field($model, 're_exported_fish_quantity_pieces') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
