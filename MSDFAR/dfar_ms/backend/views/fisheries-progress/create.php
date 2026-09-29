<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\FisheriesProgress $model */

$this->title = Yii::t('app', 'Create Fisheries Progress');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fisheries Progresses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fisheries-progress-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
