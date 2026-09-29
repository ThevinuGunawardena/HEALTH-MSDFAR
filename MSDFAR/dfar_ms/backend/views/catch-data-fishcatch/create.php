<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataFishcatch $model */

$this->title = Yii::t('app', 'Create Catch Data Fishcatch');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Catch Data Fishcatches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catch-data-fishcatch-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
