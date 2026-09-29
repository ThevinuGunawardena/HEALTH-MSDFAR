<?php

/** @var yii\web\View $this */
/** @var string $form */
/** @var backend\models\MDivision[] $divisions */
/** @var backend\models\Bsc1Submission[] $submissions keyed by fi_division_id */
/** @var string $periodDate */
/** @var int $district */
/** @var string $districtName */

use yii\helpers\Html;
use yii\helpers\Url;
use backend\config\Constant;

$this->title = 'BSC-1 District Compilation';
$this->params['breadcrumbs'][] = $this->title;

/**
 * Returns [label, bootstrap badge class] for a division's current state.
 * A division with no submission row at all is "Not Started" — this is
 * the case the old query silently hid, so it's deliberately the first
 * branch here, not an afterthought.
 *
 * A closure, not a named function — a named function declared inside a
 * view file would throw a fatal "cannot redeclare" error if this view is
 * ever rendered more than once in the same request (partial renders,
 * some caching setups).
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

/**
 * Deadline/extension state for a division, handling the case where no
 * submission row exists yet (never started) — that division still has a
 * deadline, it just has no model to ask. Returns:
 *   'ok'        — not past deadline, or already submitted; no button
 *   'grantable' — past deadline, still with FI, no extension used yet
 *   'active'    — extension granted and still running
 *   'used'      — extension already granted and lapsed; no second chance
 */
$extensionState = function ($submission, $extension) use ($periodDate) {

    // Normal deadline: 5th of the following month
    $deadline = strtotime(
        date('Y-m-d 23:59:59', strtotime($periodDate . ' +1 month +4 days'))
    );

    // Deadline has not passed yet
    if (time() <= $deadline) {
        return 'ok';
    }

    // Already submitted — no extension needed
    if ($submission && $submission->approval_stage != Constant::FI) {
        return 'ok';
    }

    // An extension exists
    if ($extension) {

        // Extension is still active
        if (time() <= strtotime($extension->extension_deadline)) {
            return 'active';
        }

        // Extension existed but has expired
        return 'used';
    }

    // Deadline passed, no submission submitted, no extension
    return 'grantable';
};

$totalDivisions = count($divisions);

$startedCount = 0;
$submittedCount = 0;
$validatedCount = 0;

foreach ($divisions as $d) {

    $s = $submissions[$d->id] ?? null;

    if ($s !== null) {
        $startedCount++;
    }

    if ($s !== null && $s->approval_stage == Constant::AD) {
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

$allValidated = $totalDivisions > 0
    && $validatedCount === $totalDivisions;
?>    

<div class="bsc-forms-district-review">

    <?= $this->render('_form-tabs', ['form' => $form, 'action' => 'district-review']) ?>


    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4>
                <?= Html::encode(date('F Y', strtotime($periodDate))) ?>
                — <?= Html::encode(strtoupper($form)) ?> District Review
            </h4>
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
                'data-confirm' => $allValidated 
                    ? 'Send this ' . strtoupper($form) . ' district return to the Development Division?'
                    : null,
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
                    <div class="h4 mb-0"><?= $totalDivisions - $startedCount ?></div>
                    <div class="text-muted small">Not started</div>
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
                <th class="text-right">Families</th>
                <th class="text-right">Active Fishermen</th>
                <th class="text-right">Population</th>
                <th>Status</th>
                <th style="width:100px;"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($divisions as $i => $division): ?>
                <?php
                $submission = $submissions[$division->id] ?? null;
                list($statusLabel, $statusClass) = $bscStatus($submission);
                ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= Html::encode($division->name) ?></td>
                    <td class="text-right"><?= $submission ? Html::encode($submission->families ?? '—') : '—' ?></td>
                    <td class="text-right"><?= $submission ? Html::encode($submission->active_fishermen ?? '—') : '—' ?></td>
                    <td class="text-right"><?= $submission ? Html::encode($submission->population ?? '—') : '—' ?></td>
                    <td><span class="badge badge-<?= $statusClass ?>"><?= $statusLabel ?></span></td>
                    <td>
                        <?php 
                        $extension = $extensions[$division->id] ?? null;
                        $extState = $extensionState($submission, $extension); ?>

                        <?php if ($submission): ?>
                            <?= Html::a(
                                'Review →', 
                                ['district-review-detail', 'form' => $form, 'id' => $submission->id], 
                                ['class' => 'btn btn-sm btn-outline-primary']
                            ) ?>

                        <?php elseif ($extState !== 'grantable'): ?>
                            <span class="text-muted small">No return yet</span>
                        <?php endif; ?>

                        <?php if ($extState === 'grantable'): ?>
                            <?= Html::a('Grant 48h Extension', [
                                'grant-extension',
                                'form' => $form,
                                'divisionId' => $division->id,
                                'period' => date('Y-m', strtotime($periodDate)),
                            ], [
                                'class' => 'btn btn-sm btn-warning',
                                'style' => 'white-space: nowrap;',
                                'data-method' => 'post',
                                'data-confirm' => 'Grant a one-time 48-hour extension to ' . $division->name . "?\n\nThis can only be done once for this period — if they still don't submit, there is no second chance.",
                            ]) ?>
                        <?php elseif ($extState === 'active'): ?>
                            <span
                                class="badge badge-info"
                                title="Extension expires <?= Html::encode(
                                    date('j M, g:i A', strtotime($extension->extension_deadline))
                                ) ?>"
                            >
                                48h active
                            </span>
                        <?php elseif ($extState === 'used'): ?>
                            <span class="badge badge-secondary" title="A 48-hour extension was already granted and has expired">Extension used</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>