<?php

/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoat $model */

$this->title = Yii::t('app', 'Update Fisherman Registered Boat: {name} (SPECIAL-PERMISSION)', [
    'name' => $model->boatNumber->boat_number,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fisherman Registerd Boats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->boatNumber->boat_number ?? "", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= $this->render('_form', [
                "boatValidated" => $boatValidated,
                'model' => $model,
                'specialAlter' => $specialAlter,
                'error' => $error,
            ]) ?>

        </div>
    </div>
</div>
