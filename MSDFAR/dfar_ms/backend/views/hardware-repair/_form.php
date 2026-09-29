<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\HardwareRepair $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCss("
    .hardware-form {
        width: 1000px;
        max-width: 1000px;
        padding: 30px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        background: #ffffff;
        border-radius: 10px;
    }
    .form-group label {
        font-weight: 600;
    }
    .form-control {
        height: 45px;
        font-size: 14px;
    }
    textarea.form-control {
        resize: vertical;
    }
    .btn {
        padding: 10px 20px;
        font-size: 16px;
    }
");

?>

<div class="full-page-container">
    <div class="hardware-form">
        <?php $form = ActiveForm::begin([
            'id' => 'hardware-form',
            'options' => ['enctype' => 'multipart/form-data', 'data-pjax' => true],
        ]); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'Name')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'Phone_number')->textInput(['type' => 'tel', 'maxlength' => true, 'class' => 'form-control']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'Email')->textInput(['type' => 'email', 'maxlength' => true, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'Office')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'Serial_number')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'Brand_name')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'Issue')->textarea(['rows' => 6, 'class' => 'form-control']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'Received_date')->textInput([
                    'type' => 'date',
                    'value' => date('Y-m-d'),
                    'class' => 'form-control',
                    'readonly' => true,
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'Status')->dropDownList(
                    [
                        'Open' => 'Open',
                        'In Progress' => 'In Progress',
                        'Completed' => 'Completed',
                    ],
                    [
                        'prompt' => 'Select Status',
                        'class' => 'form-control',
                    ]
                ) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'Remarks')->textarea(['rows' => 6, 'class' => 'form-control']) ?>
            </div>
        </div>

        <div class="form-group text-center">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <?= Html::resetButton('Reset', ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>