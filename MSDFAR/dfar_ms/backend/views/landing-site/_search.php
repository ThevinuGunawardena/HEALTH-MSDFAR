<?php

use backend\config\Constant;
use backend\models\MDivision;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\MLandingSiteSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="mlanding-site-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>


    <?= $form->field($model, 'division_id')->dropDownList(ArrayHelper::map(MDivision::find()->where(["status" => 1])->asArray
    ()->all(), 'id', "name"), ['prompt' => "select..."]) ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'status')->dropDownList(Constant::$actDeactInt) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
