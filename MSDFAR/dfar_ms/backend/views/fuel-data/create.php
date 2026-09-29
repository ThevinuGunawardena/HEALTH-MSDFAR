<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\FuelData $model */

$this->title = Yii::t('app', 'Create Fuel Data');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fuel Datas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fuel-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
         'boatList' => $boatList,
        'boatJson' => $boatJson,
    ]) ?>

</div>
