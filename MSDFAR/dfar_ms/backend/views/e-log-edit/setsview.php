<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;
use backend\models\ELogSetCatch;
use backend\models\ELogSetDiscardedDead;
use backend\models\ELogSetDiscardedLive;

$this->title = 'Sets — ' . strtoupper($type) . ' Gear ';
$typeColors = [
    'longline' => 'primary',
    'gillnet' => 'success',
    'ringnet' => 'warning'
];
$color = $typeColors[$type] ?? 'secondary';
$gearTypeJs = Json::encode($type);
?>

<div class="container mt-3">

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ((array) Yii::$app->session->getFlash('error', [], true) as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <!-- EXISTING SETS -->
    <div class="card mb-4">
        <div class="card-header bg-<?= $color ?> text-white">
            <h5><?= strtoupper($type) ?> Gear Existing Sets</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Set #</th>
                        <th>Start DateTime</th>
                        <th>Start GPS</th>
                        <th>End DateTime</th>
                        <th>End GPS</th>
                        <th>Retained Catch</th>
                        <th>Discarded Dead</th>
                        <th>Discarded Live</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sets)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No sets yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sets as $set): ?>
                            <tr>
                                <td><?= $set->set_number ?></td>
                                <td><?= $set->start_datetime ?></td>
                                <td>
                                    <strong><?= $set->start_gps_direction ?></strong>
                                    :<?= $set->start_gps_n ?><br>
                                    E: <?= $set->start_gps_e ?>
                                </td>
                                <td><?= $set->end_datetime ?></td>
                                <td>
                                    <strong><?= $set->end_gps_direction ?></strong>
                                    :<?= $set->end_gps_n ?><br>
                                    E: <?= $set->end_gps_e ?>
                                </td>

                                <!-- Retained -->
                                <td>
                                    <?php $catches = ELogSetCatch::find()->where(['e_log_set_id' => $set->id])->all(); ?>
                                    <?php if (empty($catches)): ?>
                                        <span class="text-muted">None</span>
                                    <?php else: ?>
                                        <?php foreach ($catches as $c): ?>
                                            <small>
                                                <?= $c->fishType->name ?? '?' ?> /
                                                <?= $c->fishVariant->name ?? '?' ?>
                                                — <?= $c->weight ?>kg (<?= $c->fish_count ?>)
                                            </small><br>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>

                                <!-- Discarded Dead -->
                                <td>
                                    <?php $deadRows = ELogSetDiscardedDead::find()->where(['e_log_set_id' => $set->id])->all(); ?>
                                    <?php if (empty($deadRows)): ?>
                                        <span class="text-muted">None</span>
                                    <?php else: ?>
                                        <?php foreach ($deadRows as $c): ?>
                                            <small>
                                                <?= $c->fishType->name ?? '?' ?> /
                                                <?= $c->fishVariant->name ?? '?' ?>
                                                — <?= $c->weight ?>kg (<?= $c->fish_count ?>)
                                            </small><br>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>

                                <!-- Discarded Live -->
                                <td>
                                    <?php $liveRows = ELogSetDiscardedLive::find()->where(['e_log_set_id' => $set->id])->all(); ?>
                                    <?php if (empty($liveRows)): ?>
                                        <span class="text-muted">None</span>
                                    <?php else: ?>
                                        <?php foreach ($liveRows as $c): ?>
                                            <small>
                                                <?= $c->fishType->name ?? '?' ?> /
                                                <?= $c->fishVariant->name ?? '?' ?>
                                                — <?= $c->weight ?>kg (<?= $c->fish_count ?>)
                                            </small><br>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= Html::a(
                                        'Delete',
                                        [
                                            'delete-set',
                                            'id' => $set->id,
                                            'type' => $type,
                                            'gearId' => $gearId,
                                            'elogId' => $elogId,
                                        ],
                                        [
                                            'class' => 'btn btn-danger btn-sm',
                                            'data-confirm' => 'Delete this set?',
                                            'data-method' => 'post',
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ADD NEW SET -->
    <div class="card">
        <div class="card-header bg-<?= $color ?> text-white">
            <h5>Add Set #<?= $model->set_number ?></h5>
        </div>

        <?php $form = ActiveForm::begin(); ?>

        <?= Html::hiddenInput('ELogSets[gear_id]', $gearId) ?>
        <?= Html::hiddenInput('ELogSets[gear_type]', $type) ?>
        <?= Html::hiddenInput('ELogSets[set_number]', $model->set_number) ?>

        <div class="card-body">
            <div class="row">

                <!-- ===== START ===== -->
                <div class="col-12 mb-2">
                    <h6 class="text-muted fw-bold">▶ Start</h6>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'start_datetime')->input('datetime-local')
                        ->label('Start GPS Direction <span class="text-danger">*</span>', ['encode' => false]) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'start_gps_direction')
                        ->dropDownList(
                            ['S' => 'South (S)', 'N' => 'North (N)'],
                            [
                                'id' => 'start_gps_direction',
                                'onchange' => 'updateGpsN("start")',
                                'prompt' => '— Select Direction —',
                            ]
                        )
                        ->label('Start GPS Direction <span class="text-danger">*</span>', ['encode' => false]) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'start_gps_n')
                        ->textInput([
                            'id' => 'start_gps_n',
                            'type' => 'number',
                            'step' => '0.01',
                            'class' => 'form-control',
                            'placeholder' => 'e.g. 12.30',
                            'onkeyup' => 'updateGpsE("start")',
                            'onchange' => 'updateGpsE("start")',
                        ])
                        ->label('Start GPS N <span class="text-danger">*</span>', ['encode' => false]) ?>
                    <small id="start_gps_n_hint" class="text-muted"></small>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'start_gps_e')
                        ->textInput([
                            'id' => 'start_gps_e',
                            'type' => 'number',
                            'step' => '0.01',
                            'class' => 'form-control',
                            'placeholder' => '— Select N First —',
                            'disabled' => true,
                            'onkeyup' => 'validateGpsE("start")',
                            'onchange' => 'validateGpsE("start")',
                        ])
                        ->label('Start GPS E <span class="text-danger">*</span>', ['encode' => false]) ?>
                    <small id="start_gps_e_hint" class="text-muted"></small>
                </div>

                <!-- ===== END ===== -->
                <div class="col-12 mb-2 mt-3">
                    <h6 class="text-muted fw-bold">⏹ End</h6>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'end_datetime')->input('datetime-local') ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'end_gps_direction')
                        ->dropDownList(
                            ['S' => 'South (S)', 'N' => 'North (N)'],
                            [
                                'id' => 'end_gps_direction',
                                'onchange' => 'updateGpsN("end")',
                                'prompt' => '— Select Direction —',
                            ]
                        ) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'end_gps_n')
                        ->textInput([
                            'id' => 'end_gps_n',
                            'type' => 'number',
                            'step' => '0.01',
                            'class' => 'form-control',
                            'placeholder' => 'e.g. 12.30',
                            'onkeyup' => 'updateGpsE("end")',
                            'onchange' => 'updateGpsE("end")',
                        ]) ?>
                    <small id="end_gps_n_hint" class="text-muted"></small>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'end_gps_e')
                        ->textInput([
                            'id' => 'end_gps_e',
                            'type' => 'number',
                            'step' => '0.01',
                            'class' => 'form-control',
                            'placeholder' => '— Select N First —',
                            'disabled' => true,
                            'onkeyup' => 'validateGpsE("end")',
                            'onchange' => 'validateGpsE("end")',
                        ]) ?>
                    <small id="end_gps_e_hint" class="text-muted"></small>
                </div>

                <style>
    /* Responsive tweaks for the fish-adding sections only (tablet & mobile) */
    @media (max-width: 991.98px) {
        .fish-add-card .card-header.d-flex {
            flex-wrap: wrap;
            row-gap: 0.5rem;
        }
    }

    @media (max-width: 767.98px) {
        .fish-add-card table.table-bordered {
            font-size: 0.85rem;
        }
        .fish-add-card table.table-bordered td,
        .fish-add-card table.table-bordered th {
            padding: 0.4rem;
        }
    }

    @media (max-width: 575.98px) {
        .fish-add-card .card-header.d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .fish-add-card .card-header.d-flex button {
            width: 100%;
        }
    }
