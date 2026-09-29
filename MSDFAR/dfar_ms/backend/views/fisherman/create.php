<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman $model */

$renew = $renew ?? false;

$this->title = $renew
    ? Yii::t('app', 'Renew Profile Fisherman')
    : Yii::t('app', 'Create Profile Fisherman');

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Profile Fishermen'),
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
                'districtList' => $districtList ?? [],
                'renew' => $renew,
            ]) ?>

        </div>
    </div>
</div>