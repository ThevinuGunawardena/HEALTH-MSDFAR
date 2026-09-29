<?php

use backend\config\Constant;
use backend\models\MApprovalWorkflow;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

$webURL = Yii::getAlias('@web');
//$fishermen = $model->owner0;
$officer = CommonService::getApprovedOfficer($model->id, "ExportLobster");

$hex = bin2hex('{"type":"ExportLobster","id":"' . $model->id . '"}');
$qrCode = new QrCode(Constant::$BASEURL . "/site/license-validation?token=" . $hex);
$output = new Output\Svg();
$outputCode = str_replace('<?xml version="1.0"?>', '', $output->output($qrCode, 110, 'white', 'black'));
$approvalWorkflow = MApprovalWorkflow::findOne(['type' => "ExportLobster"]);

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

        <h3 style="text-align: center;">LOBSTER FISHERIES MANAGEMENT REGULATIONS – 2000 SCHEDULE V (REGULATIONS 9 (2)
            <br>
            LICENSE FOR EXPORTATION OF SPINY LOBSTERS OR SLIPPER LOBSTERS
        </h3>


        <!--        <h4>Applicant Information</h4>-->
        <p><?= $model->full_name ?> of <?= $model->address ?> is hereby authorized to export the following
            lobster species. This License if not cancelled previously,
            shall be valid from Issue Date:<?= date("Y-m-d", strtotime($model->approved_time)) ?> to <?=
            date("Y-m-d", strtotime($model->expire_date))
            ?></p>
        <table class="table table-bordered" style="width: 100%;">
            <tr>
                <td style="width: 50%;"><strong>Permit Number:</strong></td>
                <td style="width: 50%;"><?= date("Y") . "/" . $model->id . "E" ?></td>
            </tr>

        </table>
        <h3>Names and Sizes of Lobster Species</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Lobster Species</th>
                    <th>Weight</th>
                    <th>From where caught</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($consignments as $place): ?>
                    <tr>
                        <td><?= Html::encode($place->lobster_species) ?></td>
                        <td><?= Html::encode($place->weight) ?></td>
                        <td><?= Html::encode($place->caught_from) ?></td>

                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <h4>Lobster Export Details</h4>


        <p>According to your request dated <?= Html::encode($model->approved_time) ?>, this Department has no objection
            of transporting and keep in possession of
            Lobster subjected to the following conditions.</p>
        <?= $model->tnc ?>
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
