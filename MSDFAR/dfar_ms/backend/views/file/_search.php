<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\FilesSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="files-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?php // $form->field($model, 'id') ?>

    <?php // $form->field($model, 'type') ?>

    <?php // $form->field($model, 'file_type') ?>

    <?php // $form->field($model, 'file_name') ?>

    <?php // $form->field($model, 'process_id') ?>
    
    <div class="row">

        <div class="col-xl-6">
            <?= $form->field($model, 'id') ?>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'process_id') ?>
        </div>
    </div>
    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
