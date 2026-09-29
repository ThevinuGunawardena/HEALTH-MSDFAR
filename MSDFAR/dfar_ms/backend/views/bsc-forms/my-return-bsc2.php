<?php

/** @var yii\web\View $this */
/** @var backend\models\Bsc2Submission $submission */
/** @var bool $locked */
/** @var string $periodDate */
/** @var array $seaworthiness category => Bsc2SeaworthinessCertificates|null */
/** @var array $boatsInsured craft_type => Bsc2BoatInsured|null */
/** @var backend\models\Bsc2LagoonActivity[] $lagoonActivities */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'BSC-2 Monthly Return';
$this->params['breadcrumbs'][] = $this->title;

$seaworthyCategories = ['inboard' => 'Inboard', 'outboard' => 'Outboard'];
$insuredCraftTypes = ['imul' => 'IMUL', 'iday' => 'IDAY', 'ofrp' => 'OFRP', 'mtrb' => 'MTRB'];

$bscCell = function ($name, $value, $locked) {
    return Html::textInput($name, $value, [
        'type' => 'number', 
        'min' => 0,
        'class' => 'form-control form-control-sm text-center',
        'style' => 'width:100px; display:block; margin:0 auto;',
        'disabled' => $locked,
    ]);
};

$totalProduction = $submission->getTotalProduction();
?>

