<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Ring Net Details';
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
            <h5>Ring Net Details — E-Log #<?= $eLogId ?></h5>
        </div>

        <div class="card-body">
            <div class="row">

                <!-- Length of the Ring Net -->
                <div class="col-md-6">
                    <?= $form->field($model, 'net_length')->textInput([
                        'type'        => 'number',
                        'step'        => '0.01',
                        'placeholder' => 'e.g. 200.00',
                    ]) ?>
                </div>

                <!-- Height of the Ring Net -->
                <div class="col-md-6">
                    <?= $form->field($model, 'net_height')->textInput([
                        'type'        => 'number',
                        'step'        => '0.01',
                        'placeholder' => 'e.g. 20.00',
                    ]) ?>
                </div>
                <!-- FAD Information -->
                <div class="col-md-6">
                    <?= $form->field($model, 'fad')->textarea([
                        'rows'        => 4,
                        'placeholder' => 'If FAD is used, please provide details here.',
                    ]) ?>

            </div>
        </div>

        <div class="card-footer text-end">
            <?= Html::submitButton('Save Ring Net Data', [
                'class' => 'btn btn-success'
            ]) ?>
        </div>

    </div>
</div>

<?php ActiveForm::end(); ?>