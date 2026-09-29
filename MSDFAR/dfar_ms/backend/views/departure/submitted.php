<?php

use backend\services\Util;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\DepartureRequests $model */
/** @var backend\models\DeparureRequestCrew[] $crews */
/** @var string $token */

$this->title = "Departure Request: " . $model->boat_no;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Departure Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">


            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'boat_no',
                    'boat_name',
                    'owner',
                    'contact_no',
                    'email:email',
                    'skipper',
                    'skipper_no',
                    'skipper_nic',
                    'district',
                    'harbor',
                    'fishing_area',
                    'length_longline',
                    'length_gillnet',
                    'length_ringnet',
                    'longline_hooks',
                    'mesh_gillnet',
                    'mesh_ringnet',

                    'national_license_no',
                    'hs_license_no',
                    'vms',
//                    'agree',
                    'req_date_time',
                    'user',
                    'action_date',
                    'approve',
//                    'remarks',
//                    'water_bot',
                    'mcs',
//                    'frequency',
                    'vms_code',
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
