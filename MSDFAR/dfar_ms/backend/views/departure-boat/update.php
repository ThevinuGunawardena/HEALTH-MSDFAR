<?php

/** @var yii\web\View $this */
/** @var backend\models\DepartureBoats $model */
/** @var string $token */

$this->title = Yii::t('app', 'Update Boat: {name}', [
    'name' => $model->boat->boat_number,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Departure Boats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->boat->boat_number, 'url' => ['view', 'token' => $token]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= $this->render('_form', [
                'model' => $model,
                'token' => $token,
            ]) ?>

        </div>
    </div>
</div>