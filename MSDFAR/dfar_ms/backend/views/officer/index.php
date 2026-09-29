<?php

use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\ProfileOfficerSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Profile Officers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-officer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Profile Officer'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
<div class="row">
    <div class="col-lg-12 ">
        <?= GridView::widget([
            'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
            'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'first_name',
                'last_name',
                [
                    'attribute' => 'district',
                    'format' => 'text',
                    'value' => function ($model) {
                        return $model->district0->name?? "";
                    }
                ],
                [
                    'attribute' => 'division',
                    'format' => 'text',
                    'value' => function ($model) {
                        return $model->division0->name??"";
                    }
                ],
                'status',
                [
                    'attribute' => 'Action',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return  '<a href="view?id='.$model->id.'" class="btn btn-sm btn-primary">View</a>';
                    }
                ],
            ],
        ]); ?>
    </div>
</div>


    <?php Pjax::end(); ?>

</div>
