<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\MHarbours;
use kartik\select2\Select2;
use yii\web\JsExpression;

$this->title = 'E-Log Entry';
$this->params['breadcrumbs'][] = $this->title;

/* Harbours dropdown */
$harbours = ArrayHelper::map(
    MHarbours::find()
        ->select(['id', 'Name'])
        ->orderBy(['Name' => SORT_ASC])
        ->asArray()
        ->all(),
    'id',
    'Name'
);

$form = ActiveForm::begin();
?>

<div class="container mt-3">
    <div class="card">

        <div class="card-header">
            <h5>E-Log Entry</h5>
        </div>

        <div class="card-body">
            <div class="row">

                <!-- Log Book No -->
                <div class="col-md-6">
                    <?= $form->field($model, 'log_book_no')->textInput(['readonly' => true]) ?>
                </div>

                <!-- Log Sheet Number -->
                <div class="col-md-6">
                    <?= $form->field($model, 'log_sheet_number')->textInput(['readonly' => true]) ?>
                </div>

                <!-- Vessel -->
                <div class="col-md-6">
                    <?= $form->field($model, 'vessel_id')->widget(Select2::class, [
                        'initValueText' => $model->vessel_id,
                        'options' => [
                            'placeholder' => 'Search boat...',
                            'id' => 'vessel-select',
                            'disabled' => !empty($model->vessel_id),
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
                                    $('#elog-phone_number').val(data.contact_no);
                                }
                            "),
                        ],
                    ]) ?>
                </div>

                <!-- Skipper -->
                <div class="col-md-6">
                    <?= $form->field($model, 'skipper_id')->widget(Select2::class, [
                        'initValueText' => $model->skipper_id,
                        'options' => [
                            'placeholder' => 'Search skipper...',
                            'disabled' => !empty($model->skipper_id),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 2,
                            'ajax' => [
                                'url' => Url::to(['e-log-edit/skipper-search']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params){
                                    return {q: params.term};
                                }'),
                                'processResults' => new JsExpression('function(data){
                                    return {results: data.results};
                                }'),
                            ],
                        ],
                    ]) ?>
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                    <?= $form->field($model, 'phone_number')->textInput(['readonly' => true]) ?>
                </div>

                <!-- Departure Date -->
                <div class="col-md-6">
                    <?= $form->field($model, 'departure_date')->input('date', ['readonly' => true]) ?>
                </div>

                <!-- Departure Harbour -->
                <div class="col-md-6">
                    <?= $form->field($model, 'departure_harbour')->dropDownList(
                        $harbours,
                        [
                            'prompt' => 'Select Departure Harbour',
                            'disabled' => !empty($model->departure_harbour),
                            'value' => $model->departure_harbour,  // ← selects the correct option by Id
                        ]
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

                <div class="col-md-6">
    <div class="card h-100">
        <div class="card-header">
            <h6 class="mb-0">Equipment bring for Departure</h6>
        </div>
        <div class="card-body">
            <?php if ($longline || $gillnet || $ringnet): ?>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($longline): ?>
                        <span class="badge  px-3 py-2">Longline</span>
                    <?php endif; ?>

                    <?php if ($ringnet): ?>
                        <span class="badge  px-3 py-2">Ringnet</span>
                    <?php endif; ?>

                    <?php if ($gillnet): ?>
                        <span class="badge px-3 py-2">Gillnet</span>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-muted small">No gear equipment recorded for this departure.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

            </div>
        </div>

        <div class="card-footer text-end">
            <?= Html::submitButton('Move to Data Entry', ['class' => 'btn btn-success']) ?>
        </div>

    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->registerJs("
$('#elog-arrival_date').on('change', function() {
    var departureDate = $('#elog-departure_date').val();
    var arrivalDate = $(this).val();

    if (departureDate && arrivalDate && arrivalDate <= departureDate) {
        $(this).addClass('is-invalid');
        if (!$(this).next('.invalid-feedback').length) {
            $(this).after('<div class=\"invalid-feedback\">Arrival Date must be later than Departure Date.</div>');
        } else {
            $(this).next('.invalid-feedback').text('Arrival Date must be later than Departure Date.');
        }
    } else {
        $(this).removeClass('is-invalid');
    }
});
") ?>