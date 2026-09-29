<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Exportregistration $model */

$this->title = Yii::t('app', 'Export Registration Application');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Exportregistrations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exportregistration-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
