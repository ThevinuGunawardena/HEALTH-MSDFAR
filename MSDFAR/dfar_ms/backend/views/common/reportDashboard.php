<?php

use yii\helpers\Json;

/** @var array $counts */
/** @var array $districtStats */
/** @var string|null $fromDate */
/** @var string|null $toDate */

$fromDate = $fromDate ?? null;
$toDate = $toDate ?? null;

$counts = $counts ?? [];
$districtStats = $districtStats ?? [];

/*
 * Dashboard statistics
 */
$fishermanRegistration = (int) (
    $counts['fishermanRegistration'] ?? 0
);

$boatNumberIssuing = (int) (
    $counts['boatNumberIssuing'] ?? 0
);


$boatRegistration = (int) (
    $counts['boatRegistration'] ?? 0
);

$boatRegistrationRenew = (int) (
    $counts['boatRegistrationRenew'] ?? 0
);

$boatRegistrationByType =
    $counts['boatRegistrationByType'] ?? [];

$boatRegistrationRenewByType =
    $counts['boatRegistrationRenewByType'] ?? [];

$highSeasLicenseIssuing = (int) (
    $counts['highSeasLicenseIssuing'] ?? 0
);

$nationalLicenseIssuing = (int) (
    $counts['nationalLicenseIssuing'] ?? 0
);

$nationalLicenseByType =
    $counts['nationalLicenseByType'] ?? [];

$boatLicenseDistrictTypeStats =
    $boatLicenseDistrictTypeStats ?? [];

$districtLabels = [];
$boatTypeLabels = [];
$boatTypeDistrictCounts = [];

foreach ($boatLicenseDistrictTypeStats as $row) {
    $districtName = trim(
        (string) (
            $row['district_name']
            ?? 'Unknown District'
        )
    );

    $boatTypeName = trim(
        (string) (
            $row['boat_type_name']
            ?? 'Unknown Boat Type'
        )
    );

    if ($districtName === '') {
        $districtName = 'Unknown District';
    }

    if ($boatTypeName === '') {
        $boatTypeName = 'Unknown Boat Type';
    }

    $count = (int) ($row['count'] ?? 0);

    if (!in_array($districtName, $districtLabels, true)) {
        $districtLabels[] = $districtName;
    }

    if (!in_array($boatTypeName, $boatTypeLabels, true)) {
        $boatTypeLabels[] = $boatTypeName;
    }

    if (!isset(
        $boatTypeDistrictCounts[$boatTypeName][$districtName]
    )) {
        $boatTypeDistrictCounts[$boatTypeName][$districtName] = 0;
    }

    /*
     * Add counts instead of overwriting when multiple rows have
     * the same district name and boat-type code.
     */
    $boatTypeDistrictCounts[$boatTypeName][$districtName] +=
        $count;
}

sort($districtLabels, SORT_NATURAL | SORT_FLAG_CASE);
sort($boatTypeLabels, SORT_NATURAL | SORT_FLAG_CASE);

$chartColours = [
    'rgba(37, 99, 235, 0.80)',
    'rgba(20, 184, 166, 0.80)',
    'rgba(245, 158, 11, 0.80)',
    'rgba(139, 92, 246, 0.80)',
    'rgba(239, 68, 68, 0.80)',
    'rgba(14, 165, 233, 0.80)',
    'rgba(16, 185, 129, 0.80)',
    'rgba(236, 72, 153, 0.80)',
    'rgba(99, 102, 241, 0.80)',
    'rgba(249, 115, 22, 0.80)',
];

$chartDatasets = [];

foreach ($boatTypeLabels as $index => $boatTypeName) {
    $values = [];

    foreach ($districtLabels as $districtName) {
        $values[] = (int) (
            $boatTypeDistrictCounts[$boatTypeName][$districtName]
            ?? 0
        );
    }

    $colour = $chartColours[
        $index % count($chartColours)
    ];

    $chartDatasets[] = [
        'label' => $boatTypeName,
        'data' => $values,
        'backgroundColor' => $colour,
        'borderColor' => $colour,
        'borderWidth' => 1,
        'borderRadius' => 5,
        'borderSkipped' => false,
        'maxBarThickness' => 28,
    ];
}

