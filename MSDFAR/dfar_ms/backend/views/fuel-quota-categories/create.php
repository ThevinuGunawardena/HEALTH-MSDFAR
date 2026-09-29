<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\FuelQuotaCategories $model */

$this->title = Yii::t('app', 'Create Fuel Quota Categories');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fuel Quota Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fuel-quota-categories-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
