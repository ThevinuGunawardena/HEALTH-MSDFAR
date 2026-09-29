<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ProfileFisherman;
use backend\services\CommonService;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoat $model */
/** @var yii\widgets\ActiveForm $form */
$script = <<< JS
    $("#fishermanregisterdboatlicense-district").change()
JS;
$this->registerJs($script);

$ownerData = ArrayHelper::map(ProfileFisherman::find()->andWhere(['id' => $model->fisherman_id])->all(), 'id', function ($model) {
    return "ID-" . $model['fisherman_uid'] . ", NIC:" . $model['nic'];
});

//if ($error != "")
    print_r("<div style='color:red;'>" . $error . "</div><br>");
?>

<div class="fisherman-registerd-boat-form">
    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'userform'
    ]]); ?>
    <?php if (!UserTypeUtil::hasType(Constant::FISHERMAN)) { ?>
    <!--    --><?php //= $form->field($model, 'boat_number_id')->textInput() ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'fisherman_id')->widget(Select2::classname(), [
                'data' => $ownerData,

                'options' => ['placeholder' => 'Search for a fisherman ...'],
                'pluginOptions' => [
                    'allowClear' => false,

                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => "../fisherman/search-global-all",
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(boat_number) { return boat_number.text; }'),
                    'templateSelection' => new JsExpression('function (boat_number) { return boat_number.text; }'),
                ],
            ]); ?>
        </div>
        <div class="col-lg-12">
            <?= $form->field($model, 'district')->dropDownList(CommonService::getFIDistrictArray(), ['prompt' => 'Select...', "onchange" => 'loadDivisionsAjaxBatRegSpecial($(this).val(),' . $model->division . ')']) ?>

        </div>
        <div class="col-lg-12">
            <?= $form->field($model, 'division')->dropDownList([], ['prompt' => 'Select...', "onchange" => 'loadLandingSiteAjaxBatRegSpecial($(this).val(),' . $model->landing_site . ')']) ?>

        </div>
        <div class="col-lg-12">
            <?= $form->field($model, 'landing_site')->dropDownList(["prompt" => "Select"]) ?>
        </div>
        <div class="col-lg-12">
            <p>Upload a copy of the boat registration</p>
            <?= $form->field($specialAlter, 'file')->fileInput() ?>
        </div>


    </div>
</div>

<?php } ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

</div>
