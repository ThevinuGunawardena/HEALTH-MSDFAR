<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataPurchaseDetails $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="catch-data-purchase-details-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fish_type')->textInput() ?>

    <?= $form->field($model, 'No_of_Fish')->textInput() ?>

    <?= $form->field($model, 'Weight_of_Fish')->textInput() ?>

    <?= $form->field($model, 'exporter_uid')->textInput() ?>

    <?= $form->field($model, 'catch_data_request_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
