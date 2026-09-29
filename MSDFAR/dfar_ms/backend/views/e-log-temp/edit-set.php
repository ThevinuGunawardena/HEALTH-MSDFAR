<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use backend\models\ELogCatchTemp;
use backend\models\MELogFishType;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\ELogSetTemp */
/* @var $elog backend\models\ELogTemp */

$this->title = 'Edit Set #' . $model->set_number;
$this->params['breadcrumbs'][] = ['label' => 'E-Log Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'View', 'url' => ['view', 'id' => $elog->id]];
$this->params['breadcrumbs'][] = 'Edit Set';

$typeColors = [
    'longline' => 'primary',
    'gillnet' => 'success',
    'ringnet' => 'warning',
];
$color = $typeColors[$elog->gear_type] ?? 'secondary';

$fishTypes = ArrayHelper::map(
    MELogFishType::find()->orderBy('name')->all(),
    'id',
    'name'
);

$existingCatches = ELogCatchTemp::find()
    ->where(['e_log_set_temp_id' => $model->id])
    ->all();

$catchesData = [];
foreach ($existingCatches as $c) {
    $catchesData[] = [
        'id' => $c->id,
        'fish_type_id' => $c->fish_type_id,
        'fish_variant_id' => $c->fish_variant_id,
        'variant_name' => $c->fishVariant->name ?? '',
        'weight' => $c->weight,
        'fish_count' => $c->fish_count,
    ];
}

$fishTypesJson = Json::encode($fishTypes);
$catchesJson = Json::encode($catchesData);
$variantUrl = Url::to(['e-log-temp/fish-variants']);
$gearTypeJs = Json::encode($elog->gear_type);
?>

<div class="container e-log-temp-edit-set mt-3">
    <div class="text-end mb-3">
        <?= Html::a('Back to View', ['view', 'id' => $elog->id], ['class' => 'btn btn-secondary btn-sm']) ?>
    </div>

    <div class="card">
        <div class="card-header bg-<?= $color ?> text-white">
            <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'edit-set-form']); ?>

        <?= Html::hiddenInput('ELogSetTemp[elog_id]', $elog->id) ?>
        <?= Html::hiddenInput('ELogSetTemp[gear_type]', $elog->gear_type) ?>
        <?= Html::hiddenInput('ELogSetTemp[set_number]', $model->set_number) ?>

        <div class="card-body">
            <div class="row">

                <!-- ===== START ===== -->
                <div class="col-12 mb-2">
                    <h6 class="text-muted fw-bold">▶ Start</h6>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'start_datetime')->input('datetime-local', [
                        'value' => $model->start_datetime ? date('Y-m-d\TH:i', strtotime($model->start_datetime)) : '',
                    ]) ?>
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
                        ) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'start_gps_n')
                        ->textInput([
                            'id' => 'start_gps_n',
                            'type' => 'number',
                            'step' => '0.01',
                            'class' => 'form-control',
                            'onkeyup' => 'updateGpsE("start")',
                            'onchange' => 'updateGpsE("start")',
                        ]) ?>
                    <small id="start_gps_n_hint" class="text-muted"></small>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'start_gps_e')
                        ->textInput([
                            'id' => 'start_gps_e',
                            'type' => 'number',
                            'step' => '0.01',
                            'class' => 'form-control',
                            'onkeyup' => 'validateGpsE("start")',
                            'onchange' => 'validateGpsE("start")',
                        ]) ?>
                    <small id="start_gps_e_hint" class="text-muted"></small>
                </div>

                <!-- ===== END ===== -->
                <div class="col-12 mb-2 mt-3">
                    <h6 class="text-muted fw-bold">⏹ End</h6>
                </div>

                <div class="col-md-3">
                    <?= $form->field($model, 'end_datetime')->input('datetime-local', [
                        'value' => $model->end_datetime ? date('Y-m-d\TH:i', strtotime($model->end_datetime)) : '',
                    ]) ?>
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
                            'onkeyup' => 'validateGpsE("end")',
                            'onchange' => 'validateGpsE("end")',
                        ]) ?>
                    <small id="end_gps_e_hint" class="text-muted"></small>
                </div>

                <!-- ===== RETAINED FISH ===== -->
                <div class="col-12 mt-4">
                    <div class="card border-secondary fish-add-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">Retained Fish Data</h6>
                            <button type="button" class="btn btn-success btn-sm"
                                onclick="addRow(null)">
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
                            <p class="text-muted p-3 mb-0" id="no-catch-msg" style="display:none;">
                                No fish added yet. Click "+ Add Fish" to start.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer text-end">
            <?= Html::submitButton('Save Set', ['class' => 'btn btn-' . $color]) ?>
            <?= Html::a('Cancel', ['view', 'id' => $elog->id], ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
    const gearType = <?= $gearTypeJs ?>;
    const fishTypes = <?= $fishTypesJson ?>;
    const existingCatches = <?= $catchesJson ?>;
    const variantUrl = '<?= $variantUrl ?>';

    const variantMinWeight = {
        1: 5, 2: 5, 4: 1, 5: 5, 6: 0.05, 8: 1, 9: 0.05, 10: 0.05, 11: 0.05,
        12: 0.1, 14: 0.1, 16: 0.1, 17: 5, 18: 5, 20: 5, 21: 2, 22: 3, 23: 3,
        24: 3, 26: 5, 27: 3, 28: 5, 33: 3, 35: 0.3, 36: 0.3, 37: 0.5, 38: 1,
        39: 0.1, 41: 0.1, 42: 1,
    };

    let rowCounter = 0;

    function buildTypeOptions(selectedId) {
        let opts = '<option value="">Select Fish Type</option>';
        for (const [id, name] of Object.entries(fishTypes)) {
            const sel = (String(id) === String(selectedId)) ? 'selected' : '';
            opts += `<option value="${id}" ${sel}>${name}</option>`;
        }
        return opts;
    }

    function addRow(existing) {
        const i = rowCounter++;
        const uid = `catch_${i}`;
        const row = document.createElement('tr');
        row.id = `row-${uid}`;

        const catchId = existing ? existing.id : '';
        const typeId = existing ? existing.fish_type_id : '';
        const weight = existing ? existing.weight : 1;
        const count = existing ? existing.fish_count : 0;

        row.innerHTML = `
    <td>
        ${catchId ? `<input type="hidden" name="catches[${i}][id]" value="${catchId}">` : ''}
        <select name="catches[${i}][fish_type_id]"
                class="form-select"
                id="type-${uid}"
                onchange="loadVariants(this.value, '${uid}')">
            ${buildTypeOptions(typeId)}
        </select>
    </td>
    <td>
        <select name="catches[${i}][fish_variant_id]"
                class="form-select"
                id="variant-${uid}"
                onchange="validateRowWeight('${uid}')">
            <option value="">Select Type First</option>
        </select>
    </td>
    <td>
        <input type="number"
               name="catches[${i}][weight]"
               id="weight-${uid}"
               class="form-control"
               step="0.01" min="0" value="${weight}"
               onkeyup="validateRowWeight('${uid}')"
               onchange="validateRowWeight('${uid}')">
        <small id="weight-hint-${uid}" class="text-muted"></small>
    </td>
    <td>
        <input type="number"
               name="catches[${i}][fish_count]"
               id="count-${uid}"
               class="form-control"
               min="0" value="${count}"
               onkeyup="validateRowWeight('${uid}')"
               onchange="validateRowWeight('${uid}')">
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow('${uid}')">
            Remove
        </button>
    </td>`;

        document.getElementById('catch-rows').appendChild(row);
        document.getElementById('no-catch-msg').style.display = 'none';

        if (existing && existing.fish_type_id) {
            loadVariants(existing.fish_type_id, uid, existing.fish_variant_id);
        }
    }

    function removeRow(uid) {
        const row = document.getElementById(`row-${uid}`);
        if (row) row.remove();
        if (document.getElementById('catch-rows').children.length === 0) {
            document.getElementById('no-catch-msg').style.display = 'block';
        }
        updateVariantLocking();
    }

    function loadVariants(typeId, uid, preselectVariantId) {
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
                data.forEach(v => {
                    const sel2 = (preselectVariantId && String(v.id) === String(preselectVariantId)) ? 'selected' : '';
                    opts += `<option value="${v.id}" ${sel2}>${v.name}</option>`;
                });
                sel.innerHTML = opts;
                updateVariantLocking();
                validateRowWeight(uid);
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

    function validateRowWeight(uid) {
        if (gearType === 'ringnet') return;

        const variantSel = document.getElementById(`variant-${uid}`);
        const weightEl = document.getElementById(`weight-${uid}`);
        const countEl = document.getElementById(`count-${uid}`);
        const hintEl = document.getElementById(`weight-hint-${uid}`);

        if (!variantSel || !weightEl || !countEl) return;

        const variantId = variantSel.value;
        const weight = parseFloat(weightEl.value);
        const count = parseInt(countEl.value, 10);

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

        if (minPerFish === undefined || !variantId) {
            weightEl.classList.remove('is-invalid', 'is-valid');
            if (hintEl) hintEl.textContent = '';
            return;
        }

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
            hintEl.textContent = valid
                ? `${perFish.toFixed(2)} kg per fish (min ${minPerFish} kg) ✓`
                : `Each fish would be ${perFish.toFixed(2)} kg — min allowed is ${minPerFish} kg per fish.`;
            hintEl.className = valid ? 'text-muted' : 'text-danger';
        }
    }

    function hasInvalidMinutes(val) {
        if (val === '' || val === null) return false;
        const str = String(val);
        const dotIndex = str.indexOf('.');
        if (dotIndex === -1) return false;
        const decimals = str.substring(dotIndex + 1);
        const minutes = parseInt(decimals.substring(0, 2).padEnd(2, '0'), 10);
        return minutes >= 60;
    }

    const gpsMap = <?= Json::encode([
        'N' => (new \backend\models\ELogSetTemp())::class ? null : null,
    ]) ?>;
</script>

<?php
// GPS map is large — reuse the same static map from create.php via a shared JS file
// if you have one, or paste the same `gpsMap`, `getEListForN`, `updateGpsN`,
// `updateGpsE`, and `validateGpsE` functions from create.php's <script> block here.
$this->registerJs(<<<JS
document.addEventListener('DOMContentLoaded', function () {
    // Pre-fill existing catches
    existingCatches.forEach(c => addRow(c));
    if (existingCatches.length === 0) {
        document.getElementById('no-catch-msg').style.display = 'block';
    }

    const formEl = document.getElementById('edit-set-form');
    if (formEl) {
        formEl.addEventListener('submit', function (e) {
            const allRows = document.querySelectorAll('#catch-rows tr');
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

            if (gearType !== 'ringnet') {
                const invalidWeights = document.querySelectorAll('#catch-rows .is-invalid');
                if (invalidWeights.length > 0) {
                    e.preventDefault();
                    alert('One or more fish entries are below the minimum allowed weight per fish. Please correct the highlighted fields before saving.');
                    invalidWeights[0].focus();
                }
            }
        });
    }
});
JS
);
?>