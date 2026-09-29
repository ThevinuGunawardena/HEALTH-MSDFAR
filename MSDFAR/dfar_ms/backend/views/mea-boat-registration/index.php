<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
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

            <!--            --><?php //Pjax::begin(); ?>
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
                'columns' => [

                    [
                        'attribute' => 'id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boat_number;
                        }
                    ],
                    [
                        'attribute' => 'owner',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->owner0->fisherman_uid ?? "";
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'label' => 'Status',
                        'value' => function ($model) {
                            return Constant::$licenseStatus[$model->status];
                        }
                    ],
                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $button = UserTypeUtil::hasType(Constant::MEA) ? '<a href="create?boatNumber=' .
                                $model->id . '" id="approve" class="btn btn-primary">Start inspection</a> ' : '';
                            return $button .
                                '<a href="index-mea?boat_number=' . $model->id . '" id="approve" class="btn btn-info">View History</a>';
                        }
                    ],

                ],
            ]); ?>

            <!--            --><?php //Pjax::end(); ?>

        </div>
    </div>
</div>
