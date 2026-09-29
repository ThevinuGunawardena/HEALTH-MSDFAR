<?php

use backend\config\Constant;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\MeaBoatRegistrationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Mea Boat Registrations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <!--        --><?php //= Html::a(Yii::t('app', 'Create Mea Boat Registration'), ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <!--    --><?php //Pjax::begin(); ?>
            <?php echo $this->render('_search2', ['model' => $searchModel]); ?>
            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
                'columns' => [

                    'mea_certificate_number',
                    'inspection_date',
                    'next_inspected_date',
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$licenseStatus[$model->status] ?? "";
                        }
                    ],
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
                        }
                    ],
                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<a href="view?id=' . $model->id . '" id="approve" class="btn btn-sm btn-primary">View</a>';
                        }
                    ],

                ],
            ]); ?>

            <!--    --><?php //Pjax::end(); ?>

        </div>
    </div>
</div>
