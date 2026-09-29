<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Edit Set #' . $model->set_number;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-4" style="max-width: 760px;">

    <h3 class="mb-4">Edit Set #<?= Html::encode($model->set_number) ?></h3>

    <?php $form = \yii\widgets\ActiveForm::begin(['id' => 'edit-set-form']); ?>

    <!-- Date/Time -->
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">Timing</div>
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'start_datetime')->textInput(['type' => 'datetime-local']) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'end_datetime')->textInput(['type' => 'datetime-local']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- GPS -->
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">GPS Coordinates</div>
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <?= $form->field($model, 'start_gps_direction')->dropDownList(
                        ['N' => 'North', 'S' => 'South']
                    ) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'end_gps_direction')->dropDownList(
                        ['N' => 'North', 'S' => 'South']
                    ) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'start_gps_n')->textInput(['placeholder' => 'Start GPS N']) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'end_gps_n')->textInput(['placeholder' => 'End GPS N']) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'start_gps_e')->textInput(['placeholder' => 'Start GPS E']) ?>
                </div>
                <div class="col-6">
                    <?= $form->field($model, 'end_gps_e')->textInput(['placeholder' => 'End GPS E']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Catches -->
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">Catches</div>
        <div class="card-body" id="catches-wrapper">
            <?php foreach ($existingCatches as $i => $c): ?>
                <div class="catch-row border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Fish Type</label>
                            <select name="catches[<?= $i ?>][fish_type_id]" class="form-select fish-type-select" data-section="catches" data-index="<?= $i ?>">
                                <option value="">-- Select --</option>
                                <?php foreach ($fishTypes as $fid => $fname): ?>
                                    <option value="<?= $fid ?>" <?= $c->fish_type_id == $fid ? 'selected' : '' ?>>
                                        <?= Html::encode($fname) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Variant</label>
                            <select name="catches[<?= $i ?>][fish_variant_id]" class="form-select variant-select" id="catches-variant-<?= $i ?>">
                                <option value="<?= $c->fish_variant_id ?>" selected>[Loading...]</option>
                            </select>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" step="0.01" name="catches[<?= $i ?>][weight]"
                                   class="form-control" value="<?= Html::encode($c->weight) ?>">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label">Count</label>
                            <input type="number" name="catches[<?= $i ?>][fish_count]"
                                   class="form-control" value="<?= Html::encode($c->fish_count) ?>">
                        </div>
                        <div class="col-4 d-flex align-items-end mb-2">
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-row">Remove</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-secondary btn-sm" id="add-catch">+ Add Catch</button>
        </div>
    </div>

    <!-- Discarded Dead -->
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">Discarded Dead</div>
        <div class="card-body" id="dead-wrapper">
            <?php foreach ($existingDiscardedDead as $i => $d): ?>
                <div class="dead-row border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Fish Type</label>
                            <select name="discarded_dead[<?= $i ?>][fish_type_id]" class="form-select fish-type-select" data-section="dead" data-index="<?= $i ?>">
                                <option value="">-- Select --</option>
                                <?php foreach ($fishTypes as $fid => $fname): ?>
                                    <option value="<?= $fid ?>" <?= $d->fish_type_id == $fid ? 'selected' : '' ?>>
                                        <?= Html::encode($fname) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Variant</label>
                            <select name="discarded_dead[<?= $i ?>][fish_variant_id]" class="form-select variant-select" id="dead-variant-<?= $i ?>">
                                <option value="<?= $d->fish_variant_id ?>" selected>[Loading...]</option>
                            </select>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" step="0.01" name="discarded_dead[<?= $i ?>][weight]"
                                   class="form-control" value="<?= Html::encode($d->weight) ?>">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label">Count</label>
                            <input type="number" name="discarded_dead[<?= $i ?>][fish_count]"
                                   class="form-control" value="<?= Html::encode($d->fish_count) ?>">
                        </div>
                        <div class="col-4 d-flex align-items-end mb-2">
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-row">Remove</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-secondary btn-sm" id="add-dead">+ Add Discarded Dead</button>
        </div>
    </div>

    <!-- Discarded Live -->
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">Discarded Live</div>
        <div class="card-body" id="live-wrapper">
            <?php foreach ($existingDiscardedLive as $i => $l): ?>
                <div class="live-row border rounded p-3 mb-3">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form-label">Fish Type</label>
                            <select name="discarded_live[<?= $i ?>][fish_type_id]" class="form-select fish-type-select" data-section="live" data-index="<?= $i ?>">
                                <option value="">-- Select --</option>
                                <?php foreach ($fishTypes as $fid => $fname): ?>
                                    <option value="<?= $fid ?>" <?= $l->fish_type_id == $fid ? 'selected' : '' ?>>
                                        <?= Html::encode($fname) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label">Variant</label>
                            <select name="discarded_live[<?= $i ?>][fish_variant_id]" class="form-select variant-select" id="live-variant-<?= $i ?>">
                                <option value="<?= $l->fish_variant_id ?>" selected>[Loading...]</option>
                            </select>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" step="0.01" name="discarded_live[<?= $i ?>][weight]"
                                   class="form-control" value="<?= Html::encode($l->weight) ?>">
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form-label">Count</label>
                            <input type="number" name="discarded_live[<?= $i ?>][fish_count]"
                                   class="form-control" value="<?= Html::encode($l->fish_count) ?>">
                        </div>
                        <div class="col-4 d-flex align-items-end mb-2">
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-row">Remove</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-secondary btn-sm" id="add-live">+ Add Discarded Live</button>
        </div>
    </div>

    <div class="d-flex gap-2">
        <?= Html::submitButton('Save Changes', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancel', ['view', 'id' => $elogId], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php \yii\widgets\ActiveForm::end(); ?>
</div>

<?php
$variantUrl = Url::to(['e-log-edit/fish-variants']);
$fishTypesJson = json_encode($fishTypes);

// Pre-load existing variant selections
$preload = [];
foreach ($existingCatches as $i => $c) {
    $preload[] = ['section' => 'catches', 'index' => $i, 'typeId' => $c->fish_type_id, 'variantId' => $c->fish_variant_id];
}
foreach ($existingDiscardedDead as $i => $d) {
    $preload[] = ['section' => 'dead', 'index' => $i, 'typeId' => $d->fish_type_id, 'variantId' => $d->fish_variant_id];
}
foreach ($existingDiscardedLive as $i => $l) {
    $preload[] = ['section' => 'live', 'index' => $i, 'typeId' => $l->fish_type_id, 'variantId' => $l->fish_variant_id];
}
$preloadJson = json_encode($preload);
?>

<script>
const variantUrl  = <?= json_encode($variantUrl) ?>;
const preload     = <?= $preloadJson ?>;

// Counters for new rows
const counters = {
    catches: <?= count($existingCatches) ?>,
    dead:    <?= count($existingDiscardedDead) ?>,
    live:    <?= count($existingDiscardedLive) ?>,
};

// Load variants into a select, optionally pre-selecting one
function loadVariants(typeId, selectEl, selectedId = null) {
    if (!typeId) { selectEl.innerHTML = '<option value="">-- Select --</option>'; return; }
    fetch(`${variantUrl}?typeId=${typeId}`)  // change & to ?
        .then(r => r.json())
        .then(data => {
            selectEl.innerHTML = '<option value="">-- Select --</option>';
            data.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.textContent = v.name;
                if (selectedId && v.id == selectedId) opt.selected = true;
                selectEl.appendChild(opt);
            });
        });
}

