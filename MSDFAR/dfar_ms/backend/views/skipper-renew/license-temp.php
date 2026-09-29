<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\models\Skipper;
use backend\models\SkipperRenew;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

/** @var Skipper|SkipperRenew $model */
/** @var bool $pdf */
/** @var array|null $officer */
/** @var \Closure|null $imageSrc */

$isPdf = (bool) ($pdf ?? false);

if (!isset($imageSrc) || !$imageSrc instanceof \Closure) {
    $imageSrc = static function (string $relativePath): string {
        $relativePath = trim(str_replace('\\', '/', $relativePath), '/');

        if (
            $relativePath === ''
            || str_contains($relativePath, '..')
            || str_contains($relativePath, "\0")
        ) {
            return '';
        }

        $encodedPath = implode(
            '/',
            array_map('rawurlencode', explode('/', $relativePath))
        );

        return rtrim(Constant::$FILE_VIEW_PATH, '/')
            . '/'
            . $encodedPath;
    };
}

$isRenewal = $model instanceof SkipperRenew;

$workflowType = $isRenewal
    ? 'SKIPPER_LICENCE_RENEW'
    : 'SKIPPER_LICENCE';

/*
 * Load approved officer details.
 */
$officer = $officer
    ?? CommonService::getApprovedOfficer(
        (int) $model->id,
        $workflowType
    );

/*
 * Fallback for renewals if renewal workflow officer is not found.
 */
if (empty($officer) && $isRenewal) {
    $officer = CommonService::getApprovedOfficer(
        (int) $model->id,
        'SKIPPER_LICENCE'
    );
}

/*
 * Normalize officer into array form.
 */
if ($officer instanceof \yii\db\ActiveRecord) {
    $officer = $officer->toArray();
}

if (!is_array($officer)) {
    $officer = [];
}

/*
 * Resolve image sources safely.
 */
$headingSrc = is_callable($imageSrc)
    ? $imageSrc('static/DFAR_heading.jpg')
    : '';

$profileImageFilename = basename(
    (string) ($model->fisherman->profile_image ?? '')
);

$profileImageSrc = (
    $profileImageFilename !== ''
    && is_callable($imageSrc)
)
    ? $imageSrc('fisherman/' . $profileImageFilename)
    : '';

$fishermanSignatureFilename = basename(
    (string) ($model->fisherman->signature ?? '')
);

$fishermanSignatureSrc = (
    $fishermanSignatureFilename !== ''
    && is_callable($imageSrc)
)
    ? $imageSrc('fisherman/' . $fishermanSignatureFilename)
    : '';

$officerSignatureFilename = basename(
    (string) ($officer['signature'] ?? '')
);

$officerSignatureSrc = (
    $officerSignatureFilename !== ''
    && is_callable($imageSrc)
)
    ? $imageSrc(
        'officer/signature/' . $officerSignatureFilename
    )
    : '';

/*
 * Token model depends on whether this is a renewal.
 */
$tokenModelClass = $isRenewal
    ? SkipperRenew::class
    : Skipper::class;

/*
 * Validation type used by SiteController.
 */
$validationType = $isRenewal
    ? 'skipper-renew'
    : 'skipper';

/*
 * Generate secure token for QR validation.
 */
$validationToken = SecurityHelper::encryptId(
    $tokenModelClass,
    (int) $model->id
);

/*
 * Optional readable reference.
 */
$requestReference = 'SKPR-'
    . strtoupper(
        substr(
            hash('sha256', $validationToken),
            0,
            10
        )
    );

/*
 * Build QR validation URL safely.
 */
$validationUrl =
    rtrim(Constant::$BASEURL, '/')
    . '/site/license-validation?'
    . http_build_query(
        [
            'type' => $validationType,
            'token' => $validationToken,
        ],
        '',
        '&',
        PHP_QUERY_RFC3986
    );

/*
 * Generate QR code.
 */
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

$sourceDate = $model->approval_stage === 'Completed'
    ? $model->approved_time
    : $model->created;

$issuedDate = !empty($sourceDate)
    ? date('Y-m-d', strtotime($sourceDate))
    : '';

$expiryDate = !empty($sourceDate)
    ? date(
        'Y-m-d',
        strtotime(
            $model->approval_stage === 'Completed'
                ? '+1 year -1 day'
                : '+6 months -1 day',
            strtotime($sourceDate)
        )
    )
    : '';
?>

