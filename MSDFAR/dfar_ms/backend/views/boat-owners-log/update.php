<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberOwnersLog $model */

$this->title = Yii::t('app', 'Update Boat Number Owners Log: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Boat Number Owners Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="boat-number-owners-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
