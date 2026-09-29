<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\HardwareRepair $model */

$this->title = 'Create Hardware Repair';
$this->params['breadcrumbs'][] = ['label' => 'Hardware Repairs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hardware-repair-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
