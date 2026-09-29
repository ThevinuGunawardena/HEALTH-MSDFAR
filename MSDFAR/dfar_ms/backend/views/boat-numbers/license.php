<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\models\BoatNumbers;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var object $model */
/** @var array|\yii\db\ActiveRecord|null $officer */
/** @var bool|null $isPdf */
/** @var bool|null $hasOfficerSignature */
/** @var string|null $token */
/** @var string|null $validationToken */

$isPdf = (bool) ($isPdf ?? false);
$hasOfficerSignature = (bool) ($hasOfficerSignature ?? false);

$fishermen = $model->owner0 ?? null;
$boatDesign = $model->boatDesign ?? null;

/*
 * Use the officer passed by the PDF controller. For the browser preview,
 * retrieve the approved officer when it was not supplied.
 */
$officer = $officer
    ?? CommonService::getApprovedOfficer(
        $model->id,
        'BOAT_NUMBER'
    );

if ($officer instanceof \yii\db\ActiveRecord) {
    $officer = $officer->toArray();
}

if (!is_array($officer)) {
    $officer = [];
}

$signatureFilename = basename(
    (string) ($officer['signature'] ?? '')
);

/*
 * PDF mode uses mPDF imageVars registered by the controller.
 * Browser mode uses same-domain protected file URLs.
 */
$nationalLogoSrc = $isPdf
    ? 'var:nationalLogo'
    : '/files/static/national_Logo2.jpg';

$boatLicenseSrc = $isPdf
    ? 'var:boatLicense'
    : '/files/static/boatlicense_1.jpg';

$officerSignatureSrc = $isPdf
    ? 'var:officerSignature'
    : '/files/officer/signature/'
        . rawurlencode($signatureFilename);

$showOfficerSignature =
    $signatureFilename !== ''
    && (!$isPdf || $hasOfficerSignature);

/*
 * The QR code carries the same model-bound encrypted token used by
 * the protected boat-number routes. It does not expose the record ID.
 */
$validationToken = trim(
    (string) (
        $validationToken
        ?? $token
        ?? ''
    )
);

if ($validationToken === '') {
    $validationToken = SecurityHelper::encryptId(
        BoatNumbers::class,
        (int) $model->id
    );
}

$validationUrl = Url::to(
    [
        '/site/license-validation',
        'type' => 'BOAT_NUMBER',
        'token' => $validationToken,
    ],
    true
);

$qrCode = new QrCode($validationUrl);

$output = new Output\Svg();

$outputCode = str_replace(
    '<?xml version="1.0"?>',
    '',
    $output->output(
        $qrCode,
        110,
        'white',
        'black'
    )
);

$boatNumber = (string) ($model->boat_number ?? '');
$yardName = (string) ($boatDesign?->yard0?->name ?? '');
$hullNumber = (
    isset($model->hull_number)
    && (string) $model->hull_number !== ''
    && (string) $model->hull_number !== '0'
)
    ? (string) $model->hull_number
    : '';
$vesselLength = (string) ($boatDesign?->length ?? '');

$ownerName = trim(
    (string) ($fishermen?->preferred_name_for_id ?? '')
    . ' '
    . (string) ($fishermen?->last_name ?? '')
);

$ownerAddress = (string) ($fishermen?->permanent_address ?? '');
$ownerNic = (string) ($fishermen?->nic ?? '');
$additionalConditions = (string) ($model->additional_conditions ?? '');

$approvedDate = '';

if (!empty($model->approved_time)) {
    $approvedTimestamp = strtotime((string) $model->approved_time);

    if ($approvedTimestamp !== false) {
        $approvedDate = date('Y-m-d', $approvedTimestamp);
    }
}

$tableStyle = 'width:100%; border-collapse:collapse;';
$cellStyle = 'border:1px solid #000; padding:4px 6px; vertical-align:top;';
?>

