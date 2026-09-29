<?php

use backend\models\SkipperTranings;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var SkipperTranings $model */
/** @var ActiveForm $form */
?>

<div class="skipper-renew-training-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'class' => 'userform',
        ],
    ]); ?>

    <?= $form->field(
        $model,
        'skipper_id'
    )->hiddenInput()->label(false) ?>

    <?= $form->field(
        $model,
        'institute'
    ) ?>

    <?= $form->field(
        $model,
        'program_name'
    ) ?>

    <?= $form->field(
        $model,
        'training_period'
    ) ?>

    <?= $form->field(
        $model,
        'date_certified'
    )->textInput([
        'type' => 'date',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('app', 'Submit'),
            [
                'class' => 'btn btn-primary',
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
