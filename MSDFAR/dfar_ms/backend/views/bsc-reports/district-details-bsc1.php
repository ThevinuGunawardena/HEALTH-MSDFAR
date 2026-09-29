<?php

/**
 * @var yii\web\View $this
 * @var string $form
 * @var string $periodDate
 * @var int $district
 * @var array $submissions
 */

use yii\helpers\Html;

$this->title = 'BSC-1 District Details';

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
                BSC-1 District Details —
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
                'form' => 'bsc1',
                'period' => date('Y-m', strtotime($periodDate)),
            ],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
    </div>

    <?php if (empty($submissions)): ?>

        <div class="alert alert-info">
            No validated BSC-1 returns were found for this district and period.
        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table class="table table-bordered table-sm">

                <thead class="thead-light">

                    <tr>
                        <th>F.I. Division</th>
                        <th class="text-right">Families</th>
                        <th class="text-right">Active Fishermen</th>
                        <th class="text-right">Population</th>
                        <th class="text-right">Total Boats</th>
                        <th class="text-right">Total Registrations</th>
                        <th class="text-right">Total Licenses</th>
                        <th class="text-right">Migrated Boats</th>
                        <th class="text-right">Partial Loss</th>
                        <th class="text-right">Total Loss</th>
                        <th class="text-right">Deaths</th>
                        <th class="text-right">Missing</th>
                        <th class="text-right">Awareness Programmes</th>
                        <th>Details</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($submissions as $submission): ?>

                        <?php
                        $totalBoats = array_sum(
                            array_map(
                                function ($boat) {
                                    return $boat->boat_count;
                                },
                                $submission->boats
                            )
                        );

                        $totalRegistrations = array_sum(
                            array_map(
                                function ($registration) {
                                    return $registration->reg_count;
                                },
                                $submission->registrations
                            )
                        );

                        $totalLicenses =
                            array_sum(
                                array_map(
                                    function ($license) {
                                        return $license->license_count;
                                    },
                                    $submission->licensesWithCraft
                                )
                            )
                            + (int) $submission->licenses_without_craft;

                        $migratedBoats =
                            (int) $submission->migrated_imul
                            + (int) $submission->migrated_iday
                            + (int) $submission->migrated_ofrp;
                        ?>

                        <tr>

                            <td>
                                <?= Html::encode(
                                    $submission->division->name ?? ''
                                ) ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->families ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->active_fishermen ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->population ?>
                            </td>

                            <td class="text-right">
                                <?= $totalBoats ?>
                            </td>

                            <td class="text-right">
                                <?= $totalRegistrations ?>
                            </td>

                            <td class="text-right">
                                <?= $totalLicenses ?>
                            </td>

                            <td class="text-right">
                                <?= $migratedBoats ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->incident_partial_loss ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->incident_total_loss ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->incident_natural_deaths ?>
                            </td>

                            <td class="text-right">
                                <?= (int) $submission->incident_missing ?>
                            </td>

                            <td class="text-right">
                                <?= count($submission->awarenessProgrammes) ?>
                            </td>

                            <td>
                                <?= Html::a(
                                    'View Details',
                                    [
                                        '/bsc-forms/district-division-details',
                                        'form' => 'bsc1',
                                        'divisionId' => $submission->fi_division_id,
                                        'period' => date('Y-m', strtotime($periodDate)),
                                    ],
                                    ['class' => 'btn btn-sm btn-outline-primary']
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>