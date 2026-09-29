<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ApplicationexportbechedemerSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationexportbechedemer-search">

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

    <?= $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'business_reg_number') ?>

    <?php // echo $form->field($model, 'commercial_name') ?>

    <?php // echo $form->field($model, 'quantity_kg') ?>

    <?php // echo $form->field($model, 'quantity_pieces') ?>

    <?php // echo $form->field($model, 'total_weight') ?>

    <?php // echo $form->field($model, 'total_number') ?>

    <?php // echo $form->field($model, 'collection_area') ?>

    <?php // echo $form->field($model, 'charges') ?>

    <?php // echo $form->field($model, 'export_country') ?>

    <?php // echo $form->field($model, 'additional_information') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
