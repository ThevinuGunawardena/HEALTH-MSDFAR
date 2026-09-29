<?php

use backend\config\Constant;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

/** @var object $model */
/** @var array $qties */
/** @var array $statements */
/** @var array|null $officer */
/** @var bool|null $isPdf */
/** @var Closure|null $imageSrc */

$isPdf = (bool) ($isPdf ?? false);

$qties = is_array($qties ?? null)
    ? $qties
    : [];

$statements = is_array($statements ?? null)
    ? $statements
    : [];

$processType = 'ExportLiveFish';

/*
 * Use the approved officer passed by the controller.
 * Older browser actions that do not pass the officer use this fallback.
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
                'Export Live Fish licence image is missing '
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
    '{"type":"ExportLiveFish","id":"'
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

/*
 * Safe dates.
 */
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
            Licence for Export Live Fish
        </h3>

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
                        . 'E'
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
                        (string) ($model->address ?? '')
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
                            $model->business_reg_number
                            ?? ''
                        )
                    ) ?>
                </td>
            </tr>
            </tbody>
        </table>

        <h4>
            Name of the Species of Live Fish and the Quantity
        </h4>

        <?php if (!empty($qties)): ?>
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
                    <th style="border:1px solid #000000; padding:4px;">
                        Species of Fish
                    </th>
                    <th style="border:1px solid #000000; padding:4px;">
                        Quantity
                    </th>
                    <th style="border:1px solid #000000; padding:4px;">
                        Area of Capture
                    </th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($qties as $qty): ?>
                    <tr>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) ($qty->species_fish ?? '')
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) ($qty->qty ?? '')
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) ($qty->area_capture ?? '')
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No quantity records available.</p>
        <?php endif; ?>

        <hr>

        <h4>
            Monthly Statement of Fish, Eggs, Roe or Spawn Exported
            for the Last Six Months
        </h4>

        <?php if (!empty($statements)): ?>
            <table
                class="table table-bordered table-striped"
                style="
                    width:100%;
                    border-collapse:collapse;
                    font-size:9px;
                "
            >
                <thead>
                <tr>
                    <th style="border:1px solid #000000; padding:4px;">
                        Month
                    </th>
                    <th style="border:1px solid #000000; padding:4px;">
                        Species of Fish
                    </th>
                    <th style="border:1px solid #000000; padding:4px;">
                        Locally Collected Quantity Exported
                    </th>
                    <th style="border:1px solid #000000; padding:4px;">
                        Locally Bred Quantity Exported
                    </th>
                    <th style="border:1px solid #000000; padding:4px;">
                        Imported Fish Re-exported
                    </th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($statements as $statement): ?>
                    <tr>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) ($statement->month ?? '')
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) ($statement->fish ?? '')
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) (
                                    $statement->local_collected_qty
                                    ?? ''
                                )
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) (
                                    $statement->local_breed_qty
                                    ?? ''
                                )
                            ) ?>
                        </td>
                        <td style="border:1px solid #000000; padding:4px;">
                            <?= Html::encode(
                                (string) (
                                    $statement->imported_reexported_qty
                                    ?? ''
                                )
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No monthly statement records available.</p>
        <?php endif; ?>

        <p>
            According to your request dated
            <?= Html::encode($approvedDate) ?>,
            this Department has no objection to the export and possession
            of live fish, subject to the following conditions.
        </p>

        <?= $model->tnc ?? '' ?>

        <table
            style="
                width:100%;
                border-collapse:collapse;
                margin-top:20px;
            "
        >
            <tbody>
            <tr>
                <td style="width:35%; vertical-align:bottom; font-size:10px;">
                    <strong>Issue Date:</strong>
                    <?= Html::encode($approvedDate) ?>
                    <br>

                    <?php if ($expiryDate !== ''): ?>
                        <strong>Expiry Date:</strong>
                        <?= Html::encode($expiryDate) ?>
                    <?php endif; ?>
                </td>

                <td style="width:30%; text-align:center; vertical-align:bottom;">
                    <p style="font-size:10px; margin:0;">
                        ......................................................
                        <br>
                        Issuer's signature and stamp
                    </p>

                    <p style="font-size:8px; margin:4px 0 0;">
                        (The licence is valid only with the issuer's
                        signature and stamp.)
                    </p>
                </td>

                <td style="width:35%; text-align:center; vertical-align:bottom;">
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

                    <strong style="font-size:10px;">
                        Director General
                        <br>
                        Department of Fisheries &amp; Aquatic Resources
                    </strong>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>