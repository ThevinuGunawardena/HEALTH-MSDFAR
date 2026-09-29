<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Applicationtransportbechedemer $model */

$this->title = Yii::t('app', 'Update Transport beche-de-mers License: {name}', [
    'name' => $model->full_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transport beche-de-mers License'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->full_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

    <?= $this->render('_form', [
        'model' => $model,
        'storePlaces' => $storePlaces,

    ]) ?>

        </div>
    </div>
</div>
