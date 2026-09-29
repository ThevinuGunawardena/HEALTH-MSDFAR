<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\ResourceProfile $model */
/** @var bool $isDashboardView */

$isDashboardView = $isDashboardView ?? false;

$this->title = 'Resource Profile';
$this->params['breadcrumbs'][] = ['label' => 'Profile Officers', 'url' => ['profile-officers/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <?php if (!$isDashboardView && UserTypeUtil::hasType(Constant::FI)): ?>
                <p>
                    <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Back to Profile', ['profile-officers/view', 'id' => Yii::$app->user->identity->profile_id], ['class' => 'btn btn-secondary']) ?>
                </p>
            <?php elseif ($isDashboardView): ?>
                <p>
                    <?php 
                    // Determine the correct dashboard URL based on user type
                    $dashboardUrl = (UserTypeUtil::hasType(Constant::ADMIN) || UserTypeUtil::hasType(Constant::DG)) 
                        ? ['dashboard-all'] 
                        : ['dashboard'];
                    ?>
                    <?= Html::a('Back', 'javascript:history.back()', ['class' => 'btn btn-secondary']) ?>
                </p>
            <?php endif; ?>

            <h3 class="text-muted mb-3">Location Details</h3>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label"><strong>FI Division Area</strong></label>
                        <div class="border rounded p-2 bg-light">
                            <?= $model->division ? Html::encode($model->division->name) : '<span class="text-muted">Not set</span>' ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="control-label"><strong>Grama Niladari Wasam</strong></label>
                    <?php if (!empty($model->gramaNiladariWasams)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->gramaNiladariWasams as $index => $wasam): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($wasam->wasam_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No grama niladari wasam added yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <label class="control-label"><strong>Fishing Villages</strong></label>
                    <?php if (!empty($model->fishingVillages)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->fishingVillages as $index => $village): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($village->village_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No fishing villages added yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <h3 class="text-muted mb-3 mt-4">Fishing Statistics</h3>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label"><strong>Fishing Population</strong></label>
                        <div class="border rounded p-2 bg-light">
                            <?= $model->fishing_population !== null ? Html::encode($model->fishing_population) : '<span class="text-muted">Not set</span>' ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label"><strong>Fisheries Families</strong></label>
                        <div class="border rounded p-2 bg-light">
                            <?= $model->fisheries_families !== null ? Html::encode($model->fisheries_families) : '<span class="text-muted">Not set</span>' ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label"><strong>Active Fishermen</strong></label>
                        <div class="border rounded p-2 bg-light">
                            <?= $model->active_fishermen !== null ? Html::encode($model->active_fishermen) : '<span class="text-muted">Not set</span>' ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label"><strong>Fishing Households</strong></label>
                        <div class="border rounded p-2 bg-light">
                            <?= $model->fishing_households !== null ? Html::encode($model->fishing_households) : '<span class="text-muted">Not set</span>' ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label"><strong>Fishing Households Without Sanitary</strong></label>
                        <div class="border rounded p-2 bg-light">
                            <?= $model->fishing_households_without_sanitary !== null ? Html::encode($model->fishing_households_without_sanitary) : '<span class="text-muted">Not set</span>' ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label"><strong>Fishing Households Without Drinking Water</strong></label>
                        <div class="border rounded p-2 bg-light">
                            <?= $model->fishing_households_without_drinking_water !== null ? Html::encode($model->fishing_households_without_drinking_water) : '<span class="text-muted">Not set</span>' ?>
                        </div>
                    </div>
                </div>

                
                <div class="col-12 col-md-6">
                    <label class="control-label"><strong>Landing Sites</strong></label>
                    <?php if (!empty($model->landingSites)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->landingSites as $index => $site): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($site->landing_site_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No landing sites added yet.</div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label class="control-label"><strong>Beach Sites</strong></label>
                    <?php if (!empty($model->beachSites)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->beachSites as $index => $beach): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($beach->beach_site_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No beach sites added yet.</div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6 pt-4">
                    <label class="control-label"><strong>Ice Factories</strong></label>
                    <?php if (!empty($model->iceFactories)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->iceFactories as $index => $factory): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($factory->ice_factory_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No ice factories added yet.</div>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-md-6 pt-4">
                    <label class="control-label"><strong>Exporters</strong></label>
                    <?php if (!empty($model->exporters)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->exporters as $index => $exporter): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($exporter->exporter_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No exporters added yet.</div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6 pt-4">
                    <label class="control-label"><strong>Boat Building Yards</strong></label>
                    <?php if (!empty($model->boatBuildingYards)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->boatBuildingYards as $index => $yard): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($yard->yard_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No boat building yards added yet.</div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6 pt-4">
                    <label class="control-label"><strong>Dry Fish Manufactures</strong></label>
                    <?php if (!empty($model->dryFishManufactures)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->dryFishManufactures as $index => $manufacture): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($manufacture->manufacture_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No dry fish manufactures added yet.</div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6 pt-4">
                    <label class="control-label"><strong>Fisheries Cooperate Societies</strong></label>
                    <?php if (!empty($model->fisheriesCooperateSocieties)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->fisheriesCooperateSocieties as $index => $society): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($society->cooperate_society_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No fisheries cooperate societies added yet.</div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6 pt-4">
                    <label class="control-label"><strong>Fisheries Rural Societies</strong></label>
                    <?php if (!empty($model->fisheriesRuralSocieties)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->fisheriesRuralSocieties as $index => $ruralSociety): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($ruralSociety->rural_society_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No fisheries rural societies added yet.</div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6 pt-4">
                    <label class="control-label"><strong>Government Offices</strong></label>
                    <?php if (!empty($model->governmentOffices)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->governmentOffices as $index => $office): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($office->office_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No government offices added yet.</div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6 pt-4">
                    <label class="control-label"><strong>Fisheries Roads</strong></label>
                    <?php if (!empty($model->fisheriesRoads)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->fisheriesRoads as $index => $road): ?>
                                <div class="mb-2">
                                    <div class="border rounded p-2 bg-light">
                                        <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($road->road_name) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No fisheries roads added yet.</div>
                    <?php endif; ?>
                </div>
                    
                <div class="col-12 m-0 p-0">
                    
                    <div class="col-12 col-md-6 pt-4">
                        <label class="control-label"><strong>Others</strong></label>
                        <?php if (!empty($model->others)): ?>
                            <div class="mt-2">
                                <?php foreach ($model->others as $index => $other): ?>
                                    <div class="mb-2">
                                        <div class="border rounded p-2 bg-light">
                                            <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($other->other_name) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="border rounded p-2 bg-light text-muted mt-2">No other resources added yet.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 col-md-6 pt-4">
                    <label class="control-label"><strong>Police Stations</strong></label>
                    <?php if (!empty($model->policeStations)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->policeStations as $index => $station): ?>
                                <div class="mb-2">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="border rounded p-2 bg-light w-100">
                                                <strong><?= $index + 1 ?>.</strong>&nbsp;<?= Html::encode($station->police_station_name) ?>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mt-2 mt-md-0">
                                            <div class="border rounded p-2 bg-light w-100">
                                                <?= Html::encode($station->phone_number) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No police stations added yet.</div>
                    <?php endif; ?>
                </div>
                
                <div class="col-12 col-md-6 pt-4">
                    <label class="control-label"><strong>Special Projects</strong></label>
                    <?php if (!empty($model->specialProjects)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->specialProjects as $index => $project): ?>
                                <div class="mb-3 border rounded p-3 bg-light">
                                    <div class="mb-2">
                                        <strong><?= $index + 1 ?>. <?= Html::encode($project->project_name) ?></strong>
                                    </div>
                                    <div class="text-muted">
                                        <?= Html::encode($project->project_description) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No special projects added yet.</div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6 pt-4">
                    <label class="control-label"><strong>Traditional Fishing</strong></label>
                    <?php if (!empty($model->traditionalFishings)): ?>
                        <div class="mt-2">
                            <?php foreach ($model->traditionalFishings as $index => $fishing): ?>
                                <div class="mb-3 border rounded p-3 bg-light">
                                    <div class="mb-2">
                                        <strong><?= $index + 1 ?>. <?= Html::encode($fishing->traditional_fishing_name) ?></strong>
                                    </div>
                                    <div class="text-muted">
                                        <?= Html::encode($fishing->traditional_fishing_description) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="border rounded p-2 bg-light text-muted mt-2">No traditional fishing added yet.</div>
                    <?php endif; ?>
                </div>

                

            </div>
        </div>
    </div>
</div>
