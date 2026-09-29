<?php

use backend\config\Constant;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\DepartureSkipper|app\models\DepartureSkipperC $model */
/** @var yii\widgets\ActiveForm $form */
/** @var string|null $token */
/** @var string|null $formAction */

$token = isset($token)
    ? trim((string) $token)
    : '';

$formAction = isset($formAction)
    ? trim((string) $formAction)
    : '';

$formConfig = [];

/*
 * On tokenized update pages, post back to the same action with the token.
 * On create, allow ActiveForm to use the current /create route normally.
 */
if ($token !== '' && $formAction !== '') {
    $formConfig['action'] = [
        $formAction,
        'token' => $token,
    ];
}

$nicOptions = [
    'maxlength' => true,
];

/*
 * Keep the identity value stable after creation. Public navigation now uses a
 * token bound to the persisted DepartureSkipper record rather than NIC.
 */
if (!$model->isNewRecord) {
    $nicOptions['readonly'] = true;
}
?>

<div class="row">
    <div class="col-lg-8">

        <?php $form = ActiveForm::begin($formConfig); ?>

        <div class="row">
            <div class="col-lg-12">
                <?= $form->field(
                    $model,
                    'skipper_name'
                )->textInput([
                    'maxlength' => true,
                ]) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field(
                    $model,
                    'nic'
                )->textInput($nicOptions) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field(
                    $model,
                    'crew_type'
                )->dropDownList(
                    [
                        'Skipper' => 'Skipper',
                        'Crew Member' => 'Crew Member',
                    ],
                    [
                        'prompt' => 'Please Select',
                    ]
                ) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field(
                    $model,
                    'skipper_id'
                )->textInput([
                    'maxlength' => true,
                ]) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field(
                    $model,
                    'address'
                )->textInput([
                    'maxlength' => true,
                ]) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field(
                    $model,
                    'contact'
                )->textInput([
                    'maxlength' => true,
                ]) ?>
            </div>
        </div>

        <?= $form->field(
            $model,
            'harbor'
        )->dropDownList(
            Constant::$departureHarbours,
            [
                'prompt' => 'Please Select',
            ]
        ) ?>

        <?= $form->field(
            $model,
            'served_vessel'
        )->textInput([
            'maxlength' => true,
        ]) ?>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field(
                    $model,
                    'dep_date'
                )->textInput([
                    'type' => 'date',
                ]) ?>
            </div>

            <div class="col-lg-6">
                <?= $form->field(
                    $model,
                    'dep_id'
                )->textInput([
                    'maxlength' => true,
                ]) ?>
            </div>
        </div>

        <?= $form->field(
            $model,
            'status'
        )->dropDownList(
            Constant::$departureStatus,
            [
                'prompt' => 'Please Select',
            ]
        ) ?>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field(
                    $model,
                    'dep_cancel_date'
                )->textInput([
                    'type' => 'date',
                ]) ?>
            </div>

            <div class="col-lg-6">
                <?= $form->field(
                    $model,
                    'to_date'
                )->textInput([
                    'type' => 'date',
                ]) ?>
            </div>
        </div>

        <?= $form->field(
            $model,
            'offence_reason'
        )->dropDownList(
            Constant::$departureOffence,
            [
                'prompt' => 'Please Select',
            ]
        ) ?>

        <?= $form->field(
            $model,
            'remarks'
        )->textInput([
            'maxlength' => true,
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton(
                Yii::t('app', 'Save'),
                [
                    'class' => 'btn btn-success',
                ]
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
