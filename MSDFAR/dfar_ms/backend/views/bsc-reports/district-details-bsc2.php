<?php

/**
 * @var yii\web\View $this
 * @var string $form
 * @var string $periodDate
 * @var int $district
 * @var array $submissions
 */

use yii\helpers\Html;

$this->title = 'BSC-2 District Details';

?>

<div class="bsc-district-details">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <p class="mb-1"
                style="font-size: 1.10 rem; font-weight: 700; color: #212529;">
                    District Name:
                    <?= Html::encode($districtName) ?>
            </p>

            <h4>
                BSC-2 District Details —
                <?= Html::encode(date('F Y', strtotime($periodDate))) ?>
            </h4>

            <p class="text-muted mb-0">
                Validated division-level data
            </p>
        </div>

        <?= Html::a(
            '← Back to National Report',
            [
                'national',
                'form' => 'bsc2',
                'period' => date('Y-m', strtotime($periodDate)),
            ],
            ['class' => 'btn btn-outline-secondary']
        ) ?>

    </div>


    <?php if (empty($submissions)): ?>

        <div class="alert alert-info">
            No validated BSC-2 returns were found for this district and period.
        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table class="table table-bordered table-sm">

                <thead class="thead-light">

                    <tr>

                        <th>F.I. Division</th>

                        <th class="text-right">
                            Beach Seine Licenses
                        </th>

                        <th class="text-right">
                            Fishing Boats Insured
                        </th>

                        <th class="text-right">
                            Total Production (Mt)
                        </th>

                        <th class="text-right">
                            Log Sheets Collected
                        </th>

                        <th class="text-right">
                            Raids
                        </th>

                        <th class="text-right">
                            Court Cases
                        </th>

                        <th class="text-right">
                            Departures
                        </th>

                        <th>Details</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($submissions as $submission): ?>

                        <?php

                        /*
                         * Calculate total fishing boats insured
                         * across all craft types for this division.
                         */
                        $totalInsured = array_sum(
                            array_map(
                                function ($boat) {
                                    return (int) $boat->insured_count;
                                },
                                $submission->boatsInsured
                            )
                        );

                        ?>

                        <tr>

                            <td>
                                <?= Html::encode(
                                    $submission->division->name ?? ''
                                ) ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->beach_seine_licenses ?>
                            </td>

                            <td class="text-right">
                                <?= $totalInsured ?>
                            </td>

                            <td class="text-right">
                                <?= number_format(
                                    (float) $submission->getTotalProduction(),
                                    2
                                ) ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->log_sheets_collected ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->no_of_raids ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->no_of_court_cases ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->departures ?>
                            </td>

                            <td>

                                <?= Html::a(
                                    'View Details',
                                    [
                                        '/bsc-forms/district-division-details',
                                        'form' => 'bsc2',
                                        'divisionId' => $submission->fi_division_id,
                                        'period' => date(
                                            'Y-m',
                                            strtotime($periodDate)
                                        ),
                                    ],
                                    [
                                        'class' =>
                                            'btn btn-sm btn-outline-primary'
                                    ]
                                ) ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>