<?php

/** @var yii\web\View $this */
/** @var backend\models\User $model */

$this->title = Yii::t('app', 'Update User: {name}', [
    'name' => $model->nic,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nic, 'url' => ['view', 'id' => $model->nic]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

    <?= $this->render('_form', [
        'model' => $model,
        'update' => true,
    ]) ?>

</div>
</div>
</div>
