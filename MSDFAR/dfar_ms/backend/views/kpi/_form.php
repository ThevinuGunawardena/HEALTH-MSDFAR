<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\ProfileOfficer;

/* @var $this yii\web\View */
/* @var $model backend\models\Kpi */
/* @var $form yii\widgets\ActiveForm */
/** @var array $divisions Passed down from the controller */

// Dynamic contextual lookup on initial load/update states
$divisionalOfficersList = [];
if (!empty($model->divisionId)) {
    $officers = ProfileOfficer::find()
        ->select([
            "user.id AS user_id", 
            "CONCAT(profile_officer.first_name, ' ', profile_officer.last_name) AS full_name"
        ])
        ->innerJoin('user', 'user.profile_id = profile_officer.id')
        ->where(['profile_officer.kpi_division_id' => $model->divisionId])
        ->orderBy(['user.id' => SORT_ASC]) // Guarantees the first account takes priority
        ->groupBy(['profile_officer.id'])  // Merges the duplicates safely
        ->asArray()
        ->all();

    $divisionalOfficersList = ArrayHelper::map($officers, 'user_id', 'full_name');
}
?>

<div class="kpi-form">

    <?php $form = ActiveForm::begin(['id' => 'kpi-active-form']); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'indicator')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'rational')->textarea(['rows' => 3])->label('Rationale / Remark') ?>

    <?= $form->field($model, 'unit')->dropDownList([
        'Number of Units' => 'Number of Units',
        'Number of Individuals' => 'Number of Individuals',
        'Amount' => 'Amount',
        'Percentage' => 'Percentage',
        'Hours' => 'Hours',
        'Days' => 'Days',
        'Months' => 'Months',
    ]) ?>

    <?= $form->field($model, 'target')->textInput(['type' => 'number', 'step' => 'any']) ?>

    <?= $form->field($model, 'targetType')->dropDownList([
        'One-Time' => 'One-Time',
        'monthly' => 'Monthly',
        'weekly' => 'Weekly',
        'daily' => 'Daily',
    ]) ?>

    <?= $form->field($model, 'targetDate')->textInput(['type' => 'date']) ?>

    <!-- <?= $form->field($model, 'divisionId')->dropDownList(
        ArrayHelper::map(
            \backend\models\MDivision::find()
                ->orderBy(['name' => SORT_ASC]) // Sorts the records by the 'name' column alphabetically (A-Z)
                ->all(), 
            'id', 
            'name'
        ),
        [
            'prompt' => '-- Select Division --',
            'id' => 'division-dropdown',
        ]
    )  ->label('Division')?>

    <?= $form->field($model, 'supervisor')->dropDownList([], [
        'prompt' => 'Choose Supervisor',
        'id' => 'supervisor-dropdown',
        'disabled' => true,
    ]) ?>

    <?= $form->field($model, 'responsibility')->dropDownList([], [
        'prompt' => 'Choose Responsibility',
        'id' => 'responsibility-dropdown',
        'disabled' => true,
    ]) ?> -->

    <?= $form->field($model, 'divisionId')->dropDownList($divisions, [
        'id' => 'kpi-division-dropdown',
        'prompt' => Yii::t('app', 'Select Division/District...')
    ])->label('Division') ?>

    <?= $form->field($model, 'supervisor')->dropDownList($divisionalOfficersList, [
        'id' => 'kpi-supervisor-dropdown',
        'prompt' => Yii::t('app', 'Select Supervising Officer...'),
        'disabled' => empty($model->divisionId)
    ]) ?>

    <?= $form->field($model, 'responsibility')->dropDownList($divisionalOfficersList, [
        'id' => 'kpi-responsible-dropdown',
        'prompt' => Yii::t('app', 'Select Responsible Officer...'),
        'disabled' => empty($model->divisionId)
    ]) ?>
    
    <div class="form-group d-flex justify-content-end">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- <?php
// Dynamic AJAX Script registration to handle structural sub-filtering on choice selection
$getUsersUrl = Url::to(['kpi/get-users-by-division']);
$js = <<<JS
$('#division-dropdown').on('change', function() {
    var divId = $(this).val();
    if (divId) {
        // Make call to controller to pull back valid division records
        $.get('{$getUsersUrl}', { divisionId: divId }, function(data) {
            var supervisorSelect = $('#supervisor-dropdown');
            var responsibilitySelect = $('#responsibility-dropdown');
            
            // Clean out current placeholders
            supervisorSelect.empty().append('<option value="">Choose Supervisor</option>');
            responsibilitySelect.empty().append('<option value="">Choose Responsibility</option>');
            
            if(data.length > 0) {
                $.each(data, function(index, user) {
                    var optionHtml = '<option value="' + user.userEmpNo + '">' + user.empNameWithInitials + '</option>';
                    supervisorSelect.append(optionHtml);
                    responsibilitySelect.append(optionHtml);
                });
                supervisorSelect.prop('disabled', false);
                responsibilitySelect.prop('disabled', false);
            } else {
                supervisorSelect.prop('disabled', true);
                responsibilitySelect.prop('disabled', true);
                alert('No users found inside this division branch.');
            }
        });
    } else {
        $('#supervisor-dropdown').empty().append('<option value="">Choose Supervisor</option>').prop('disabled', true);
        $('#responsibility-dropdown').empty().append('<option value="">Choose Responsibility</option>').prop('disabled', true);
    }
});
JS;
$this->registerJs($js);
?> -->

<?php
// Script to dynamically chain supervisor and responsible officer dropdown selection elements
$js = <<<JS
$('#kpi-division-dropdown').on('change', function() {
    var divisionId = $(this).val();
    var supervisorDropdown = $('#kpi-supervisor-dropdown');
    var responsibleDropdown = $('#kpi-responsible-dropdown');

    if (!divisionId) {
        supervisorDropdown.html('<option value="">Select Supervising Officer...</option>').prop('disabled', true);
        responsibleDropdown.html('<option value="">Select Responsible Officer...</option>').prop('disabled', true);
        return;
    }

    // Call controller route to fetch filtered users matching selected kpi_divison_id
    $.ajax({
        url: 'get-officers-by-division',
        type: 'GET',
        data: { divisionId: divisionId },
        dataType: 'json',
        success: function(data) {
            var options = '<option value="">Select Officer...</option>';
            $.each(data, function(id, name) {
                options += '<option value="' + id + '">' + name + '</option>';
            });

            supervisorDropdown.html(options).prop('disabled', false);
            responsibleDropdown.html(options).prop('disabled', false);
        },
        error: function() {
            console.error('Failed to load dynamic divisional officers list endpoints.');
        }
    });
});
JS;
$this->registerJs($js);
?>