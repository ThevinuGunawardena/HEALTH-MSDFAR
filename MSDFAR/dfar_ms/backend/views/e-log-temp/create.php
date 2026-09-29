<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use backend\models\ELogTemp;
use backend\models\MHarbours;
use backend\models\ELogCatchTemp;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\ELogTemp */
/* @var $setModel backend\models\ELogSetTemp|null */
/* @var $sets backend\models\ELogSetTemp[] */
/* @var $fishTypes array */

$this->title = 'Create E-Log';
$this->params['breadcrumbs'][] = ['label' => 'E-Log Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/* Harbours dropdown data */
$harbours = ArrayHelper::map(
    MHarbours::find()
        ->select(['id', 'Name'])
        ->orderBy(['Name' => SORT_ASC])
        ->asArray()
        ->all(),
    'id',
    'Name'
);
?>

<div class="container e-log-temp-create mt-3">
    <div class="text-end mb-3">
        <?= Html::a(
            '<i class="glyphicon glyphicon-list"></i> Click to see Ringnet E-Log List Add by Here',
            ['index'],
            ['class' => 'btn btn-secondary btn-sm']
        ) ?>
    </div>
</div>

<?php if (!$model->isNewRecord && isset($setModel)): ?>
    <?php
    $typeColors = [
        'longline' => 'primary',
        'gillnet' => 'success',
        'ringnet' => 'warning',
    ];
    $color = $typeColors[$model->gear_type] ?? 'secondary';
    $gearTypeJs = Json::encode($model->gear_type);
    $fishTypesJson = Json::encode($fishTypes);
    $variantUrl = Url::to(['e-log-temp/fish-variants']);
    ?>

    <div class="container e-log-temp-create">

        <!-- EXISTING SETS -->
        <div class="card mb-4 mt-4">
            <div class="card-header bg-<?= $color ?> text-white">
                <h5><?= strtoupper($model->gear_type) ?> Gear Existing Sets</h5>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sets)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No sets yet.</td>
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
                                    <td>
                                        <?php $catches = ELogCatchTemp::find()->where(['e_log_set_temp_id' => $set->id])->all(); ?>
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
                                    <td>
                                        <?= Html::a(
                                            'Delete',
                                            ['e-log-temp/delete-set', 'id' => $set->id, 'elogId' => $model->id],
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
                <h5>Add Set #<?= $setModel->set_number ?></h5>
            </div>

            <?php $setForm = ActiveForm::begin([
                'id' => 'set-form',
                'action' => Url::to(['e-log-temp/add-set', 'id' => $model->id]),
            ]); ?>

            <?= Html::hiddenInput('ELogSetTemp[elog_id]', $model->id) ?>
            <?= Html::hiddenInput('ELogSetTemp[gear_type]', $model->gear_type) ?>
            <?= Html::hiddenInput('ELogSetTemp[set_number]', $setModel->set_number) ?>

            <div class="card-body">
                <div class="row">

                    <!-- ===== START ===== -->
                    <div class="col-12 mb-2">
                        <h6 class="text-muted fw-bold">▶ Start</h6>
                    </div>

                    <div class="col-md-3">
                        <?= $setForm->field($setModel, 'start_datetime')->input('datetime-local') ?>
                    </div>

                    <div class="col-md-3">
                        <?= $setForm->field($setModel, 'start_gps_direction')
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
                        <?= $setForm->field($setModel, 'start_gps_n')
                            ->textInput([
                                'id' => 'start_gps_n',
                                'type' => 'number',
                                'step' => '0.01',
                                'class' => 'form-control',
                                'placeholder' => 'e.g. 12.30',
                                'onkeyup' => 'updateGpsE("start")',
                                'onchange' => 'updateGpsE("start")',
                            ]) ?>
                        <small id="start_gps_n_hint" class="text-muted"></small>
                    </div>

                    <div class="col-md-3">
                        <?= $setForm->field($setModel, 'start_gps_e')
                            ->textInput([
                                'id' => 'start_gps_e',
                                'type' => 'number',
                                'step' => '0.01',
                                'class' => 'form-control',
                                'placeholder' => '— Select N First —',
                                'disabled' => true,
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
                        <?= $setForm->field($setModel, 'end_datetime')->input('datetime-local') ?>
                    </div>

                    <div class="col-md-3">
                        <?= $setForm->field($setModel, 'end_gps_direction')
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
                        <?= $setForm->field($setModel, 'end_gps_n')
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
                        <?= $setForm->field($setModel, 'end_gps_e')
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

                    <!-- ===== RETAINED FISH (only) ===== -->
                    <div class="col-12 mt-4">
                        <div class="card border-secondary fish-add-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">Retained Fish Data</h6>
                                <button type="button" class="btn btn-success btn-sm"
                                    onclick="addRow('catches', 'catch-rows', 'no-catch-msg')">
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

                </div><!-- /.row -->
            </div><!-- /.card-body -->

            <div class="card-footer text-end">
                <?= Html::submitButton('Save Set', ['class' => 'btn btn-' . $color]) ?>
                <?= Html::a('Finish Data Entry', ['create'], ['class' => 'btn btn-' . $color]) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div><!-- /.card -->

    </div>

    <style>
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

    <script>
        const gearType = <?= $gearTypeJs ?>;
        const fishTypes = <?= $fishTypesJson ?>;
        const variantUrl = '<?= $variantUrl ?>';

        const variantMinWeight = {
            1: 5, 2: 5, 4: 1, 5: 5, 6: 0.05, 8: 1, 9: 0.05, 10: 0.05, 11: 0.05,
            12: 0.1, 14: 0.1, 16: 0.1, 17: 5, 18: 5, 20: 5, 21: 2, 22: 3, 23: 3,
            24: 3, 26: 5, 27: 3, 28: 5, 33: 3, 35: 0.3, 36: 0.3, 37: 0.5, 38: 1,
            39: 0.1, 41: 0.1, 42: 1,
        };

        const counters = { catches: 0 };

        function buildTypeOptions() {
            let opts = '<option value="">Select Fish Type</option>';
            for (const [id, name] of Object.entries(fishTypes)) {
                opts += `<option value="${id}">${name}</option>`;
            }
            return opts;
        }

        function addRow(namespace, tbodyId, noMsgId) {
            const i = counters[namespace]++;
            const uid = `${namespace}_${i}`;
            const row = document.createElement('tr');
            row.id = `row-${uid}`;

            const typeOptions = buildTypeOptions();

            row.innerHTML = `
    <td>
        <select name="${namespace}[${i}][fish_type_id]"
                class="form-select"
                id="type-${uid}"
                onchange="loadVariants(this.value, '${uid}', '${namespace}')">
            ${typeOptions}
        </select>
    </td>
    <td>
        <select name="${namespace}[${i}][fish_variant_id]"
                class="form-select"
                id="variant-${uid}"
                onchange="validateRowWeight('${uid}', '${namespace}')">
            <option value="">Select Type First</option>
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
                "0": [46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
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

            if (hasInvalidMinutes(nRaw)) {
                nInput.classList.add('is-invalid');
                nInput.classList.remove('is-valid');
                if (nHint) {
                    nHint.textContent = 'Decimal must be .00–.59';
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
                    eHint.textContent = 'Decimal must be .00–.59';
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

        document.addEventListener('DOMContentLoaded', function () {
            updateGpsN('start');
            updateGpsN('end');

            const formEl = document.getElementById('set-form');
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

                    const startE = document.getElementById('start_gps_e');
                    if (!startE.value.trim()) {
                        e.preventDefault();
                        alert('Start GPS E is empty. Please enter a value before submitting.');
                        startE.focus();
                        return;
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
    </script>

<?php endif; ?>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'e-log-form'],
]); ?>

<div class="container mt-3 e-log-temp-create">
    <div class="card">

        <div class="card-header">
            <h5 class="mb-0"><i class="glyphicon glyphicon-plus-sign"></i> <?= Html::encode($this->title) ?></h5>
        </div>

        <div class="card-body">
            <div class="row">

                <!-- Vessel -->
                <div class="col-md-6">
                    <?= $form->field($model, 'vessel_id')->widget(Select2::class, [
                        'initValueText' => $model->vessel_id,
                        'options' => [
                            'placeholder' => 'Search boat...',
                            'id' => 'vessel-select',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 2,
                            'ajax' => [
                                'url' => Url::to(['e-log-temp/boat-search']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params){
                                    return {q: params.term};
                                }'),
                                'processResults' => new JsExpression('function(data){
                                    return {results: data.results};
                                }'),
                            ],
                        ],
                        'pluginEvents' => [
                            'select2:select' => new JsExpression("
                                function(e) {
                                    var data = e.params.data;
                                    $('#elogtemp-phone_number').val(data.contact_no);
                                }
                            "),
                        ],
                    ]) ?>
                </div>

                <!-- Phone (auto-filled from vessel owner) -->
                <div class="col-md-6">
                    <?= $form->field($model, 'phone_number')->textInput(['readonly' => true]) ?>
                </div>

                <!-- Gear Type -->
                <div class="col-md-6">
                    <?= $form->field($model, 'gear_type')->dropDownList(
                        ELogTemp::gearTypeList(),
                        ['id' => 'gear-type-select', 'prompt' => 'Select Gear Type']
                    ) ?>
                </div>

                <!-- Departure Date -->
                <div class="col-md-6">
                    <?= $form->field($model, 'departure_date')->input('date') ?>
                </div>

                <!-- Departure Harbour -->
                <div class="col-md-6">
                    <?= $form->field($model, 'departure_harbour')->dropDownList(
                        $harbours,
                        ['prompt' => 'Select Departure Harbour']
                    ) ?>
                </div>

                <!-- Arrival Date -->
                <div class="col-md-6">
                    <?= $form->field($model, 'arrival_date')->input('date') ?>
                </div>

                <!-- Arrival Harbour -->
                <div class="col-md-6">
                    <?= $form->field($model, 'arrival_harbour')->dropDownList(
                        $harbours,
                        ['prompt' => 'Select Arrival Harbour']
                    ) ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Ring Net Section -->
    <div id="ringnet-fields" class="card gear-section mt-3" style="display:none;">
        <div class="card-header">
            <i class="glyphicon glyphicon-record"></i> Ring Net Details
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'net_length') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'net_height') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'fad') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Longline Section -->
    <div id="longline-fields" class="card gear-section mt-3" style="display:none;">
        <div class="card-header">
            <i class="glyphicon glyphicon-minus"></i> Longline Details
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'mainline') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'branchline') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'no_of_hooks') ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'hook_type')->dropDownList(ELogTemp::hookTypeList(), ['prompt' => 'Select Hook Type']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'depth') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'bait')->dropDownList(ELogTemp::baitList(), ['prompt' => 'Select Bait Type']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'no_hook_bet') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Gillnet Section -->
    <div id="gillnet-fields" class="card gear-section mt-3" style="display:none;">
        <div class="card-header">
            <i class="glyphicon glyphicon-th"></i> Gillnet Details
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'net_material')->dropDownList(ELogTemp::materialList(), ['prompt' => 'Select Net Material']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'mesh_size') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'ply') ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'set_depth') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'length') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'net_pieces') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group text-end e-log-actions mt-3">
        <?= Html::submitButton('<i class="glyphicon glyphicon-ok"></i> Save E-Log', ['class' => 'btn btn-success']) ?>
    </div>

</div>

<?php ActiveForm::end(); ?>

<?php
$css = <<<CSS
.e-log-temp-create .card {
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.e-log-temp-create .card-header {
    font-weight: 600;
    letter-spacing: 0.3px;
}
.e-log-temp-create .gear-section {
    animation: fadeIn 0.25s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}
CSS;
$this->registerCss($css);

$js = <<<JS
function toggleGearFields() {
    $('.gear-section').hide();
    var val = $('#gear-type-select').val();
    if (val === 'ringnet') $('#ringnet-fields').show();
    if (val === 'longline') $('#longline-fields').show();
    if (val === 'gillnet') $('#gillnet-fields').show();
}
$('#gear-type-select').on('change', toggleGearFields);
toggleGearFields();

$('#elogtemp-arrival_date').on('change', function() {
    var departureDate = $('#elogtemp-departure_date').val();
    var arrivalDate = $(this).val();

    if (departureDate && arrivalDate && arrivalDate <= departureDate) {
        $(this).addClass('is-invalid');
        if (!$(this).next('.invalid-feedback').length) {
            $(this).after('<div class="invalid-feedback">Arrival Date must be later than Departure Date.</div>');
        } else {
            $(this).next('.invalid-feedback').text('Arrival Date must be later than Departure Date.');
        }
    } else {
        $(this).removeClass('is-invalid');
    }
});
JS;
$this->registerJs($js);
?>