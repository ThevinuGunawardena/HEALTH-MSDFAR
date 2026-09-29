<?php

use backend\services\Util;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\DepartureSkipper $model */
/** @var yii\data\ActiveDataProvider $activity */
/** @var string $token */

$this->title = $model->nic;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Departure Skippers'),
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= Util::adminPermission()
                    ? Html::a(
                        Yii::t('app', 'Update'),
                        [
                            'update',
                            'token' => $token,
                        ],
                        [
                            'class' => 'btn btn-primary',
                        ]
                    )
                    : '' ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'skipper_name',
                    'crew_type',
                    'nic',
                    'skipper_id',
                    'address',
                    'contact',
                    'status',
                    'approved_by',
                    'timestamp',
                    'harbor',
                    'served_vessel',
                    'dep_date',
                    'dep_id',
                    'dep_cancel_allow_by',
                    'dep_cancel_date',
                    'to_date:ntext',
                    'remarks',
                    'offence_reason',
                ],
            ]) ?>

        </div>
    </div>
</div>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <h1>History</h1>
                </div>
                <div class="col-lg-12">
                    <?= GridView::widget([
                        'dataProvider' => $activity,
                        'pager' => [
                            'class' => LinkPager::class,
                            'firstPageLabel' => 'First',
                            'lastPageLabel' => 'Last',
                        ],
                        'columns' => [
                            'boat_no',
                            'activity',
                            'description',
                            'date_time',
                            'to_date',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
