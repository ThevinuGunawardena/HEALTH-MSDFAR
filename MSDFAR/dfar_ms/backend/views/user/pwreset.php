<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\User $model */

$this->title = Yii::t('app', 'Update User password: {name}', [
    'name' => $modelData->nic,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $modelData->id, 'url' => ['view', 'id' => $modelData->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <?php $form = ActiveForm::begin(['id' => 'form-create']); ?>

            <?= "Username :" . $modelData->nic ?>

            <?= $form->field($model, 'password', [
                'template' => "{label}\n<div class=\"input-group\">{input}<span class=\"input-group-text toggle-password\">  <i class=\"fa fa-fw fa-eye\"></i></span></div>\n{error}",
                'inputOptions' => ['class' => 'form-control'],
                'labelOptions' => ['class' => 'form-label'],
            ])->passwordInput() ?>


            <div class="form-group">
                <?= Html::submitButton('Create', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
