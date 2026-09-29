<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFishermanRenewSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="profile-fisherman-renew-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fisherman_uid') ?>

    <?= $form->field($model, 'fisherman_id') ?>

    <?= $form->field($model, 'district') ?>

    <?= $form->field($model, 'division') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'approval_stage') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'approved_time') ?>

    <?php // echo $form->field($model, 'expire_date') ?>

    <?php // echo $form->field($model, 'renew') ?>

    <?php // echo $form->field($model, 'printed') ?>

    <?php // echo $form->field($model, 'printed_date') ?>

    <?php // echo $form->field($model, 'privacy_policy') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
