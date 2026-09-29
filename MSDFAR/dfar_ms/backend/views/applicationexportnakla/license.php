<?php

use backend\config\Constant;
use backend\models\MApprovalWorkflow;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

$webURL = Yii::getAlias('@web');
//$fishermen = $model->owner0;
$officer = CommonService::getApprovedOfficer($model->id, "ExportNakla");

$hex = bin2hex('{"type":"ExportNakla","id":"' . $model->id . '"}');
$qrCode = new QrCode(Constant::$BASEURL . "/site/license-validation?token=" . $hex);
$output = new Output\Svg();
$outputCode = str_replace('<?xml version="1.0"?>', '', $output->output($qrCode, 110, 'white', 'black'));
$approvalWorkflow = MApprovalWorkflow::findOne(['type' => "ExportNakla"]);

?>
<div class="license-view">

    <div class="page" style=" border: 1px solid black;
        padding: 1cm">
        <table>
            <tr>
                <td style="width: 80%">
                    <div id="license_header">
                        <p><img style="width: 50px ; " src="<?= Constant::$BASEURL_LICENSE ?>national_Logo2.jpg">
                            <img style="width: 400px" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_1.jpg"></p>
                    </div>
                </td>
                <td style="width: 20%">
                    <div>
                        <?= $outputCode ?>
                    </div>
                </td>
            </tr>
        </table>

        <h3 style="text-align: center;">Licence for Export Lobster</h3>


        <h4>Applicant Information</h4>
        <table class="table table-bordered" style="width: 100%;">
            <tr>
                <td style="width: 50%;"><strong>Permit Number:</strong></td>
                <td style="width: 50%;"><?= date("Y") . "/" . $model->id . "E" ?></td>
            </tr>
            <tr>
                <td style="width: 50%;"><strong>Full Name:</strong></td>
                <td style="width: 50%;"><?= Html::encode($model->full_name) ?></td>
            </tr>
            <tr>
                <td><strong>Permanent Address:</strong></td>
                <td><?= Html::encode($model->permanent_address) ?></td>
            </tr>
            <tr>
                <td><strong>NIC Number:</strong></td>
                <td><?= Html::encode($model->nic_number) ?></td>
            </tr>
            <tr>
                <td><strong>Business Registration Number:</strong></td>
                <td><?= Html::encode($model->business_reg_number) ?></td>
            </tr>
        </table>

        <h4>NAKLA Export Details</h4>
        <table class="table table-bordered" style="width: 100%;">
            <tr>
                <td style="width: 50%;"><strong>Previous Permit Exported Quantity per unit:</strong></td>
                <td style="width: 50%;"><?= Html::encode($model->previouspermit_exported_quantity_kg) ?></td>
            </tr>
            <tr>
                <td><strong>Purchase Places / Places of Fishing:</strong></td>
                <td><?= Html::encode($model->purchase_place) ?></td>
            </tr>
            <tr>
                <td><strong>Export Quantity per unit:</strong></td>
                <td><?= Html::encode($model->export_quantity_kg) ?></td>
            </tr>
            <tr>
                <td><strong>Export Countries:</strong></td>
                <td><?= CommonService::getCountryNames($model->export_countries) ?></td>
            </tr>
        </table>
        <p>According to your request dated <?= date("Y-m-d", strtotime($model->approved_time)) ?>, this Department has
            no objection
            on exporting
            of “NAKLA” (operculum of <i>Chicoreous ramosus</i>) subjected to the following conditions.</p>
        <?= $model->tnc ?>
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
                <td style="font-size: 10px"><img style="max-width: 100px; max-height: 50px"
                                                 src="<?= Constant::$FILE_VIEW_PATH ?>officer/signature/<?= $officer["signature"] ?? "" ?>"><br><span
                            style="font-size: 10px;"> <?= $officer["first_name"] ?? "" ?>  <?= $officer["last_name"] ?? "" ?></span><br>
                    <br/>
                    Director General/Authorized Officer
                </td>
            </tr>
            </tbody>
        </table>

    </div>
</div>
