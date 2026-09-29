<?php

/** @var yii\web\View $this */
/** @var backend\models\Bsc1Submission $submission */
/** @var array $boats */
/** @var array $registrations */
/** @var array $licenses */
/** @var backend\models\Bsc1AwarenessProgramme[] $awareness */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use backend\config\Constant;

$this->title = 'Review BSC-1 Return';
$this->params['breadcrumbs'][] = ['label' => 'District Compilation', 'url' => ['district-review', 'form' => 'bsc1']];
$this->params['breadcrumbs'][] = $this->title;

$craftTypes = [
    'imul_over50' => "IMUL(Over50')", 'imul' => 'IMUL', 'iday' => 'IDAY',
    'ofrp' => 'OFRP', 'mtrb' => 'MTRB', 'ntrb' => 'NTRB', 'nbsb' => 'NBSB(Craft)',
];
$licenseCraftTypes = $craftTypes;
unset($licenseCraftTypes['imul_over50']);
$migrationCraftTypes = ['imul' => 'IMUL', 'iday' => 'IDAY', 'ofrp' => 'OFRP'];

$bscCell = function ($name, $value) {
    return Html::textInput($name, $value, [
        'type' => 'number', 'min' => 0,
        'class' => 'form-control form-control-sm text-center',
        'style' => 'width:80px; display:inline-block;',
    ]);
};

// Attribution — who originally filed this vs. who last touched it.
// officerProfile is safely null-checked since a User row might not always
// resolve to a profile (see User::findByProfileId()'s note that profile_id
// isn't exclusively an officer profile for every account type).
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
?>

