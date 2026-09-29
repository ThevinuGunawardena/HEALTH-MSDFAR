<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\SkipperTranings $model */

?>
<div class="training-form">
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'userform'],
    ]); ?>

    <?= $form->errorSummary($model, [
        'class' => 'alert alert-danger',
    ]) ?>

    <?php if (!empty($model->skipper_id)): ?>
        <?= Html::activeHiddenInput(
            $model,
            'skipper_id'
        ) ?>
    <?php endif; ?>

    <?= $form->field($model, 'institute') ?>
    <?= $form->field($model, 'program_name') ?>
    <?= $form->field($model, 'training_period') ?>
    <?= $form->field($model, 'date_certified')->input('date') ?>

    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('app', 'Submit'),
            ['class' => 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
