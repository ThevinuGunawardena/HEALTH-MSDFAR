<?php

use backend\config\Constant;
use backend\models\MApprovalWorkflow;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

/** @var object $model */
/** @var array $storePlaces */
/** @var array|null $officer */
/** @var bool|null $isPdf */
/** @var Closure|null $imageSrc */

$isPdf = (bool) ($isPdf ?? false);

$storePlaces = is_array($storePlaces ?? null)
    ? $storePlaces
    : [];

$processType = 'TransportChank';

/*
 * Use the officer passed by the controller.
 * Older browser actions that do not pass it use this fallback.
 */
$officer = $officer
    ?? CommonService::getApprovedOfficer(
        $model->id,
        $processType
    );

if ($officer instanceof \yii\db\ActiveRecord) {
    $officer = $officer->toArray();
}

if (!is_array($officer)) {
    $officer = [];
}

/*
 * Resolve image sources safely.
 *
 * Browser:
 *   /files/static/example.jpg
 *
 * PDF:
 *   file:///var/mountpoint/uploads/static/example.jpg
 */
if (!isset($imageSrc) || !$imageSrc instanceof \Closure) {
    $imageSrc = static function (
        string $relativePath
    ) use ($isPdf): string {
        $relativePath = trim(
            str_replace('\\', '/', $relativePath),
            '/'
        );

        if (
            $relativePath === ''
            || strpos($relativePath, '..') !== false
            || strpos($relativePath, "\0") !== false
        ) {
            return '';
        }

        if (!$isPdf) {
            $encodedPath = implode(
                '/',
                array_map(
                    'rawurlencode',
                    explode('/', $relativePath)
                )
            );

            return '/files/' . $encodedPath;
        }

        $uploadRoot = realpath(
            '/var/mountpoint/uploads'
        );

        if ($uploadRoot === false) {
            Yii::error(
                'Upload root was not found: '
                . '/var/mountpoint/uploads',
                __FILE__
            );

            return '';
        }

        $requestedPath = $uploadRoot
            . DIRECTORY_SEPARATOR
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $relativePath
            );

        $realPath = realpath($requestedPath);

        if (
            $realPath === false
            || !is_file($realPath)
            || !is_readable($realPath)
        ) {
            Yii::warning(
                'Transport CHANK licence image is missing '
                . 'or unreadable: '
                . $requestedPath,
                __FILE__
            );

            return '';
        }

        $allowedPrefix = $uploadRoot
            . DIRECTORY_SEPARATOR;

        if (
            $realPath !== $uploadRoot
            && strpos($realPath, $allowedPrefix) !== 0
        ) {
            Yii::warning(
                'Blocked image outside uploads directory: '
                . $realPath,
                __FILE__
            );

            return '';
        }

        return 'file:///'
            . ltrim(
                str_replace('\\', '/', $realPath),
                '/'
            );
    };
}

/*
 * Header images.
 */
$nationalLogoSrc = $imageSrc(
    'static/national_Logo2.jpg'
);

$departmentHeadingSrc = $imageSrc(
    'static/boatlicense_1.jpg'
);

/*
 * Officer signature.
 */
$officerSignatureFilename = basename(
    (string) ($officer['signature'] ?? '')
);

$officerSignatureSrc =
    $officerSignatureFilename !== ''
        ? $imageSrc(
            'officer/signature/'
            . $officerSignatureFilename
        )
        : '';

/*
 * QR code.
 */
$hex = bin2hex(
    '{"type":"TransportChank","id":"'
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
        110,
        'white',
        'black'
    )
);

$approvalWorkflow = MApprovalWorkflow::findOne([
    'type' => $processType,
]);

$formatDate = static function ($value): string {
    if (empty($value)) {
        return '';
    }

    $timestamp = strtotime((string) $value);

    return $timestamp === false
        ? ''
        : date('Y-m-d', $timestamp);
};

$approvedDate = $formatDate(
    $model->approved_time ?? null
);

$expiryDate = $formatDate(
    $model->expire_date ?? null
);

$validFromDate = '';

if (
    $approvalWorkflow !== null
    && !empty($approvalWorkflow->expired_in)
    && !empty($model->expire_date)
) {
    $expiryTimestamp = strtotime(
        (string) $model->expire_date
    );

    if ($expiryTimestamp !== false) {
        $startTimestamp = strtotime(
            '-'
            . (int) $approvalWorkflow->expired_in
            . ' month',
            $expiryTimestamp
        );

        if ($startTimestamp !== false) {
            $startTimestamp = strtotime(
                '+1 day',
                $startTimestamp
            );

            if ($startTimestamp !== false) {
                $validFromDate = date(
                    'Y-m-d',
                    $startTimestamp
                );
            }
        }
    }
}
?>

