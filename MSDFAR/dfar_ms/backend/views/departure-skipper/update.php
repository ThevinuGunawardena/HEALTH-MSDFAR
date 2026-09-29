<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\DepartureSkipper|app\models\DepartureSkipperC $model */
/** @var string $token */
/** @var string $formAction */

$this->title = Yii::t(
    'app',
    'Update Departure Skipper: {name}',
    [
        'name' => $model->nic,
    ]
);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Departure Skippers'),
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = [
    'label' => $model->nic,
    'url' => [
        'view',
        'token' => $token,
    ],
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= $this->render('_form', [
                'model' => $model,
                'token' => $token,
                'formAction' => $formAction,
            ]) ?>

        </div>
    </div>
</div>
