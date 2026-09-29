<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use backend\models\ELogLongline;

$this->title = 'Edit Longline';
$form = ActiveForm::begin();
?>

<div class="container mt-3">
    <div class="card">
        <div class="card-header"><h5>Edit Longline — E-Log #<?= $elogId ?></h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'mainline')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'branchline')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'no_of_hooks')->textInput(['type' => 'number', 'min' => '1']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'hook_type')->dropDownList(ELogLongline::hookTypeList(), ['prompt' => 'Select Hook Type','disabled'=> true,]) ?>
                    <?= Html::activeHiddenInput($model, 'hook_type') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'depth')->textInput(['type' => 'number', 'step' => '0.01','disabled'=> true,]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'bait')->dropDownList(ELogLongline::baitList(), ['prompt' => 'Select Bait']) ?>
                </div>
               
                <div class="col-md-6">
                    <?= $form->field($model, 'no_hook_bet')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
           <?= Html::a('Cancel', [
                'geardata',
                'id' => $elogId,
                'dep_id' => $dep_id,
                'longline' => $longline,
                'gillnet' => $gillnet,
                'ringnet' => $ringnet,
            ], ['class' => 'btn btn-secondary me-2']) ?>
            <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>