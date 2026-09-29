<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumbers;
use backend\services\Util;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumbers $model */
/** @var string $token */
/** @var array $approvalHistory */
/** @var bool $showApproveBtn */
/** @var bool $showRejectBtn */
/** @var array $paymentHistory */
/** @var string $process */
/** @var array $files */
/** @var bool $validated */
/** @var array $activeRecords */

$token = trim((string) ($token ?? ''));

if ($token === '') {
    $token = SecurityHelper::encryptId(
        BoatNumbers::class,
        $model->id
    );
}

$this->title = trim((string) $model->boat_number) !== ''
    ? (string) $model->boat_number
    : Yii::t('app', 'Boat Number: Not Generated');

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Boat Numbers'),
    'url' => ['/boat-numbers/index'],
];
$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);

$status = (int) $model->status;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="mb-3">
                <?php if (Util::editPermission()): ?>
                    <?= Html::a(
                        Yii::t('app', 'Update'),
                        [
                            '/boat-numbers/update',
                            'token' => $token,
                        ],
                        [
                            'class' => 'btn btn-primary mb-1',
                        ]
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

                <?php if ($status === (int) Constant::Active): ?>
                    <?= Html::a(
                        Yii::t('app', 'View License'),
                        [
                            '/boat-numbers/license-view',
                            'token' => $token,
                        ],
                        [
                            'class' => 'btn btn-info mb-1',
                        ]
                    ) ?>

                    <?= Html::a(
                        Yii::t('app', 'Add Previous Owners'),
                        [
                            '/boat-owners-log/create',
                            'boatNo' => (string) $model->boat_number,
                        ],
                        [
                            'class' =>
                                'btn btn-outline-primary mb-1',
                        ]
                    ) ?>
                <?php endif; ?>

                <?php if (
                    Util::editPermission()
                    && $status === (int) Constant::PaymentPending
                    && Yii::$app->user->can(
                        'BoatNumbersController-payment'
                    )
                ): ?>
                    <?= Html::a(
                        Yii::t('app', 'Update Payment'),
                        [
                            '/boat-numbers/payment',
                            'token' => $token,
                        ],
                        [
                            'class' => 'btn btn-warning mb-1',
                        ]
                    ) ?>
                <?php endif; ?>
            </div>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'boat_number',
                        'format' => 'text',
                    ],
                    [
                        'attribute' => 'boat_design',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            return (string) (
                                $model->boatDesign?->design_notation
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'owner',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Owner'),
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            $owner = $model->owner0;

                            if ($owner === null) {
                                return '';
                            }

                            $name = trim(
                                (string) ($owner->first_name ?? '')
                                . ' '
                                . (string) ($owner->last_name ?? '')
                            );

                            $districtCode = (string) (
                                $owner->district0?->code
                                ?? ''
                            );

                            $result = (string) ($owner->nic ?? '');

                            if ($name !== '') {
                                $result .= ' (' . $name . ')';
                            }

                            if ($districtCode !== '') {
                                $result .= ' [' . $districtCode . ']';
                            }

                            return $result;
                        },
                    ],
                    [
                        'attribute' => 'boat_type',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            return (string) (
                                $model->boatType?->code
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'fisheries_district',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Fisheries District'
                        ),
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            return (string) (
                                $model->fisheriesDistrict?->name
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
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            return (string) (
                                $model->fisheriesDivision?->name
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'yard',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            return (string) (
                                $model->yard0?->name
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'additional_conditions',
                        'format' => 'ntext',
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'label' => Yii::t('app', 'Status'),
                        'value' => static function (
                            BoatNumbers $model
                        ): string {
                            return (string) (
                                Constant::$licenseStatus[
                                    $model->status
                                ]
                                ?? Yii::t(
                                    'app',
                                    'Unknown Status'
                                )
                            );
                        },
                    ],
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumbers $model
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
                        'attribute' => 'created',
                        'format' => 'text',
                    ],
                    [
                        'attribute' => 'approved_time',
                        'format' => 'text',
                    ],
                ],
            ]) ?>

            <hr>

            <h3>
                <?= Html::encode(
                    Yii::t('app', 'Uploaded Files')
                ) ?>
            </h3>

            <?php if (!empty($files)): ?>
                <ul class="list-unstyled">
                    <?php foreach ($files as $file): ?>
                        <?php
                        $fileName = basename(
                            (string) $file->file_name
                        );

                        $fileUrl =
                            rtrim(
                                Constant::$FILE_VIEW_PATH,
                                '/'
                            )
                            . '/files/'
                            . rawurlencode($fileName);

                        $fileType = (string) (
                            $file->fileType?->discription
                            ?? Yii::t('app', 'Document')
                        );
                        ?>

                        <li class="mb-2">
                            <?= Html::encode($fileType) ?>:

                            <?= Html::a(
                                $fileName,
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
                <p class="text-muted">
                    <?= Html::encode(
                        Yii::t(
                            'app',
                            'No files have been uploaded.'
                        )
                    ) ?>
                </p>
            <?php endif; ?>

            <hr>

            <?php Pjax::begin(['id' => 'boat-numbers-log']); ?>

            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h3 class="card-subtitle mb-3 text-muted">
                        <?= Html::encode(
                            Yii::t('app', 'Activity Log')
                        ) ?>
                    </h3>

                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-dark">
                            <tr>
                                <th><?= Html::encode(Yii::t('app', 'Officer Name')) ?></th>
                                <th><?= Html::encode(Yii::t('app', 'Officer Position')) ?></th>
                                <th><?= Html::encode(Yii::t('app', 'Status')) ?></th>
                                <th><?= Html::encode(Yii::t('app', 'Remarks')) ?></th>
                                <th><?= Html::encode(Yii::t('app', 'Date')) ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($approvalHistory ?? [] as $item): ?>
                                <tr>
                                    <td>
                                        <?= Html::encode(
                                            (string) (
                                                $item->doneBy?->nic
                                                ?? ''
                                            )
                                        ) ?>
                                    </td>
                                    <td>
                                        <?= Html::encode(
                                            (string) (
                                                $item->doneBy !== null
                                                    ? UserTypeUtil::getTypeNames(
                                                        $item->doneBy->type
                                                    )
                                                    : ''
                                            )
                                        ) ?>
                                    </td>
                                    <td><?= Html::encode((string) ($item->status ?? '')) ?></td>
                                    <td><?= Html::encode((string) ($item->remark ?? '')) ?></td>
                                    <td><?= Html::encode((string) ($item->date_time ?? '')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (Util::editPermission() && $showApproveBtn): ?>
                        <hr>

                        <?php $approvalForm = ActiveForm::begin([
                            'action' => [
                                '/boat-numbers/view',
                                'token' => $token,
                            ],
                            'method' => 'post',
                            'options' => [
                                'class' => 'userform',
                                'data-pjax' => 1,
                            ],
                        ]); ?>

                        <div class="row">
                            <div class="col-xl-3">
                                <div class="form-group">
                                    <?= Html::label(
                                        Yii::t('app', 'Status'),
                                        'status-approval'
                                    ) ?>

                                    <?= Html::dropDownList(
                                        'status-approval',
                                        null,
                                        [
                                            'approve' => Yii::t('app', 'Approve'),
                                            'reject' => Yii::t('app', 'Reject'),
                                        ],
                                        [
                                            'id' => 'status-approval',
                                            'class' => 'form-control',
                                            'disabled' => !$validated,
                                        ]
                                    ) ?>
                                </div>
                            </div>

                            <div class="col-xl-9">
                                <div class="form-group">
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
                            </div>

                            <div class="col-xl-12">
                                <?php if ($validated): ?>
                                    <?= Html::submitButton(
                                        Yii::t('app', 'Submit'),
                                        [
                                            'id' => 'status-approval-btn',
                                            'class' =>
                                                'btn btn-primary btn-block mt-3',
                                        ]
                                    ) ?>
                                <?php else: ?>
                                    <div class="alert alert-danger">
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
                    <h3 class="card-subtitle mb-3 text-muted">
                        <?= Html::encode(
                            Yii::t('app', 'Payments History')
                        ) ?>
                    </h3>

                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-dark">
                            <tr>
                                <th><?= Html::encode(Yii::t('app', 'Amount')) ?></th>
                                <th><?= Html::encode(Yii::t('app', 'Reference')) ?></th>
                                <th><?= Html::encode(Yii::t('app', 'Attachment')) ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($paymentHistory ?? [] as $item): ?>
                                <?php
                                $paymentFile = basename(
                                    (string) ($item->file ?? '')
                                );
                                ?>
                                <tr>
                                    <td><?= Html::encode((string) ($item->amount ?? '')) ?></td>
                                    <td><?= Html::encode((string) ($item->ref ?? '')) ?></td>
                                    <td>
                                        <?php if ($paymentFile !== ''): ?>
                                            <?= Html::a(
                                                $paymentFile,
                                                rtrim(
                                                    Constant::$FILE_VIEW_PATH,
                                                    '/'
                                                )
                                                . '/payment/'
                                                . rawurlencode($paymentFile),
                                                [
                                                    'target' => '_blank',
                                                    'rel' => 'noopener noreferrer',
                                                ]
                                            ) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h3>
                <?= Html::encode(
                    Yii::t('app', 'Previous Owners')
                ) ?>
            </h3>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th><?= Html::encode(Yii::t('app', 'Name')) ?></th>
                        <th><?= Html::encode(Yii::t('app', 'NIC')) ?></th>
                        <th><?= Html::encode(Yii::t('app', 'From')) ?></th>
                        <th><?= Html::encode(Yii::t('app', 'To')) ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($activeRecords ?? [] as $activeRecord): ?>
                        <tr>
                            <td><?= Html::encode((string) ($activeRecord['name'] ?? '')) ?></td>
                            <td><?= Html::encode((string) ($activeRecord['nic'] ?? '')) ?></td>
                            <td><?= Html::encode((string) ($activeRecord['from_date'] ?? '')) ?></td>
                            <td><?= Html::encode((string) ($activeRecord['to_date'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
