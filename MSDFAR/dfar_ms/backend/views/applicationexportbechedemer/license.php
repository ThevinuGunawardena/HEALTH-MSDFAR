<?php

use backend\config\Constant;
use backend\models\MApprovalWorkflow;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\models\Applicationexportbechedemer $model
 * @var backend\models\ExportBecheDemerConsignment[] $consignments
 * @var bool $isPdf
 * @var array $officer
 * @var Closure $imageSrc
 */

$isPdf = (bool) ($isPdf ?? $pdf ?? false);

/*
 * Normalize the approved-officer value.
 * The controller should normally pass this value, but this fallback keeps
 * the view compatible with older actions.
 */
$officer = $officer
    ?? CommonService::getApprovedOfficer(
        $model->id,
        'ExportBedchamber'
    );

if ($officer instanceof \yii\db\ActiveRecord) {
    $officer = $officer->toArray();
}

if (!is_array($officer)) {
    $officer = [];
}

/*
 * Fallback image resolver.
 *
 * Browser:
 * /files/static/example.jpg
 *
 * PDF:
 * file:///var/mountpoint/uploads/static/example.jpg
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

            return rtrim(
                Constant::$FILE_VIEW_PATH,
                '/'
            ) . '/' . $encodedPath;
        }

        $uploadRoot = realpath('/var/mountpoint/uploads');

        if ($uploadRoot === false) {
            Yii::error(
                'Upload root was not found: /var/mountpoint/uploads',
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
                'Export licence image is missing or unreadable: '
                . $requestedPath,
                __FILE__
            );

            return '';
        }

        $allowedPrefix = $uploadRoot
            . DIRECTORY_SEPARATOR;

        if (strpos($realPath, $allowedPrefix) !== 0) {
            Yii::warning(
                'Blocked image outside the uploads directory: '
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
 * Image sources.
 */
$nationalLogoSrc = $imageSrc(
    'static/national_Logo2.jpg'
);

$departmentHeadingSrc = $imageSrc(
    'static/boatlicense_1.jpg'
);

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
    '{"type":"ExportBedchamber","id":"'
    . $model->id
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
 * Workflow and dates.
 */
$approvalWorkflow = MApprovalWorkflow::findOne([
    'type' => 'ExportBedchamber',
]);

$expiredInMonths = (int) (
    $approvalWorkflow->expired_in ?? 0
);

$approvedTimestamp = strtotime(
    (string) ($model->approved_time ?? '')
);

$expireTimestamp = strtotime(
    (string) ($model->expire_date ?? '')
);

$approvedDate = $approvedTimestamp !== false
    ? date('Y-m-d', $approvedTimestamp)
    : '';

$expireDate = $expireTimestamp !== false
    ? date('Y-m-d', $expireTimestamp)
    : '';

$validFromDate = '';

if ($expireTimestamp !== false) {
    $validFromTimestamp = strtotime(
        '-' . $expiredInMonths . ' months +1 day',
        $expireTimestamp
    );

    if ($validFromTimestamp !== false) {
        $validFromDate = date(
            'Y-m-d',
            $validFromTimestamp
        );
    }
}

$permitNumber = date('Y')
    . '/'
    . $model->id
    . 'E';

$consignments = is_array($consignments ?? null)
    ? $consignments
    : [];
?>