<div class="bsc-forms-district-review-detail">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Reviewing — <?= Html::encode($submission->division->name ?? 'Unknown division') ?></h4>
        <?php
        $statusLabel = $submission->approval_stage == Constant::DEVELOPMENT_DIVISION ? 'Validated'
            : ($submission->approval_stage == Constant::AD ? 'Submitted' : 'With FI');
        $statusClass = $submission->approval_stage == Constant::DEVELOPMENT_DIVISION ? 'success'
            : ($submission->approval_stage == Constant::AD ? 'info' : 'secondary');
        ?>
        <span class="badge badge-<?= $statusClass ?> p-2"><?= $statusLabel ?></span>
    </div>

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

    <?php $form = ActiveForm::begin(['id' => 'bsc1-review-form']); ?>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-users"></i> Community Profile</div>
        <div class="card-body row">
            <div class="col-md-4"><?= $form->field($submission, 'families')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-4"><?= $form->field($submission, 'active_fishermen')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-4"><?= $form->field($submission, 'population')->textInput(['type' => 'number']) ?></div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-ship"></i> Existing Operated Boats &amp; Beach Seines</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm text-center">
                <thead><tr><th class="text-left">Craft type</th><?php foreach ($craftTypes as $label): ?><th><?= $label ?></th><?php endforeach; ?></tr></thead>
                <tbody><tr>
                    <td class="text-left font-weight-bold">Count</td>
                    <?php foreach ($craftTypes as $c => $label): ?>
                        <td><?= $bscCell("Bsc1Boat[$c]", $boats[$c]->boat_count ?? 0) ?></td>
                    <?php endforeach; ?>
                </tr></tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-clipboard-list"></i> Boat Registration Activity</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm text-center">
                <thead><tr><th class="text-left">Action</th><?php foreach ($craftTypes as $label): ?><th><?= $label ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                <?php foreach (['first' => '1st Registration', 'renewal' => 'Renewal', 'cancellation' => 'Cancellation'] as $action => $actionLabel): ?>
                    <tr>
                        <td class="text-left font-weight-bold"><?= $actionLabel ?></td>
                        <?php foreach ($craftTypes as $c => $label): ?>
                            <td><?= $bscCell("Bsc1Registration[$action][$c]", $registrations[$action][$c]->reg_count ?? 0) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-id-card"></i> Operating Licenses Issued</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm text-center">
                <thead>
                <tr>
                    <th class="text-left">With craft</th>
                    <?php foreach ($licenseCraftTypes as $label): ?><th><?= $label ?></th><?php endforeach; ?>
                    <th style="border-left:2px solid #dee2e6;">Without craft</th>
                </tr>
                </thead>
                <tbody><tr>
                    <td class="text-left font-weight-bold">Count</td>
                    <?php foreach ($licenseCraftTypes as $c => $label): ?>
                        <td><?= $bscCell("Bsc1LicenseWithCraft[$c]", $licenses[$c]->license_count ?? 0) ?></td>
                    <?php endforeach; ?>
                    <td style="border-left:2px solid #dee2e6;">
                        <?= Html::activeTextInput($submission, 'licenses_without_craft', [
                            'type' => 'number', 'min' => 0,
                            'class' => 'form-control form-control-sm text-center',
                            'style' => 'width:80px; display:inline-block;',
                        ]) ?>
                    </td>
                </tr></tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-water"></i> Migration</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm text-center" style="max-width:400px;">
                <thead><tr><th class="text-left">Craft type</th><?php foreach ($migrationCraftTypes as $label): ?><th><?= $label ?></th><?php endforeach; ?></tr></thead>
                <tbody><tr>
                    <td class="text-left font-weight-bold">Count</td>
                    <?php foreach ($migrationCraftTypes as $c => $label): ?>
                        <td><?= Html::activeTextInput($submission, "migrated_$c", [
                            'type' => 'number', 'min' => 0,
                            'class' => 'form-control form-control-sm text-center',
                            'style' => 'width:80px; display:inline-block;',
                        ]) ?></td>
                    <?php endforeach; ?>
                </tr></tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-exclamation-triangle"></i> Reported Incidents</div>
        <div class="card-body row">
            <div class="col-md-3"><?= $form->field($submission, 'incident_partial_loss')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-3"><?= $form->field($submission, 'incident_total_loss')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-3"><?= $form->field($submission, 'incident_natural_deaths')->textInput(['type' => 'number']) ?></div>
            <div class="col-md-3"><?= $form->field($submission, 'incident_missing')->textInput(['type' => 'number']) ?></div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-bullhorn"></i> Awareness Programmes / Special Events</div>
        <div class="card-body">
            <div id="awareness-rows">
                <?php foreach ($awareness as $a): ?>
                    <div class="row awareness-row mb-2">
                        <div class="col-md-2"><?= Html::textInput("Bsc1AwarenessProgramme[{$a->id}][event_date]", $a->event_date, ['type' => 'date', 'class' => 'form-control form-control-sm']) ?></div>
                        <div class="col-md-3"><?= Html::textInput("Bsc1AwarenessProgramme[{$a->id}][nature]", $a->nature, ['class' => 'form-control form-control-sm', 'placeholder' => 'Nature of programme']) ?></div>
                        <div class="col-md-1"><?= Html::textInput("Bsc1AwarenessProgramme[{$a->id}][participants]", $a->participants, ['type' => 'number', 'class' => 'form-control form-control-sm', 'placeholder' => 'Pax']) ?></div>
                        <div class="col-md-1"><?= Html::textInput("Bsc1AwarenessProgramme[{$a->id}][cost]", $a->cost, ['type' => 'number', 'class' => 'form-control form-control-sm', 'placeholder' => 'Rs']) ?></div>
                        <div class="col-md-2"><?= Html::textInput("Bsc1AwarenessProgramme[{$a->id}][resource_person]", $a->resource_person, ['class' => 'form-control form-control-sm', 'placeholder' => 'Resource person']) ?></div>
                        <div class="col-md-2"><?= Html::textInput("Bsc1AwarenessProgramme[{$a->id}][institution]", $a->institution, ['class' => 'form-control form-control-sm', 'placeholder' => 'Institution']) ?></div>
                        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger remove-awareness-row">✕</button></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-awareness-row" class="btn btn-sm btn-outline-primary mt-2">+ Add programme</button>
        </div>
    </div>

    <div class="bg-light p-3 rounded d-flex justify-content-between align-items-center">

        <?= Html::a(
            '← Back to district dashboard',
            ['district-review', 'form' => 'bsc1'],
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
                        'form' => 'bsc1',
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
                            'form' => 'bsc1',
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
        <?= Html::beginForm(['return', 'form' => 'bsc1', 'id' => $submission->id], 'post') ?>
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
                              placeholder="e.g. Population figure looks off, please recheck"></textarea>
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
let newAwarenessCounter = 0;
document.getElementById('add-awareness-row')?.addEventListener('click', function () {
    newAwarenessCounter++;
    const key = 'new_' + newAwarenessCounter;
    const row = document.createElement('div');
    row.className = 'row awareness-row mb-2';
    row.innerHTML = `
        <div class="col-md-2"><input type="date" name="Bsc1AwarenessProgramme[\${key}][event_date]" class="form-control form-control-sm"></div>
        <div class="col-md-3"><input type="text" name="Bsc1AwarenessProgramme[\${key}][nature]" class="form-control form-control-sm" placeholder="Nature of programme"></div>
        <div class="col-md-1"><input type="number" name="Bsc1AwarenessProgramme[\${key}][participants]" class="form-control form-control-sm" placeholder="Pax"></div>
        <div class="col-md-1"><input type="number" name="Bsc1AwarenessProgramme[\${key}][cost]" class="form-control form-control-sm" placeholder="Rs"></div>
        <div class="col-md-2"><input type="text" name="Bsc1AwarenessProgramme[\${key}][resource_person]" class="form-control form-control-sm" placeholder="Resource person"></div>
        <div class="col-md-2"><input type="text" name="Bsc1AwarenessProgramme[\${key}][institution]" class="form-control form-control-sm" placeholder="Institution"></div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger remove-awareness-row">✕</button></div>
    `;
    document.getElementById('awareness-rows').appendChild(row);
});
document.getElementById('awareness-rows')?.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-awareness-row')) {
        e.target.closest('.awareness-row').remove();
    }
});
JS
);
?>