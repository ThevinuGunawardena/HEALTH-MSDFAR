<?php

use backend\config\Constant;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\DepartureSkipperSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="departure-skipper-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-xl-4">
            <?= $form->field($model, 'skipper_name') ?>
        </div>

        <div class="col-xl-4">
            <?= $form->field($model, 'nic') ?>
        </div>

        <div class="col-xl-4">
            <?= $form->field($model, 'skipper_id') ?>
        </div>

        <div class="col-xl-4">
            <?= $form->field(
                $model,
                'status'
            )->dropDownList(
                Constant::$departureStatus,
                [
                    'prompt' => 'Please Select',
                ]
            ) ?>
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
            'Reset',
            ['index'],
            [
                'class' => 'btn btn-outline-secondary',
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
