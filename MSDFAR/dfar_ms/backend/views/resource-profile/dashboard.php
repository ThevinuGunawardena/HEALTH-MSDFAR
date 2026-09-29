<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var backend\models\MDivision[] $divisions */
/** @var array $stats */

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
.division-dropdown {
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
.division-dropdown:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}
");

$this->registerJs("
$('#division-select').on('change', function() {
    var divisionId = $(this).val();
    if (divisionId) {
        window.location.href = '" . Url::to(['view-division']) . "?id=' + divisionId;
    }
});
", \yii\web\View::POS_END);
?>

<!-- <div class="container-fluid"> -->
    <div class="resource-profile-dashboard">
        <div class="row mb-4">
            <div class="col-12">
                <label for="division-select" style="font-weight: 600; font-size: 16px; margin-bottom: 10px; display: block;">
                    Select Division
                </label>
                <select id="division-select" class="division-dropdown">
                    <option value="">All Divisions</option>
                    <?php foreach ($divisions as $division): ?>
                        <option value="<?= $division->id ?>">
                            <?= Html::encode($division->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <h5 class="mb-3" style="color: #666;">Fishing Statistics Across All Divisions</h5>

        <div class="row">
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="stats-card" style="border-left: 4px solid #007bff;">
                    <h3>Fishing Population</h3>
                    <p class="value"><?= number_format($stats['total_fishing_population'] ?? 0) ?></p>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="stats-card" style="border-left: 4px solid #28a745;">
                    <h3>Fisheries Families</h3>
                    <p class="value"><?= number_format($stats['total_fisheries_families'] ?? 0) ?></p>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="stats-card" style="border-left: 4px solid #ffc107;">
                    <h3>Active Fishermen</h3>
                    <p class="value"><?= number_format($stats['total_active_fishermen'] ?? 0) ?></p>
                </div>
            </div>
        </div>

        <div class="row">
            

            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="stats-card" style="border-left: 4px solid #17a2b8;">
                    <h3>Fishing Households</h3>
                    <p class="value"><?= number_format($stats['total_fishing_households'] ?? 0) ?></p>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="stats-card" style="border-left: 4px solid #dc3545;">
                    <h3>Fishing Households Without Sanitary</h3>
                    <p class="value"><?= number_format($stats['total_fishing_households_without_sanitary'] ?? 0) ?></p>
                </div>
            </div>

            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="stats-card" style="border-left: 4px solid #6f42c1;">
                    <h3>Fishing Households Without Drinking Water</h3>
                    <p class="value"><?= number_format($stats['total_fishing_households_without_drinking_water'] ?? 0) ?></p>
                </div>
            </div>
        </div>

        <div class="row">
            
        </div>
    <!-- </div> -->
</div>
