<?php

use backend\models\ProfileFisherman;
use backend\models\Skipper;
use backend\models\SkipperRenew;
use backend\models\SkipperTranings;
use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var Skipper|SkipperRenew $model */
/** @var SkipperTranings $skipperTranings */
/** @var array $educationQualification */
/** @var array $districtList */
/** @var array $instituteList */
/** @var bool $renew */
/** @var ActiveForm $form */

$renew = (bool) ($renew ?? true);

$ownerData = [];

if (!empty($model->fisherman_id)) {
    $ownerData = ArrayHelper::map(
        ProfileFisherman::find()
            ->where([
                'id' => $model->fisherman_id,
            ])
            ->all(),
        'id',
        static function (
            ProfileFisherman $profile
        ): string {
            return 'ID-'
                . (string) $profile->fisherman_uid
                . ', NIC: '
                . (string) $profile->nic;
        }
    );
}

$divisionList = [];

if (!empty($model->fisheries_division)) {
    $divisionName = (string) (
        $model->fisheriesDivision->name
        ?? $model->fisheries_division
    );

    $divisionList[
        (int) $model->fisheries_division
    ] = $divisionName;
}

$currentDivisionJson = Json::htmlEncode(
    (string) (
        $model->fisheries_division
        ?? ''
    )
);
?>

