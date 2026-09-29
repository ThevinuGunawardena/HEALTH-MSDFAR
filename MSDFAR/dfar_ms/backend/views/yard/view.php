<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\ProfileYard $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Yards'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="profile-yard-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'owner',
            [
                'attribute' => 'owner',
                'format' => 'text',
                'label' => 'Owner',
                'value' => function ($model) {
                    return $model->owner0->first_name;
                }
            ],
            'address',
            'mobile_number',
            'land_line',
            'email:email',
            'web',
            'fax',
            'business_reg_no',
            'business_reg_date',
            'land_owner',
            'deed_number',
            'ownership_get_date',
            'land_area',
            'land_area_under_roof',
            'remark',
            [
                'attribute' => 'admin_district',
                'format' => 'text',
                'label' => 'Admin district',
                'value' => function ($model) {
                    return $model->adminDistrict->name;
                }
            ],
            [
                'attribute' => 'fisheries_district',
                'format' => 'text',
                'label' => 'Fisheries district',
                'value' => function ($model) {
                    return $model->fisheriesDistrict->name;
                }
            ],
            [
                'attribute' => 'division',
                'format' => 'text',
                'label' => 'Division',
                'value' => function ($model) {
                    return $model->division0->name;
                }
            ],
            'division',
            'gps_latitude',
            'gps_longitude',
            'transpotation_method',
            'distance_rural_hospital',
            'distance_district_hospital',
            'distance_base_hospital',
            'distance_teaching_hospital',
            'distance_genaral_hospital',
            'distance_fire_brigade',
            'distance_police_station',
        ],
    ]) ?>
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
                                        <th scope="row"><?=  $item->doneBy->nic ?> </th>
                                        <td><?=  $item->doneBy->type ?> </td>
                                        <td><?=$item->status  ?></td>
                                        <td><?=$item->remark  ?></td>
                                        <td><?= $item->date_time ?></td>
                                    </tr>
                                <?php } ?>


                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if ($showApproveBtn) { ?>
                        <hr>
                        <?php $form = ActiveForm::begin(['options' => [
                            'class' => 'userform'
                        ]]); ?>
                        <div class="col-xl-12">
                            <div class="row">

                                <div class="col-xl-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select id="status-approval" name="status-approval" class="form-control">
                                            <option value="approve">Approve</option>
                                            <option value="reject">Reject</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-xl-9">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea id="remarks-approval" name="remarks-approval" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <input type="submit" id="status-approval-btn" value="Submit"
                                               class="btn btn-primary btn-block mt-5">


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
                                        <th scope="row"><?=  $item->amount ?> </th>
                                        <td><?=  $item->ref ?> </td>
                                        <td><?=$item->file  ?></td>

                                    </tr>
                                <?php } ?>


                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if ($model->status==100) { ?>
                        <hr>
                        <?php $form = ActiveForm::begin(['options' => [
                            'class' => 'userform'
                        ]]); ?>
                        <div class="col-xl-12">
                            <div class="row">

                                <div class="col-xl-3">
                                    <input type="hidden" value="<?= $model->id ?>" id="reg-yard-id"/>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select id="yard-payment-approval" name="status-approval" class="form-control">
                                            <option value="approve">Approve</option>
                                            <option value="reject">Reject</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-xl-9">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea id="yard-payment-remarks-approval" name="remarks-approval" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <button type="button" id="yard-payment-approval-btn"
                                                class="btn btn-primary btn-block mt-5">Submit</button>


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
