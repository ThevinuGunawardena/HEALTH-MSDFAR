<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\DepatureBoatPayment $model */
/** @var array|null $previousUnpaidPaymentData */
/** @var bool|null $hasPreviousUnpaidMonths */
/** @var string $token */

$this->title = Yii::t(
    'app',
    'Add Departure Boat Payment'
);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Departure Boats'),
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

/*
 * Input IDs.
 */
$amountId = Html::getInputId(
    $model,
    'amount'
);

$fromMonthId = Html::getInputId(
    $model,
    'from_month'
);

$toDateId = Html::getInputId(
    $model,
    'to_date'
);

$fileUploadId = Html::getInputId(
    $model,
    'fileUpload'
);

$affidavitFileUploadId = Html::getInputId(
    $model,
    'affidavitFileUpload'
);

/*
 * Previous unpaid payment information.
 */
$previousUnpaidPaymentData =
    $previousUnpaidPaymentData ?? [];

$previousUnpaidCount = (int) (
    $previousUnpaidPaymentData['count'] ?? 0
);

$previousUnpaidMonths = (
    isset($previousUnpaidPaymentData['months']) &&
    is_array($previousUnpaidPaymentData['months'])
)
    ? $previousUnpaidPaymentData['months']
    : [];

/*
 * Use either the calculated count or the controller Boolean.
 */
$hasPreviousUnpaidMonths =
    $previousUnpaidCount > 0 ||
    (bool) ($hasPreviousUnpaidMonths ?? false);

/*
 * HTML month inputs require YYYY-MM.
 */
$fromMonthValue = !empty($model->from_month)
    ? substr((string) $model->from_month, 0, 7)
    : '';

$toMonthValue = !empty($model->to_date)
    ? substr((string) $model->to_date, 0, 7)
    : '';
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <div class="col-lg-8">

                <?php $form = ActiveForm::begin([
                    'id' => 'departure-payment-form',

                    'action' => [
                        '/departure-boat/update-payment',
                        'token' => $token,
                    ],

                    'options' => [
                        'enctype' => 'multipart/form-data',
                    ],

                    'enableClientValidation' => true,
                    'enableAjaxValidation' => false,
                ]); ?>

                <?= $form->field(
                    $model,
                    'amount'
                )->textInput([
                    'id' => $amountId,
                    'type' => 'number',
                    'min' => 6000,
                    'step' => 6000,
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => Yii::t(
                        'app',
                        'Enter an amount in multiples of 6,000'
                    ),
                ]) ?>

                <div
                    id="amount-calculation-error"
                    class="text-danger mb-3"
                    style="display: none;"
                ></div>

                <?= $form->field(
                    $model,
                    'from_month'
                )->input('month', [
                    'id' => $fromMonthId,
                    'value' => $fromMonthValue,
                    'required' => true,
                ]) ?>

                <?= $form->field(
                    $model,
                    'to_date'
                )->input('month', [
                    'id' => $toDateId,
                    'value' => $toMonthValue,
                    'readonly' => true,
                    'required' => true,
                ]) ?>

                <?= $form->field(
                    $model,
                    'fileUpload'
                )->fileInput([
                    'id' => $fileUploadId,
                    'accept' => '.jpg,.jpeg,.png,.pdf',
                ]) ?>

                <?php if ($hasPreviousUnpaidMonths): ?>

                    <div class="alert alert-warning mt-3">

                        <strong>
                            <?= Yii::t(
                                'app',
                                'Previous Unpaid Months:'
                            ) ?>
                        </strong>

                        <?= Html::encode(
                            $previousUnpaidCount
                        ) ?>

                        <?= Yii::t('app', 'month(s)') ?>

                        <?php if (!empty($previousUnpaidMonths)): ?>
                            —
                            <?= Html::encode(
                                implode(
                                    ', ',
                                    $previousUnpaidMonths
                                )
                            ) ?>
                        <?php endif; ?>

                    </div>

                <?php endif; ?>

                <div
                    id="affidavit-upload-section"
                    style="<?= $hasPreviousUnpaidMonths
                        ? ''
                        : 'display: none;' ?>"
                >

                    <?= $form->field(
                        $model,
                        'affidavitFileUpload'
                    )->fileInput([
                        'id' => $affidavitFileUploadId,
                        'accept' => '.jpg,.jpeg,.png,.pdf',
                        'required' =>
                            $hasPreviousUnpaidMonths,
                    ]) ?>

                    <div
                        id="affidavit-client-error"
                        class="text-danger mb-2"
                        style="display: none;"
                    ></div>

                    <div class="alert alert-danger">

                        <strong>
                            <?= Yii::t(
                                'app',
                                'Affidavit Required:'
                            ) ?>
                        </strong>

                        <?= Yii::t(
                            'app',
                            'Previous payments are unsettled. Upload an affidavit to proceed with the payment.'
                        ) ?>

                    </div>

                </div>

                <div class="form-group mt-4">

                    <?= Html::submitButton(
                        Yii::t('app', 'Save'),
                        [
                            'id' => 'save-payment-button',
                            'class' => 'btn btn-success',
                        ]
                    ) ?>

                </div>

                <?php ActiveForm::end(); ?>

            </div>

        </div>
    </div>

</div>

<?php

/*
 * Pass PHP values safely to JavaScript.
 */
$amountIdJs = Json::htmlEncode(
    $amountId
);