input[id^="weight-"],
input[id^="count-"] {
    min-height: 48px;
    min-width: 120px;
    font-size: 1.1rem;
    padding: 0.5rem 0.75rem;
}

@media (max-width: 767.98px) {
    input[id^="weight-"],
    input[id^="count-"] {
        min-height: 52px;
        min-width: 100px;
        font-size: 1.15rem;
    }
}
    
    
</style>

<!-- ===== RETAINED FISH ===== -->
<div class="col-12 mt-4">
    <div class="card border-secondary fish-add-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Retained Fish Data</h6>
            <button type="button" class="btn btn-success btn-sm"
                onclick="addRow('catches', 'catch-rows', 'no-catch-msg', null, bycatchTypeId)">
                + Add Fish
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fish Type</th>
                            <th>Fish Variant</th>
                            <th>Weight (kg)</th>
                            <th>No. of Fish</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="catch-rows"></tbody>
                </table>
            </div>
            <p class="text-muted p-3 mb-0" id="no-catch-msg">
                No fish added yet. Click "+ Add Fish" to start.
            </p>
        </div>
    </div>
</div>

<!-- ===== DISCARDED DEAD ===== -->
<div class="col-12 mt-4">
    <div class="card border-danger fish-add-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Discarded Dead Fish Data</h6>
            <button type="button" class="btn btn-danger btn-sm"
                onclick="addRow('discarded_dead', 'dead-rows', 'no-dead-msg', bycatchTypeId)">
                + Add Fish
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fish Type</th>
                            <th>Fish Variant</th>
                            <th>Weight (kg)</th>
                            <th>No. of Fish</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="dead-rows"></tbody>
                </table>
            </div>
            <p class="text-muted p-3 mb-0" id="no-dead-msg">
                No fish added yet. Click "+ Add Fish" to start.
            </p>
        </div>
    </div>
