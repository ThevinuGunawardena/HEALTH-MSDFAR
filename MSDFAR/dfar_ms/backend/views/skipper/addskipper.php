<?php

use backend\config\Constant;
use backend\models\ProfileFisherman;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\Skipper $model */
/** @var backend\models\SkipperTranings $skipperTranings */
/** @var array $educationQualification */
/** @var array $districtList */
/** @var array $divisionList */
/** @var array $instituteList */
/** @var array $existingTrainings */
/** @var bool $renew */
/** @var yii\bootstrap4\ActiveForm $form */

$ownerData = [];


    $ownerData = ArrayHelper::map(
    ProfileFisherman::find()
        ->where(['status' => Constant::Active])
        ->orderBy(['fisherman_uid' => SORT_ASC])
        ->all(),
    'id',
    function ($model) {
        return "ID-" . $model->fisherman_uid .
            ", NIC: " . $model->nic;
    }
);


$divisionList = $divisionList ?? [];
$existingTrainings = $existingTrainings ?? [];
$trainingCount = !empty($existingTrainings) ? count($existingTrainings) : 0;

?>

<div class="skipper-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'class' => 'userform'
        ]
    ]); ?>

    <?php if ($renew) { ?>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'skipper_uid',
                [
                    'attribute' => 'fisherman_id',
                    'format' => 'text',
                    'label' => 'Fisherman ID',
                    'value' => function ($model) {
                        return $model->fisherman->fisherman_uid ?? "";
                    }
                ],
                [
                    'attribute' => 'highest_education_qualification',
                    'format' => 'text',
                    'label' => 'Highest Education Qualification',
                    'value' => function ($model) {
                        return $model->highestEducationQualification->description ?? "";
                    }
                ],
                'other_qualifications',
                [
                    'attribute' => 'fisheries_district',
                    'format' => 'text',
                    'label' => 'Fisheries District',
                    'value' => function ($model) {
                        return $model->fisheriesDistrict->name ?? "";
                    }
                ],
                [
                    'attribute' => 'fisheries_division',
                    'format' => 'text',
                    'label' => 'Fisheries Division',
                    'value' => function ($model) {
                        return $model->fisheriesDivision->name ?? "";
                    }
                ],
                [
                    'attribute' => 'status',
                    'format' => 'text',
                    'value' => function ($model) {
                        return Constant::$licenseStatus[$model->status] ?? $model->status;
                    }
                ],
                [
                    'attribute' => 'approval_stage',
                    'format' => 'text',
                    'label' => 'Approval Stage',
                    'value' => function ($model) {
                        return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
                    }
                ],
            ],
        ]) ?>

    <?php } ?>

    <div class="row">

           <div class="col-xl-6">
    <?= !$renew ? $form->field($model, 'fisherman_id')->widget(Select2::classname(), [
        'data' => $ownerData,
        'options' => [
            'placeholder' => 'Search for a fisherman ...',
            'value' => $model->fisherman_id,
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
        'pluginEvents' => [
            "select2:select" => new JsExpression("
                function(e) {
                    var fishermanId = e.params.data.id;

                    if (fishermanId) {
                        window.location.href = '" . Url::to(['/skipper/addskipper']) . "?fisherman_id=' + fishermanId;
                    }
                }
            "),
            "select2:clear" => new JsExpression("
                function(e) {
                    window.location.href = '" . Url::to(['/skipper/addskipper']) . "';
                }
            "),
        ],
    ]) : ""; ?>
        </div>

        <div class="col-xl-6">
            <?= !$renew ? $form->field($model, 'highest_education_qualification')->dropDownList(
                $educationQualification,
                [
                    "prompt" => "Select"
                ]
            ) : "" ?>
        </div>

    </div>

    <div class="row">

        <div class="col-xl-6">
            <?= !$renew ? $form->field($model, 'other_qualifications')->textInput([
                'maxlength' => true
            ]) : "" ?>
        </div>

        <div class="col-xl-6">
            <?= !$renew ? $form->field($model, 'fisheries_district')->dropDownList(
                $districtList,
                [
                    "prompt" => "Select",
                    "onchange" => 'loadDivisionsAjaxSkipper($(this).val(), "' . ($model->fisheries_division ?? '') . '")'
                ]
            ) : "" ?>
        </div>

    </div>

    <div class="row">

        <div class="col-xl-6">
            <?= !$renew ? $form->field($model, 'fisheries_division')->dropDownList(
                $divisionList,
                [
                    "prompt" => "Select"
                ]
            ) : "" ?>
        </div>

        <div class="col-xl-6">
            <?= !$renew ? $form->field($model, 'approved_time')->textInput([
                'type' => 'datetime-local',
                'class' => 'form-control',
                'value' => !empty($model->approved_time)
                    ? date('Y-m-d\TH:i', strtotime($model->approved_time))
                    : '',
            ]) : "" ?>
        </div>

    </div>

    <?php if (!$renew) { ?>

        <hr>

        <h3>Educational Qualifications</h3>

        <div class="row">

            <div class="col-lg-6">
                <div class="form-group">
                    <label for="training-institute">Institute</label>
                    <?= Html::dropDownList(
                        'temp_training_institute',
                        null,
                        $instituteList,
                        [
                            'prompt' => 'Select',
                            'id' => 'training-institute',
                            'class' => 'form-control',
                            'onchange' => 'loadProgramsAjaxSkipper($(this).val(), "")'
                        ]
                    ) ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <label for="skippertranings-program_name">Program Name</label>
                    <?= Html::dropDownList(
                        'temp_program_name',
                        null,
                        [],
                        [
                            'prompt' => 'Select',
                            'id' => 'skippertranings-program_name',
                            'class' => 'form-control'
                        ]
                    ) ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <label for="training-period">Training Period</label>
                    <?= Html::textInput(
                        'temp_training_period',
                        null,
                        [
                            'id' => 'training-period',
                            'class' => 'form-control',
                            'maxlength' => true
                        ]
                    ) ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <label for="date-certified">Date Certified</label>
                    <?= Html::textInput(
                        'temp_date_certified',
                        null,
                        [
                            'type' => 'date',
                            'id' => 'date-certified',
                            'class' => 'form-control'
                        ]
                    ) ?>
                </div>
            </div>

        </div>

        <div class="form-group">
            <button type="button" id="add-skipper-training-row" class="btn btn-primary">
                Add Trainings
            </button>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">Institute</th>
                        <th scope="col">Program Name</th>
                        <th scope="col">Training Period</th>
                        <th scope="col">Date Certified</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>

                    <tbody class="programs">

                    <?php if (!empty($existingTrainings)) { ?>

                        <?php foreach ($existingTrainings as $index => $training) { ?>

                            <?php
                            /*
                             * If your SkipperTranings model has relation names,
                             * replace these with relation values.
                             *
                             * Example:
                             * $instituteName = $training->institute0->name ?? $training->institute;
                             * $programName = $training->programName->name ?? $training->program_name;
                             */
                            $instituteName = $training->institute;
                            $programName = $training->program_name;
                            ?>

                            <tr>
                                <td>
                                    <?= Html::encode($instituteName) ?>
                                    <input type="hidden"
                                           name="TrainingDetails[<?= $index ?>][institute]"
                                           value="<?= Html::encode($training->institute) ?>">
                                </td>

                                <td>
                                    <?= Html::encode($programName) ?>
                                    <input type="hidden"
                                           name="TrainingDetails[<?= $index ?>][program_name]"
                                           value="<?= Html::encode($training->program_name) ?>">
                                </td>

                                <td>
                                    <?= Html::encode($training->training_period) ?>
                                    <input type="hidden"
                                           name="TrainingDetails[<?= $index ?>][training_period]"
                                           value="<?= Html::encode($training->training_period) ?>">
                                </td>

                                <td>
                                    <?= Html::encode($training->date_certified) ?>
                                    <input type="hidden"
                                           name="TrainingDetails[<?= $index ?>][date_certified]"
                                           value="<?= Html::encode($training->date_certified) ?>">
                                </td>

                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-skipper-training-row">
                                        Remove
                                    </button>
                                </td>
                            </tr>

                        <?php } ?>

                    <?php } ?>

                    </tbody>
                </table>

            </div>
        </div>

    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), [
            'class' => 'btn btn-success'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$js = <<<JS
(function () {
    'use strict';

    let skipperTrainingIndex = $trainingCount;

    function addHidden(cell, name, value) {
        $('<input>', {
            type: 'hidden',
            name: name,
            value: value
        }).appendTo(cell);
    }

    $(document)
        .off('click.addSkipperTraining', '#add-skipper-training-row')
        .on(
            'click.addSkipperTraining',
            '#add-skipper-training-row',
            function (event) {
                event.preventDefault();

                const institute = $('#training-institute');
                const program = $('#skippertranings-program_name');
                const period = $('#training-period');
                const certifiedDate = $('#date-certified');

                const instituteId = String(institute.val() || '');
                const programId = String(program.val() || '');
                const periodValue = String(period.val() || '').trim();
                const dateValue = String(certifiedDate.val() || '').trim();

                if (!instituteId || !programId || !periodValue || !dateValue) {
                    window.alert('Please complete all training fields.');
                    return;
                }

                const row = $('<tr>');

                const instituteCell = $('<td>').text(
                    institute.find('option:selected').text()
                );
                addHidden(
                    instituteCell,
                    'TrainingDetails[' + skipperTrainingIndex + '][institute]',
                    instituteId
                );

                const programCell = $('<td>').text(
                    program.find('option:selected').text()
                );
                addHidden(
                    programCell,
                    'TrainingDetails[' + skipperTrainingIndex + '][program_name]',
                    programId
                );

                const periodCell = $('<td>').text(periodValue);
                addHidden(
                    periodCell,
                    'TrainingDetails[' + skipperTrainingIndex + '][training_period]',
                    periodValue
                );

                const dateCell = $('<td>').text(dateValue);
                addHidden(
                    dateCell,
                    'TrainingDetails[' + skipperTrainingIndex + '][date_certified]',
                    dateValue
                );

                const actionCell = $('<td>');
                $('<button>', {
                    type: 'button',
                    class: 'btn btn-danger btn-sm remove-skipper-training-row',
                    text: 'Remove'
                }).appendTo(actionCell);

                row.append(
                    instituteCell,
                    programCell,
                    periodCell,
                    dateCell,
                    actionCell
                );

                $('.programs').append(row);
                skipperTrainingIndex++;

                institute.val('');
                program.html('<option value="">Select</option>');
                period.val('');
                certifiedDate.val('');
            }
        );

    $(document)
        .off('click.removeSkipperTraining', '.remove-skipper-training-row')
        .on(
            'click.removeSkipperTraining',
            '.remove-skipper-training-row',
            function (event) {
                event.preventDefault();
                $(this).closest('tr').remove();
            }
        );
}());
JS;

$this->registerJs($js);

?>