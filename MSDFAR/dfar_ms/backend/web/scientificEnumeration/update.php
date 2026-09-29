<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ScientificEnumerationRequest $model */

$this->title = Yii::t('app', 'Update Scientific Enumeration Request: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Scientific Enumeration Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="scientific-enumeration-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
