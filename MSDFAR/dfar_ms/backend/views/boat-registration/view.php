<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\FishermanRegisterdBoatLicense;
use backend\services\Util;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var FishermanRegisterdBoatLicense $model */
/** @var array $files */
/** @var array $approvalHistory */
/** @var array $paymentHistory */
/** @var bool $showApproveBtn */
/** @var bool $validated */
/** @var string $process */

$recordToken = SecurityHelper::encryptId(
    FishermanRegisterdBoatLicense::class,
    $model->nid
);

/*
 * Use the workflow provided by the controller.
 * Fall back to the correct workflow based on renew status.
 */
$uploadProcess = (string) (
    $process
    ?? (
        (int) $model->renew === 1
            ? 'BOAT_REGISTER_ReNEW'
            : 'BOAT_REGISTER'
    )
);

$boatNumber = (string) (
    $model->boatNumber->boat_number
    ?? ''
);

$this->title = $boatNumber !== ''
    ? $boatNumber
    : Yii::t('app', 'Boat Registration');

$this->params['breadcrumbs'][] = [
    'label' => Yii::t(
        'app',
        'Fisherman Registered Boats'
    ),
    'url' => [
        '/boat-registration/index',
    ],
];

$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);

/*
 * CALL_SIGN users use the dedicated update action.
 */
$updateRoute = UserTypeUtil::hasType(
    Constant::CALL_SIGN
)
    ? '/boat-registration/update-callsign'
    : '/boat-registration/update';

/*
 * The original licence and renewed licence have
 * different view actions.
 */
