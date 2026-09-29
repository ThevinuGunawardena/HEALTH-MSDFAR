<?php

use backend\config\Constant;
use backend\services\Util;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberTransferRequest $model */

$this->title = "Transfer Request: " . $model->boatNumber->boat_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Boat Number Transfer Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
$webURL = Yii::getAlias('@web');

?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <p>
                <?= Util::editPermission() ? Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : "" ?>
                <?= Util::editPermission() ? Html::a(Yii::t('app', 'Upload Required files'), ['./file/upload', 'id' => $model->id, 'process' => $process], ['class' => 'btn btn-outline-primary']) : "" ?>

            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'boat_number',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boatNumber->boat_number;
                        }
                    ],
                    [
                        'attribute' => 'Current Owner',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boatNumber->owner0->first_name . " " . $model->boatNumber->owner0->last_name . " - NIC:" . $model->boatNumber->owner0->nic;
                        }
                    ],
                    [
                        'attribute' => 'new_owner',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->newOwner->fisherman_uid . " - " . $model->newOwner->first_name . " " . $model->newOwner->last_name . "- NIC:" . $model->newOwner->nic;
                        }
                    ],
                    'witness_name',
                    'witness_address',
                    'witness_nic',
                    'witness_sign_date',
                    'remark',
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$licenseStatus[$model->status] ?? "Error loading status";
                        }
                    ],
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
                        }
                    ],
                    'created',
                    'approved_time',
                ],
            ]) ?>

            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <h3>Uploaded files</h3>
                </div>
                <div class="col-lg-12">
                    <?php
                    foreach ($files as $file) { ?>
                        <li><?= $file->fileType->discription ?> : <a target="_blank"
                                                                     href="<?=Constant::$FILE_VIEW_PATH?>files/<?= $file->file_name ?>"><?= $file->file_name ?></a>
                        </li>
                    <?php }
                    ?>
                </div>
            </div>

            <hr>


            <?php Pjax::begin(['id' => 'boat-numbers-log']); ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">


                            <div class="row">
                                <div class="col-lg-12">
                                    <h3 class="card-subtitle mb-2 text-muted">Activity Log</h3>
                                </div>
                                <div class="col-lg-12">

                                    <table class="table ">
                                        <thead class="table-dark">
                                        <tr>
                                            <th scope="col">Officer Name</th>
                                            <th scope="col">Officer Position</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Remarks</th>
                                            <th scope="col">Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if ($approvalHistory != null) foreach ($approvalHistory as $item) { ?>
                                            <tr>
                                                <th scope="row"><?= $item->doneBy->nic ?> </th>
                                                <td><?= $item->doneBy->type ?> </td>
                                                <td><?= $item->status ?></td>
                                                <td><?= $item->remark ?></td>
                                                <td><?= $item->date_time ?></td>
                                            </tr>
                                        <?php } ?>


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php if (Util::editPermission() && $showApproveBtn) { ?>
                                <hr>
                                <?php $form = ActiveForm::begin(['options' => [
                                    'class' => 'userform'
                                ]]); ?>
                                <div class="col-xl-12">
                                    <div class="row">

                                        <div class="col-xl-3">
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select id="status-approval" name="status-approval"
                                                    <?= !$validated ? "disabled" : "" ?>
                                                        class="form-control">
                                                    <option value="approve">Approve</option>
                                                    <option value="reject">Reject</option>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-xl-9">
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea id="remarks-approval"
                                                          name="remarks-approval"  <?= !$validated ? "disabled" : "" ?>
                                                          class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="form-group">
                                                <?php
                                                if ($validated) {
                                                    ?>
                                                    <input type="submit" id="status-approval-btn" value="Submit"
                                                           class="btn btn-primary btn-block mt-5">
                                                    <?php
                                                } else {
                                                    ?>
                                                    <label class="btn btn-danger">Please fill out all required file and
                                                        upload
                                                        all required documents to continue</label>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>
                            <?php } ?>


                        </div>
                    </div>


                </div>
            </div>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
