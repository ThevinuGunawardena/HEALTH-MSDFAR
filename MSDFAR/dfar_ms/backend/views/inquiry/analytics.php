<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use miloschuman\highcharts\Highcharts;
use backend\models\MFiDistrict;

$this->title = 'Analytics Dashboard';
$this->params['breadcrumbs'][] = $this->title;

// Register CSS
$this->registerCss("
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

    @media (max-width: 768px) {
        .analytics-dashboard {
            padding: 1rem;
        }
        .metric-value {
            font-size: 1.5rem;
        }
        .metrics-wrapper {
            grid-template-columns: 1fr;
        }
    }
");

// Map district IDs to names for chart display

// // Build a mapping of id => name
// $districtMap = MFiDistrict::find()
//     ->select(['id', 'name'])
//     ->indexBy('id')
//     ->column();

// If districtData is set and not empty, convert IDs to names for chart
// if (isset($districtData) && !empty($districtData)) {
//     foreach ($districtData as &$item) {
//         if (isset($item['name']) && isset($districtMap[$item['name']])) {
//             $item['name'] = $districtMap[$item['name']];
//         }
//     }
//     unset($item);
// }
?>

<div class="analytics-dashboard">
    <div class="dashboard-header">
        <h1 class="text-3xl font-bold text-gray-900"><?= Html::encode($this->title) ?></h1>
        <p class="text-gray-600 mt-2">Detailed insights into your inquiry management performance</p>
    </div>

    <!-- Category Distribution Chart -->
    <div class="chart-container">
        <h3 class="chart-title">Inquiry Categories</h3>
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
                        'name' => 'Categories',
                        'data' => isset($categoryData) ? $categoryData : [],
                        'colors' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
                    ]
                ],
                'credits' => ['enabled' => false]
            ]
        ]); ?>
    </div>

    <!-- District Office Distribution Chart -->
    <div class="chart-container">
        <h3 class="chart-title">District Office Distribution</h3>
        <?= Highcharts::widget([
            'options' => [
                'chart' => [
                    'type' => 'bar',
                    'height' => 350
                ],
                'title' => ['text' => ''],
                'xAxis' => [
                    'type' => 'category',
                    'labels' => [
                        'style' => ['fontSize' => '12px'],
                        'rotation' => -45
                    ]
                ],
                'yAxis' => [
                    'title' => ['text' => 'Number of Inquiries'],
                    'gridLineDashStyle' => 'Dash'
                ],
                'legend' => ['enabled' => false],
                'plotOptions' => [
                    'bar' => [
                        'dataLabels' => [
                            'enabled' => true,
                            'format' => '{y}',
                            'style' => [
                                'fontWeight' => 'bold'
                            ]
                        ],
                        'colorByPoint' => true,
                        'colors' => [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444',
                            '#8b5cf6'
                        ]
                    ]
                ],
                'series' => [
                    [
                        'name' => 'Inquiries',
                        'data' => $districtData
                    ]
                ],
                'tooltip' => [
                    'headerFormat' => '<b>{point.key}</b><br/>',
                    'pointFormat' => 'Inquiries: <b>{point.y}</b>'
                ],
                'credits' => ['enabled' => false]
            ]
        ]); ?>
    </div>



    <!-- Key Metrics Grid -->
    <div class="metrics-wrapper">
        <!-- Resolution Rate -->
        <div class="metric-card">
            <div class="flex justify-between items-start">
                <div>
                    <p class="metric-title">Resolution Rate</p>
                    <p class="metric-value">
                        <?= isset($metrics['resolutionRate']) ? $metrics['resolutionRate'] : 'N/A' ?>%
                    </p>
                    <div class="trend">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span class="trend-up">+5.2% vs last month</span>
                    </div>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Response Time -->
        <div class="metric-card">
            <div class="flex justify-between items-start">
                <div>
                    <p class="metric-title">Avg Response Time</p>
                    <p class="metric-value">
                        <?= isset($metrics['avgResponseTime']) ? $metrics['avgResponseTime'] : 'N/A' ?>h
                    </p>
                    <div class="trend">
                        <i class="fas fa-arrow-down mr-1"></i>
                        <span class="trend-up">-0.8h improvement</span>
                    </div>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg">
                    <i class="fas fa-clock text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Add more metric cards here -->
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Trends Chart -->
        <div class="chart-container">
            <h3 class="chart-title">Monthly Inquiry Trends</h3>
            <?= Highcharts::widget([
                'options' => [
                    'chart' => [
                        'type' => 'spline',
                        'height' => 350
                    ],
                    'title' => ['text' => ''],
                    'xAxis' => [
                        'categories' => isset($monthlyData['categories']) ? $monthlyData['categories'] : [],
                        'labels' => ['style' => ['fontSize' => '12px']]
                    ],
                    'yAxis' => [
                        'title' => ['text' => 'Count'],
                        'gridLineDashStyle' => 'Dash'
                    ],
                    'series' => [
                        [
                            'name' => 'Inquiries',
                            'data' => isset($monthlyData['inquiries']) ? $monthlyData['inquiries'] : [],
                            'color' => '#3b82f6'
                        ],
                        [
                            'name' => 'Resolved',
                            'data' => isset($monthlyData['resolved']) ? $monthlyData['resolved'] : [],
                            'color' => '#10b981'
                        ]
                    ],
                    'tooltip' => ['shared' => true],
                    'legend' => ['align' => 'center'],
                    'credits' => ['enabled' => false]
                ]
            ]); ?>
        </div>


    </div>

    <!-- Performance Summary -->
    <div class="summary-grid">
        <div class="summary-card bg-green-50">
            <h3 class="text-lg font-semibold text-green-800 mb-2">
                <i class="fas fa-chart-line mr-2"></i>Performance Insights
            </h3>
            <p class="text-green-700">
                Your team achieved a <?= isset($metrics['resolutionRate']) ? $metrics['resolutionRate'] : 'N/A' ?>%
                resolution rate this month,
                with an average response time of
                <?= isset($metrics['avgResponseTime']) ? $metrics['avgResponseTime'] : 'N/A' ?> hours.
            </p>
        </div>

        <!-- District Office Insights Card -->
        <div class="summary-card bg-blue-50">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">
                <i class="fas fa-building mr-2"></i>District Office Insights
            </h3>
            <p class="text-blue-700">
                <?php
                $topOffice = isset($districtData) && !empty($districtData) ? reset($districtData) : ['name' => 'N/A', 'y' => 0];
                $totalInquiries = isset($districtData) ? array_sum(array_column($districtData, 'y')) : 0;
                $percentage = ($totalInquiries > 0) ? round(($topOffice['y'] / $totalInquiries) * 100, 1) : 0;
                ?>
                <?= $topOffice['name'] ?> handles the highest volume with <?= $topOffice['y'] ?> inquiries
                (<?= $percentage ?>% of total).
            </p>
        </div>
    </div>
</div>