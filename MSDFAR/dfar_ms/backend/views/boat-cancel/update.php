<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberCancelRequests $model */
/** @var string $token */

$boatNumberText = trim(
    (string) ($model->boatNumber->boat_number ?? '')
);

$this->title = Yii::t(
    'app',
    'Update Boat Cancellation Request: {boatNumber}',
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
$this->params['breadcrumbs'][] = [
    'label' => $boatNumberText,
    'url' => [
        '/boat-cancel/view',
        'token' => $token,
    ],
];
$this->params['breadcrumbs'][] = Yii::t(
    'app',
    'Update'
);
?>

<div class="boat-number-cancel-requests-update">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render(
                '_form',
                [
                    'model' => $model,
                    'formAction' => [
                        '/boat-cancel/update',
                        'token' => $token,
                    ],
                    'cancelUrl' => [
                        '/boat-cancel/view',
                        'token' => $token,
                    ],
                ]
            ) ?>
        </div>
    </div>
</div>