<div class="license-view">
    <div
        class="page"
        style="border:1px solid #000; padding:1cm; color:#000;"
    >
        <table style="<?= Html::encode($tableStyle) ?>">
            <tbody>
            <tr>
                <td
                    style="width:80%; border:0; text-align:center; vertical-align:middle;"
                >
                    <div id="license_header">
                        <p style="margin:0; text-align:center;">
                            <img
                                src="<?= Html::encode($nationalLogoSrc) ?>"
                                style="width:50px;"
                                alt="National Logo"
                            >

                            <img
                                src="<?= Html::encode($boatLicenseSrc) ?>"
                                style="width:400px;"
                                alt="Boat Licence"
                            >
                        </p>
                    </div>
                </td>

                <td
                    style="width:20%; border:0; text-align:right; vertical-align:top;"
                >
                    <?= $outputCode ?>
                </td>
            </tr>
            </tbody>
        </table>

        <p
            style="margin:12px 0; text-align:center; font-size:20px; font-weight:bold;"
        >
            BOAT NUMBER CERTIFICATE
        </p>

        <table style="<?= Html::encode($tableStyle) ?>">
            <tbody>
            <tr>
                <th style="<?= Html::encode($cellStyle) ?> width:35%; text-align:left;">
                    Boat Number
                </th>
                <td style="<?= Html::encode($cellStyle) ?>">
                    <?= Html::encode($boatNumber) ?>
                </td>
            </tr>
            <tr>
                <th style="<?= Html::encode($cellStyle) ?> text-align:left;">
                    Yard Name
                </th>
                <td style="<?= Html::encode($cellStyle) ?>">
                    <?= Html::encode($yardName) ?>
                </td>
            </tr>
            <tr>
                <th style="<?= Html::encode($cellStyle) ?> text-align:left;">
                    Hull Number
                </th>
                <td style="<?= Html::encode($cellStyle) ?>">
                    <?= Html::encode($hullNumber) ?>
                </td>
            </tr>
            <tr>
                <th style="<?= Html::encode($cellStyle) ?> text-align:left;">
                    Vessel Length
                </th>
                <td style="<?= Html::encode($cellStyle) ?>">
                    <?= Html::encode($vesselLength) ?>
                </td>
            </tr>
            </tbody>
        </table>

        <p style="margin:0.5cm 0 4px; font-weight:bold;">
            Owner Details
        </p>

        <table style="<?= Html::encode($tableStyle) ?>">
            <thead>
            <tr>
                <th style="<?= Html::encode($cellStyle) ?> width:33.33%; text-align:left;">
                    Owner Name
                </th>
                <th style="<?= Html::encode($cellStyle) ?> width:33.33%; text-align:left;">
                    Address
                </th>
                <th style="<?= Html::encode($cellStyle) ?> width:33.33%; text-align:left;">
                    NIC No
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="<?= Html::encode($cellStyle) ?>">
                    <?= Html::encode($ownerName) ?>
                </td>
                <td style="<?= Html::encode($cellStyle) ?>">
                    <?= Html::encode($ownerAddress) ?>
                </td>
                <td style="<?= Html::encode($cellStyle) ?>">
                    <?= Html::encode($ownerNic) ?>
                </td>
            </tr>
            </tbody>
        </table>

        <p style="margin:1.5cm 0 4px; font-weight:bold;">
            Conditions
        </p>

        <ul style="margin-top:4px;">
            <li>
                The boat must comply with the specifications approved by the Department.
            </li>
            <li>
                The boat must be built in accordance with the conditions stipulated in
                the Fishing Boats Safety (Design, Construction and Equipment)
                Regulations of 2009.
            </li>
            <li>
                Additionally, the following conditions must be followed.
            </li>
        </ul>

        <p style="margin:1.2cm 0 4px; font-weight:bold;">
            Additional Conditions:
        </p>

        <div style="min-height:180px;">
            <p style="margin:0 0 1cm 1cm;">
                <?= nl2br(Html::encode($additionalConditions)) ?>
            </p>
        </div>

        <table style="<?= Html::encode($tableStyle) ?> page-break-inside:avoid;">
            <tbody>
            <tr>
                <td
                    style="width:34%; border:0; text-align:center; vertical-align:bottom;"
                >
                    <?php if (!empty($officer)): ?>
                        <?php if ($showOfficerSignature): ?>
                            <img
                                src="<?= Html::encode($officerSignatureSrc) ?>"
                                style="max-width:100px; max-height:50px;"
                                alt="Officer Signature"
                            >
                            <br>
                        <?php endif; ?>

                        <span style="font-size:10px;">
                            <?= Html::encode(
                                trim(
                                    (string) ($officer['first_name'] ?? '')
                                    . ' '
                                    . (string) ($officer['last_name'] ?? '')
                                )
                            ) ?>
                        </span>
                        <br>
                    <?php endif; ?>

                    <span style="font-size:12px;">
                        Approved by<br>
                        Director General<br>
                        Department of Fisheries &amp; Aquatic Resources
                    </span>
                </td>

                <td
                    style="width:32%; border:0; text-align:center; vertical-align:bottom;"
                >
                    <?= Html::encode($approvedDate) ?>
                    <br>
                    <span style="font-size:12px;">Date of Issue</span>
                </td>

                <td
                    style="width:34%; border:0; text-align:right; vertical-align:bottom;"
                >
                    <p style="margin:0; text-align:right; font-size:12px;">
                        ...........................................
                        <br>
                        Issuer's signature and stamp
                    </p>
                    <p style="margin:4px 0 0; text-align:right; font-size:8px;">
                        (The certificate is valid only with the issuer's signature and stamp.)
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>