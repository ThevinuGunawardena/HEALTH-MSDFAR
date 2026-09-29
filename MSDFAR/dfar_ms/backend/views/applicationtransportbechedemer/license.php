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

$processType = 'TransportBechedemer';

/*
 * Use the officer passed by the controller.
 * For older browser actions, load it here when it was not passed.
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
 *
 * A controller may pass its own $imageSrc closure. If not, this
 * fallback handles both browser and PDF rendering.
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
                'Transport Beche-de-mer licence image is '
                . 'missing or unreadable: '
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
    '{"type":"TransportBechedemer","id":"'
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

$approvedDate = '';
$expiryDate = '';
$validFromDate = '';

if (!empty($model->approved_time)) {
    $approvedTimestamp = strtotime(
        (string) $model->approved_time
    );

    if ($approvedTimestamp !== false) {
        $approvedDate = date(
            'Y-m-d',
            $approvedTimestamp
        );
    }
}

if (!empty($model->expire_date)) {
    $expiryTimestamp = strtotime(
        (string) $model->expire_date
    );

    if ($expiryTimestamp !== false) {
        $expiryDate = date(
            'Y-m-d',
            $expiryTimestamp
        );

        if (
            $approvalWorkflow !== null
            && !empty($approvalWorkflow->expired_in)
        ) {
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

        <h3 style="text-align: center;">Licence for Possession, Exhibit for Sale, Selling or Transport Beche-de-mer</h3>
        <br>

        <h4>Applicant Information</h4>
        <table class="table table-bordered">
            <tr>
                <td style="width: 50%;"><strong>Permit Number:</strong></td>
                <td style="width: 50%;"><?= Html::encode(date('Y') . '/' . (string) ($model->id ?? '') . 'T') ?></td>
            </tr>
            <tr>
                <td style="width: 50%;"><strong>Full Name:</strong></td>
                <td style="width: 50%;"><?= Html::encode((string) ($model->full_name ?? '')) ?></td>
            </tr>
            <tr>
                <td><strong>Permanent Address:</strong></td>
                <td><?= Html::encode((string) ($model->permanent_address ?? '')) ?></td>
            </tr>
            <!-- <tr>
            <td><strong>Telephone Number:</strong></td>
            <td><?= Html::encode($model->telephone) ?></td>
        </tr> -->
            <tr>
                <td><strong>National ID Number:</strong></td>
                <td><?= Html::encode((string) ($model->nic_number ?? '')) ?></td>
            </tr>
            <tr>
                <td><strong>Business Registration Number:</strong></td>
                <td><?= Html::encode((string) ($model->business_reg_number ?? '')) ?></td>
            </tr>
        </table>

        <h4>Store Place &amp; Transport Details</h4>
        <?php if (!empty($storePlaces)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Species</th>
                        <th>Total weight (Kg)</th>
                        <th>Purchasing District</th>
                        <th>Intermediate Destination</th>
                        <th>Final Store Place</th>
                        <th>Transport Method</th>
                        <th>Vehicle Number</th>
                        <th>Boat Number</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($storePlaces as $place): ?>
                        <tr>
                            <td><?= Html::encode((string) ($place->species ?? '')) ?></td>
                            <td><?= Html::encode((string) ($place->weight_per_distict ?? '')) ?></td>
                            <td><?= Html::encode((string) ($place->purchasing_district ?? '')) ?></td>
                            <td><?= Html::encode((string) ($place->intermediat_destination ?? '')) ?></td>
                            <td><?= Html::encode((string) ($place->final_store_place ?? '')) ?></td>
                            <td><?= Html::encode((string) ($place->transport_method ?? '')) ?></td>
                            <td><?= Html::encode((string) ($place->vehicle_number ?? '')) ?></td>
                            <td><?= Html::encode((string) ($place->boat_number ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No records available.</p>
        <?php endif; ?>

        <p>This Licence, unless cancelled earlier, shall be valid for a period of 06 months from <strong>Issue
                Date:</strong> <?= Html::encode($approvedDate) ?>
            to <strong>Expiry Date:</strong> <?= Html::encode($expiryDate) ?></p>
        <p>This Permit / Licence shall be subject to the following conditions:</p>
        <?= $model->tnc ?>
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