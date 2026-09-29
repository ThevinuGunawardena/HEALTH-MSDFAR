<?php


use backend\config\Constant;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\HighseasLicenseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Highseas Licenses');
$this->params['breadcrumbs'][] = $this->title;


$exportMenu =
    ['license_number',
        [
            'attribute' => 'fisherman_id',
            'format' => 'text',
            'label' => 'Fisherman',
            'value' => function ($model) {
                return $model->fisherman->fisherman_uid . "-" . $model->fisherman->first_name . " " . $model->fisherman->last_name;
            }
        ],
        [
            'attribute' => 'boat_registration_id',
            'format' => 'text',
            'label' => 'Boat Number',
            'value' => function ($model) {
                return $model->boatRegistration->boatNumber->boat_number;
            }
        ],
        [
            'attribute' => 'skipper_id',
            'format' => 'text',
            'value' => function ($model) {
                return $model->skipper->skipper_uid ?? "";
            }
        ],
        [
            'attribute' => 'prevouse_boat_flag',
            'format' => 'text',
            'value' => function ($model) {
                return Constant::$countries[$model->prevouse_boat_flag] ?? "";
            }
        ],
        'no_if_crew_members',
        [
            'attribute' => 'main_gear_type',
            'format' => 'text',
            'value' => function ($model) {
                return $model->mainGearType->description ?? "";
            }
        ],

        [
            'attribute' => 'expire_date',
            'format' => 'text'
        ],
        [
            'attribute' => 'status',
            'format' => 'text',
            'value' => function ($model) {
                return Constant::$licenseStatus[$model->status];
            }
        ],
        [
            'attribute' => 'approval_stage',
            'format' => 'text',
            'label' => 'Approval stage',
            'value' => function ($model) {
                return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
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
                'district' => $district,
                "action" => "highseas-license"]); ?>
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
