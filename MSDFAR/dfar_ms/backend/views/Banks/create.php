<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Banks $model */

$this->title = Yii::t('app', 'Create Banks');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banks-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
