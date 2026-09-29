<?php

/**
 * @var yii\web\View $this
 * @var string $form
 * @var backend\models\MDivision $division
 * @var backend\models\Bsc1Submission $submission
 * @var string $periodDate
 */

use yii\helpers\Html;

$this->title = 'BSC-1 Division Details';

$craftLabels = [
    'imul_over50' => 'IMUL > 50',
    'imul'        => 'IMUL',
    'iday'        => 'IDAY',
    'ofrp'        => 'OFRP',
    'mtrb'        => 'MTRB',
    'ntrb'        => 'NTRB',
    'nbsb'        => 'NBSB',
];

$actionLabels = [
    'first'        => 'First Registration',
    'renewal'      => 'Renewal',
    'cancellation' => 'Cancellation',
];

/*
 * Build lookup arrays so every craft type is displayed,
 * even when its value is zero or the child row does not exist.
 */

$boats = [];

foreach ($submission->boats as $boat) {
    $boats[$boat->craft_type] = (int) $boat->boat_count;
}

$registrations = [];

foreach ($submission->registrations as $registration) {
    $registrations[$registration->action][$registration->craft_type]
        = (int) $registration->reg_count;
}

$licenses = [];

foreach ($submission->licensesWithCraft as $license) {
    $licenses[$license->craft_type] = (int) $license->license_count;
}

?>

<style>
    .bsc-division-details {
        width: 100%;
        max-width: 1000px;
        margin-left: auto;
        margin-right: auto;
    }
</style>

