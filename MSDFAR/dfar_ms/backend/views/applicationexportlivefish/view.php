<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\services\CommonService;
use backend\services\Util;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\Applicationexportlivefish $model */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Export live fish'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <p>
            <p>
                <?= Util::editPermission() ? Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : "" ?>
                <?= Util::editPermission() && $model->approval_stage != Constant::Completed ? Html::a(Yii::t('app', 'Upload Required files'), ['./file/upload', 'id' => $model->id, 'process' => $process], ['class' => 'btn btn-outline-primary']) : "" ?>
                <?= Util::editPermission() && $model->approval_stage == Constant::Completed ? Html::a(Yii::t('app', 'Update Conditions'), ['update-tnc', 'id' => $model->id, 'process' => $process], ['class' => 'btn btn-outline-primary']) : "" ?>
                <?= $model->status == Constant::Active & $model->tnc != null ? Html::a(Yii::t('app', 'View License'), ['license-view', 'id' => $model->id], ['class' => 'btn btn-info']) : "" ?>
                <?= Util::editPermission() && $model->status == Constant::PaymentPending && Yii::$app->user->can("Export-license-controller-payment") ? Html::a(Yii::t('app', 'Update Payment'), ['payment', 'id' => $model->id], ['class' => 'btn btn-warning']) : "" ?>

            </p>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'full_name',
                    'address',
                    // 'fax_number',
                    'business_reg_number',

                    [
                        'attribute' => 'export_countries',
                        'format' => 'text',
                        'value' => function ($model) {
                            return CommonService::getCountryNames($model->export_countries);
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'label' => 'Status',
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
                    'created',
                    'approved_time',
                    // 'fish_species_6month:ntext',
                    // 'locally_collected_fish_quantity_kg',
                    // 'locally_collected_fish_quantity_pieces',
                    // 'locally_breed_fish_quantity_kg',
                    // 'locally_breed_fish_quantity_pieces',
                    // 're_exported_fish_quantity_kg',
                    // 're_exported_fish_quantity_pieces',
                ],
            ]) ?>

        </div>
    </div>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">

                <h3>Name of the Species of Live Fish and the quantity </h3>
                <?php if (!empty($qties)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Species of Fish</th>
                                <th>Quantity</th>
                                <th>Area of Capture</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($qties as $qty): ?>
                                <tr>
                                    <td><?= Html::encode($qty->species_fish) ?></td>
                                    <td><?= Html::encode($qty->qty) ?></td>
                                    <td><?= Html::encode($qty->area_capture) ?></td>

                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No records available.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">

                <h3>Monthly statement of Fish, Eggs, Roe or Spawn exported for last six (6)
                    months </h3>
                <?php if (!empty($statements)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Month</th>
                                <th>Species of fish</th>
                                <th>Quantity of Locally collected live fish, eggs, roe or spawn exported</th>
                                <th>Quantity of Locally breed species of live fish, eggs, roe or spawn exported</th>
                                <th>Quantity of imported fish re-exported</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($statements as $statement): ?>
                                <tr>
                                    <td><?= Html::encode($statement->month) ?></td>
                                    <td><?= Html::encode($statement->fish) ?></td>
                                    <td><?= Html::encode($statement->local_collected_qty) ?></td>
                                    <td><?= Html::encode($statement->local_breed_qty) ?></td>
                                    <td><?= Html::encode($statement->imported_reexported_qty) ?></td>

                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>No records available.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-12">
            <h3>Uploaded files</h3>
        </div>
        <div class="col-lg-12">
            <?php
            foreach ($files as $file) { ?>
                <li><?= $file->fileType->discription ?> : <a target="_blank"
                                                             href="<?= Constant::$FILE_VIEW_PATH ?>files/<?= $file->file_name ?>"><?= $file->file_name ?></a>
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
                                        <select id="status-approval" name="status-approval"
                                                class="form-control" <?= !$validated ? "disabled" : "" ?>>
                                            <option value="approve">Approve</option>
                                            <option value="reject">Reject</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-xl-9">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea id="remarks-approval"
                                                  name="remarks-approval"    <?= !$validated ? "disabled" : "" ?> class="form-control"></textarea>
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
                                    <input type="hidden" value="<?= $model->id ?>" id="boat-id"/>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select id="payment-approval" name="status-approval"
                                                class="form-control">
                                            <option value="approve">Approve</option>
                                            <option value="reject">Reject</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-xl-9">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea id="payment-remarks-approval" name="remarks-approval"
                                                  class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <button type="button" id="payment-approval-btn"
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
