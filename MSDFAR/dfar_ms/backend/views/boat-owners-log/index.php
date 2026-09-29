<?php

use backend\models\BoatNumberOwnersLog;
use yii\bootstrap4\LinkPager;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberOwnersLogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Boat Number Owners Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="boat-number-owners-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Boat Number Owners Log'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'nic',
            'from_date',
            'to_date',
            //'status',
            //'added_by',
            //'created_time',
            //'boat_number',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, BoatNumberOwnersLog $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
