<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ApiKeyPermissions $model */

$this->title = Yii::t('app', 'Create Api Key Permissions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Api Key Permissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-key-permissions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
