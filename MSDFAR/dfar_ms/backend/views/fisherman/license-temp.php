<?php

use backend\config\Constant;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

/** @var object $model */
/** @var object|null $renewModel */
/** @var bool|null $isPdf */
/** @var bool|null $hasProfileImage */

$isPdf = (bool) ($isPdf ?? false);
$hasProfileImage = (bool) ($hasProfileImage ?? false);

$renewModel = $renewModel ?? null;

/*
 * Use renewal request details when a renewal request exists.
 * Keep $model for fisherman profile information.
 */
$licenseRequestModel = $renewModel ?? $model;

$requestPrefix = $renewModel !== null
    ? 'FMREQ-RENEW-'
    : 'FMREQ-';

$requestId = $requestPrefix
    . (string) ($licenseRequestModel->id ?? '');

$appliedDateValue = !empty($licenseRequestModel->created)
    ? $licenseRequestModel->created
    : ($model->created ?? null);

$appliedDate = '';

if (!empty($appliedDateValue)) {
    $appliedTimestamp = strtotime((string) $appliedDateValue);

    if ($appliedTimestamp !== false) {
        $appliedDate = date('Y-m-d', $appliedTimestamp);
    }
}

/*
 * PDF mode uses mPDF imageVars registered by the controller.
 * Browser mode uses the same-domain protected file routes.
 */
$headingImageSrc = $isPdf
    ? 'var:dfarHeading'
    : '/files/static/DFAR_heading.jpg';

$profileFilename = basename(
    (string) ($model->profile_image ?? '')
);

$profileImageSrc = $isPdf
    ? 'var:profileImage'
    : '/files/fisherman/'
        . rawurlencode($profileFilename);

$showProfileImage =
    $profileFilename !== ''
    && (!$isPdf || $hasProfileImage);

/*
 * Generate the QR-code validation URL.
 */
$hex = bin2hex(
    '{"type":"fisherman-license","id":"'
    . (string) ($model->id ?? '')
    . '"}'
);

$qrCode = new QrCode(
    rtrim(Constant::$BASEURL, '/')
    . '/site/license-validation?token='
    . $hex
);

$output = new Output\Svg();

$outputCode = str_replace(
    '<?xml version="1.0"?>',
    '',
    $output->output(
        $qrCode,
        200,
        'white',
        'black'
    )
);

$fishermanName = strtoupper(
    (string) ($model->preferred_name_for_id ?? '')
);

$fishermanAddress = strtoupper(
    (string) ($model->permanent_address ?? '')
);

$fishermanNic = strtoupper(
    (string) ($model->nic ?? '')
);
?>

<div class="license-view">
    <div
        class="page"
        style="
            border: 1px solid #000000;
            padding: 1cm;
            color: #000000;
            font-family: sans-serif;
        "
    >
        <p style="margin: 0 0 10px; text-align: center;">
            <img
                src="<?= Html::encode($headingImageSrc) ?>"
                style="width: 100%; max-height: 120px; object-fit: contain;"
                alt="DFAR Heading"
            >
        </p>

        <p
            style="
                margin: 10px 0 18px;
                text-align: center;
                font-size: 20px;
                font-weight: bold;
            "
        >
            Temporary Fisherman Licence
        </p>

        <table
            style="
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 18px;
            "
        >
            <tbody>
            <tr>
                <td
                    style="
                        width: 55%;
                        border: 0;
                        padding: 3px 6px;
                        vertical-align: top;
                    "
                >
                    <?= $outputCode ?>
                </td>

                <td
                    style="
                        width: 45%;
                        border: 0;
                        padding: 3px 6px;
                        text-align: right;
                        vertical-align: top;
                    "
                >
                    <?php if ($showProfileImage): ?>
                        <img
                            src="<?= Html::encode($profileImageSrc) ?>"
                            style="
                                width: 185px;
                                height: 220px;
                                object-fit: cover;
                            "
                            alt="Fisherman Profile"
                        >
                    <?php else: ?>
                        <div
                            style="
                                display: inline-block;
                                width: 185px;
                                height: 220px;
                                border: 1px solid #777777;
                                text-align: center;
                                line-height: 220px;
                                font-size: 10px;
                            "
                        >
                            No profile image
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
            </tbody>
        </table>

        <table
            style="
                width: 100%;
                border-collapse: collapse;
                color: #000000;
            "
        >
            <tbody>
            <tr>
                <th style="border: 1px solid #000000; padding: 5px 6px; text-align: left; width: 35%;">
                    Request ID
                </th>
                <td style="border: 1px solid #000000; padding: 5px 6px;">
                    <?= Html::encode($requestId) ?>
                </td>
            </tr>

            <tr>
                <th style="border: 1px solid #000000; padding: 5px 6px; text-align: left;">
                    Fisherman Name
                </th>
                <td style="border: 1px solid #000000; padding: 5px 6px;">
                    <?= Html::encode($fishermanName) ?>
                </td>
            </tr>

            <tr>
                <th style="border: 1px solid #000000; padding: 5px 6px; text-align: left;">
                    Fisherman Address
                </th>
                <td style="border: 1px solid #000000; padding: 5px 6px;">
                    <?= Html::encode($fishermanAddress) ?>
                </td>
            </tr>

            <tr>
                <th style="border: 1px solid #000000; padding: 5px 6px; text-align: left;">
                    Fisherman NIC
                </th>
                <td style="border: 1px solid #000000; padding: 5px 6px;">
                    <?= Html::encode($fishermanNic) ?>
                </td>
            </tr>

            <tr>
                <th style="border: 1px solid #000000; padding: 5px 6px; text-align: left;">
                    Applied Date
                </th>
                <td style="border: 1px solid #000000; padding: 5px 6px;">
                    <?= Html::encode($appliedDate) ?>
                </td>
            </tr>
            </tbody>
        </table>

        <div style="margin-top: 30px;">
            <p style="margin: 0 0 8px; font-weight: bold;">
                Conditions
            </p>

            <ul style="margin-top: 0;">
                <li>Valid for six months from the applied date.</li>
            </ul>
        </div>

        <div style="height: 180px;"></div>

        <table style="width: 100%; border-collapse: collapse;">
            <tbody>
            <tr>
                <td style="text-align: right; vertical-align: bottom;">
                    <p style="margin: 0; font-size: 12px;">
                        ...........................................
                        <br>
                        Issuer's signature and stamp
                    </p>

                    <p style="margin: 4px 0 0; font-size: 8px;">
                        (The licence is valid only with the issuer's signature and stamp.)
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>