<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use backend\models\ELogTemp;
use backend\models\MHarbours;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\ELogTemp */

$this->title = 'Edit E-Log';
$this->params['breadcrumbs'][] = ['label' => 'E-Log Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'View', 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Edit';

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

<div class="container e-log-temp-update mt-3">
    <div class="text-end mb-3">
        <?= Html::a('Back to View', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary btn-sm']) ?>
    </div>
</div>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'e-log-form'],
]); ?>

<div class="container mt-3 e-log-temp-update">
    <div class="card">

        <div class="card-header">
            <h5 class="mb-0"><i class="glyphicon glyphicon-edit"></i> <?= Html::encode($this->title) ?></h5>
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

                <!-- Phone -->
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
        <?= Html::submitButton('<i class="glyphicon glyphicon-ok"></i> Save Changes', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

</div>

<?php ActiveForm::end(); ?>

<?php
$css = <<<CSS
.e-log-temp-update .card {
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.e-log-temp-update .card-header {
    font-weight: 600;
    letter-spacing: 0.3px;
}
.e-log-temp-update .gear-section {
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