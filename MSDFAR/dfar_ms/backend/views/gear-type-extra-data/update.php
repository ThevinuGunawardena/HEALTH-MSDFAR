<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MGearTypeExtraData $model */

$this->title = Yii::t('app', 'Update M Gear Type Extra Data: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Gear Type Extra Datas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="mgear-type-extra-data-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
