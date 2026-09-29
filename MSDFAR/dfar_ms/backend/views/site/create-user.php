<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */

/** @var SignupForm $model */

use backend\config\Constant;
use frontend\models\SignupForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Register new system user';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <p>Please fill out the following fields to signup:</p>

            <div class="row">
                <div class="col-lg-5">
                    <?php $form = ActiveForm::begin(['id' => 'form-create']); ?>

                    <?= $form->field($model, 'nic')->textInput() ?>


                    <?= $form->field($model, 'email') ?>

                    <?= $form->field($model, 'password', [
                        'template' => "{label}\n<div class=\"input-group\">{input}<span class=\"input-group-text toggle-password\">  <i class=\"fa fa-fw fa-eye\"></i></span></div>\n{error}",
                        'inputOptions' => ['class' => 'form-control'],
                        'labelOptions' => ['class' => 'form-label'],
                    ])->passwordInput() ?>
                    <?= $form->field($model, 'type')->dropDownList(Constant::getUserTypesByCategory('main')) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Create', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
