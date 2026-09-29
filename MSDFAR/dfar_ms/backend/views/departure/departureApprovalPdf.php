<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\models\DepartureRequests;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

/** @var object $row */
/** @var array $crew */

$crew = is_array($crew ?? null) ? $crew : [];

$validationToken = SecurityHelper::encryptId(
    DepartureRequests::class,
    (int) ($row->id ?? 0)
);

$qrCode = new QrCode(
    rtrim(Constant::$BASEURL, '/')
    . '/site/license-validation?type=departure-request&token='
    . rawurlencode($validationToken)
);

$output = new Output\Svg();

$outputCode = str_replace(
    '<?xml version="1.0"?>',
    '',
    $output->output(
        $qrCode,
        105,
        'white',
        'black'
    )
);

$issueDate = !empty($row->action_date)
    ? date('Y-m-d H:i', strtotime($row->action_date))
    : date('d/m/Y H:i:s');

$fishingArea = ((int) ($row->fishing_area ?? 0) === 0)
    ? 'EEZ'
    : 'High Seas';

$vmsStatus = !empty($row->vms) ? 'Yes' : 'No';
$ssbStatus = !empty($row->mcs) ? 'Yes' : 'No';

$tableStyle = 'width:100%; border-collapse:collapse;';
$cellStyle = 'border:1px solid #000000; padding:3px 4px; vertical-align:top;';
$headerCellStyle = $cellStyle . ' font-weight:bold; text-align:left;';

/*
 * Embed PDF images directly from disk.
 * This avoids dependency on mPDF imageVars, public URLs, HTTP/HTTPS,
 * and web-server static-file routing.
 */
$imageToDataUri = static function (string $filePath): ?string {
    if (!is_file($filePath) || !is_readable($filePath)) {
        \Yii::warning(
            'Departure PDF image was not found or is unreadable: ' . $filePath,
            __FILE__
        );

        return null;
    }

    $imageData = file_get_contents($filePath);

    if ($imageData === false || $imageData === '') {
        \Yii::warning(
            'Departure PDF image could not be read: ' . $filePath,
            __FILE__
        );

        return null;
    }

    $mimeType = function_exists('mime_content_type')
        ? mime_content_type($filePath)
        : false;

    if (!is_string($mimeType) || $mimeType === '') {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = $extension === 'png' ? 'image/png' : 'image/jpeg';
    }

    return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
};

$nationalLogoSrc = $imageToDataUri(
    '/var/mountpoint/uploads/static/national_Logo2.jpg'
);

$boatLicenseSrc = $imageToDataUri(
    '/var/mountpoint/uploads/static/boatlicense_1.jpg'
);

$bottlesImageSrc = $imageToDataUri(
    '/var/mountpoint/uploads/static/bottles.png'
);
?>

<div style="
    width:100%;
    font-family:sans-serif;
    font-size:9.5px;
    line-height:1.2;
    color:#000000;
