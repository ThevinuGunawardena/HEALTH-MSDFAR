<?php
use yii\helpers\Html;
use backend\controllers\InquiryController;
// Assuming you have the $counts array passed from your controller
?>
<div class="card-body">
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="d-inline-block">
                        <h5 class="text-muted mb-3"> Open </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['countOpen']) ?></h2>
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
                        <h5 class="text-muted mb-3"> In Progress </h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['countInProgress']) ?></h2>
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
                        <h5 class="text-muted mb-3"> Completed</h5>
                        <h2 class="mb-0"> <?= Html::encode($counts['countCompleted']) ?></h2>
                    </div>
                    <div class="float-right icon-shape icon-xl rounded-circle bg-info-light mt-1">
                        <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
