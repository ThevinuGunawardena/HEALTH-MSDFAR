<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ApplicationexportchankSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationexportchank-search">

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

    <?php // echo $form->field($model, 'commercial_name') ?>

    <?php // echo $form->field($model, 'size') ?>

    <?php // echo $form->field($model, 'weight_kg') ?>

    <?php // echo $form->field($model, 'weight_pieces') ?>

    <?php // echo $form->field($model, 'caught_place') ?>

    <?php // echo $form->field($model, 'export_country') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
