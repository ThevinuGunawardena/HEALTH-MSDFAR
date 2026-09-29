<?php

use backend\config\Constant;
use backend\controllers\InquiryController;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFishermanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Inquiries');
$this->params['breadcrumbs'][] = $this->title;

$dataProvider->setSort([
    'defaultOrder' => ['Inquiry_ID' => SORT_DESC], // Sorting new inquiries first
]);

$gridColumns = [
    [
        'attribute' => 'Inquiry_ID',
        'filter' => Html::activeTextInput($searchModel, 'Inquiry_ID', ['class' => 'form-control']),
    ],
    [
        'attribute' => 'Name',
        'filter' => Html::activeTextInput($searchModel, 'Name', ['class' => 'form-control']),
    ],
    [
        'attribute' => 'phone_number',
        'filter' => Html::activeTextInput($searchModel, 'phone_number', ['class' => 'form-control']),
    ],
    [
        'attribute' => 'District',
        'filter' => Html::activeDropDownList(
            $searchModel,
            'District',
            \yii\helpers\ArrayHelper::map(\backend\models\MFiDistrict::find()->all(), 'id', 'name'),
            ['class' => 'form-control', 'prompt' => 'Select District']
        ),
        'value' => function ($model) {
            return $model->district0 ? $model->district0->name : null;
        },
    ],
    [
        'attribute' => 'Office',
        'filter' => Html::activeTextInput($searchModel, 'Office', ['class' => 'form-control']),
    ],

    [
        'attribute' => 'Submission_Date',
        'filter' => Html::activeTextInput($searchModel, 'Submission_Date', ['class' => 'form-control']),
    ],

    [
        'attribute' => 'completion_date',
        'filter' => Html::activeTextInput($searchModel, 'completion_date', ['class' => 'form-control']),
    ],
    [
        'attribute' => 'Inquiry_Type',
        'filter' => Html::activeTextInput($searchModel, 'Inquiry_Type', ['class' => 'form-control']),
    ],

    [
        'attribute' => 'Inquiry_Status',
        'filter' => Html::activeDropDownList($searchModel, 'Inquiry_Status', Constant::$Inquiry_Status, ['class' => 'form-control', 'prompt' => 'Select Status']),
        'format' => 'text',
        'value' => function ($model) {
            return isset(Constant::$Inquiry_Status[$model->Inquiry_Status])
                ? Constant::$Inquiry_Status[$model->Inquiry_Status]
                : 'N/A';
        }
    ],
    [
        'attribute' => 'Action',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::a('View', ['view', 'id' => $model->Inquiry_ID], ['class' => 'btn btn-sm btn-primary']);
        }
    ],
];
?>

<?php echo $this->render('../common/inquiryStack', ["counts" => InquiryController::getStacs()]); ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <!-- Create Inquiry Button and Dashboard Button -->
            <div class="mb-3">
                <?= Html::a('Create Inquiry', ['create'], ['class' => 'btn btn-success']) ?>

                <?php if (!Yii::$app->user->isGuest && in_array(Yii::$app->user->identity->type, [15])): ?>
                    <?= Html::a('Dashboard', ['inquiry/dashboard'], ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
            </div>

            <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
                'dropdownOptions' => [
                    'label' => 'Export All',
                    'class' => 'btn btn-outline-secondary btn-default'
                ]
            ]); ?>

            <?= GridView::widget([
                'filterModel' => $searchModel, // Pass the search model to enable filtering
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns,
            ]); ?>
        </div>
    </div>
</div>