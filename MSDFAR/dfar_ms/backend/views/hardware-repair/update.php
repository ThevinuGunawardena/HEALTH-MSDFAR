<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\HardwareRepair $model */

$this->title = 'Update Hardware Repair: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Hardware Repairs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hardware-repair-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
