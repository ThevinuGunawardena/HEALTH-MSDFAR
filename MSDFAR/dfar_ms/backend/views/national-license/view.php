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
use backend\components\SecurityHelper;

/** @var yii\web\View $this */
/** @var backend\models\NationalLicense $model */

$this->title = $model->license_number ?? "Not Generated";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'National Licenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
$webURL = Yii::getAlias('@web');

$gearTypes = DistrictGearTypes::find()->where(["IN", "id", explode(",", $model->division_gear_types)])->all();
$boatRegistration = FishermanRegisterdBoatLicense::find()->where(["=", "id", $model->boatRegistration->id])->orderBy(["nid" => SORT_DESC])->one();

?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= Util::editPermission() ? Html::a(Yii::t('app', 'Update'), ['update', 'token' => SecurityHelper::encryptId(\backend\models\NationalLicense::class, $model->id)], ['class' => 'btn btn-primary']) : '' ?>  
                <?= Util::editPermission() ? Html::a(Yii::t('app', 'Upload Required files'), ['/file/upload', 'token' => \backend\components\SecurityHelper::encryptId(\backend\models\NationalLicense::class, $model->id), 'process' => $process], ['class' => 'btn btn-outline-primary']) : '' ?>                <?= (int) $model->status === (int) Constant::Active && $boatRegistration->validate() ? Html::a(Yii::t('app', 'View License'), ['license-view', 'token' => SecurityHelper::encryptId(\backend\models\NationalLicense::class, $model->id)], ['class' => 'btn btn-info']) : '' ?>                <?= Util::editPermission() && $model->status == Constant::PaymentPending && Yii::$app->user->can("NationalLicenseController-payment") ? Html::a(Yii::t('app', 'Update Payment'), ['payment', 'id' => $model->id], ['class' => 'btn btn-warning']) : "" ?>

                <?php
                if (!$boatRegistration->validate()) { ?>
            <div class="alert alert-warning">
                <strong>🚨
                    Please update all required information in boat registration in order to continue the license
                    workflow. 🚨</strong>
            </div>
            <?php }
            ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'license_number',
                        'format' => 'text',
                        'label' => 'License number',
                        'value' => function ($model) {
                            return $model->license_number ?? "Not Generated";
                        }
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisherman->fisherman_uid . " - " . $model->fisherman->first_name . " " . $model->fisherman->last_name;
                        }
                    ],
                    [
                        'attribute' => 'boat_registration_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boatRegistration->boatNumber->boat_number;
                        }
                    ],
                    [
                        'attribute' => 'fisheries_district',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisheriesDistrict->name;
                        }
                    ],
                    [
                        'attribute' => 'division',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->division0->name;
                        }
                    ],
                    [
                        'attribute' => 'landing_site',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->landingSite->name ?? "";
                        }
                    ],
                    [
                        'attribute' => 'main_gear_type',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->mainGearType->description ?? "";
                        }
                    ],

                    [
                        'attribute' => 'expire_date',
                        'format' => 'text'
                    ],
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
                    <h3 class="card-subtitle mb-2 text-muted">Gear Type(s)</h3>
                </div>
                <div class="col-lg-12">

                    <table class="table ">
                        <thead class="table-dark">
                        <tr>
                            <th scope="col">Gear Type</th>
                            <th scope="col">Extra</th>
                            <th scope="col">Fishing Time Durations</th>
                            <th scope="col">Fish Species</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($gearTypes != null) foreach ($gearTypes as $item) { ?>
                            <tr>
                                <th scope="row"><?= $item->gearType->description; ?> </th>
                                <td><?= str_replace("\"", " ", str_replace("{", " ", str_replace("}", " ", $item->extra)));
                                    ?>  </td>
                                <td><?php $times = [];

                                    foreach (explode(",", $item->fishing_time_durations) as $time) {
                                        $times[] = Constant::$FishingDuration[$time];
                                    }
                                    echo implode(", ", $times); ?></td>
                                <td><?php
                                    $fishTypes = MFishTypes::find()->select("name")->where(["IN", "id", explode(",", $item->fish_species)])->asArray()->all();
                                    $fish = [];
                                    foreach ($fishTypes as $fishType) {
                                        $fish[] = $fishType["name"];
                                    }
                                    echo implode(", ", $fish); ?></td>

                            </tr>
                        <?php } ?>


                        </tbody>
                    </table>
                </div>
            </div>
            <hr>

            <div class="row">
                <div class="col-lg-12">
                    <h3>Uploaded files</h3>
                </div>
               <div class="col-lg-12">

    <?php if (!empty($files)): ?>

        <ul class="list-group">

            <?php foreach ($files as $file): ?>

                <?php
                $fileName = basename(
                    (string) $file->file_name
                );

                $fileUrl =
                    rtrim(Constant::$FILE_VIEW_PATH, '/')
                    . '/files/'
                    . rawurlencode($fileName);

                $fileTypeDescription =
                    $file->fileType->discription
                    ?? Yii::t('app', 'Other');
                ?>

                <li class="list-group-item">

                    <strong>
                        <?= Html::encode(
                            $fileTypeDescription
                        ) ?>:
                    </strong>

                    <?= Html::a(
                        Html::encode($fileName),
                        $fileUrl,
                        [
                            'target' => '_blank',
                            'rel' => 'noopener noreferrer',
                        ]
                    ) ?>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php else: ?>

        <div class="alert alert-info">
            <?= Html::encode(
                Yii::t(
                    'app',
                    'No files have been uploaded.'
                )
            ) ?>
        </div>

    <?php endif; ?>

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
                                                <td><?= UserTypeUtil::getTypeNames($item->doneBy->type) ?? "Undefined" ?>  </td>
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
                                                if ($validated && $boatRegistration->validate()) {
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
                                            <input type="hidden" value="<?= $model->id ?>" id="national_license_id"/>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select id="national-payment-approval" name="status-approval"
                                                        class="form-control">
                                                    <option value="approve">Approve</option>
                                                    <option value="reject">Reject</option>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-xl-9">
                                            <div class="form-group">
                                                <label>Remarks</label>
                                                <textarea id="national-payment-remarks-approval" name="remarks-approval"
                                                          class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="form-group">
                                                <button type="button" id="national-payment-approval-btn"
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
