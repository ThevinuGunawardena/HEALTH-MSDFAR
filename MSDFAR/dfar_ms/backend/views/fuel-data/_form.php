<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/** @var yii\web\View $this */
/** @var backend\models\FuelData $model */
/** @var yii\widgets\ActiveForm $form */

use backend\models\Banks;
use backend\models\BankBranches;

use backend\models\FuelQuotaCategories;



$banks = ArrayHelper::map(
    Banks::find()->asArray()->all(),
    'code',       // ✅ MUST use code
    'bank_name'
);


$categories = FuelQuotaCategories::find()->asArray()->all();

// Dropdown list
$fuelCategories = ArrayHelper::map($categories, 'id', 'category');

// Full data for JS
$fuelCategoryData = [];

foreach ($categories as $cat) {
    $fuelCategoryData[$cat['id']] = [
        'category' => $cat['category'],
        'fuel_quota' => $cat['fuel_quota'],
    ];
}

$fuelCategoryJson = json_encode($fuelCategoryData);
?>

<!-- <div class="fuel-data-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'bank_code')->textInput() ?>

    <?= $form->field($model, 'bank_branch')->textInput() ?>

    <?= $form->field($model, 'account_number')->textInput() ?>

    <?= $form->field($model, 'fuel_quota_cat')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div> -->


<div class="catch-data-form">

<?php $form = ActiveForm::begin(); ?>

<div class="card mb-5 shadow-sm">
<div class="card-body">
<div class="row">

<!-- SELECT -->
<div class="col-md-6">
<?= $form->field($model, 'boat_registration_id')->widget(Select2::classname(), [
    'data' => $boatList,
    'options' => [
        'placeholder' => 'Select boat number...',
        'id' => 'boat-select'
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
</div>

<div class="col-md-6"></div>

<!-- DISPLAY DETAILS -->
<div class="col-md-6">
    <label>Boat Number:</label><br>
    <span style="font-size:18px;font-weight:600;" id="selected-boat-number"></span>
</div>

<div class="col-md-6">
    <label>Owner NIC:</label><br>
    <span style="font-size:18px;font-weight:600;" id="owner-nic"></span>
</div>

<div class="col-md-6">
    <label>Owner Name:</label><br>
    <span style="font-size:18px;font-weight:600;" id="owner-name"></span>
</div>

<div class="col-md-6">
    <label>Address:</label><br>
    <span style="font-size:18px;font-weight:600;" id="owner-address"></span>
</div>

<div class="col-md-6">
    <label>District:</label><br>
    <span style="font-size:18px;font-weight:600;" id="owner-district"></span>
</div>

<div class="col-md-6">
    <label>Division:</label><br>
    <span style="font-size:18px;font-weight:600;" id="owner-division"></span>
</div>

<div class="col-md-6">
    <label>Engine Capacity:</label><br>
    <span style="font-size:18px;font-weight:600;" id="boat-capasity"></span>
</div>

<!-- FORM FIELDS -->
<div class="col-md-6">
</div>

<div class="col-md-6">
<?= $form->field($model, 'bank_code')->widget(Select2::classname(), [
    'data' => $banks,
    'options' => [
        'placeholder' => 'Select bank...', // ✅ FIXED
        'id' => 'bank'
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
</div>

<div class="col-md-6">
   <?= $form->field($model, 'bank_branch')->widget(Select2::classname(), [
    'data' => [], // 🔥 empty initially
    'options' => [
        'placeholder' => 'Select branch...',
        'id' => 'bank-branches'
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
</div>

<div class="col-md-6">
    <?= $form->field($model, 'account_number')->textInput() ?>
</div>

<div class="col-md-6">
    <?= $form->field($model, 'fuel_quota_cat')->dropDownList($fuelCategories, [
    'prompt' => 'Select Fuel Quota Category',
    'id' => 'fuel-category'
]) ?>
</div>



<div class="col-md-6">
    <label>Category Description:</label><br>
    <span id="category-description" style="font-weight:600;"></span>
</div>

<div class="col-md-6">
    <label>Quota:</label><br>
    <span id="category-quota" style="font-weight:600;"></span>
</div>

<div class="col-md-6">
</div>

<div class="col-12">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

</div>
</div>
</div>

<?php ActiveForm::end(); ?>

</div>
<?php
$script = <<< JS

var boatData = $boatJson;

function loadBoatDetails(boatId){
    if (boatId && boatData[boatId]) {
        var data = boatData[boatId];

        $('#selected-boat-number').text(data.boat_number);
        $('#owner-nic').text(data.nic);
        $('#owner-name').text(data.name);
        $('#owner-address').text(data.address);
       $('#owner-district').text(data.district);
        $('#owner-division').text(data.division);
        $('#boat-capasity').text(data.capacity + ' hp');
    } else {
        $('#selected-boat-number').text('');
        $('#owner-nic').text('');
        $('#owner-name').text('');
        $('#owner-address').text('');
        $('#owner-district').text('');
        $('#owner-division').text('');
        $('#boat-capasity').text('');
    }
}

// On change
$('#boat-select').on('change', function () {
    loadBoatDetails($(this).val());
});

// Load default (update page)
$(document).ready(function () {
    loadBoatDetails($('#boat-select').val());
});

JS;

$this->registerJs($script);
?>

<?php
$script = <<< JS

var categoryData = $fuelCategoryJson;

function loadCategoryDetails(catId){
    if (catId && categoryData[catId]) {
        var data = categoryData[catId];

        $('#category-description').text(data.category);
        $('#category-quota').text(data.fuel_quota + ' Liters');
    } else {
        $('#category-description').text('');
        $('#category-quota').text('');
    }
}

// On change
$('#fuel-category').on('change', function () {
    loadCategoryDetails($(this).val());
});

// Load default (update page)
$(document).ready(function () {
    loadCategoryDetails($('#fuel-category').val());
});

JS;

$this->registerJs($script);
?>

<?php
$script = <<< JS

$('#bank').on('change', function () {
    var bankCode = $(this).val(); // ✅ this is now code

    if (bankCode) {
        $.ajax({
            url: 'get-branches',
            type: 'GET',
            data: { bankCode: bankCode },
            success: function (data) {

                var branches = JSON.parse(data);
                var \$branch = $('#bank-branches');

                \$branch.empty();

                $.each(branches, function (id, text) {
                    \$branch.append(new Option(text, id));
                });

                \$branch.trigger('change');
            }
        });
    } else {
        $('#bank-branches').empty();
    }
});

JS;

$this->registerJs($script);
?>
