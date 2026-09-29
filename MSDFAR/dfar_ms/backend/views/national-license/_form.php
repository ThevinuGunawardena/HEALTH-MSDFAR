<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\services\CommonService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\NationalLicense $model */
/** @var backend\models\FishermanRegisterdBoatLicense|null $boatRegistration */
/** @var array $divisionGearData */
/** @var array $readOnly */
/** @var array $errors */
/** @var yii\widgets\ActiveForm $form */

$boatRegistration = $boatRegistration ?? null;
$divisionGearData = $divisionGearData ?? [];
$errors = $errors ?? [];

/*
 * Preserve selected values after validation failure.
 */
$selectedGearIds = [];

if (!empty($model->division_gear_types)) {
    $selectedGearIds = array_values(
        array_filter(
            array_map(
                'intval',
                explode(
                    ',',
                    (string) $model->division_gear_types
                )
            )
        )
    );
}
?>

<div class="national-license-form">

    <?php $form = ActiveForm::begin([
        'id' => 'national-license-form',
        'method' => 'post',
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
    ]); ?>

    <?php
    $errorModels = [$model];

    if ($boatRegistration !== null) {
        $errorModels[] = $boatRegistration;
    }
    ?>

    <?= $form->errorSummary(
        $errorModels,
        [
            'class' => 'alert alert-danger',
            'header' => Html::tag(
                'strong',
                Yii::t(
                    'app',
                    'Please correct the following errors:'
                )
            ),
        ]
    ) ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">

            <strong>
                <?= Html::encode(
                    Yii::t(
                        'app',
                        'The application could not be submitted.'
                    )
                ) ?>
            </strong>

            <ul class="mb-0">

                <?php foreach ($errors as $attributeErrors): ?>

                    <?php foreach (
                        (array) $attributeErrors as $error
                    ): ?>

                        <li>
                            <?= Html::encode($error) ?>
                        </li>

                    <?php endforeach; ?>

                <?php endforeach; ?>

            </ul>

        </div>
    <?php endif; ?>

    <?php
    /*
     * This hidden model field satisfies the required
     * division_gear_types validation rule.
     */
    echo Html::activeHiddenInput(
        $model,
        'division_gear_types',
        [
            'id' =>
                'national-license-division-gear-types',
        ]
    );

    $boatType = $boatRegistration
        ?->boatNumber
        ?->boat_type;
    ?>

    <?php if (
        $boatRegistration !== null
        && $boatType !== null
        && (int) $boatType !== 1
    ): ?>

        <div class="row">
            <div class="col-lg-6">

                <?= $form->field(
                    $boatRegistration,
                    'engine_type'
                )->dropDownList(
                    Constant::$engineType,
                    [
                        'prompt' => Yii::t(
                            'app',
                            'Select'
                        ),
                    ]
                ) ?>

                <?= $form->field(
                    $boatRegistration,
                    'engine_serial_number'
                )->textInput([
                    'maxlength' => true,
                ]) ?>

            </div>
        </div>

    <?php endif; ?>

    <hr>

    <?php if (
        UserTypeUtil::hasType(Constant::FI)
    ): ?>

        <?= $form->field(
            $model,
            'landing_site'
        )->dropDownList(
            CommonService::getLandingSitesArray(
                $model->fisheries_district
            ),
            [
                'prompt' => Yii::t(
                    'app',
                    'Select'
                ),
            ]
        ) ?>

    <?php endif; ?>

    <div class="row">

        <div class="col-lg-12">

            <h3 class="card-subtitle mb-2 text-muted">
                <?= Html::encode(
                    Yii::t('app', 'Gear Type(s)')
                ) ?>
            </h3>

            <div
                id="division-gear-error"
                class="alert alert-danger"
                style="display: none;"
            >
                <?= Html::encode(
                    Yii::t(
                        'app',
                        'Please select at least one gear type.'
                    )
                ) ?>
            </div>

        </div>

        <div class="col-lg-12">

            <div class="table-responsive">

                <table class="table">

                    <thead class="table-dark">
                    <tr>

                        <th scope="col">
                            <?= Html::encode(
                                Yii::t(
                                    'app',
                                    'Division Gear Type'
                                )
                            ) ?>
                        </th>

                        <th scope="col">
                            <?= Html::encode(
                                Yii::t(
                                    'app',
                                    'Sub Gear Type'
                                )
                            ) ?>
                        </th>

                        <th scope="col">
                            <?= Html::encode(
                                Yii::t('app', 'Extra')
                            ) ?>
                        </th>

                        <th scope="col">
                            <?= Html::encode(
                                Yii::t(
                                    'app',
                                    'Fishing Time Durations'
                                )
                            ) ?>
                        </th>

                        <th scope="col">
                            <?= Html::encode(
                                Yii::t(
                                    'app',
                                    'Fish Species'
                                )
                            ) ?>
                        </th>

                    </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($divisionGearData)): ?>

                        <?php foreach (
                            $divisionGearData as $item
                        ): ?>

                            <?php
                            $gearId = (int) (
                                $item['id'] ?? 0
                            );

                            $selectedValue =
                                $item['selected'] ?? null;

                            $isSelected =
                                in_array(
                                    $gearId,
                                    $selectedGearIds,
                                    true
                                )
                                || $selectedValue === true
                                || $selectedValue === 1
                                || $selectedValue === '1'
                                || $selectedValue === 'checked'
                                || $selectedValue === 'checked="checked"';
                            ?>

                            <tr>

                                <td>

                                    <div class="custom-control custom-checkbox">

                                        <?= Html::checkbox(
                                            'divisionGears[]',
                                            $isSelected,
                                            [
                                                'value' => $gearId,
                                                'uncheck' => null,
                                                'id' =>
                                                    'division-gear-'
                                                    . $gearId,
                                                'class' =>
                                                    'custom-control-input division-gear-checkbox',
                                            ]
                                        ) ?>

                                        <label
                                            class="custom-control-label"
                                            for="division-gear-<?= $gearId ?>"
                                        >
                                            <?= Html::encode(
                                                $item['name'] ?? ''
                                            ) ?>
                                        </label>

                                    </div>

                                </td>

                                <td>
                                    <?= Html::encode(
                                        $item['subGear'] ?? ''
                                    ) ?>
                                </td>

                                <td>
                                    <?= Html::encode(
                                        $item['extra'] ?? ''
                                    ) ?>
                                </td>

                                <td>
                                    <?= Html::encode(
                                        $item['times'] ?? ''
                                    ) ?>
                                </td>

                                <td>
                                    <?= Html::encode(
                                        $item['fishTypes'] ?? ''
                                    ) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5">

                                <?= Html::encode(
                                    Yii::t(
                                        'app',
                                        'No gear types were found.'
                                    )
                                ) ?>

                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <div class="form-group">

        <?= Html::submitButton(
            Yii::t('app', 'Apply'),
            [
                'class' => 'btn btn-success',
                'id' => 'national-license-apply-button',
                'name' => 'apply-button',
                'type' => 'submit',
            ]
        ) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$script = <<<'JS'
