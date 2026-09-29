<?php

use yii\bootstrap4\LinkPager;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Profile Officers';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <!--    <h1>--><?php //= Html::encode($this->title) ?><!--</h1>-->

            <p>
                <?= Html::a('Create Profile Officer', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],

                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'first_name',
                    'last_name',
                    'nic',
                    'current_designation',
//            'current_workplace',
//            'district_office',
//            'user_level',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('View', ['view', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']);
                            },

                        ],
                    ],
                ],
            ]); ?>

        </div>
    </div>
</div>