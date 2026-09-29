<?php

use backend\config\Constant;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ApplicationtransportbechedemerSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Beche-De-Mer transport License');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

    <p>
        <?= Html::a(Yii::t('app', 'Apply Transport beche-de-mers License'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'full_name',
            'permanent_address',
            'mailing_address',
            'email:email',
            'telephone_number',
            [
                'attribute' => 'approval_stage',
                'format' => 'text',
                'filter' => [Constant::SPECIAL_LICENCE => "SPECIAL_LICENCE", Constant::AD => "AD",
                    Constant::DM => "DM", Constant::DG => "DG"],
                'value' => function ($model) {
                    return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'text',
                'label' => 'Status',
                'filter' => ArrayHelper::toArray(Constant::$licenseStatus),
                'value' => function ($model) {
                    return Constant::$licenseStatus[$model->status] ?? $model->status;
                }
            ],

            //'nic_number',
            //'id',
            //'business_reg_number',
            //'purchasing_district',
            //'store_district',
            //'final_destination',
            //'quantity',
            //'vehicle_number',
            //'boat_number',
            //'store_places:ntext',
            //'final_store_place',
            [
                'attribute' => 'Action',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<a href="view?id=' . $model->id . '" class="btn btn-sm btn-primary" target="_blank">View</a>';
                }
            ],
        ],
    ]); ?>


        </div>
    </div>
</div>