$districtLabelsJson =
    Json::encode($districtLabels);

$chartDatasetsJson =
    Json::encode($chartDatasets);

$hasBoatChartData =
    !empty($districtLabels)
    && !empty($chartDatasets)
    && array_sum(
        array_map(
            static function ($dataset) {
                return array_sum($dataset['data'] ?? []);
            },
            $chartDatasets
        )
    ) > 0;

$dynamicChartHeight = max(
    450,
    count($districtLabels) * 70
);
?>

<!-- =========================================================
     First section: Summary cards
========================================================== -->

<div class="row dashboard-summary-row">

    <!-- Fisherman Registration -->
 <div class="col-xl-4 col-md-6 col-12 mb-4">
    <div class="card dashboard-card fisherman-card h-100 border-0">
        <div class="card-body d-flex align-items-center">

            <div class="icon-box icon-green">
                <i class="fa fa-user fa-fw"></i>
            </div>

            <div class="card-content ml-3">
                <p class="card-label mb-1">
                    Total Fisherman Registrations
                </p>

                <h3 class="card-value mb-2">
                    <?= number_format(
                        (int) $fishermanRegistration
                    ) ?>
                </h3>

                <small class="text-muted">
                    Based on selected filters
                </small>
            </div>

        </div>
    </div>
</div>
    <!-- Boat Number Issuing -->
    <!-- <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card dashboard-card h-100 border-0">
            <div class="card-body d-flex align-items-center">

                <div class="icon-box icon-blue">
                    <i class="fa fa-anchor fa-fw text-primary font-24"></i>
                </div>

                <div class="card-content ml-3">
                    <p class="card-label mb-1">
                        Total Boat Number Issuings
                    </p>

                    <h3 class="card-value mb-2">
                        <?= number_format($boatNumberIssuing) ?>
                    </h3>

                    <small class="text-muted">
                        Based on selected filters
                    </small>
                </div>

            </div>
        </div>
    </div> -->

    <!-- Boat Registration -->
    <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card dashboard-card boat-card h-100 border-0">
            <div class="card-body d-flex align-items-center">

                <div class="icon-box icon-blue">
                    <i class="fa fa-ship fa-fw text-primary font-24"></i>
                </div>

                <div class="card-content ml-3">
                    <p class="card-label mb-1">
                        Total Boat Registrations
                    </p>

                    <h3 class="card-value mb-2">
                        <?= number_format($boatRegistration) ?>
                    </h3>

                    <small class="text-muted">
                        Based on selected filters
                    </small>
                </div>

            </div>
        </div>
    </div>

    

     <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card dashboard-card boat-card h-100 border-0">
            <div class="card-body d-flex align-items-center">

                <div class="icon-box icon-blue">
                    <i class="fa fa-ship fa-fw text-primary font-24"></i>
                </div>

                <div class="card-content ml-3">
                    <p class="card-label mb-1">
                        Total Boat Registrations Renewals
                    </p>

                    <h3 class="card-value mb-2">
                        <?= number_format($boatRegistrationRenew) ?>
                    </h3>

                    <small class="text-muted">
                        Based on selected filters
                    </small>
                </div>

            </div>
        </div>
    </div>

  <?php foreach ($boatRegistrationByType as $boatType): ?>

    <!-- Boat Registration by Type -->
    <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card dashboard-card boat-card h-100 border-0">
            <div class="card-body d-flex align-items-center">

                <div class="icon-box icon-blue">
                    <i
                        class="fa fa-ship fa-fw text-primary font-24"
                    ></i>
                </div>

                <div class="card-content ml-3">
                    <p class="card-label mb-1">
                        Total Boat Registrations
                        (
                        <?= \yii\helpers\Html::encode(
                            $boatType['boat_type_code']
                        ) ?>
                        )
                    </p>

                    <h3 class="card-value mb-2">
                        <?= number_format(
                            (int) $boatType['total_count']
                        ) ?>
                    </h3>

                    <small class="text-muted">
                        Based on selected filters
                    </small>
                </div>

            </div>
        </div>
    </div>

