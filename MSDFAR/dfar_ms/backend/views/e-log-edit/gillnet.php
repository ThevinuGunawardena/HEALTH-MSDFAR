<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use backend\models\ELogGillnet;

$this->title = 'Gillnet Details';
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
            <h5>Gillnet Details — E-Log #<?= $eLogId ?></h5>
        </div>

        <div class="card-body">
            <div class="row">

                <!-- Net Material -->
                <div class="col-md-6">
                    <?= $form->field($model, 'net_material')->dropDownList(
                        ELogGillnet::materialList(),
                        ['prompt' => 'Select Net Material',
                        'disabled'    => true,]

                    ) ?>
                    <?= Html::activeHiddenInput($model, 'net_material') ?>
                </div>

                <!-- Mesh Size -->
                <div class="col-md-6">
                    <?= $form->field($model, 'mesh_size')->textInput([
                        'type'        => 'number',
                        'step'        => '0.01',
                        'placeholder' => 'e.g. 25.00',
                    ]) ?>
                </div>

                <!-- Ply of the Net -->
                <div class="col-md-6">
                    <?= $form->field($model, 'ply')->textInput([
                        'type'        => 'number',
                        'min'         => '1',
                        'placeholder' => 'e.g. 3',
                        'disabled'    => true,
                    ]) ?>
                </div>

                <!-- Height of the Net -->
                <div class="col-md-6">
                    <?= $form->field($model, 'net_height')->textInput([
                        'type'        => 'number',
                        'step'        => '0.01',
                        'placeholder' => 'e.g. 10.00',
                        'disabled'    => true,
                    ]) ?>
                </div>

                <!-- Depth at Which Net is Set -->
                <div class="col-md-6">
                    <?= $form->field($model, 'set_depth')->textInput([
                        'type'        => 'number',
                        'step'        => '0.01',
                        'placeholder' => 'e.g. 30.00',
                    ]) ?>
                </div>

                <!-- Number of Net Pieces -->
                <div class="col-md-6">
                    <?= $form->field($model, 'net_pieces')->textInput([
                        'type'        => 'number',
                        'min'         => '1',
                        'placeholder' => 'e.g. 10',
                        'disabled'    => true,
                    ]) ?>
                </div>

                <!-- Length of the Net -->
                <div class="col-md-6">  
                    <?= $form->field($model, 'length')->textInput([
                        'type'        => 'number',
                        'step'        => '0.01',
                        'placeholder' => 'e.g. 100.00',
                    ]) ?>

            </div>
        </div>

        <div class="card-footer text-end">
            <?= Html::submitButton('Save Gillnet Data', [
                'class' => 'btn btn-success'
            ]) ?>
        </div>

    </div>
</div>

<?php ActiveForm::end(); ?>