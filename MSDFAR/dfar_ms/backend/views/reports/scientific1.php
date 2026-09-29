<?php

use backend\models\ProfileOfficer;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\SkipperSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Scientific Report');
$this->params['breadcrumbs'][] = $this->title;


$exportMenu =
    [

        [
            'attribute' => 'id',
            'format' => 'text',
            'label' => 'Scientific Data ID'

        ],
        [
            'attribute' => 'landing_site',
            'format' => 'text',
            'label' => 'Scientific Code',
            'value' => function ($model) {
                return $model->landingSite->scientific_code ?? '';
            }
        ],
        [
            'attribute' => 'district',
            'format' => 'text',
            'label' => 'Fi District Name',
            'value' => function ($model) {
                return $model->district0->name;
            }
        ],
        [
            'attribute' => 'division',
            'format' => 'text',
            'label' => 'Fi Division Name',
            'value' => function ($model) {
                return $model->division0->name;
            }
        ],
        [
            'attribute' => 'landing_site',
            'format' => 'text',
            'label' => 'Landing Site Name',
            'value' => function ($model) {
                return $model->landingSite->name ?? "";
            }
        ],
        'start_time:date',
        ['attribute' => 'start_time',
            'label' => 'Start time',
            'format' => 'datetime'
        ],
        ['attribute' => 'end_time',
            'label' => 'End time',
            'format' => 'datetime'
        ],
        [
            'attribute' => 'CreatedBy',
            'label' => 'Added By',
            'format' => 'text',
            'value' => function ($model) {
                $profile = $model->addedBy ? ProfileOfficer::findOne($model->addedBy->profile_id) : null;
                return $model->addedBy->nic ?? "" . " " . ($profile ? "(" . $profile->first_name . " " . $profile->last_name . ")" : "");
//                $user = User::findOne($model->done_by);
//                $officer = ProfileOfficer::find()->where(["id" => $user->profile_id])->asArray()->one();
//                $officer["type"] = $user->type;


            }
        ],
        [
            'attribute' => 'status',

        ],
        [
            'attribute' => 'approval_stage',

        ],
        [
            'attribute' => 'inspected_by',
            'format' => 'text',
            'value' => function ($model) {
                $profile = $model->addedBy ? ProfileOfficer::findOne($model->addedBy->profile_id) : null;
                return $model->addedBy->nic ?? "" . " " . ($profile ? "(" . $profile->first_name . " " . $profile->last_name . ")" : "");
//                $user = User::findOne($model->done_by);
//                $officer = ProfileOfficer::find()->where(["id" => $user->profile_id])->asArray()->one();
//                $officer["type"] = $user->type;


            }
        ],


    ];
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <!--    --><?php //Pjax::begin(); ?>
            <?php echo $this->render('_search', ['from' => $from,
                'to' => $to,
                'action' => "scientific1",
                'district' => $district]); ?>
            <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $exportMenu,
                'batchSize' => 500,
                'target' => '_blank',
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
                'columns' => $exportMenu
            ]); ?>
        </div>
    </div>
</div>
