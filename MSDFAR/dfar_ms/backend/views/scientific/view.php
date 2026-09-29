<?php

/** @var yii\web\View $this */

/** @var backend\models\ScientificData $model */
/** @var string $token */

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ScientificSamplingData;
use backend\services\Util;
use yii\helpers\Html;
use yii\web\YiiAsset;

$this->title = Yii::t('app', 'Scientific Data');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Scientific Datas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="section-block" id="cards">
                        <h3 class="card-title">Scientific Data</h3>
                    </div>
                </div>

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <h4>General Details</h4>
                            <label name="fi_district">Fisheries District :
                                <strong><?= $model->district0->name ?> </strong> </label><br>
                            <label name="fi_division">FI Division :
                                <strong><?= $model->division0->name ?></strong></label><br>
                            <label name="landing_place">Name of the landing place :
                                <strong><?= $model->landingSite->name ?? "" ?></strong></label><br>
                            <label name="landing_place">Sampling date :
                                <strong><?= date("Y-m-d", strtotime($model->start_time)) ?? "" ?></strong></label><br>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <h4>Fleet Details Details</h4><br>
                            <h4>Number of craft operated</h4>
                            <div class="table-responsive ">
                                <table class="table">
                                    <thead><br>

                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">No. of Boats</th>
                                        <th scope="col">Boat Type</th>
                                        <th scope="col">Sub Category</th>
                                        <th scope="col">Gear Type</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($model->scientificFleetDatas as $scientificFleetData) { ?>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col"><?= $scientificFleetData["no_of_boats"] ?></th>
                                            <th scope="col"><?= $scientificFleetData->boatType->code ?></th>
                                            <th scope="col"><?= $scientificFleetData->subCategory->code ?? "" ?></th>
                                            <th scope="col"><?= $scientificFleetData->gearType->code . " - " . $scientificFleetData->gearType->description ?></th>
                                        </tr>
                                    <?php }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <h4>Sampling Crafts</h4>
                            <div class="row">
                                <div class="col-xl-12">


                                </div>
                            </div>
                            <div class="row">
                                <div class="table-responsive ">
                                    <table class="table">
                                        <thead><br>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Boat No.</th>
                                            <th scope="col">View</th>
                                            <th scope="col">View</th>
                                            <th scope="col">View</th>
                                            <th scope="col">View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($model->scientificSamplingDatas as $item) {
                                            $samplingToken = SecurityHelper::encryptId(
                                                ScientificSamplingData::class,
                                                (int) $item->id
                                            );
                                            ?>
                                            <tr>
                                                <td>1</td>
                                                <td><?= $item->boat_number ?></td>
                                                <td>
                                                    <?= Html::a(
                                                        Yii::t('app', 'Boat & Gear Data'),
                                                        ['/scientific/viewboatgeaers', 'token' => $samplingToken],
                                                        ['class' => 'btn btn-primary']
                                                    ) ?>
                                                </td>
                                                <td>
                                                    <?= Html::a(
                                                        Yii::t('app', 'Catch Data'),
                                                        ['/scientific/viewcatch', 'token' => $samplingToken],
                                                        ['class' => 'btn btn-primary']
                                                    ) ?>
                                                </td>
                                                <td>
                                                    <?= Html::a(
                                                        Yii::t('app', 'Length & Weight Data'),
                                                        ['/scientific/viewlengthweight', 'token' => $samplingToken],
                                                        ['class' => 'btn btn-primary']
                                                    ) ?>
                                                </td>
                                                <td>
                                                    <?= Html::a(
                                                        Yii::t('app', 'Economic Data'),
                                                        ['/scientific/viewoperationcost', 'token' => $samplingToken],
                                                        ['class' => 'btn btn-primary']
                                                    ) ?>
                                                </td>

                                            </tr>

                                        <?php } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="row">
                        <?php if (Util::editPermission()) { ?>
                            <div class="col-xl-3 col-lg-3">
                            </div>
                            <div class="col-xl-3 col-lg-3">
                                <div class="form-group">
                                    <?php if ($model->approval_stage == "Approved") { ?>
                                        <a class="btn btn-success btn-block">Approved</a>
                                    <?php } else if ($model->approval_stage == "Pending") { ?>
                                        <?php if (UserTypeUtil::hasType(Constant::MANAGEMENT)) { ?>
                                            <?= Html::beginForm(
                                                ['/scientific/approve', 'token' => $token],
                                                'post'
                                            ) ?>
                                            <?= Html::submitButton(
                                                Yii::t('app', 'Mark as Approved'),
                                                [
                                                    'class' => 'btn btn-success btn-block',
                                                    'data-confirm' => Yii::t(
                                                        'app',
                                                        'Are you sure you want to approve this scientific report?'
                                                    ),
                                                ]
                                            ) ?>
                                            <?= Html::endForm() ?>

                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3">
                                <div class="form-group">
                                    <?php if ($model->approval_stage == "Rejected") { ?>
                                        <a class="btn btn-success btn-danger btn-block">Rejected</a>
                                    <?php } else if ($model->approval_stage == "Pending") { ?>
                                        <?php if (UserTypeUtil::hasType(Constant::MANAGEMENT)) { ?>
                                            <?= Html::beginForm(
                                                ['/scientific/reject', 'token' => $token],
                                                'post'
                                            ) ?>
                                            <?= Html::submitButton(
                                                Yii::t('app', 'Mark as Rejected'),
                                                [
                                                    'class' => 'btn btn-danger btn-block',
                                                    'data-confirm' => Yii::t(
                                                        'app',
                                                        'Are you sure you want to reject this scientific report?'
                                                    ),
                                                ]
                                            ) ?>
                                            <?= Html::endForm() ?>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-xl-3 col-lg-3">
                            <div class="form-group">
                                <?= Html::a(
                                    Yii::t('app', 'Close'),
                                    ['/scientific/index'],
                                    ['class' => 'btn btn-primary btn-block']
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

