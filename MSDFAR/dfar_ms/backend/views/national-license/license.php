<?php

use backend\config\Constant;
use backend\models\DistrictGearTypes;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\MApprovalWorkflow;
use backend\models\MFishTypes;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;

$webURL = Yii::getAlias('@web');

$boatNumber = $model->boatRegistration->boatNumber;

$boatDesign = $model->boatRegistration
    ->boatNumber
    ->boatDesign;

$fishermen = $model->fisherman;

$gears = $model->division_gear_types
    ? DistrictGearTypes::find()
        ->where([
            'in',
            'id',
            explode(',', $model->division_gear_types),
        ])
        ->all()
    : [];

$officer = $officer
    ?? CommonService::getApprovedOfficer(
        $model->id,
        'NATIONAL_LICENSE'
    );

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
 * Generate QR code.
 */
$hex = bin2hex(
    '{"type":"national-license","id":"'
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
 * Get latest boat registration details.
 */
$boatRegistration =
    FishermanRegisterdBoatLicense::find()
        ->where([
            'id' => $model->boatRegistration->id,
        ])
        ->orderBy([
            'nid' => SORT_DESC,
        ])
        ->one();

$approvalWorkflow = MApprovalWorkflow::findOne([
    'type' => 'NATIONAL_LICENSE',
]);

/*
 * PDF-related values.
 */
$isPdf = (bool) ($isPdf ?? false);

$hasOfficerSignature =
    (bool) ($hasOfficerSignature ?? false);

$signatureFilename = basename(
    (string) ($officer['signature'] ?? '')
);

/*
 * For PDF generation, image binary data is registered in the
 * controller using $mpdf->imageVars.
 *
 * For browser display, normal image URLs are used.
 */
$nationalLogoSrc = $isPdf
    ? 'var:nationalLogo'
    : rtrim(Constant::$BASEURL_LICENSE, '/')
        . '/national_Logo2.jpg';

$boatLicenseSrc = $isPdf
    ? 'var:boatLicense'
    : rtrim(Constant::$BASEURL_LICENSE, '/')
        . '/boatlicense_1.jpg';

$officerSignatureSrc = $isPdf
    ? 'var:officerSignature'
    : rtrim(Constant::$FILE_VIEW_PATH, '/')
        . '/officer/signature/'
        . rawurlencode($signatureFilename);

/*
 * Display signature only when a filename exists.
 *
 * In PDF mode, also require the controller to confirm that the
 * physical signature file was found and registered in imageVars.
 */
$showOfficerSignature =
    $signatureFilename !== ''
    && (!$isPdf || $hasOfficerSignature);
?>


<style>

</style>

<div class="license-view">
    <div class="page" style="
    /*margin: 1cm;*/
        /*width: 19.0cm;*/
        height: 28.7cm;
        border: 1px solid black;
        padding: 1cm"
    >
        <div class="">
            <div class="">

                <div style="text-align: center;font-size: 12px">
                    <table>
                        <tr>
                            <td style="width: 14cm">
                                <div id="license_header">
                                   <p style="text-align: center; margin: 0;">
                                    <?php if ($nationalLogoSrc !== null): ?>
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

                                   <?php if ($boatLicenseSrc !== null): ?>
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
                            <td style="width: 3cm; text-align: right">
                                <div>
                                    <?= $outputCode ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <hr/>
                    <?php
                    if($model->boatRegistration->boatNumber->boat_type != 1){?>
                        <p style="font-size: 14px;line-height: 10px;    margin-bottom: 2px">FISHING OPERATIONS LICENSE FOR FISHING (for OFRP/ MTRB/ NTRB boat types)</p>
                    <p style="font-size: 10px;line-height: 13px;    margin-bottom: 2px"
                    >(This License is issued in accordance with provisions of the National Fishing Operations Regulation <br> No. 1 
                    of 2014, under the Fisheries and Aquatic Resources Act, No 2 of 1996)</p
                    >
                    <div style="    height: 570px ">
                        <table style="border-collapse: collapse;border: 1px solid black;padding:2px;text-align: left;font-size: 13px">
                            <!-- <table> -->
                            <tbody>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    1
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    
                                    License Number
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $model->license_number ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    2
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    Registration Number of the Boat
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $model->boatRegistration->boatNumber->boat_number ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    3
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
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
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                Name of the owner 
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $fishermen->preferred_name_for_id ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    5
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                National Identity Card No
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?=$fishermen->nic ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">6
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    Contact number
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $fishermen->mobile ?></td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">7
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    Address
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $fishermen->permanent_address ?></td>
                            </tr>

                            
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    8
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    Reg.
                                    Number of the Supporting Owners Details
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="3">
                                    N/A
                                </td>
                            </tr>

                            <!-- <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">9
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                	Home/ Port of Landing	
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"></strong><?= $model->landingSite->name ?? "" ?></td>
                            </tr> -->


                            

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    scope="row">9</th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="5">
                                    <b>Authorized
                                        Fishing Operations</b></td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
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
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px">Fishing season
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px">Fishing
                                    Duration
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
                                    <td style="border-collapse: collapse;border: 1px solid black;padding:2px;font-size: 12px"><?= str_replace(",", ", ", $gear->fishing_time_periods) ?></td>
                                    <td style="border-collapse: collapse;border: 1px solid black;padding:2px ; font-size: 12px"><?php
                                        $duration = explode(",", $gear->fishing_time_durations);
                                        $durationText = [];
                                        foreach ($duration as $item) {
                                            $durationText[] = Constant::$FishingDuration[$item];
                                        }
                                        echo str_replace(",", ", ", implode(",", $durationText)) ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">10
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    <strong>Home
                                        port Landing : </strong><?= $model->landingSite->name ?? "" ?>
                                </td>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><strong>11: Engine </strong>
                                    : <?= Constant::$engineMake[$boatRegistration->engine_make] ?? "" ?>
                                    : <?= Constant::$engineType[$boatRegistration->engine_type] ?? "" ?>
                                    - <?= $boatRegistration->engine_serial_number ?? "" ?>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <?php
                    }
                    else{
                        ?>
                         <p style="font-size: 14px;line-height: 10px;    margin-bottom: 2px">FISHING OPERATIONS LICENSE FOR
                        FISHING (EEZ)</p>
                    <p style="font-size: 10px;line-height: 13px;    margin-bottom: 2px"
                    >(This License is issued in accordance with provisions of the
                        National Fishing Operations Regulation, under the
                        Fisheries and Aquatic Resources Act, No 2 of 1996)</p
                    >
                    <div style="    height: 570px ">
                        <table style="border-collapse: collapse;border: 1px solid black;padding:2px;text-align: left;font-size: 13px">
                            <!-- <table> -->
                            <tbody>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    1
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    EEZ
                                    License Number
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $model->license_number ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    2
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    Registration Number of the Boat
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $model->boatRegistration->boatNumber->boat_number ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    3
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
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
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
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
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    Make
                                    of Hull
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $boatDesign && $boatDesign->hull_material != 0 ? Constant::$hullMaterials[$boatDesign->hull_material] : "" ?></td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    6
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    Boat
                                    Yard Number
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $boatDesign->yard0->name ?? "" ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    7
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
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
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    Name
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $fishermen->preferred_name_for_id ?> </td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">
                                    9
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    National Identity Card No
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $fishermen->nic ?></td>
                            </tr>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">10
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    Contact number
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $fishermen->mobile ?></td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">11
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    Address
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><?= $fishermen->permanent_address ?></td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    scope="row"></th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="5">
                                    <b>Authorized
                                        Fishing Operations</b></td>
                            </tr>

                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">12
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
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px">Fishing season
                                </th>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px">Fishing
                                    Duration
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
                                    <td style="border-collapse: collapse;border: 1px solid black;padding:2px;font-size: 12px"><?= str_replace(",", ", ", $gear->fishing_time_periods) ?></td>
                                    <td style="border-collapse: collapse;border: 1px solid black;padding:2px ; font-size: 12px"><?php
                                        $duration = explode(",", $gear->fishing_time_durations);
                                        $durationText = [];
                                        foreach ($duration as $item) {
                                            $durationText[] = Constant::$FishingDuration[$item];
                                        }
                                        echo str_replace(",", ", ", implode(",", $durationText)) ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <th style="border-collapse: collapse;border: 1px solid black;padding:2px" scope="row">13
                                </th>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px" colspan="2">
                                    <strong>Home
                                        port Landing : </strong><?= $model->landingSite->name ?? "" ?>
                                </td>
                                <td style="border-collapse: collapse;border: 1px solid black;padding:2px"
                                    colspan="3"><strong>14: Engine </strong>
                                    : <?= Constant::$engineMake[$boatRegistration->engine_make] ?? "" ?>
                                    : <?= Constant::$engineType[$boatRegistration->engine_type] ?? "" ?>
                                    - <?= $boatRegistration->engine_serial_number ?? "" ?>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                        
                        <?php
                    }
                    ?>
                   

                   
                    <br>
                    <table style="width: 100%; text-align: center">
                        <tbody>
                        <tr>
                            <td style=" font-size: 10px">Date of Issue
                                : <?= date("Y-m-d", strtotime($model->approved_time)) ?><br>

                                Valid From
                                : <?= date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d', strtotime('-' . $approvalWorkflow->expired_in . ' month', strtotime($model->expire_date)))))); ?>
                                To : <?= date("Y-m-d", strtotime($model->expire_date)) ?>
                            </td>

                            <td>
                                <p style="font-size: 10px;margin-bottom: 5px">
                                    ...................................................... <br>Issuer's
                                    signature and
                                    stamp</p>
                                <p style=" font-size: 8px">(The license is only valid with the issuer's signature and
                                    stamp.)</p>

                            </td>
                            <td style="text-align: center;font-size: 8px">
                               <?php if ($showOfficerSignature): ?>
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

                                <br>
                                Director General/Authorized Officer
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <br>


                    <div style="text-align: center; font-size: 8px; border-top: 1px solid black">
                        <br>
                        <p>Please see overleaf for conditions of the license)<br>
                            T.P./ Fax No: +94 112449170/+94 112434075
                            email:info@fisheriesdept.gov.lk</p>
                    </div>


                </div>


            </div>


        </div>


    </div>
