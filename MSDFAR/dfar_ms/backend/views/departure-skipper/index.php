<?php

use backend\components\SecurityHelper;
use backend\models\DepartureSkipper;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\DepartureSkipperSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Departure Skippers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <p>
                <?= Html::a(
                    Yii::t('app', 'Add new Skipper/Crew member'),
                    ['create'],
                    ['class' => 'btn btn-success']
                ) ?>
            </p>

            <?= $this->render('_search', [
                'model' => $searchModel,
            ]) ?>

            <?= GridView::widget([
                'pager' => [
                    'class' => LinkPager::class,
                    'firstPageLabel' => 'First',
                    'lastPageLabel' => 'Last',
                ],
                'dataProvider' => $dataProvider,
                'columns' => [
                    'skipper_name',
                    'crew_type',
                    'nic',
                    'skipper_id',
                    'status',
                    'offence_reason',
                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => static function ($model) {
                            $token = SecurityHelper::encryptId(
                                DepartureSkipper::class,
                                (int) $model->id
                            );

                            return Html::a(
                                'View',
                                [
                                    'view',
                                    'token' => $token,
                                ],
                                [
                                    'class' => 'btn btn-sm btn-primary',
                                ]
                            );
                        },
                    ],
                ],
            ]) ?>

        </div>
    </div>
</div>
