<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var backend\models\MFiDistrict[] $districts */
/** @var backend\models\MDivision[] $divisions */
/** @var array $stats */
/** @var int|null $selectedDistrictId */
/** @var int|null $selectedDivisionId */

$this->title = 'Resource Profile Dashboard';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
.resource-profile-dashboard {
    width: 100%;
    max-width: 100%;
    padding: 0 15px;
}
.stats-card {
    height: 150px;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    position: relative;
}
.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
.stats-card h3 {
    font-size: 14px;
    color: #666;
    margin: 0 0 10px 0;
    font-weight: 500;
    text-transform: uppercase;
}
.stats-card .value {
    font-size: 32px;
    font-weight: bold;
    color: #333;
    margin: 0;
}
.stats-card .spinner-container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: none;
}
.stats-card.loading .spinner-container {
    display: block;
}
.stats-card.loading .value {
    visibility: hidden;
}
.spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #007bff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.dropdown-select {
    width: 100%;
    max-width: 400px;
    height: 45px;
    font-size: 16px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    padding: 8px 40px 8px 15px;
    background: white;
    cursor: pointer;
    appearance: none;
    background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e');
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 20px;
}
.dropdown-select:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
    opacity: 0.6;
}
.dropdown-select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}
.dropdown-container {
    margin-bottom: 15px;
}
.dropdown-label {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 10px;
    display: block;
}
");

