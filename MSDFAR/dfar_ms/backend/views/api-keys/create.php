<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ApiKeys $model */

$this->title = Yii::t('app', 'Create Api Keys');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Api Keys'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-keys-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
