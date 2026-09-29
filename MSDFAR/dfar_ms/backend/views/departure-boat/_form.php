<?php

use backend\config\Constant;
use backend\models\MFiDistrict;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\DepartureBoats $model */
/** @var yii\widgets\ActiveForm $form */
/** @var string|null $token */

$token = trim((string) ($token ?? ''));

$districtList = ArrayHelper::map(
    MFiDistrict::find()
        ->where(['status' => 1])
        ->asArray()
        ->all(),
    'id',
    'name'
);

$status = [];
$role = '';

if (Yii::$app->user->can('DepartureBoatController-VMS')) {
    $status = Constant::getMainTypesKeyValue('VMS');
    $role = 'VMS';
} elseif (Yii::$app->user->can('DepartureBoatController-Investigation')) {
    $status = Constant::getMainTypesKeyValue('Investigation');
    $role = 'Investigation';
} elseif (Yii::$app->user->can('DepartureBoatController-Operation')) {
    $status = Constant::getMainTypesKeyValue('Operation');
    $role = 'Operation';
}

$formConfig = [];

if ($token !== '') {
    $formConfig['action'] = [
        '/departure-boat/update',
        'token' => $token,
    ];
}

$statusInputId = Html::getInputId(
    $model,
    'status'
);

$roleJs = Json::htmlEncode($role);
$selectedReasonJs = Json::htmlEncode(
    (string) ($model->remarks ?? '')
);
$statusInputIdJs = Json::htmlEncode(
    $statusInputId
);
?>

<div class="col-lg-8">

    <?php $form = ActiveForm::begin($formConfig); ?>

    <?= $form->field(
        $model,
        'status'
    )->dropDownList(
        $status,
        [
            'prompt' => 'Please select ...',
        ]
    ) ?>

    <?= $form->field(
        $model,
        'district'
    )->dropDownList(
        $districtList,
        [
            'prompt' => 'Please select ...',
        ]
    ) ?>

    <?= $form->field(
        $model,
        'remarks'
    )->dropDownList(
        [],
        [
            'prompt' => 'Please select ...',
        ]
    ) ?>

    <?= $form->field(
        $model,
        'date_violation'
    )->textInput([
        'type' => 'date',
    ]) ?>

    <?= $form->field(
        $model,
        'offence'
    )->textInput([
        'maxlength' => true,
    ]) ?>

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

<?php
$this->registerJs(<<<JS
(function () {
    'use strict';

    const statusInputId = {$statusInputIdJs};
    const role = {$roleJs};
    const selectedReason = {$selectedReasonJs};
    const \$status = $('#' + statusInputId);

    if (typeof window.loadDepartureBoatReasons !== 'function') {
        console.error('loadDepartureBoatReasons() is not available.');
        return;
    }

    \$status.on('change', function () {
        loadDepartureBoatReasons(
            $(this).val(),
            role,
            ''
        );
    });

    if (\$status.val()) {
        loadDepartureBoatReasons(
            \$status.val(),
            role,
            selectedReason
        );
    }
})();
JS);
?>