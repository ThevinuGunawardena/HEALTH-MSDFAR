<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataFishcatch $model */

$this->title = Yii::t('app', 'Update Catch Data Fishcatch: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Catch Data Fishcatches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="catch-data-fishcatch-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
