<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\assets\AppAsset;

/** @var yii\web\View $this */
/** @var backend\models\InquirySearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="inquiry-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Inquiry_ID') ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'Name') ?>

    <?= $form->field($model, 'phone_number') ?>

    <?= $form->field($model, 'Email') ?>

    <?= $form->field($model, 'District') ?>

    <?= $form->field($model, 'Office') ?>

    <?php // echo $form->field($model, 'Subject') ?>

    <?php // echo $form->field($model, 'Description') ?>

    <?php echo $form->field($model, 'Submission_Date') ?>

    <?php // echo $form->field($model, 'Inquiry_Status') ?>

    <?php // echo $form->field($model, 'Response') ?>

    <?php // echo $form->field($model, 'Response_Date') ?>
    <?php // echo $form->field($model, 'status') ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>