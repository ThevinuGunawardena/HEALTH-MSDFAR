<?php

/** @var yii\web\View $this */
/** @var backend\models\Bsc2Submission $submission */
/** @var array $seaworthiness */
/** @var array $boatsInsured */
/** @var backend\models\Bsc2LagoonActivity[] $lagoonActivities */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use backend\config\Constant;

$this->title = 'Review BSC-2 Return';
$this->params['breadcrumbs'][] = ['label' => 'District Compilation', 'url' => ['district-review', 'form' => 'bsc2']];
$this->params['breadcrumbs'][] = $this->title;

$seaworthyCategories = ['inboard' => 'Inboard', 'outboard' => 'Outboard'];
$insuredCraftTypes = ['imul' => 'IMUL', 'iday' => 'IDAY', 'ofrp' => 'OFRP', 'mtrb' => 'MTRB'];

$bscCell = function ($name, $value) {
    return Html::textInput($name, $value, [
        'type' => 'number', 'min' => 0,
        'class' => 'form-control form-control-sm text-center',
        'style' => 'width:80px; display:inline-block;',
    ]);
};

$submittedByName = null;
if ($submission->submittedByUser && $submission->submittedByUser->officerProfile) {
    $p = $submission->submittedByUser->officerProfile;
    $submittedByName = trim($p->first_name . ' ' . $p->last_name);
}
$lastEditedByName = null;
if ($submission->lastEditedByUser && $submission->lastEditedByUser->officerProfile) {
    $p = $submission->lastEditedByUser->officerProfile;
    $lastEditedByName = trim($p->first_name . ' ' . $p->last_name);
}
$wasEditedByAd = $submission->wasEditedAfterSubmission();
$totalProduction = $submission->getTotalProduction();
?>

