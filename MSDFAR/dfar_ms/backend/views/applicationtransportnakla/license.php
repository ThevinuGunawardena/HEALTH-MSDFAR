<?php

use backend\config\Constant;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

$webURL = Yii::getAlias('@web');
//$fishermen = $model->owner0;
$officer = CommonService::getApprovedOfficer($model->id, "BOAT_NUMBER");

$hex = bin2hex('{"type":"BOAT_NUMBER","id":"' . $model->id . '"}');
$qrCode = new QrCode(Constant::$BASEURL . "/site/license-validation?token=" . $hex);
$output = new Output\Svg();
$outputCode = str_replace('<?xml version="1.0"?>', '', $output->output($qrCode, 110, 'white', 'black'));

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

        <h3 style="text-align: center;">Licence for Transport and keep in possession of “NAKLA” (operculum of Chicoreous
            ramosus)</h3>


        <h4>Applicant Information</h4>
        <table class="table table-bordered">
            <tr>
                <td style="width: 50%;"><strong>Full Name:</strong></td>
                <td style="width: 50%;"><?= Html::encode($model->full_name) ?></td>
            </tr>
            <tr>
                <td><strong>Permanent Address:</strong></td>
                <td><?= Html::encode($model->permanent_address) ?></td>
            </tr>
            <tr>
                <td><strong>Telephone Number:</strong></td>
                <td><?= Html::encode($model->telephone) ?></td>
            </tr>
            <tr>
                <td><strong>National ID Number:</strong></td>
                <td><?= Html::encode($model->nic_number) ?></td>
            </tr>
            <tr>
                <td><strong>Business Registration Number:</strong></td>
                <td><?= Html::encode($model->business_reg_number) ?></td>
            </tr>
        </table>

        <h4><h4>Store Place & Transport Details</h4></h4>
        <?php if (!empty($storePlaces)): ?>
            <div class="">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Species</th>
                        <th>Weight per District</th>
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
                            <td><?= Html::encode($place->species) ?></td>
                            <td><?= Html::encode($place->weight_per_distict) ?></td>
                            <td><?= Html::encode($place->purchasing_district) ?></td>
                            <td><?= Html::encode($place->intermediat_destination) ?></td>
                            <td><?= Html::encode($place->final_store_place) ?></td>
                            <td><?= Html::encode($place->transport_method) ?></td>
                            <td><?= Html::encode($place->vehicle_number) ?></td>
                            <td><?= Html::encode($place->boat_number) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No records available.</p>
        <?php endif; ?>


        <p>According to your request dated <?= Html::encode($model->request_date) ?>, this Department has no objection
            of transporting and keep in possession of
            “NAKLA” (Operculum of Chicoreous ramosus) subjected to the following conditions.</p>
            <?= Html::encode($model->tnc) ?>
        <br>
        <br>
        <table style="width: 100%">
            <tr>
                <td>
                    <p style="font-size: 10px"><strong>Issue Date:</strong> <?= date('Y-m-d') ?></p></td>
                <td style="text-align: right"><p style="text-align: right;">
                        <strong>E-signature:</strong><br>
                    <p style="font-size: 10px"><strong>Director General <br>
                            Department of Fisheries & Aquatic Resources</strong>
                    </p>
                </td>
            </tr>
        </table>

    </div>
</div>