<div class="skipper-renew-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'class' => 'userform',
        ],
    ]); ?>

    <div class="row">

        <div class="col-xl-6">
            <?php if ($renew): ?>

                <div class="form-group">
                    <label>
                        <?= Html::encode(
                            Yii::t(
                                'app',
                                'Skipper ID'
                            )
                        ) ?>
                    </label>

                    <?= Html::textInput(
                        'skipper_reference',
                        (string) (
                            $model->skipper_uid
                            ?? ''
                        ),
                        [
                            'class' => 'form-control',
                            'readonly' => true,
                        ]
                    ) ?>
                </div>

            <?php else: ?>

                <?= $form->field(
                    $model,
                    'fisherman_id'
                )->widget(
                    Select2::class,
                    [
                        'data' => $ownerData,
                        'options' => [
                            'placeholder' =>
                                Yii::t(
                                    'app',
                                    'Search for a fisherman'
                                ),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => Url::to([
                                    '/fisherman/search',
                                ]),
                                'dataType' => 'json',
                                'data' =>
                                    new JsExpression(
                                        'function(params) {
                                            return {
                                                q: params.term
                                            };
                                        }'
                                    ),
                            ],
                            'escapeMarkup' =>
                                new JsExpression(
                                    'function(markup) {
                                        return markup;
                                    }'
                                ),
                            'templateResult' =>
                                new JsExpression(
                                    'function(item) {
                                        return item.text;
                                    }'
                                ),
                            'templateSelection' =>
                                new JsExpression(
                                    'function(item) {
                                        return item.text;
                                    }'
                                ),
                        ],
                    ]
                ) ?>

            <?php endif; ?>
        </div>

        <div class="col-xl-6">
            <?php if ($renew): ?>

                <div class="form-group">
                    <label>
                        <?= Html::encode(
                            Yii::t(
                                'app',
                                'Fisherman ID'
                            )
                        ) ?>
                    </label>

                    <?= Html::textInput(
                        'fisherman_reference',
                        (string) (
                            $model
                                ->fisherman
                                ->fisherman_uid
                            ?? ''
                        ),
                        [
                            'class' => 'form-control',
                            'readonly' => true,
                        ]
                    ) ?>
                </div>

            <?php endif; ?>
        </div>

        <div class="col-xl-6">
            <?= $form->field(
                $model,
                'highest_education_qualification'
            )->dropDownList(
                $educationQualification,
                [
                    'prompt' => Yii::t(
                        'app',
                        'Select'
                    ),
                ]
            ) ?>
        </div>

        <div class="col-xl-6">
            <?= $form->field(
                $model,
                'other_qualifications'
            )->textarea([
                'rows' => 3,
                'maxlength' => true,
            ]) ?>
        </div>

        <div class="col-xl-6">
            <?= $form->field(
                $model,
                'fisheries_district'
            )->dropDownList(
                $districtList,
                [
                    'prompt' => Yii::t(
                        'app',
                        'Select'
                    ),
                    'onchange' =>
                        'loadDivisionsAjaxSkipper('
                        . '$(this).val(), '
                        . $currentDivisionJson
                        . ')',
                ]
            ) ?>
        </div>

        <div class="col-xl-6">
            <?= $form->field(
                $model,
                'fisheries_division'
            )->dropDownList(
                $divisionList,
                [
                    'prompt' => Yii::t(
                        'app',
                        'Select'
                    ),
                ]
            ) ?>
        </div>

    </div>

    <hr>

    <h3>
        <?= Html::encode(
            Yii::t(
                'app',
                'Additional Training'
            )
        ) ?>
    </h3>

    <div class="row">

        <div class="col-lg-6">
            <div class="form-group">
                <label for="renew-training-institute">
                    <?= Html::encode(
                        Yii::t('app', 'Institute')
                    ) ?>
                </label>

                <?= Html::dropDownList(
                    'temp_renew_training_institute',
                    null,
                    $instituteList,
                    [
                        'prompt' => Yii::t(
                            'app',
                            'Select'
                        ),
                        'id' =>
                            'renew-training-institute',
                        'class' => 'form-control',
                        'onchange' =>
                            'loadProgramsAjaxSkipper('
                            . '$(this).val(), "")',
                    ]
                ) ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group">
                <label for="renew-training-program">
                    <?= Html::encode(
                        Yii::t(
                            'app',
                            'Program Name'
                        )
                    ) ?>
                </label>

                <?= Html::dropDownList(
                    'temp_renew_training_program',
                    null,
                    [],
                    [
                        'prompt' => Yii::t(
                            'app',
                            'Select'
                        ),
                        'id' =>
                            'skippertranings-program_name',
                        'class' => 'form-control',
                    ]
                ) ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group">
                <label for="renew-training-period">
                    <?= Html::encode(
                        Yii::t(
                            'app',
                            'Training Period'
                        )
                    ) ?>
                </label>

                <?= Html::textInput(
                    'temp_renew_training_period',
                    null,
                    [
                        'id' =>
                            'renew-training-period',
                        'class' => 'form-control',
                        'maxlength' => true,
                    ]
                ) ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group">
                <label for="renew-date-certified">
                    <?= Html::encode(
                        Yii::t(
                            'app',
                            'Date Certified'
                        )
                    ) ?>
                </label>

                <?= Html::textInput(
                    'temp_renew_date_certified',
                    null,
                    [
                        'type' => 'date',
                        'id' =>
                            'renew-date-certified',
                        'class' => 'form-control',
                    ]
                ) ?>
            </div>
        </div>

    </div>

    <div class="form-group">
        <button
            type="button"
            id="add-renew-training-row"
            class="btn btn-primary"
        >
            <?= Html::encode(
                Yii::t('app', 'Add Training')
            ) ?>
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>
                    <?= Html::encode(
                        Yii::t('app', 'Institute')
                    ) ?>
                </th>
                <th>
                    <?= Html::encode(
                        Yii::t(
                            'app',
                            'Program Name'
                        )
                    ) ?>
                </th>
                <th>
                    <?= Html::encode(
                        Yii::t(
                            'app',
                            'Training Period'
                        )
                    ) ?>
                </th>
                <th>
                    <?= Html::encode(
                        Yii::t(
                            'app',
                            'Date Certified'
                        )
                    ) ?>
                </th>
                <th>
                    <?= Html::encode(
                        Yii::t('app', 'Action')
                    ) ?>
                </th>
            </tr>
            </thead>

            <tbody id="renew-training-rows"></tbody>
        </table>
    </div>

    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('app', 'Save'),
            [
                'class' => 'btn btn-success',
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$selectMessage = Json::htmlEncode(
    Yii::t(
        'app',
        'Please complete all training fields.'
    )
);

$removeText = Json::htmlEncode(
    Yii::t('app', 'Remove')
);

$js = <<<JS
(function () {
    const message = {$selectMessage};
    const removeText = {$removeText};

    $(document)
        .off('click.skipperRenew', '#add-renew-training-row')
        .on(
            'click.skipperRenew',
            '#add-renew-training-row',
            function () {
                const institute =
                    $('#renew-training-institute');
                const program =
                    $('#skippertranings-program_name');
                const period =
                    $('#renew-training-period');
                const date =
                    $('#renew-date-certified');

                const instituteId =
                    String(institute.val() || '');
                const programId =
                    String(program.val() || '');
                const periodValue =
                    String(period.val() || '').trim();
                const dateValue =
                    String(date.val() || '');

                if (
                    instituteId === ''
                    || programId === ''
                    || periodValue === ''
                    || dateValue === ''
                ) {
                    alert(message);
                    return;
                }

                const row = $('<tr>');

                const instituteCell = $('<td>')
                    .text(
                        institute
                            .find('option:selected')
                            .text()
                    )
                    .append(
                        $('<input>', {
                            type: 'hidden',
                            name: 'Tranings[institute][]',
                            value: instituteId,
                        })
                    );

                const programCell = $('<td>')
                    .text(
                        program
                            .find('option:selected')
                            .text()
                    )
                    .append(
                        $('<input>', {
                            type: 'hidden',
                            name: 'Tranings[program_name][]',
                            value: programId,
                        })
                    );

                const periodCell = $('<td>')
                    .text(periodValue)
                    .append(
                        $('<input>', {
                            type: 'hidden',
                            name: 'Tranings[training_period][]',
                            value: periodValue,
                        })
                    );

                const dateCell = $('<td>')
                    .text(dateValue)
                    .append(
                        $('<input>', {
                            type: 'hidden',
                            name: 'Tranings[date_certified][]',
                            value: dateValue,
                        })
                    );

                const removeButton = $('<button>', {
                    type: 'button',
                    class:
                        'btn btn-danger btn-sm '
                        + 'remove-renew-training-row',
                    text: removeText,
                });

                row.append(
                    instituteCell,
                    programCell,
                    periodCell,
                    dateCell,
                    $('<td>').append(removeButton)
                );

                $('#renew-training-rows').append(row);

                institute.val('');
                program
                    .empty()
                    .append(
                        $('<option>', {
                            value: '',
                            text: 'Select',
                        })
                    );
                period.val('');
                date.val('');
            }
        );

    $(document)
        .off(
            'click.skipperRenew',
            '.remove-renew-training-row'
        )
        .on(
            'click.skipperRenew',
            '.remove-renew-training-row',
            function () {
                $(this).closest('tr').remove();
            }
        );
})();
JS;

$this->registerJs($js);
?>