(function () {
    const form = document.getElementById(
        'national-license-form'
    );

    const hiddenField = document.getElementById(
        'national-license-division-gear-types'
    );

    const errorBox = document.getElementById(
        'division-gear-error'
    );

    if (!form || !hiddenField) {
        return;
    }

    function getSelectedGearIds() {
        const selected = [];

        document
            .querySelectorAll(
                '.division-gear-checkbox:checked'
            )
            .forEach(function (checkbox) {
                const value = parseInt(
                    checkbox.value,
                    10
                );

                if (
                    Number.isInteger(value)
                    && value > 0
                ) {
                    selected.push(value);
                }
            });

        return [...new Set(selected)];
    }

    function syncGearField() {
        const selected = getSelectedGearIds();

        hiddenField.value = selected.join(',');

        if (errorBox) {
            errorBox.style.display =
                selected.length > 0
                    ? 'none'
                    : 'block';
        }

        return selected;
    }

    document
        .querySelectorAll(
            '.division-gear-checkbox'
        )
        .forEach(function (checkbox) {
            checkbox.addEventListener(
                'change',
                syncGearField
            );
        });

    form.addEventListener(
        'submit',
        function (event) {
            const selected = syncGearField();

            if (selected.length === 0) {
                event.preventDefault();

                if (errorBox) {
                    errorBox.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        }
    );

    syncGearField();
})();
JS;

$this->registerJs($script);
?>