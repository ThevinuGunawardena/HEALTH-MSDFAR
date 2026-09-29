<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataPurchaseDetailsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="catch-data-purchase-details-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fish_type') ?>

    <?= $form->field($model, 'No_of_Fish') ?>

    <?= $form->field($model, 'Weight_of_Fish') ?>

    <?= $form->field($model, 'exporter_uid') ?>

    <?php // echo $form->field($model, 'catch_data_request_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