<?php endforeach; ?>
<?php foreach (
    $boatRegistrationRenewByType as $boatType
): ?>

    <!-- Boat Registration Renewal by Type -->
    <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card dashboard-card boat-renew-card h-100 border-0">
            <div class="card-body d-flex align-items-center">

                 <div class="icon-box icon-blue">
                    <i
                        class="fa fa-ship fa-fw text-primary font-24"
                    ></i>
                </div>

                <div class="card-content ml-3">
                    <p class="card-label mb-1">
                        Total Boat Registration Renewals
                        (
                        <?= \yii\helpers\Html::encode(
                            $boatType['boat_type_code']
                        ) ?>
                        )
                    </p>

                    <h3 class="card-value mb-2">
                        <?= number_format(
                            (int) $boatType['total_count']
                        ) ?>
                    </h3>

                    <small class="text-muted">
                        Based on selected filters
                    </small>
                </div>

            </div>
        </div>
    </div>

<?php endforeach; ?>


    <!-- High Seas Licence -->
    <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card dashboard-card oprhs-lic-card h-100 border-0">
            <div class="card-body d-flex align-items-center">

               <div class="icon-box oprhs-lic-icon">
                    <i class="fa fa-id-card fa-fw"></i>
                </div>

                <div class="card-content ml-3">
                    <p class="card-label mb-1">
                        Total High Seas Licence Issuings
                    </p>

                    <h3 class="card-value mb-2">
                        <?= number_format($highSeasLicenseIssuing) ?>
                    </h3>

                    <small class="text-muted">
                        Based on selected filters
                    </small>
                </div>

            </div>
        </div>
    </div>

    <!-- National Licence -->
    <?php foreach ($nationalLicenseByType as $boatType): ?>

    <!-- National Licence by Boat Type -->
    <div class="col-xl-4 col-md-6 col-12 mb-4">
        <div class="card dashboard-card opr-lic-card h-100 border-0">
            <div class="card-body d-flex align-items-center">

                <div class="icon-box opr-lic-icon">
                    <i class="fa fa-id-card fa-fw"></i>
                </div>

                <div class="card-content ml-3">
                    <p class="card-label mb-1">
                        Total National Licences
                        (
                        <?= \yii\helpers\Html::encode(
                            $boatType['boat_type_code'] ?? ''
                        ) ?>
                        )
                    </p>

                    <h3 class="card-value mb-2">
                        <?= number_format(
                            (int) (
                                $boatType['total_count']
                                ?? 0
                            )
                        ) ?>
                    </h3>

                    <small class="text-muted">
                        Based on selected filters
                    </small>
                </div>

            </div>
        </div>
    </div>

<?php endforeach; ?>
    </div>


<!-- =========================================================
     Second section: Interactive district map
========================================================== -->

<?php

/*
 * Do not load another Chart.js version here when the application
 * already loads Chart.js through an asset bundle or layout.
 *
 * The browser error showed that window.Chart already exists and is
 * Chart.js 2.x. Loading Chart.js 4 again can create a version conflict.
 */

