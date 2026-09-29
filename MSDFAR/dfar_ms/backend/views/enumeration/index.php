<?php

use backend\models\ProfileOfficer;
use common\models\User;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\ScientificEnumerationRequestSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Scientific Enumeration Requests');
$this->params['breadcrumbs'][] = $this->title;

$exportMenu = [

    [
        'attribute' => 'boat_number_id',
        'format' => 'text',
        'value' => function ($model) {
            return $model->boatNumber->boat_number;
        }
    ],
    [
        'attribute' => 'fisherman_id',
        'format' => 'text',
        'value' => function ($model) {
            return $model->fisherman->fisherman_uid;
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
    'mea_report',
    'created',
    'approved_time',
    'expire_date'
];
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>


            <?php Pjax::begin(); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $exportMenu,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
                'dropdownOptions' => [
                    'label' => 'Export All',
                    'class' => 'btn btn-outline-secondary btn-default'
                ]
            ]);
            ?>
            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
                'columns' => [


                    [
                        'attribute' => 'user',
                        'format' => 'text',
                        'value' => function ($model) {
                            $user = User::findOne($model->user);
                            $profile = ProfileOfficer::findOne($user->profile_id ?? "");
                            return ($profile->first_name ?? " ") . " " . ($profile->last_name ?? "") . " (" . ($user->nic ?? "") . ")";
                        }
                    ],
                    'request_date',
                    'can_continue',
                    'reson',
                    //'status',
                    [
                        'attribute' => 'district',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->district0->name ?? "";
                        }
                    ],
                    [
                        'attribute' => 'division',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->division0->name ?? "";
                        }
                    ],
                    [
                        'attribute' => 'landing_site',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->landingSite->name ?? "";
                        }
                    ],

                ],
            ]); ?>

            <?php Pjax::end(); ?>

        </div>
    </div>
</div>
