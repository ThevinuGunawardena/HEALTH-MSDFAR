<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberCancelRequests $model */
/** @var backend\models\BoatNumbers $boatNumber */
/** @var string $boatToken */

$boatNumberText = trim(
    (string) ($boatNumber->boat_number ?? '')
);

$this->title = Yii::t(
    'app',
    'Create Boat Cancellation Request'
);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t(
        'app',
        'Boat Cancellation Requests'
    ),
    'url' => ['/boat-cancel/index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="boat-number-cancel-requests-create">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <div class="alert alert-info">
                <strong>
                    <?= Html::encode(
                        Yii::t('app', 'Boat Number')
                    ) ?>:
                </strong>
                <?= Html::encode($boatNumberText) ?>
            </div>

            <?= $this->render(
                '_form',
                [
                    'model' => $model,
                    'formAction' => [
                        '/boat-cancel/create',
                        'token' => $boatToken,
                    ],
                    'cancelUrl' => [
                        '/boat-numbers/view',
                        'token' => $boatToken,
                    ],
                ]
            ) ?>
        </div>
    </div>
</div>
