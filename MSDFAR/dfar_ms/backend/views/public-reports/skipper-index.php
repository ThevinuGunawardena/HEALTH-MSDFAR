<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\DepartureBoatsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Departure Cancelled Skippers/Crew members list');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

            <p>
                <!--                --><?php //= Html::a(Yii::t('app', 'Create Departure Boats'), ['create'], ['class' => 'btn btn-success']) ?>
                <?= $this->render('_searchSkipper', ['model' => $searchModel]) ?>

            </p>


            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],

                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,

                'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'id',
                    'skipper_name',
                    'crew_type',
                    'nic',
                    'skipper_id',
                    //'address',
                    //'contact',
                    'status',
//                    'offence_reason',
                    //'approved_by',
                    //'timestamp',
                    //'harbor',
                    //'served_vessel',
                    //'dep_date',
                    //'dep_id',
                    //'dep_cancel_allow_by',
                    'dep_cancel_date',
                    'to_date',
//                    'remarks',


                ],
            ]); ?>


        </div>
    </div>
</div>
