<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\controllers\ExportCompanyController;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Applicationtransportlivefish $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationtransportlivefish-form">

    <?php $form = ActiveForm::begin([
        'id' => 'applicationtransportlivefish-form',
        'options' => ['enctype' => 'multipart/form-data'], // Enable file upload
        'enableClientValidation' => true,
    ]); ?>

    <?= !UserTypeUtil::hasType(Constant::EXPORT_COMPANY) ? $form->field($model, 'company')->dropDownList
    (ExportCompanyController::getExportCompanyArray(), ["prompt" => "Please select a company"]) : "" ?>
    <!-- Telephone Number -->
    <?= $form->field($model, 'telephone')->textInput([
        'type' => 'text', 
        'required' => true
    ])->label('Telephone Number') ?>
    <?= $form->field($model, 'fax_number')->textInput([
        'type' => 'text',
        'required' => true
    ]) ?>

    <?= $form->field($model, 'nic_number')->textInput([
        'maxlength' => true, 
        'required' => true
    ])->label('NIC Number') ?>


    <div class="row">
        <div class="col-lg-6"><h3>Store places</h3></div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" id="dynamicTable">
            <thead>
            <tr>
                <th>Species</th>
                <th>Weight per District</th>
                <th>Purchasing District</th>
                <th>Intermediate Destination</th>
                <th>Final Store Place</th>
                <th>Transport Method</th>
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
                <td><?= Html::textInput('ApplicationTransportStorePlace[transport_method][]', '', ['class' => 'form-control']) ?></td>
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
                    <td><?= Html::textInput('ApplicationTransportStorePlace[transport_method][]', $place->transport_method, ['class' => 'form-control']) ?></td>
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
            'onclick' => "$('#applicationtransportlivefish-form')[0].reset();"
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- Store Place Modal -->
<div id="storePlaceModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Store Place Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="storePlaceFields">
                    <!-- Store Place Fields -->
                    <div class="form-group"><input type="text" class="form-control" placeholder="Species / Type" name="Applicationtransportlivefish[species_type][]"></div>
                    <div class="form-group"><input type="number" class="form-control" placeholder="Weight per purchasing district in kilograms" name="Applicationtransportlivefish[weight_per_district][]"></div>
                    <div class="form-group"><input type="number" class="form-control" placeholder="Total Weight in kilograms" name="Applicationtransportlivefish[total_weight][]"></div>
                    <div class="form-group"><input type="text" class="form-control" placeholder="Purchasing District / Collected Area" name="Applicationtransportlivefish[purchasing_district][]"></div>
                    <div class="form-group"><input type="text" class="form-control" placeholder="Intermediate Destination / Store places" name="Applicationtransportlivefish[intermediate_destination][]"></div>
                    <div class="form-group"><input type="text" class="form-control" placeholder="Final Store Place / District" name="Applicationtransportlivefish[final_destination][]"></div>
                    <div class="form-group"><input type="text" class="form-control" placeholder="Transport Route" name="Applicationtransportlivefish[transport_route][]"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveStorePlaceFields()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Transport Method Modal -->
<div id="transportMethodModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Transport Method</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="transportMethodFields">
                    <!-- Transport Method Fields -->
                    <div class="form-group"><input type="text" class="form-control" placeholder="Enter vehicle number" name="Applicationtransportlivefish[vehicle]"></div>
                    <div class="form-group"><input type="text" class="form-control" placeholder="Enter boat number" name="Applicationtransportlivefish[boat]"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveTransportMethodFields()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Handlers -->
<script>
    function showStorePlaceModal() {
        $('#storePlaceModal').modal('show');
    }

    function saveStorePlaceFields() {
        const fields = document.querySelectorAll('#storePlaceFields .form-control');
        const container = document.getElementById('store_place_details');
        container.innerHTML = '';
        fields.forEach(field => {
            const value = field.value;
            if (value) {
                const input = `<input type="hidden" name="${field.name}" value="${value}">`;
                container.insertAdjacentHTML('beforeend', input);
            }
        });
        $('#storePlaceModal').modal('hide');
    }

    function showTransportMethodModal() {
        $('#transportMethodModal').modal('show');
    }

    function saveTransportMethodFields() {
        const fields = document.querySelectorAll('#transportMethodFields .form-control');
        const container = document.getElementById('transport_method_details');
        container.innerHTML = '';
        fields.forEach(field => {
            const value = field.value;
            if (value) {
                const input = `<input type="hidden" name="${field.name}" value="${value}">`;
                container.insertAdjacentHTML('beforeend', input);
            }
        });
        $('#transportMethodModal').modal('hide');
    }
</script>