</div>

<!-- ===== DISCARDED LIVE ===== -->
<div class="col-12 mt-4">
    <div class="card border-info fish-add-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Discarded Live Fish Data</h6>
            <button type="button" class="btn btn-info btn-sm"
                onclick="addRow('discarded_live', 'live-rows', 'no-live-msg', bycatchTypeId)">
                + Add Fish
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fish Type</th>
                            <th>Fish Variant</th>
                            <th>Weight (kg)</th>
                            <th>No. of Fish</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="live-rows"></tbody>
                </table>
            </div>
            <p class="text-muted p-3 mb-0" id="no-live-msg">
                No fish added yet. Click "+ Add Fish" to start.
            </p>
        </div>
    </div>
</div>
            </div><!-- /.row -->
        </div><!-- /.card-body -->

        <div class="card-footer text-end">
            <?= Html::submitButton('Save Set', ['class' => 'btn btn-' . $color]) ?>

            <?= Html::a(
                'Back',
                ($page)
                ? ['e-log-view/view', 'id' => $elogId]
                : ['setdata', 'id' => $elogId],
                ['class' => 'btn btn-secondary ms-2']
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <?php if (!$page): ?>
            <div class="text-end mt-2">
                <a href="<?= \yii\helpers\Url::to(['main']) ?>" class="btn btn-outline-secondary ml-3 px-50"
                    onclick="return confirm('Are you sure you want to finish?')">
                    Finish Data Entry
                </a>
            </div>
        <?php endif; ?>

    </div><!-- /.card -->

</div><!-- /.container -->

<?php
$fishTypesJson = Json::encode($fishTypes);
$variantUrl = \yii\helpers\Url::to(['fish-variants']);
?>

<script>
    // ─── Gear type (for per-fish weight validation) ──────────────────────────────
    const gearType = <?= $gearTypeJs ?>;

    // ─── Fish type / variant dropdowns ───────────────────────────────────────────
    const fishTypes = <?= $fishTypesJson ?>;
    const variantUrl = '<?= $variantUrl ?>';

    // Dynamically detect the "bycatch and discards" type ID from the fishTypes map.
    const bycatchTypeId = (Object.entries(fishTypes).find(
        ([id, name]) => name.toLowerCase().includes('bycatch')
    ) ?? [null])[0];

    // ─── Min weight per individual fish (kg), keyed by fish_variant_id ───────────
    // Variants not listed here have no minimum enforced.
    // This validation is skipped entirely for ringnet gear.
    const variantMinWeight = {
        1: 5,     // Albacore
        2: 5,     // Bigeye tuna
        4: 1,     // Skipjack tuna
        5: 5,     // Yellowfin tuna
        6: 0.05,  // Bigeye scad
        8: 1,     // Dolphin fish
        9: 0.05,  // Indian mackerel
        10: 0.05,  // Indian scad
        11: 0.05,  // Needle cuttle fish
        12: 0.1,   // Ocean trigger fish
        14: 0.1,   // Rainbow runner
        16: 0.1,   // Trevally
        17: 5,     // Black marlin
        18: 5,     // Blue marlin
        20: 5,     // Sailfish
        21: 2,     // Shortbill spearfish
        22: 3,     // Striped marlin
        23: 3,     // Swordfish
        24: 3,     // Blue shark
        26: 5,     // Giant devil ray
        27: 3,     // Mako shark
        28: 5,     // Manta rays
        33: 3,     // Silky shark
        35: 0.3,   // Bullet tuna
        36: 0.3,   // Frigate tuna
        37: 0.5,   // Kawakawa
        38: 1,     // Longtail tuna
        39: 0.1,   // Narrow-barred Spanish mackerel
        41: 0.1,   // Spanish mackerel
        42: 1,     // Wahoo
    };

    const counters = { catches: 0, discarded_dead: 0, discarded_live: 0 };

    /**
     * Builds <option> elements for the Fish Type dropdown.
     * @param {string|null} filterToId  When set, only that one type is included.
     */
    function buildTypeOptions(filterToId = null, excludeId = null) {
        let opts = '<option value="">Select Fish Type</option>';
        for (const [id, name] of Object.entries(fishTypes)) {
            if (filterToId !== null && id !== String(filterToId)) continue;
            if (excludeId !== null && id === String(excludeId)) continue;
            opts += `<option value="${id}">${name}</option>`;
        }
        return opts;
    }

    /**
     * Adds a new fish row to the given table body.
     * @param {string}      namespace  Form namespace: 'catches' | 'discarded_dead' | 'discarded_live'
     * @param {string}      tbodyId    ID of the <tbody> to append to
     * @param {string}      noMsgId    ID of the "no rows" message element
     * @param {string|null} typeFilter When set, locks the Fish Type to that ID only
     */
    function addRow(namespace, tbodyId, noMsgId, typeFilter = null, excludeFilter = null) {
        const i = counters[namespace]++;
        const uid = `${namespace}_${i}`;
        const row = document.createElement('tr');
        row.id = `row-${uid}`;

        const typeOptions = buildTypeOptions(typeFilter, excludeFilter);
        const isLocked = typeFilter !== null && typeFilter !== undefined;

        row.innerHTML = `
    <td>
        <select name="${namespace}[${i}][fish_type_id]"
                class="form-select"
                id="type-${uid}"
                onchange="loadVariants(this.value, '${uid}', '${namespace}')"
                ${isLocked ? 'disabled' : ''}>
            ${typeOptions}
        </select>
        ${isLocked
                ? `<input type="hidden"
                      name="${namespace}[${i}][fish_type_id]"
                      value="${typeFilter}">`
                : ''}
    </td>
    <td>
        <select name="${namespace}[${i}][fish_variant_id]"
                class="form-select"
                id="variant-${uid}"
                onchange="validateRowWeight('${uid}', '${namespace}')">
            <option value="">Loading...</option>
        </select>
    </td>
    <td>
        <input type="number"
               name="${namespace}[${i}][weight]"
               id="weight-${uid}"
               class="form-control"
               step="0.01" min="0" value="1"
               placeholder="e.g. 12.50"
               onfocus="if(this.value==='1') this.value=''"
               onblur="if(this.value==='') this.value='1'"
               onkeyup="validateRowWeight('${uid}', '${namespace}')"
               onchange="validateRowWeight('${uid}', '${namespace}')">
        <small id="weight-hint-${uid}" class="text-muted"></small>
    </td>
    <td>
        <input type="number"
               name="${namespace}[${i}][fish_count]"
               id="count-${uid}"
               class="form-control"
               min="0"
               placeholder="e.g. 5"
               value="0"
               onfocus="if(this.value==='0') this.value=''"
               onblur="if(this.value==='') this.value='0'"
               onkeyup="validateRowWeight('${uid}', '${namespace}')"
               onchange="validateRowWeight('${uid}', '${namespace}')">
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm"
                onclick="removeRow('${uid}', '${tbodyId}', '${noMsgId}')">
            Remove
        </button>
    </td>`;

        document.getElementById(tbodyId).appendChild(row);
        document.getElementById(noMsgId).style.display = 'none';

        // If the type is locked, auto-select it and immediately fetch its variants.
        if (isLocked) {
            const typeSelect = document.getElementById(`type-${uid}`);
            if (typeSelect) typeSelect.value = String(typeFilter);
            loadVariants(String(typeFilter), uid, namespace);
        }
    }
    function hasInvalidMinutes(val) {
        if (val === '' || val === null) return false;
        const str = String(val);
        const dotIndex = str.indexOf('.');
        if (dotIndex === -1) return false;
        const decimals = str.substring(dotIndex + 1);
        // Take first two decimal digits as minutes
        const minutes = parseInt(decimals.substring(0, 2).padEnd(2, '0'), 10);
        return minutes >= 60;
    }
    function removeRow(uid, tbodyId, noMsgId) {
        const row = document.getElementById(`row-${uid}`);
        if (row) row.remove();
        if (document.getElementById(tbodyId).children.length === 0) {
            document.getElementById(noMsgId).style.display = 'block';
        }
        updateVariantLocking();
    }

    function loadVariants(typeId, uid, namespace) {
        const sel = document.getElementById(`variant-${uid}`);
        if (!sel) return;
        sel.innerHTML = '<option value="">Loading...</option>';
        if (!typeId) {
            sel.innerHTML = '<option value="">Select Type First</option>';
            return;
        }
        fetch(`${variantUrl}?typeId=${typeId}`)
            .then(r => r.json())
            .then(data => {
                let opts = '<option value="">Select Variant</option>';
                data.forEach(v => { opts += `<option value="${v.id}">${v.name}</option>`; });
                sel.innerHTML = opts;
                updateVariantLocking();
                validateRowWeight(uid, namespace);
            })
            .catch(() => { sel.innerHTML = '<option value="">Error loading</option>'; });
    }

    function getAllSelectedVariants() {
        const selected = [];
        document.querySelectorAll('select[name*="[fish_variant_id]"]').forEach(sel => {
            if (sel.value) selected.push(sel.value);
        });
        return selected;
    }

    function updateVariantLocking() {
        const selected = getAllSelectedVariants();
        document.querySelectorAll('select[name*="[fish_variant_id]"]').forEach(sel => {
            Array.from(sel.options).forEach(opt => {
                if (!opt.value) return;
                opt.disabled = selected.includes(opt.value) && opt.value !== sel.value;
            });
        });
    }

    document.addEventListener('change', function (e) {
        if (e.target.name && e.target.name.includes('fish_variant_id')) {
            updateVariantLocking();
        }
    });

    // ─── Per-fish weight validation (min only) ───────────────────────────────────
    /**
     * Validates that (weight / fish_count) is not below the per-variant
     * minimum individual fish weight. Skipped entirely for ringnet gear.
     *
     * @param {string} uid       Row unique ID, e.g. 'catches_0'
     * @param {string} namespace Form namespace: 'catches' | 'discarded_dead' | 'discarded_live'
     */
    function validateRowWeight(uid, namespace) {
        if (gearType === 'ringnet') return;

        const variantSel = document.getElementById(`variant-${uid}`);
        const weightEl = document.getElementById(`weight-${uid}`);
        const countEl = document.getElementById(`count-${uid}`);
        const hintEl = document.getElementById(`weight-hint-${uid}`);

        if (!variantSel || !weightEl || !countEl) return;

        const variantId = variantSel.value;
        const weight = parseFloat(weightEl.value);
        const count = parseInt(countEl.value, 10);

        // ── Both weight and count are zero or empty ──
        if ((isNaN(weight) || weight === 0) && (isNaN(count) || count === 0)) {
            weightEl.classList.add('is-invalid');
            weightEl.classList.remove('is-valid');
            if (hintEl) {
                hintEl.textContent = 'Weight and fish count cannot both be zero.';
                hintEl.className = 'text-danger';
            }
            return;
        }

        const minPerFish = variantMinWeight[variantId];

        // No limit defined for this variant — clear any previous state
        if (minPerFish === undefined || !variantId) {
            weightEl.classList.remove('is-invalid', 'is-valid');
            if (hintEl) hintEl.textContent = '';
            return;
        }

        // Incomplete input — just show the limit as a hint, no red/green yet
        if (isNaN(weight) || isNaN(count) || count <= 0 || weightEl.value === '') {
            weightEl.classList.remove('is-invalid', 'is-valid');
            if (hintEl) {
                hintEl.textContent = `Min ${minPerFish} kg per fish`;
                hintEl.className = 'text-muted';
            }
            return;
        }

        const perFish = weight / count;
        const valid = perFish >= minPerFish;

        weightEl.classList.toggle('is-invalid', !valid);
        weightEl.classList.toggle('is-valid', valid);

        if (hintEl) {
            if (!valid) {
                hintEl.textContent =
                    `Each fish would be ${perFish.toFixed(2)} kg — min allowed is ${minPerFish} kg per fish.`;
                hintEl.className = 'text-danger';
            } else {
                hintEl.textContent = `${perFish.toFixed(2)} kg per fish (min ${minPerFish} kg) ✓`;
                hintEl.className = 'text-muted';
            }
        }
    }

    // ─── GPS dependent fields ────────────────────────────────────────────────────
    const gpsMap = {
        N: {
            "0": [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            "1": [49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92],
            "2": [50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92],
            "3": [50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92],
            "4": [51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91],
            "5": [52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90],
            "6": [52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89],
            "7": [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 78, 79, 81, 82, 83, 84, 85, 86, 87, 88, 89],
            "8": [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89],
            "9": [54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89],
            "10": [56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88],
            "11": [57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 81, 82, 83, 84, 85, 86, 87, 88],
            "12": [57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 83, 84, 85, 86, 87, 88, 89],
            "13": [57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 83, 84, 85, 86, 87, 88, 89],
            "14": [57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 84, 85, 86, 87, 88, 89],
            "15": [58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 85, 86, 87, 88, 89, 90],
            "16": [59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 86, 87, 88, 89, 90],
            "17": [60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 88, 89, 90],
            "18": [61, 62, 63, 64, 65, 66, 67, 68, 89, 90],
            "19": [62, 63, 64, 65, 66, 67],
            "20": [62, 63, 64, 65, 66],
            "21": [63]
        },
        S: {
            "0": [46,47,48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            "1": [46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94],
            "2": [45, 46, 47, 48, 49, 50, 51, 52, 53, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95],
            "3": [44, 45, 46, 47, 48, 49, 50, 51, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95],
            "4": [44, 45, 46, 47, 48, 49, 50, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97],
            "5": [43, 44, 45, 46, 47, 48, 49, 50, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98],
            "6": [43, 44, 45, 46, 47, 48, 49, 59, 60, 61, 62, 63, 64, 65, 66, 67, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98],
            "7": [43, 44, 45, 46, 47, 48, 49, 59, 60, 61, 62, 63, 64, 65, 66, 67, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98],
            "8": [43, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99],
            "9": [59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100],
            "10": [60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94],
            "11": [59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            "12": [59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            "13": [60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            "14": [61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            "15": [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            "16": [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94],
            "17": [63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95],
            "18": [65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100],
            "19": [66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100],
            "20": [66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100]
        }
    };

    function getEListForN(dir, nRaw) {
        if (!dir || nRaw === '' || nRaw === null || !gpsMap[dir]) return null;
        const nKey = String(Math.floor(parseFloat(nRaw)));
        const eList = gpsMap[dir][nKey];
        if (!eList || !eList.length) return null;
        return eList;
    }

    function getEBoundsForN(dir, nRaw) {
        const eList = getEListForN(dir, nRaw);
        if (!eList) return null;
        return [Math.min(...eList), Math.max(...eList)];
    }

    function updateGpsN(prefix) {
        const nInput = document.getElementById(prefix + '_gps_n');
        const eInput = document.getElementById(prefix + '_gps_e');
        const eHint = document.getElementById(prefix + '_gps_e_hint');

        nInput.value = '';
        eInput.value = '';
        eInput.disabled = true;
        eInput.placeholder = '— Select N First —';
        if (eHint) eHint.textContent = '';
    }

    function updateGpsE(prefix) {
        const dir = document.getElementById(prefix + '_gps_direction').value;
        const nInput = document.getElementById(prefix + '_gps_n');
        const nRaw = nInput.value;
        const eInput = document.getElementById(prefix + '_gps_e');
        const eHint = document.getElementById(prefix + '_gps_e_hint');
        const nHint = document.getElementById(prefix + '_gps_n_hint');

        // Validate N minutes
        if (hasInvalidMinutes(nRaw)) {
            nInput.classList.add('is-invalid');
            nInput.classList.remove('is-valid');
            if (nHint) {
                nHint.textContent = 'Decimal  must be .00–.59';
                nHint.className = 'text-danger';
            }
            eInput.disabled = true;
            eInput.value = '';
            return;
        } else {
            nInput.classList.remove('is-invalid');
            if (nRaw !== '') nInput.classList.add('is-valid');
            if (nHint) nHint.textContent = '';
        }

        const eList = getEListForN(dir, nRaw);

        if (!eList) {
            eInput.disabled = true;
            eInput.value = '';
            eInput.placeholder = '— Select N First —';
            if (eHint) eHint.textContent = '';
            return;
        }

        const sortedList = [...eList].sort((a, b) => a - b);
        eInput.disabled = false;
        eInput.placeholder = `e.g. ${sortedList[0]}.00`;
        eInput.removeAttribute('min');
        eInput.removeAttribute('max');
        if (eHint) eHint.textContent =
            `Allowed E: ${sortedList.join(', ')} (decimals .00–.59 only)`;

        validateGpsE(prefix);
    }

    function validateGpsE(prefix) {
        const dir = document.getElementById(prefix + '_gps_direction').value;
        const nRaw = document.getElementById(prefix + '_gps_n').value;
        const eInput = document.getElementById(prefix + '_gps_e');
        const eHint = document.getElementById(prefix + '_gps_e_hint');

        const eList = getEListForN(dir, nRaw);
        if (!eList || eInput.value === '') {
            eInput.classList.remove('is-invalid', 'is-valid');
            return;
        }

        const eVal = parseFloat(eInput.value);
        const eKey = Math.floor(eVal);
        const inRange = !isNaN(eVal) && eList.includes(eKey);
        const badMins = hasInvalidMinutes(eInput.value);
        const valid = inRange && !badMins;

        eInput.classList.toggle('is-invalid', !valid);
        eInput.classList.toggle('is-valid', valid);

        if (eHint) {
            const sortedList = [...eList].sort((a, b) => a - b).join(', ');
            if (badMins) {
                eHint.textContent = 'Decimal  must be .00–.59';
                eHint.className = 'text-danger';
            } else {
                eHint.textContent = valid
                    ? `Allowed E: ${sortedList} (decimals .00–.59 only)`
                    : `Out of range! Allowed E: ${sortedList}`;
                eHint.classList.toggle('text-danger', !valid);
                eHint.classList.toggle('text-muted', valid);
            }
        }
    }

    // ─── Init ────────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        updateGpsN('start');
        updateGpsN('end');

        const formEl = document.querySelector('form');
        if (formEl) {
            formEl.addEventListener('submit', function (e) {
                // Zero weight + count check
                const allRows = document.querySelectorAll('#catch-rows tr, #dead-rows tr, #live-rows tr');
                for (const row of allRows) {
                    const weightInput = row.querySelector('input[id^="weight-"]');
                    const countInput = row.querySelector('input[id^="count-"]');
                    if (!weightInput || !countInput) continue;

                    const w = parseFloat(weightInput.value);
                    const c = parseInt(countInput.value, 10);

                    if ((isNaN(w) || w === 0) && (isNaN(c) || c === 0)) {
                        e.preventDefault();
                        alert('Each fish entry must have a weight or a fish count greater than zero.');
                        weightInput.focus();
                        return;
                    }
                }
                // GPS minutes validation (.00–.59 only)
                const fields = ['start_gps_n', 'start_gps_e', 'end_gps_n', 'end_gps_e'];
                for (const fieldId of fields) {
                    const el = document.getElementById(fieldId);
                    if (el && hasInvalidMinutes(el.value)) {
                        e.preventDefault();
                        alert(`${fieldId.replace(/_/g, ' ').toUpperCase()}: decimal must be .00–.59`);
                        el.focus();
                        return;
                    }
                }

                // GPS E empty check
                const startE = document.getElementById('start_gps_e');
                if (!startE.value.trim()) {
                    e.preventDefault();
                    alert('Start GPS E is empty. Please enter a value before submitting.');
                    startE.focus();
                    return;
                }

                // Per-fish weight validation (skipped for ringnet)
                if (gearType !== 'ringnet') {
                    const invalidWeights = document.querySelectorAll(
                        '#catch-rows .is-invalid, #dead-rows .is-invalid, #live-rows .is-invalid'
                    );
                    if (invalidWeights.length > 0) {
                        e.preventDefault();
                        alert('One or more fish entries are below the minimum allowed weight per fish. Please correct the highlighted fields before saving.');
                        invalidWeights[0].focus();
                    }
                }
            });
        }
    });
</script>