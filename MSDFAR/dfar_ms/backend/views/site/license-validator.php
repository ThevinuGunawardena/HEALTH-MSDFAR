<?php

use backend\config\Constant;

$webURL = Yii::getAlias('@web');

?>

<div class="min-vh-100 d-flex align-items-center">
    <div class="splash-container">
        <div class="card shadow-sm">
            <div class="card-header">
                <div style="text-align: center">
                    <img style="width: 60px ; " src="<?= Constant::$BASEURL_LICENSE ?>national_Logo2.jpg">
                    <h1 class="mb-1">Online License / Certificate Validator</h1>
                    <p class="mb-0">Department of Fisheries and Aquatic Resource</p>
                </div>

            </div>

        </div>
        <div class="card shadow-sm">

            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($status == "ACTIVE" || $status == "Approved") { ?>

                            <div class="alert alert-success" role="alert">
                                <h4 class="alert-heading">Well done!</h4>
                                <p>This license / certificate has been officially issued through DFAR Online and has
                                    been verified as
                                    valid.</p>
                            </div>
                        <?php } ?>
                        <?php if ($status == "Not_allowed") { ?>

                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading">Alert!</h4>
                                <p>This license / certificate has not been issued through DFAR Online and is therefore
                                    invalid.</p>
                            </div>
                        <?php } ?>
                        <?php if ($status == "NOT_AVAILABLE") { ?>

                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading">Alert!</h4>
                                <p>This license / certificate has not been issued through DFAR Online and is therefore
                                    invalid.</p>
                            </div>
                        <?php } ?>
                        <?php if ($status == "EXPIRED") { ?>

                            <div class="alert alert-warning" role="alert">
                                <h4 class="alert-heading">Warning!</h4>
                                <p>This license / certificate was issued through DFAR Online but has expired. Please
                                    renew the license / certificate
                                    or
                                    contact support for further assistance.</p>
                            </div>
                        <?php } ?>
                        <?php if ($status == "INVALID_TOKEN") { ?>

                            <div class="alert alert-dark" role="alert">
                                The token is invalid, Please try again with a valid token.
                            </div>
                        <?php } ?>
                        <?php if ($status != "NOT_AVAILABLE" && $status != "INVALID_TOKEN") { ?>
                            <table class="table-responsive">
                                <tbody>
                                <tr>
                                    <td>license / certificate Type</td>
                                    <td>: <?= $data["license_type"] ?></td>
                                </tr>
                                <?php if (!empty($data["license_number"])) { ?>
                                <tr>
                                    <td>License Number</td>
                                    <td>: <?= $data["license_number"] ?></td>
                                </tr>
                                <?php } ?>
                                <?php if (!empty($data["fisherman_name"])) { ?>
                                <tr>
                                    <td>Fisherman Name</td>
                                    <td>: <?= $data["fisherman_name"] ?></td>
                                </tr>
                                <?php } ?>
                                <?php if (!empty($data["fisherman_nic"])) { ?>
                                <tr>
                                    <td>Fisherman NIC</td>
                                    <td>: <?= $data["fisherman_nic"] ?></td>
                                </tr>
                                <?php } ?>
                                <?php if (!empty($data["expire_date"])) { ?>
                                <tr>
                                    <td>Expire Date</td>
                                    <td>: <?= $data["expire_date"] ?></td>
                                </tr>
                                <?php } ?>
                                <?php if (!empty($data["current_status"])) { ?>
                                    <tr>
                                        <td>Current Status</td>
                                        <td>: <?= $data["current_status"] ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                        <div id="validationRespose" sclass="row">
                            <div class="col-lg-12">
                                <div style="   margin: auto;">
                                    <div id="">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card shadow-sm">


                    <div class="card-footer bg-white">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                2026 © IT division - Designed and Developed by<a
                                        href="https://www.fisheriesdept.gov.lk/software-development-unit/"
                                        target="_blank"
                                        class="ml-1">IT Division, Department of
                                    Fisheries and Aquatic Resources, Sri Lanka.</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
