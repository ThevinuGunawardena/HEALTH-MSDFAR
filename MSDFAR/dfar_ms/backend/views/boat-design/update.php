<?php

/** @var yii\web\View $this */
/** @var backend\models\BoatDesign $model */

$this->title = Yii::t('app', 'Update Boat Design: {name}', [
    'name' => $model->design_notation ?? "",
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Boat Designs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->design_notation ?? "", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="boat-design-update">


    <?= $this->render('_form', [
        'model' => $model,
        'yardsList' => $yardsList,
        'boatCategories' => $boatCategories,

    ]) ?>

</div>
