<?php
use yii\helpers\Html;
use backend\controllers\ProgressItemsController;
// Assuming you have the $counts array passed from your controller
?>
<div class="card-body">
    <h3>Boat Registration Renewal (Overall)</h3>
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0">
                            <?= Html::encode($counts['renewboatRegistrationcountuptoMonthBeforePrevious']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['renewboatRegistrationcountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['renewboatRegistrationpersentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['renewboatRegistrationcountuptoPreviousMonth']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3>First Boat Registration (Overall)</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationcountuptoMonthBeforePrevious']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationcountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['boatRegistrationpersentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationcountuptoPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationcountThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3>Boat Registration (IMUL)</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0">
                            <?= Html::encode($counts['boatRegistrationcountIMULuptoMonthBeforePrevious']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationIMULcountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['boatRegistrationIMULPercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationIMULcountuptoPreviousMonth']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationcountIMULinThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3>Boat Registration (IDAY)</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0">
                            <?= Html::encode($counts['boatRegistrationcountIDAYuptoMonthBeforePrevious']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationIDAYcountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['boatRegistrationIDAYPercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationIDAYcountuptoPreviousMonth']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationcountIDAYinThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <h3>Boat Registration (OFRP)</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0">
                            <?= Html::encode($counts['boatRegistrationcountOFRPuptoMonthBeforePrevious']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationOFRPcountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['boatRegistrationOFRPPercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationOFRPcountuptoPreviousMonth']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationcountOFRPinThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <h3>Boat Registration (MTRB)</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0">
                            <?= Html::encode($counts['boatRegistrationcountMTRBuptoMonthBeforePrevious']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationMTRBcountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['boatRegistrationMTRBPercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationMTRBcountuptoPreviousMonth']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationcountMTRBinThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <h3>Boat Registration (NBSB)</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0">
                            <?= Html::encode($counts['boatRegistrationcountNBSBuptoMonthBeforePrevious']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationNBSBcountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['boatRegistrationNBSBPercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto
                            <?= Html::encode($counts['lastDayOfPreviousMonthFormatted']) ?>
                        </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationNBSBcountuptoPreviousMonth']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationcountNBSBinThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3>Boat Registration (NTRB)</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0">
                            <?= Html::encode($counts['boatRegistrationcountNTRBuptoMonthBeforePrevious']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationNTRBcountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['boatRegistrationNTRBPercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationNTRBcountuptoPreviousMonth']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['boatRegistrationcountNTRBinThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <h3>Skipper License</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['SkipperuptoMonthBeforePrevious']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['SkippercountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['SkipperPercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['SkippercountuptoPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['SkipperInThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <h3>Fisherman Registration</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['FishermanuptoMonthBeforePrevious']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['FishermancountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['FishermanPercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['FishermancountuptoPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['FishermanThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <h3>Boat Number Issuing</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['BoatnumbernuptoMonthBeforePrevious']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['BoatnumbercountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['BoatnumberPercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['BoatnumbercountuptoPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['BoatnumberThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <h3>Highseas License Issuing</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['HighseasLicenseuptoMonthBeforePrevious']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['HighseasLicensecountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['HighseasLicensePercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['HighseasLicensecountuptoPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['HighseasLicenseInThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3>National License Issuing (IMUL)</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['NationalLicenseIMULuptoMonthBeforePrevious']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['NationalLicenseIMULcountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['NationalLicenseIMULPercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['NationalLicenseIMULcountuptoPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['NationalLicenseIMULinThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <h3>National License Issuing (Other)</h3>
    <div class="row">
        <!-- Fisherman License -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">From 2022 Upto<br>
                            <?= Html::encode($counts['monthBeforePreviousName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['NationalLicenseOtheruptoMonthBeforePrevious']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">On <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['NationalLicenseOthercountinPreviousMonth']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light mt-1">
                        <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Increase By</h5>
                        <?= Html::encode($counts['NationalLicenseOtherPercentage']) . '%' ?>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Upto <?= Html::encode($counts['previousMonthName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['NationalLicenseOthercountuptoPreviousMonth']) ?>
                        </h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3">Within <?= Html::encode($counts['CurrentYearName']) ?> </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['NationalLicenseOtherInThisYear']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-primary-light mt-1">
                        <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>