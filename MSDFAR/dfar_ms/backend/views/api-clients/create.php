<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ApiClients $model */

$this->title = Yii::t('app', 'Create Api Clients');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Api Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-clients-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
