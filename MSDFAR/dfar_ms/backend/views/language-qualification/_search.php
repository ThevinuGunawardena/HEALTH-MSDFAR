<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\LanguageQualificationSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="language-qualification-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'profile_officer_id') ?>

    <?= $form->field($model, 'language') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'year') ?>

    <?php // echo $form->field($model, 'results_certificate') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