<div class="bsc-division-details">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                BSC-1 Division Details
            </h4>

            <p class="text-muted mb-0">
                <?= Html::encode($division->name) ?>
                —
                <?= Html::encode(date('F Y', strtotime($periodDate))) ?>
            </p>
        </div>


        <?= Html::a(
            '← Back to District Report',
            [
                '/bsc-reports/district',
                'form' => 'bsc1',
                'district' => $division->district_id,
                'period' => date('Y-m', strtotime($periodDate)),
            ],
            ['class' => 'btn btn-outline-secondary']
        ) ?>

    </div>


    <!-- Submission Information -->
    <div class="card mb-3">

        <div class="card-header">
            <strong>Submission Information</strong>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">
                    <strong>District</strong>
                    <div>
                        <?= Html::encode($division->district->name ?? '—') ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <strong>F.I. Division</strong>
                    <div>
                        <?= Html::encode($division->name) ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <strong>Reporting Period</strong>
                    <div>
                        <?= Html::encode(date('F Y', strtotime($periodDate))) ?>
                    </div>
                </div>

            </div>

        </div>
    </div>


    <!-- Community Profile + Migrated Boats -->
    <div class="row">

        <!-- Community Profile -->
        <div class="col-md-6">

            <div class="card mb-3">

                <div class="card-header">
                    <strong>Community Profile</strong>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered mb-0">

                            <thead class="thead-light">
                                <tr>
                                    <th>Indicator</th>
                                    <th class="text-right">Value</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td>No. of Fisher Families</td>
                                    <td class="text-right">
                                        <?= (int) $submission->families ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>No. of Active Fishermen</td>
                                    <td class="text-right">
                                        <?= (int) $submission->active_fishermen ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Fisheries Sector Population</td>
                                    <td class="text-right">
                                        <?= (int) $submission->population ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Beach Seines</td>
                                    <td class="text-right">
                                        <?= (int) $submission->beach_seines ?>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <!-- Incidents -->
        <div class="col-md-6">

            <div class="card mb-3">

                <div class="card-header">
                    <strong>Incidents</strong>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered mb-0">

                            <thead class="thead-light">
                                <tr>
                                    <th>Incident</th>
                                    <th class="text-right">Number</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td>Partial Loss of Boats</td>
                                    <td class="text-right">
                                        <?= (int) $submission->incident_partial_loss ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Total Loss of Boats</td>
                                    <td class="text-right">
                                        <?= (int) $submission->incident_total_loss ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Natural Deaths of Fishermen</td>
                                    <td class="text-right">
                                        <?= (int) $submission->incident_natural_deaths ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Missing Fishermen at Sea</td>
                                    <td class="text-right">
                                        <?= (int) $submission->incident_missing ?>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Existing Boats + Boat Registration -->
    <div class="row">

        <!-- Existing Operated Boats -->
        <div class="col-md-6">

            <div class="card mb-3">

                <div class="card-header">
                    <strong>Existing Operated Boats</strong>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered mb-0">

                            <thead class="thead-light">
                                <tr>
                                    <th>Craft Type</th>
                                    <th class="text-right">
                                        Number of Boats
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($craftLabels as $type => $label): ?>

                                    <tr>

                                        <td>
                                            <?= Html::encode($label) ?>
                                        </td>

                                        <td class="text-right">
                                            <?= $boats[$type] ?? 0 ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                                <tr class="font-weight-bold">

                                    <td>Total Boats</td>

                                    <td class="text-right">
                                        <?= array_sum($boats) ?>
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <!-- Boat Registration Activity -->
        <div class="col-md-6">

            <div class="card mb-3">

                <div class="card-header">
                    <strong>Boat Registration Activity</strong>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered mb-0">

                            <thead class="thead-light">

                                <tr>

                                    <th>Craft Type</th>

                                    <?php foreach ($actionLabels as $action => $label): ?>

                                        <th class="text-right">
                                            <?= Html::encode($label) ?>
                                        </th>

                                    <?php endforeach; ?>

                                    <th class="text-right">
                                        Total
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($craftLabels as $type => $craftLabel): ?>

                                    <?php

                                    $first =
                                        $registrations['first'][$type] ?? 0;

                                    $renewal =
                                        $registrations['renewal'][$type] ?? 0;

                                    $cancellation =
                                        $registrations['cancellation'][$type] ?? 0;

                                    $total =
                                        $first
                                        + $renewal
                                        + $cancellation;

                                    ?>

                                    <tr>

                                        <td>
                                            <?= Html::encode($craftLabel) ?>
                                        </td>

                                        <td class="text-right">
                                            <?= $first ?>
                                        </td>

                                        <td class="text-right">
                                            <?= $renewal ?>
                                        </td>

                                        <td class="text-right">
                                            <?= $cancellation ?>
                                        </td>

                                        <td class="text-right">
                                            <?= $total ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Registrations + Licenses -->
    <div class="row">

        <!-- Operating Licenses -->
        <div class="col-md-6">

            <div class="card mb-4">

                <div class="card-header">
                    <strong>Operating Licenses</strong>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered mb-0">

                            <thead class="thead-light">

                                <tr>
                                    <th>Craft Type</th>

                                    <th class="text-right">
                                        Licenses With Craft
                                    </th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($craftLabels as $type => $label): ?>

                                    <?php

                                    /*
                                     * There is no IMUL > 50 category in
                                     * Bsc1LicenseWithCraft.
                                     */

                                    if ($type === 'imul_over50') {
                                        continue;
                                    }

                                    ?>

                                    <tr>

                                        <td>
                                            <?= Html::encode($label) ?>
                                        </td>

                                        <td class="text-right">
                                            <?= $licenses[$type] ?? 0 ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>


                                <tr>

                                    <td>
                                        Licenses Without Craft
                                    </td>

                                    <td class="text-right">
                                        <?= (int) $submission->licenses_without_craft ?>
                                    </td>

                                </tr>


                                <tr class="font-weight-bold">

                                    <td>
                                        Total Licenses
                                    </td>

                                    <td class="text-right">

                                        <?=
                                            array_sum($licenses)
                                            + (int) $submission->licenses_without_craft
                                        ?>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

         <!-- Migrated Boats -->
        <div class="col-md-6">

            <div class="card mb-2">

                <div class="card-header">
                    <strong>Migrated Boats</strong>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered mb-0">

                            <thead class="thead-light">
                                <tr>
                                    <th>Craft Type</th>
                                    <th class="text-right">
                                        Number of Migrated Boats
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td>IMUL</td>
                                    <td class="text-right">
                                        <?= (int) $submission->migrated_imul ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>IDAY</td>
                                    <td class="text-right">
                                        <?= (int) $submission->migrated_iday ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>OFRP</td>
                                    <td class="text-right">
                                        <?= (int) $submission->migrated_ofrp ?>
                                    </td>
                                </tr>

                                <tr class="font-weight-bold">
                                    <td>Total Migrated Boats</td>
                                    <td class="text-right">
                                        <?=
                                            (int) $submission->migrated_imul
                                            + (int) $submission->migrated_iday
                                            + (int) $submission->migrated_ofrp
                                        ?>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Awareness Programmes -->
    <div class="card mb-4">

        <div class="card-header">
            <strong>Awareness Programmes / Special Events</strong>
        </div>

        <div class="card-body">

            <?php if (empty($submission->awarenessProgrammes)): ?>

                <p class="text-muted mb-0">
                    No awareness programmes were recorded.
                </p>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-bordered table-sm mb-0">

                        <thead class="thead-light">

                            <tr>
                                <th>Date</th>
                                <th>Nature of Programme</th>
                                <th class="text-right">
                                    Participants
                                </th>
                                <th class="text-right">
                                    Cost (Rs.)
                                </th>
                                <th>Resource Person</th>
                                <th>Institution</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($submission->awarenessProgrammes as $programme): ?>

                                <tr>

                                    <td>
                                        <?= $programme->event_date
                                            ? Html::encode(
                                                date(
                                                    'd M Y',
                                                    strtotime($programme->event_date)
                                                )
                                            )
                                            : '—'
                                        ?>
                                    </td>

                                    <td>
                                        <?= Html::encode(
                                            $programme->nature ?: '—'
                                        ) ?>
                                    </td>

                                    <td class="text-right">
                                        <?= (int) $programme->participants ?>
                                    </td>

                                    <td class="text-right">
                                        <?= number_format(
                                            (float) $programme->cost,
                                            2
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= Html::encode(
                                            $programme->resource_person ?: '—'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= Html::encode(
                                            $programme->institution ?: '—'
                                        ) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <!-- Footer navigation -->
         
    <div class="mt-3 mb-4">

        <?= Html::a(
            '← Back to District Report',
            [
                '/bsc-reports/district',
                'form' => 'bsc1',
                'district' => $division->district_id,
                'period' => date('Y-m', strtotime($periodDate)),
            ],
            [
                'class' => 'btn btn-outline-secondary',
            ]
        ) ?>

    </div>

</div>