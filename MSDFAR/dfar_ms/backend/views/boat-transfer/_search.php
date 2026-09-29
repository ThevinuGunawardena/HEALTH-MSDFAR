<?php

use backend\config\Constant;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberTransferRequestFromSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="boat-number-transfer-request-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?php // $form->field($model, 'id') ?>

    <?php // $form->field($model, 'current_owner') ?>

    <?php // $form->field($model, 'new owner') ?>

    <?php // $form->field($model, 'new_landing_district') ?>

    <?php // $form->field($model, 'new_landing_site') ?>
    <div class="row">

        <div class="col-lg-4">
            <?= $form->field($model, 'status')->dropDownList(Constant::$licenseStatus, ["prompt" => "-"]) ?>
        </div>
    </div>
    <?php // echo $form->field($model, 'witness_name') ?>

    <?php // echo $form->field($model, 'witness_address') ?>

    <?php // echo $form->field($model, 'witness_nic') ?>

    <?php // echo $form->field($model, 'witness_sign_date') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'approval_stage') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'approved_time') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
