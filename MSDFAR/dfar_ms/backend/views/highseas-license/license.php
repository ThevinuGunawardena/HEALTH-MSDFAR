<?php

use backend\config\Constant;
use backend\models\DistrictGearTypes;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\HighseasLicense;
use backend\models\MApprovalWorkflow;
use backend\models\MFishTypes;
use backend\models\MHarbours;
use backend\models\MLandingSite;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;

/** @var HighseasLicense $model */
/** @var array|null $officer */
/** @var bool|null $isPdf */
/** @var bool|null $hasOfficerSignature */

$boatNumber = $model->boatRegistration->boatNumber;

$boatDesign = $model->boatRegistration
    ->boatNumber
    ->boatDesign;

/*
 * Get the latest boat registration record.
 */
$boatRegistration = FishermanRegisterdBoatLicense::find()
    ->where([
        'id' => $model->boatRegistration->id,
    ])
    ->orderBy([
        'nid' => SORT_DESC,
    ])
    ->one();

$fishermen = $model->fisherman;

/*
 * Unloading sites.
 */
$landing = $model->unloading_sites
    ? MLandingSite::find()
        ->select('name')
        ->where([
            'IN',
            'id',
            $model->unloading_sites,
        ])
        ->asArray()
        ->all()
    : [];

/*
 * Landing harbour.
 */
$landingHarbour = $model->landing_harbour
    ? MHarbours::find()
        ->select('Name')
        ->where([
            'Id' => $model->landing_harbour,
        ])
        ->asArray()
        ->one()
    : [];

/*
 * Fish species.
 */
$fishTypes = (
    $model->fishingGearType
    && $model->fishingGearType->fish_species
)
    ? MFishTypes::find()
        ->select('name')
        ->where([
            'IN',
            'id',
            explode(
                ',',
                $model->fishingGearType->fish_species
            ),
        ])
        ->asArray()
        ->all()
    : [];

/*
 * Select the correct workflow for new and renewed licences.
 */
$workflowType = (int) $model->renew === 1
    ? 'HIGHSEAS_LICENSE_RENEW'
    : 'HIGHSEAS_LICENSE';

/*
 * Use the officer passed by the PDF controller.
 *
 * For the normal browser preview, retrieve the officer here when
 * the controller has not passed one.
 */
$officer = $officer
    ?? CommonService::getApprovedOfficer(
        $model->id,
        $workflowType
    );

/*
 * Renewal fallback.
 */
if (
    empty($officer)
    && $workflowType === 'HIGHSEAS_LICENSE_RENEW'
) {
    $officer = CommonService::getApprovedOfficer(
        $model->id,
        'HIGHSEAS_LICENSE'
    );
}

/*
 * Ensure officer data is always an array.
 */
if ($officer instanceof \yii\db\ActiveRecord) {
    $officer = $officer->toArray();
}

if (!is_array($officer)) {
    $officer = [];
}

/*
 * Fishing gears.
 */
$gears = $model->fishing_gear_type
    ? DistrictGearTypes::find()
        ->where([
            'in',
            'id',
            explode(',', $model->fishing_gear_type),
        ])
        ->all()
    : [];

/*
 * Create QR-code token.
 */
