<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ScientificData $model */
/** @var yii\widgets\ActiveForm $form */
/** @var string|null $token */
?>

<div class="scientific-data-form">

    <?php
    $formConfig = [
        'options' => [
            'class' => 'userform',
        ],
    ];

    if (!empty($token)) {
        $formConfig['action'] = [
            '/scientific/update',
            'token' => $token,
        ];
    }

    $form = ActiveForm::begin($formConfig);
    ?>

    <?= $form->field($model, 'district')->textInput() ?>

    <?= $form->field($model, 'division')->textInput() ?>

    <?= $form->field($model, 'landing_site')->textInput() ?>

    <?= $form->field($model, 'start_time')->textInput() ?>

    <?= $form->field($model, 'end_time')->textInput() ?>

    <?= $form->field($model, 'added_by')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