<div class="license-view">
    <div
        class="page"
        style="
            border:1px solid #000000;
            padding:1cm;
            color:#000000;
        "
    >
        <table style="width:100%; border-collapse:collapse;">
            <tbody>
            <tr>
                <td
                    style="
                        width:80%;
                        border:0;
                        text-align:center;
                        vertical-align:middle;
                    "
                >
                    <?php if ($nationalLogoSrc !== ''): ?>
                        <img
                            style="width:50px;"
                            src="<?= Html::encode(
                                $nationalLogoSrc
                            ) ?>"
                            alt="National Logo"
                        >
                    <?php endif; ?>

                    <?php if ($departmentHeadingSrc !== ''): ?>
                        <img
                            style="width:400px;"
                            src="<?= Html::encode(
                                $departmentHeadingSrc
                            ) ?>"
                            alt="Department Heading"
                        >
                    <?php endif; ?>
                </td>

                <td
                    style="
                        width:20%;
                        border:0;
                        text-align:right;
                        vertical-align:top;
                    "
                >
                    <?= $outputCode ?>
                </td>
            </tr>
            </tbody>
        </table>

        <h3 style="text-align:center;">
            Licence for Transport of CHANK
        </h3>

        <br>

        <h4>Applicant Information</h4>

        <table
            class="table table-bordered"
            style="
                width:100%;
                border-collapse:collapse;
            "
        >
            <tbody>
            <tr>
                <td style="width:50%; border:1px solid #000000; padding:5px;">
                    <strong>Permit Number:</strong>
                </td>
                <td style="width:50%; border:1px solid #000000; padding:5px;">
                    <?= Html::encode(
                        date('Y')
                        . '/'
                        . (string) ($model->id ?? '')
                        . 'T'
                    ) ?>
                </td>
            </tr>

            <tr>
                <td style="border:1px solid #000000; padding:5px;">
                    <strong>Full Name:</strong>
                </td>
                <td style="border:1px solid #000000; padding:5px;">
                    <?= Html::encode(
                        (string) ($model->full_name ?? '')
                    ) ?>
                </td>
            </tr>

            <tr>
                <td style="border:1px solid #000000; padding:5px;">
                    <strong>Permanent Address:</strong>
                </td>
                <td style="border:1px solid #000000; padding:5px;">
                    <?= Html::encode(
                        (string) ($model->permanent_address ?? '')
                    ) ?>
                </td>
            </tr>

            <tr>
                <td style="border:1px solid #000000; padding:5px;">
                    <strong>NIC Number:</strong>
                </td>
                <td style="border:1px solid #000000; padding:5px;">
                    <?= Html::encode(
                        (string) ($model->national_id ?? '')
                    ) ?>
                </td>
            </tr>

            <tr>
                <td style="border:1px solid #000000; padding:5px;">
                    <strong>Business Registration Number:</strong>
                </td>
                <td style="border:1px solid #000000; padding:5px;">
                    <?= Html::encode(
                        (string) (
                            $model->business_registration_no
                            ?? ''
                        )
                    ) ?>
                </td>
            </tr>
            </tbody>
        </table>

        <h4>Store Place &amp; Transport Details</h4>

        <?php if (!empty($storePlaces)): ?>
            <table
                class="table table-bordered table-striped"
                style="
                    width:100%;
                    border-collapse:collapse;
                    font-size:10px;
                "
            >
                <thead>
                <tr>
                    <th style="border:1px solid #000000; padding:4px;">Species</th>
                    <th style="border:1px solid #000000; padding:4px;">Total weight (Pieces)</th>
                    <th style="border:1px solid #000000; padding:4px;">Purchasing District</th>
                    <th style="border:1px solid #000000; padding:4px;">Intermediate Destination</th>
                    <th style="border:1px solid #000000; padding:4px;">Final Store Place</th>
                    <th style="border:1px solid #000000; padding:4px;">Transport Method</th>
                    <th style="border:1px solid #000000; padding:4px;">Vehicle Number</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($storePlaces as $place): ?>
                    <tr>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) ($place->species ?? '')
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) (
                                    $place->weight_per_distict
                                    ?? ''
                                )
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) (
                                    $place->purchasing_district
                                    ?? ''
                                )
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) (
                                    $place->intermediat_destination
                                    ?? ''
                                )
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) (
                                    $place->final_store_place
                                    ?? ''
                                )
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) (
                                    $place->transport_method
                                    ?? ''
                                )
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) (
                                    $place->vehicle_number
                                    ?? ''
                                )
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records available.</p>
        <?php endif; ?>

        <p>
            According to your request dated
            <?= Html::encode($approvedDate) ?>,
            this Department has no objection to the transport of
            CHANK, subject to the following conditions.
        </p>

        <?= $model->tnc ?? '' ?>

        <table
            style="
                width:100%;
                text-align:center;
                border-collapse:collapse;
                margin-top:20px;
            "
        >
            <tbody>
            <tr style="font-size:12px;">
                <td style="font-size:10px; vertical-align:bottom;">
                    Date of Issue:
                    <?= Html::encode($approvedDate) ?>
                    <br>

                    Valid From:
                    <?= Html::encode($validFromDate) ?>

                    To:
                    <?= Html::encode($expiryDate) ?>
                </td>

                <td style="vertical-align:bottom;">
                    <p style="font-size:10px;">
                        ......................................................
                        <br>
                        Issuer's signature and stamp
                    </p>

                    <p style="font-size:8px;">
                        (The licence is valid only with the issuer's
                        signature and stamp.)
                    </p>
                </td>

                <td style="font-size:10px; vertical-align:bottom;">
                    <?php if ($officerSignatureSrc !== ''): ?>
                        <img
                            style="
                                max-width:100px;
                                max-height:50px;
                            "
                            src="<?= Html::encode(
                                $officerSignatureSrc
                            ) ?>"
                            alt="Officer Signature"
                        >
                        <br>
                    <?php endif; ?>

                    <span style="font-size:10px;">
                        <?= Html::encode(
                            (string) ($officer['first_name'] ?? '')
                        ) ?>
                        <?= Html::encode(
                            (string) ($officer['last_name'] ?? '')
                        ) ?>
                    </span>

                    <br><br>
                    Director General/Authorized Officer
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>