$hex = bin2hex(
    '{"type":"highseas-license","id":"'
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
 * Approval-workflow configuration.
 */
$approvalWorkflow = MApprovalWorkflow::findOne([
    'type' => $workflowType,
]);

/*
 * false: browser preview
 * true:  mPDF download
 */
$isPdf = (bool) ($isPdf ?? false);

$hasOfficerSignature = (bool) (
    $hasOfficerSignature ?? false
);

$signatureFilename = basename(
    (string) ($officer['signature'] ?? '')
);

/*
 * PDF mode:
 * The controller registers the image binary data through:
 *
 * $mpdf->imageVars['nationalLogo']
 * $mpdf->imageVars['boatLicense']
 * $mpdf->imageVars['officerSignature']
 *
 * Browser mode:
 * Use same-domain protected file URLs so browser cookies and
 * authentication are included automatically.
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

/*
 * For browser preview, show the signature when a filename exists.
 *
 * For PDF mode, also require the controller to confirm that the
 * signature file exists, is readable, and was registered.
 */
$showOfficerSignature =
    $signatureFilename !== ''
    && (!$isPdf || $hasOfficerSignature);
?>

<div class="license-view">
    <div class="page" style="
    /*margin: 1cm;*/
        /*width: 19.0cm;*/
        height: 28.7cm;
        border: 1px solid black;
        padding: 0.5cm"
    >
        <div class="">
            <div class="">
                <div class=""></div>
                <div class="" style="text-align: center">
                    <table>
                        <tr>
                            <td style="width: 15cm">
                                <div id="license_header">
                                    <p style="margin: 0; text-align: center;">
                                        <?php if ($nationalLogoSrc !== ''): ?>
                                            <img
                                                src="<?= htmlspecialchars(
                                                    $nationalLogoSrc,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                style="width: 50px;"
                                                alt="National Logo"
                                            >
                                        <?php endif; ?>

                                        <?php if ($boatLicenseSrc !== ''): ?>
                                            <img
                                                src="<?= htmlspecialchars(
                                                    $boatLicenseSrc,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                style="width: 450px;"
                                                alt="Boat License"
                                            >
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </td>
                            <td style="width: 4cm">
                                <div>
                                    <?= $outputCode ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <hr class="new4"/>
                    <p style="font-size: 14px;line-height: 13px;    margin-bottom: 5px">FISHING OPERATIONS LICENSE FOR
                        HIGH SEAS FISHING</p>
                    <p style="font-size: 10px;line-height: 13px;    margin-bottom: 0px"
                    >(This License is issued in accordance with provisions of the
                        High Seas Fishing Operations Regulation No. 1 of 2014, under the
                        Fisheries <br/>and Aquatic Resources Act, No 2 of 1996)</p
                    >


                    <div style="    height: 580px ">
                        <table style="border-collapse: collapse;border: 1px solid black;padding:2px;font-size: 12px">
                            <!-- <table> -->
                            <tbody>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    1
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    Highseas License Number
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $model->license_number ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    2
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    Registration Number of the Boat
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $model->boatRegistration->boatNumber->boat_number ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    3
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    Year
                                    of Boat Registration
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px;"
                                    colspan="3"><?= $boatRegistration->date_of_first_registration ? date("Y", strtotime($boatRegistration->date_of_first_registration)) : "" ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    4
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    Length
                                    of the Fishing Boat
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= ($model->boatRegistration->boatNumber->length != null && $model->boatRegistration->boatNumber->length != 0 ? $model->boatRegistration->boatNumber->length : $boatDesign->length ?? "") ?? "" ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    5
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    Make
                                    of Hull
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= ($boatDesign && (int) $boatDesign->hull_material !== 0)
                                        ? (Constant::$hullMaterials[$boatDesign->hull_material] ?? '')
                                        : '' ?></td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    6
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    Boat
                                    Yard Number
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $boatNumber->yard0->yard_uid ?? "" ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    7
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    Reg.
                                    Number of the Supporting Owners Details
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="3">
                                    N/A
                                </td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    8
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    Name
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $fishermen->preferred_name_for_id ?></td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    9
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    National Identity Card No
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $fishermen->nic ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">10
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    Contact number
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $fishermen->mobile ?></td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    11
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    Address
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $fishermen->permanent_address ?></td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    scope="row"></th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="3">
                                    <b>Authorized Fishing Operations</b></td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    12
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px">Authorized
                                    Fishing
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px">Authorized
                                    Fishing
                                    Gear Unit Details
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px">Authorized
                                    Species
                                    of Fish
                                </th>
                            </tr>

                            <?php foreach ($gears as $gear) {
                                $fishTypes = MFishTypes::find()->select("name")->where(["IN", "id", explode(",", $gear->fish_species)])->asArray()->all();

                                ?>


                                <tr>
                                    <th style="border-collapse: collapse;border: 1px solid black;padding:2px;font-size: 12px"
                                        scope="row"></th>
                                    <td style="border-collapse: collapse;border: 1px solid black;padding:2px;font-size: 12px"><?= $gear->subGear->description ?></td>
                                    <td style="border-collapse: collapse;border: 1px solid black;padding:2px ; font-size: 12px"> <?= str_replace("\"", " ", str_replace("{", " ", str_replace("}", " ", $gear->extra))) ?></td>
                                    <td style="border-collapse: collapse;border: 1px solid black;padding:2px ; font-size: 12px;"><?php
                                        $fish = [];
                                        foreach ($fishTypes as $fishType) {
                                            $fish[] = $fishType["name"];
                                        }
                                        echo implode(", ", $fish);
                                        ?></td>

                                </tr>
                            <?php } ?>


                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">13
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="1">
                                    Home
                                    port Landing
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?php
                                    echo $landingHarbour['Name'] ?? ""
                                    ?></td>

                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <br>
                    <br>

                    <br>


                    <table style="width: 100%; text-align: center">
                        <tbody>
                        <tr style="font-size: 12px">
                            <td style="font-size: 10px">Date of Issue
                                : <?= date("Y-m-d", strtotime($model->approved_time)) ?><br>
                                Valid
                                From
                                : <?= date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d', strtotime('-' . $approvalWorkflow->expired_in . ' month', strtotime($model->expire_date)))))); ?>
                                To : <?= date("Y-m-d", strtotime($model->expire_date)) ?>
                            </td>
                            <td>
                                <p style="font-size: 10px">...................................................... <br>Issuer's
                                    signature and
                                    stamp</p>
                                <p style=" font-size: 8px">(The license is only valid with the issuer's signature and
                                    stamp.)</p>

                            </td>
                            <td style="font-size: 10px"><?php if ($showOfficerSignature): ?>
                                    <img
                                        src="<?= htmlspecialchars(
                                            $officerSignatureSrc,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        style="max-width: 100px; max-height: 50px;"
                                        alt="Officer Signature"
                                    >
                                    <br>
                                <?php endif; ?>

                                <span style="font-size: 10px;">
                                    <?= htmlspecialchars(
                                        $officer['first_name'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                    <?= htmlspecialchars(
                                        $officer['last_name'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                                <br><br><br>
                                <br/>
                                Director General/Authorized Officer
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <br>

                    <div style="text-align: center; font-size: 8px; border-top: 1px solid black">
                        <br>
                        Please see overleaf for conditions of the license)<br/>
                        T.P./ Fax No: +94 112449170/+94 112434075<br/>
                        email:info@fisheriesdept.gov.lk

                    </div>
                </div>

            </div>
        </div>
    </div>