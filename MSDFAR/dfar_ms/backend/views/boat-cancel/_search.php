<?php

use backend\config\Constant;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberCancelRequestsSearch $model */

$yesNoOptions = [];

foreach (Constant::$yesNo as $key => $value) {
    $yesNoOptions[$key] = Yii::t(
        'app',
        (string) $value
    );
}

$statusOptions = [];

foreach (Constant::$licenseStatus as $key => $value) {
    $statusOptions[$key] = Yii::t(
        'app',
        (string) $value
    );
}
?>

<div class="boat-number-cancel-requests-search mb-4">
    <?php $form = ActiveForm::begin([
        'action' => ['/boat-cancel/index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'autocomplete' => 'off',
        ],
    ]); ?>

    <div class="row">
        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <?= $form->field(
                $model,
                'repairable'
            )->dropDownList(
                $yesNoOptions,
                [
                    'prompt' => Yii::t(
                        'app',
                        'All'
                    ),
                ]
            ) ?>
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <?= $form->field(
                $model,
                'parts_available_for_inspection'
            )->dropDownList(
                $yesNoOptions,
                [
                    'prompt' => Yii::t(
                        'app',
                        'All'
                    ),
                ]
            ) ?>
        </div>

        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
            <?= $form->field(
                $model,
                'status'
            )->dropDownList(
                $statusOptions,
                [
                    'prompt' => Yii::t(
                        'app',
                        'All'
                    ),
                ]
            ) ?>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <?= $form->field(
                $model,
                'proposed_dispose'
            )->textInput([
                'maxlength' => true,
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('app', 'Search'),
            [
                'class' => 'btn btn-primary',
            ]
        ) ?>

        <?= Html::a(
            Yii::t('app', 'Reset'),
            ['/boat-cancel/index'],
            [
                'class' => 'btn btn-outline-secondary',
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
