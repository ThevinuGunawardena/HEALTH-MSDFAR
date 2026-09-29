<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\SkipperRenew;
use backend\services\Util;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var SkipperRenew $model */
/** @var array $tranings */
/** @var array $approvalHistory */
/** @var array $paymentHistory */
/** @var array $files */
/** @var bool $showApproveBtn */
/** @var bool $validated */
/** @var string $process */

$token = SecurityHelper::encryptId(
    SkipperRenew::class,
    $model->id
);

$reference = (string) (
    $model->skipper_uid
    ?? Yii::t('app', 'Not Generated')
);

$this->title = Yii::t(
    'app',
    'Skipper Renewal: {reference}',
    [
        'reference' => $reference,
    ]
);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t(
        'app',
        'Skipper Renewals'
    ),
    'url' => ['/skipper-renew/index'],
];

$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <div class="mb-3">

                <?php if (Util::editPermission()): ?>

                    <?= Html::a(
                        Yii::t('app', 'Update'),
                        [
                            '/skipper-renew/update',
                            'token' => $token,
                        ],
                        [
                            'class' =>
                                'btn btn-primary mb-1',
                        ]
                    ) ?>

                    <?= Html::a(
                        Yii::t(
                            'app',
                            'Upload Required Files'
                        ),
                        [
                            '/file/upload',
                            'token' => $token,
                            'process' => $process,
                        ],
                        [
                            'class' =>
                                'btn btn-outline-primary mb-1',
                        ]
                    ) ?>

                <?php endif; ?>

                <?= Html::a(
                    Yii::t(
                        'app',
                        'View License'
                    ),
                    [
                        '/skipper-renew/license-view',
                        'token' => $token,
                    ],
                    [
                        'class' =>
                            'btn btn-info mb-1',
                    ]
                ) ?>

                <?php if (
                    (int) $model->status
                        === (int) Constant::PaymentPending
                    && Yii::$app->user->can(
                        'SkipperController-payment'
                    )
                ): ?>

                    <?= Html::a(
                        Yii::t(
                            'app',
                            'Update Payment'
                        ),
                        [
                            '/skipper-renew/payment',
                            'token' => $token,
                        ],
                        [
                            'class' =>
                                'btn btn-warning mb-1',
                        ]
                    ) ?>

                <?php endif; ?>

            </div>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'skipper_uid',
                        'format' => 'text',
                        'value' => static function (
                            SkipperRenew $model
                        ): string {
                            return (string) (
                                $model->skipper_uid
                                ?? Yii::t(
                                    'app',
                                    'Not Generated'
                                )
                            );
                        },
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Fisherman ID'
                        ),
                        'value' => static function (
                            SkipperRenew $model
                        ): string {
                            return (string) (
                                $model
                                    ->fisherman
                                    ->fisherman_uid
                                ?? Yii::t('app', 'N/A')
                            );
                        },
                    ],
                    [
                        'attribute' =>
                            'highest_education_qualification',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Highest Education Qualification'
                        ),
                        'value' => static function (
                            SkipperRenew $model
                        ): string {
                            return (string) (
                                $model
                                    ->highestEducationQualification
                                    ->description
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' =>
                            'other_qualifications',
                        'format' => 'ntext',
                    ],
                    [
                        'attribute' =>
                            'fisheries_district',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Fisheries District'
                        ),
                        'value' => static function (
                            SkipperRenew $model
                        ): string {
                            return (string) (
                                $model
                                    ->fisheriesDistrict
                                    ->name
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' =>
                            'fisheries_division',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Fisheries Division'
                        ),
                        'value' => static function (
                            SkipperRenew $model
                        ): string {
                            return (string) (
                                $model
                                    ->fisheriesDivision
                                    ->name
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => static function (
                            SkipperRenew $model
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
                        'attribute' =>
                            'approval_stage',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Approval Stage'
                        ),
                        'value' => static function (
                            SkipperRenew $model
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
                            SkipperRenew $model
                        ): string {
                            return !empty(
                                $model->expire_date
                            )
                                ? (string)
                                    $model->expire_date
                                : Yii::t(
                                    'app',
                                    'N/A'
                                );
                        },
                    ],
                ],
            ]) ?>

            <hr>

            <h3>
                <?= Html::encode(
                    Yii::t(
                        'app',
                        'Uploaded Files'
                    )
                ) ?>
            </h3>

            <?php if (!empty($files)): ?>

                <ul class="list-group">

                    <?php foreach ($files as $file): ?>

                        <?php
                        $fileName = basename(
                            (string) (
                                $file->file_name
                                ?? ''
                            )
                        );

                        $description = (string) (
                            $file
                                ->fileType
                                ->discription
                            ?? Yii::t(
                                'app',
                                'Document'
                            )
                        );

                        $fileUrl = rtrim(
                            Constant::$FILE_VIEW_PATH,
                            '/'
                        )
                            . '/files/'
                            . rawurlencode($fileName);
                        ?>

                        <li class="list-group-item">
                            <strong>
                                <?= Html::encode(
                                    $description
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

            <hr>

            <h3>
                <?= Html::encode(
                    Yii::t(
                        'app',
                        'Training Details'
                    )
                ) ?>
            </h3>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>
                            <?= Html::encode(
                                Yii::t(
                                    'app',
                                    'Institute'
                                )
                            ) ?>
                        </th>
                        <th>
                            <?= Html::encode(
                                Yii::t(
                                    'app',
                                    'Program Name'
                                )
                            ) ?>
                        </th>
                        <th>
                            <?= Html::encode(
                                Yii::t(
                                    'app',
                                    'Training Period'
                                )
                            ) ?>
                        </th>
                        <th>
                            <?= Html::encode(
                                Yii::t(
                                    'app',
                                    'Date Certified'
                                )
                            ) ?>
                        </th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php if (!empty($tranings)): ?>

                        <?php foreach (
                            $tranings
                            as $training
                        ): ?>

                            <tr>
                                <td>
                                    <?= Html::encode(
                                        (string) (
                                            $training
                                                ->institute0
                                                ->name
                                            ?? ''
                                        )
                                    ) ?>
                                </td>
                                <td>
                                    <?= Html::encode(
                                        (string) (
                                            $training
                                                ->programName
                                                ->name
                                            ?? ''
                                        )
                                    ) ?>
                                </td>
                                <td>
                                    <?= Html::encode(
                                        (string) (
                                            $training
                                                ->training_period
                                            ?? ''
                                        )
                                    ) ?>
                                </td>
                                <td>
                                    <?= Html::encode(
                                        (string) (
                                            $training
                                                ->date_certified
                                            ?? ''
                                        )
                                    ) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td
                                colspan="4"
                                class="text-center text-muted"
                            >
                                <?= Html::encode(
                                    Yii::t(
                                        'app',
                                        'No training records were found.'
                                    )
                                ) ?>
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>
                </table>
            </div>

            <?php Pjax::begin([
                'id' => 'skipper-renew-log',
            ]); ?>

            <div class="card mb-5 shadow-sm">
                <div class="card-body">

                    <h3 class="card-subtitle mb-2 text-muted">
                        <?= Html::encode(
                            Yii::t(
                                'app',
                                'Activity Log'
                            )
                        ) ?>
                    </h3>

                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-dark">
                            <tr>
                                <th>
                                    <?= Html::encode(
                                        Yii::t(
                                            'app',
                                            'Officer Name'
                                        )
                                    ) ?>
                                </th>
                                <th>
                                    <?= Html::encode(
                                        Yii::t(
                                            'app',
                                            'Officer Position'
                                        )
                                    ) ?>
                                </th>
                                <th>
                                    <?= Html::encode(
                                        Yii::t(
                                            'app',
                                            'Status'
                                        )
                                    ) ?>
                                </th>
                                <th>
                                    <?= Html::encode(
                                        Yii::t(
                                            'app',
                                            'Remarks'
                                        )
                                    ) ?>
                                </th>
                                <th>
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
                                !empty($approvalHistory)
                            ): ?>

                                <?php foreach (
                                    $approvalHistory
                                    as $item
                                ): ?>

                                    <tr>
                                        <td>
                                            <?= Html::encode(
                                                (string) (
                                                    $item
                                                        ->doneBy
                                                        ->nic
                                                    ?? ''
                                                )
                                            ) ?>
                                        </td>
                                        <td>
                                            <?= Html::encode(
                                                (string) (
                                                    UserTypeUtil::getTypeNames(
                                                        $item
                                                            ->doneBy
                                                            ->type
                                                        ?? null
                                                    )
                                                    ?? Yii::t(
                                                        'app',
                                                        'Undefined'
                                                    )
                                                )
                                            ) ?>
                                        </td>
                                        <td>
                                            <?= Html::encode(
                                                (string) (
                                                    $item
                                                        ->status
                                                    ?? ''
                                                )
                                            ) ?>
                                        </td>
                                        <td>
                                            <?= Html::encode(
                                                (string) (
                                                    $item
                                                        ->remark
                                                    ?? ''
                                                )
                                            ) ?>
                                        </td>
                                        <td>
                                            <?= Html::encode(
                                                (string) (
                                                    $item
                                                        ->date_time
                                                    ?? ''
                                                )
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
                                                'No activity records were found.'
                                            )
                                        ) ?>
                                    </td>
                                </tr>

                            <?php endif; ?>

                            </tbody>
                        </table>
                    </div>

                    <?php if (
                        Util::editPermission()
                        && $showApproveBtn
                    ): ?>

                        <hr>

                        <?php $form =
                            ActiveForm::begin([
                                'options' => [
                                    'class' =>
                                        'userform',
                                ],
                            ]); ?>

                        <div class="row">

                            <div class="col-xl-3">
                                <div class="form-group">
                                    <label
                                        for="status-approval"
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
                                    <label
                                        for="remarks-approval"
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
                                <?php if ($validated): ?>

                                    <?= Html::submitButton(
                                        Yii::t(
                                            'app',
                                            'Submit'
                                        ),
                                        [
                                            'class' =>
                                                'btn btn-primary btn-block mt-3',
                                        ]
                                    ) ?>

                                <?php else: ?>

                                    <div
                                        class="alert alert-danger"
                                    >
                                        <?= Html::encode(
                                            Yii::t(
                                                'app',
                                                'Please complete all required fields and upload all required documents before continuing.'
                                            )
                                        ) ?>
                                    </div>

                                <?php endif; ?>
                            </div>

                        </div>

                        <?php ActiveForm::end(); ?>

                    <?php endif; ?>

                </div>
            </div>

            <div class="card mb-5 shadow-sm">
                <div class="card-body">

                    <h3 class="card-subtitle mb-2 text-muted">
                        <?= Html::encode(
                            Yii::t(
                                'app',
                                'Payment History'
                            )
                        ) ?>
                    </h3>

                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-dark">
                            <tr>
                                <th>
                                    <?= Html::encode(
                                        Yii::t(
                                            'app',
                                            'Amount'
                                        )
                                    ) ?>
                                </th>
                                <th>
                                    <?= Html::encode(
                                        Yii::t(
                                            'app',
                                            'Reference'
                                        )
                                    ) ?>
                                </th>
                                <th>
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
                                    as $payment
                                ): ?>

                                    <?php
                                    $paymentFile =
                                        basename(
                                            (string) (
                                                $payment->file
                                                ?? ''
                                            )
                                        );
                                    ?>

                                    <tr>
                                        <td>
                                            <?= Html::encode(
                                                (string) (
                                                    $payment
                                                        ->amount
                                                    ?? ''
                                                )
                                            ) ?>
                                        </td>
                                        <td>
                                            <?= Html::encode(
                                                (string) (
                                                    $payment->ref
                                                    ?? ''
                                                )
                                            ) ?>
                                        </td>
                                        <td>
                                            <?= Html::encode(
                                                $paymentFile
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
                                                'No payment records were found.'
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

            <?php Pjax::end(); ?>

        </div>
    </div>
</div>
