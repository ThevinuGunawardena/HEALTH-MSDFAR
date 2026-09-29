<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\controllers\ExportCompanyController;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportlobster $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="applicationexportlobster-form">

    <?php $form = ActiveForm::begin([
        'id' => 'applicationexportlobster-form',
        'options' => ['enctype' => 'multipart/form-data'], // Enable file upload
        'enableClientValidation' => true,
    ]); ?>

    <?= !UserTypeUtil::hasType(Constant::EXPORT_COMPANY) ? $form->field($model, 'company')->dropDownList
    (ExportCompanyController::getExportCompanyArray(), ["prompt" => "Please select a company"]) : "" ?>


    <!-- Telephone Number -->
    <?= $form->field($model, 'telephone_number')->textInput([
        'type' => 'text', 
        'required' => true
    ]) ?>

    <!-- Telephone Number -->
    <?= $form->field($model, 'address')->textInput([
        'type' => 'text',
        'required' => true
    ]) ?>

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

    <div class="row">
        <div class="col-lg-6"><h3>Names and Sizes of Lobster Species</h3></div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" id="dynamicTable">
            <thead>
            <tr>
                <th>Lobster Species</th>
                <th>Whole Weight (kg)</th>
                <th>From where caught</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <tr class="template" style="display:none;">
                <td><?= Html::hiddenInput('ExportLobsterTypes[id][]', '') ?>
                    <?= Html::dropDownList(
                        'ExportLobsterTypes[lobster_species][]',        // name
                        "",             // selected value
                        [
                            'P.homarus ' => ' P.homarus ',
                            'P.longipes' => 'P.longipes',
                            'P.ornatus' => 'P.ornatus',
                            'P.versicollar' => 'P.versicollar',
                            'P.penicillatus' => 'P.penicillatus',
                            'T.orientalis' => 'T.orientalis',
                            'Scyllaridae' => 'Scyllaridae',
                            'T.Antarcticus' => 'T.Antarcticus',
                        ],
                        [
                            'class' => 'form-control',
                            'prompt' => 'Select type'
                        ]
                    ) ?>

                </td>
                <td><?= Html::textInput('ExportLobsterTypes[weight][]', '', ['class' => 'form-control', 'type' => 'number']) ?></td>
                <td><?= Html::textInput('ExportLobsterTypes[caught_from][]', '', ['class' => 'form-control']) ?></td>
                <td>
                    <button type="button" class="btn btn-danger removeRow">Remove</button>
                </td>
            </tr>
            <?php foreach ($consignments as $index => $consignment): ?>
                <tr>
                    <td><?= Html::hiddenInput('ExportLobsterTypes[id][]', $consignment->id) ?>
                        <?= Html::dropDownList(
                            'ExportLobsterTypes[lobster_species][]',        // name
                            $consignment->lobster_species,             // selected value
                            [
                                'P.homarus ' => ' P.homarus ',
                                'P.longipes' => 'P.longipes',
                                'P.ornatus' => 'P.ornatus',
                                'P.versicollar' => 'P.versicollar',
                                'P.penicillatus' => 'P.penicillatus',
                                'T.orientalis' => 'T.orientalis',
                                'Scyllaridae' => 'Scyllaridae',
                                'T.Antarcticus' => 'T.Antarcticus',
                            ],
                            [
                                'class' => 'form-control',
                                'prompt' => 'Select type'
                            ]
                        ) ?>

                    </td>
                    <td><?= Html::textInput('ExportLobsterTypes[weight][]', $consignment->weight,
                            ['class' => 'form-control', 'type' => 'number']) ?></td>
                    <td><?= Html::textInput('ExportLobsterTypes[caught_from][]', $consignment->caught_from,
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
        <button type="button" id="addRow" class="btn btn-primary">Add new</button>
    </div>
    <?php if (UserTypeUtil::hasType(Constant::SPECIAL_LICENCE)) { ?>
        <div style="border: 1px solid darkred; padding: 10px;" class="mb-5">
            <?= $form->field($model, 'file_number')->textInput([
                'maxlength' => true

            ]) ?>
        </div>
    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton(Yii::t('app', 'Clear'), [
            'class' => 'btn btn-secondary',
            'onclick' => "$('#applicationexportlobster-form')[0].reset();"
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
                        <input type="text" class="form-control" placeholder="Enter export country" name="Applicationexportlobster[export_countries][]">
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
        const input = `<div class="form-group"><input type="text" class="form-control" placeholder="Enter export country" name="Applicationexportlobster[export_countries][]"></div>`;
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