$licenseViewRoute = (int) $model->renew === 1
    ? '/boat-registration/license-view'
    : '/boat-registration/license-view-first';
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">

        <div class="card-body">

            <div class="mb-3">

                <?php if (Util::editPermission()): ?>

                    <?= Html::a(
                        Yii::t('app', 'Update'),
                        [
                            $updateRoute,
                            'token' => $recordToken,
                        ],
                        [
                            'class' =>
                                'btn btn-primary mb-1',
                        ]
                    ) ?>

                <?php endif; ?>

                <?php if (
                    Util::editPermission()
                    && (int) $model->status
                        !== (int) Constant::Active
                    && !UserTypeUtil::hasType(
                        Constant::CALL_SIGN
                    )
                ): ?>

                    <?= Html::a(
                        Yii::t(
                            'app',
                            'Upload Required Files'
                        ),
                        [
                            '/file/upload',
                            'token' => $recordToken,
                            'process' => $uploadProcess,
                        ],
                        [
                            'class' =>
                                'btn btn-outline-primary mb-1',
                        ]
                    ) ?>

                <?php endif; ?>

                <?php if (
                    (int) $model->status
                        === (int) Constant::Active
                    && $model->validate()
                ): ?>

                    <?= Html::a(
                        Yii::t('app', 'View License'),
                        [
                            $licenseViewRoute,
                            'token' => $recordToken,
                        ],
                        [
                            'class' =>
                                'btn btn-info mb-1',
                        ]
                    ) ?>

                <?php endif; ?>

                <?php if (
                    Util::editPermission()
                    && (int) $model->status
                        === (int) Constant::PaymentPending
                    && Yii::$app->user->can(
                        'BoatRegistrationController-payment'
                    )
                ): ?>

                    <?= Html::a(
                        Yii::t('app', 'Update Payment'),
                        [
                            '/boat-registration/payment',
                            'token' => $recordToken,
                        ],
                        [
                            'class' =>
                                'btn btn-warning mb-1',
                        ]
                    ) ?>

                <?php endif; ?>

            </div>

            <?php if (!$model->validate()): ?>

                <div class="alert alert-warning">

                    <strong>
                        <?= Html::encode(
                            Yii::t(
                                'app',
                                'Please update all required information before viewing the licence.'
                            )
                        ) ?>
                    </strong>

                </div>

            <?php endif; ?>

            <?= DetailView::widget([
                'model' => $model,

                /*
                 * "text" and "ntext" formats HTML-encode
                 * database values before rendering them.
                 */
                'attributes' => [
                    [
                        'attribute' => 'boat_number_id',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Boat Number'
                        ),
                        'value' => static function (
                            FishermanRegisterdBoatLicense $model
                        ): string {
                            return (string) (
                                $model
                                    ->boatNumber
                                    ->boat_number
                                ?? ''
                            );
                        },
                    ],

                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Fisherman'
                        ),
                        'value' => static function (
                            FishermanRegisterdBoatLicense $model
                        ): string {
                            return (string) (
                                $model
                                    ->fisherman
                                    ->fisherman_uid
                                ?? ''
                            );
                        },
                    ],

                    [
                        'attribute' => 'district',
                        'format' => 'text',
                        'value' => static function (
                            FishermanRegisterdBoatLicense $model
                        ): string {
                            return (string) (
                                $model
                                    ->district0
                                    ->name
                                ?? ''
                            );
                        },
                    ],

                    [
                        'attribute' => 'division',
                        'format' => 'text',
                        'value' => static function (
                            FishermanRegisterdBoatLicense $model
                        ): string {
                            return (string) (
                                $model
                                    ->division0
                                    ->name
                                ?? ''
                            );
                        },
                    ],

                    [
                        'attribute' => 'landing_site',
                        'format' => 'text',
                        'value' => static function (
                            FishermanRegisterdBoatLicense $model
                        ): string {
                            return (string) (
                                $model
                                    ->landingSite
                                    ->name
                                ?? ''
                            );
                        },
                    ],

                    [
                        'attribute' => 'insurance_no',
                        'format' => 'text',
                    ],

                    [
                        'attribute' => 'call_sign_no',
                        'format' => 'text',
                    ],

                    [
                        'attribute' => 'engine_make',
                        'format' => 'text',
                        'value' => static function (
                            FishermanRegisterdBoatLicense $model
                        ): string {
                            return (string) (
                                Constant::$engineMake[
                                    $model->engine_make
                                ]
                                ?? ''
                            );
                        },
                    ],

                    [
                        'attribute' => 'engine_horsepower',
                        'format' => 'text',
                    ],

                    [
                        'attribute' => 'engine_serial_number',
                        'format' => 'text',
                    ],

                    [
                        'attribute' =>
                            'communication_equipment',
                        'format' => 'text',
                    ],

                    [
                        'attribute' => 'fishing_equipment',
                        'format' => 'text',
                    ],

                    [
                        'attribute' =>
                            'navigation_equipment',
                        'format' => 'text',
                    ],

                    [
                        'attribute' => 'witness_name',
                        'format' => 'text',
                    ],

                    [
                        'attribute' => 'witness_nic',
                        'format' => 'text',
                    ],

                    [
                        'attribute' => 'mea_report',
                        'format' => 'raw',
                        'value' => static function (
                            FishermanRegisterdBoatLicense $model
                        ): string {
                            $meaReport = trim(
                                (string) $model->mea_report
                            );

                            if ($meaReport === '') {
                                return Html::encode(
                                    Yii::t('app', 'N/A')
                                );
                            }

                            return Html::a(
                                Html::encode($meaReport),
                                [
                                    '/boat-registration/view-mea',
                                    'mea' => $meaReport,
                                ],
                                [
                                    'target' => '_blank',
                                    'rel' =>
                                        'noopener noreferrer',
                                ]
                            );
                        },
                    ],

                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => static function (
                            FishermanRegisterdBoatLicense $model
                        ): string {
                            return (string) (
                                Constant::$licenseStatus[
                                    $model->status
                                ]
                                ?? Yii::t(
                                    'app',
                                    'Unknown'
                                )
                            );
                        },
                    ],

                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'value' => static function (
                            FishermanRegisterdBoatLicense $model
                        ): string {
                            return (string) (
                                Constant::$userTypes[
                                    $model->approval_stage
                                ]['name']
                                ?? $model->approval_stage
                                ?? ''
                            );
                        },
                    ],

                    [
                        'attribute' => 'expire_date',
                        'format' => 'text',
                        'value' => static function (
                            FishermanRegisterdBoatLicense $model
                        ): string {
                            return !empty(
                                $model->expire_date
                            )
                                ? (string) $model->expire_date
                                : Yii::t(
                                    'app',
                                    'N/A'
                                );
                        },
                    ],
                ],
            ]) ?>

            <hr>

            <div class="row">

                <div class="col-lg-12">

                    <h3>
                        <?= Html::encode(
                            Yii::t(
                                'app',
                                'Uploaded Files'
                            )
                        ) ?>
                    </h3>

                </div>

                <div class="col-lg-12">

                    <?php if (!empty($files)): ?>

                        <ul class="list-group">

                            <?php foreach ($files as $file): ?>

                                <?php
                                /*
                                 * basename prevents directory traversal
                                 * through a stored filename.
                                 */
                                $fileName = basename(
                                    (string) $file->file_name
                                );

                                $fileTypeDescription =
                                    (string) (
                                        $file
                                            ->fileType
                                            ->discription
                                        ?? Yii::t(
                                            'app',
                                            'Document'
                                        )
                                    );

                                $fileUrl =
                                    rtrim(
                                        Constant::$FILE_VIEW_PATH,
                                        '/'
                                    )
                                    . '/files/'
                                    . rawurlencode($fileName);
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
                                            'rel' =>
                                                'noopener noreferrer',
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

            <?php Pjax::begin([
                'id' => 'boat-numbers-log',
            ]); ?>

            <div class="row">

                <div class="col-lg-12">

                    <div class="card mb-5 shadow-sm">

                        <div class="card-body">

                            <div class="row">

                                <div class="col-lg-12">

                                    <h3 class="card-subtitle mb-2 text-muted">

                                        <?= Html::encode(
                                            Yii::t(
                                                'app',
                                                'Activity Log'
                                            )
                                        ) ?>

                                    </h3>

                                </div>

                                <div class="col-lg-12">

                                    <div class="table-responsive">

                                        <table class="table">

                                            <thead class="table-dark">
                                            <tr>
                                                <th scope="col">
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Officer Name'
                                                        )
                                                    ) ?>
                                                </th>

                                                <th scope="col">
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Officer Position'
                                                        )
                                                    ) ?>
                                                </th>

                                                <th scope="col">
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Status'
                                                        )
                                                    ) ?>
                                                </th>

                                                <th scope="col">
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Remarks'
                                                        )
                                                    ) ?>
                                                </th>

                                                <th scope="col">
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Date'
                                                        )
                                                    ) ?>
                                                </th>
                                            </tr>
                                            </thead>

                                            <tbody>

                                            <?php if (
                                                !empty(
                                                    $approvalHistory
                                                )
                                            ): ?>

                                                <?php foreach (
                                                    $approvalHistory
                                                    as $item
                                                ): ?>

                                                    <tr>

                                                        <td>
                                                            <?= Html::encode(
                                                                $item
                                                                    ->doneBy
                                                                    ->nic
                                                                ?? ''
                                                            ) ?>
                                                        </td>

                                                        <td>
                                                            <?= Html::encode(
                                                                UserTypeUtil::getTypeNames(
                                                                    $item
                                                                        ->doneBy
                                                                        ->type
                                                                    ?? null
                                                                )
                                                            ) ?>
                                                        </td>

                                                        <td>
                                                            <?= Html::encode(
                                                                $item->status
                                                                ?? ''
                                                            ) ?>
                                                        </td>

                                                        <td>
                                                            <?= Html::encode(
                                                                $item->remark
                                                                ?? ''
                                                            ) ?>
                                                        </td>

                                                        <td>
                                                            <?= Html::encode(
                                                                $item
                                                                    ->date_time
                                                                ?? ''
                                                            ) ?>
                                                        </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php else: ?>

                                                <tr>
                                                    <td
                                                        colspan="5"
                                                        class="text-center text-muted"
                                                    >
                                                        <?= Html::encode(
                                                            Yii::t(
                                                                'app',
                                                                'No activity history is available.'
                                                            )
                                                        ) ?>
                                                    </td>
                                                </tr>

                                            <?php endif; ?>

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                            <?php if (
                                Util::editPermission()
                                && $showApproveBtn
                            ): ?>

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

                                                <label for="status-approval">
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Status'
                                                        )
                                                    ) ?>
                                                </label>

                                                <?= Html::dropDownList(
                                                    'status-approval',
                                                    'approve',
                                                    [
                                                        'approve' =>
                                                            Yii::t(
                                                                'app',
                                                                'Approve'
                                                            ),
                                                        'reject' =>
                                                            Yii::t(
                                                                'app',
                                                                'Reject'
                                                            ),
                                                    ],
                                                    [
                                                        'id' =>
                                                            'status-approval',
                                                        'class' =>
                                                            'form-control',
                                                        'disabled' =>
                                                            !$validated,
                                                    ]
                                                ) ?>

                                            </div>

                                        </div>

                                        <div class="col-xl-9">

                                            <div class="form-group">

                                                <label for="remarks-approval">
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Remarks'
                                                        )
                                                    ) ?>
                                                </label>

                                                <?= Html::textarea(
                                                    'remarks-approval',
                                                    '',
                                                    [
                                                        'id' =>
                                                            'remarks-approval',
                                                        'class' =>
                                                            'form-control',
                                                        'disabled' =>
                                                            !$validated,
                                                    ]
                                                ) ?>

                                            </div>

                                        </div>

                                        <div class="col-xl-12">

                                            <div class="form-group">

                                                <?php if ($validated): ?>

                                                    <?= Html::submitButton(
                                                        Yii::t(
                                                            'app',
                                                            'Submit'
                                                        ),
                                                        [
                                                            'id' =>
                                                                'status-approval-btn',
                                                            'class' =>
                                                                'btn btn-primary btn-block mt-5',
                                                        ]
                                                    ) ?>

                                                <?php else: ?>

                                                    <div
                                                        class="alert alert-danger"
                                                    >
                                                        <?= Html::encode(
                                                            Yii::t(
                                                                'app',
                                                                'Please complete all required information and upload all required documents to continue.'
                                                            )
                                                        ) ?>
                                                    </div>

                                                <?php endif; ?>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <?php ActiveForm::end(); ?>

                            <?php endif; ?>

                        </div>

                    </div>

                    <div class="card mb-5 shadow-sm">

                        <div class="card-body">

                            <div class="row">

                                <div class="col-lg-12">

                                    <h3 class="card-subtitle mb-2 text-muted">
                                        <?= Html::encode(
                                            Yii::t(
                                                'app',
                                                'Payment History'
                                            )
                                        ) ?>
                                    </h3>

                                </div>

                                <div class="col-lg-12">

                                    <div class="table-responsive">

                                        <table class="table">

                                            <thead class="table-dark">
                                            <tr>
                                                <th scope="col">
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Amount'
                                                        )
                                                    ) ?>
                                                </th>

                                                <th scope="col">
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Reference'
                                                        )
                                                    ) ?>
                                                </th>

                                                <th scope="col">
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Attachment'
                                                        )
                                                    ) ?>
                                                </th>
                                            </tr>
                                            </thead>

                                            <tbody>

                                            <?php if (
                                                !empty($paymentHistory)
                                            ): ?>

                                                <?php foreach (
                                                    $paymentHistory
                                                    as $item
                                                ): ?>

                                                    <tr>

                                                        <td>
                                                            <?= Html::encode(
                                                                $item->amount
                                                                ?? ''
                                                            ) ?>
                                                        </td>

                                                        <td>
                                                            <?= Html::encode(
                                                                $item->ref
                                                                ?? ''
                                                            ) ?>
                                                        </td>

                                                        <td>
                                                            <?= Html::encode(
                                                                $item->file
                                                                ?? ''
                                                            ) ?>
                                                        </td>

                                                    </tr>

                                                <?php endforeach; ?>

                                            <?php else: ?>

                                                <tr>
                                                    <td
                                                        colspan="3"
                                                        class="text-center text-muted"
                                                    >
                                                        <?= Html::encode(
                                                            Yii::t(
                                                                'app',
                                                                'No payment history is available.'
                                                            )
                                                        ) ?>
                                                    </td>
                                                </tr>

                                            <?php endif; ?>

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                            <?php if (
                                (int) $model->status === 100
                            ): ?>

                                <hr>

                                <?php $form = ActiveForm::begin([
                                    'options' => [
                                        'class' => 'userform',
                                    ],
                                ]); ?>

                                <div class="col-xl-12">

                                    <div class="row">

                                        <div class="col-xl-3">

                                            <?= Html::hiddenInput(
                                                'registration-token',
                                                $recordToken,
                                                [
                                                    'id' =>
                                                        'reg-boat-token',
                                                ]
                                            ) ?>

                                            <div class="form-group">

                                                <label
                                                    for="reg-payment-approval"
                                                >
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Status'
                                                        )
                                                    ) ?>
                                                </label>

                                                <?= Html::dropDownList(
                                                    'status-approval',
                                                    'approve',
                                                    [
                                                        'approve' =>
                                                            Yii::t(
                                                                'app',
                                                                'Approve'
                                                            ),
                                                        'reject' =>
                                                            Yii::t(
                                                                'app',
                                                                'Reject'
                                                            ),
                                                    ],
                                                    [
                                                        'id' =>
                                                            'reg-payment-approval',
                                                        'class' =>
                                                            'form-control',
                                                    ]
                                                ) ?>

                                            </div>

                                        </div>

                                        <div class="col-xl-9">

                                            <div class="form-group">

                                                <label
                                                    for="reg-payment-remarks-approval"
                                                >
                                                    <?= Html::encode(
                                                        Yii::t(
                                                            'app',
                                                            'Remarks'
                                                        )
                                                    ) ?>
                                                </label>

                                                <?= Html::textarea(
                                                    'remarks-approval',
                                                    '',
                                                    [
                                                        'id' =>
                                                            'reg-payment-remarks-approval',
                                                        'class' =>
                                                            'form-control',
                                                    ]
                                                ) ?>

                                            </div>

                                        </div>

                                        <div class="col-xl-12">

                                            <div class="form-group">

                                                <?= Html::button(
                                                    Yii::t(
                                                        'app',
                                                        'Submit'
                                                    ),
                                                    [
                                                        'type' =>
                                                            'button',
                                                        'id' =>
                                                            'reg-payment-approval-btn',
                                                        'class' =>
                                                            'btn btn-primary btn-block mt-5',
                                                    ]
                                                ) ?>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <?php ActiveForm::end(); ?>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

            <?php Pjax::end(); ?>

        </div>

    </div>

</div>