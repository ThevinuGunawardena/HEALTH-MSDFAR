<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\ProfileOfficer;

/** @var yii\web\View $this */
/** @var backend\models\Kpi $model */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var array $officerList */
/** @var array $divisions */

// --- LOAD EXISTING DIVISION USERS FOR READONLY DISPLAY CONTEXT ---
$divisionalOfficersList = [];
if (!empty($model->divisionId)) {
    $officers = ProfileOfficer::find()
        ->select([
            "user.id AS user_id", 
            "CONCAT(profile_officer.first_name, ' ', profile_officer.last_name) AS full_name"
        ])
        ->innerJoin('user', 'user.profile_id = profile_officer.id')
        // 💡 Ensure this column lookup exactly matches your database table name string:
        ->where(['profile_officer.kpi_division_id' => $model->divisionId])
        ->orderBy(['user.id' => SORT_ASC]) 
        ->groupBy(['profile_officer.id'])  
        ->asArray()
        ->all();

    $divisionalOfficersList = ArrayHelper::map($officers, 'user_id', 'full_name');
}
?>

<div class="kpi-form">

    <?php $form = ActiveForm::begin([
        'id' => 'kpi-routing-form',
        'options' => ['class' => 'shadow-sm p-4 bg-white rounded']
    ]); ?>

            <?= $form->field($model, 'title')->textInput(['readonly' => true, 'class' => 'form-control bg-light']) ?>

            <?= $form->field($model, 'indicator')->textInput(['readonly' => true, 'class' => 'form-control bg-light']) ?>

            <?= $form->field($model, 'unit')->dropDownList([
                'Number of Units' => 'Number of Units',
                'Number of Individuals' => 'Number of Individuals',
                'Amount' => 'Amount',
                'Percentage' => 'Percentage',
                'Hours' => 'Hours',
                'Days' => 'Days',
                'Months' => 'Months',
            ], ['disabled' => true, 'class' => 'form-control custom-select bg-light']) ?>

            <?= $form->field($model, 'target')->textInput([
                'id' => 'kpi-target-input', 
                'type' => 'number', 
                'step' => '0.01', 
                'readonly' => true, 
                'class' => 'form-control bg-light'
            ]) ?>

            <?= $form->field($model, 'progress')->textInput([
                'id' => 'kpi-progress-input', 
                'type' => 'number', 
                'step' => '0.01',
                'class' => 'form-control border-warning',
                'placeholder' => 'Enter updated metrics progress...'
            ])->label('Current Progress <span class="text-warning">*</span>') ?>

            <?= $form->field($model, 'targetDate')->textInput(['type' => 'date', 'readonly' => true, 'class' => 'form-control bg-light']) ?>

            <?= $form->field($model, 'divisionId')->dropDownList($divisions, [
                'id' => 'kpi-division-dropdown',
                'disabled' => true,
                'prompt' => Yii::t('app', 'Select Division/District...')
            ])->label('Division') ?>

            <?= $form->field($model, 'supervisor')->dropDownList($divisionalOfficersList, [
                'id' => 'kpi-supervisor-dropdown',
                'disabled' => true,
            ]) ?>

            <?= $form->field($model, 'responsibility')->dropDownList($divisionalOfficersList, [
                'disabled' => true,
                
            ]) ?>

            <?= $form->field($model, 'status')->dropDownList([
                'Pending' => 'Pending',
                'In Progress' => 'In Progress',
                'Achieved' => 'Achieved',
                'Overdue' => 'Overdue',
            ], [
                'id' => 'kpi-status-dropdown',
                
                'prompt' => 'Select Status Updates...'
            ]) ?>

    <div class="form-group text-right mt-3 mb-0">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-light px-4 mr-2']) ?>
        <?= Html::submitButton(Yii::t('app', 'Save Changes'), ['class' => 'btn btn-primary px-5 shadow-sm']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
$('#kpi-progress-input').on('input change', function() {
    var progress = parseFloat($(this).val());
    var target = parseFloat($('#kpi-target-input').val());
    var statusDropdown = $('#kpi-status-dropdown');

    // Return early if progress input field is cleared out or invalid
    if (isNaN(progress)) {
        return;
    }

    if (progress >= target) {
        statusDropdown.val('Achieved');
    } else if (progress > 0) {
        statusDropdown.val('In Progress');
    } else {
        statusDropdown.val('Pending');
    }
});
JS;
$this->registerJs($js);
?>