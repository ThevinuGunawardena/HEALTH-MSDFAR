<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\MTraningInstituteProgramsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="mtraning-institute-programs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

     <?php // $form->field($model, 'id') ?>

    <?php //$form->field($model, 'institute_id') ?>

    <?php // $form->field($model, 'name') ?>

    <?php // $form->field($model, 'status') ?> 

    <div class="row">
        <div class="col-xl-6">
            <?= $form->field($model, 'institute_id') ?>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'name')?>
        </div>
    </div>
    <div class="row">

        <div class="col-xl-6">
            <?= $form->field($model, 'status') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
