<?php

use backend\models\MFishTypes;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\DistrictGearTypes $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'District Gear Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'gear_type',
                'format' => 'text',
                'value' => function ($model) {
                    return $model->gearType->description ;
                }
            ],
            'fishing_time_periods',
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
                        $times[]=\backend\config\Constant::$FishingDuration[$time];
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
        ],
    ]) ?>

</div>
</div>
</div>
