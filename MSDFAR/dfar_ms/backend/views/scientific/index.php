<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ProfileOfficer;
use backend\models\ScientificData;
use backend\services\CommonService;
use backend\services\Util;
use common\models\User;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ScientificDataSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Scientific Inspections');
$this->params['breadcrumbs'][] = $this->title;

// Registers safe Yii-generated URLs and the authenticated draft-storage key.
$this->render('_js_config');

$exportMenuEmu = [


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

];


$exportMenu = [

    [
        'attribute' => 'district',
        'format' => 'text',
        'label' => 'District',
        'value' => function ($model) {
            return $model->district0->name;
        }
    ],
    [
        'attribute' => 'landing_site',
        'format' => 'text',
        'label' => 'Landing Site',
        'value' => function ($model) {
            return $model->landingSite->name ?? "";
        }
    ],

    [
        'attribute' => 'start_time',
        'format' => 'raw',
        'value' => function ($model) {
            return date("Y-m-d", strtotime($model->start_time));
        }
    ], [
        'attribute' => 'end_time',
        'format' => 'raw',
        'value' => function ($model) {
            return date("Y-m-d", strtotime($model->end_time));
        }
    ],
    //'end_time',
    [
        'attribute' => 'added_by',
        'format' => 'text',
//                        'label' => 'Inspection Done by',
        'value' => function ($model) {
            $profile = $model->addedBy ? ProfileOfficer::findOne($model->addedBy->profile_id) : null;
            return $profile ? $profile->first_name . " " . $profile->last_name : "";
        }
    ],

    //'status',
//            [
//                'class' => ActionColumn::className(),
//                'urlCreator' => function ($action, ScientificData $model, $key, $index, $column) {
//                    return Url::toRoute([$action, 'id' => $model->id]);
//                }
//            ],
];
?>

<?php if (!UserTypeUtil::hasType(Constant::MANAGEMENT)) { ?>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <h1><?= Html::encode($this->title) ?>
                    <a class="btn btn-outline-info float-right" target="_blank"
                       href="https://iotcofcf.wixsite.com/speciesid"> Fish Identification </a>
                    <a class="btn btn-outline-info float-right" target="_blank"
                       href="https://iotcofcf.wixsite.com/speciesid/iotc-species-photolibrary">Fish Photo Library</a>
                    <a class="btn btn-outline-info float-right" target="_blank"
                       href="https://ee.kobotoolbox.org/x/085tobym">Tool Box </a>
                </h1>
                <?php if ($sientificEnumerationData == null) { ?>
                    <?php $form = ActiveForm::begin(['options' => [
                        'class' => 'userform'
                    ]]); ?>
                    <div class="row">
                        <div class="col-xl-4">
                            <?= $form->field($sientificEnumeration, 'request_date')->textInput(["type" => "date", "max" => date("Y-m-d"), 'onkeydown' => "return false"]) ?>
                        </div>
                        <div class="col-xl-4">
                            <?= $form->field($sientificEnumeration, 'can_continue')->dropDownList(["yes" => "Yes", "no" => "No"]) ?>
                        </div>
                        <div class="col-xl-12">
                            <div class="sientificEnumeration-reason" style="display:none;">
                                <?= $form->field($sientificEnumeration, 'reson')->dropDownList(Constant::$ScintificEnumReason, ["prompt" => "Select"]) ?>

                            </div>
                            <div class="sientificEnumeration-reason-other" style="display:none;">
                                <div class="form-group field-scientificenumerationrequest-request_date  ">
                                    <label class="control-label" for="scientificenumerationrequest-request_date">Please
                                        Enter the Reason</label>
                                    <input type="text" id="scientificenumerationrequest-other-reason"
                                           class="form-control" name="ScientificEnumerationRequest[other-reason]">

                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="row">

                        <div class="col-xl-4">
                            <?= $form->field($sientificEnumeration, 'district')->dropDownList(CommonService::getFIDistrictArray(), ['prompt' => 'Select...', "onchange" => 'loadDivisionsAjaxScientific($(this).val(),' . $sientificEnumeration->division . ')']) ?>
                        </div>
                        <div class="col-xl-4">
                            <?= $form->field($sientificEnumeration, 'division')->dropDownList([], ['prompt' => 'Select...', "onchange" => 'loadLandingSiteAjaxScientific($(this).val(),' . $sientificEnumeration->landing_site . ')']) ?>
                        </div>
                        <div class="col-xl-4">
                            <?= $form->field($sientificEnumeration, 'landing_site')->dropDownList([]) ?>
                        </div>

                    </div>

                    <div class="form-group">
                        <?= Util::editPermission() ? Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) : "" ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                <?php } ?>
                <?php if ($sientificEnumerationData != null && $sientificEnumerationData->can_continue == "yes") { ?>
                    <p>
                        <?= Util::editPermission() ? Html::a(Yii::t('app', 'Start Scientific Inspection'), ['/scientific/create'], ['class' => 'btn btn-success']) : "" ?>
                    </p>

                <?php } ?>

            </div>
        </div>
    </div>
