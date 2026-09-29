<?php

use backend\config\Constant;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberCancelRequests $model */
/** @var array|string $formAction */
/** @var array|string $cancelUrl */

$yesNoOptions = [];

foreach (Constant::$yesNo as $key => $value) {
    $yesNoOptions[$key] = Yii::t(
        'app',
        (string) $value
    );
}
?>

<div class="boat-number-cancel-requests-form">
    <?php $form = ActiveForm::begin([
        'action' => $formAction,
        'method' => 'post',
        'options' => [
            'class' => 'userform',
            'autocomplete' => 'off',
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field(
                $model,
                'repairable'
            )->dropDownList(
                $yesNoOptions,
                [
                    'prompt' => Yii::t(
                        'app',
                        'Select'
                    ),
                ]
            ) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field(
                $model,
                'parts_available_for_inspection'
            )->dropDownList(
                $yesNoOptions,
                [
                    'prompt' => Yii::t(
                        'app',
                        'Select'
                    ),
                ]
            ) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field(
                $model,
                'proposed_dispose'
            )->textarea([
                'maxlength' => true,
                'rows' => 4,
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field(
                $model,
                'address_of_part_inspection'
            )->textarea([
                'maxlength' => true,
                'rows' => 4,
            ]) ?>
        </div>

        <div class="col-md-12">
            <?= $form->field(
                $model,
                'present_condition_of_boat'
            )->textarea([
                'maxlength' => true,
                'rows' => 4,
            ]) ?>
        </div>
    </div>

    <div class="form-group d-flex justify-content-end">
        <?= Html::a(
            Yii::t('app', 'Cancel'),
            $cancelUrl,
            [
                'class' => 'btn btn-outline-secondary mr-2',
            ]
        ) ?>

        <?= Html::submitButton(
            Yii::t('app', 'Save'),
            [
                'class' => 'btn btn-success',
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
