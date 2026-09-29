<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Inquiry $model */
/** @var ActiveForm $form */
?>
<div class="inquiry">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'id') ?>
        <?= $form->field($model, 'Email') ?>
        <?= $form->field($model, 'Description') ?>
        <?= $form->field($model, 'Response') ?>
        <?= $form->field($model, 'Submission_Date') ?>
        <?= $form->field($model, 'Response_Date') ?>
        <?= $form->field($model, 'Name') ?>
        <?= $form->field($model, 'Status') ?>
        <?= $form->field($model, 'phone_number') ?>
        <?= $form->field($model, 'Subject') ?>
        <?= $form->field($model, 'Inquiry_Type') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- inquiry -->
