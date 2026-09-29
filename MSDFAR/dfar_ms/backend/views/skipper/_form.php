<?php

use backend\config\Constant;
use backend\models\ProfileFisherman;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\helpers\Json;

/** @var yii\web\View $this */
/** @var backend\models\Skipper|backend\models\SkipperRenew $model */
/** @var backend\models\SkipperTranings $skipperTranings */
/** @var array $educationQualification */
/** @var array $districtList */
/** @var array $instituteList */
/** @var bool $renew */

$renew = (bool) ($renew ?? false);
$educationQualification = $educationQualification ?? [];
$districtList = $districtList ?? [];
$divisionList = $divisionList ?? [];
$instituteList = $instituteList ?? [];

$ownerData = ArrayHelper::map(
    ProfileFisherman::find()
        ->where(['id' => $model->fisherman_id])
        ->all(),
    'id',
    static function (ProfileFisherman $profile): string {
        return 'ID-' . (string) $profile->fisherman_uid
            . ', NIC: ' . (string) $profile->nic;
    }
);

$loadProgramsUrl = Json::htmlEncode(
    Url::to(['/skipper/load-programs'])
);
?>

<div class="skipper-form">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'options' => ['class' => 'userform'],
    ]); ?>

    <?= $form->errorSummary($model, [
        'class' => 'alert alert-danger',
        'header' => '<strong>'
            . Html::encode(
                Yii::t(
                    'app',
                    'Please correct the following errors:'
                )
            )
            . '</strong>',
    ]) ?>

    <?php if ($renew): ?>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'skipper_uid',
                    'format' => 'text',
                ],
                [
                    'attribute' => 'fisherman_id',
                    'format' => 'text',
                    'label' => Yii::t('app', 'Fisherman ID'),
                    'value' => static function ($model): string {
                        return (string) (
                            $model->fisherman->fisherman_uid
                            ?? ''
                        );
                    },
                ],
                [
                    'attribute' =>
                        'highest_education_qualification',
                    'format' => 'text',
                    'label' => Yii::t(
                        'app',
                        'Highest Education Qualification'
                    ),
                    'value' => static function ($model): string {
                        return (string) (
                            $model
                                ->highestEducationQualification
                                ->description
                            ?? ''
                        );
                    },
                ],
                [
                    'attribute' => 'other_qualifications',
                    'format' => 'ntext',
                ],
                [
                    'attribute' => 'fisheries_district',
                    'format' => 'text',
                    'label' => Yii::t(
                        'app',
                        'Fisheries District'
                    ),
                    'value' => static function ($model): string {
                        return (string) (
                            $model->fisheriesDistrict->name
                            ?? ''
                        );
                    },
                ],
                [
                    'attribute' => 'fisheries_division',
                    'format' => 'text',
                    'label' => Yii::t(
                        'app',
                        'Fisheries Division'
                    ),
                    'value' => static function ($model): string {
                        return (string) (
                            $model->fisheriesDivision->name
                            ?? ''
                        );
                    },
                ],
                [
                    'attribute' => 'status',
                    'format' => 'text',
                    'value' => static function ($model): string {
                        return (string) (
                            Constant::$licenseStatus[$model->status]
                            ?? $model->status
                            ?? ''
                        );
                    },
                ],
                [
                    'attribute' => 'approval_stage',
                    'format' => 'text',
                    'label' => Yii::t(
                        'app',
                        'Approval Stage'
                    ),
                    'value' => static function ($model): string {
                        return (string) (
                            Constant::$userTypes[
                                $model->approval_stage
                            ]['name']
                            ?? $model->approval_stage
                            ?? ''
                        );
                    },
                ],
            ],
        ]) ?>

        <div class="alert alert-info">
            <?= Html::encode(
                Yii::t(
                    'app',
                    'Submit this form to create the renewal request.'
                )
            ) ?>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-xl-6">
                <?= $form->field($model, 'fisherman_id')->widget(
                    Select2::class,
                    [
                        'data' => $ownerData,
                        'options' => [
                            'placeholder' => Yii::t(
                                'app',
                                'Search for a fisherman...'
                            ),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression(
                                    "function () { return 'Waiting for results...'; }"
                                ),
                            ],
                            'ajax' => [
                                'url' => Url::to([
                                    '/fisherman/search-global',
                                ]),
                                'dataType' => 'json',
                                'data' => new JsExpression(
                                    'function(params) { return {q: params.term}; }'
                                ),
                            ],
                        ],
                    ]
                ) ?>
            </div>

            <div class="col-xl-6">
                <?= $form->field(
                    $model,
                    'highest_education_qualification'
                )->dropDownList(
                    $educationQualification,
                    ['prompt' => Yii::t('app', 'Select')]
                ) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6">
                <?= $form->field(
                    $model,
                    'other_qualifications'
                )->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-xl-6">
                <?= $form->field(
                    $model,
                    'fisheries_district'
                )->dropDownList(
                    $districtList,
                    [
                        'prompt' => Yii::t('app', 'Select'),
                        'onchange' =>
                            'loadDivisionsAjaxSkipper('
                            . '$(this).val(), '
                            . json_encode(
                                (string) (
                                    $model->fisheries_division
                                    ?? ''
                                )
                            )
                            . ')',
                    ]
                ) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6">
                <?= $form->field(
                    $model,
                    'fisheries_division'
                )->dropDownList(
                    $divisionList,
                    ['prompt' => Yii::t('app', 'Select')]
                ) ?>
            </div>
        </div>

        <hr>

        <h3>
            <?= Html::encode(
                Yii::t(
                    'app',
                    'Training Qualifications'
                )
            ) ?>
        </h3>

        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <?= Html::label(
                        Yii::t('app', 'Institute'),
                        'training-institute'
                    ) ?>

                    <?= Html::dropDownList(
                        'temporary_institute',
                        null,
                        $instituteList,
                        [
                            'id' => 'training-institute',
                            'class' => 'form-control',
                            'prompt' => Yii::t(
                                'app',
                                'Select'
                            ),
                            'onchange' =>
                                'loadProgramsAjaxSkipper('
                                . 'this.value, "");',
                        ]
                    ) ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <?= Html::label(
                        Yii::t('app', 'Program Name'),
                        'training-program'
                    ) ?>

                    <?= Html::dropDownList(
                        'temporary_program',
                        null,
                        [],
                        [
                            'id' => 'training-program',
                            'class' => 'form-control',
                            'prompt' => Yii::t(
                                'app',
                                'Select'
                            ),
                            'disabled' => true,
                        ]
                    ) ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <?= Html::label(
                        Yii::t('app', 'Training Period'),
                        'training-period'
                    ) ?>

                    <?= Html::textInput(
                        'temporary_period',
                        '',
                        [
                            'id' => 'training-period',
                            'class' => 'form-control',
                            'maxlength' => true,
                        ]
                    ) ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <?= Html::label(
                        Yii::t('app', 'Date Certified'),
                        'training-date'
                    ) ?>

                    <?= Html::input(
                        'date',
                        'temporary_date',
                        '',
                        [
                            'id' => 'training-date',
                            'class' => 'form-control',
                        ]
                    ) ?>
                </div>
            </div>
        </div>

        <div class="form-group mt-3">
            <?= Html::button(
                Yii::t('app', 'Add Training'),
                [
                    'type' => 'button',
                    'id' => 'add-training-program',
                    'class' => 'btn btn-primary',
                ]
            ) ?>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">
                        <?= Html::encode(
                            Yii::t('app', 'Institute')
                        ) ?>
                    </th>
                    <th scope="col">
                        <?= Html::encode(
                            Yii::t('app', 'Program Name')
                        ) ?>
                    </th>
                    <th scope="col">
                        <?= Html::encode(
                            Yii::t('app', 'Training Period')
                        ) ?>
                    </th>
                    <th scope="col">
                        <?= Html::encode(
                            Yii::t('app', 'Date Certified')
                        ) ?>
                    </th>
                    <th scope="col">
                        <?= Html::encode(
                            Yii::t('app', 'Action')
                        ) ?>
                    </th>
                </tr>
                </thead>
                <tbody id="training-programs"></tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton(
            Yii::t(
                'app',
                $renew ? 'Submit Renewal' : 'Submit'
            ),
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php

$js = <<<'JS'
(function () {
    'use strict';

    const loadProgramsUrl = __LOAD_PROGRAMS_URL__;

    /*
     * Load training programs for the selected institute.
     *
     * This is global because the institute dropdown calls it
     * through its onchange attribute.
     */
    window.loadProgramsAjaxSkipper = function (
    instituteId,
    selectedProgramId
) {
    const programDropdown = $('#training-program');

    instituteId = String(
        instituteId || ''
    ).trim();

    selectedProgramId = String(
        selectedProgramId || ''
    ).trim();

    console.log(
        'Loading programs for institute:',
        instituteId
    );

    programDropdown
        .prop('disabled', true)
        .empty()
        .append(
            new Option(
                'Loading...',
                ''
            )
        );

    if (!instituteId) {
        programDropdown
            .empty()
            .append(
                new Option(
                    'Select',
                    ''
                )
            )
            .prop('disabled', true);

        return;
    }

    $.ajax({
        url: loadProgramsUrl,
        type: 'GET',
        dataType: 'json',
        cache: false,
        data: {
            institute_id: instituteId
        }
    })
        .done(function (response) {
            console.log(
                'Training program response:',
                response
            );

            programDropdown
                .empty()
                .append(
                    new Option(
                        'Select',
                        ''
                    )
                );

            const programs =
                response.results || [];

            $.each(
                programs,
                function (
                    index,
                    program
                ) {
                    const programId = String(
                        program.id || ''
                    );

                    const programName = String(
                        program.text || ''
                    ).trim();

                    if (
                        programId === ''
                        || programName === ''
                    ) {
                        return;
                    }

                    const option = new Option(
                        programName,
                        programId,
                        false,
                        programId
                            === selectedProgramId
                    );

                    programDropdown.append(
                        option
                    );
                }
            );

            programDropdown.prop(
                'disabled',
                false
            );

            console.log(
                'Loaded program count:',
                programs.length
            );
        })
        .fail(function (xhr) {
            console.error(
                'Program request failed:',
                xhr.status,
                xhr.responseText
            );

            programDropdown
                .empty()
                .append(
                    new Option(
                        'Unable to load programs',
                        ''
                    )
                )
                .prop('disabled', false);
        });
};

    function appendHidden(
        cell,
        name,
        value
    ) {
        $('<input>', {
            type: 'hidden',
            name: name,
            value: value
        }).appendTo(cell);
    }

    $(document)
        .off(
            'click.skipperTraining',
            '#add-training-program'
        )
        .on(
            'click.skipperTraining',
            '#add-training-program',
            function () {
                const institute =
                    $('#training-institute');

                const program =
                    $('#training-program');

                const period =
                    $('#training-period');

                const certifiedDate =
                    $('#training-date');

                const instituteId = String(
                    institute.val() || ''
                );

                const programId = String(
                    program.val() || ''
                );

                const periodValue = String(
                    period.val() || ''
                ).trim();

                const dateValue = String(
                    certifiedDate.val() || ''
                ).trim();

                if (
                    !instituteId
                    || !programId
                    || !periodValue
                    || !dateValue
                ) {
                    window.alert(
                        'Please complete all training fields.'
                    );

                    return;
                }

                const instituteText = String(
                    institute
                        .find('option:selected')
                        .text()
                    || ''
                ).trim();

                const programText = String(
                    program
                        .find('option:selected')
                        .text()
                    || ''
                ).trim();

                if (
                    instituteText === 'Select'
                    || programText === 'Select'
                ) {
                    window.alert(
                        'Please select valid training details.'
                    );

                    return;
                }

                const row = $('<tr>');

                const instituteCell =
                    $('<td>').text(
                        instituteText
                    );

                appendHidden(
                    instituteCell,
                    'Tranings[institute][]',
                    instituteId
                );

                const programCell =
                    $('<td>').text(
                        programText
                    );

                appendHidden(
                    programCell,
                    'Tranings[program_name][]',
                    programId
                );

                const periodCell =
                    $('<td>').text(
                        periodValue
                    );

                appendHidden(
                    periodCell,
                    'Tranings[training_period][]',
                    periodValue
                );

                const dateCell =
                    $('<td>').text(
                        dateValue
                    );

                appendHidden(
                    dateCell,
                    'Tranings[date_certified][]',
                    dateValue
                );

                const actionCell =
                    $('<td>');

                $('<button>', {
                    type: 'button',
                    class:
                        'btn btn-danger btn-sm remove-training-program',
                    text: 'Remove'
                }).appendTo(actionCell);

                row.append(
                    instituteCell,
                    programCell,
                    periodCell,
                    dateCell,
                    actionCell
                );

                $('#training-programs').append(
                    row
                );

                institute.val('');

                program
                    .empty()
                    .append(
                        new Option(
                            'Select',
                            ''
                        )
                    )
                    .prop('disabled', true);

                period.val('');
                certifiedDate.val('');
            }
        );

    $(document)
        .off(
            'click.skipperTrainingRemove',
            '.remove-training-program'
        )
        .on(
            'click.skipperTrainingRemove',
            '.remove-training-program',
            function () {
                $(this)
                    .closest('tr')
                    .remove();
            }
        );
}());
JS;

$js = str_replace(
    '__LOAD_PROGRAMS_URL__',
    $loadProgramsUrl,
    $js
);

$this->registerJs($js);
?>