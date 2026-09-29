<?php

use backend\config\Constant;
use backend\models\Files;
use backend\models\User;
use backend\services\CommonService;
use backend\services\Util;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\DepartureBoats $model */
/** @var string $token */

$this->title = $model->boat->boat_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Departure Boats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

$roleString = Yii::$app->user->identity->type ?? '';

// Convert to array
$roles = explode(',', $roleString);

// Assign main & secondary
$mainRole = $roles[0] ?? null;
$secondaryRole = $roles[1] ?? null;

// Conditions
$isMainPaymentOfficer = ($mainRole == Constant::VMS_PAYMENT_OFFICER_MAIN);
$isSecondaryPaymentOfficer = ($secondaryRole == Constant::VMS_PAYMENT_OFFICER_SECONDARY);

// Users who can update the VMS boolean status from this page.
$canUpdateVmsStatus = $isMainPaymentOfficer
    || $isSecondaryPaymentOfficer
    || Util::adminPermission();

?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <div class="mb-3">
                <?php if ($isMainPaymentOfficer): ?>

                    <!-- Main payment officer: payment-related actions only -->
                    <?= Html::a(
                        Yii::t('app', 'Update Payment details'),
                        ['update-payment', 'token' => $token],
                        ['class' => 'btn btn-primary mr-1 mb-1']
                    ) ?>

                <?php elseif ($isSecondaryPaymentOfficer): ?>

                    <?= Util::adminPermission()
                        ? Html::a(
                            Yii::t('app', 'Update Boat status'),
                            ['update', 'token' => $token],
                            ['class' => 'btn btn-primary mr-1 mb-1']
                        )
                        : '' ?>

                    <?= CommonService::validateEditPermissionBoolean()
                        ? Html::a(
                            Yii::t('app', 'Update Owner details'),
                            ['update-owner', 'token' => $token],
                            ['class' => 'btn btn-primary mr-1 mb-1']
                        )
                        : '' ?>

                    <?= CommonService::validateEditPermissionBoolean()
                        ? Html::a(
                            Yii::t('app', 'Add Comment To Boat'),
                            ['add-boat-comment', 'token' => $token],
                            ['class' => 'btn btn-primary mr-1 mb-1']
                        )
                        : '' ?>

                    <?= Html::a(
                        Yii::t('app', 'Update Payment details'),
                        ['update-payment', 'token' => $token],
                        ['class' => 'btn btn-primary mr-1 mb-1']
                    ) ?>

                <?php else: ?>

                    <?= Util::adminPermission()
                        ? Html::a(
                            Yii::t('app', 'Update Boat status'),
                            ['update', 'token' => $token],
                            ['class' => 'btn btn-primary mr-1 mb-1']
                        )
                        : '' ?>

                    <?= CommonService::validateEditPermissionBoolean()
                        ? Html::a(
                            Yii::t('app', 'Update Owner details'),
                            ['update-owner', 'token' => $token],
                            ['class' => 'btn btn-primary mr-1 mb-1']
                        )
                        : '' ?>

                    <?= CommonService::validateEditPermissionBoolean()
                        ? Html::a(
                            Yii::t('app', 'Add Comment To Boat'),
                            ['add-boat-comment', 'token' => $token],
                            ['class' => 'btn btn-primary mr-1 mb-1']
                        )
                        : '' ?>

                    <?= Util::adminPermission()
                        && Yii::$app->user->can('DepartureBoatController-payment')
                        ? Html::a(
                            Yii::t('app', 'Update Payment details'),
                            ['update-payment', 'token' => $token],
                            ['class' => 'btn btn-primary mr-1 mb-1']
                        )
                        : '' ?>

                <?php endif; ?>

                <?php if (Yii::$app->user->can('DepartureBoatController-VMS')): ?>

    <?= Html::beginForm(
        ['update-vms-status', 'token' => $token],
        'post',
        ['class' => 'd-inline-block']
    ) ?>

    <?= Html::hiddenInput(
        'VMS',
        (int) $model->VMS === 1 ? 0 : 1
    ) ?>

    <?= Html::submitButton(
        (int) $model->VMS === 1
            ? Yii::t('app', 'Set VMS Inactive')
            : Yii::t('app', 'Set VMS Active'),
        [
            'class' => (int) $model->VMS === 1
                ? 'btn btn-danger mr-1 mb-1'
                : 'btn btn-success mr-1 mb-1',
            'data' => [
                'confirm' => Yii::t(
                    'app',
                    'Are you sure you want to update the VMS status?'
                ),
                'method' => 'post',
            ],
        ]
    ) ?>

                    <?= Html::endForm() ?>
                <?php endif; ?>
            </div>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'boat_number_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boat->boat_number;
                        }
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisherman->preferred_name_for_id??'N/A';
                        }
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'label' => 'Owner email',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisherman->email??'N/A';
                        }
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'label' => 'Owner mobile',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisherman->mobile??'N/A';
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            if ((int) $model->compulsory_service === 0) {
                                return 'Compulsory Service Pending';
                            }

                            return empty($model->status)
                                ? 'Departure Allowed'
                                : $model->status;
                        },
                    ],
                    // [
                    //     'attribute' => 'VMS',
                    //     'label' => 'VMS Status',
                    //     'format' => 'raw',
                    //     'value' => function ($model) {
                    //         return (int) $model->VMS === 1
                    //             ? Html::tag(
                    //                 'span',
                    //                 'VMS Active',
                    //                 ['class' => 'badge badge-success']
                    //             )
                    //             : Html::tag(
                    //                 'span',
                    //                 'VMS Inactive',
                    //                 ['class' => 'badge badge-danger']
                    //             );
                    //     },
                    // ],
                    'approved_by',
                    'timestamp',
