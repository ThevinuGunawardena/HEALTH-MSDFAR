<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\FisheriesProgressRecords $model */

$this->title = Yii::t('app', 'Create Fisheries Progress Records');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fisheries Progress Records'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fisheries-progress-records-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
