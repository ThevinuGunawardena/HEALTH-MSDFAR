<?php

use backend\services\CommonService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ScientificDataSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="scientific-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <!--    --><?php //= $form->field($model, 'id') ?>
    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'district')->dropDownList(CommonService::getFIDistrictArray(), ["prompt" => "-"]) ?>

        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'landing_site')->dropDownList(CommonService::getLandingSitesArray(), ["prompt" => "-"]) ?>

        </div>
        <div class="col-lg-6"></div>
    </div>

    <!--    --><?php //= $form->field($model, 'division') ?>
    <!---->

    <!--    --><?php //= $form->field($model, 'start_time') ?>

    <?php // echo $form->field($model, 'end_time') ?>

    <!--    --><?php // echo $form->field($model, 'added_by') ->dropDownList(U, ["prompt" => "-"]) ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
