<?php

/** @var yii\web\View $this */
/** @var string $form */
/** @var backend\models\MDivision[] $divisions */
/** @var backend\models\Bsc2Submission[] $submissions keyed by fi_division_id */
/** @var string $periodDate */
/** @var int $district */

use yii\helpers\Html;
use backend\config\Constant;

$this->title = 'BSC-2 District Compilation';
$this->params['breadcrumbs'][] = $this->title;

/**
 * Closure, not a named function — see district-review-bsc1.php for why
 * (avoids a "cannot redeclare" fatal if this view ever renders twice in
 * one request).
 */
$bscStatus = function ($submission) {

    if ($submission === null) {
        return ['Not Started', 'secondary'];
    }

    if (
        $submission->validated_by !== null &&
        $submission->validated_at !== null
    ) {
        return ['Validated', 'success'];
    }

    if ($submission->approval_stage == Constant::AD) {
        return ['Submitted', 'info'];
    }

    if ($submission->approval_stage == Constant::FI) {
        return $submission->was_returned
            ? ['Returned to FI', 'warning']
            : ['Draft', 'secondary'];
    }

    if ($submission->approval_stage == Constant::DEVELOPMENT_DIVISION) {
        return ['Sent to Development Division', 'success'];
    }

    return ['Unknown', 'secondary'];
};

$totalDivisions = count($divisions);

$startedCount = 0;
$submittedCount = 0;
$validatedCount = 0;
$totalProduction = 0;

foreach ($divisions as $d) {

    $s = $submissions[$d->id] ?? null;

    if ($s !== null) {

        $startedCount++;

        $totalProduction += $s->getTotalProduction();
    }

    if (
        $s !== null &&
        $s->approval_stage == Constant::AD &&
        $s->validated_by === null &&
        $s->validated_at === null
    ) {
        $submittedCount++;
    }

    if (
        $s !== null &&
        $s->validated_by !== null &&
        $s->validated_at !== null
    ) {
        $validatedCount++;
    }

}

$allValidated = $totalDivisions > 0 && $validatedCount === $totalDivisions;
?>

<div class="bsc-forms-district-review">

    <?= $this->render('_form-tabs', ['form' => $form, 'action' => 'district-review']) ?>


    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4><?= Html::encode(date('F Y', strtotime($periodDate))) ?> — BSC-2 District Compilation</h4>
            <p class="text-muted mb-0">
                District: <strong><?= Html::encode($districtName) ?></strong>
                <span class="mx-3">·</span>
                Review each division's submission, correct as needed, validate it, then compile the district total.
            </p>
        </div>
        <?= Html::a(
            'Send to Development Division',
            ['compile', 'form' => $form, 'period' => Yii::$app->request->get('period')],
            [
                'class' => 'btn btn-success ml-4' . ($allValidated ? '' : ' disabled'),
                'data-method' => $allValidated ? 'post' : null,
                'data-confirm' => $allValidated ? 'Compile and send this district\'s return to HQ?' : null,
                'onclick' => $allValidated ? null : 'return false;',
            ]
        ) ?>
    </div>

    <!-- Period picker -->
    <div class="mb-3">
        <div style="display: flex; align-items: center; justify-content: flex-start; width: fit-content;">
            <form method="get" style="display: flex; align-items: center; margin: 0;">
                <input type="hidden" name="form" value="<?= Html::encode($form) ?>">

                <label for="period" style="margin: 0 8px 0 0; white-space: nowrap;">
                    Period:
                </label>

                <input
                    type="month"
                    id="period"
                    name="period"
                    class="form-control form-control-sm"
                    style="width: 160px; margin-right: 8px;"
                    value="<?= Html::encode(date('Y-m', strtotime($periodDate))) ?>"
                >

                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    Go
                </button>
            </form>
        </div>
    </div>

    <!-- Summary strip -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body py-2">
                    <div class="h4 mb-0"><?= $startedCount ?>/<?= $totalDivisions ?></div>
                    <div class="text-muted small">Divisions reported</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body py-2">
                    <div class="h4 mb-0"><?= $submittedCount ?></div>
                    <div class="text-muted small">Awaiting your review</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body py-2">
                    <div class="h4 mb-0"><?= $validatedCount ?>/<?= $totalDivisions ?></div>
                    <div class="text-muted small">Validated</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body py-2">
                    <div class="h4 mb-0"><?= number_format($totalProduction, 2) ?></div>
                    <div class="text-muted small">Total fish production (Mt)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Division table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr>
                <th style="width:40px;">No</th>
                <th>F.I. Division</th>
                <th class="text-right">Production (Mt)</th>
                <th class="text-right">Departures</th>
                <th class="text-right">Raids + Court Cases</th>
                <th>Status</th>
                <th style="width:180px;">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($divisions as $i => $division): ?>
                <?php
                $submission = $submissions[$division->id] ?? null;
                list($statusLabel, $statusClass) = $bscStatus($submission);
                $legalCount = $submission ? ($submission->no_of_raids + $submission->no_of_court_cases) : null;
                ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= Html::encode($division->name) ?></td>
                    <td class="text-right"><?= $submission ? number_format($submission->getTotalProduction(), 2) : '—' ?></td>
                    <td class="text-right"><?= $submission ? Html::encode($submission->departures) : '—' ?></td>
                    <td class="text-right"><?= $legalCount !== null ? Html::encode($legalCount) : '—' ?></td>
                    <td><span class="badge badge-<?= $statusClass ?>"><?= $statusLabel ?></span></td>
                    <td>
                       

                            <?php
                            $extension = $extensions[$division->id] ?? null;

                            $deadline = strtotime(
                                date(
                                    'Y-m-d 23:59:59',
                                    strtotime($periodDate . ' +1 month +4 days')
                                )
                            );

                            $deadlinePassed = time() > $deadline;
                            ?>

                            <?php if ($submission): ?>

                                    <?= Html::a(
                                        'Review →',
                                        [
                                            'district-review-detail',
                                            'form' => $form,
                                            'id' => $submission->id
                                        ],
                                        [
                                            'class' => 'btn btn-sm btn-outline-primary mb-1'
                                        ]
                                    ) ?>

                                <?php else: ?>

                                    <span class="text-muted small d-block mb-1">
                                        No return yet
                                    </span>

                                <?php endif; ?>


                                <?php if ($deadlinePassed && !$extension && (!$submission || $submission->approval_stage == Constant::FI)): ?>

                                    <?= Html::a(
                                        'Grant 48h Extension',
                                        [
                                            'grant-extension',
                                            'form' => $form,
                                            'divisionId' => $division->id,
                                            'period' => date('Y-m', strtotime($periodDate)),
                                        ],
                                        [
                                            'class' => 'btn btn-sm btn-warning',
                                            'data-method' => 'post',
                                            'data-confirm' =>
                                                'Grant a one-time 48-hour extension to ' . $division->name . "?\n\nThis can only be done once for this period — if they still don't submit, there is no second chance.",
                                        ]
                                    ) ?>

                                <?php elseif ($extension): ?>

                                    <span class="badge badge-warning">
                                        48h Extension Granted
                                    </span>

                                <?php endif; ?>

                        
        
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>