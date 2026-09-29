<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use backend\models\ELogGillnet;

$this->title = 'Edit Gillnet';
$form = ActiveForm::begin();
?>

<div class="container mt-3">
    <div class="card">
        <div class="card-header"><h5>Edit Gillnet — E-Log #<?= $elogId ?></h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'net_material')->dropDownList(ELogGillnet::materialList(), ['prompt' => 'Select Material','disabled'=> true]) ?>
                    <?= Html::activeHiddenInput($model, 'net_material') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'mesh_size')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'ply')->textInput(['type' => 'number', 'min' => '1','disabled'=> true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'net_height')->textInput(['type' => 'number', 'step' => '0.01','disabled'=> true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'set_depth')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'net_pieces')->textInput(['type' => 'number', 'min' => '1','disabled'=> true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'length')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                 </div>
                
            </div>
        </div>
        <div class="card-footer text-end">
            <?= Html::a('Cancel', ['view', 'id' => $elogId], ['class' => 'btn btn-secondary me-2']) ?>
            <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>