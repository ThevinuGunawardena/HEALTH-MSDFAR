<?php

use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFishermanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Profile Fishermen');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-fisherman-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Profile Fisherman'), ['create'], ['class' => 'btn btn-success']) ?>
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
            'first_name',
            'last_name',
            'preferred_name_for_id',
            'nic',
            //'passport',
            //'dob',
            //'gender',
            //'permanent_address',
            //'current_address',
            //'blood_group',
            //'mobile',
            //'fixed_line',
            //'email:email',
            //'district',
            //'division',
            //'landing_site',
            //'year_recruitment',
            //'life_isurance_no',
            //'member_fisheries_society',
            //'civil',
            //'category',
            //'management_area',
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
