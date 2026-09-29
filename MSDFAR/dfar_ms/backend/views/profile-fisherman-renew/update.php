<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFishermanRenew $model */

$this->title = Yii::t('app', 'Update Profile Fisherman Renew: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Fisherman Renews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="profile-fisherman-renew-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
