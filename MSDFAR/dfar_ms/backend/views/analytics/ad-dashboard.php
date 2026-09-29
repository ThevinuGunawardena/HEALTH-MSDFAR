<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use miloschuman\highcharts\Highcharts;
use backend\models\MFiDistrict;

$this->title = 'Performance Dashboard';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss(<<<CSS
	.analytics-dashboard {
	    padding: 1.5rem;
	    background: #f9fafb;
	}
	.dashboard-header {
	    margin-bottom: 2rem;
	}
	.metric-card { 
	    background: #fff;
	    border-radius: 0.75rem;
	    padding: 1.5rem;
	    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
	    transition: transform 0.2s ease;
	}
	.metric-card:hover {
	    transform: translateY(-2px);
	    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
	}
	.metric-title {
	    color: #4B5563;
	    font-size: 0.875rem;
	    font-weight: 600;
	    text-transform: uppercase;
	    letter-spacing: 0.025em;
	}
	.metric-value {
	    color: #111827;
	    font-size: 2rem;
	    font-weight: 700;
	    margin: 0.75rem 0;
	    line-height: 1;
	}
	.trend {
	    display: flex;
	    align-items: center;
	    font-size: 0.875rem;
	    font-weight: 500;
	}
	.trend-up {
	    color: #059669;
	}
	.trend-down {
	    color: #DC2626;
	}
	.chart-container {
	    background: #fff;
	    border-radius: 0.75rem;
	    padding: 1.5rem;
	    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
	    margin-bottom: 1.5rem;
	}
	.chart-title {
	    font-size: 1.25rem;
	    font-weight: 600;
	    color: #111827;
	    margin-bottom: 1rem;
	}
	.summary-grid {
	    display: grid;
	    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
	    gap: 1.5rem;
	    margin-top: 2rem;
	}
	.summary-card {
	    padding: 1.5rem;
	    border-radius: 0.75rem;
	    background: #fff;
	    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
	}
	.metrics-wrapper {
	    display: grid;
	    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
	    gap: 1.5rem;
	    margin-bottom: 2rem;
	}
	.table-responsive { width: 100%; overflow-x: auto; }
	.table { min-width: 600px; }
	.no-data {
	    text-align: center;
	    padding: 2rem;
	    color: #6B7280;
	    font-style: italic;
	}
	@media (max-width: 1024px) {
	  .metrics-wrapper, .summary-grid { grid-template-columns: 1fr 1fr; }
	}
	@media (max-width: 768px) {
	    .analytics-dashboard { padding: 1rem; }
	    .metric-value { font-size: 1.5rem; }
	    .metrics-wrapper, .summary-grid { grid-template-columns: 1fr; }
	    .chart-container { padding: 1rem; }
	    .chart-title { font-size: 1rem; }
	}
	@media (max-width: 480px) {
	    .analytics-dashboard { padding: 0.5rem; }
	    .metric-card, .chart-container, .summary-card { padding: 0.75rem; }
	    .metric-value { font-size: 1.1rem; }
	    .chart-title { font-size: 0.95rem; }
	}
	CSS);

$categoryLabels = [
    'skipper_license' => 'Skipper License',
    'boat_numbers' => 'Boat Numbers',
    'boat_registration' => 'Boat Registration',
    'national_license' => 'National License',
    'highseas_license' => 'Highseas License',
    'boat_cancel' => 'Boat Cancel Requests',
    'boat_transfer' => 'Boat Transfer Requests',
];

// Ensure data arrays exist and are properly formatted
$categoryData = $categoryData ?? [];
$approvedData = $approvedData ?? [];
$avgApprovalTimeData = $avgApprovalTimeData ?? [];
$metrics = $metrics ?? [];
$monthlyData = $monthlyData ?? ['categories' => [], 'pending' => [], 'approved' => []];
$districtData = $districtData ?? [];

// Helper function to safely get values
function safeGet($array, $key, $default = 0)
{
    return isset($array[$key]) ? (int) $array[$key] : $default;
}

