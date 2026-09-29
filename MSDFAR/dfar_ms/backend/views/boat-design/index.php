<?php

use backend\config\Constant;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\BoatDesignSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Boat Designs');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Boat Design'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'yard',
                'format' => 'text',
                'label' => 'Yard',
                'value' => function ($model) {
                    return $model->yard0->name??"";
                }
            ],
            [
                'attribute' => 'boat_type',
                'format' => 'text',
                'value' => function ($model) {
                    return $model->boatType->code;
                }
            ],
            [
                'attribute' => 'hull_material',
                'format' => 'text',
                'label' => 'Hull material',
                'value' => function ($model) {
                    return Constant::$hullMaterials[$model->hull_material]??"";
                }
            ],
            [
                'attribute' => 'engin_type',
                'format' => 'text',
                'label' => 'Engin type',
                'value' => function ($model) {
                    return Constant::$engineTypes[$model->engin_type]??"";
                }
            ],
            [
                'attribute' => 'fi_district',
                'format' => 'text',
                'label' => 'District',
                'value' => function ($model) {
                    return $model->fiDistrict->name;
                }
            ],
            //'fi_district',
            //'design_notation',
            //'length',
            //'width',
            //'height',
            //'draft',
            //'remark',
            //'status',
            [
                'attribute' => 'Action',
                'format' => 'raw',
                'value' => function ($model) {
                    return  '<a href="view?id='.$model->id.'" class="btn btn-sm btn-primary">View</a>';
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
</div>
</div>
