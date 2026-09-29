<?php


use backend\config\Constant;
use backend\controllers\BoatTransferController;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberTransferRequestFromSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Boat Number Transfer Requests');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php echo $this->render('../common/stac',["counts" => BoatTransferController::getStacs()]); ?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

<div class="card shadow-sm mb-5">
        <div class="card-body">
        <div class="col-lg-4 offset-lg-8">
    <?= Html::a(Yii::t('app', 'Create Boat Number Transfer Request'), ['create'], ['class' => 'btn btn-success']) ?>
    </div>

  

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'boat_number',
                'format' => 'text',
                'value' => function ($model) {
                    return $model->boatNumber->boat_number;
                }
            ],
            [
                'attribute' => 'new_owner',
                'format' => 'text',
                'value' => function ($model) {
                    return $model->newOwner->fisherman_uid ." - ".$model->newOwner->first_name . " " . $model->newOwner->last_name;
                }
            ],
            'witness_name',
            [
                'attribute' => 'status',
                'format' => 'text',
                'value' => function ($model) {
                    return Constant::$licenseStatus[$model->status]??"Error loading status";
                }
            ],
            [
                'attribute' => 'approval_stage',
                'format' => 'text',
                'value' => function ($model) {
                    return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
                }
            ],
            'created',
            'approved_time',
            //'witness_name',
            //'witness_address',
            //'witness_nic',
            //'witness_sign_date',
            //'remark',
            //'status',
            //'approval_stage',
            //'created',
            //'approved_time',
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
</div>
