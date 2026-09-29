<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\services\CommonService;
use backend\services\Util;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use backend\components\SecurityHelper;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman $model */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fishermen'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
$webURL = Yii::getAlias('@web');

$renewModel = $renewModel ?? null;
$approvalModel = $approvalModel ?? $model;
$isRenewal = $isRenewal ?? false;
$processId = $processId ?? $model->id;
$this->title = ($isRenewal && $renewModel !== null)
    ? 'Fisherman Renewal : ' . $model->fisherman_uid . ' ' . $model->first_name . ' ' . $model->last_name
    : 'Fisherman : ' . $model->fisherman_uid . ' ' . $model->first_name . ' ' . $model->last_name;
$licenseRoute = (
    $isRenewal
    && $renewModel !== null
)
    ? [
        '/fisherman/renew-license-view',
        'token' => SecurityHelper::encryptId(
            \backend\models\ProfileFishermanRenew::class,
            $renewModel->id
        ),
    ]
    : [
        '/fisherman/license-view',
        'token' => SecurityHelper::encryptId(
            \backend\models\ProfileFisherman::class,
            $model->id
        ),
    ];

?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-1">
        <div class="card-body">
            <div class="row">

                <?php if (Util::editPermission()) { ?>
                    <div class="col-lg-2">
                <?= Util::editPermission()
                    ? Html::a(
                        Yii::t('app', 'Update'),
                        [
                            '/fisherman/update',
                            'token' => SecurityHelper::encryptId(
                                \backend\models\ProfileFisherman::class,
                                $model->id
                            ),
                        ],
                        [
                            'class' => 'btn btn-primary btn-block',
                        ]
                    )
                    : ''
                ?>
                    </div>
                <?php } ?>
                <?php if (Util::editPermission()) { ?>

                    <div class="col-lg-3"><?= Util::editPermission() ? Html::a( Yii::t('app', 'Upload Required files'),['./file/upload', 'id' => $processId, 'process' => $process],['class' => 'btn btn-outline-primary btn-block']) : "" ?>                    </div>
                <?php } ?>

                <div class="col-lg-2"><?= Html::a(Yii::t('app', 'View License'),$licenseRoute,['class' => 'btn btn-info btn-block']) ?> 
                </div>
                <div class="col-lg-2">
<?= Html::a(
    Yii::t('app', 'View Profile'),
    [
        '/fisherman/profile',
        'token' => SecurityHelper::encryptId(
            \backend\models\Fisherman::class,
            $model->id
        ),
    ],
    [
        'class' => 'btn btn-info btn-block',
    ]
) ?>
                </div>
                <?php if (Util::editPermission()) { ?>

                    <div class="col-lg-3">
                        <?= Util::editPermission()  && UserTypeUtil::hasType
                        (Constant::FI) || UserTypeUtil::hasType(Constant::DO) ? Html::a(Yii::t('app', 'Manage Profile as Fisherman'), ['view-fisherman', 'fishermanId' => $model->id], ['class' => 'btn btn-danger btn-block']) : "" ?>

                    </div>
                <?php } ?>
                <?php
                if (!$model->validate()) { ?>
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <strong>🚨
                                Please update all required information in fisherman profile in order to continue the
                                workflow. 🚨</strong>
                        </div>
                    </div>
                <?php }
                ?>
            </div>

        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3">
                    <div class="image-card">
                        <img src="<?=Constant::$FILE_VIEW_PATH?>fisherman/<?= $model->profile_image ?>">
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="">
                        <img style="max-width: 300px; max-height: 300px"
                             src="<?=Constant::$FILE_VIEW_PATH?>fisherman/<?= $model->signature ?>">
                    </div>
                </div>
                <hr>
                <div class="col-lg-12">

    <?php
    $detailAttributes = [
        'fisherman_uid',
        'first_name',
        'last_name',
        'preferred_name_for_id',
        'nic',
        'passport',
        'dob',
        'gender',
        'permanent_address',
        'current_address',
        'mobile',
        'email:email',
        [
            'attribute' => 'district',
            'format' => 'text',
            'label' => 'District',
            'value' => function ($model) {
                return $model->district0->name ?? '';
            },
        ],
        [
            'attribute' => 'division',
            'format' => 'text',
            'label' => 'Division',
            'value' => function ($model) {
                return $model->division0->name ?? '';
            },
        ],
        [
            'attribute' => 'category',
            'format' => 'text',
            'label' => 'Category',
            'value' => function ($model) {
                return $model->category0->category ?? '';
            },
        ],
    ];

    if ($isRenewal && $renewModel !== null) {

       

        $detailAttributes[] = [
            'label' => 'Status',
            'value' => Constant::$licenseStatus[$renewModel->status]
                ?? $renewModel->status,
        ];

        $detailAttributes[] = [
            'label' => 'Created Date',
            'value' => $renewModel->created,
        ];

        $detailAttributes[] = [
            'label' => 'Approved Date',
            'value' => $renewModel->approved_time,
        ];

        $detailAttributes[] = [
            'label' => 'Expire Date',
            'value' => $renewModel->expire_date,
        ];

        $detailAttributes[] = [
            'label' => 'Approval Stage',
            'value' => Constant::$userTypes[$renewModel->approval_stage]['name']
                ?? $renewModel->approval_stage,
        ];

    } else {

        $detailAttributes[] = 'blood_group';
        $detailAttributes[] = 'fixed_line';

        $detailAttributes[] = [
            'attribute' => 'landing_site',
            'format' => 'text',
            'label' => 'Landing Site',
            'value' => function ($model) {
                return $model->landingSite->name ?? '';
            },
        ];

        $detailAttributes[] = 'year_recruitment';
        $detailAttributes[] = 'life_isurance_no';

        $detailAttributes[] = [
            'attribute' => 'member_fisheries_society',
            'format' => 'text',
            'label' => 'Member Fisheries Society',
            'value' => function ($model) {
                return Constant::$yesNo[$model->member_fisheries_society] ?? '';
            },
        ];

        $detailAttributes[] = 'civil';
        $detailAttributes[] = 'management_area';

        $detailAttributes[] = [
            'attribute' => 'status',
            'format' => 'text',
            'value' => function ($model) {
                return Constant::$licenseStatus[$model->status] ?? $model->status;
            },
        ];

        $detailAttributes[] = 'created';
        $detailAttributes[] = 'approved_time';

        $detailAttributes[] = [
            'attribute' => 'approval_stage',
            'format' => 'text',
            'value' => function ($model) {
                return Constant::$userTypes[$model->approval_stage]['name']
                    ?? $model->approval_stage;
            },
        ];
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $detailAttributes,
    ]) ?>

</div>
            </div>

        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
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
        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-12">


                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="card-subtitle mb-2 text-muted">Activity Log</h3>
                        </div>
                        <div class="col-lg-12">

                            <table class="table ">
                                <thead class="table-dark">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Position</th>
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
                   <?php if (
    $showApproveBtn
    && Util::editPermission()
    && CommonService::validateApprovePermission(
        $model->district,
        $model->division,
        $approvalModel->approval_stage
    )
) { ?>
    <hr>

    <?php $form = ActiveForm::begin([
        'options' => [
            'class' => 'userform',
        ],
    ]); ?>

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
                    <textarea
                        id="remarks-approval"
                        name="remarks-approval"
                        class="form-control"
                    ></textarea>
                </div>
            </div>

            <div class="col-xl-12">
                <div class="form-group">
                    <input
                        type="submit"
                        id="status-approval-btn"
                        value="Submit"
                        class="btn btn-primary btn-block mt-5"
                    >
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
</div>
