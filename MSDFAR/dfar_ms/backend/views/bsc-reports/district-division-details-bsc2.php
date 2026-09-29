<?php

use yii\helpers\Html;

/* ---------------------------------------------------------
 * Helper data
 * --------------------------------------------------------- */

$divisionName = $division->name ?? 'Unknown Division';
$periodLabel = date('F Y', strtotime($periodDate));


/* ---------------------------------------------------------
 * Sea-worthiness certificates
 * --------------------------------------------------------- */

$seaworthiness = [
    'inboard' => 0,
    'outboard' => 0,
];

foreach ($submission->seaworthinessCerts as $certificate) {
    if (isset($seaworthiness[$certificate->category])) {
        $seaworthiness[$certificate->category] = (int) $certificate->cert_count;
    }
}


/* ---------------------------------------------------------
 * Boats insured
 * --------------------------------------------------------- */

$boatsInsured = [
    'imul' => 0,
    'iday' => 0,
    'ofrp' => 0,
    'mtrb' => 0,
];

foreach ($submission->boatsInsured as $boat) {
    if (isset($boatsInsured[$boat->craft_type])) {
        $boatsInsured[$boat->craft_type] = (int) $boat->insured_count;
    }
}


/* ---------------------------------------------------------
 * Labels
 * --------------------------------------------------------- */

$craftLabels = [
    'imul' => 'IMUL',
    'iday' => 'IDAY',
    'ofrp' => 'OFRP',
    'mtrb' => 'MTRB',
];

?>

<style>
    .bsc-division-details {
        width: 100%;
        max-width: 1500px;
        margin-left: auto;
        margin-right: auto;
    }

    .bsc-division-details .card {
        margin-bottom: 12px !important;
    }

    .bsc-division-details .card-header {
        padding: 7px 12px;
    }

    .bsc-division-details .card-body {
        padding: 8px;
    }

    .bsc-division-details .table {
        margin-bottom: 0;
    }

    .bsc-division-details .table td,
    .bsc-division-details .table th {
        padding: 5px 8px;
        vertical-align: middle;
    }

    .bsc-division-details .row {
        margin-bottom: 0;
    }
</style>


