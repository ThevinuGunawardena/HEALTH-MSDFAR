<?php

/** @var yii\web\View $this */

/** @var backend\models\FishermanRegisterdBoat $model */

use backend\config\Constant;
use backend\config\UserTypeUtil;

$this->title = Yii::t('app', 'Update Fisherman Registered Boat: {name}', [
    'name' => $model->boatNumber->boat_number,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fisherman Registerd Boats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= $this->render(UserTypeUtil::hasType(Constant::CALL_SIGN) ? '_formSpecialCallSign' : '_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>
