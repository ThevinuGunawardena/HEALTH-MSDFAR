<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ResourceProfile $model */

$this->title = 'Resource Profile';
$this->params['breadcrumbs'][] = ['label' => 'Profile Officers', 'url' => ['profile-officers/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="resource-profile-index">
    <div class="col-12">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