$this->registerJs("
var selectedDistrictId = null;
var selectedDivisionId = null;

// Function to update statistics on the page
function updateStats(stats) {
    $('.stats-card:eq(0) .value').text(formatNumber(stats.total_fishing_population || 0));
    $('.stats-card:eq(1) .value').text(formatNumber(stats.total_fisheries_families || 0));
    $('.stats-card:eq(2) .value').text(formatNumber(stats.total_active_fishermen || 0));
    $('.stats-card:eq(3) .value').text(formatNumber(stats.total_fishing_households || 0));
    $('.stats-card:eq(4) .value').text(formatNumber(stats.total_fishing_households_without_sanitary || 0));
    $('.stats-card:eq(5) .value').text(formatNumber(stats.total_fishing_households_without_drinking_water || 0));
}

// Function to format numbers with commas
function formatNumber(num) {
    return parseInt(num).toLocaleString();
}

// Function to update the title based on selection
function updateTitle(districtName) {
    var title = districtName 
        ? 'Fishing Statistics for ' + districtName + ' District'
        : 'Fishing Statistics Across All Districts';
    $('.resource-profile-dashboard h5').text(title);
}

// Load statistics via AJAX
function loadStats(districtId) {
    $.ajax({
        url: '" . Url::to(['get-stats-ajax']) . "',
        type: 'GET',
        data: { districtId: districtId || '' },
        dataType: 'json',
        beforeSend: function() {
            // Show loading spinners
            $('.stats-card').addClass('loading');
        },
        success: function(response) {
            if (response.stats) {
                updateStats(response.stats);
            }
        },
        error: function() {
            alert('Error loading statistics. Please try again.');
        },
        complete: function() {
            // Hide loading spinners
            $('.stats-card').removeClass('loading');
        }
    });
}

// Handle district selection change
$('#district-select').on('change', function() {
    selectedDistrictId = $(this).val();
    selectedDivisionId = null;
    
    if (selectedDistrictId) {
        // Load divisions and stats via AJAX
        $('#division-select').prop('disabled', false);
        loadDivisions(selectedDistrictId);
        loadStats(selectedDistrictId);
        
        // Update title
        var districtName = $('#district-select option:selected').text();
        updateTitle(districtName);
        
        // Update URL without reload
        var newUrl = '" . Url::to(['dashboard-all']) . "?districtId=' + selectedDistrictId;
        history.pushState({districtId: selectedDistrictId}, '', newUrl);
    } else {
        // Disable division dropdown and load all districts stats
        $('#division-select').prop('disabled', true);
        $('#division-select').html('<option value=\"\">Select Division</option>');
        loadStats(null);
        updateTitle(null);
        
        // Update URL without reload
        var newUrl = '" . Url::to(['dashboard-all']) . "';
        history.pushState({}, '', newUrl);
    }
});

// Handle division selection change
$('#division-select').on('change', function() {
    selectedDivisionId = $(this).val();
    
    if (selectedDivisionId && selectedDistrictId) {
        // Navigate to division view (this requires full page load)
        window.location.href = '" . Url::to(['view-division-all']) . "?id=' + selectedDivisionId;
    }
});

// Load divisions via AJAX
function loadDivisions(districtId) {
    $.ajax({
        url: '" . Url::to(['get-divisions-by-district']) . "',
        type: 'GET',
        data: { districtId: districtId },
        dataType: 'json',
        success: function(response) {
            var divisionSelect = $('#division-select');
            divisionSelect.html('<option value=\"\">Select Division</option>');
            
            if (response.divisions && response.divisions.length > 0) {
                $.each(response.divisions, function(index, division) {
                    divisionSelect.append(
                        $('<option></option>').val(division.id).text(division.name)
                    );
                });
            }
        },
        error: function() {
            alert('Error loading divisions. Please try again.');
        }
    });
}

// Handle browser back/forward buttons
window.addEventListener('popstate', function(event) {
    if (event.state && event.state.districtId) {
        selectedDistrictId = event.state.districtId;
        $('#district-select').val(selectedDistrictId);
        $('#division-select').prop('disabled', false);
        loadDivisions(selectedDistrictId);
        loadStats(selectedDistrictId);
        var districtName = $('#district-select option:selected').text();
        updateTitle(districtName);
    } else {
        selectedDistrictId = null;
        $('#district-select').val('');
        $('#division-select').prop('disabled', true);
        $('#division-select').html('<option value=\"\">Select Division</option>');
        loadStats(null);
        updateTitle(null);
    }
});

// Initialize on page load
$(document).ready(function() {
    var initialDistrictId = " . ($selectedDistrictId ? $selectedDistrictId : 'null') . ";
    
    if (initialDistrictId) {
        selectedDistrictId = initialDistrictId;
        $('#district-select').val(initialDistrictId);
        $('#division-select').prop('disabled', false);
        loadDivisions(initialDistrictId);
    }
});
", \yii\web\View::POS_END);
?>

<div class="resource-profile-dashboard">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="dropdown-container">
                <label for="district-select" class="dropdown-label">
                    Select District
                </label>
                <select id="district-select" class="dropdown-select">
                    <option value="">All Districts</option>
                    <?php foreach ($districts as $district): ?>
                        <option value="<?= $district->id ?>" <?= $selectedDistrictId == $district->id ? 'selected' : '' ?>>
                            <?= Html::encode($district->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="dropdown-container">
                <label for="division-select" class="dropdown-label">
                    Select Division
                </label>
                <select id="division-select" class="dropdown-select" <?= !$selectedDistrictId ? 'disabled' : '' ?>>
                    <option value="">Select Division</option>
                    <?php if ($selectedDistrictId && !empty($divisions)): ?>
                        <?php foreach ($divisions as $division): ?>
                            <option value="<?= $division->id ?>" <?= $selectedDivisionId == $division->id ? 'selected' : '' ?>>
                                <?= Html::encode($division->name) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>

    <h5 class="mb-3" style="color: #666;">
        <?php if ($selectedDistrictId): ?>
            <?php 
                $selectedDistrict = array_filter($districts, function($d) use ($selectedDistrictId) {
                    return $d->id == $selectedDistrictId;
                });
                $selectedDistrict = reset($selectedDistrict);
            ?>
            Fishing Statistics for <?= Html::encode($selectedDistrict->name) ?> District
        <?php else: ?>
            Fishing Statistics Across All Districts
        <?php endif; ?>
    </h5>

    <div class="row">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="stats-card" style="border-left: 4px solid #007bff;">
                <div class="spinner-container">
                    <div class="spinner"></div>
                </div>
                <h3>Fishing Population</h3>
                <p class="value"><?= number_format($stats['total_fishing_population'] ?? 0) ?></p>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="stats-card" style="border-left: 4px solid #28a745;">
                <div class="spinner-container">
                    <div class="spinner"></div>
                </div>
                <h3>Fisheries Families</h3>
                <p class="value"><?= number_format($stats['total_fisheries_families'] ?? 0) ?></p>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="stats-card" style="border-left: 4px solid #ffc107;">
                <div class="spinner-container">
                    <div class="spinner"></div>
                </div>
                <h3>Active Fishermen</h3>
                <p class="value"><?= number_format($stats['total_active_fishermen'] ?? 0) ?></p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="stats-card" style="border-left: 4px solid #17a2b8;">
                <div class="spinner-container">
                    <div class="spinner"></div>
                </div>
                <h3>Fishing Households</h3>
                <p class="value"><?= number_format($stats['total_fishing_households'] ?? 0) ?></p>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="stats-card" style="border-left: 4px solid #dc3545;">
                <div class="spinner-container">
                    <div class="spinner"></div>
                </div>
                <h3>Fishing Households Without Sanitary</h3>
                <p class="value"><?= number_format($stats['total_fishing_households_without_sanitary'] ?? 0) ?></p>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="stats-card" style="border-left: 4px solid #6f42c1;">
                <div class="spinner-container">
                    <div class="spinner"></div>
                </div>
                <h3>Fishing Households Without Drinking Water</h3>
                <p class="value"><?= number_format($stats['total_fishing_households_without_drinking_water'] ?? 0) ?></p>
            </div>
        </div>
    </div>
</div>