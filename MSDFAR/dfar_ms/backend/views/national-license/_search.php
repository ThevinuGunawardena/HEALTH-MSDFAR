<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\FishermanRegisterdBoat;
use backend\models\ProfileFisherman;
use backend\services\CommonService;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\NationalLicenseSearch $model */
/** @var yii\widgets\ActiveForm $form */
$ownerData = ArrayHelper::map(ProfileFisherman::find()->andWhere(['id' => $model->fisherman_id])->all(), 'id', function ($model) {
    return "ID-" . $model['fisherman_uid'] . ", NIC:" . $model['nic'];
});
$boatDat = FishermanRegisterdBoat::find()->andWhere(['id' => $model->boat_registration_id])->one();
if (isset($boatDat)) {
    $boatDataS[$boatDat->id] = $boatDat->boatNumber->boat_number;

} else {
    $boatDataS = [];
}
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <?php // $form->field($model, 'id') ?>

            <?php // $form->field($model, 'fisherman_id') ?>

            <?php // $form->field($model, 'boat_registration_id') ?>

            <?php // $form->field($model, 'fishing_area') ?>

            <?php // $form->field($model, 'fisheries_district') ?>

            <?php // echo $form->field($model, 'name_of_coastal') ?>

            <?php // echo $form->field($model, 'main_gear_type') ?>

            <?php // echo $form->field($model, 'landing_site') ?>

            <?php // echo $form->field($model, 'status') ?>

            <?php // echo $form->field($model, 'approval_stage') ?>
            <div class="row">
                <div class="col-xl-3">
                    <!--                    --><?php //= $form->field($model, 'fisherman_id') ?>
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
                                'url' => "../fisherman/search-global",
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(boat_number) { return boat_number.text; }'),
                            'templateSelection' => new JsExpression('function (boat_number) { return boat_number.text; }'),
                        ],
                    ]); ?>
                </div>
                <div class="col-xl-3">
                    <!--                    --><?php //= $form->field($model, 'boat_registration_id') ?>
                    <?= $form->field($model, 'boat_registration_id')->widget(Select2::classname(), [
                        'data' => $boatDataS,

                        'options' => ['placeholder' => 'Search ...'],
                        'pluginOptions' => [
                            'allowClear' => false,

                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                            ],
                            'ajax' => [
                                'url' => "../boat-registration/search-global",
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(boat_number) { return boat_number.text; }'),
                            'templateSelection' => new JsExpression('function (boat_number) { return boat_number.text; }'),
                        ],
                    ]); ?>
                </div>


                <div class="col-xl-2">
                    <?php echo $form->field($model, 'license_number') ?>
                </div>
                <div class="col-xl-2">
                    <?= $form->field($model, 'fisheries_district')->dropDownList(CommonService::getFIDistrictArray(), ["prompt" => "-"]) ?>
                </div>

                <div class="col-lg-6">
                    <?= (UserTypeUtil::hasType(Constant::DG)) ? $form->field($model, 'approval_stage')->dropDownList
                    (Constant::$userTypes, ["prompt" => "-"]) : "" ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'status')->dropDownList(Constant::$licenseStatus, ["prompt" => "-"]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>

            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
