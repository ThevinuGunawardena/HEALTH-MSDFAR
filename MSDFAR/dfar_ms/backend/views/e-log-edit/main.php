<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\select2\Select2;
use backend\models\MHarbours;

$this->title = 'E-Log Entry';
$this->params['breadcrumbs'][] = $this->title;

$form = ActiveForm::begin([
    'action' => ['e-log-edit/main'],
    'method' => 'post',
    'id' => 'elog-main-form',
    'enableClientValidation' => false,
]);
?>

<div class="container mt-3">
    <div class="card">

        <div class="card-header">
            <h5>E-Log Entry</h5>
        </div>

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Log Book Number <span class="text-danger">*</span></label>
                        <input type="text" name="log_sheet_number" id="log_sheet_number" class="form-control"
                            style="text-transform: uppercase;"
                            value="<?= Html::encode(strtoupper(Yii::$app->request->post('log_sheet_number', ''))) ?>">
                        <div class="invalid-feedback">Log Sheet Number is required.</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Log Page Number <span class="text-danger">*</span></label>
                        <input type="text" name="log_page_number" id="log_page_number" class="form-control"
                            style="text-transform: uppercase;"
                            value="<?= Html::encode(strtoupper(Yii::$app->request->post('log_page_number', ''))) ?>">
                        <div class="invalid-feedback">Log Page Number is required.</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Search Vessel <span class="text-danger">*</span></label>
                        <?= Html::hiddenInput('vessel_id', '', ['id' => 'vessel-hidden-id']) ?>
                        <?= Select2::widget([
                            'name' => 'vessel_search',
                            'options' => [
                                'placeholder' => 'Search boat...',
                                'id' => 'vessel-select',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 2,
                                'ajax' => [
                                    'url' => Url::to(['e-log-edit/boat-search']),
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
                                        $('#vessel-hidden-id').val(data.id);
                                        $('#vessel-select').closest('.form-group').find('.select2-selection').removeClass('is-invalid');
                                        $('#vessel-error').hide();
                                    }
                                "),
                                'select2:clear' => new JsExpression("
                                    function(e) {
                                        $('#vessel-hidden-id').val('');
                                    }
                                "),
                            ],
                        ]) ?>
                        <div id="vessel-error" class="text-danger small" style="display:none;">Vessel is required.</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Year <span class="text-danger">*</span></label>
                        <input type="number" name="year" id="year" class="form-control" min="1" placeholder="e.g. 2023"
                            value="<?= Html::encode(Yii::$app->request->post('year', '')) ?>">
                        <div class="invalid-feedback">Year is required.</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Month <span class="text-danger">*</span></label>
                        <select name="month" id="month" class="form-control">
                            <option value="">-- Select Month --</option>
                            <?php
                            $months = [
                                1 => 'January',
                                2 => 'February',
                                3 => 'March',
                                4 => 'April',
                                5 => 'May',
                                6 => 'June',
                                7 => 'July',
                                8 => 'August',
                                9 => 'September',
                                10 => 'October',
                                11 => 'November',
                                12 => 'December'
                            ];
                            $selectedMonth = Yii::$app->request->post('month', '');
                            foreach ($months as $num => $name): ?>
                                <option value="<?= $num ?>" <?= $selectedMonth == $num ? 'selected' : '' ?>>
                                    <?= $name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Month is required.</div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer text-end">
            <?= Html::submitButton('Move to Data Entry', ['class' => 'btn btn-success', 'id' => 'submit-btn']) ?>
        </div>

    </div><!-- end card -->

    <!-- Results Table -->
    <?php if ($duplicateError): ?>
        <div class="alert alert-danger mt-3">
            <?= Html::encode($duplicateError) ?>
        </div>
    <?php elseif (!empty($results)): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5>
                    Results for Boat: <strong><?= Html::encode($boat_no) ?></strong> —
                    <?= $months[$month] ?? '' ?>     <?= Html::encode($year) ?>
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Boat No</th>
                            <th>Boat Name</th>
                            <th>Skipper</th>
                            <th>Harbor</th>
                            <th>Fishing Area</th>
                            <th>Action Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= Html::encode($row->boat_no) ?></td>
                                <td><?= Html::encode($row->boat_name) ?></td>
                                <td><?= Html::encode($row->skipper) ?></td>
                                <td><?= Html::encode($row->harbor) ?></td>
                                <td><?= Html::encode($row->fishing_area) ?></td>
                                <td><?= Html::encode(substr($row->action_date, 0, 10)) ?></td>
                                <td>
                                    <?php if ($row->approve == 'A'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= Html::a('Create E-Log', [
                                        'e-log-edit/index',
                                        'dep_id' => $row->id,
                                        'log_sheet_number' => Yii::$app->request->post('log_sheet_number'),
                                        'log_page_number' => Yii::$app->request->post('log_page_number'),
                                    ], [
                                        'class' => 'btn btn-primary btn-sm'
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif (Yii::$app->request->isPost): ?>
        <div class="alert alert-warning mt-3">
            No records found for the given boat number, year, and month.
        </div>
    <?php endif; ?>

</div>

<?php ActiveForm::end(); ?>

<?php $this->registerJs("

// Restrict input - only allow letters and numbers as user types
$('#log_sheet_number, #log_page_number').on('input', function() {
    var cleaned = $(this).val().replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
    $(this).val(cleaned);
});

$('#submit-btn').on('click', function(e) {
    e.preventDefault();
    var valid = true;
    var alphanumeric = /^[a-zA-Z0-9]+\$/;

    // Check log sheet number
    var logSheet = $('#log_sheet_number').val().trim();
    if (logSheet === '') {
        $('#log_sheet_number').addClass('is-invalid');
        $('#log_sheet_number').next('.invalid-feedback').text('Log Sheet Number is required.');
        valid = false;
    } else if (!alphanumeric.test(logSheet)) {
        $('#log_sheet_number').addClass('is-invalid');
        $('#log_sheet_number').next('.invalid-feedback').text('Only letters and numbers allowed, no spaces or special characters.');
        valid = false;
    } else {
        $('#log_sheet_number').removeClass('is-invalid');
    }

    // Check log page number
    var logPage = $('#log_page_number').val().trim();
    if (logPage === '') {
        $('#log_page_number').addClass('is-invalid');
        $('#log_page_number').next('.invalid-feedback').text('Log Page Number is required.');
        valid = false;
    } else if (!alphanumeric.test(logPage)) {
        $('#log_page_number').addClass('is-invalid');
        $('#log_page_number').next('.invalid-feedback').text('Only letters and numbers allowed, no spaces or special characters.');
        valid = false;
    } else {
        $('#log_page_number').removeClass('is-invalid');
    }

    // Check year
    if ($('#year').val().trim() === '') {
        $('#year').addClass('is-invalid');
        valid = false;
    } else {
        $('#year').removeClass('is-invalid');
    }

    // Check month select
    if ($('#month').val() === '') {
        $('#month').addClass('is-invalid');
        valid = false;
    } else {
        $('#month').removeClass('is-invalid');
    }

    // Check vessel select2
    if ($('#vessel-hidden-id').val() === '') {
        $('#vessel-error').show();
        valid = false;
    } else {
        $('#vessel-error').hide();
    }

    if (valid) {
        $('#elog-main-form').submit();
    }
});
") ?>