<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\services\Util;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoat $model */

$this->title = $model->boatNumber->boat_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Boat Registration Renew'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$webURL = Yii::getAlias('@web');

YiiAsset::register($this);
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <p>
                <?= Util::editPermission() ? Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : "" ?>
                <?= Util::editPermission() ? Html::a(Yii::t('app', 'Upload Required files'), ['./file/upload', 'id' => $model->id, 'process' => $process], ['class' => 'btn btn-outline-primary']) : "" ?>

                <?= $model->status == Constant::Active ? Html::a(Yii::t('app', 'View License'), ['license-view', 'id' => $model->id], ['class' => 'btn btn-info']) : "" ?>
                <?= Util::editPermission() && $model->status == Constant::PaymentPending && Yii::$app->user->can("BoatRegistrationController-payment") ? Html::a(Yii::t('app', 'Update Payment'), ['payment', 'id' => $model->id], ['class' => 'btn btn-warning']) : "" ?>

            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'boat_number_id',
                        'format' => 'text',
                        'label' => 'Boat number',
                        'value' => function ($model) {
                            return $model->boatNumber->boat_number;
                        }
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'label' => 'Fisherman',
                        'value' => function ($model) {
                            return $model->fisherman->fisherman_uid;
                        }
                    ],
                    [
                        'attribute' => 'district',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisheriesDistrict->name;
                        }
                    ],
                    [
                        'attribute' => 'division',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisheriesDivision->name;
                        }
                    ],

                    [
                        'attribute' => 'landing_site',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->landingSite->name ?? "";
                        }
                    ],
                    'insurance_no',
                    'call_sign_no',
                    [
                        'attribute' => 'engine_make',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$engineMake[$model->engine_make] ?? "";
                        }
                    ],
                    'engine_horsepower',
                    'engine_serial_number',
                    'communication_equipment',
                    'fishing_equipment',
                    'navigation_equipment',
                    'mea_report',
                    'witness_name',
                    'witness_nic',
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$licenseStatus[$model->status];
                        }
                    ],
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
                        }
                    ],
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
                                                <td><?= UserTypeUtil::getTypeNames($item->doneBy->type) ?> </td>
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
                                                <select id="status-approval"
                                                        name="status-approval" <?= !$validated ? "disabled" : "" ?>
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
                                                          name="remarks-approval" <?= !$validated ? "disabled" : "" ?> class="form-control"></textarea>
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


                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">


                            <div class="row">
                                <div class="col-lg-12">
                                    <h3 class="card-subtitle mb-2 text-muted">Payments History</h3>
                                </div>
                                <div class="col-lg-12">

                                    <table class="table ">
                                        <thead class="table-dark">
                                        <tr>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Ref</th>
                                            <th scope="col">Attachment</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if ($paymentHistory != null) foreach ($paymentHistory as $item) { ?>
                                            <tr>
                                                <th scope="row"><?= $item->amount ?> </th>
                                                <td><?= $item->ref ?> </td>
                                                <td><?= $item->file ?></td>

                                            </tr>
                                        <?php } ?>


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php if ($model->status == 100) { ?>
                                <hr>
                                <?php $form = ActiveForm::begin(['options' => [
                                    'class' => 'userform'
                                ]]); ?>
                                <div class="col-xl-12">
                                    <div class="row">

                                        <div class="col-xl-3">
                                            <input type="hidden" value="<?= $model->id ?>" id="reg-boat-id"/>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select id="reg-payment-approval" name="status-approval"
                                                        class="form-control">
                                                    <option value="approve">Approve</option>
                                                    <option value="reject">Reject</option>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-xl-9">
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea id="reg-payment-remarks-approval" name="remarks-approval"
                                                          class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="form-group">
                                                <button type="button" id="reg-payment-approval-btn"
                                                        class="btn btn-primary btn-block mt-5">Submit
                                                </button>


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
