<?php

use backend\config\Constant;
use backend\services\CommonService;

$webURL = Yii::getAlias('@web');

$boatNumber = $model->boatNumber;
$boatDesign = $model->boatNumber->boatDesign;
$fishermen = $model->fisherman;
$officer = CommonService::getApprovedOfficer($model->id, "BOAT_REGISTER");
?>


<div class="">
    <div class="page" style="
    /*margin: 1cm;*/
        /*width: 19.0cm;*/
        /*height: 28.7cm;*/
        border: 1px solid black; padding: 2px; font-size: 10px;
        padding: 1cm;
position: relative"
    >
        <div style="text-align: center ;width: 760px;" id="license_header">
            <img style="width: 40px" src="<?= Constant::$BASEURL_LICENSE ?>national_Logo2.jpg"><br>
            <p><img style="width: 710px" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_1.jpg"></p>
        </div>
        <!--        <div style="position: absolute; text-align: center;    right: 15px;    top: 15px;">-->
        <!--            <h5>--><?php //= $boatNumber->boat_number ?><!--</h5>-->
        <!--        </div>-->
        <div style="    font-size: 12px;    line-height: 5px;    text-align: center;">
            <p><img style="height: 20px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_28.jpg">
            </p>
        </div>
        <div>
            <table style="border-collapse: collapse">
                <tr>
                    <th style="border: 1px solid black; padding: 2px; font-size: 10px">1</th>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_2.jpg">
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $model->boatNumber->boat_number ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $model->boatNumber->boat_number ?></td>


                </tr>
                <tr style="height: 30px;">
                    <th style="border: 1px solid black; padding: 2px; font-size: 10px;">2</th>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_3.jpg">
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $model->date_of_first_registration ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $model->boatNumber->boat_number ?></td>


                </tr>
                <tr>
                    <th style="border: 1px solid black; padding: 2px; font-size: 10px">3</th>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="5"><img
                                style="height: 12px;width: auto"
                                src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_4.jpg">
                    </td>


                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="3"><img
                                style="height:50px;width: auto"
                                src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_5.jpg">
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="1"><?= $boatDesign->hull_material != 0 ? Constant::$hullMaterials[$boatDesign->hull_material] : "" ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="1"><img
                                style="height:50px;width: auto"
                                src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_6.jpg">
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="1"><?= $model->date_of_construction ?></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="3">
                        <img style="height: 40px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_7.jpg">
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="1"><?= $boatNumber->boatType->code ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="1">
                        <img style="height: 40px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_8.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="1"><?= $boatDesign->length ?> Ft * <?= $boatDesign->width ?> Ft
                    </td>
                </tr>

                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" rowspan="3"></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" rowspan="2">
                        <img style="height: 90px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_9.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="4">
                        <img style="height: 30px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_10.jpg">

                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">
                        <img style="height: 50px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_11.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">
                        <img style="height: 50px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_12.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">
                        <img style="height: 50px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_13.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">
                        <img style="height: 50px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_14.jpg">

                    </td>

                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"> <?= $model->how_propelled ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= $model->engine_serial_number ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= $model->engine_type ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= $model->engine_horsepower ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= $model->fuel_type ?></td>

                </tr>

                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_15.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_16.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">DFAR/FI/GLE/BY/2009/0034</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_17.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= $model->fishing_equipment ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_18.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= $model->navigation_equipment ?>
                        / <?= $model->communication_equipment ?></td>
                </tr>
            </table>

            <div style="    font-size: 12px;    line-height: 5px;    text-align: center;margin-top: 12px">
                <p>
                    <img style="height: 20px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_19.jpg">

                </p>
            </div>
            <table style="width: 100%; border-collapse: collapse">
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">1./2.</td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 30px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_20.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 30px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_21.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">ල
                        <img style="height: 30px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_22.jpg">

                    </td>


                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $fishermen->first_name ?> <?= $fishermen->last_name ?>
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $fishermen->nic ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $fishermen->permanent_address ?>.
                    </td>


                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">3</td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 30px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_23.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="4"><?= $model->insurance_no ?></td>


                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 30px;width: auto" src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_24.jpg">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $model->landingSite->name ?? "" ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">Galle Fisheries
                        Harbor.
                    </td>


                </tr>

            </table>
            <div>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>


                <br>
                <table style="width: 100%; border: none ;text-align: center">
                    <tbody>
                    <tr>

                        <td>
                            <?PHP
                            if ($officer != null) { ?>
                                <p><img style="max-width: 100px; max-height: 50px"
                                        src="<?= Constant::$FILE_VIEW_PATH ?>officer/signature/<?= $officer["signature"] ?>"><br><span
                                            style="font-size: 10px;"> <?= $officer["first_name"] ?>  <?= $officer["last_name"] ?></span>
                                    <br> <img style="height: 30px;width: auto"
                                              src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_25.jpg">
                                </p>
                            <?PHP } ?>
                        </td>

                        <td><p>...........................................
                                <br>
                                <img style="height: 30px;width: auto"
                                     src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_26.jpg">
                            </p>
                        </td>
                        <td><p>
                                <img style="max-width: 100px; max-height: 50px"
                                     src="<?= Constant::$FILE_VIEW_PATH ?>fisherman/<?= $fishermen->signature ?>">

                                <br>
                                <img style="height: 30px;width: auto"
                                     src="<?= Constant::$BASEURL_LICENSE ?>boatlicense_27.jpg">
                            </p>
                        </td>
                    </tr>

                    </tbody>
                </table>
                <br>
                <br>
                <br>
                <br>
                <table style="width: 100%; border: none; text-align: right">
                    <tbody>

                    <tr>
                        <td>
                            <p style="text-align: right;font-size: 8px">........................................... <br>Issuer's
                                signature and
                                stamp</p>
                            <p style="text-align: right; font-size: 8px">(The license is only valid with the issuer's
                                signature and
                                stamp.)</p>


                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
