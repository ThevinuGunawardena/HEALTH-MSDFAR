<?php

use backend\config\Constant;
use backend\models\ApprovalLog;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use backend\models\MFishermanCategory;


/** @var yii\web\View $this */
/** @var backend\models\SkipperSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Fisherman Registration License printed Report');
$this->params['breadcrumbs'][] = $this->title;


$categoryList = ArrayHelper::map(
    MFishermanCategory::find()->where(['status' => 1])->asArray()->all(),
    'id',
    'category'
);
$exportMenu =
    [
        'fisherman_uid',
        'first_name',
        'last_name',
        'nic',
        //'passport',
        //'dob',
//        'gender',
    'permanent_address',
    'current_address',
    //'blood_group',
//        'mobile',
    //'fixed_line',
    //'email:email',
    [
        'attribute' => 'district',
        'format' => 'text',
        'label' => 'District',
        'value' => function ($model) {
            return $model->district0->name;
        }
    ],
    [
        'attribute' => 'division',
        'format' => 'text',
        'label' => 'Division',
        'value' => function ($model) {
            return $model->division0->name;
        }
    ],

    //'year_recruitment',
    //'life_isurance_no',
    //'member_fisheries_society',
    //'civil',
[
    'attribute' => 'category',
    'format' => 'text',
    'label' => 'Category',
    'value' => function ($model) use ($categoryList) {
        return $categoryList[$model->category] ?? '-';
    }
],   //'management_area',
    [
        'attribute' => 'status',
        'format' => 'text',
        'value' => function ($model) {
            return Constant::$licenseStatus[$model->status];
        }
    ],
    'created',
    'expire_date',
    [
        'attribute' => 'CreatedBy',
        'format' => 'text',
        'value' => function ($model) {
            $model = ApprovalLog::find()->where(["type" => "FISHERMAN-REG", "process_id" => $model->id, "status" => "Submitted"])->orderBy(["id" => SORT_ASC])->one();
            if ($model != "") {
//                $user = User::findOne($model->done_by);
//                $officer = ProfileOfficer::find()->where(["id" => $user->profile_id])->asArray()->one();
//                $officer["type"] = $user->type;

                return $model->doneBy->nic ?? " ";
            }
            return "";
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
                'action' => "fishermen-registration-printed",
                'status' => $status,

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
