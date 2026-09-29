<?php


use backend\config\Constant;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\BoatRegiatrationLicenseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Boat Registration Licenses');
$this->params['breadcrumbs'][] = $this->title;


$exportMenu =
    [
        [
            'attribute' => 'boat_registration_id',
            'format' => 'text',
            'label' => 'Boat Number',
            'value' => function ($model) {
                return $model->boatNumber->boat_number;
            }
        ],
        [
            'attribute' => 'fisherman_id',
            'format' => 'text',
            'label' => 'Fisherman',
            'value' => function ($model) {
                if ($model->fisherman == null) {
                    return '-';
                }

                return $model->fisherman->fisherman_uid . " - " .
                    $model->fisherman->first_name . " " .
                    $model->fisherman->last_name;
            }
        ],

        [
            'attribute' => 'district',
            'format' => 'text',
            'label' => 'Fisheries District',
            'value' => function ($model) {
                return $model->district0->name;
            }
        ],
        
       
        [
            'attribute' => 'status',
            'format' => 'text',
            'value' => function ($model) {
                return Constant::$licenseStatus[$model->status];
            }
        ],
        // [
        //     'attribute' => 'approval_stage',
        //     'format' => 'text',
        //     'label' => 'Approval stage',
        //     'value' => function ($model) {
        //         return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
        //     }
        // ],
         [
            'attribute' => 'expire_date',
            'format' => 'text'
        ],

        [
            'attribute' => 'renew',
            'format' => 'text',
            'label' => 'First Registration / Renewal',
            'value' => function ($model) {
                if ($model->renew == 0) {
                    return 'First Registration';
                }

                if ($model->renew == 1) {
                    return 'Renewal';
                }

                return '-';
            }
        ],

    ];
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <!--    --><?php //Pjax::begin(); ?>
           <?= $this->render('_search', [
                    'from' => $from,
                    'to' => $to,
                    'district' => $district,
                    'boat_type' => $boat_type,
                    'reg_type' => $reg_type,
                    'status' => $status,
                    'action' => 'boat-registration',
                ]); ?>
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
