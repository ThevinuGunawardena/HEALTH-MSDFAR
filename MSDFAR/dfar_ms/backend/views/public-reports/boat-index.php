<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\DepartureBoatsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Departure Cancelled Boat list');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1 style="text-align: center;"><?= Html::encode($this->title) ?></h1>

            <p>
                <!--                --><?php //= Html::a(Yii::t('app', 'Create Departure Boats'), ['create'], ['class' => 'btn btn-success']) ?>
                <?= $this->render('_search', ['model' => $searchModel]) ?>

            </p>


            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
//                'filterModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'columns' => [
//                    ['class' => 'yii\grid\SerialColumn'],

//                    'id',
                    [
                        'attribute' => 'boat_number_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boat->boat_number;
                        }
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisherman->preferred_name_for_id;
                        }
                    ],

                    'status',
//                    'offence',
                    'dep_cancel_date',
                    'to_date',


                ],
            ]); ?>


        </div>
    </div>
</div>
