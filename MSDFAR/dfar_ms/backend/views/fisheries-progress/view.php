<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\MFiDistrict;
use yii\widgets\ActiveForm;


/** @var yii\web\View $this */
/** @var backend\models\FisheriesProgress $model */
$districtModel = MFiDistrict::findOne($model->district); // Find district by ID
$districtName = $districtModel ? $districtModel->name : 'Unknown District'; // Default if no district found
$formattedMonth = Yii::$app->formatter->asDate($model->month, 'php:Y F');

$this->title = $formattedMonth . " For " . $districtName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fisheries Progresses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="col-xl-12">


    <p>
        <!-- <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?> -->
        <!-- <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?> -->
    </p>
<div class="fisheries-progress-view">

    <!-- Display the related FisheriesProgressRecords -->
    <div class="fisheries-progress-records-form">
    <?php $form = ActiveForm::begin([
        'id' => 'fisheries-progress-records-form',
        'action' => ['view', 'id' => $model->id], // Form submission to the current view action
    ]); ?>
<h2>Management</h2>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($newRecord, 'topic')->textInput(['class' => 'form-control']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($newRecord, 'description')->textInput(['class' => 'form-control']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
                        <?= $form->field($newRecord, 'type')->hiddenInput(['class' => 'form-control','value' => 1])->label(false)  ?> <!-- Adjust class for better width -->

        </div>
        <div class="col-md-6">
    <?= $form->field($newRecord, 'fisheries_progress_id')->hiddenInput(['value' => $model->id])->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($newRecord, 'officer_uid')->hiddenInput(['value' => $model->officer, 'class' => 'form-control'])->label(false) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>


<div class="fisheries-progress-records-form">
    <?php $form = ActiveForm::begin([
        'id' => 'fisheries-progress-records-form',
        'action' => ['view', 'id' => $model->id], // Form submission to the current view action
    ]); ?>
<h2>Conservation</h2>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($newRecord, 'topic')->textInput(['class' => 'form-control']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($newRecord, 'description')->textInput(['class' => 'form-control']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
                        <?= $form->field($newRecord, 'type')->hiddenInput(['class' => 'form-control','value' => 2])->label(false)  ?> <!-- Adjust class for better width -->

        </div>
        <div class="col-md-6">
    <?= $form->field($newRecord, 'fisheries_progress_id')->hiddenInput(['value' => $model->id])->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($newRecord, 'officer_uid')->hiddenInput(['value' => $model->officer, 'class' => 'form-control'])->label(false) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<div class="fisheries-progress-records-form">
    <?php $form = ActiveForm::begin([
        'id' => 'fisheries-progress-records-form',
        'action' => ['view', 'id' => $model->id], // Form submission to the current view action
    ]); ?>
<h2>Regulations</h2>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($newRecord, 'topic')->textInput(['class' => 'form-control']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($newRecord, 'description')->textInput(['class' => 'form-control']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
                        <?= $form->field($newRecord, 'type')->hiddenInput(['class' => 'form-control','value' => 3])->label(false)  ?> <!-- Adjust class for better width -->

        </div>
        <div class="col-md-6">
    <?= $form->field($newRecord, 'fisheries_progress_id')->hiddenInput(['value' => $model->id])->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($newRecord, 'officer_uid')->hiddenInput(['value' => $model->officer, 'class' => 'form-control'])->label(false) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<div class="fisheries-progress-records-form">
    <?php $form = ActiveForm::begin([
        'id' => 'fisheries-progress-records-form',
        'action' => ['view', 'id' => $model->id], // Form submission to the current view action
    ]); ?>
<h2>Development</h2>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($newRecord, 'topic')->textInput(['class' => 'form-control']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($newRecord, 'description')->textInput(['class' => 'form-control']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
                        <?= $form->field($newRecord, 'type')->hiddenInput(['class' => 'form-control','value' => 4])->label(false)  ?> <!-- Adjust class for better width -->

        </div>
        <div class="col-md-6">
    <?= $form->field($newRecord, 'fisheries_progress_id')->hiddenInput(['value' => $model->id])->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($newRecord, 'officer_uid')->hiddenInput(['value' => $model->officer, 'class' => 'form-control'])->label(false) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<h2>Summary of Records</h2>

    <?php if (!empty($groupedRecords)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Topic and Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groupedRecords as $type => $records): ?>
                    <tr>
                        <td rowspan="<?= count($records) ?>" style="vertical-align: middle;">
                            <!-- Display type name -->
                            <?= $type == 1 ? 'Management' : ($type == 2 ? 'Conservation' : 'Regulations') ?>
                        </td>
                        <?php foreach ($records as $key => $record): ?>
                            <!-- Display Topic and Description for each record -->
                            <?php if ($key > 0): ?>
                                <tr> <!-- Start a new row for subsequent records of the same type -->
                            <?php endif; ?>
                                <td>
                                    <strong>Topic:</strong> <?= Html::encode($record->topic) ?><br>
                                    <strong>Description:</strong> <?= Html::encode($record->description) ?>
                                </td>
                            <?php if ($key > 0): ?>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No records available.</p>
    <?php endif; ?>
</div>
</div>
