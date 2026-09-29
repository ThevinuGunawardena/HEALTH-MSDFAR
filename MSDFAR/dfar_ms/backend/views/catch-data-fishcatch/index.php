<?php

use backend\models\CatchDataFishcatch;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var backend\models\CatchDataFishcatchSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Catch Data Fishcatches');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catch-data-fishcatch-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Catch Data Fishcatch'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'fish_type',
            'num_of_fish_log',
            'weight_of_fish_log',
            'num_of_fish_act',
            //'weight_of_fish_act',
            //'catchdata_req_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, CatchDataFishcatch $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
