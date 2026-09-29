<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\AuthenticatedPasswordChangeForm $model */
/** @var common\models\User $modelData */

$this->title = Yii::t('app', 'Change Password');

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Users'),
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = [
    'label' => $modelData->nic,
    'url' => ['view', 'id' => $modelData->nic],
];

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <h1><?= Html::encode($this->title) ?></h1>

            <?php $form = ActiveForm::begin([
                'id' => 'form-change-password',
            ]); ?>

            <?= $form->field($model, 'currentPassword', [
                'template' =>
                    "{label}\n" .
                    "<div class=\"input-group\">" .
                    "{input}" .
                    "<span class=\"input-group-text toggle-password\">" .
                    "<i class=\"fa fa-fw fa-eye\"></i>" .
                    "</span>" .
                    "</div>\n{error}",
                'inputOptions' => [
                    'class' => 'form-control',
                    'autocomplete' => 'current-password',
                ],
                'labelOptions' => [
                    'class' => 'form-label',
                ],
            ])->passwordInput() ?>

            <?= $form->field($model, 'newPassword', [
                'template' =>
                    "{label}\n" .
                    "<div class=\"input-group\">" .
                    "{input}" .
                    "<span class=\"input-group-text toggle-password\">" .
                    "<i class=\"fa fa-fw fa-eye\"></i>" .
                    "</span>" .
                    "</div>\n{error}",
                'inputOptions' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password',
                ],
                'labelOptions' => [
                    'class' => 'form-label',
                ],
            ])->passwordInput() ?>

            <?= $form->field($model, 'confirmPassword', [
                'template' =>
                    "{label}\n" .
                    "<div class=\"input-group\">" .
                    "{input}" .
                    "<span class=\"input-group-text toggle-password\">" .
                    "<i class=\"fa fa-fw fa-eye\"></i>" .
                    "</span>" .
                    "</div>\n{error}",
                'inputOptions' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password',
                ],
                'labelOptions' => [
                    'class' => 'form-label',
                ],
            ])->passwordInput() ?>

            <div class="form-group">
                <?= Html::submitButton(
                    Yii::t('app', 'Change Password'),
                    [
                        'class' => 'btn btn-primary',
                        'name' => 'change-password-button',
                    ]
                ) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>