<div class="bsc-forms-my-return">

    <?= $this->render('_form-tabs', ['form' => $form, 'action' => 'my-return']) ?>

    <div class="d-flex justify-content-between align-items-center mb-3" style="width: 85%;">

        <div>

            <h4 class="mt-2 mb-0">BSC-2 Monthly Return</h4>

            <p class="text-muted mb-0">
                Division: <strong><?= Html::encode($divisionName) ?></strong>
                <span class="mx-4">·</span>
                Locked to your account
            </p>

            <label for="period-picker" class="mb-1">
                <strong>Reporting Period</strong>
            </label>

            <?= Html::dropDownList(
                'period',
                date('Y-m', strtotime($periodDate)),
                $periodOptions,
                [
                    'id' => 'period-picker',
                    'class' => 'form-control',
                    'style' => 'width: 220px;',
                    'onchange' => 'window.location.href = "' .
                        Url::to(['my-return', 'form' => $form]) .
                        '&period=" + this.value;',
                ]
            ) ?>

        </div>

        <span class="badge badge-<?= $locked ? 'warning' : 'secondary' ?> p-2">
            <?= $submission->isNewRecord
                ? 'Draft'
                : ($locked
                    ? 'Submitted — awaiting Divisional Head review'
                    : 'Draft')
            ?>
        </span>

    </div>

    <?php if ($expired): ?>

        <div class="alert alert-warning">
            🔒 This reporting period is closed.
            The submission deadline has passed, so this return can no longer be edited or submitted.
        </div>

    <?php elseif ($locked): ?>

        <div class="alert alert-info">
            🔒 This return is locked — it's been submitted and is awaiting your Divisional Head's review.
            Contact them if it needs correcting before they validate it.
        </div>

    <?php endif; ?>

    <?php if ($submission->was_returned && !empty($submission->return_reason)): ?>
        <div class="alert alert-danger">
            <strong>⚠️ This return was returned by your Divisional Head.</strong>
            <hr>
            <strong>Reason for return:</strong><br>
            <?= nl2br(Html::encode($submission->return_reason)) ?>
        </div>
    <?php endif; ?>

    <?php if ($extensionActive && $extension): ?>

        <div class="alert alert-warning">
            <strong>48-hour extension granted.</strong><br>
            Your Divisional Head has granted you an extension for this reporting period.
            You can submit this return until

            <strong>
                <?= Html::encode(
                    date(
                        'j M Y, g:i A',
                        strtotime($extension->extension_deadline)
                    )
                ) ?>
            </strong>.

            After this time, the submission will be locked again.
        </div>

    <?php endif; ?>

    <?php $form = ActiveForm::begin(['id' => 'bsc2-form']); ?>

    <!-- ============================================================== -->
    <!-- Licenses & Sea-worthiness Certificates -->
    <!-- ============================================================== -->
    <div class="card mb-3" style="width: 85%;">
        <div class="card-header"><i class="fa fa-fw fa-id-card"></i> Licenses &amp; Sea-worthiness Certificates</div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <?= $form->field($submission, 'beach_seine_licenses')->textInput(['type' => 'number', 'min' => 0, 'value' => $submission->beach_seine_licenses ?? 0, 'disabled' => $locked])->label('Beach seine operating licenses issued (NET)') ?>
                </div>
            </div>
            <p class="text-muted small mb-1">Sea-worthiness certificates issued</p>
            <div class="table-responsive">
                <table
                    class="table table-bordered table-sm text-center mb-0"
                    style="width: 65%;"
                >
                    <thead>
                        <tr>
                            <th class="text-left">Category</th>

                            <?php foreach ($seaworthyCategories as $label): ?>
                                <th><?= $label ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="text-left font-weight-bold">Count</td>

                            <?php foreach ($seaworthyCategories as $c => $label): ?>
                                <td>
                                    <?= $bscCell(
                                        "Bsc2SeaworthinessCertificates[$c]",
                                        $seaworthiness[$c]->cert_count ?? 0,
                                        $locked
                                    ) ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Boats Insured -->
    <!-- ============================================================== -->
    <div class="card mb-3" style="width: 85%;">
        <div class="card-header"><i class="fa fa-fw fa-shield-alt"></i> No. of Fishing Boats Insured</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm text-center">
                <thead><tr><th class="text-left">Craft type</th><?php foreach ($insuredCraftTypes as $label): ?><th><?= $label ?></th><?php endforeach; ?></tr></thead>
                <tbody><tr>
                    <td class="text-left font-weight-bold">Count</td>
                    <?php foreach ($insuredCraftTypes as $c => $label): ?>
                        <td><?= $bscCell("Bsc2BoatInsured[$c]", $boatsInsured[$c]->insured_count ?? 0, $locked) ?></td>
                    <?php endforeach; ?>
                </tr></tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Fish Production -->
    <!-- ============================================================== -->
    <div class="card mb-3" style="width: 85%;">
        <div class="card-header"><i class="fa fa-fw fa-fish"></i> Fish Production (Mt)</div>
        <div class="card-body">
            <p class="text-muted small">Production reported this month, by source. Total is calculated automatically.</p>
            <div class="row">
                <div class="col-md-4"><?= $form
                    ->field($submission, 'production_lagoon')
                    ->textInput([
                        'type' => 'number', 
                        'step' => '0.01', 
                        'min' => 0, 
                        'value' => $submission->production_lagoon ?? 0, 
                        'disabled' => $locked, 
                        'id' => 'prod-lagoon'])
                    ->label('Lagoon & brackish water') ?>
                </div>
                <div class="col-md-4"><?= $form
                    ->field($submission, 'production_coastal')
                    ->textInput([
                        'type' => 'number', 
                        'step' => '0.01', 
                        'min' => 0, 
                        'value' => $submission->production_coastal ?? 0, 
                        'disabled' => $locked, 
                        'id' => 'prod-coastal'])
                    ->label('Coastal') ?>
                </div>
                <div class="col-md-4"><?= $form
                    ->field($submission, 'production_offshore')
                    ->textInput([
                        'type' => 'number', 
                        'step' => '0.01', 
                        'min' => 0, 
                        'value' => $submission->production_offshore ?? 0, 
                        'disabled' => $locked, 
                        'id' => 'prod-offshore'])
                    ->label('Offshore') ?>
                </div>
            </div>
            <div class="bg-light p-2 rounded d-flex justify-content-between align-items-center">
                <span class="text-muted small font-weight-bold">Total fish production</span>
                <span id="production-total" class="font-weight-bold"><?= number_format($totalProduction, 2) ?> Mt</span>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Legal & Compliance -->
    <!-- ============================================================== -->
    <div class="card mb-3" style="width: 85%;">
        <div class="card-header"><i class="fa fa-fw fa-gavel"></i> Legal &amp; Compliance</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6"><?= $form
                ->field($submission, 'no_of_raids')
                ->textInput([
                    'type' => 'number', 
                    'min' => 0,
                    'value' => $submission->no_of_raids ?? 0,
                    'disabled' => $locked]) ?>
                </div>
                <div class="col-md-6"><?= $form
                ->field($submission, 'no_of_court_cases')
                ->textInput([
                    'type' => 'number', 
                    'min' => 0,
                    'value' => $submission->no_of_court_cases ?? 0,
                    'disabled' => $locked]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Log Sheets & Departures -->
    <!-- ============================================================== -->
    <div class="card mb-3" style="width: 85%;">
        <div class="card-header"><i class="fa fa-fw fa-file-alt"></i> Log Sheets &amp; Departures</div>
        <div class="card-body row">
            <div class="col-md-6"><?= $form
                ->field($submission, 'log_sheets_collected')
                ->textInput([
                    'type' => 'number',
                    'min' => 0,
                    'value' => $submission->log_sheets_collected ?? 0,
                    'disabled' => $locked]) ?>
            </div>
            <div class="col-md-6"><?= $form
                ->field($submission, 'departures')
                ->textInput([
                    'type' => 'number',
                    'min' => 0,
                    'value' => $submission->departures ?? 0,
                    'disabled' => $locked]) ?>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Registration & Welfare -->
    <!-- ============================================================== -->
    <div class="card mb-3" style="width: 85%;">
        <div class="card-header"><i class="fa fa-fw fa-life-ring"></i> Registration &amp; Welfare</div>
        <div class="card-body row">
            <div class="col-md-4"><?= $form
                ->field($submission, 'fishermen_registered')
                ->textInput([
                    'type' => 'number',
                    'min' => 0,
                    'value' => $submission->fishermen_registered ?? 0,
                    'disabled' => $locked]) ?>
            </div>
            <div class="col-md-4"><?= $form
                ->field($submission, 'id_cards_issued')
                ->textInput([
                    'type' => 'number',
                    'min' => 0,
                    'value' => $submission->id_cards_issued ?? 0,
                    'disabled' => $locked]) ?>
            </div>
            <div class="col-md-4"><?= $form
                ->field($submission, 'awareness_programmes')
                ->textInput([
                    'type' => 'number',
                    'min' => 0,
                    'value' => $submission->awareness_programmes ?? 0,
                    'disabled' => $locked]) ?>
            </div>
            <div class="col-md-6"><?= $form
                ->field($submission, 'insurance_enrolled')
                ->textInput([
                    'type' => 'number',
                    'min' => 0,
                    'value' => $submission->insurance_enrolled ?? 0,
                    'disabled' => $locked]) ?>
            </div>
            <div class="col-md-6"><?= $form
                ->field($submission, 'pension_enrolled')
                ->textInput([
                    'type' => 'number',
                    'min' => 0,
                    'value' => $submission->pension_enrolled ?? 0,
                    'disabled' => $locked]) ?>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Bycatch / Environmental Recording -->
    <!-- ============================================================== -->
    <div class="card mb-3" style="width: 85%;">
        <div class="card-header"><i class="fa fa-fw fa-water"></i> Recorded Bycatch Deaths</div>
        <div class="card-body row">
            <div class="col-md-6"><?= $form
                ->field($submission, 'recorded_marine_mammal_deaths')
                ->textInput([
                    'type' => 'number', 
                    'min' => 0,
                    'value' => $submission->recorded_marine_mammal_deaths ?? 0,
                    'disabled' => $locked]) ?>
            </div>
            <div class="col-md-6"><?= $form
                ->field($submission, 'recorded_turtle_deaths')
                ->textInput([
                    'type' => 'number',
                    'min' => 0,
                    'value' => $submission->recorded_turtle_deaths ?? 0,
                    'disabled' => $locked]) ?>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Lagoon Management Activities — repeatable list -->
    <!-- ============================================================== -->
    <div class="card mb-3" style="width: 85%;">
        <div class="card-header"><i class="fa fa-fw fa-water"></i> Lagoon Management Activities</div>
        <div class="card-body">
            <div id="lagoon-rows">
                <?php foreach ($lagoonActivities as $l): ?>
                    <div class="row lagoon-row mb-2" data-key="<?= $l->id ?>">
                        <div class="col-md-4"><?= Html::textInput("Bsc2LagoonActivity[{$l->id}][lagoon_name]", $l->lagoon_name, ['class' => 'form-control form-control-sm', 'placeholder' => 'Lagoon name', 'disabled' => $locked]) ?></div>
                        <div class="col-md-7"><?= Html::textInput("Bsc2LagoonActivity[{$l->id}][activity]", $l->activity, ['class' => 'form-control form-control-sm', 'placeholder' => 'Special activity carried out', 'disabled' => $locked]) ?></div>
                        <div class="col-md-1">
                            <?php if (!$locked): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-lagoon-row">✕</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (!$locked): ?>
                <button type="button" id="add-lagoon-row" class="btn btn-sm btn-outline-primary mt-2">+ Add lagoon activity</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$locked): ?>
        <div class="card mb-3" style="width: 85%;">
            <div class="card-body d-flex justify-content-between align-items-center bg-light p-3 rounded">
                <span class="text-muted small">
                    Once submitted, this return locks and goes to your Divisional AD for review.
                </span>

                <div>
                    <?= Html::submitButton('Save Draft', [
                        'name' => 'save-only',
                        'class' => 'btn btn-outline-secondary mr-2'
                    ]) ?>

                    <?= Html::submitButton('Submit to Divisional Head', [
                        'name' => 'submit-to-ad',
                        'class' => 'btn btn-primary',
                        'onclick' => "return confirm('Submit this return to your Divisional Head? You will not be able to edit it further unless it is returned to you.');",
                    ]) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs(<<<JS
// Live production total, matching the prototype's behavior
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

// Lagoon activity add/remove — same pattern as the awareness list
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