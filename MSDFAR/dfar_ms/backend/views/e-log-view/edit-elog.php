<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use backend\config\Constant;

$this->title = 'Edit E-Log #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'E-Log View', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'View', 'url' => ['view', 'id' => $model->id]];
$isAdminUser = \backend\config\UserTypeUtil::hasType(Constant::AD_Highseas) || \backend\config\UserTypeUtil::hasType(Constant::ITD);
$this->params['breadcrumbs'][] = 'Edit';
?>

<div class="col-xl-8 col-lg-10 col-md-12">
    <div class="card shadow-sm mb-5">
        <div class="card-header">
            <h5 class="mb-0">Edit E-Log</h5>
        </div>
        <div class="card-body">

            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'log_sheet_number')->textInput() ?>
            <?= $form->field($model, 'log_book_no')->textInput() ?>

            <?= $form->field($model, 'vessel_id')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'skipper_id')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone_number')->textInput() ?>

            <?= $form->field($model, 'arrival_date')->input('date') ?>

            <?= $form->field($model, 'arrival_harbour')->dropDownList(
                $harbours,
                ['prompt' => '-- Select Arrival Harbour --']
            ) ?>

            <?= $form->field($model, 'departure_date')->input('date') ?>

            <?= $form->field($model, 'departure_harbour')->dropDownList(
                $harbours,
                ['prompt' => '-- Select Departure Harbour --']
            ) ?>
            <?php if ($isAdminUser): ?>
            <?= $form->field($model, 'approve')->checkbox() ?>
            <?php endif; ?>
            <div class="mt-3">
                <?= Html::submitButton('Save Changes', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary ml-2']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>