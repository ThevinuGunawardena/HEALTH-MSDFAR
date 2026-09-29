<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ResourceProfile $model */
/** @var backend\models\ResourceProfileFishingVillages[] $fishingVillages */
/** @var backend\models\ResourceProfileLandingSites[] $landingSites */
/** @var backend\models\ResourceProfileBeachSites[] $beachSites */
/** @var backend\models\ResourceProfileIceFactories[] $iceFactories */
/** @var backend\models\ResourceProfileExporters[] $exporters */
/** @var backend\models\ResourceProfileBoatBuildingYards[] $boatBuildingYards */
/** @var backend\models\ResourceProfileDryFishManufactures[] $dryFishManufactures */
/** @var backend\models\ResourceProfileFisheriesCooperateSociety[] $fisheriesCooperateSocieties */
/** @var backend\models\ResourceProfileFisheriesRuralSociety[] $fisheriesRuralSocieties */
/** @var backend\models\ResourceProfileGovernmentOffices[] $governmentOffices */
/** @var backend\models\ResourceProfileFisheriesRoads[] $fisheriesRoads */
/** @var backend\models\ResourceProfileOthers[] $others */
/** @var backend\models\ResourceProfilePoliceStations[] $policeStations */
/** @var backend\models\ResourceProfileSpecialProjects[] $specialProjects */
/** @var backend\models\ResourceProfileTraditionalFishing[] $traditionalFishings */


$this->title = 'Edit Resource Profile';
$this->params['breadcrumbs'][] = ['label' => 'Profile Officers', 'url' => ['profile-officers/index']];
$this->params['breadcrumbs'][] = ['label' => 'Resource Profile', 'url' => ['view']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <?= $this->render('_form', [
                'model' => $model,
                'fishingVillages' => $fishingVillages,
                'landingSites' => $landingSites,
                'beachSites' => $beachSites,
                'iceFactories' => $iceFactories,
                'exporters' => $exporters,
                'boatBuildingYards' => $boatBuildingYards,
                'dryFishManufactures' => $dryFishManufactures,
                'fisheriesCooperateSocieties' => $fisheriesCooperateSocieties,
                'fisheriesRuralSocieties' => $fisheriesRuralSocieties,
                'governmentOffices' => $governmentOffices,
                'fisheriesRoads' => $fisheriesRoads,
                'others' => $others,
                'policeStations' => $policeStations,
                'specialProjects' => $specialProjects,
                'traditionalFishings' => $traditionalFishings,
                'gramaNiladariWasams' => $gramaNiladariWasams,
            ]) ?>
        </div>
    </div>
</div>
