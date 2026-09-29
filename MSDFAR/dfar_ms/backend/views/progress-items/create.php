<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ProgressItems $model */

$this->title = Yii::t('app', 'Create Progress Items');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Progress Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="progress-items-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
