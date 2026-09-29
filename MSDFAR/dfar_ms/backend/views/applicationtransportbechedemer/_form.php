<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\controllers\ExportCompanyController;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Applicationtransportbechedemer $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationtransportbechedemer-form">

    <?php $form = ActiveForm::begin([
        'id' => 'applicationtransportbechedemer-form',
        'options' => ['enctype' => 'multipart/form-data'], // Enable file upload
        'enableClientValidation' => true,
    ]); ?>

    <?= !UserTypeUtil::hasType(Constant::EXPORT_COMPANY) ? $form->field($model, 'company')->dropDownList
    (ExportCompanyController::getExportCompanyArray(), ["prompt" => "Please select a company"]) : "" ?>

    <?= $form->field($model, 'telephone')->textInput([
        'type' => 'text', 
        'required' => true
    ])->label('Telephone Number') ?>

    <?= $form->field($model, 'nic_number')->textInput([
        'maxlength' => true, 
        'required' => true
    ])->label('NIC Number') ?>

    <?= $form->field($model, 'mailing_address')->textInput([
        'maxlength' => true, 
        'required' => true
    ])->label('Mailing Address') ?>

    <?= $form->field($model, 'email')->textInput([
        'type' => 'email', 
        'required' => true
    ])->label('Email') ?>
    <div class="row">
        <div class="col-lg-6"><h3>Store places</h3></div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" id="dynamicTable">
            <thead>
            <tr>
                <th>Species</th>
                <th>Total weight (Kg)</th>
                <th>Purchasing District</th>
                <th>Intermediate Destination</th>
                <th>Final Store Place</th>
                <th>Vehicle Number</th>
                <th>Boat Number</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <tr class="template" style="display:none;">
                <td><?= Html::hiddenInput('ApplicationTransportStorePlace[id][]', '') ?>
                    <?= Html::textInput('ApplicationTransportStorePlace[species][]', '', ['class' => 'form-control']) ?></td>
                <td><?= Html::textInput('ApplicationTransportStorePlace[weight_per_distict][]', '', ['class' => 'form-control', 'type' => 'number']) ?></td>
                <td><?= Html::textInput('ApplicationTransportStorePlace[purchasing_district][]', '', ['class' => 'form-control']) ?></td>
                <td><?= Html::textInput('ApplicationTransportStorePlace[intermediat_destination][]', '', ['class' => 'form-control']) ?></td>
                <td><?= Html::textInput('ApplicationTransportStorePlace[final_store_place][]', '', ['class' => 'form-control']) ?></td>
                <td><?= Html::textInput('ApplicationTransportStorePlace[vehicle_number][]', '', ['class' => 'form-control']) ?></td>
                <td><?= Html::textInput('ApplicationTransportStorePlace[boat_number][]', '', ['class' => 'form-control']) ?></td>
                <td>
                    <button type="button" class="btn btn-danger removeRow">Remove</button>
                </td>
            </tr>
            <?php foreach ($storePlaces as $index => $place): ?>
                <tr>
                    <td><?= Html::hiddenInput('ApplicationTransportStorePlace[id][]', $place->id) ?>
                        <?= Html::textInput('ApplicationTransportStorePlace[species][]', $place->species, ['class' => 'form-control']) ?></td>
                    <td><?= Html::textInput('ApplicationTransportStorePlace[weight_per_distict][]', $place->weight_per_distict, ['class' => 'form-control', 'type' => 'number']) ?></td>
                    <td><?= Html::textInput('ApplicationTransportStorePlace[purchasing_district][]', $place->purchasing_district, ['class' => 'form-control']) ?></td>
                    <td><?= Html::textInput('ApplicationTransportStorePlace[intermediat_destination][]', $place->intermediat_destination, ['class' => 'form-control']) ?></td>
                    <td><?= Html::textInput('ApplicationTransportStorePlace[final_store_place][]', $place->final_store_place, ['class' => 'form-control']) ?></td>
                    <td><?= Html::textInput('ApplicationTransportStorePlace[vehicle_number][]', $place->vehicle_number, ['class' => 'form-control']) ?></td>
                    <td><?= Html::textInput('ApplicationTransportStorePlace[boat_number][]', $place->boat_number, ['class' => 'form-control']) ?></td>
                    <td>
                        <button type="button" class="btn btn-danger removeRow">Remove</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-group">
        <button type="button" id="addRow" class="btn btn-primary">Add new Store Place</button>
    </div>


    <!-- Buttons -->
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Clear'), [
            'class' => 'btn btn-secondary',
            'onclick' => "$('#applicationtransportbechedemer-form')[0].reset();"
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

