<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Promotion $model */

$this->title = Yii::t('app', 'Create Promotion');
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Promotions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'Profile Officers', 'url' => ['profile-officer/index']];
$this->params['breadcrumbs'][] = ['label' => 'Promotions', 'url' => ['index', 'profile_officer_id' => $model->profile_officer_id]];
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>
