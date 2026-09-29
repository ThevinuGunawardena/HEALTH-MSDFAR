<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Edit Ring Net';
$form = ActiveForm::begin();
?>

<div class="container mt-3">
    <div class="card">
        <div class="card-header"><h5>Edit Ring Net — E-Log #<?= $elogId ?></h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'net_length')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'net_height')->textInput(['type' => 'number', 'step' => '0.01']) ?>
                </div>
                 <div class="col-md-6">
                    <?= $form->field($model, 'fad')->textarea(['rows' => 4]) ?>
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