$fromMonthIdJs = Json::htmlEncode(
    $fromMonthId
);

$toDateIdJs = Json::htmlEncode(
    $toDateId
);

$affidavitFileUploadIdJs = Json::htmlEncode(
    $affidavitFileUploadId
);

$hasPreviousUnpaidMonthsJs =
    $hasPreviousUnpaidMonths
        ? 'true'
        : 'false';

$this->registerJs(<<<JS
(function () {
    'use strict';

    const amountId =
        {$amountIdJs};

    const fromMonthId =
        {$fromMonthIdJs};

    const toDateId =
        {$toDateIdJs};

    const affidavitFileUploadId =
        {$affidavitFileUploadIdJs};

    const hasPreviousUnpaidMonths =
        {$hasPreviousUnpaidMonthsJs};

    const monthlyPaymentAmount = 6000;

    const \$form =
        $('#departure-payment-form');

    const \$amount =
        $('#' + amountId);

    const \$fromMonth =
        $('#' + fromMonthId);

    const \$toDate =
        $('#' + toDateId);

    const \$affidavit =
        $('#' + affidavitFileUploadId);

    const \$affidavitSection =
        $('#affidavit-upload-section');

    const \$amountError =
        $('#amount-calculation-error');

    const \$affidavitError =
        $('#affidavit-client-error');

    const \$saveButton =
        $('#save-payment-button');

    function clearCustomErrors() {
        \$amountError
            .hide()
            .text('');

        \$affidavitError
            .hide()
            .text('');
    }

    function setAffidavitRequired(required) {
        if (required) {
            \$affidavitSection.show();

            \$affidavit
                .prop('disabled', false)
                .prop('required', true);

            return;
        }

        \$affidavitSection.hide();

        \$affidavit
            .prop('required', false)
            .prop('disabled', false);
    }

    function showAmountError(message) {
        \$amountError
            .text(message)
            .show();
    }

    function calculatePaymentPeriod() {
        clearCustomErrors();

        /*
         * Previous unpaid months mean the affidavit
         * must always be displayed and required.
         */
        setAffidavitRequired(
            hasPreviousUnpaidMonths
        );

        const amountValue =
            \$amount.val();

        const fromMonth =
            \$fromMonth.val();

        if (!amountValue || !fromMonth) {
            \$toDate.val('');

            return false;
        }

        const amount =
            Number(amountValue);

        if (
            !Number.isFinite(amount) ||
            amount < monthlyPaymentAmount
        ) {
            \$toDate.val('');

            showAmountError(
                'Amount must be at least 6,000.'
            );

            return false;
        }

        if (
            amount % monthlyPaymentAmount !== 0
        ) {
            \$toDate.val('');

            showAmountError(
                'Amount must be a multiple of 6,000.'
            );

            return false;
        }

        const monthParts =
            fromMonth.split('-');

        if (monthParts.length !== 2) {
            \$toDate.val('');

            showAmountError(
                'Please select a valid From Month.'
            );

            return false;
        }

        const year =
            parseInt(monthParts[0], 10);

        const monthIndex =
            parseInt(monthParts[1], 10) - 1;

        if (
            !Number.isInteger(year) ||
            !Number.isInteger(monthIndex) ||
            monthIndex < 0 ||
            monthIndex > 11
        ) {
            \$toDate.val('');

            showAmountError(
                'Please select a valid From Month.'
            );

            return false;
        }

        const monthCount =
            amount / monthlyPaymentAmount;

        /*
         * From Month is counted as the first month.
         *
         * Example:
         * From: 2026-08
         * Amount: 12,000
         * Until: 2026-09
         */
        const calculatedDate =
            new Date(
                year,
                monthIndex + monthCount - 1,
                1
            );

        const calculatedYear =
            calculatedDate.getFullYear();

        const calculatedMonth =
            String(
                calculatedDate.getMonth() + 1
            ).padStart(2, '0');

        const calculatedToMonth =
            calculatedYear +
            '-' +
            calculatedMonth;

        \$toDate.val(
            calculatedToMonth
        );

        return true;
    }

    \$amount.on(
        'input change',
        calculatePaymentPeriod
    );

    \$fromMonth.on(
        'input change',
        calculatePaymentPeriod
    );

    \$affidavit.on(
        'change',
        function () {
            if (
                this.files &&
                this.files.length > 0
            ) {
                \$affidavitError
                    .hide()
                    .text('');
            }
        }
    );

    \$form.on(
        'beforeSubmit',
        function () {
            clearCustomErrors();

            const paymentPeriodIsValid =
                calculatePaymentPeriod();

            if (!paymentPeriodIsValid) {
                return false;
            }

            if (
                hasPreviousUnpaidMonths &&
                (
                    !\$affidavit[0] ||
                    !\$affidavit[0].files ||
                    \$affidavit[0].files.length === 0
                )
            ) {
                \$affidavitError
                    .text(
                        'Please upload the required affidavit.'
                    )
                    .show();

                \$affidavit.trigger('focus');

                return false;
            }

            \$saveButton
                .prop('disabled', true)
                .text('Saving...');

            return true;
        }
    );

    /*
     * Display the affidavit immediately when
     * previous unpaid months exist.
     */
    setAffidavitRequired(
        hasPreviousUnpaidMonths
    );

    calculatePaymentPeriod();
})();
JS);
?>