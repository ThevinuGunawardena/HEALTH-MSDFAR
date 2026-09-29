<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Files $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="files-form">

 <?php

$formAction = [
    '/file/upload',
    'process' => $process,
];

if (
    $isTokenizedProcess
    && $token !== ''
) {
    $formAction['token'] = $token;
} else {
    $formAction['id'] = $recordId;
}

$form = ActiveForm::begin([
    'action' => $formAction,
    'method' => 'post',
    'options' => [
        'class' => 'userform',
        'enctype' =>
            'multipart/form-data',
    ],
]);

?>

    <!--    --><?php //= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'file_type')->dropDownList($documentArray) ?>

    <?= $form->field($model, 'file_name')->fileInput(['maxlength' => true]) ?>

    <!--    --><?php //= $form->field($model, 'process_id')->textInput() ?>

    <!--    --><?php //= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
