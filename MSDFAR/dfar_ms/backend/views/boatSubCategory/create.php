<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MBoatSubCategory $model */

$this->title = Yii::t('app', 'Create M Boat Sub Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Boat Sub Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mboat-sub-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
