<?php


use backend\config\Constant;
use backend\models\MFishTypes;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\DistrictGearTypesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'District Gear Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="col-lg-4 offset-lg-8">
                <?= Html::a(Yii::t('app', 'Create District Gear Types'), ['create'], ['class' => 'btn btn-success']) ?>
            </div>


            <!--    --><?php //Pjax::begin(); ?>
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
                // 'filterModel' => $searchModel,
                'columns' => [

                    'name',
                    [
                        'attribute' => 'gear_type',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->gearType->description;
                }
            ],
            [
                'attribute' => 'extra',
                'format' => 'text',
                'value' => function ($model) {
                    return str_replace("\""," ",str_replace("{"," ",str_replace("}"," ",$model->extra)));
                }
            ],[
                'attribute' => 'fishing_time_durations',
                'format' => 'text',
                'value' => function ($model) {
                    $times=[];

                    foreach (explode(",",$model->fishing_time_durations) as $time) {
                        $times[]= Constant::$FishingDuration[$time];
                    }
                    return implode(", ",$times);
                }
            ],
            [
                'attribute' => 'fish_species',
                'format' => 'text',
                'value' => function ($model) {
                    $fishTypes =  MFishTypes::find()->select("name")->where(["IN","id",explode(",",$model->fish_species)])->asArray()->all();
                    $fish=[];
                    foreach ($fishTypes as $fishType) {
                        $fish[]=$fishType["name"];
                    }
                    return implode(", ",$fish);
                }
            ],
            //'fish_species',
            //'status',
            //'district_id',
                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<a href="view?id=' . $model->id . '" class="btn btn-sm btn-primary">View</a>';
                        }
                    ],
                ],
            ]); ?>

            <!--    --><?php //Pjax::end(); ?>
        </div>
    </div>
</div>
