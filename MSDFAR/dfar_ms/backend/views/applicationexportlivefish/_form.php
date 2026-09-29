<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\controllers\ExportCompanyController;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportlivefish $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
    <?php $form = ActiveForm::begin([
        'id' => 'applicationexportlivefish-form',
        'options' => ['enctype' => 'multipart/form-data'], // Enable file upload
        'enableClientValidation' => true,
    ]); ?>

            <?= !UserTypeUtil::hasType(Constant::EXPORT_COMPANY) ? $form->field($model, 'company')->dropDownList
    (ExportCompanyController::getExportCompanyArray(), ["prompt" => "Please select a company"]) : "" ?>

    <?= $form->field($model, 'address')->textInput([
        'maxlength' => true, 
        'required' => true
    ]) ?>

    <!-- Telephone Number -->
    <?= $form->field($model, 'telephone')->textInput([
        'type' => 'text', 
        'required' => true
    ]) ?>
        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6"><h3>Name of the Species of Live Fish and the quantity</h3></div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dynamicTable">
                    <thead>
                    <tr>
                        <th>Species of Fish</th>
                        <th>Quantity</th>
                        <th>Area of Capture</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="template" style="display:none;">
                        <td><?= Html::hiddenInput('ExportLiveFishFishQty[id][]', '') ?>
                            <?= Html::textInput('ExportLiveFishFishQty[species_fish][]', '', ['class' => 'form-control']) ?></td>
                        <td><?= Html::textInput('ExportLiveFishFishQty[qty][]', '', ['class' => 'form-control', 'type' => 'number']) ?></td>
                        <td><?= Html::textInput('ExportLiveFishFishQty[area_capture][]', '', ['class' => 'form-control']) ?></td>
                        <td>
                            <button type="button" class="btn btn-danger removeRow">Remove</button>
                        </td>
                    </tr>
                    <?php foreach ($qties as $index => $qty): ?>
                        <tr>
                            <td><?= Html::hiddenInput('ExportLiveFishFishQty[id][]', $qty->id) ?>
                                <?= Html::textInput('ExportLiveFishFishQty[species_fish][]',
                                    $qty->species_fish, ['class' => 'form-control']) ?></td>
                            <td><?= Html::textInput('ExportLiveFishFishQty[qty][]',
                                    $qty->qty, ['class' => 'form-control', 'type' => 'number']) ?></td>
                            <td><?= Html::textInput('ExportLiveFishFishQty[area_capture][]', $qty->area_capture,
                                    ['class' => 'form-control']) ?></td>
                            <td>
                                <button type="button" class="btn btn-danger removeRow">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                <button type="button" id="addRow" class="btn btn-primary">Add new Consignment</button>
            </div>

        </div>
    </div>
</div>
<hr>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6"><h3>Monthly statement of Fish, Eggs, Roe or Spawn exported for last six (6)
                        months </h3></div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dynamicTables">
                    <thead>
                    <tr>
                        <th>Month</th>
                        <th>Species of fish</th>
                        <th>Quantity of Locally collected live fish, eggs, roe or spawn exported</th>
                        <th>Quantity of Locally breed species of live fish, eggs, roe or spawn exported</th>
                        <th>Quantity of imported fish re-exported</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="templates" style="display:none;">
                        <td><?= Html::hiddenInput('ExportLiveFishMonthlyStatement[id][]', '') ?>
                            <?= Html::textInput('ExportLiveFishMonthlyStatement[month][]', '', ['class' => 'form-control']) ?></td>
                        <td><?= Html::textInput('ExportLiveFishMonthlyStatement[fish][]', '', ['class' => 'form-control']) ?></td>
                        <td><?= Html::textInput('ExportLiveFishMonthlyStatement[local_collected_qty][]', '', ['class' => 'form-control', 'type' => 'number']) ?></td>
                        <td><?= Html::textInput('ExportLiveFishMonthlyStatement[local_breed_qty][]', '', ['class' => 'form-control', 'type' => 'number']) ?></td>
                        <td><?= Html::textInput('ExportLiveFishMonthlyStatement[imported_reexported_qty][]', '', ['class' => 'form-control', 'type' => 'number']) ?></td>
                        <td>
                            <button type="button" class="btn btn-danger removeRows">Remove</button>
                        </td>
                    </tr>
                    <?php foreach ($statements as $index => $statement): ?>
                        <tr>
                            <td><?= Html::hiddenInput('ExportLiveFishMonthlyStatement[id][]', $statement->id) ?>
                                <?= Html::textInput('ExportLiveFishMonthlyStatement[month][]',
                                    $statement->month, ['class' => 'form-control']) ?></td>
                            <td><?= Html::textInput('ExportLiveFishMonthlyStatement[fish][]',
                                    $statement->fish, ['class' => 'form-control']) ?></td>
                            <td><?= Html::textInput('ExportLiveFishMonthlyStatement[local_collected_qty][]', $statement->local_collected_qty,
                                    ['class' => 'form-control', 'type' => 'number']) ?></td>
                            <td><?= Html::textInput('ExportLiveFishMonthlyStatement[local_breed_qty][]', $statement->local_breed_qty,
                                    ['class' => 'form-control', 'type' => 'number']) ?></td>
                            <td><?= Html::textInput('ExportLiveFishMonthlyStatement[imported_reexported_qty][]', $statement->imported_reexported_qty,
                                    ['class' => 'form-control', 'type' => 'number']) ?></td>
                            <td>
                                <button type="button" class="btn btn-danger removeRows">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                <button type="button" id="addRows" class="btn btn-primary">Add new Statement</button>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <!-- Document Upload -->
            <?= $form->field($model, 'export_countries', [
                'template' => "<div class='row fish-checkbox'><div class='col-lg-12'>{label}</div><div class='chboxlist-fish col-lg-12'><input type='text' id='country-search' class='form-control' placeholder='Search countries...'><div class='checkbox-list'>{input}</div></div></div>\n{hint}\n{error}"
            ])->checkboxList(Constant::$countries, [
                'itemOptions' => ['class' => 'country-checkbox'],
                'item' => function ($index, $label, $name, $checked, $value) {
                    return "<div class='checkbox'><label><input type='checkbox' class='country-checkbox' name='$name' value='$value' " . ($checked ? 'checked' : '') . "> $label</label></div>";
                },
                'prompt' => 'Select'
            ]) ?>

        </div>
    </div>