//            'harbor',
                    'district',
                    'date_violation',

                    [
                        'attribute' => 'dep_cancelled_by',
                        'label' => 'Last activity done by',
                        'format' => 'text',
                        'value' => function ($model) {
                            $user = User::findOne($model->dep_cancelled_by);
                            return $user->nic ?? $model->dep_cancelled_by;
                        }
                    ],
                    'dep_cancel_date',
                    'remarks',
                    'offence',
                    'to_date',
                ],
                
            ]) ?>

        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="col-lg-12">
                <h2>Boat History</h2>
            </div>
            <div class="col-lg-12">

                <?= GridView::widget([
                    'dataProvider' => $activity,
                    'pager' => [
                        'class' => LinkPager::class,
                        'firstPageLabel' => 'First',
                        'lastPageLabel' => 'Last'
                    ],

                    'columns' => [

                        'served_vessel',
                        'dep_date',
                        'dep_id',
                        'activity',
                        'description',
                            [
                                    'attribute' => 'date_time',
                                    'label' => 'Date',
                                    'format' => ['date', 'php:Y-m-d'],
                            ],
                        'to_date',

                        // ===============================
                        // ✅ Supporting Documents Column
                        // ===============================
                       [
    'label' => 'Supporting Documents',
    'format' => 'raw',
    'value' => function ($model) {

        if ($model->activity !== 'comment') {
            return '-';
        }

        // Get files linked to THIS comment
        $files = Files::find()
            ->where(['process_id' => $model->id])
            ->all();

        if (empty($files)) {
            return '<span class="text-muted">-</span>';
        }

        $links = [];

        foreach ($files as $file) {

            // ✅ USE VIEW PATH
            $url = Constant::$FILE_VIEW_PATH
                . 'files/'
                . rawurlencode(
                    basename((string) $file->file_name)
                );

            $links[] = Html::a(
                '<i class="fa fa-download"></i> ' . 'Download Supporting Document',
                $url,
                [
                    'target' => '_blank',
                    'class' => 'd-block',
                    'rel' => 'noopener noreferrer',
                ]
            );
        }

        return implode('', $links);
    }
]

                    ],
                ]); ?>

            </div>
        </div>
    </div>
