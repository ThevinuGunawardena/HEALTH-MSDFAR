<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumberCancelRequests;
use backend\services\Util;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberCancelRequests $model */
/** @var array $approvalHistory */
/** @var bool $showRejectBtn */
/** @var bool $showApproveBtn */
/** @var string $process */
/** @var array $files */
/** @var string $token */

$boatNumberText = trim(
    (string) ($model->boatNumber->boat_number ?? '')
);

$this->title = Yii::t(
    'app',
    'Boat Cancellation Request: {boatNumber}',
    [
        'boatNumber' => $boatNumberText,
    ]
);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t(
        'app',
        'Boat Cancellation Requests'
    ),
    'url' => ['/boat-cancel/index'],
];
$this->params['breadcrumbs'][] = $this->title;

yii\web\YiiAsset::register($this);

$requestToken = SecurityHelper::encryptId(
    BoatNumberCancelRequests::class,
    (int) $model->id
);
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?php if (Util::editPermission()): ?>
                    <?= Html::a(
                        Yii::t('app', 'Update'),
                        [
                            '/boat-cancel/update',
                            'token' => $requestToken,
                        ],
                        [
                            'class' => 'btn btn-primary',
                        ]
                    ) ?>

                    <?= Html::a(
                        Yii::t(
                            'app',
                            'Upload Required Files'
                        ),
                        [
                            '/file/upload',
                            'token' => $requestToken,
                            'process' => $process,
                        ],
                        [
                            'class' =>
                                'btn btn-outline-primary',
                        ]
                    ) ?>
                <?php endif; ?>

                <?php if (
                    UserTypeUtil::hasType(Constant::ADMIN)
                    && (int) $model->status
                        === (int) Constant::Pending
                ): ?>
                    <?= Html::a(
                        Yii::t('app', 'Delete'),
                        [
                            '/boat-cancel/delete',
                            'token' => $requestToken,
                        ],
                        [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => Yii::t(
                                    'app',
                                    'Are you sure you want to delete this request?'
                                ),
                                'method' => 'post',
                            ],
                        ]
                    ) ?>
                <?php endif; ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'boat_number_id',
                        'format' => 'text',
                        'label' => Yii::t(
                            'app',
                            'Boat Number'
                        ),
                        'value' => static function (
                            BoatNumberCancelRequests $model
                        ): string {
                            return (string) (
                                $model->boatNumber->boat_number
                                ?? ''
                            );
                        },
                    ],
                    [
                        'attribute' => 'repairable',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumberCancelRequests $model
                        ): string {
                            return (string) (
                                Constant::$yesNo[
                                    $model->repairable
                                ]
                                ?? Yii::t('app', 'Unknown')
                            );
                        },
                    ],
                    [
                        'attribute' =>
                            'parts_available_for_inspection',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumberCancelRequests $model
                        ): string {
                            return (string) (
                                Constant::$yesNo[
                                    $model
                                        ->parts_available_for_inspection
                                ]
                                ?? Yii::t('app', 'Unknown')
                            );
                        },
                    ],
                    [
                        'attribute' => 'proposed_dispose',
                        'format' => 'ntext',
                    ],
                    [
                        'attribute' =>
                            'address_of_part_inspection',
                        'format' => 'ntext',
                    ],
                    [
                        'attribute' =>
                            'present_condition_of_boat',
                        'format' => 'ntext',
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumberCancelRequests $model
                        ): string {
                            return (string) (
                                Constant::$licenseStatus[
                                    $model->status
                                ]
                                ?? Yii::t('app', 'Unknown')
                            );
                        },
                    ],
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'value' => static function (
                            BoatNumberCancelRequests $model
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
                ],
            ]) ?>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3 class="card-subtitle mb-3 text-muted">
                <?= Html::encode(
                    Yii::t('app', 'Uploaded Files')
                ) ?>
            </h3>

            <?php if (empty($files)): ?>
                <p class="text-muted">
                    <?= Html::encode(
                        Yii::t(
                            'app',
                            'No files have been uploaded.'
                        )
                    ) ?>
                </p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($files as $file): ?>
                        <?php
                        $fileName = basename(
                            (string) $file->file_name
                        );

                        $fileDescription = (string) (
                            $file->fileType->discription
                            ?? Yii::t('app', 'Document')
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
                                    $fileDescription
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
            <?php endif; ?>
        </div>
    </div>

    <?php Pjax::begin([
        'id' => 'boat-cancel-approval-log',
    ]); ?>

    <div class="card mb-5 shadow-sm">
        <div class="card-body">
            <h3 class="card-subtitle mb-3 text-muted">
                <?= Html::encode(
                    Yii::t('app', 'Activity Log')
                ) ?>
            </h3>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
                    <tr>
                        <th scope="col">
                            <?= Html::encode(
                                Yii::t('app', 'Officer Name')
                            ) ?>
                        </th>
                        <th scope="col">
                            <?= Html::encode(
                                Yii::t('app', 'Officer Position')
                            ) ?>
                        </th>
                        <th scope="col">
                            <?= Html::encode(
                                Yii::t('app', 'Status')
                            ) ?>
                        </th>
                        <th scope="col">
                            <?= Html::encode(
                                Yii::t('app', 'Remarks')
                            ) ?>
                        </th>
                        <th scope="col">
                            <?= Html::encode(
                                Yii::t('app', 'Date')
                            ) ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach (
                        $approvalHistory as $item
                    ): ?>
                        <tr>
                            <td>
                                <?= Html::encode(
                                    (string) (
                                        $item->doneBy->nic
                                        ?? ''
                                    )
                                ) ?>
                            </td>
                            <td>
                                <?= Html::encode(
                                    (string) (
                                        $item->doneBy->type
                                        ?? ''
                                    )
                                ) ?>
                            </td>
                            <td>
                                <?= Html::encode(
                                    (string) (
                                        $item->status
                                        ?? ''
                                    )
                                ) ?>
                            </td>
                            <td>
                                <?= nl2br(
                                    Html::encode(
                                        (string) (
                                            $item->remark
                                            ?? ''
                                        )
                                    )
                                ) ?>
                            </td>
                            <td>
                                <?= Html::encode(
                                    (string) (
                                        $item->date_time
                                        ?? ''
                                    )
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($approvalHistory)): ?>
                        <tr>
                            <td
                                colspan="5"
                                class="text-center text-muted"
                            >
                                <?= Html::encode(
                                    Yii::t(
                                        'app',
                                        'No activity has been recorded.'
                                    )
                                ) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (
                $showApproveBtn
                && Util::editPermission()
            ): ?>
                <hr>

                <?php $form = ActiveForm::begin([
                    'action' => [
                        '/boat-cancel/view',
                        'token' => $requestToken,
                    ],
                    'method' => 'post',
                    'options' => [
                        'class' => 'userform',
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
                                    'id' =>
                                        'remarks-approval',
                                    'class' => 'form-control',
                                    'maxlength' => 1000,
                                    'rows' => 4,
                                ]
                            ) ?>
                        </div>
                    </div>

                    <div class="col-xl-12">
                        <?= Html::submitButton(
                            Yii::t('app', 'Submit'),
                            [
                                'id' =>
                                    'status-approval-btn',
                                'class' =>
                                    'btn btn-primary btn-block mt-3',
                            ]
                        ) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
    </div>

    <?php Pjax::end(); ?>
</div>
