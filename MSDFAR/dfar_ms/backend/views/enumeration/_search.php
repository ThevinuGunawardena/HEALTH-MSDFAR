<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ScientificEnumerationRequestSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="scientific-enumeration-request-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user') ?>

    <?= $form->field($model, 'request_date') ?>

    <?= $form->field($model, 'date') ?>

    <!--    --><?php //= $form->field($model, 'can_continue') ?>

    <?php // echo $form->field($model, 'reson') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php echo $form->field($model, 'district') ?>
    <!---->
    <?php echo $form->field($model, 'division') ?>

    <?php // echo $form->field($model, 'landing_site') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
