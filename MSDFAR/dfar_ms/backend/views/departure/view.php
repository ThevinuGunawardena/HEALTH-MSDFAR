<?php

use backend\config\Constant;
use backend\models\User;
use backend\services\CommonService;
use backend\services\Util;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\DepartureRequests $model */
/** @var backend\models\DeparureRequestCrew[] $crews */
/** @var string $token */

$this->title = "Departure Request: " . $model->boat_no;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Departure Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <p>
                <?php if (
                    $model->approve !== 'A'
                    && CommonService::validateEditPermissionBoolean()
                ): ?>
                    <?= Html::a(
                        Yii::t('app', 'Update'),
                        [
                            '/departure/update',
                            'token' => $token,
                        ],
                        [
                            'class' => 'btn btn-primary',
                        ]
                    ) ?>
                <?php endif; ?>
            </p>

            <p>
                <?php if (
                    $model->approve === 'A'
                    && CommonService::validateEditPermissionBoolean()
                ): ?>
                    <?= Html::a(
                        Yii::t('app', 'Download PDF'),
                        [
                            '/departure/license-download',
                            'token' => $token,
                        ],
                        [
                            'class' => 'btn btn-info',
                            'target' => '_blank',
                            'rel' => 'noopener noreferrer',
                        ]
                    ) ?>
                <?php endif; ?>
            </p>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'boat_no',
                        'format' => 'text',
                        'value' => function ($model) {
                            return strtoupper($model->boat_no);
                        }
                    ],
                    'boat_name',
                    'owner',

                    'contact_no',
                    'email:email',
                    'skipper',
                    'skipper_no',
                    'skipper_nic',
                    'harbor',
//                    'fishing_area',
                    [
                        'attribute' => 'fishing_area',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$fishingArea[$model->fishing_area] ?? $model->fishing_area;
                        }
                    ],
                    'length_longline',
                    'longline_hooks',
                    'length_ringnet',
                    'mesh_gillnet',

                    'length_gillnet',
                    'mesh_ringnet',


                    'national_license_no',
                    'hs_license_no',
//                    'vms',
//                    'agree',
                    [
                        'attribute' => 'req_date_time',
//                        'format' => ['datetime', 'php:Y-m-d H:i'],
                        'value' => function ($model) {
                            return $model->req_date_time ? date('Y-m-d H:i', strtotime($model->req_date_time)) : null;
                        },
                    ],
                    [
                        'attribute' => 'action_date',
//                        'format' => ['datetime', 'php:Y-m-d H:i'],
                        'value' => function ($model) {
                            return $model->action_date ? date('Y-m-d H:i', strtotime($model->action_date)) : null;
                        },
                    ],
                    [
                        'attribute' => 'approve',

                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->approve == "A" ? "Approved" :
                                ($model->approve == "P" ? "Pending" :
                                    ($model->approve == "R" ? "Rejected" : "NA"));
                        }
                    ],
                    [
                        'attribute' => 'user',
                        'format' => 'text',
                        'value' => function ($model) {
                            $user = User::findOne($model->user);
                            return $user->nic ?? $model->user;
                        }
                    ],
                    'remarks',
                    'water_bot',
                    'mcs',
                    'frequency',
//                    'manual',
//                    'arrivalPort',
//                    'arrivalDate',
//                    'arrTime',
                ],
            ]) ?>

            <br><label>(13). Detail of Crew Members :</label>
        </div>


        <div class="col-md-12 col-sm-12">
            <div class="col-md-6 col-sm-8">
                <table class="table borderless">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>National Identity Card Number</th>
                    </tr>
                    </thead>
                    <?php foreach ($crews as $crew): ?>
                        <tr>
                            <td>
                                <?= Html::encode((string) $crew->name) ?>
                            </td>
                            <td>
                                <?= Html::encode((string) $crew->nic) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </table>
            </div>
        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <?php if (Util::editPermission() && $model->approve != "A") { ?>
                <hr>
                <?php $form = ActiveForm::begin(['options' => [
                    'class' => 'userform'
                ]]); ?>
                <div class="col-xl-12">
                    <div class="row">

                        <div class="col-xl-6">
                            <div class="form-group">

                                <?= $form->field($model, 'approve')->dropDownList(["A" => "Approve", "R" =>
                                    "Reject"], ["prompt" => "Select"]) ?>

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
</div>
