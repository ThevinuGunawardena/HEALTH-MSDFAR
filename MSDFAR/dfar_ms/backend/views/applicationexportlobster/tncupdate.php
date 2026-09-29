<?php

/** @var yii\web\View $this */

/** @var backend\models\Applicationexportbechedemer $model */

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Update Export Lobster License: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Export Lobster'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">


            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'tnc', [
                'template' => "{label}\n{input}\n{hint}\n{error}",
                'labelOptions' => ['class' => 'form-label'],
                'hintOptions' => ['class' => 'form-text text-muted'],
                'errorOptions' => ['class' => 'invalid-feedback'],
            ])->widget(CKEditor::class, [
                'clientOptions' => [
                    'height' => 400,
                    'toolbar' => [
                        ['name' => 'basicstyles', 'items' => ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat']],
                        ['name' => 'paragraph', 'items' => ['NumberedList', 'BulletedList']],
                    ],
                ],
            ])->hint('Use the editor to create a rich email body with formatting, links, and images.')->label
            ("Conditions") ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>

            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
</div>
</div>