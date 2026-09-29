<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

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

/** @var ActiveForm $form */

// Register Select2 CSS and JS
$this->registerCssFile('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerCss("
.select2-container--default {
    width: 100% !important;
    font-size: 1rem;
}

.select2-container--default.select2-container--open .select2-selection--single {
    border-radius: 0.25rem 0.25rem 0 0;
}

.select2-container--default .select2-selection--single {
    height: auto !important;
    min-height: 38px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    padding: 0 !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    padding: 0.375rem 0.75rem !important;
    line-height: 1.5 !important;
    color: #495057;
}

.select2-container--default .select2-selection--single .select2-selection__clear {
    display: none !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    display: none !important;
}

.select2-container--default .select2-selection--single::after {
    content: '';
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 6px solid #999;
    pointer-events: none;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.select2-container--default .select2-dropdown--below {
    border-top: none;
    margin-top: -1px;
    border-radius: 0 0 0.25rem 0.25rem;
}

.select2-search--dropdown {
    padding: 4px 8px;
}

.select2-search--dropdown .select2-search__field {
    padding: 6px 10px !important;
    font-size: 0.9rem;
    border: 1px solid #ced4da !important;
    border-radius: 0.2rem;
    width: 100% !important;
    height: auto !important;
}

.select2-results__options {
    max-height: 200px;
    font-size: 0.9rem;
}

.select2-results__option {
    padding: 8px 10px;
    line-height: 1.5;
}

.select2-results__option--highlighted[aria-selected] {
    background-color: #007bff !important;
    color: white !important;
}

.select2-results__option[aria-selected=true] {
    background-color: #f0f0f0;
    color: #333;
}

.select2-results__option[role='group'] {
    padding: 0;
}

.select2-results__group {
    padding: 8px 10px;
    font-weight: bold;
    color: #666;
}
");

$this->registerJs("
$(document).ready(function() {
    $('#resourceprofile-m_division_id').select2({
        placeholder: 'Search and select FI Division Area',
        allowClear: true,
        width: '100%'
    });
});
", \yii\web\View::POS_END);

$this->registerJs("
function addFishingVillage() {
    var container = $('#fishing-villages-container');
    var index = container.find('.fishing-village-item').length;
    var template = `
        <div class=\"fishing-village-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"fishingVillages[\${index}][village_name]\" class=\"form-control\" placeholder=\"Enter village name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-village\" onclick=\"removeFishingVillage(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeFishingVillage(button) {
    $(button).closest('.fishing-village-item').remove();
}

function addLandingSite() {
    var container = $('#landing-sites-container');
    var index = container.find('.landing-site-item').length;
    var template = `
        <div class=\"landing-site-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"landingSites[\${index}][landing_site_name]\" class=\"form-control\" placeholder=\"Enter landing site name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-site\" onclick=\"removeLandingSite(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeLandingSite(button) {
    $(button).closest('.landing-site-item').remove();
}

function addBeachSite() {
    var container = $('#beach-sites-container');
    var index = container.find('.beach-site-item').length;
    var template = `
        <div class=\"beach-site-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"beachSites[\${index}][beach_site_name]\" class=\"form-control\" placeholder=\"Enter beach site name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-beach-site\" onclick=\"removeBeachSite(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeBeachSite(button) {
    $(button).closest('.beach-site-item').remove();
}

function addIceFactory() {
    var container = $('#ice-factories-container');
    var index = container.find('.ice-factory-item').length;
    var template = `
        <div class=\"ice-factory-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"iceFactories[\${index}][ice_factory_name]\" class=\"form-control\" placeholder=\"Enter ice factory name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-ice-factory\" onclick=\"removeIceFactory(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeIceFactory(button) {
    $(button).closest('.ice-factory-item').remove();
}

function addExporter() {
    var container = $('#exporters-container');
    var index = container.find('.exporter-item').length;
    var template = `
        <div class=\"exporter-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"exporters[\${index}][exporter_name]\" class=\"form-control\" placeholder=\"Enter exporter name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-exporter\" onclick=\"removeExporter(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeExporter(button) {
    $(button).closest('.exporter-item').remove();
}

function addBoatBuildingYard() {
    var container = $('#boat-building-yards-container');
    var index = container.find('.boat-building-yard-item').length;
    var template = `
        <div class=\"boat-building-yard-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"boatBuildingYards[\${index}][yard_name]\" class=\"form-control\" placeholder=\"Enter yard name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-yard\" onclick=\"removeBoatBuildingYard(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeBoatBuildingYard(button) {
    $(button).closest('.boat-building-yard-item').remove();
}

function addDryFishManufacture() {
    var container = $('#dry-fish-manufactures-container');
    var index = container.find('.dry-fish-manufacture-item').length;
    var template = `
        <div class=\"dry-fish-manufacture-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"dryFishManufactures[\${index}][manufacture_name]\" class=\"form-control\" placeholder=\"Enter manufacture name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-manufacture\" onclick=\"removeDryFishManufacture(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeDryFishManufacture(button) {
    $(button).closest('.dry-fish-manufacture-item').remove();
}

function addFisheriesCooperateSociety() {
    var container = $('#fisheries-cooperate-societies-container');
    var index = container.find('.fisheries-cooperate-society-item').length;
    var template = `
        <div class=\"fisheries-cooperate-society-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"fisheriesCooperateSocieties[\${index}][cooperate_society_name]\" class=\"form-control\" placeholder=\"Enter cooperate society name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-cooperate-society\" onclick=\"removeFisheriesCooperateSociety(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeFisheriesCooperateSociety(button) {
    $(button).closest('.fisheries-cooperate-society-item').remove();
}

function addFisheriesRuralSociety() {
    var container = $('#fisheries-rural-societies-container');
    var index = container.find('.fisheries-rural-society-item').length;
    var template = `
        <div class=\"fisheries-rural-society-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"fisheriesRuralSocieties[\${index}][rural_society_name]\" class=\"form-control\" placeholder=\"Enter rural society name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-rural-society\" onclick=\"removeFisheriesRuralSociety(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeFisheriesRuralSociety(button) {
    $(button).closest('.fisheries-rural-society-item').remove();
}

function addGovernmentOffice() {
    var container = $('#government-offices-container');
    var index = container.find('.government-office-item').length;
    var template = `
        <div class=\"government-office-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"governmentOffices[\${index}][office_name]\" class=\"form-control\" placeholder=\"Enter office name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-office\" onclick=\"removeGovernmentOffice(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeGovernmentOffice(button) {
    $(button).closest('.government-office-item').remove();
}

function addFisheriesRoad() {
    var container = $('#fisheries-roads-container');
    var index = container.find('.fisheries-road-item').length;
    var template = `
        <div class=\"fisheries-road-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"fisheriesRoads[\${index}][road_name]\" class=\"form-control\" placeholder=\"Enter road name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-road\" onclick=\"removeFisheriesRoad(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeFisheriesRoad(button) {
    $(button).closest('.fisheries-road-item').remove();
}

function addOther() {
    var container = $('#others-container');
    var index = container.find('.other-item').length;
    var template = `
        <div class=\"other-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"others[\${index}][other_name]\" class=\"form-control\" placeholder=\"Enter other name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-other\" onclick=\"removeOther(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeOther(button) {
    $(button).closest('.other-item').remove();
}

function addPoliceStation() {
    var container = $('#police-stations-container');
    var index = container.find('.police-station-item').length;
    var template = `
        <div class=\"police-station-item mb-2\">
            <div class=\"row\">
                <div class=\"col-md-6\">
                    <input type=\"text\" name=\"policeStations[\${index}][police_station_name]\" class=\"form-control\" placeholder=\"Enter police station name\" required>
                </div>
                <div class=\"col-md-6\">
                    <div class=\"input-group\">
                        <input type=\"tel\" name=\"policeStations[\${index}][phone_number]\" class=\"form-control phone-number-input\" placeholder=\"Enter phone number\" maxlength=\"10\" pattern=\"[0-9]{0,10}\" inputmode=\"numeric\" onkeypress=\"return /[0-9]/.test(String.fromCharCode(event.which))\">
                        <div class=\"input-group-append\">
                            <button type=\"button\" class=\"btn btn-danger btn-sm remove-police-station\" onclick=\"removePoliceStation(this)\">
                                <i class=\"fa fa-trash\"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removePoliceStation(button) {
    $(button).closest('.police-station-item').remove();
}

function addSpecialProject() {
    var container = $('#special-projects-container');
    var index = container.find('.special-project-item').length;
    var template = `
        <div class=\"special-project-item mb-3 border rounded p-3\">
            <div class=\"mb-2\">
                <input type=\"text\" name=\"specialProjects[\${index}][project_name]\" class=\"form-control\" placeholder=\"Enter project name\" required>
            </div>
            <div class=\"mb-2\">
                <textarea name=\"specialProjects[\${index}][project_description]\" class=\"form-control\" rows=\"3\" placeholder=\"Enter project description\" required></textarea>
            </div>
            <div>
                <button type=\"button\" class=\"btn btn-danger btn-sm remove-special-project\" onclick=\"removeSpecialProject(this)\">
                    <i class=\"fa fa-trash\"></i> Remove
                </button>
            </div>
        </div>
    `;
    container.append(template);
}

function removeSpecialProject(button) {
    $(button).closest('.special-project-item').remove();
}

function addTraditionalFishing() {
    var container = $('#traditional-fishings-container');
    var index = container.find('.traditional-fishing-item').length;
    var template = `
        <div class=\"traditional-fishing-item mb-3 border rounded p-3\">
            <div class=\"mb-2\">
                <input type=\"text\" name=\"traditionalFishings[\${index}][traditional_fishing_name]\" class=\"form-control\" placeholder=\"Enter fishing name\" required>
            </div>
            <div class=\"mb-2\">
                <textarea name=\"traditionalFishings[\${index}][traditional_fishing_description]\" class=\"form-control\" rows=\"3\" placeholder=\"Enter fishing description\" required></textarea>
            </div>
            <div>
                <button type=\"button\" class=\"btn btn-danger btn-sm remove-traditional-fishing\" onclick=\"removeTraditionalFishing(this)\">
                    <i class=\"fa fa-trash\"></i> Remove
                </button>
            </div>
        </div>
    `;
    container.append(template);
}

function removeTraditionalFishing(button) {
    $(button).closest('.traditional-fishing-item').remove();
}

function addGramaNiladariWasam() {
    var container = $('#grama-niladari-wasams-container');
    var index = container.find('.grama-niladari-wasam-item').length;
    var template = `
        <div class=\"grama-niladari-wasam-item mb-2\">
            <div class=\"input-group\">
                <input type=\"text\" name=\"gramaNiladariWasams[\${index}][wasam_name]\" class=\"form-control\" placeholder=\"Enter wasam name\" required>
                <div class=\"input-group-append\">
                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-wasam\" onclick=\"removeGramaNiladariWasam(this)\">
                        <i class=\"fa fa-trash\"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.append(template);
}

function removeGramaNiladariWasam(button) {
    $(button).closest('.grama-niladari-wasam-item').remove();
}

", \yii\web\View::POS_END);
?>

<div class="resource-profile-form">
    <?php $form = ActiveForm::begin(); ?>

    <h6 class="text-muted mb-3">Location Details</h6>
    <div class="row">
        <div class="col-12 col-md-6">
            <?= $form->field($model, 'm_division_id')->dropDownList(
                \yii\helpers\ArrayHelper::map(\backend\models\MDivision::find()->where(['status' => 1])->all(), 'id', 'name'),
                ['prompt' => 'Select FI Division Area', 'class' => 'form-control']
            ) ?>
        </div>
        <div class="col-12 col-md-6">
            <label class="control-label">Grama Niladari Wasam</label>
            <div id="grama-niladari-wasams-container">
                <?php if (!empty($gramaNiladariWasams)): ?>
                    <?php foreach ($gramaNiladariWasams as $index => $wasam): ?>
                        <div class="grama-niladari-wasam-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="gramaNiladariWasams[<?= $index ?>][id]" value="<?= $wasam->id ?>">
                                <input type="text" name="gramaNiladariWasams[<?= $index ?>][wasam_name]" class="form-control" 
                                       value="<?= Html::encode($wasam->wasam_name) ?>" placeholder="Enter wasam name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-wasam" onclick="removeGramaNiladariWasam(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-2" onclick="addGramaNiladariWasam()">
                <i class="fa fa-plus"></i> Add Grama Niladari Wasam
            </button>
        </div>
    </div>

    <div class="row pt-4">
        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Fishing Villages</label>
            <div id="fishing-villages-container">
                <?php if (!empty($fishingVillages)): ?>
                    <?php foreach ($fishingVillages as $index => $village): ?>
                        <div class="fishing-village-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="fishingVillages[<?= $index ?>][id]" value="<?= $village->id ?>">
                                <input type="text" name="fishingVillages[<?= $index ?>][village_name]" class="form-control" 
                                       value="<?= Html::encode($village->village_name) ?>" placeholder="Enter village name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-village" onclick="removeFishingVillage(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addFishingVillage()">
                <i class="fa fa-plus"></i> Add Fishing Village
            </button>
        </div>
    </div>

    <h6 class="text-muted mb-3 mt-4">Fishing Statistics</h6>
    <div class="row">
        <div class="col-12 col-md-6">
            <?= $form->field($model, 'fishing_population')->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control']) ?>
        </div>
        <div class="col-12 col-md-6">
            <?= $form->field($model, 'fisheries_families')->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <?= $form->field($model, 'active_fishermen')->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control']) ?>
        </div>
        <div class="col-12 col-md-6">
            <?= $form->field($model, 'fishing_households')->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <?= $form->field($model, 'fishing_households_without_sanitary')->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control']) ?>
        </div>
        <div class="col-12 col-md-6">
            <?= $form->field($model, 'fishing_households_without_drinking_water')->textInput(['type' => 'number', 'min' => 0, 'class' => 'form-control']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Landing Sites</label>
            <div id="landing-sites-container">
                <?php if (!empty($landingSites)): ?>
                    <?php foreach ($landingSites as $index => $site): ?>
                        <div class="landing-site-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="landingSites[<?= $index ?>][id]" value="<?= $site->id ?>">
                                <input type="text" name="landingSites[<?= $index ?>][landing_site_name]" class="form-control" 
                                       value="<?= Html::encode($site->landing_site_name) ?>" placeholder="Enter landing site name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-site" onclick="removeLandingSite(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addLandingSite()">
                <i class="fa fa-plus"></i> Add Landing Site
            </button>
        </div>

        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Beach Sites</label>
            <div id="beach-sites-container">
                <?php if (!empty($beachSites)): ?>
                    <?php foreach ($beachSites as $index => $beachSite): ?>
                        <div class="beach-site-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="beachSites[<?= $index ?>][id]" value="<?= $beachSite->id ?>">
                                <input type="text" name="beachSites[<?= $index ?>][beach_site_name]" class="form-control" 
                                       value="<?= Html::encode($beachSite->beach_site_name) ?>" placeholder="Enter beach site name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-beach-site" onclick="removeBeachSite(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addBeachSite()">
                <i class="fa fa-plus"></i> Add Beach Site
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Ice Factories</label>
            <div id="ice-factories-container">
                <?php if (!empty($iceFactories)): ?>
                    <?php foreach ($iceFactories as $index => $iceFactory): ?>
                        <div class="ice-factory-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="iceFactories[<?= $index ?>][id]" value="<?= $iceFactory->id ?>">
                                <input type="text" name="iceFactories[<?= $index ?>][ice_factory_name]" class="form-control" 
                                       value="<?= Html::encode($iceFactory->ice_factory_name) ?>" placeholder="Enter ice factory name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-ice-factory" onclick="removeIceFactory(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addIceFactory()">
                <i class="fa fa-plus"></i> Add Ice Factory
            </button>
        </div>

        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Exporters</label>
            <div id="exporters-container">
                <?php if (!empty($exporters)): ?>
                    <?php foreach ($exporters as $index => $exporter): ?>
                        <div class="exporter-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="exporters[<?= $index ?>][id]" value="<?= $exporter->id ?>">
                                <input type="text" name="exporters[<?= $index ?>][exporter_name]" class="form-control" 
                                       value="<?= Html::encode($exporter->exporter_name) ?>" placeholder="Enter exporter name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-exporter" onclick="removeExporter(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addExporter()">
                <i class="fa fa-plus"></i> Add Exporter
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Boat Building Yards</label>
            <div id="boat-building-yards-container">
                <?php if (!empty($boatBuildingYards)): ?>
                    <?php foreach ($boatBuildingYards as $index => $yard): ?>
                        <div class="boat-building-yard-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="boatBuildingYards[<?= $index ?>][id]" value="<?= $yard->id ?>">
                                <input type="text" name="boatBuildingYards[<?= $index ?>][yard_name]" class="form-control" 
                                       value="<?= Html::encode($yard->yard_name) ?>" placeholder="Enter yard name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-yard" onclick="removeBoatBuildingYard(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addBoatBuildingYard()">
                <i class="fa fa-plus"></i> Add Boat Building Yard
            </button>
        </div>

        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Dry Fish Manufactures</label>
            <div id="dry-fish-manufactures-container">
                <?php if (!empty($dryFishManufactures)): ?>
                    <?php foreach ($dryFishManufactures as $index => $manufacture): ?>
                        <div class="dry-fish-manufacture-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="dryFishManufactures[<?= $index ?>][id]" value="<?= $manufacture->id ?>">
                                <input type="text" name="dryFishManufactures[<?= $index ?>][manufacture_name]" class="form-control" 
                                       value="<?= Html::encode($manufacture->manufacture_name) ?>" placeholder="Enter manufacture name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-manufacture" onclick="removeDryFishManufacture(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addDryFishManufacture()">
                <i class="fa fa-plus"></i> Add Dry Fish Manufacture
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Fisheries Cooperate Societies</label>
            <div id="fisheries-cooperate-societies-container">
                <?php if (!empty($fisheriesCooperateSocieties)): ?>
                    <?php foreach ($fisheriesCooperateSocieties as $index => $society): ?>
                        <div class="fisheries-cooperate-society-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="fisheriesCooperateSocieties[<?= $index ?>][id]" value="<?= $society->id ?>">
                                <input type="text" name="fisheriesCooperateSocieties[<?= $index ?>][cooperate_society_name]" class="form-control" 
                                       value="<?= Html::encode($society->cooperate_society_name) ?>" placeholder="Enter cooperate society name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-cooperate-society" onclick="removeFisheriesCooperateSociety(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addFisheriesCooperateSociety()">
                <i class="fa fa-plus"></i> Add Fisheries Cooperate Society
            </button>
        </div>

        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Fisheries Rural Societies</label>
            <div id="fisheries-rural-societies-container">
                <?php if (!empty($fisheriesRuralSocieties)): ?>
                    <?php foreach ($fisheriesRuralSocieties as $index => $ruralSociety): ?>
                        <div class="fisheries-rural-society-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="fisheriesRuralSocieties[<?= $index ?>][id]" value="<?= $ruralSociety->id ?>">
                                <input type="text" name="fisheriesRuralSocieties[<?= $index ?>][rural_society_name]" class="form-control" 
                                       value="<?= Html::encode($ruralSociety->rural_society_name) ?>" placeholder="Enter rural society name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-rural-society" onclick="removeFisheriesRuralSociety(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addFisheriesRuralSociety()">
                <i class="fa fa-plus"></i> Add Fisheries Rural Society
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Government Offices</label>
            <div id="government-offices-container">
                <?php if (!empty($governmentOffices)): ?>
                    <?php foreach ($governmentOffices as $index => $office): ?>
                        <div class="government-office-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="governmentOffices[<?= $index ?>][id]" value="<?= $office->id ?>">
                                <input type="text" name="governmentOffices[<?= $index ?>][office_name]" class="form-control" 
                                       value="<?= Html::encode($office->office_name) ?>" placeholder="Enter office name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-office" onclick="removeGovernmentOffice(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addGovernmentOffice()">
                <i class="fa fa-plus"></i> Add Government Office
            </button>
        </div>

        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Fisheries Roads</label>
            <div id="fisheries-roads-container">
                <?php if (!empty($fisheriesRoads)): ?>
                    <?php foreach ($fisheriesRoads as $index => $road): ?>
                        <div class="fisheries-road-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="fisheriesRoads[<?= $index ?>][id]" value="<?= $road->id ?>">
                                <input type="text" name="fisheriesRoads[<?= $index ?>][road_name]" class="form-control" 
                                       value="<?= Html::encode($road->road_name) ?>" placeholder="Enter road name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-road" onclick="removeFisheriesRoad(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addFisheriesRoad()">
                <i class="fa fa-plus"></i> Add Fisheries Road
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Others</label>
            <div id="others-container">
                <?php if (!empty($others)): ?>
                    <?php foreach ($others as $index => $other): ?>
                        <div class="other-item mb-2">
                            <div class="input-group">
                                <input type="hidden" name="others[<?= $index ?>][id]" value="<?= $other->id ?>">
                                <input type="text" name="others[<?= $index ?>][other_name]" class="form-control" 
                                       value="<?= Html::encode($other->other_name) ?>" placeholder="Enter other name" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger btn-sm remove-other" onclick="removeOther(this)">
                                        <i class="fa fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addOther()">
                <i class="fa fa-plus"></i> Add Other
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Police Stations</label>
            <div id="police-stations-container">
                <?php if (!empty($policeStations)): ?>
                    <?php foreach ($policeStations as $index => $station): ?>
                        <div class="police-station-item mb-2">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <input type="hidden" name="policeStations[<?= $index ?>][id]" value="<?= $station->id ?>">
                                    <input type="text" name="policeStations[<?= $index ?>][police_station_name]" class="form-control" 
                                           value="<?= Html::encode($station->police_station_name) ?>" placeholder="Enter police station name" required>
                                </div>
                                <div class="col-12 col-md-6 mt-2 mt-md-0">
                                    <div class="input-group">
                                        <input type="tel" name="policeStations[<?= $index ?>][phone_number]" class="form-control phone-number-input" 
                                               value="<?= Html::encode($station->phone_number) ?>" placeholder="Enter phone number" maxlength="10" pattern="[0-9]{0,10}" inputmode="numeric" onkeypress="return /[0-9]/.test(String.fromCharCode(event.which))">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger btn-sm remove-police-station" onclick="removePoliceStation(this)">
                                                <i class="fa fa-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addPoliceStation()">
                <i class="fa fa-plus"></i> Add Police Station
            </button>
        </div>

        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Special Projects</label>
            <div id="special-projects-container">
                <?php if (!empty($specialProjects)): ?>
                    <?php foreach ($specialProjects as $index => $project): ?>
                        <div class="special-project-item mb-3 border rounded p-3">
                            <input type="hidden" name="specialProjects[<?= $index ?>][id]" value="<?= $project->id ?>">
                            <div class="mb-2">
                                <input type="text" name="specialProjects[<?= $index ?>][project_name]" class="form-control" 
                                       value="<?= Html::encode($project->project_name) ?>" placeholder="Enter project name" required>
                            </div>
                            <div class="mb-2">
                                <textarea name="specialProjects[<?= $index ?>][project_description]" class="form-control" rows="3"
                                       placeholder="Enter project description" required><?= Html::encode($project->project_description) ?></textarea>
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger btn-sm remove-special-project" onclick="removeSpecialProject(this)">
                                    <i class="fa fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addSpecialProject()">
                <i class="fa fa-plus"></i> Add Special Project
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-6">
            <label class="control-label mb-2">Traditional Fishings</label>
            <div id="traditional-fishings-container">
                <?php if (!empty($traditionalFishings)): ?>
                    <?php foreach ($traditionalFishings as $index => $fishing): ?>
                        <div class="traditional-fishing-item mb-3 border rounded p-3">
                            <input type="hidden" name="traditionalFishings[<?= $index ?>][id]" value="<?= $fishing->id ?>">
                            <div class="mb-2">
                                <input type="text" name="traditionalFishings[<?= $index ?>][traditional_fishing_name]" class="form-control" 
                                       value="<?= Html::encode($fishing->traditional_fishing_name) ?>" placeholder="Enter fishing name" required>
                            </div>
                            <div class="mb-2">
                                <textarea name="traditionalFishings[<?= $index ?>][traditional_fishing_description]" class="form-control" rows="3"
                                       placeholder="Enter fishing description" required><?= Html::encode($fishing->traditional_fishing_description) ?></textarea>
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger btn-sm remove-traditional-fishing" onclick="removeTraditionalFishing(this)">
                                    <i class="fa fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-3 mb-4 w-100" onclick="addTraditionalFishing()">
                <i class="fa fa-plus"></i> Add Traditional Fishing
            </button>
        </div>        
    </div>

    <div class="row pt-8">
        <div class="col-12 col-md-6"></div>
        <div class="col-12 col-md-3">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success btn-block' : 'btn btn-primary btn-block']) ?>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="form-group">
                <?= Html::a('Back to Profile', ['profile-officers/view', 'id' => Yii::$app->user->identity->profile_id], ['class' => 'btn btn-secondary btn-block']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
