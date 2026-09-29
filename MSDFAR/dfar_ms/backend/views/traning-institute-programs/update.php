<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MTraningInstitutePrograms $model */

$this->title = Yii::t('app', 'Update M Traning Institute Programs: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'M Traning Institute Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="mtraning-institute-programs-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
