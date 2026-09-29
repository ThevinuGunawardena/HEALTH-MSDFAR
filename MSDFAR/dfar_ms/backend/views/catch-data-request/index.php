<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\BoatNumbers;
use backend\models\MHarbours;
use backend\models\MMainGearTypes;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataRequestSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Export Sales Record');
$this->params['breadcrumbs'][] = $this->title;
$userType = Yii::$app->user->identity->type;

?>

<div class="catch-data-request-index container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- <h1 class="mb-0"><?= Html::encode($this->title) ?></h1> -->

       <?php if ($userType != 226): ?>

<?= Html::a(
    Yii::t('app', 'Create Export Sales Record'),
    ['create'],
    ['class' => 'btn btn-success']
) ?>

<?php endif; ?>
    </div>

    <!-- SEARCH -->
    <div class="card mb-3">
        <div class="card-body">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>

    <!-- GRID -->
    <?php Pjax::begin(); ?>

    <div class="card">
        <div class="card-body p-0">

            <?= GridView::widget([
                'dataProvider' => $dataProvider,

                'layout' => "{items}\n<div class='p-2'>{summary}{pager}</div>",

                'tableOptions' => [
                    'class' => 'table table-striped table-bordered mb-0',
                    'style' => 'width:100%',
                ],

                'columns' => [

                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'label' => 'Boat Number',
                        'contentOptions' => ['style' => 'vertical-align:middle;'],
                        'value' => function ($model) {
                            $boat = BoatNumbers::findOne($model->boat_registration_id);
                            return $boat ? $boat->boat_number : 'N/A';
                        },
                    ],

                    [
                        'label' => 'Unloading Harbour',
                        'contentOptions' => ['style' => 'vertical-align:middle;'],
                        'value' => function ($model) {
                            $harbour = MHarbours::findOne($model->unloading_harbour);
                            return $harbour ? $harbour->Name : 'N/A';
                        }
                    ],

                    [
                        'label' => 'Main Gear Type',
                        'contentOptions' => ['style' => 'vertical-align:middle;'],
                        'value' => function ($model) {
                            $gear = MMainGearTypes::findOne($model->fishing_gear_type);
                            return $gear ? $gear->description : 'N/A';
                        }
                    ],

                    [
                        'attribute' => 'landing_date',
                        'contentOptions' => ['style' => 'vertical-align:middle;'],
                    ],

                    [
                        'attribute' => 'log_book_no',
                        'contentOptions' => ['style' => 'vertical-align:middle;'],
                    ],

                    [
                        'label' => 'Action',
                        'format' => 'raw',
                        'contentOptions' => [
                            'style' => 'text-align:center; width:120px; vertical-align:middle;'
                        ],
                        'value' => function ($model) {
                            return Html::a(
                                'View',
                                ['catch-data-request/view', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-sm btn-primary',
                                    'data-pjax' => '0',
                                ]
                            );
                        }
                    ],
                ],
            ]); ?>

        </div>
    </div>

    <?php Pjax::end(); ?>

</div>