<?php

use backend\config\Constant;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MLandingSiteSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Landing Sites');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <p>
                <?= Html::a(Yii::t('app', 'Create Landing Site'), ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?php echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
                // 'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'division_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->division0->name;
                        }
                    ],
                    'name',
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$actDeactInt[$model->status];
                        }
                    ],

                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<a href="view?id=' . $model->id . '" class="btn btn-sm btn-primary">View</a>';
                        }
                    ],
                ],
            ]); ?>


        </div>
    </div>
</div>
