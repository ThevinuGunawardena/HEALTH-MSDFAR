<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataPurchaseDetails $model */

$this->title = Yii::t('app', 'Create Catch Data Purchase Details');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Catch Data Purchase Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catch-data-purchase-details-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
