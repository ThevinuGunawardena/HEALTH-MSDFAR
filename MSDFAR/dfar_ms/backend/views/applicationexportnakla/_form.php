<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\controllers\ExportCompanyController;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportnakla $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationexportnakla-form">

    <?php $form = ActiveForm::begin([
        'id' => 'applicationexportnakla-form',
        'options' => ['enctype' => 'multipart/form-data'], // Enable file upload
        'enableClientValidation' => true,
    ]); ?>

    <?= !UserTypeUtil::hasType(Constant::EXPORT_COMPANY) ? $form->field($model, 'company')->dropDownList
    (ExportCompanyController::getExportCompanyArray(), ["prompt" => "Please select a company"]) : "" ?>


    <!-- Telephone Number -->
    <?= $form->field($model, 'telephone_number')->textInput([
        'type' => 'tel',
        'required' => true
    ])->label('Telephone Number') ?>

    <?= $form->field($model, 'nic_number')->textInput([
        'maxlength' => true,
        'required' => true
    ])->label('NIC Number') ?>


    <?= $form->field($model, 'purchase_place')->textInput([
        'maxlength' => true,
        'required' => true
    ])->label('Purchase Places / Places of Fishing') ?>

    <!-- Previous Permit Exported Quantity in Kilograms -->
    <?= $form->field($model, 'previouspermit_exported_quantity_kg')->textInput([
        'required' => true
    ])->label('Previous Permit Exported Quantity per unit') ?>

    <!-- Export Quantity in Kilograms -->
    <?= $form->field($model, 'export_quantity_kg')->textInput([
        'required' => true
    ])->label('Export Quantity per unit') ?>

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
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Clear'), [
            'class' => 'btn btn-secondary',
            'onclick' => "$('#applicationexportnakla-form')[0].reset();"
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
                        <input type="text" class="form-control" placeholder="Enter export country" name="Applicationexportnakla[export_countries][]">
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
        const input = `<div class="form-group"><input type="text" class="form-control" placeholder="Enter export country" name="Applicationexportnakla[export_countries][]"></div>`;
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