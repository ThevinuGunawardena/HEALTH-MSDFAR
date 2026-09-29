<?php

use backend\config\Constant;
use backend\models\ProfileFisherman;
use backend\services\Util;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFishermanSearch $model */
/** @var yii\widgets\ActiveForm $form */
$ownerData = ArrayHelper::map(ProfileFisherman::find()->andWhere(['id' => $model->id])->all(), 'id', function ($model) {
    return "ID-" . $model['fisherman_uid'] . ", NIC:" . $model['nic'];
});
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'action' => ['data-view'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <?php // $form->field($model, 'id') ?>

            <?php // $form->field($model, 'fisherman_uid') ?>

            <?php // $form->field($model, 'first_name') ?>

            <?php // $form->field($model, 'last_name') ?>

            <?php // $form->field($model, 'preferred_name_for_id') ?>

            <?php // echo $form->field($model, 'nic') ?>

            <?php // echo $form->field($model, 'passport') ?>

            <?php // echo $form->field($model, 'dob') ?>

            <?php // echo $form->field($model, 'gender') ?>

            <?php // echo $form->field($model, 'permanent_address') ?>

            <?php // echo $form->field($model, 'current_address') ?>

            <?php // echo $form->field($model, 'blood_group') ?>

            <?php // echo $form->field($model, 'mobile') ?>

            <?php // echo $form->field($model, 'fixed_line') ?>

            <?php // echo $form->field($model, 'email') ?>

            <?php // echo $form->field($model, 'district') ?>

            <?php // echo $form->field($model, 'division') ?>

            <?php // echo $form->field($model, 'landing_site') ?>

            <?php // echo $form->field($model, 'year_recruitment') ?>

            <?php // echo $form->field($model, 'life_isurance_no') ?>

            <?php // echo $form->field($model, 'member_fisheries_society') ?>

            <?php // echo $form->field($model, 'civil') ?>

            <?php // echo $form->field($model, 'category') ?>

            <?php // echo $form->field($model, 'management_area') ?>

            <?php // echo $form->field($model, 'approval_stage') ?>

            <?php // echo $form->field($model, 'created') ?>

            <?php // echo $form->field($model, 'approved_time') ?>
            <div class="row">
                <div class="col-lg-6">

                </div>
               
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <!--                    --><?php //= $form->field($model, 'nic') ?>
                    <?= $form->field($model, 'id')->widget(Select2::classname(), [
                        'data' => $ownerData,

                        'options' => ['placeholder' => 'Search for a boat owner ...'],
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

                <div class="col-lg-6">
                    <!-- <?= $form->field($model, 'status')->dropDownList(Constant::$licenseStatus, ["prompt" => "-"]) ?> -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Reset', ['data-view'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>

            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
