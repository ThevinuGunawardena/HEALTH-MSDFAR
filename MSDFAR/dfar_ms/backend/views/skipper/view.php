<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Skipper;
use backend\services\Util;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var Skipper $model */
/** @var array $tranings */
/** @var array $approvalHistory */
/** @var array $paymentHistory */
/** @var array $files */
/** @var bool $showApproveBtn */
/** @var bool $validated */
/** @var string $process */

$token = SecurityHelper::encryptId(
    Skipper::class,
    $model->id
);

$skipperReference = (string) (
    $model->skipper_uid
    ?? 'SKP-' . sprintf('%05d', (int) $model->id)
);

$this->title = Yii::t(
    'app',
    'Skipper License: {reference}',
    ['reference' => $skipperReference]
);
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Skippers'),
    'url' => ['/skipper/index'],
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
                            '/skipper/update',
                            'token' => $token,
                        ],
                        ['class' => 'btn btn-primary mb-1']
                    ) ?>

                    <?= Html::a(
                        Yii::t('app', 'Upload Required Files'),
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
                    Yii::t('app', 'View License'),
                    [
                        '/skipper/license-view',
                        'token' => $token,
                    ],
                    ['class' => 'btn btn-info mb-1']
                ) ?>

                <?php if (
                    (int) $model->status
                        === (int) Constant::PaymentPending
                    && Yii::$app->user->can(
                        'SkipperController-payment'
                    )
                ): ?>
                    <?= Html::a(
                        Yii::t('app', 'Update Payment'),
                        [
                            '/skipper/payment',
                            'token' => $token,
                        ],
                        ['class' => 'btn btn-warning mb-1']
                    ) ?>
                <?php endif; ?>
            </div>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'skipper_uid',
                        'format' => 'text',
                        'value' => static function (Skipper $model): string {
                            return (string) (
                                $model->skipper_uid
                                ?? Yii::t('app', 'Not Generated')
                            );
                        },
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Fisherman ID'),
                        'value' => static function (Skipper $model): string {
                            return (string) (
                                $model->fisherman->fisherman_uid
                                ?? ''
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
                        'value' => static function (Skipper $model): string {
                            return (string) (
                                $model
                                    ->highestEducationQualification
                                    ->description
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'other_qualifications',
                        'format' => 'ntext',
                    ],
                    [
                        'attribute' => 'fisheries_district',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Fisheries District'
                        ),
                        'value' => static function (Skipper $model): string {
                            return (string) (
                                $model->fisheriesDistrict->name
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'fisheries_division',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Fisheries Division'
                        ),
                        'value' => static function (Skipper $model): string {
                            return (string) (
                                $model->fisheriesDivision->name
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => static function (Skipper $model): string {
                            return (string) (
                                Constant::$licenseStatus[$model->status]
                                ?? Yii::t('app', 'Unknown')
                            );
                        },
                    ],
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Approval Stage'
                        ),
                        'value' => static function (Skipper $model): string {
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
                        'value' => static function (Skipper $model): string {
                            return !empty($model->expire_date)
                                ? (string) $model->expire_date
                                : Yii::t('app', 'N/A');
                        },
                    ],
                ],
            ]) ?>

            <hr>

            <div class="row">
                <div class="col-lg-12">
                    <h3><?= Html::encode(
                        Yii::t('app', 'Uploaded Files')
                    ) ?></h3>
                </div>
                <div class="col-lg-12">
                    <?php if (!empty($files)): ?>
                        <ul class="list-group">
                            <?php foreach ($files as $file): ?>
                                <?php
                                $fileName = basename(
                                    (string) $file->file_name
                                );
                                $description = (string) (
                                    $file->fileType->discription
                                    ?? Yii::t('app', 'Document')
                                );
                                $fileUrl = rtrim(
                                    Constant::$FILE_VIEW_PATH,
                                    '/'
                                ) . '/files/' . rawurlencode($fileName);
                                ?>
                                <li class="list-group-item">
                                    <strong><?= Html::encode(
                                        $description
                                    ) ?>:</strong>
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

            <div class="row">
                <div class="col-lg-12">
                    <h3><?= Html::encode(
                        Yii::t('app', 'Training History')
                    ) ?></h3>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Institute</th>
                                <th scope="col">Program Name</th>
                                <th scope="col">Training Period</th>
                                <th scope="col">Date Certified</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($tranings)): ?>
                                <?php foreach ($tranings as $training): ?>
                                    <tr>
                                        <td><?= Html::encode(
                                            $training->institute0->name
                                            ?? ''
                                        ) ?></td>
                                        <td><?= Html::encode(
                                            $training->programName->name
                                            ?? ''
                                        ) ?></td>
                                        <td><?= Html::encode(
                                            $training->training_period
                                            ?? ''
                                        ) ?></td>
                                        <td><?= Html::encode(
                                            $training->date_certified
                                            ?? ''
                                        ) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <?= Html::encode(
                                            Yii::t(
                                                'app',
                                                'No training records are available.'
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

            <?php Pjax::begin(['id' => 'skipper-activity-log']); ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-subtitle mb-2 text-muted">
                                <?= Html::encode(
                                    Yii::t('app', 'Activity Log')
                                ) ?>
                            </h3>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table-dark">
                                    <tr>
                                        <th>Officer Name</th>
                                        <th>Officer Position</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        <th>Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($approvalHistory)): ?>
                                        <?php foreach ($approvalHistory as $item): ?>
                                            <tr>
                                                <td><?= Html::encode(
                                                    $item->doneBy->nic
                                                    ?? ''
                                                ) ?></td>
                                                <td><?= Html::encode(
                                                    UserTypeUtil::getTypeNames(
                                                        $item->doneBy->type
                                                        ?? null
                                                    )
                                                    ?? Yii::t(
                                                        'app',
                                                        'Undefined'
                                                    )
                                                ) ?></td>
                                                <td><?= Html::encode(
                                                    $item->status
                                                    ?? ''
                                                ) ?></td>
                                                <td><?= Html::encode(
                                                    $item->remark
                                                    ?? ''
                                                ) ?></td>
                                                <td><?= Html::encode(
                                                    $item->date_time
                                                    ?? ''
                                                ) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
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

                                <div class="row">
                                    <div class="col-xl-3">
                                        <?= Html::label(
                                            Yii::t('app', 'Status'),
                                            'status-approval'
                                        ) ?>

                                        <?= Html::dropDownList(
                                            'status-approval',
                                            'approve',
                                            [
                                                'approve' => Yii::t(
                                                    'app',
                                                    'Approve'
                                                ),
                                                'reject' => Yii::t(
                                                    'app',
                                                    'Reject'
                                                ),
                                            ],
                                            [
                                                'id' => 'status-approval',
                                                'class' => 'form-control',
                                                'disabled' => !$validated,
                                            ]
                                        ) ?>
                                    </div>

                                    <div class="col-xl-9">
                                        <?= Html::label(
                                            Yii::t('app', 'Remarks'),
                                            'remarks-approval'
                                        ) ?>

                                        <?= Html::textarea(
                                            'remarks-approval',
                                            '',
                                            [
                                                'id' => 'remarks-approval',
                                                'class' => 'form-control',
                                                'disabled' => !$validated,
                                            ]
                                        ) ?>
                                    </div>

                                    <div class="col-xl-12 mt-3">
                                        <?php if ($validated): ?>
                                            <?= Html::submitButton(
                                                Yii::t('app', 'Submit'),
                                                [
                                                    'id' =>
                                                        'status-approval-btn',
                                                    'class' =>
                                                        'btn btn-primary btn-block',
                                                ]
                                            ) ?>
                                        <?php else: ?>
                                            <div class="alert alert-danger">
                                                <?= Html::encode(
                                                    Yii::t(
                                                        'app',
                                                        'Please complete the required information and upload all required documents to continue.'
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
                                    Yii::t('app', 'Payment History')
                                ) ?>
                            </h3>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="table-dark">
                                    <tr>
                                        <th>Amount</th>
                                        <th>Reference</th>
                                        <th>Attachment</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($paymentHistory)): ?>
                                        <?php foreach ($paymentHistory as $item): ?>
                                            <tr>
                                                <td><?= Html::encode(
                                                    $item->amount
                                                    ?? ''
                                                ) ?></td>
                                                <td><?= Html::encode(
                                                    $item->ref
                                                    ?? ''
                                                ) ?></td>
                                                <td><?= Html::encode(
                                                    $item->file
                                                    ?? ''
                                                ) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
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
                </div>
            </div>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