<div class="bsc-forms-district-review-detail">
    <h4>Reviewing — <?= Html::encode($submission->division->name ?? 'Unknown division') ?></h4>
    <?php
    if (
        $submission->validated_by !== null &&
        $submission->validated_at !== null
    ) {
        $statusLabel = 'Validated';
        $statusClass = 'success';
    } elseif (
        $submission->approval_stage == Constant::AD
    ) {
        $statusLabel = 'Submitted';
        $statusClass = 'info';
    } elseif (
        $submission->approval_stage == Constant::DEVELOPMENT_DIVISION
    ) {
        $statusLabel = 'Sent to Development Division';
        $statusClass = 'success';
    } else {
        $statusLabel = 'With FI';
        $statusClass = 'secondary';
    }
    ?>

    <?php if ($submittedByName): ?>
        <p class="text-muted small mb-1">
            Originally submitted by <strong><?= Html::encode($submittedByName) ?></strong>
            on <?= Html::encode(date('j M Y, g:i A', strtotime($submission->submitted_at))) ?>
        </p>
    <?php endif; ?>

    <?php if ($wasEditedByAd && $lastEditedByName): ?>
        <div class="alert alert-warning py-2">
            ✏️ Last edited by <strong><?= Html::encode($lastEditedByName) ?></strong>
            on <?= Html::encode(date('j M Y, g:i A', strtotime($submission->last_edited_at))) ?>
            — differs from who originally submitted this. Changes since submission are tracked in the audit log.
        </div>
    <?php endif; ?>

    <?php if ($submission->was_returned): ?>
        <div class="alert alert-info py-2">
            🔁 This return was previously sent back to the Fisheries Inspector.
            <?php if ($submission->return_reason): ?>
                Reason given: <em><?= Html::encode($submission->return_reason) ?></em>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(['id' => 'bsc2-review-form']); ?>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-id-card"></i> Licenses &amp; Sea-worthiness Certificates</div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4"><?= $form->field($submission, 'beach_seine_licenses')->textInput(['type' => 'number']) ?></div>
            </div>
            <p class="text-muted small mb-1">Sea-worthiness certificates issued</p>
            <table class="table table-bordered table-sm text-center" style="max-width:300px;">
                <thead><tr><th class="text-left">Category</th><?php foreach ($seaworthyCategories as $label): ?><th><?= $label ?></th><?php endforeach; ?></tr></thead>
                <tbody><tr>
                    <td class="text-left font-weight-bold">Count</td>
                    <?php foreach ($seaworthyCategories as $c => $label): ?>
                        <td><?= $bscCell("Bsc2SeaworthinessCert[$c]", $seaworthiness[$c]->cert_count ?? 0) ?></td>
                    <?php endforeach; ?>
                </tr></tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-shield-alt"></i> No. of Fishing Boats Insured</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm text-center">
                <thead><tr><th class="text-left">Craft type</th><?php foreach ($insuredCraftTypes as $label): ?><th><?= $label ?></th><?php endforeach; ?></tr></thead>
                <tbody><tr>
                    <td class="text-left font-weight-bold">Count</td>
                    <?php foreach ($insuredCraftTypes as $c => $label): ?>
                        <td><?= $bscCell("Bsc2BoatInsured[$c]", $boatsInsured[$c]->insured_count ?? 0) ?></td>
                    <?php endforeach; ?>
                </tr></tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-fish"></i> Fish Production (Mt)</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><?= $form->field($submission, 'production_lagoon')->textInput(['type' => 'number', 'step' => '0.01', 'id' => 'prod-lagoon'])->label('Lagoon & brackish water') ?></div>
                <div class="col-md-4"><?= $form->field($submission, 'production_coastal')->textInput(['type' => 'number', 'step' => '0.01', 'id' => 'prod-coastal'])->label('Coastal') ?></div>
                <div class="col-md-4"><?= $form->field($submission, 'production_offshore')->textInput(['type' => 'number', 'step' => '0.01', 'id' => 'prod-offshore'])->label('Offshore') ?></div>
            </div>
            <div class="bg-light p-2 rounded d-flex justify-content-between align-items-center">
                <span class="text-muted small font-weight-bold">Total fish production</span>
                <span id="production-total" class="font-weight-bold"><?= number_format($totalProduction, 2) ?> Mt</span>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-gavel"></i> Legal &amp; Compliance</div>
        <div class="card-body row">
            <div class="col-md-6"><?= $form->field($submission, 'no_of_raids')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-6"><?= $form->field($submission, 'no_of_court_cases')->textInput(['type' => 'number']) ?></div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-file-alt"></i> Log Sheets &amp; Departures</div>
        <div class="card-body row">
            <div class="col-md-6"><?= $form->field($submission, 'log_sheets_collected')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-6"><?= $form->field($submission, 'departures')->textInput(['type' => 'number']) ?></div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-life-ring"></i> Registration &amp; Welfare</div>
        <div class="card-body row">
            <div class="col-md-4"><?= $form->field($submission, 'fishermen_registered')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-4"><?= $form->field($submission, 'id_cards_issued')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-4"><?= $form->field($submission, 'awareness_programmes')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-6"><?= $form->field($submission, 'insurance_enrolled')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-6"><?= $form->field($submission, 'pension_enrolled')->textInput(['type' => 'number']) ?></div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-water"></i> Recorded Bycatch Deaths</div>
        <div class="card-body row">
            <div class="col-md-6"><?= $form->field($submission, 'recorded_marine_mammal_deaths')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-6"><?= $form->field($submission, 'recorded_turtle_deaths')->textInput(['type' => 'number']) ?></div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-water"></i> Lagoon Management Activities</div>
        <div class="card-body">
            <div id="lagoon-rows">
                <?php foreach ($lagoonActivities as $l): ?>
                    <div class="row lagoon-row mb-2">
                        <div class="col-md-4"><?= Html::textInput("Bsc2LagoonActivity[{$l->id}][lagoon_name]", $l->lagoon_name, ['class' => 'form-control form-control-sm', 'placeholder' => 'Lagoon name']) ?></div>
                        <div class="col-md-7"><?= Html::textInput("Bsc2LagoonActivity[{$l->id}][activity]", $l->activity, ['class' => 'form-control form-control-sm', 'placeholder' => 'Special activity carried out']) ?></div>
                        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger remove-lagoon-row">✕</button></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-lagoon-row" class="btn btn-sm btn-outline-primary mt-2">+ Add lagoon activity</button>
        </div>
    </div>

    <div class="bg-light p-3 rounded d-flex justify-content-between align-items-center">

        <?= Html::a(
            '← Back to district dashboard',
            ['district-review', 'form' => 'bsc2'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>

        <div>

            <?php if ($submission->approval_stage == Constant::DEVELOPMENT_DIVISION): ?>

                <span class="text-muted mr-3">
                    This return has been sent to the Development Division and is locked.
                </span>

            <?php elseif (
                $submission->validated_by !== null &&
                $submission->validated_at !== null
            ): ?>

                <?= Html::a(
                    'Reopen for Edits',
                    [
                        'reopen',
                        'form' => 'bsc2',
                        'id' => $submission->id
                    ],
                    [
                        'class' => 'btn btn-warning',
                        'data-method' => 'post',
                        'data-confirm' =>
                            'Reopen this return for editing? The current validation will be removed.',
                    ]
                ) ?>

            <?php else: ?>

                <?= Html::submitButton(
                    'Save Changes',
                    ['class' => 'btn btn-outline-primary mr-2']
                ) ?>

                <?php if ($submission->approval_stage == Constant::AD): ?>

                    <button
                        type="button"
                        class="btn btn-warning mr-2"
                        data-toggle="modal"
                        data-target="#returnModal"
                    >
                        Return to FI
                    </button>

                    <?= Html::a(
                        'Validate',
                        [
                            'validate',
                            'form' => 'bsc2',
                            'id' => $submission->id
                        ],
                        [
                            'class' => 'btn btn-success',
                            'data-method' => 'post',
                            'data-confirm' =>
                                'Validate this division\'s return?',
                        ]
                    ) ?>

                <?php else: ?>

                    <span class="text-muted small">
                        This return is currently with the Fisheries Inspector — nothing to validate yet.
                    </span>

                <?php endif; ?>

            <?php endif; ?>

        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- Return to FI modal -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?= Html::beginForm(['return', 'form' => 'bsc2', 'id' => $submission->id], 'post') ?>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return to Fisheries Inspector</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">
                    This unlocks the return for the FI to edit and resubmit. A reason is required.
                </p>
                <div class="form-group">
                    <label for="return-reason">Reason</label>
                    <textarea name="reason" id="return-reason" class="form-control" rows="3" required
                              placeholder="e.g. Production figures look off, please recheck"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Return to FI</button>
            </div>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>

<?php
$this->registerJs(<<<JS
function updateProductionTotal() {
    const lagoon = parseFloat(document.getElementById('prod-lagoon')?.value) || 0;
    const coastal = parseFloat(document.getElementById('prod-coastal')?.value) || 0;
    const offshore = parseFloat(document.getElementById('prod-offshore')?.value) || 0;
    const total = lagoon + coastal + offshore;
    const el = document.getElementById('production-total');
    if (el) el.textContent = total.toFixed(2) + ' Mt';
}
['prod-lagoon', 'prod-coastal', 'prod-offshore'].forEach(function (id) {
    document.getElementById(id)?.addEventListener('input', updateProductionTotal);
});

let newLagoonCounter = 0;
document.getElementById('add-lagoon-row')?.addEventListener('click', function () {
    newLagoonCounter++;
    const key = 'new_' + newLagoonCounter;
    const row = document.createElement('div');
    row.className = 'row lagoon-row mb-2';
    row.innerHTML = `
        <div class="col-md-4"><input type="text" name="Bsc2LagoonActivity[\${key}][lagoon_name]" class="form-control form-control-sm" placeholder="Lagoon name"></div>
        <div class="col-md-7"><input type="text" name="Bsc2LagoonActivity[\${key}][activity]" class="form-control form-control-sm" placeholder="Special activity carried out"></div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger remove-lagoon-row">✕</button></div>
    `;
    document.getElementById('lagoon-rows').appendChild(row);
});
document.getElementById('lagoon-rows')?.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-lagoon-row')) {
        e.target.closest('.lagoon-row').remove();
    }
});
JS
);
?>