function safeGetFloat($array, $key, $default = null)
{
    return isset($array[$key]) && $array[$key] !== null ? (float) $array[$key] : $default;
}
?>
<div class="analytics-dashboard">
    <div class="dashboard-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= Html::encode($this->title) ?></h1>
                <p class="text-gray-600 mt-2">Performance overview for approvals and actions</p>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="metrics-wrapper">
        <div class="metric-card">
            <p class="metric-title">Pending Approvals</p>
            <p class="metric-value"><?= safeGet($metrics, 'pendingApprovals') ?></p>
            <div class="trend"><span class="trend-down">Awaiting action</span></div>
        </div>
        <div class="metric-card">
            <p class="metric-title">Approved</p>
            <p class="metric-value"><?= safeGet($metrics, 'approvedByAD') ?></p>
            <div class="trend"><span class="trend-up">Completed</span></div>
        </div>
        <div class="metric-card">
            <p class="metric-title">Avg Approval Time</p>
            <p class="metric-value">
                <?= safeGetFloat($metrics, 'avgApprovalTime') !== null ? safeGetFloat($metrics, 'avgApprovalTime') . ' days' : 'N/A' ?>
            </p>
            <div class="trend"><span class="trend-up">Efficiency</span></div>
        </div>
        <div class="metric-card">
            <p class="metric-title">Approval Rate</p>
            <p class="metric-value"><?= safeGetFloat($metrics, 'approvalRate', 0) ?>%</p>
            <div class="trend"><span class="trend-up">Approval %</span></div>
        </div>
    </div>

    <!-- Pie Chart: Pending Approvals by Category -->
    <div class="chart-container">
        <h3 class="chart-title">Pending Approvals by Category</h3>
        <?php
        $pieData = [];
        $hasData = false;
        $totalPending = 0;

        foreach ($categoryLabels as $key => $label) {
            $value = safeGet($categoryData, $key);
            $totalPending += $value;
            if ($value > 0) {
                $hasData = true;
            }
            $pieData[] = [
                'name' => $label,
                'y' => $value
            ];
        }

        if ($hasData): ?>
            <?= Highcharts::widget([
                'options' => [
                    'chart' => [
                        'type' => 'pie',
                        'height' => 350
                    ],
                    'title' => ['text' => ''],
                    'plotOptions' => [
                        'pie' => [
                            'allowPointSelect' => true,
                            'cursor' => 'pointer',
                            'dataLabels' => [
                                'enabled' => true,
                                'format' => '<b>{point.name}</b>: {point.percentage:.1f}%'
                            ]
                        ]
                    ],
                    'series' => [
                        [
                            'name' => 'Pending',
                            'data' => $pieData,
                            'colors' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16']
                        ]
                    ],
                    'credits' => ['enabled' => false]
                ]
            ]); ?>
        <?php else: ?>
            <div class="no-data">No pending approvals data available</div>
        <?php endif; ?>
    </div>

    <!-- Category Breakdown Table -->
    <div class="chart-container table-responsive">
        <h3 class="chart-title">Approval Breakdown by Category</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Pending</th>
                    <th>Approved</th>
                    <th>Avg Approval Time (days)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categoryLabels as $key => $label): ?>
                    <tr>
                        <td><?= Html::encode($label) ?></td>
                        <td><?= safeGet($categoryData, $key) ?></td>
                        <td><?= safeGet($approvedData, $key) ?></td>
                        <td><?= safeGetFloat($avgApprovalTimeData, $key) !== null ? safeGetFloat($avgApprovalTimeData, $key) : 'N/A' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Monthly Trends Chart -->
    <div class="chart-container">
        <h3 class="chart-title">Monthly Approval Trends</h3>
        <?php
        $hasMonthlyData = !empty($monthlyData['categories']) && (array_sum($monthlyData['pending']) > 0 || array_sum($monthlyData['approved']) > 0);

        if ($hasMonthlyData): ?>
            <?= Highcharts::widget([
                'options' => [
                    'chart' => ['type' => 'column', 'height' => 350],
                    'title' => ['text' => ''],
                    'xAxis' => [
                        'categories' => $monthlyData['categories'],
                        'labels' => ['style' => ['fontSize' => '12px']]
                    ],
                    'yAxis' => [
                        'title' => ['text' => 'Count'],
                        'gridLineDashStyle' => 'Dash'
                    ],
                    'series' => [
                        [
                            'name' => 'Pending',
                            'data' => array_map('intval', $monthlyData['pending']),
                            'color' => '#f59e0b'
                        ],
                        [
                            'name' => 'Approved',
                            'data' => array_map('intval', $monthlyData['approved']),
                            'color' => '#10b981'
                        ]
                    ],
                    'tooltip' => ['shared' => true],
                    'legend' => ['align' => 'center'],
                    'credits' => ['enabled' => false]
                ]
            ]); ?>
        <?php else: ?>
            <div class="no-data">No monthly trends data available</div>
        <?php endif; ?>
    </div>
</div> ;