<div class="license-view">
    <div
        class="page"
        style="
            border: 1px solid #000000;
            padding: 10mm;
            font-family: sans-serif;
            font-size: 11px;
            color: #000000;
        "
    >
        <table
            style="
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 8px;
            "
        >
            <tr>
                <td
                    style="
                        width: 80%;
                        border: 0;
                        text-align: center;
                        vertical-align: middle;
                    "
                >
                    <?php if ($nationalLogoSrc !== ''): ?>
                        <img
                            src="<?= Html::encode(
                                $nationalLogoSrc
                            ) ?>"
                            style="
                                width: 50px;
                                height: auto;
                                vertical-align: middle;
                            "
                            alt="National Logo"
                        >
                    <?php endif; ?>

                    <?php if ($departmentHeadingSrc !== ''): ?>
                        <img
                            src="<?= Html::encode(
                                $departmentHeadingSrc
                            ) ?>"
                            style="
                                width: 400px;
                                height: auto;
                                vertical-align: middle;
                            "
                            alt="Department Heading"
                        >
                    <?php endif; ?>
                </td>

                <td
                    style="
                        width: 20%;
                        border: 0;
                        text-align: right;
                        vertical-align: middle;
                    "
                >
                    <?= $outputCode ?>
                </td>
            </tr>
        </table>

        <h3
            style="
                text-align: center;
                margin: 8px 0 14px;
            "
        >
            Licence for Export of Beche-de-mer
        </h3>

        <p
            style="
                text-align: justify;
                line-height: 1.5;
                margin: 0 0 12px;
            "
        >
            <strong>
                <?= Html::encode(
                    (string) ($model->full_name ?? '')
                ) ?>
            </strong>
            of
            <strong>
                <?= Html::encode(
                    (string) ($model->address ?? '')
                ) ?>
            </strong>
            is hereby authorized to export the following
            consignment. This licence, unless previously
            cancelled, shall be valid from the issue date
            <strong><?= Html::encode($approvedDate) ?></strong>
            to
            <strong><?= Html::encode($expireDate) ?></strong>.
        </p>

        <h4 style="margin: 10px 0 5px;">
            Applicant Information
        </h4>

        <table
            style="
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            "
        >
            <tbody>
            <tr>
                <td
                    style="
                        width: 50%;
                        border: 1px solid #000000;
                        padding: 5px;
                    "
                >
                    <strong>Permit Number:</strong>
                </td>
                <td
                    style="
                        width: 50%;
                        border: 1px solid #000000;
                        padding: 5px;
                    "
                >
                    <?= Html::encode($permitNumber) ?>
                </td>
            </tr>

            <tr>
                <td
                    style="
                        border: 1px solid #000000;
                        padding: 5px;
                    "
                >
                    <strong>Full Name:</strong>
                </td>
                <td
                    style="
                        border: 1px solid #000000;
                        padding: 5px;
                    "
                >
                    <?= Html::encode(
                        (string) ($model->full_name ?? '')
                    ) ?>
                </td>
            </tr>

            <tr>
                <td
                    style="
                        border: 1px solid #000000;
                        padding: 5px;
                    "
                >
                    <strong>Address:</strong>
                </td>
                <td
                    style="
                        border: 1px solid #000000;
                        padding: 5px;
                    "
                >
                    <?= Html::encode(
                        (string) ($model->address ?? '')
                    ) ?>
                </td>
            </tr>

            <tr>
                <td
                    style="
                        border: 1px solid #000000;
                        padding: 5px;
                    "
                >
                    <strong>
                        Business Registration Number:
                    </strong>
                </td>
                <td
                    style="
                        border: 1px solid #000000;
                        padding: 5px;
                    "
                >
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

        <h4 style="margin: 10px 0 5px;">
            Details of Consignment
        </h4>

        <?php if (!empty($consignments)): ?>
            <table
                style="
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 10px;
                "
            >
                <thead>
                <tr>
                    <th
                        style="
                            width: 34%;
                            border: 1px solid #000000;
                            padding: 5px;
                            text-align: left;
                        "
                    >
                        Commercial Name
                    </th>
                    <th
                        style="
                            width: 20%;
                            border: 1px solid #000000;
                            padding: 5px;
                            text-align: left;
                        "
                    >
                        Total Weight (Kg)
                    </th>
                    <th
                        style="
                            width: 46%;
                            border: 1px solid #000000;
                            padding: 5px;
                            text-align: left;
                        "
                    >
                        Total Weight in Words (Kg)
                    </th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($consignments as $place): ?>
                    <?php
                    $totalWeight = $place->total_weight ?? '';

                    $weightInWords = $totalWeight !== ''
                        ? ucwords(
                            CommonService::numberToWords(
                                $totalWeight
                            )
                        ) . ' Only'
                        : '';
                    ?>
                    <tr>
                        <td
                            style="
                                border: 1px solid #000000;
                                padding: 5px;
                            "
                        >
                            <?= Html::encode(
                                (string) (
                                    $place->commetial_name
                                    ?? ''
                                )
                            ) ?>
                        </td>

                        <td
                            style="
                                border: 1px solid #000000;
                                padding: 5px;
                            "
                        >
                            <?= Html::encode(
                                (string) $totalWeight
                            ) ?>
                        </td>

                        <td
                            style="
                                border: 1px solid #000000;
                                padding: 5px;
                            "
                        >
                            <?= Html::encode($weightInWords) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No consignment records are available.</p>
        <?php endif; ?>

        <p
            style="
                font-size: 12px;
                margin: 12px 0 5px;
            "
        >
            This licence is subject to the following terms
            and conditions.
        </p>

        <div
            style="
                font-size: 10px;
                line-height: 1.4;
                margin-bottom: 16px;
            "
        >
            <?= (string) ($model->tnc ?? '') ?>
        </div>

        <table
            style="
                width: 100%;
                border-collapse: collapse;
                text-align: center;
                margin-top: 12px;
            "
        >
            <tbody>
            <tr>
                <td
                    style="
                        width: 33.33%;
                        font-size: 10px;
                        vertical-align: bottom;
                        text-align: left;
                    "
                >
                    Date of Issue:
                    <?= Html::encode($approvedDate) ?>
                    <br>

                    Valid From:
                    <?= Html::encode($validFromDate) ?>
                    <br>

                    Valid To:
                    <?= Html::encode($expireDate) ?>
                </td>

                <td
                    style="
                        width: 33.33%;
                        font-size: 10px;
                        vertical-align: bottom;
                        text-align: center;
                    "
                >
                    ......................................................
                    <br>
                    Issuer's signature and stamp

                    <p
                        style="
                            font-size: 8px;
                            margin: 5px 0 0;
                        "
                    >
                        The licence is valid only with the
                        issuer's signature and stamp.
                    </p>
                </td>

                <td
                    style="
                        width: 33.33%;
                        font-size: 10px;
                        vertical-align: bottom;
                        text-align: center;
                    "
                >
                    <?php if ($officerSignatureSrc !== ''): ?>
                        <img
                            src="<?= Html::encode(
                                $officerSignatureSrc
                            ) ?>"
                            style="
                                max-width: 100px;
                                max-height: 50px;
                                width: auto;
                                height: auto;
                            "
                            alt="Authorized Officer Signature"
                        >
                        <br>
                    <?php endif; ?>

                    <span style="font-size: 10px;">
                        <?= Html::encode(
                            trim(
                                (string) (
                                    $officer['first_name']
                                    ?? ''
                                )
                                . ' '
                                . (string) (
                                    $officer['last_name']
                                    ?? ''
                                )
                            )
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