<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ApplicationexportlobsterSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationexportlobster-search">

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

    <?= $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'lobster_species') ?>

    <?php // echo $form->field($model, 'number') ?>

    <?php // echo $form->field($model, 'weight') ?>

    <?php // echo $form->field($model, 'caught_place') ?>

    <?php // echo $form->field($model, 'export_country') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
