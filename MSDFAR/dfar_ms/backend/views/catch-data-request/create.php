<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataRequest $model */

$this->title = Yii::t('app', 'Create Catch Data Request');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Catch Data Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catch-data-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
