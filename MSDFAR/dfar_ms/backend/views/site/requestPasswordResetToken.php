<?php

/** @var yii\web\View $this */
/** @var frontend\models\PasswordResetRequestForm $model */

use frontend\models\PasswordResetRequestForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Request Password Reset';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-request-password-reset">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please enter your email address to reset your password.</p>

    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin([
                'id' => 'request-password-reset-form',
            ]); ?>

            <?= $form->field($model, 'email')->textInput([
                'autofocus' => true,
                'type' => 'email',
                'placeholder' => 'Enter your email address',
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('Send', [
                    'class' => 'btn btn-primary',
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>