">
    <div style="
        border:1px solid #000000;
        padding:8mm;
    ">

        <table style="<?= $tableStyle ?>">
            <tbody>
            <tr>
                <td style="
                    width:80%;
                    border:0;
                    text-align:center;
                    vertical-align:middle;
                    padding:0;
                ">
                    <?php if ($nationalLogoSrc !== null): ?>
                        <img
                            src="<?= Html::encode($nationalLogoSrc) ?>"
                            style="width:50px;"
                            alt="National Logo"
                        >
                    <?php endif; ?>

                    <?php if (
                        $nationalLogoSrc !== null
                        && $boatLicenseSrc !== null
                    ): ?>
                        <br>
                    <?php endif; ?>

                    <?php if ($boatLicenseSrc !== null): ?>
                        <img
                            src="<?= Html::encode($boatLicenseSrc) ?>"
                            style="width:400px;"
                            alt="Boat License"
                        >
                    <?php endif; ?>
                </td>

                <td style="
                    width:20%;
                    border:0;
                    text-align:right;
                    vertical-align:top;
                    padding:0;
                ">
                    <?= $outputCode ?>
                </td>
            </tr>
            </tbody>
        </table>

        <div style="text-align:center; margin:4px 0 6px;">
            <div style="font-size:15px; font-weight:bold;">
                Boat Departure Form
            </div>
            <div style="
                border-top:1px solid #777777;
                margin-top:5px;
            "></div>
        </div>

        <table style="<?= $tableStyle ?>">
            <tbody>
            <tr>
                <td style="
                    width:72%;
                    border:0;
                    padding:0 5px 0 0;
                    vertical-align:top;
                ">
                    <table style="<?= $tableStyle ?>">
                        <tbody>
                        <tr>
                            <th style="<?= $headerCellStyle ?> width:52%;">
                                (1). Departure Form No
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode((string) ($row->id ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (2). Boat Registration Number
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode(strtoupper((string) ($row->boat_no ?? ''))) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (2).(A). Boat Name
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode((string) ($row->boat_name ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (3). National License No
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode((string) ($row->national_license_no ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (4). High Seas License No
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode((string) ($row->hs_license_no ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (5). Name of the Owner
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode((string) ($row->owner ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (6). Owner's Contact No
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode((string) ($row->contact_no ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (7). Owner's Email
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode((string) ($row->email ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (8). Skipper Name
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode((string) ($row->skipper ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (9). Skipper's National Identity Card No
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode((string) ($row->skipper_nic ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (10). Skipper License Number
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode((string) ($row->skipper_no ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (11). Departure Port
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode((string) ($row->harbor ?? '')) ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="<?= $headerCellStyle ?>">
                                (12). Area of Fishing Operation
                            </th>
                            <td style="<?= $cellStyle ?>">
                                <?= Html::encode($fishingArea) ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>

                <td style="
                    width:28%;
                    border:0;
                    padding:0;
                    text-align:center;
                    vertical-align:middle;
                ">
                    <?php if ($bottlesImageSrc !== null): ?>
                        <img
                            src="<?= Html::encode($bottlesImageSrc) ?>"
                            style="width:175px; height:125px;"
                            alt="Bottles"
                        >
                    <?php endif; ?>
                </td>
            </tr>
            </tbody>
        </table>

        <div style="margin:7px 0 4px; font-weight:bold;">
            (13). Detail of Crew Members:
        </div>

        <table style="<?= $tableStyle ?>">
            <thead>
            <tr>
                <th style="<?= $headerCellStyle ?> width:50%;">Name</th>
                <th style="<?= $headerCellStyle ?> width:50%;">
                    National Identity Card Number
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($crew)): ?>
                <?php foreach ($crew as $member): ?>
                    <tr>
                        <td style="<?= $cellStyle ?>">
                            <?= Html::encode((string) ($member->name ?? '')) ?>
                        </td>
                        <td style="<?= $cellStyle ?>">
                            <?= Html::encode((string) ($member->nic ?? '')) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" style="<?= $cellStyle ?> text-align:center;">
                        No crew members available.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <div style="margin:7px 0 4px; font-weight:bold;">
            Information of Fishing Gears
        </div>

        <table style="<?= $tableStyle ?>">
            <tbody>
            <tr>
                <th style="<?= $headerCellStyle ?> width:52%;">
                    (14). Length of Long Line (m)
                </th>
                <td style="<?= $cellStyle ?>">
                    <?= Html::encode((string) ($row->length_longline ?? '')) ?>
                </td>
            </tr>
            <tr>
                <th style="<?= $headerCellStyle ?>">
                    (15). No. of Hooks in Long Line
                </th>
                <td style="<?= $cellStyle ?>">
                    <?= Html::encode((string) ($row->longline_hooks ?? '')) ?>
                </td>
            </tr>
            <tr>
                <th style="<?= $headerCellStyle ?>">
                    (16). Length of Gill Net (m)
                </th>
                <td style="<?= $cellStyle ?>">
                    <?= Html::encode((string) ($row->length_gillnet ?? '')) ?>
                </td>
            </tr>
            <tr>
                <th style="<?= $headerCellStyle ?>">
                    (17). Mesh Size in Gill Net (inch)
                </th>
                <td style="<?= $cellStyle ?>">
                    <?= Html::encode((string) ($row->mesh_gillnet ?? '')) ?>
                </td>
            </tr>
            <tr>
                <th style="<?= $headerCellStyle ?>">
                    (18). Length of Purse Seine (m)
                </th>
                <td style="<?= $cellStyle ?>">
                    <?= Html::encode((string) ($row->length_ringnet ?? '')) ?>
                </td>
            </tr>
            <tr>
                <th style="<?= $headerCellStyle ?>">
                    (19). Mesh Size in Purse Seine (inch)
                </th>
                <td style="<?= $cellStyle ?>">
                    <?= Html::encode((string) ($row->mesh_ringnet ?? '')) ?>
                </td>
            </tr>
            <tr>
                <th style="<?= $headerCellStyle ?>">
                    (20). Following requirements fulfilled
                </th>
                <td style="<?= $cellStyle ?>">
                    Vessel Registration Book, Relevant Licenses, Fishing Log Book,
                    Radio Call Sign, Life Jackets, Gear Marking, No prohibited fishing
                    gear on boat, Fire extinguishers, Line Cutter and Dehookers,
                    Boat Insurance
                </td>
            </tr>
            <tr>
                <th style="<?= $headerCellStyle ?>">(21). VMS on board</th>
                <td style="<?= $cellStyle ?>"><?= Html::encode($vmsStatus) ?></td>
            </tr>
            <tr>
                <th style="<?= $headerCellStyle ?>">(22). SSB Radio on board</th>
                <td style="<?= $cellStyle ?>"><?= Html::encode($ssbStatus) ?></td>
            </tr>
            <tr>
                <th style="<?= $headerCellStyle ?>">(23). Note</th>
                <td style="<?= $cellStyle ?>">
                    <?= Html::encode((string) ($row->remarks ?? '')) ?>
                </td>
            </tr>
            <tr>
                <th style="<?= $headerCellStyle ?>">
                    (24). Departure Approved By
                </th>
                <td style="<?= $cellStyle ?>">
                    <?= Html::encode((string) ($row->user ?? '')) ?>
                </td>
            </tr>
            </tbody>
        </table>

        <div style="
            border-top:1px solid #777777;
            margin:7px 0;
        "></div>

        <table style="<?= $tableStyle ?> page-break-inside:avoid;">
            <tbody>
            <tr>
                <th style="<?= $headerCellStyle ?> width:50%; height:92px;">
                    (26). Approval of the Harbour Manager
                    <br><br>
                    Name:
                    <br><br><br>
                    Signature: .........................................
                </th>
                <th style="<?= $headerCellStyle ?> width:50%; height:92px;">
                    (27). Approval of the Coast Guard
                    <br><br>
                    Name:
                    <br><br><br>
                    Signature: .........................................
                </th>
            </tr>
            </tbody>
        </table>

        <div style="margin-top:7px;">
            Date of Issue: <?= Html::encode($issueDate) ?>
        </div>

        <div style="margin-top:4px; font-weight:bold;">
            *** Boat should initiate departure within 24 hours from the approval time.
        </div>

        <div style="margin-top:12px; text-align:center;">
            <div style="margin:2px 0;">
                Department of Fisheries and Aquatic Resources - Colombo, Sri Lanka
            </div>
            <div style="margin:2px 0;">
                T.P./Fax No: +94(0) 112446183 / +94(0) 112449170
            </div>
        </div>

    </div>
</div>