</div>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="col-lg-12">
                <h2>VMS Event Hostory</h2>
            </div>
            <div class="col-lg-12">
               <?= GridView::widget([
                    'dataProvider' => $blueTrakerEvents,
                    'summary' => '',
                    'emptyText' => 'No BlueTraker events found for this boat.',
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],
                        [
                            'attribute' => 'Event',
                            'label' => 'Event',
                            'value' => function ($model) {
                                return Constant::$BlueTrakerEventTypes[$model->Event]
                                    ?? 'Unknown Event';
                            },
                        ],
                        // [
                        //     'attribute' => 'CreatedGpsTime',
                        //     'label' => 'GPS Created Time',
                        // ],
                        [
                            'attribute' => 'ReceiveTime',
                            'label' => 'Received Time',
                        ],
                        [
                            'attribute' => 'Latitude',
                            'label' => 'Latitude',
                        ],
                        [
                            'attribute' => 'Longitude',
                            'label' => 'Longitude',
                        ],
                        // [
                        //     'attribute' => 'Speed',
                        //     'label' => 'Speed',
                        // ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>


<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="col-lg-12">
                <h2>Payment History</h2>
            </div>
            <div class="col-lg-12">
                <?= GridView::widget([
                    'dataProvider' => $payments,
                    'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],

                    'columns' => [
//                    ['class' => 'yii\grid\SerialColumn'],

//                    'id',
                        [
                            'attribute' => 'amount',
                            'format' => 'text',
                            'value' => function ($model) {
                                return number_format($model->amount, 2);
                            }
                        ],
                         [
                                    'attribute' => 'from_month',
                                    'label' => 'Applicable From',
                                    'format' => ['date', 'php:Y-m'],
                            ],
                            [
                                    'attribute' => 'to_date',
                                    'label' => 'Applicable Until',
                                    'format' => ['date', 'php:Y-m'],
                            ],
                        'file',

                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
    <div class="card-body">

        <div class="col-lg-12">
            <h2>Payment History</h2>
        </div>

        <div class="col-lg-12">
           <div class="alert">
            <div class="alert alert-warning">

            <strong>Previous Unpaid Months:</strong>

            <?php if (!$previousUnpaidPaymentData['hasPaymentHistory']): ?>
             No payment history available.

            <?php elseif ($previousUnpaidPaymentData['count'] === 0): ?>
                No previous unpaid months.

             <?php else: ?>
            <?= (int) $previousUnpaidPaymentData['count'] ?>
            month(s) —
            <?= Html::encode(implode(', ', $previousUnpaidPaymentData['months'])) ?>
             </div>

            <hr>
            <div class="alert alert-danger">

            <strong>Important Notice:</strong>
            Please settle all previous unpaid payments on or before
            <strong>31 December 2026</strong>.
            If these payments are not settled, the boat departure will be cancelled.
                         </div>

        <?php endif; ?>
        </div>
        </div>

        <div class="col-lg-12">
            <?= GridView::widget([
                'dataProvider' => $payments,
                'pager' => [
                    'class' => LinkPager::class,
                    'firstPageLabel' => 'First',
                    'lastPageLabel' => 'Last'
                ],
                'columns' => [
                    [
                        'attribute' => 'amount',
                        'format' => 'text',
                        'value' => function ($model) {
                            return number_format($model->amount, 2);
                        }
                    ],
                    [
                        'attribute' => 'from_month',
                        'label' => 'Applicable From',
                        'format' => ['date', 'php:Y-m'],
                    ],
                    [
                        'attribute' => 'to_date',
                        'label' => 'Applicable Until',
                        'format' => ['date', 'php:Y-m'],
                    ],
                    'file',
                ],
            ]); ?>
        </div>

    </div>
</div>
</div>