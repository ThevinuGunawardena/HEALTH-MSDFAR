<?php

use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\ProfileYardSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Profile Yards');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-yard-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Profile Yard'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [

            'name',
            [
                'attribute' => 'owner',
                'format' => 'text',
                'value' => function ($model) {
                    return $model->owner0->first_name;
                }
            ],
            'address',
            'mobile_number',
            //'land_line',
            //'email:email',
            //'web',
            //'fax',
            //'business_reg_no',
            //'business_reg_date',
            //'land_owner',
            //'deed_number',
            //'ownership_get_date',
            //'land_area',
            //'land_area_under_roof',
            //'remark',
            //'admin_district',
            //'fisheries_district',
            //'division',
            //'gps_latitude',
            //'gps_longitude',
            //'transpotation_method',
            //'distance_rural_hospital',
            //'distance_district_hospital',
            //'distance_base_hospital',
            //'distance_teaching_hospital',
            //'distance_genaral_hospital',
            //'distance_fire_brigade',
            //'distance_police_station',
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
