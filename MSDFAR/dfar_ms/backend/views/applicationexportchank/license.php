<?php

use backend\config\Constant;
use backend\models\MApprovalWorkflow;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

/** @var object $model */
/** @var array $consignments */
/** @var array|null $officer */
/** @var bool|null $isPdf */
/** @var Closure|null $imageSrc */

$isPdf = (bool) ($isPdf ?? false);

$consignments = is_array($consignments ?? null)
    ? $consignments
    : [];

$processType = 'ExportChank';

/*
 * Use an officer passed by the controller.
 * Browser actions that do not pass one use this fallback.
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
 * Resolve images safely.
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
                'Export CHANK licence image is missing '
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
    '{"type":"ExportChank","id":"'
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

$createdDate = $formatDate(
    $model->created ?? null
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

    <div class="page" style=" border: 1px solid black;
        padding: 1cm">
        <table>
            <tr>
                <td style="width: 80%">
                    <div id="license_header">
                        <p style="margin:0; text-align:center;">
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
                        </p>
                    </div>
                </td>
                <td style="width: 20%">
                    <div>
                        <?= $outputCode ?>
                    </div>
                </td>
            </tr>
        </table>

        <h3 style="text-align: center;">Licence for Export of CHANK</h3>
        <p>
            <?= Html::encode(
                (string) ($model->full_name ?? '')
            ) ?>
            of
            <?= Html::encode(
                (string) ($model->address ?? '')
            ) ?>
            is hereby authorized to export the following consignment.
            This licence, unless cancelled earlier, is valid from
            <strong>Issue Date:</strong>
            <?= Html::encode($approvedDate) ?>
            to
            <strong>Expiry Date:</strong>
            <?= Html::encode($expiryDate) ?>.
        </p>

        <h4>Applicant Information</h4>
        <table class="table table-bordered" style="width: 100%;">
            <tr>
                <td style="width: 50%;"><strong>Permit Number:</strong></td>
                <td style="width: 50%;"><?= Html::encode(date('Y') . '/' . (string) ($model->id ?? '') . 'E') ?></td>
            </tr>
            <tr>
                <td style="width: 50%;"><strong>Full Name:</strong></td>
                <td style="width: 50%;"><?= Html::encode((string) ($model->full_name ?? '')) ?></td>
            </tr>
            <tr>
                <td><strong>Address:</strong></td>
                <td><?= Html::encode((string) ($model->address ?? '')) ?></td>
            </tr>
            <!--            <tr>-->
            <!--                <td><strong>Business Registration Number:</strong></td>-->
            <!--                <td>--><?php //= Html::encode($model->business_reg_number) ?><!--</td>-->
            <!--            </tr>-->
        </table>


        <h4>Details of consignment </h4>
        <?php if (!empty($consignments)): ?>
            <table class="table table-bordered ">
                <thead>
                <tr>
                    <th>Commercial name</th>
                    <th>Size (mm)</th>
                    <th>Number of Pieces</th>
                    <th>From where caught</th>

                </tr>
                </thead>
                <tbody>
                <?php foreach ($consignments as $place): ?>
                    <tr>
                        <td><?= Html::encode((string) ($place->commetial_name ?? '')) ?></td>
                        <td><?= Html::encode((string) ($place->size ?? '')) ?></td>
                        <td><?= Html::encode((string) ($place->total_weight ?? '')) ?></td>
                        <td><?= Html::encode((string) ($place->caught_from ?? '')) ?></td>

                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records available.</p>
        <?php endif; ?>

        <p>
            According to your request dated
            <?= Html::encode($createdDate) ?>,
            this Department has no objection to transporting and keeping
            CHANK in possession, subject to the following conditions.
        </p>
        <?= $model->tnc ?>
        <br>
        <br>
        <table style="width: 100%; text-align: center">
            <tbody>
            <tr style="font-size: 12px">
                <td style="font-size: 10px">Date of Issue
                    : <?= Html::encode($approvedDate) ?><br>
                    Valid
                    From
                    : <?= Html::encode($validFromDate) ?>
                    To : <?= Html::encode($expiryDate) ?>
                </td>
                <td>
                    <p style="font-size: 10px">...................................................... <br>Issuer's
                        signature and
                        stamp</p>
                    <p style=" font-size: 8px">(The license is only valid with the issuer's signature and
                        stamp.)</p>

                </td>
                <td style="font-size:10px;">
                    <?php if ($officerSignatureSrc !== ''): ?>
                        <img
                            style="max-width:100px; max-height:50px;"
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