<?php } ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1>Scientific Enumeration Requests</h1>


            <div class="scientific-enumeration-request-search">

                <?php $form = ActiveForm::begin([
                    'action' => ['index'],
                    'method' => 'get',
                    'options' => [
                        'data-pjax' => 1
                    ],
                ]); ?>

                <!--                --><?php //= $form->field($searchModelEmu, 'id') ?>

                <!--                --><?php //= $form->field($searchModelEmu, 'user') ?>
                <div class="row">
                    <div class="col-lg-6"> <?= $form->field($searchModelEmu, 'request_date')->textInput(["type" => "date"]) ?></div>
                    <div class="col-lg-6">  <?php echo $form->field($searchModelEmu, 'district')->dropDownList(CommonService::getFIDistrictArray(), ['prompt' => 'Select...', "onchange" => 'loadDivisionsAjaxScientific($(this).val(),' . $sientificEnumeration->division . ')']) ?>
                    </div>
                </div>


                <!--                --><?php //= $form->field($searchModelEmu, 'date') ?>

                <!--    --><?php //= $form->field($model, 'can_continue') ?>

                <?php // echo $form->field($model, 'reson') ?>

                <?php // echo $form->field($model, 'status') ?>

                <!---->
                <!--                --><?php // echo $form->field($searchModelEmu, 'division') ?>

                <?php // echo $form->field($model, 'landing_site') ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>

            <!--            --><?php //Pjax::begin(); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            <?= ExportMenu::widget([
                'dataProvider' => $dataProviderEmu,
                'columns' => $exportMenuEmu,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
                'dropdownOptions' => [
                    'label' => 'Export All',
                    'class' => 'btn btn-outline-secondary btn-default'
                ],
                'filename' => 'Scientific Enumeration Requests - ' . date('dd-MM-yy')
            ]);
            ?>
            <?= GridView::widget([
                'dataProvider' => $dataProviderEmu,
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
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

            <!--            --><?php //Pjax::end(); ?>

        </div>
    </div>
</div>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1>Scientific Reports </h1>
            <!--            --><?php //Pjax::begin(); ?>
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
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
                ],
                'filename' => 'Scientific Reports - ' . date('dd-MM-yy')
            ]);
            ?>
            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'district',
                        'format' => 'text',
                        'label' => 'District',
                        'value' => function ($model) {
                            return $model->district0->name;
                        }
                    ],
                    [
                        'attribute' => 'landing_site',
                        'format' => 'text',
                        'label' => 'Landing Site',
                        'value' => function ($model) {
                            return $model->landingSite->name ?? "";
                        }
                    ],

                    [
                        'attribute' => 'start_time',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return date("Y-m-d",strtotime($model->start_time));
                        }
                    ],[
                        'attribute' => 'end_time',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return date("Y-m-d",strtotime($model->end_time));
                        }
                    ],
                    //'end_time',
                    [
                        'attribute' => 'added_by',
                        'format' => 'text',
//                        'label' => 'Inspection Done by',
                        'value' => function ($model) {
                            $profile = $model->addedBy ? ProfileOfficer::findOne($model->addedBy->profile_id) : null;
                            return $profile ? $profile->first_name . " " . $profile->last_name : "";
                        }
                    ],

                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $recordToken = SecurityHelper::encryptId(
                                ScientificData::class,
                                (int) $model->id
                            );

                            return Html::a(
                                Yii::t('app', 'View'),
                                [
                                    '/scientific/view',
                                    'token' => $recordToken,
                                ],
                                [
                                    'class' => 'btn btn-sm btn-primary',
                                ]
                            );
                        }
                    ],
                    //'status',
//            [
//                'class' => ActionColumn::className(),
//                'urlCreator' => function ($action, ScientificData $model, $key, $index, $column) {
//                    return Url::toRoute([$action, 'id' => $model->id]);
//                }
//            ],
                ],
            ]); ?>

            <!--            --><?php //Pjax::end(); ?>

        </div>
    </div>
</div>
