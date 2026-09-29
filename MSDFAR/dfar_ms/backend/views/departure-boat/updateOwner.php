<?php

/** @var yii\web\View $this */

/** @var backend\models\ProfileFisherman $model */
/** @var string $token */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Update Owner: {name}', [
    'name' => $model->preferred_name_for_id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Departure Boats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Boat Details'),
    'url' => ['view', 'token' => $token],
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">


            <?php $form = ActiveForm::begin([
                'action' => [
                    '/departure-boat/update-owner',
                    'token' => $token,
                ],
            ]); ?>


            <?= $form->field($model, 'email')->textInput() ?>

            <!--    --><?php //= $form->field($model, 'dep_cancelled_by')->textInput(['type' => "date"]) ?>
            <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>


            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>