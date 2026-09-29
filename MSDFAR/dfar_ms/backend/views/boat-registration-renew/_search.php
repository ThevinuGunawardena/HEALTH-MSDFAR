<?php

use backend\config\Constant;
use backend\models\ProfileFisherman;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoatSearch $model */
/** @var yii\widgets\ActiveForm $form */
$ownerData = ArrayHelper::map(ProfileFisherman::find()->andWhere(['id' => $model->fisherman_id])->all(), 'id', function ($model) {
    return "ID-" . $model['fisherman_uid'] . ", NIC:" . $model['nic'];
});
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


            <?php // $form->field($model, 'boat_number_id') ?>

            <?php // $form->field($model, 'fisherman_id') ?>

            <div class="row">
                <div class="col-xl-4">
                    <?= $form->field($model, 'boat_number_id') ?>
                </div>
                <div class="col-xl-4">
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
                <div class="col-xl-4">
                    <?= $form->field($model, 'status')->dropDownList(Constant::$licenseStatus, ["prompt" => "-"]) ?>
                </div>
            </div>

            <?php //= $form->field($model, 'insurance_no') ?>

            <?php //= $form->field($model, 'call_sign_no') ?>

            <?php // echo $form->field($model, 'landing_site') ?>

            <?php // echo $form->field($model, 'witness_name') ?>

            <?php // echo $form->field($model, 'witness_address') ?>

            <?php // echo $form->field($model, 'witness_nic') ?>

            <?php // echo $form->field($model, 'witness_singing_date') ?>

            <?php //  echo $form->field($model, 'status')->dropDownList(Constant::$licenseStatus,["prompt"=>"Select"]) ?>

            <?php // echo $form->field($model, 'approval_stage')->dropDownList(Constant::$officersTypes,["prompt"=>"Select"]) ?>
            <div class="row">
                <div class="col-lg-6">
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
