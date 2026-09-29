<?php

/** @var yii\web\View $this */
/** @var backend\models\Bsc1Submission $submission */
/** @var bool $locked */
/** @var string $periodDate */
/** @var array $boats craft_type => Bsc1Boat|null */
/** @var array $registrations [action][craft_type] => Bsc1Registration|null */
/** @var array $licenses craft_type => Bsc1LicenseWithCraft|null */
/** @var backend\models\Bsc1AwarenessProgramme[] $awareness */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'BSC-1 Monthly Return';
$this->params['breadcrumbs'][] = $this->title;

// Available reporting periods: current month + previous 3 months
$periodOptions = [];

$currentMonth = new DateTime('first day of this month');

for ($i = 0; $i < 4; $i++) {
    $date = (clone $currentMonth)->modify("-{$i} months");
    $value = $date->format('Y-m');
    $label = $date->format('F Y');

    $periodOptions[$value] = $label;
}

// Matches CRAFT_LABELS from the prototype exactly
$craftTypes = [
    'imul_over50' => "IMUL(Over50')",
    'imul'        => 'IMUL',
    'iday'        => 'IDAY',
    'ofrp'        => 'OFRP',
    'mtrb'        => 'MTRB',
    'ntrb'        => 'NTRB',
    'nbsb'        => 'NBSB(Craft)',
];
// Licenses with craft: no IMUL(Over50') category, matches LIC_WITH from the prototype
$licenseCraftTypes = $craftTypes;
unset($licenseCraftTypes['imul_over50']);

$migrationCraftTypes = ['imul' => 'IMUL', 'iday' => 'IDAY', 'ofrp' => 'OFRP'];

function bscCell($name, $value, $locked)
{
    return Html::textInput($name, $value, [
        'type' => 'number', 'min' => 0,
        'class' => 'form-control form-control-sm text-center',
        'style' => 'width:80px; display:inline-block;',
        'disabled' => $locked,
    ]);
}
?>