// Pre-load variants for existing rows
preload.forEach(p => {
    const variantSel = document.getElementById(`${p.section}-variant-${p.index}`);
    if (variantSel) loadVariants(p.typeId, variantSel, p.variantId);
});

// Live change on fish type selects
document.addEventListener('change', function (e) {
    if (!e.target.classList.contains('fish-type-select')) return;
    const section  = e.target.dataset.section;
    const index    = e.target.dataset.index;
    const variantEl = document.getElementById(`${section}-variant-${index}`);
    if (variantEl) loadVariants(e.target.value, variantEl);
});

// Remove row
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('[class$="-row"]') && e.target.closest('div.border').remove();
    }
});

// Build a new row HTML
function newRow(section, namePrefix, index) {
    const fishOptions = <?= json_encode(array_map(fn($id, $name) => "<option value=\"$id\">$name</option>", array_keys($fishTypes), array_values($fishTypes))) ?>;
    // rebuild cleanly
    let opts = '<option value="">-- Select --</option>';
    <?php foreach ($fishTypes as $fid => $fname): ?>
    opts += `<option value="<?= $fid ?>"><?= addslashes($fname) ?></option>`;
    <?php endforeach; ?>

    return `
    <div class="${section}-row border rounded p-3 mb-3">
        <div class="row">
            <div class="col-6 mb-2">
                <label class="form-label">Fish Type</label>
                <select name="${namePrefix}[${index}][fish_type_id]" class="form-select fish-type-select"
                        data-section="${section}" data-index="${index}">
                    ${opts}
                </select>
            </div>
            <div class="col-6 mb-2">
                <label class="form-label">Variant</label>
                <select name="${namePrefix}[${index}][fish_variant_id]" class="form-select variant-select"
                        id="${section}-variant-${index}">
                    <option value="">-- Select type first --</option>
                </select>
            </div>
            <div class="col-4 mb-2">
                <label class="form-label">Weight (kg)</label>
                <input type="number" step="0.01" name="${namePrefix}[${index}][weight]" class="form-control">
            </div>
            <div class="col-4 mb-2">
                <label class="form-label">Count</label>
                <input type="number" name="${namePrefix}[${index}][fish_count]" class="form-control">
            </div>
            <div class="col-4 d-flex align-items-end mb-2">
                <button type="button" class="btn btn-danger btn-sm w-100 remove-row">Remove</button>
            </div>
        </div>
    </div>`;
}

document.getElementById('add-catch').addEventListener('click', () => {
    document.getElementById('catches-wrapper').insertAdjacentHTML('beforeend', newRow('catches', 'catches', counters.catches++));
});
document.getElementById('add-dead').addEventListener('click', () => {
    document.getElementById('dead-wrapper').insertAdjacentHTML('beforeend', newRow('dead', 'discarded_dead', counters.dead++));
});
document.getElementById('add-live').addEventListener('click', () => {
    document.getElementById('live-wrapper').insertAdjacentHTML('beforeend', newRow('live', 'discarded_live', counters.live++));
});
</script>