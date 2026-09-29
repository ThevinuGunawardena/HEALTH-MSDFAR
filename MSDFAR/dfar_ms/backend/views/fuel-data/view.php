<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\DistrictGearTypes;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\MFishTypes;
use backend\services\Util;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\HighseasLicense $model */

$this->title = "FUEL DATA REQUEST ";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Highseas Licenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
$webURL = Yii::getAlias('@web');


?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <!-- <h1><?= Html::encode($this->title) ?> </h1> -->

            <p>
                <?= Util::editPermission() ? Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : "" ?>
               
            </p>

            <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
                    //  'id',
                    [
                        'attribute' => 'boat_registration_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boatRegistration->boatNumber->boat_number;
                        }
                    ], 
                    [
                'attribute' => 'bank_code',
                'format' => 'text',
                'value' => function ($model) {
                    return $model->bankName->bank_name ?? '';
                }
            ],         
            // 'bank_code',
            // 'bank_branch',
            [
                'attribute' => 'bank_branch',
                'label' => 'Branch Name',
                'value' => function ($model) {
                    return $model->branchName->branch_name ?? '';
                }
            ],
            'account_number',
            // 'fuel_quota_cat',
            [
                'attribute' => 'fuel_quota_cat',
                'label' => 'Fuel Quota',
                'value' => function ($model) {
                return $model->fuelCatdata 
                    ?$model->fuelCatdata->category . ' - ' .
                    $model->fuelCatdata->fuel_quota.'L' . ' - Fuel Quota Renews By ' .($model->fuelCatdata->time_frame == 1 ? 'Weekly' : 'Monthly') 
                    : '';                
                }
            ],
        ],
    ]) ?>
            <hr>
          

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
                                            <th scope="col">ID</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Remarks</th>
                                            <th scope="col">Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if ($approvalHistory != null) foreach ($approvalHistory as $item) { ?>
                                            <tr>
                                                <th scope="row"><?= $item->doneBy->nic ?> </th>
                                                <td><?= UserTypeUtil::getTypeNames($item->doneBy->type) ?? "Undefined" ?> </td>
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
                                               
                                                    <input type="submit" id="status-approval-btn" value="Submit"
                                                           class="btn btn-primary btn-block mt-5">
                                                    
                                                    <!-- <label class="btn btn-danger">Please fill out all required file and
                                                        upload
                                                        all required documents to continue</label> -->

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


                            
                            <?php if ($model->status == 100) { ?>
                                <hr>
                                <?php $form = ActiveForm::begin(['options' => [
                                    'class' => 'userform'
                                ]]); ?>
                                <div class="col-xl-12">
                                    <div class="row">

                                        <div class="col-xl-3">
                                            <input type="hidden" value="<?= $model->id ?>" id="highseas_license_id"/>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select id="yard-payment-approval" name="status-approval"
                                                        class="form-control">
                                                    <option value="approve">Approve</option>
                                                    <option value="reject">Reject</option>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-xl-9">
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea id="yard-payment-remarks-approval" name="remarks-approval"
                                                          class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="form-group">
                                                <button type="button" id="highseas-payment-approval-btn"
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
