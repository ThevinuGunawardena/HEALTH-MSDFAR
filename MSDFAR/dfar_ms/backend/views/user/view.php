<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\User $model */

$this->title = $model->nic;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= ($model->type != Constant::FISHERMAN) ? Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : "" ?>
                <?= ($model->type != Constant::FISHERMAN) ? Html::a(Yii::t('app', 'Mark as Inactive'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to inactive this item?'),
                        'method' => 'post',
                    ],
                ]) : "" ?>
                <?= Html::a(Yii::t('app', 'Reset password'), ['password-reset', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to reset password for this user?'),
                        'method' => 'post',
                    ],
                ]) ?>
                <?= $model->profile_id != 0 ? Html::a(Yii::t('app', 'View profile'), ['officer/view', 'id' =>
                    $model->profile_id], [
                    'class' => 'btn btn-info',

                ]) : "" ?>

                <?= Html::a(Yii::t('app', 'Impersonate'), ['impersonate', 'id' =>
                    $model->id], [
                    'class' => 'btn btn-info',

                ]) ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'nic',
//            'auth_key',
//            'password_hash',
//            'password_reset_token',
                    'email:email',
                    'created_at:datetime',
                    //'updated_at',
                    //'verification_token',
//                    'profile_id',
                    [
                        'attribute' => 'type',
                        'format' => 'text',
                        'filter'=>Constant::$userTypes,

                        'value' => function ($model) {

                            return UserTypeUtil::getTypeNames($model->type) ?? $model->type;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'filter'=>array(10=>"Active",9=>"Inactive"),

                        'value' => function ($model) {

                            return $model->status==10?"Active":"Inactive";
                        }
                    ],
                ],
            ]) ?>

        </div>
    </div>
</div>
