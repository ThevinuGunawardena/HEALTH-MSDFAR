<?php

/** @var yii\web\View $this */

use backend\config\Constant;

use yii\helpers\Html;

/** @var string $parentToken */

$this->title = 'Scientific Data';
//print_r($model);exit();
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm mb-5">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="section-block" id="cards">
                                    <h3 class="card-title">Boat & Gear Data - <?= $boat ?></h3>
                                </div>
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="card mb-5 shadow-sm">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label name="fishery_type">Fishery Type
                                                    : <?= $model->fisheyType->name ?? '' ?></label><br>

                                                <label name="sub_cat2">Sub Category
                                                    : <?= $model->subCategory->code ?? "" ?></label><br>
                                                <label name="hp">Engine HP : <?= $model->engine_hp ?? "" ?></label><br>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <h4>Departure</h4>
                                            </div>
                                            <div class="col-xl-12">
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <label name="dep_date">Date
                                                            : <?= $model->departure_date ?? "" ?></label><br>
                                                    </div>

                                                    <div class="col-xl-6">
                                                        <label name="time">Time: <?= $model->departure_time ?? "" ?></label><br>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <label name="fi_district">Fisheries District
                                                    : <?= $model->departureDistrict->name ?? "" ?></label><br>
                                                <label name="fi_division">FI Division
                                                    : <?= $model->departureDivision->name ?? "" ?></label><br>
                                                <label name="landing_place">Departure Place/ Port
                                                    : <?= $model->depaturePort->name ?? "" ?></label><br>
                                                <label>Weather Occurred : <?= $model->weather ?? "" ?></label><br>
                                            </div>


                                            <div class="col-lg-12">
                                                <label name="no_crew">Number of Crew Members
                                                    : <?= $model->crew_members_count ?? "" ?> </label><br>

                                                <label>Unloading Type
                                                    : <?= $model->unloading_type == "All" ? $model->unloading_type : "Partial - " . $model->unloading_type ?> </label><br>


                                                <label name="gear_set_time">Gear Setting Time
                                                    : <?= Constant::$gearSettingTime[$model->gear_setting_time] ?></label><br>
                                            </div>
                                            <div class="col-xl-12">
                                                <div class="row">
                                                    <div class="col-xl-2">
                                                        <label name="days">Days : <?= $model->days ?></label><br>
                                                    </div>
                                                    <div class="col-xl-2">
                                                        <label name="hours">Hours : <?= $model->hours ?></label><br>
                                                    </div>
                                                    <div class="col-xl-8">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div class="row">

                                        </div>
                                        <div class="row">

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">


                                <h4>Fishing Gears</h4>
                                <?php
                                $mainGearData = array_filter($model->scientificSamplingGearDatas, function ($var) {
                                    return ($var['type'] == 'mainGear');

                                });
                                $secondGearData = array_filter($model->scientificSamplingGearDatas, function ($var) {
                                    return ($var['type'] == 'secondGear');

                                });
                                $thirdGearData = array_filter($model->scientificSamplingGearDatas, function ($var) {
                                    return ($var['type'] == 'thirdGear');

                                });
                                $mainGear = [];
                                $secondGear = [];
                                $thirdGear = [];
                                if (sizeof($mainGearData) > 0) {
                                    $mainGear = $mainGearData[0];
                                }
                                //            print_r($mainGearData);exit();
                                if (sizeof($secondGearData) > 0) {
                                    $secondGear = $secondGearData[0] ?? $secondGearData[1] ?? $secondGearData[2] ?? [];
                                }
                                if (sizeof($thirdGearData) > 0) {
                                    $thirdGear = $thirdGearData[0] ?? $thirdGearData[1] ?? $thirdGearData[2] ?? [];
                                }

                                ?>
                                <?php if (!empty($mainGear)) { ?>
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <label name="main_gear"><b>Main Fishing Gear
                                                    : <?= $mainGear->gear0->description ?></b></label><br>
                                            <label name="target_species_main">Main Target Species
                                                : <?= $mainGear->targetSpecies->name ?></label><br>
                                            <label name="no_trip_main">How Many Operations Per Trip
                                                : <?= $mainGear->operations_per_trip ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <label>True Fishing Time</label>
                                            <div class="row">
                                                <div class="col-xl-2">
                                                    <label name="days_main">Days
                                                        : <?= $mainGear->fishing_time_days ?></label>
                                                </div>

                                                <div class="col-xl-2">
                                                    <label name="hours_main">Hours
                                                        : <?= $mainGear->fishing_time_hours ?></label><br>
                                                </div>
                                                <div class="col-xl-8">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="row">
                                                <div class="col-xl-3">
                                                    <label name="fishing_depth_main">Fishing Depth (Fathoms)
                                                        : <?= $mainGear->fishing_depth ?></label><br>
                                                </div>

                                                <div class="col-xl-3">
                                                    <!-- <label>E</label><br>
                                                    <input type="text" name="e_main" class="form-control"/><br> -->
                                                    <label>G Code : <?= $mainGear->g_code ?></label><br>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($secondGear)) { ?>
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <label name="main_gear"><b>Secondary Fishing Gear
                                                    : <?= $secondGear->gear0->description ?></b></label><br>
                                            <label name="target_species_main">Main Target Species
                                                : <?= $secondGear->targetSpecies->name ?></label><br>
                                            <label name="no_trip_main">How Many Operations Per Trip
                                                : <?= $secondGear->operations_per_trip ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <label>True Fishing Time</label>
                                            <div class="row">
                                                <div class="col-xl-2">
                                                    <label name="days_main">Days
                                                        : <?= $secondGear->fishing_time_days ?></label>
                                                </div>

                                                <div class="col-xl-2">
                                                    <label name="hours_main">Hours
                                                        : <?= $secondGear->fishing_time_hours ?></label><br>
                                                </div>
                                                <div class="col-xl-8">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="row">
                                                <div class="col-xl-3">
                                                    <label name="fishing_depth_main">Fishing Depth (Fathoms)
                                                        : <?= $secondGear->fishing_depth ?></label><br>
                                                </div>

                                                <div class="col-xl-3">
                                                    <!-- <label>E</label><br>
                                                    <input type="text" name="e_main" class="form-control"/><br> -->
                                                    <label>G Code : <?= $secondGear->g_code ?></label><br>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if (!empty($thirdGear)) { ?>
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <label name="main_gear"><b>Third Fishing Gear
                                                    : <?= $thirdGear->gear0->description ?></b></label><br>
                                            <label name="target_species_main">Main Target Species
                                                : <?= $thirdGear->targetSpecies->name ?></label><br>
                                            <label name="no_trip_main">How Many Operations Per Trip
                                                : <?= $thirdGear->operations_per_trip ?></label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <label>True Fishing Time</label>
                                            <div class="row">
                                                <div class="col-xl-2">
                                                    <label name="days_main">Days
                                                        : <?= $thirdGear->fishing_time_days ?></label>
                                                </div>

                                                <div class="col-xl-2">
                                                    <label name="hours_main">Hours
                                                        : <?= $thirdGear->fishing_time_hours ?></label><br>
                                                </div>
                                                <div class="col-xl-8">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="row">
                                                <div class="col-xl-3">
                                                    <label name="fishing_depth_main">Fishing Depth (Fathoms)
                                                        : <?= $thirdGear->fishing_depth ?></label><br>
                                                </div>

                                                <div class="col-xl-3">
                                                    <!-- <label>E</label><br>
                                                    <input type="text" name="e_main" class="form-control"/><br> -->
                                                    <label>G Code : <?= $thirdGear->g_code ?></label><br>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>


                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-3">
                            </div>
                            <div class="col-xl-3">
                            </div>
                            <div class="col-xl-3">
                                <div class="form-group">

                                </div>
                            </div>
                            <div class="col-xl-3">
                                <div class="form-group">
                                    <br> <?= Html::a(
                                    Yii::t('app', 'Close'),
                                    ['/scientific/view', 'token' => $parentToken],
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
</div>

