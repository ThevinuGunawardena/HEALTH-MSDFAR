<?php

use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\MGearTypeExtraDataSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'M Gear Type Extra Datas');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgear-type-extra-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create M Gear Type Extra Data'), ['create'], ['class' => 'btn btn-success']) ?>
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
            'gear_type',
            'name',
            'status',
            [
            'label' => 'Action',
            'format' => 'raw',
            'value' => static function ($model): string {
                return Html::a(
                    Yii::t('app', 'View'),
                    [
                        'view',
                        'id' => (int) $model->id,
                    ],
                    [
                        'class' => 'btn btn-sm btn-primary',
                    ]
                );
            },
        ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
