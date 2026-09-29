<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoatLicense $model */

$this->title = Yii::t('app', 'Update Fisherman Registerd Boat License: {name}', [
    'name' => $model->nid,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fisherman Registerd Boat Licenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nid, 'url' => ['view', 'nid' => $model->nid]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="fisherman-registerd-boat-license-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render(UserTypeUtil::hasType(Constant::CALL_SIGN) ? '_formSpecialCallSign' : '_form', [
        'model' => $model,
    ]) ?>

</div>
