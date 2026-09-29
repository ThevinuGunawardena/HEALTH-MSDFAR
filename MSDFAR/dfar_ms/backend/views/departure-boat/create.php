<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\DepartureBoats $model */

$this->title = Yii::t('app', 'Create Departure Boats');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Departure Boats'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="departure-boats-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>