<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use backend\models\ELogLongline;

$this->title = 'Longline Details';
// $this->params['breadcrumbs'][] = [
//     'label' => 'E-Log',
//     'url' => ['e-log-edit/other', 'id' => $eLogId]
// ];
$this->params['breadcrumbs'][] = $this->title;

$form = ActiveForm::begin();
?>

<div class="container mt-3">
    <div class="card">

        <div class="card-header">
            <h5>Longline Details — E-Log #<?= $eLogId ?></h5>
        </div>

        <div class="card-body">
            <div class="row">

                <!-- Mainline -->
                <div class="col-md-6">
                    <?= $form->field($model, 'mainline')->textInput([
                        'type'        => 'number',
                        'step'        => '0.01',
                        'placeholder' => 'e.g. 120.50',
                    ]) ?>
                </div>

                <!-- Branchline -->
                <div class="col-md-6">
                    <?= $form->field($model, 'branchline')->textInput([
                        'type'        => 'number',
                        'step'        => '0.01',
                        'placeholder' => 'e.g. 15.00',
                    ]) ?>
                </div>

                <!-- Number of Hooks -->
                <div class="col-md-6">
                    <?= $form->field($model, 'no_of_hooks')->textInput([
                        'type'        => 'number',
                        'min'         => '1',
                        'placeholder' => 'e.g. 200',
                    ]) ?>
                </div>

                <!-- Hook Type -->
                <div class="col-md-6">
                    <?= $form->field($model, 'hook_type')->dropDownList(
                        ELogLongline::hookTypeList(),
                        ['prompt' => 'Select Hook Type',
                        'disabled'    => true,]

                    ) ?>
                    <?= Html::activeHiddenInput($model, 'hook_type') ?>

                </div>

                <!-- Depth -->
                <div class="col-md-6">
                    <?= $form->field($model, 'depth')->textInput([
                        'type'        => 'number',
                        'step'        => '0.01',
                        'placeholder' => 'e.g. 50.00',
                        'disabled'    => true,
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'no_hook_bet')->textInput([
                        'type'        => 'number',
                        'step'        => '1',
                        'placeholder' => 'e.g. 50',
                    ]) ?>
                </div>

                <!-- Bait -->
                <div class="col-md-6">
                    <?= $form->field($model, 'bait')->dropDownList(
                        ELogLongline::baitList(),
                        ['prompt' => 'Select Bait Type']
                    ) ?>
                </div>

            </div>
        </div>

        <div class="card-footer text-end">
            <?= Html::submitButton('Save Longline Data', [
                'class' => 'btn btn-success'
            ]) ?>
        </div>

    </div>
</div>

<?php ActiveForm::end(); ?>