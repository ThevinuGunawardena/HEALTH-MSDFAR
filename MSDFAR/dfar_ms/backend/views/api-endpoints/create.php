<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ApiEndpoints $model */

$this->title = Yii::t('app', 'Create Api Endpoints');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Api Endpoints'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-endpoints-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
