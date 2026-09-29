<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\ApiKeys;
use backend\models\ApiEndpoints;

/** @var yii\web\View $this */
/** @var backend\models\ApiKeyPermissions $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="api-key-permissions-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'api_key_id')->dropDownList(
        ArrayHelper::map(
            ApiKeys::find()
                ->where(['status' => 1])
                ->orderBy(['id' => SORT_ASC])
                ->all(),
            'id',
            function ($model) {
                return 'Key ID: ' . $model->id . ' - ' . $model->key_name;
            }
        ),
        ['prompt' => 'Select API Key']
    ) ?>

   <?= $form->field($model, 'api_endpoint_ids')->checkboxList(
    ArrayHelper::map(
        ApiEndpoints::find()
            ->where(['status' => 1])
            ->orderBy(['api_name' => SORT_ASC])
            ->all(),
        'id',
        function ($model) {
            return $model->api_code . ' - ' . $model->api_name . ' (' . $model->route . ')';
        }
    ),
    [
        'separator' => '<br>',
    ]
)->label('Allowed APIs') ?>

    <?= $form->field($model, 'status')->dropDownList([
        1 => 'Allowed',
        0 => 'Blocked',
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>