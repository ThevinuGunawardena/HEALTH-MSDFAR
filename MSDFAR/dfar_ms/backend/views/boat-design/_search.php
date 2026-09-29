<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\BoatDesignSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="boat-design-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?php //$form->field($model, 'id') ?>

    <?php // $form->field($model, 'yard') ?>

    <?php // $form->field($model, 'boat_category') ?>

    <?php // $form->field($model, 'hull_material') ?>

    <?php // $form->field($model, 'engin_type') ?>


    <div class="row">
        <div class="col-xl-6">
            <?= $form->field($model, 'yard') ?>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'boat_type') ?>
        </div>
    </div>
    <div class="row">

        <div class="col-xl-6">
            <?= $form->field($model, 'hull_material') ?>
        </div>
        <div class="col-xl-6">
            <?php echo $form->field($model, 'engin_type') ?>
        </div>
    </div>
    <?php // echo $form->field($model, 'fi_district') ?>

    <?php // echo $form->field($model, 'design_notation') ?>

    <?php // echo $form->field($model, 'length') ?>

    <?php // echo $form->field($model, 'width') ?>

    <?php // echo $form->field($model, 'height') ?>

    <?php // echo $form->field($model, 'draft') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