$boatChartScript = <<<JS
(function () {
    const districtLabels = {$districtLabelsJson};
    const chartDatasets = {$chartDatasetsJson};

    const canvas = document.getElementById(
        'boat-license-district-type-chart'
    );

    const emptyState = document.getElementById(
        'boat-chart-empty-state'
    );

    function showChartMessage(message) {
        if (canvas) {
            canvas.style.display = 'none';
        }

        if (emptyState) {
            emptyState.hidden = false;
            emptyState.textContent = message;
        }
    }

    if (!canvas) {
        console.error('Boat chart canvas was not found.');
        return;
    }

    if (
        !Array.isArray(districtLabels) ||
        districtLabels.length === 0 ||
        !Array.isArray(chartDatasets) ||
        chartDatasets.length === 0
    ) {
        showChartMessage(
            'No boat registration data was found for the selected filters.'
        );

        console.warn('Boat chart data is empty.', {
            districtLabels: districtLabels,
            chartDatasets: chartDatasets
        });

        return;
    }

    if (typeof window.Chart === 'undefined') {
        showChartMessage('The chart library could not be loaded.');
        console.error('Chart.js is not loaded.');
        return;
    }

    canvas.style.display = 'block';

    if (emptyState) {
        emptyState.hidden = true;
    }

    if (
        window.boatLicenseDistrictTypeChart &&
        typeof window.boatLicenseDistrictTypeChart.destroy === 'function'
    ) {
        window.boatLicenseDistrictTypeChart.destroy();
    }

    const chartVersion = String(window.Chart.version || '2');
    const chartMajorVersion =
        parseInt(chartVersion.split('.')[0], 10) || 2;

    const chartData = {
        labels: districtLabels,
        datasets: chartDatasets
    };

    let chartConfiguration;

    if (chartMajorVersion >= 3) {
        /* Chart.js 3.x or 4.x: vertical grouped bars. */
        chartConfiguration = {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    axis: 'x',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                            boxWidth: 12,
                            boxHeight: 12,
                            padding: 18,
                            color: '#334155',
                            font: {
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            title: function (items) {
                                return items.length
                                    ? items[0].label
                                    : '';
                            },
                            label: function (context) {
                                return (
                                    context.dataset.label +
                                    ': ' +
                                    Number(context.raw || 0).toLocaleString()
                                );
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        title: {
                            display: true,
                            text: 'District',
                            color: '#475569',
                            font: {
                                size: 13,
                                weight: '600'
                            }
                        },
                        ticks: {
                            autoSkip: false,
                            minRotation: 0,
                            maxRotation: 45,
                            color: '#334155',
                            font: {
                                size: 11,
                                weight: '600'
                            }
                        },
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        stacked: false,
                        title: {
                            display: true,
                            text: 'Number of boat registrations',
                            color: '#475569',
                            font: {
                                size: 13,
                                weight: '600'
                            }
                        },
                        ticks: {
                            precision: 0,
                            color: '#64748b',
                            callback: function (value) {
                                return Number(value).toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.20)'
                        },
                        border: {
                            display: false
                        }
                    }
                }
            }
        };
    } else {
        /* Chart.js 2.x: vertical grouped bars. */
        chartConfiguration = {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 12,
                        padding: 18,
                        fontColor: '#334155'
                    }
                },
                tooltips: {
                    mode: 'index',
                    axis: 'x',
                    intersect: false,
                    callbacks: {
                        title: function (tooltipItems, data) {
                            if (!tooltipItems.length) {
                                return '';
                            }

                            return data.labels[
                                tooltipItems[0].index
                            ];
                        },
                        label: function (tooltipItem, data) {
                            const dataset = data.datasets[
                                tooltipItem.datasetIndex
                            ];

                            return (
                                dataset.label +
                                ': ' +
                                Number(
                                    tooltipItem.yLabel || 0
                                ).toLocaleString()
                            );
                        }
                    }
                },
                scales: {
                    xAxes: [
                        {
                            stacked: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'District',
                                fontColor: '#475569',
                                fontStyle: 'bold'
                            },
                            ticks: {
                                autoSkip: false,
                                minRotation: 0,
                                maxRotation: 45,
                                fontColor: '#334155'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            barPercentage: 0.8,
                            categoryPercentage: 0.8
                        }
                    ],
                    yAxes: [
                        {
                            stacked: false,
                            scaleLabel: {
                                display: true,
                                labelString:
                                    'Number of boat registrations',
                                fontColor: '#475569',
                                fontStyle: 'bold'
                            },
                            ticks: {
                                beginAtZero: true,
                                precision: 0,
                                fontColor: '#64748b',
                                callback: function (value) {
                                    return Number(value).toLocaleString();
                                }
                            },
                            gridLines: {
                                color: 'rgba(148, 163, 184, 0.20)',
                                drawBorder: false
                            }
                        }
                    ]
                }
            }
        };
    }

    window.boatLicenseDistrictTypeChart =
        new window.Chart(
            canvas.getContext('2d'),
            chartConfiguration
        );
})();
JS;

$this->registerJs(
    $boatChartScript,
    \yii\web\View::POS_END
);
?>