</div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Clear'), [
            'class' => 'btn btn-secondary',
            'onclick' => "$('#applicationexportlivefish-form')[0].reset();"
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- Fish Species Modal -->
<div id="fishSpeciesModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Fish Species Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="fishSpeciesFields">
                    <!-- Fish Species Fields -->
                    <div class="form-group"><input type="number" class="form-control" placeholder="Locally Collected Quantity (kg)" name="Applicationexportlivefish[locally_collected_fish_quantity_value][]"></div>
                    <div class="form-group"><input type="number" class="form-control" placeholder="Locally Bred Quantity (kg)" name="Applicationexportlivefish[locally_bred_fish_quantity_value][]"></div>
                    <div class="form-group"><input type="number" class="form-control" placeholder="Re-exported Quantity (kg)" name="Applicationexportlivefish[re_exported_fish_quantity_value][]"></div>
                    <div class="form-group"><input type="number" class="form-control" placeholder="Weight (kg)" name="Applicationexportlivefish[weight][]"></div>
                    <div class="form-group"><input type="text" class="form-control" placeholder="Species/Type" name="Applicationexportlivefish[species_type][]"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveFishSpeciesFields()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Country Modal -->
<div id="exportCountryModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Export Countries</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="exportCountryFields">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Enter export country" name="Applicationexportlivefish[export_country][]">
                    </div>
                </div>
                <button type="button" class="btn btn-secondary" onclick="addExportCountryField()">Add More</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveExportCountryFields()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Handlers -->
<script>
    function showFishSpeciesModal() {
        $('#fishSpeciesModal').modal('show');
    }

    function saveFishSpeciesFields() {
        const fields = document.querySelectorAll('#fishSpeciesFields .form-control');
        const container = document.getElementById('fish_species_details');
        container.innerHTML = '';
        fields.forEach(field => {
            const value = field.value;
            if (value) {
                const input = `<input type="hidden" name="${field.name}" value="${value}">`;
                container.insertAdjacentHTML('beforeend', input);
            }
        });
        $('#fishSpeciesModal').modal('hide');
    }

    function showExportCountryModal() {
        $('#exportCountryModal').modal('show');
    }

    function addExportCountryField() {
        const container = document.getElementById('exportCountryFields');
        const input = `<div class="form-group"><input type="text" class="form-control" placeholder="Enter export country" name="Applicationexportlivefish[export_country][]"></div>`;
        container.insertAdjacentHTML('beforeend', input);
    }

    function saveExportCountryFields() {
        const fields = document.querySelectorAll('#exportCountryFields .form-control');
        const container = document.getElementById('export_countries');
        container.innerHTML = '';
        fields.forEach(field => {
            const value = field.value;
            if (value) {
                const input = `<input type="hidden" name="${field.name}" value="${value}">`;
                container.insertAdjacentHTML('beforeend', input);
            }
        });
        $('#exportCountryModal').modal('hide');
    }
</script>