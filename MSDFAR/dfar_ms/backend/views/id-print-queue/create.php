<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\IdPrintQueue $model */

$this->title = Yii::t('app', 'Create Id Print Queue');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Id Print Queues'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="id-print-queue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
