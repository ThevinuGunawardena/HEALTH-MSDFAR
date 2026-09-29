<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataPurchaseDetails $model */

$this->title = Yii::t('app', 'Update Catch Data Purchase Details: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Catch Data Purchase Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="catch-data-purchase-details-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
