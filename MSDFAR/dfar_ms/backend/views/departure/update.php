<?php

/** @var yii\web\View $this */
/** @var backend\models\DepartureRequests $model */
/** @var backend\models\DeparureRequestCrew[] $crews */
/** @var string $token */

$this->title = Yii::t('app', 'Update Departure Requests: {name}', [
    'name' => strtoupper((string) $model->boat_no),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Departure Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => strtoupper((string) $model->boat_no),
    'url' => [
        '/departure/view',
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
                'crews' => $crews,
                'readOnly' => true,
                'update' => true
            ]) ?>

        </div>
    </div>
</div>