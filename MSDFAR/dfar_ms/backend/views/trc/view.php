<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Fishermen'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="profile-fisherman-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'first_name',
            'last_name',
            'preferred_name_for_id',
            'nic',
            'passport',
            'dob',
            'gender',
            'permanent_address',
            'current_address',
            'blood_group',
            'mobile',
            'fixed_line',
            'email:email',
            'district',
            'division',
            'landing_site',
            'year_recruitment',
            'life_isurance_no',
            'member_fisheries_society',
            'civil',
            'category',
            'management_area',
            'status',
        ],
    ]) ?>

</div>