<div class="bsc-forms-my-return">

    <?= $this->render('_form-tabs', ['form' => $form, 'action' => 'my-return']) ?>


    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mt-2 mb-0">BSC-1 Monthly Return</h4>
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
            <?= $submission->isNewRecord ? 'Draft' : ($locked ? 'Submitted — awaiting Divisional Head review' : 'Draft') ?>
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
                    date('j M Y, g:i A', strtotime($extension->extension_deadline))
                ) ?>
            </strong>.
            After this time, the submission will be locked again.
        </div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(['id' => 'bsc1-form']); ?>

    <!-- ============================================================== -->
    <!-- Community Profile -->
    <!-- ============================================================== -->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-fw fa-users"></i> Community Profile
        </div>

        <div class="card-body">
            <p class="text-muted small">
                Prefilled from last month's validated submission. Review and correct if anything has changed.
            </p>

            <div class="row">

                <div class="col-md-4">
                    <?= $form->field($submission, 'families')->textInput([
                        'type' => 'number',
                        'min' => 0,
                        'step' => 1,
                        'disabled' => $locked,
                        'placeholder' => 'e.g. 2160'
                    ])->label('No. of fisher families') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($submission, 'active_fishermen')->textInput([
                        'type' => 'number',
                        'min' => 0,
                        'step' => 1,
                        'disabled' => $locked,
                        'placeholder' => 'e.g. 2250'
                    ])->label('No. of active fishermen') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($submission, 'population')->textInput([
                        'type' => 'number',
                        'min' => 0,
                        'step' => 1,
                        'disabled' => $locked,
                        'placeholder' => 'e.g. 6300'
                    ])->label('Population of fisheries sector') ?>
                </div>

            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Existing Boats -->
    <!-- ============================================================== -->
    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-ship"></i> Existing Operated Boats &amp; Beach Seines</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm text-center">
                <thead>
                <tr>
                    <th class="text-left">Craft type</th>
                    <?php foreach ($craftTypes as $label): ?><th><?= $label ?></th><?php endforeach; ?>
                    <th>Beach Seines</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-left font-weight-bold">Count</td>
                    <?php foreach ($craftTypes as $c => $label): ?>
                        <td><?= bscCell("Bsc1Boat[$c]", $boats[$c]->boat_count ?? 0, $locked) ?></td>
                    <?php endforeach; ?>
                    <td><?= bscCell("Bsc1Submission[beach_seines]", $submission->beach_seines ?? 0, $locked) ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Registration Activity -->
    <!-- ============================================================== -->
    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-clipboard-list"></i> Boat Registration Activity</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm text-center">
                <thead>
                <tr>
                    <th class="text-left">Action</th>
                    <?php foreach ($craftTypes as $label): ?><th><?= $label ?></th><?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach (['first' => '1st Registration', 'renewal' => 'Renewal', 'cancellation' => 'Cancellation'] as $action => $actionLabel): ?>
                    <tr>
                        <td class="text-left font-weight-bold"><?= $actionLabel ?></td>
                        <?php foreach ($craftTypes as $c => $label): ?>
                            <td><?= bscCell(
                                "Bsc1Registration[$action][$c]",
                                $registrations[$action][$c]->reg_count ?? 0,
                                $locked
                            ) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Operating Licenses -->
    <!-- ============================================================== -->
    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-id-card"></i> Operating Licenses Issued</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm text-center">
                <thead>
                <tr>
                    <th class="text-left">With craft</th>
                    <?php foreach ($licenseCraftTypes as $label): ?><th><?= $label ?></th><?php endforeach; ?>
                    <th class="border-left" style="border-left:2px solid #dee2e6;">Without craft</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-left font-weight-bold">Count</td>
                    <?php foreach ($licenseCraftTypes as $c => $label): ?>
                        <td><?= bscCell("Bsc1LicenseWithCraft[$c]", $licenses[$c]->license_count ?? 0, $locked) ?></td>
                    <?php endforeach; ?>
                    <td style="border-left:2px solid #dee2e6;">
                        <?= Html::activeTextInput($submission, 'licenses_without_craft', [
                            'type' => 'number', 'min' => 0,
                            'value' => $submission->licenses_without_craft ?? 0,
                            'class' => 'form-control form-control-sm text-center',
                            'style' => 'width:80px; display:inline-block;',
                            'disabled' => $locked,
                        ]) ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Migration -->
    <!-- ============================================================== -->
    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-water"></i> Migration</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm text-center" style="max-width:400px;">
                <thead>
                <tr>
                    <th class="text-left">Craft type</th>
                    <?php foreach ($migrationCraftTypes as $label): ?><th><?= $label ?></th><?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-left font-weight-bold">Count</td>
                    <?php foreach ($migrationCraftTypes as $c => $label): ?>
                        <td><?= Html::activeTextInput($submission, "migrated_$c", [
                            'type' => 'number', 'min' => 0,
                            'value' => $submission->{"migrated_$c"} ?? 0,
                            'class' => 'form-control form-control-sm text-center',
                            'style' => 'width:80px; display:inline-block;',
                            'disabled' => $locked,
                        ]) ?></td>
                    <?php endforeach; ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Reported Incidents -->
    <!-- ============================================================== -->
    <div class="card mb-3">
        <div class="card-header"><i class="fa fa-fw fa-exclamation-triangle"></i> Reported Incidents</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($submission, 'incident_partial_loss')
                        ->textInput([
                            'type' => 'number', 
                            'min' => 0,
                            'value' => $submission->incident_partial_loss ?? 0,
                            'disabled' => $locked
                        ])
                        ->label('Partial loss of boats') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($submission, 'incident_total_loss')
                        ->textInput([
                            'type' => 'number',
                            'min' => 0,
                            'value' => $submission->incident_total_loss ?? 0,
                            'disabled' => $locked
                        ])
                        ->label('Total loss of boats') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($submission, 'incident_natural_deaths')
                        ->textInput([
                            'type' => 'number',
                            'min' => 0,
                            'value' => $submission->incident_natural_deaths ?? 0,
                            'disabled' => $locked
                        ])
                        ->label('Natural deaths of fishermen') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($submission, 'incident_missing')
                        ->textInput([
                            'type' => 'number',
                            'min' => 0,
                            'value' => $submission->incident_missing ?? 0,
                            'disabled' => $locked
                        ])
                        ->label('Missing fishermen at sea') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Awareness Programmes — repeatable list -->
    <!-- ============================================================== -->

    <div class="card mb-3">

        <div class="card-header">
            <i class="fa fa-fw fa-bullhorn"></i>
            Awareness Programmes / Special Events
        </div>

        <div class="card-body">

            <div id="awareness-rows">

                <?php foreach ($awareness as $i => $a): ?>

                    <div class="awareness-row border rounded p-2 mb-3"
                        data-key="<?= $a->id ?>">

                        <!-- First row -->
                        <div class="row">

                            <div class="col-md-2">
                                <label class="small font-weight-bold text-muted">
                                    Event Date
                                </label>

                                <?= Html::textInput(
                                    "Bsc1AwarenessProgramme[{$a->id}][event_date]",
                                    $a->event_date,
                                    [
                                        'type' => 'date',
                                        'class' => 'form-control form-control-sm',
                                        'disabled' => $locked
                                    ]
                                ) ?>
                            </div>

                            <div class="col-md-4">
                                <label class="small font-weight-bold text-muted">
                                    Nature of Programme
                                </label>

                                <?= Html::textInput(
                                    "Bsc1AwarenessProgramme[{$a->id}][nature]",
                                    $a->nature,
                                    [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => 'Nature of programme',
                                        'disabled' => $locked
                                    ]
                                ) ?>
                            </div>

                            <div class="col-md-2">
                                <label class="small font-weight-bold text-muted">
                                    Number of Participants
                                </label>

                                <?= Html::textInput(
                                    "Bsc1AwarenessProgramme[{$a->id}][participants]",
                                    $a->participants,
                                    [
                                        'type' => 'number',
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => 'Number',
                                        'disabled' => $locked
                                    ]
                                ) ?>
                            </div>

                            <div class="col-md-2">
                                <label class="small font-weight-bold text-muted">
                                    Cost (Rs.)
                                </label>

                                <?= Html::textInput(
                                    "Bsc1AwarenessProgramme[{$a->id}][cost]",
                                    $a->cost,
                                    [
                                        'type' => 'number',
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => 'Amount',
                                        'disabled' => $locked
                                    ]
                                ) ?>
                            </div>

                            <div class="col-md-2">
                                <?php if (!$locked): ?>
                                    <label class="small font-weight-bold text-muted d-block">
                                        &nbsp;
                                    </label>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger remove-awareness-row">
                                        Remove
                                    </button>
                                <?php endif; ?>
                            </div>

                        </div>

                        <!-- Second row -->
                        <div class="row mt-2">

                            <div class="col-md-5">
                                <label class="small font-weight-bold text-muted">
                                    Resource Person
                                </label>

                                <?= Html::textInput(
                                    "Bsc1AwarenessProgramme[{$a->id}][resource_person]",
                                    $a->resource_person,
                                    [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => 'Name of resource person',
                                        'disabled' => $locked
                                    ]
                                ) ?>
                            </div>

                            <div class="col-md-5">
                                <label class="small font-weight-bold text-muted">
                                    Institution
                                </label>

                                <?= Html::textInput(
                                    "Bsc1AwarenessProgramme[{$a->id}][institution]",
                                    $a->institution,
                                    [
                                        'class' => 'form-control form-control-sm',
                                        'placeholder' => 'Institution / organisation',
                                        'disabled' => $locked
                                    ]
                                ) ?>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <?php if (!$locked): ?>

                <button
                    type="button"
                    id="add-awareness-row"
                    class="btn btn-sm btn-outline-primary mt-1">
                    + Add Programme
                </button>

            <?php endif; ?>

        </div>
    </div>


    <?php if (!$locked): ?>

        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">

            <span class="text-muted small">
                Once submitted, this return locks and goes to your Divisional Head for review.
            </span>

            <div>
                <?= Html::submitButton(
                    'Save Draft',
                    [
                        'name' => 'save-only',
                        'class' => 'btn btn-outline-secondary mr-2'
                    ]
                ) ?>

                <?= Html::submitButton(
                    'Submit to Divisional Head',
                    [
                        'name' => 'submit-to-ad',
                        'class' => 'btn btn-primary',
                        'onclick' =>
                            "return confirm('Submit this return to your Divisional Head? You will not be able to edit it further unless it is returned to you.');",
                    ]
                ) ?>
            </div>

        </div>

    <?php endif; ?>


    <?php ActiveForm::end(); ?>

    </div>


    <?php

    // Awareness row add/remove
    $this->registerJs(<<<JS

    let newAwarenessCounter = 0;

    document.getElementById('add-awareness-row')?.addEventListener('click', function () {

        newAwarenessCounter++;

        const key = 'new_' + newAwarenessCounter;

        const row = document.createElement('div');

        row.className = 'awareness-row border rounded p-2 mb-3';

        row.dataset.key = key;

        row.innerHTML = `

            <!-- First row -->
            <div class="row">

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">
                        Event Date
                    </label>

                    <input
                        type="date"
                        name="Bsc1AwarenessProgramme[\${key}][event_date]"
                        class="form-control form-control-sm">
                </div>

                <div class="col-md-4">
                    <label class="small font-weight-bold text-muted">
                        Nature of Programme
                    </label>

                    <input
                        type="text"
                        name="Bsc1AwarenessProgramme[\${key}][nature]"
                        class="form-control form-control-sm"
                        placeholder="Nature of programme">
                </div>

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">
                        Number of Participants
                    </label>

                    <input
                        type="number"
                        name="Bsc1AwarenessProgramme[\${key}][participants]"
                        class="form-control form-control-sm"
                        placeholder="Number">
                </div>

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted">
                        Cost (Rs.)
                    </label>

                    <input
                        type="number"
                        name="Bsc1AwarenessProgramme[\${key}][cost]"
                        class="form-control form-control-sm"
                        placeholder="Amount">
                </div>

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted d-block">
                        &nbsp;
                    </label>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger remove-awareness-row">
                        Remove
                    </button>
                </div>

            </div>

            <!-- Second row -->
            <div class="row mt-2">

                <div class="col-md-5">
                    <label class="small font-weight-bold text-muted">
                        Resource Person
                    </label>

                    <input
                        type="text"
                        name="Bsc1AwarenessProgramme[\${key}][resource_person]"
                        class="form-control form-control-sm"
                        placeholder="Name of resource person">
                </div>

                <div class="col-md-5">
                    <label class="small font-weight-bold text-muted">
                        Institution
                    </label>

                    <input
                        type="text"
                        name="Bsc1AwarenessProgramme[\${key}][institution]"
                        class="form-control form-control-sm"
                        placeholder="Institution / organisation">
                </div>

            </div>
        `;

        document.getElementById('awareness-rows').appendChild(row);

    });


    document.getElementById('awareness-rows')?.addEventListener('click', function (e) {

        if (e.target.classList.contains('remove-awareness-row')) {

            e.target.closest('.awareness-row').remove();

        }

    });

    JS);
    ?>
?>