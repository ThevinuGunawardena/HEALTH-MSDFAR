<?php

use backend\config\Constant;
use backend\services\CommonService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\DistrictGearTypes $model */
/** @var yii\widgets\ActiveForm $form */

$script = <<< JS
    $("#districtgeartypes-gear_type").change()
JS;
$this->registerJs($script);
?>

<div class="district-gear-types-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'userform'
    ]]); ?>

    <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'gear_type')->dropDownList(CommonService::getMainGearTypesArray(), ["prompt" => "Select", "onchange" => 'loadSubGearTypesAjaxDivisionGearType($(this).val(),' . $model->sub_gear . ')']) ?>
    <?= $form->field($model, 'sub_gear')->dropDownList([], ["prompt" => "Select", "onchange" => 'loadExtraGearDataAjax($(this).val())']) ?>

    <div class="row extra-data">

    </div>

    <?= $form->field($model, 'fishing_time_periods', ['template' => "<div class='row fish-checkbox'><div class='col-lg-12'>{label}</div><div class='chboxlist-fish' class='col-lg-12'>\n{input}</div> </div>\n{hint}\n{error}"])->checkboxList(Constant::$FishingTimes) ?>

    <?= $form->field($model,"fishing_time_durations",['template' => "<div class='row fish-checkbox'><div class='col-lg-12'>{label}</div><div class='chboxlist-fish' class='col-lg-12'>\n{input}</div> </div>\n{hint}\n{error}"] )->checkboxList(Constant::$FishingDuration) ?>

    <?= $form->field($model, 'fish_species', ['template' => "<div class='row fish-checkbox'><div class='col-lg-12'>{label}</div><div class='chboxlist-fish' class='col-lg-12'>\n{input}</div> </div>\n{hint}\n{error}"])->checkboxList(CommonService::getFishTypesArray()) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
