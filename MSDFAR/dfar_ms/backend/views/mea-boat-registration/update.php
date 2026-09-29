<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MeaBoatRegistration $model */

$this->title = Yii::t('app', 'Update Mea Boat Registration: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Mea Boat Registrations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="mea-boat-registration-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $renew ?
        $this->render('_form_renew', [
            'model' => $model,
            'boat' => $boat,
        ]) :
        $this->render('_form', [
            'model' => $model,
            'boat' => $boat,
        ]) ?>

</div>