<div class="license-view">
    <div class="page" style="border:1px solid #000; padding:1cm;">
        <?php if ($headingSrc !== ''): ?>
            <p style="margin-top:0;">
                <img
                    src="<?= Html::encode($headingSrc) ?>"
                    style="width:100%;"
                    alt="DFAR heading"
                >
            </p>
        <?php endif; ?>

        <p style="color:#000; text-align:center; font-size:20px; font-weight:bold;">
            <?= Html::encode($model->approval_stage === 'Completed' ? '' : 'Temporary ') ?>
            Skipper License
        </p>

        <table style="width:100%; color:#000; border-collapse:collapse;">
            <tbody>
            <tr>
                <th style="width:50%; border:0; padding:3px 3px 3px 6px; text-align:left; vertical-align:top;">
                    <?= $outputCode ?>
                </th>
                <td style="width:50%; border:0; padding:3px 3px 3px 6px; text-align:right; vertical-align:top;">
                    <?php if ($profileImageSrc !== ''): ?>
                        <img
                            src="<?= Html::encode($profileImageSrc) ?>"
                            style="width:185px; height:220px; object-fit:cover; object-position:center;"
                            alt="Fisherman profile"
                        >
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th style="border:1px solid; padding:3px 3px 3px 6px;">
                    <?= Html::encode($model->approval_stage === 'Completed' ? 'Skipper ID' : 'Request ID') ?>
                </th>
                <td style="border:1px solid; padding:3px 3px 3px 6px;">
                    <?= Html::encode(
                        $model->approval_stage === 'Completed'
                            ? (string) ($model->fisherman->fisherman_uid ?? '')
                        : 'SKPREQ-' . $model->id
                    ) ?>
                </td>
            </tr>

            <tr>
                <th style="border:1px solid; padding:3px 3px 3px 6px;">Skipper Name</th>
                <td style="border:1px solid; padding:3px 3px 3px 6px;">
                    <?= Html::encode(strtoupper((string) ($model->fisherman->preferred_name_for_id ?? ''))) ?>
                </td>
            </tr>

            <tr>
                <th style="border:1px solid; padding:3px 3px 3px 6px;">Skipper Address</th>
                <td style="border:1px solid; padding:3px 3px 3px 6px;">
                    <?= Html::encode(strtoupper((string) ($model->fisherman->permanent_address ?? ''))) ?>
                </td>
            </tr>

            <tr>
                <th style="border:1px solid; padding:3px 3px 3px 6px;">Skipper NIC</th>
                <td style="border:1px solid; padding:3px 3px 3px 6px;">
                    <?= Html::encode(strtoupper((string) ($model->fisherman->nic ?? ''))) ?>
                </td>
            </tr>

            <tr>
                <th style="border:1px solid; padding:3px 3px 3px 6px;">DOB</th>
                <td style="border:1px solid; padding:3px 3px 3px 6px;">
                    <?= !empty($model->fisherman->dob)
                        ? Html::encode(date('Y-m-d', strtotime($model->fisherman->dob)))
                        : '' ?>
                </td>
            </tr>
            </tbody>
        </table>

        <p style="margin-top:2cm; margin-bottom:0;">Conditions</p>
        <ul>
            <li style="text-align:justify;">
                <?= Html::encode(
                    $model->approval_stage === 'Completed'
                        ? 'This license is valid for the period of one year from issuance date unless suspended or cancelled due to illegal actions.'
                        : 'Valid for 06 months from the applied date'
                ) ?>
            </li>
        </ul>

        <table style="width:100%; margin-top:1.5cm;">
            <tbody>
            <tr>
                <td style="width:33.33%; text-align:left; vertical-align:bottom;">
                    <p style="font-size:12px;">
                        <?= Html::encode($issuedDate) ?><br>Issued Date
                    </p>
                </td>

                <td style="width:33.33%; text-align:center; vertical-align:bottom;">
                    <p style="font-size:12px;">
                    <?= Html::encode($expiryDate) ?><br>Expired Date
                    </p>
                </td>

                <td style="width:33.33%; text-align:right; vertical-align:bottom;">
                    <?php if ($fishermanSignatureSrc !== ''): ?>
                        <img
                            src="<?= Html::encode($fishermanSignatureSrc) ?>"
                            style="max-width:100px; max-height:50px; width:auto; height:auto;"
                            alt="Fisherman signature"
                        >
                    <?php endif; ?>
                    <p style="font-size:12px;">Fisherman's Signature</p>
                </td>
            </tr>
            </tbody>
        </table>

        <table style="width:100%; margin-top:2cm;">
            <tbody>
            <tr>
                <td style="width:33.33%;"></td>
                <td style="width:33.33%;"></td>
                <td style="width:33.33%; text-align:right; vertical-align:bottom;">
                    <!-- <?php if ($officerSignatureSrc !== ''): ?>
                        <img
                            src="<?= Html::encode($officerSignatureSrc) ?>"
                            style="max-width:150px; max-height:75px; width:auto; height:auto;"
                            alt="Officer signature"
                        ><br>
                    <?php endif; ?>

                    <?php if (!empty($officer)): ?>
                        <span style="font-size:10px;">
                            <?= Html::encode(trim(
                                (string) ($officer['first_name'] ?? '')
                                . ' '
                                . (string) ($officer['last_name'] ?? '')
                            )) ?>
                        </span><br>
                    <?php endif; ?> -->

                    <p style="text-align:right; font-size:12px;">
                        ...........................................<br>
                        Licensing Officer
                    </p>
                    <p style="text-align:right; font-size:8px;">
                        (The license is only valid with the Licensing Officer's signature and stamp.)
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>