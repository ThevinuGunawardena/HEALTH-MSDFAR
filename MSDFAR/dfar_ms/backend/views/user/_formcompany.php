<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\config\Constant;
use yii\helpers\ArrayHelper;
use backend\models\AuthItem;
use backend\models\AuthItemChild;
use backend\models\AuthItemSearch;
/** @var yii\web\View $this */
/** @var backend\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="row">
    <div class="col-lg-6">

        <?php $form = ActiveForm::begin(['id' => 'form-create']); ?>

        <?= $form->field($model, 'nic')->textInput() ?>


        <?= $form->field($model, 'email') ?>

        <?= isset($update) && $update ? "" : $form->field($model, 'password', [
            'template' => "{label}\n<div class=\"input-group\">{input}<span class=\"input-group-text toggle-password\">  <i class=\"fa fa-fw fa-eye\"></i></span></div>\n{error}",
            'inputOptions' => ['class' => 'form-control'],
            'labelOptions' => ['class' => 'form-label'],
        ])->passwordInput() ?>

        <?=  $form->field($model, 'type')->dropDownList(Constant::getUserTypesByCategory('main')) ?>
       <?= $form->field($model, 'secondary')->checkboxList(Constant::getUserTypesByCategory('secondary')) ?>
          <div class="form-group">
        <?= Html::label('Secondary user type (optional)', 'secondary', ['class' => 'control-label']) ?>
         <?= Html::checkboxList('SignupForm[secondary]', null, Constant::getUserTypesByCategory('secondary'), [ 'id' => 'secondary']) ?>
        
        <?=  $form->field($model, 'user_role')->dropDownList(ArrayHelper::map(AuthItem::find()->select("name")->where(['type' => 0])->orWhere(['type' => 1])->asArray()->all(), 'name', "name")) ?>

         <?= $form->field($model, 'user_permission')->dropDownList(Constant::$userPermissions) ?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
