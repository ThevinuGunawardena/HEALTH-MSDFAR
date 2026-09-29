
<?php


use backend\models\FuelData;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\LinkPager;


/** @var yii\web\View $this */
/** @var backend\models\HighseasLicenseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Fuel Datas');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <p>
        <?= Html::a(Yii::t('app', 'Create Fuel Data Record'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="card shadow-sm mb-5">
        <div class="card-body">


            <!--            --><?php //Pjax::begin(); ?>

            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
                // 'filterModel' => $searchModel,
                'columns' => [

                     'id',
            // 'boat_registration_id',
            [
                        'attribute' => 'boat_registration_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boatRegistration->boatNumber->boat_number;
                        }
                    ],
            'bank_code',
            'bank_branch',
            'account_number',
                    //'approval_stage',
                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<a href="view?id=' . $model->id . '" class="btn btn-sm btn-primary">View</a>';
                        }
                    ],
                ],
            ]); ?>

            <!--            --><?php //Pjax::end(); ?>


        </div>
    </div>
</div>
