<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MBoatCategory $model */

$this->title = Yii::t('app', 'Create M Boat Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Boat Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mboat-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
