<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFishermanRenew $model */

$this->title = Yii::t('app', 'Create Profile Fisherman Renew');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Fisherman Renews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-fisherman-renew-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