<div class="bsc-division-details">

    <!-- =====================================================
        HEADER
        ===================================================== -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                BSC-2 Division Details
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
                'form' => 'bsc2',
                'district' => $division->district_id,
                'period' => date('Y-m', strtotime($periodDate)),
            ],
            ['class' => 'btn btn-outline-secondary']
        ) ?>

    </div>

    <!-- Submission Information -->
    <div class="card mb-3">
        <div class="card-header font-weight-bold">
            Submission Information
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-4">
                    <strong>District</strong><br>
                    <?= Html::encode($division->district->name ?? '—') ?>
                </div>

                <div class="col-md-4">
                    <strong>F.I. Division</strong><br>
                    <?= Html::encode($division->name) ?>
                </div>

                <div class="col-md-4">
                    <strong>Reporting Period</strong><br>
                    <?= Html::encode(date('F Y', strtotime($periodDate))) ?>
                </div>

            </div>
        </div>
    </div>
    
    <!-- =====================================================
         LICENCES + LEGAL ACTIVITIES
         ===================================================== -->

    <div class="row">

        <!-- Licences -->

        <div class="col-md-6">

            <div class="card">

                <div class="card-header font-weight-bold">
                    Licences
                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <tr>
                            <th>
                                Beach Seine Licenses Issued (NET)
                            </th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->beach_seine_licenses
                                ) ?>
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>


        <!-- Legal -->

        <div class="col-md-6">

            <div class="card">

                <div class="card-header font-weight-bold">
                    Legal & Enforcement Activities
                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <tr>
                            <th>No. of Raids</th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->no_of_raids
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>No. of Court Cases</th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->no_of_court_cases
                                ) ?>
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         SEA-WORTHINESS + BOATS INSURED
         ===================================================== -->

    <div class="row">

        <!-- Sea-worthiness -->

        <div class="col-md-6">

            <div class="card">

                <div class="card-header font-weight-bold">
                    Sea-worthiness Certificates
                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>Category</th>
                                <th width="25%" class="text-right">
                                    Certificates
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <td>Inboard</td>
                                <td class="text-right">
                                    <?= Html::encode(
                                        $seaworthiness['inboard']
                                    ) ?>
                                </td>
                            </tr>

                            <tr>
                                <td>Outboard</td>
                                <td class="text-right">
                                    <?= Html::encode(
                                        $seaworthiness['outboard']
                                    ) ?>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <!-- Boats insured -->

        <div class="col-md-6">

            <div class="card">

                <div class="card-header font-weight-bold">
                    Fishing Boats Insured
                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <thead>
                            <tr>
                                <th>Craft Type</th>
                                <th width="25%" class="text-right">
                                    Boats Insured
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
                                        <?= Html::encode(
                                            $boatsInsured[$type]
                                        ) ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         FISH PRODUCTION
         ===================================================== -->

    <div class="card">

        <div class="card-header font-weight-bold">
            Fish Production
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>
                        <th>
                            Production Area
                        </th>

                        <th width="25%" class="text-right">
                            Production (Mt)
                        </th>
                    </tr>

                </thead>

                <tbody>

                    <tr>
                        <td>
                            Lagoon & Brackish Water Production
                        </td>

                        <td class="text-right">
                            <?= Html::encode(
                                $submission->production_lagoon
                            ) ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Coastal Production
                        </td>

                        <td class="text-right">
                            <?= Html::encode(
                                $submission->production_coastal
                            ) ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Offshore Production
                        </td>

                        <td class="text-right">
                            <?= Html::encode(
                                $submission->production_offshore
                            ) ?>
                        </td>
                    </tr>

                    <tr class="font-weight-bold">

                        <td>
                            Total Production
                        </td>

                        <td class="text-right">
                            <?= Html::encode(
                                $submission->totalProduction
                            ) ?>
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    <!-- =====================================================
         FISHERMEN REGISTRATION + ID CARDS
         ===================================================== -->

    <div class="row">

        <div class="col-md-6">

            <div class="card">

                <div class="card-header font-weight-bold">
                    Fishermen Registration & Identification
                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <tr>
                            <th>
                                Fisherman Registrations
                            </th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->fishermen_registered
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                I.D. Cards Issued
                            </th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->id_cards_issued
                                ) ?>
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>


        <!-- Welfare -->

        <div class="col-md-6">

            <div class="card">

                <div class="card-header font-weight-bold">
                    Fishermen Welfare & Awareness
                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <tr>
                            <th>
                                Awareness Programmes
                            </th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->awareness_programmes
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                Fisherman Insurance Enrolled
                            </th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->insurance_enrolled
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                Fishermen Enrolled in Pension
                            </th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->pension_enrolled
                                ) ?>
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         FISHING OPERATIONS + MARINE ANIMAL DEATHS
         ===================================================== -->

    <div class="row">

        <div class="col-md-6">

            <div class="card">

                <div class="card-header font-weight-bold">
                    Fishing Operations
                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <tr>
                            <th>
                                Log Sheets Collected
                            </th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->log_sheets_collected
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                No. of Departures
                            </th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->departures
                                ) ?>
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>


        <div class="col-md-6">

            <div class="card">

                <div class="card-header font-weight-bold">
                    Recorded Marine Animal Deaths
                </div>

                <div class="card-body">

                    <table class="table table-bordered">

                        <tr>
                            <th>
                                Marine Mammal Deaths
                            </th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->recorded_marine_mammal_deaths
                                ) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                Turtle Deaths
                            </th>

                            <td class="text-right">
                                <?= Html::encode(
                                    $submission->recorded_turtle_deaths
                                ) ?>
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         LAGOON ACTIVITIES
         ===================================================== -->

    <div class="card">

        <div class="card-header font-weight-bold">
            Lagoon Management Activities
        </div>

        <div class="card-body">

            <?php if (!empty($submission->lagoonActivities)): ?>

                <table class="table table-bordered">

                    <thead>

                        <tr>
                            <th width="30%">
                                Name of the Lagoon
                            </th>

                            <th>
                                Special Activities Carried Out
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($submission->lagoonActivities as $activity): ?>

                            <tr>

                                <td>
                                    <?= Html::encode(
                                        $activity->lagoon_name
                                    ) ?>
                                </td>

                                <td>
                                    <?= nl2br(
                                        Html::encode($activity->activity)
                                    ) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="text-muted">
                    No lagoon activities were recorded.
                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- =====================================================
         FOOTER NAVIGATION
         ===================================================== -->

    <div class="mt-3 mb-4">

        <?= Html::a(
            '← Back to District Report',
            [
                '/bsc-reports/district',
                'form' => 'bsc2',
                'district' => $division->district_id,
                'period' => date('Y-m', strtotime($periodDate)),
            ],
            [
                'class' => 'btn btn-outline-secondary',
            ]
        ) ?>

    </div>

</div>