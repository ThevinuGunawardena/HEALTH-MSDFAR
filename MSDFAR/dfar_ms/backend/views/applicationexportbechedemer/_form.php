<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\controllers\ExportCompanyController;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportbechedemer $model */
/** @var yii\widgets\ActiveForm $form */

// Register JS in your controller or view
$this->registerJs("
    function calculateTotalNumber(changedInput) {
        var row = $(changedInput).closest('tr.beche-demer-row');
        var quantityPerUnit = parseFloat(row.find('.quantity-per-unit').val()) || 0;
        var totalWeight = parseFloat(row.find('.total-weight').val()) || 0;
        var totalNumber = quantityPerUnit * totalWeight;
        row.find('.total-number').val(totalNumber.toFixed(2));
    }
", View::POS_END);
?>


<div class="applicationexportbechedemer-form">

    <?php $form = ActiveForm::begin([
        'id' => 'applicationexportbechedemer-form',
        'options' => ['enctype' => 'multipart/form-data'], // Enable file upload
        'enableClientValidation' => true,
    ]); ?>
    <?= !UserTypeUtil::hasType(Constant::EXPORT_COMPANY) ? $form->field($model, 'company')->dropDownList
    (ExportCompanyController::getExportCompanyArray(), ["prompt" => "Please select a company"]) : "" ?>


    <?= $form->field($model, 'telephone_number')->textInput([
        'maxlength' => true,
    ]) ?>
    <?= $form->field($model, 'email')->textInput([
        'maxlength' => true,
        'type' => 'email',
    ]) ?>
    <?= $form->field($model, 'business_reg_number')->textInput([
        'maxlength' => true,
    ]) ?>
    <?= $form->field($model, 'address')->textarea([
        'maxlength' => true,
    ]) ?>
    <?= $form->field($model, 'export_quantity_under_previous_license')->textInput([
        'maxlength' => true,
    ]) ?>
    <div class="row">
        <div class="col-lg-6"><h3> Details of consignment (Each license should apply separately)</h3></div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered" id="dynamicTable">
            <thead>
            <tr>
                <th>Commercial name</th>
                <th>Number of units per Kg</th>
                <th>Total weight (Kg)</th>
                <th>Total number</th>
                <th>Area of Supply</th>


            </tr>
            </thead>
            <tbody>
            <?php if ($create == true) { ?>
                <tr class="beche-demer-row">
                    <td><?= Html::hiddenInput('ExportBecheDemerConsignment[id][]', '') ?>

                        <?= Html::dropDownList(
                            'ExportBecheDemerConsignment[commetial_name][]',        // name
                            'Beche-de-mer',             // selected value
                            [
                                'Beche-de-mer' => 'Beche-de-mer',
                                'Pawakka (Stichopus nago)' => 'Pawakka (Stichopus nago)',
                            ],
                            [
                                'class' => 'form-control',
                                'prompt' => 'Select type'
                            ]
                        ) ?>
                    </td>
                    <td><?= Html::textInput('ExportBecheDemerConsignment[quantity_per_unit][]', '', ['class' => 'form-control quantity-per-unit', 'type' => 'number',
                            'min' => '0',
                            'oninput' => 'calculateTotalNumber(this)']) ?></td>
                    <td><?= Html::textInput('ExportBecheDemerConsignment[total_weight][]', '', ['class' => 'form-control  total-weight', 'type' => 'number',
                            'min' => '0',
                            'oninput' => 'calculateTotalNumber(this)']) ?></td>
                    <td><?= Html::textInput('ExportBecheDemerConsignment[total_number][]', '', ['class' => 'form-control total-number', 'type' => 'number']) ?></td>
                    <td><?= Html::textInput('ExportBecheDemerConsignment[supply_area][]', '', ['class' => 'form-control']) ?></td>

                </tr>
                <!--                <tr class="beche-demer-row">-->
                <!--                    <td>--><?php //= Html::hiddenInput('ExportBecheDemerConsignment[id][]', '') ?>
                <!--                        --><?php //= Html::textInput('ExportBecheDemerConsignment[commetial_name][]', 'Pawakka (Stichopus nago)', ['class' => 'form-control','readOnly'=>true]) ?><!--</td>-->
                <!--                    <td>--><?php //= Html::textInput('ExportBecheDemerConsignment[quantity_per_unit][]', '', ['class' => 'form-control quantity-per-unit', 'type' => 'number',
//                            'min' => '0',
//                            'oninput' => 'calculateTotalNumber(this)']) ?><!--</td>-->
                <!--                    <td>--><?php //= Html::textInput('ExportBecheDemerConsignment[total_weight][]', '', ['class' => 'form-control  total-weight', 'type' => 'number',
//                            'min' => '0',
//                            'oninput' => 'calculateTotalNumber(this)']) ?><!--</td>-->
                <!--                    <td>--><?php //= Html::textInput('ExportBecheDemerConsignment[total_number][]', '', ['class' => 'form-control total-number', 'type' => 'number']) ?><!--</td>-->
                <!--                    <td>--><?php //= Html::textInput('ExportBecheDemerConsignment[supply_area][]', '', ['class' => 'form-control']) ?><!--</td>-->
                <!---->
                <!--                </tr>-->
            <?php } ?>
            <?php if ($create == false) {
                foreach ($consignments as $index => $consignment): ?>
                    <tr class="beche-demer-row">
                    <td><?= Html::hiddenInput('ExportBecheDemerConsignment[id][]', $consignment->id) ?>

                        <?= Html::dropDownList(
                            'ExportBecheDemerConsignment[commetial_name][]',        // name
                            $consignment->commetial_name,             // selected value
                            [
                                'Beche-de-mer' => 'Beche-de-mer',
                                'Pawakka (Stichopus nago)' => 'Pawakka (Stichopus nago)',
                            ],
                            [
                                'class' => 'form-control',
                                'prompt' => 'Select type'
                            ]
                        ) ?>
                    </td>

                    <td><?= Html::textInput('ExportBecheDemerConsignment[quantity_per_unit][]',
                            $consignment->quantity_per_unit, ['class' => 'form-control quantity-per-unit', 'type' => 'number', 'step' => '0.01',
                                'min' => '0',
                                'oninput' => 'calculateTotalNumber(this)']) ?></td>
                    <td><?= Html::textInput('ExportBecheDemerConsignment[total_weight][]', $consignment->total_weight,
                            ['class' => 'form-control total-weight', 'type' => 'number', 'step' => '0.01',
                                'min' => '0',
                                'oninput' => 'calculateTotalNumber(this)']) ?></td>
                    <td><?= Html::textInput('ExportBecheDemerConsignment[total_number][]', $consignment->total_number,
                            ['class' => 'form-control total-number', 'type' => 'number']) ?></td>
                    <td><?= Html::textInput('ExportBecheDemerConsignment[supply_area][]', $consignment->supply_area,
                            ['class' => 'form-control']) ?></td>

                    </tr>
                <?php endforeach;
            } ?>
            </tbody>
        </table>
    </div>

    <?= $form->field($model, 'charges')->textInput([
        'maxlength' => true, 
    ]) ?>

    <?= $form->field($model, 'export_countries', [
        'template' => "<div class='row fish-checkbox'><div class='col-lg-12'>{label}</div><div class='chboxlist-fish col-lg-12'><input type='text' id='country-search' class='form-control' placeholder='Search countries...'><div class='checkbox-list'>{input}</div></div></div>\n{hint}\n{error}"
    ])->checkboxList(Constant::$countries, [
        'itemOptions' => ['class' => 'country-checkbox'],
        'item' => function ($index, $label, $name, $checked, $value) {
            return "<div class='checkbox'><label><input type='checkbox' class='country-checkbox' name='$name' value='$value' " . ($checked ? 'checked' : '') . "> $label</label></div>";
        },
        'prompt' => 'Select'
    ]) ?>
    <?= $form->field($model, 'additional_information')->textarea([
        'maxlength' => true

    ]) ?>
    <?php if (UserTypeUtil::hasType(Constant::SPECIAL_LICENCE)) { ?>
        <div style="border: 1px solid darkred; padding: 10px;" class="mb-5">
            <?= $form->field($model, 'file_number')->textInput([
                'maxlength' => true

            ]) ?>
        </div>
    <?php } ?>
    <!-- Document Upload -->
    <!--    --><?php //= $form->field($model, 'document')->fileInput()->label('Upload Supporting Document') ?>

    <!--    <div class="form-group">-->
    <!--        --><?php //= Html::button('Add Export Countries', ['class' => 'btn btn-primary', 'onclick' => 'showExportCountryModal()']) ?>
    <!--    </div>-->

    <div id="export_countries">
        <!-- Dynamic export countries will be added here -->
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Clear'), [
            'class' => 'btn btn-secondary',
            'onclick' => "$('#applicationexportbechedemer-form')[0].reset();"
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

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
                        <input type="text" class="form-control" placeholder="Enter export country" name="Applicationexportbechedemer[export_countries][]">
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
    function showExportCountryModal() {
        $('#exportCountryModal').modal('show');
    }

    function addExportCountryField() {
        const container = document.getElementById('exportCountryFields');
        const input = `<div class="form-group"><input type="text" class="form-control" placeholder="Enter export country" name="Applicationexportbechedemer[export_countries][]"></div>`;
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

