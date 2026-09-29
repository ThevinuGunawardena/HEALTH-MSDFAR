<?php

use backend\config\Constant;
use backend\models\MDivision;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\MLandingSite $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="mlanding-site-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'userform'
    ]]); ?>

    <?= $form->field($model, 'division_id')->dropDownList(ArrayHelper::map(MDivision::find()->where(["status" => 1])->asArray
    ()->all(), 'id', "name"), ['prompt' => "select..."]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(Constant::$actDeactInt) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
