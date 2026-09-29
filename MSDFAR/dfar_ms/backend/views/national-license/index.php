<?php


use backend\config\Constant;
use backend\controllers\NationalLicenseController;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use backend\components\SecurityHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\NationalLicenseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'National Licenses');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php echo $this->render('../common/stac', ["counts" => NationalLicenseController::getStacs()]); ?>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <div class="col-lg-4 offset-lg-8">
            </div>


            <!--            --><?php //Pjax::begin(); ?>

            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => [

                    [
                        'attribute' => 'license_number',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->license_number ?? "Not Generated";
                        }
                    ],
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            $fName = $model->fisherman->first_name ?? " ";
                            $lName = $model->fisherman->last_name ?? "";
                            return $model->fisherman->fisherman_uid ?? " " . "-" . $fName . " " . $lName;
                        }
                    ],
                    [
                        'attribute' => 'boat_registration_id',
                        'format' => 'text',
                        'label' => 'Boat Number',
                        'value' => function ($model) {
                            return $model->boatRegistration->boatNumber->boat_number ?? "";
                        }
                    ],
                    //'name_of_coastal',
                    //'main_gear_type',
                    //'landing_site',
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'label' => 'Status',
                        'value' => function ($model) {
                            return Constant::$licenseStatus[$model->status];
                        }
                    ],
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
                        }
                    ],
                    //'approval_stage',
                   [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => static function ($model) {
                            return Html::a(
                                'View',
                                [
                                    '/national-license/view',
                                    'token' => SecurityHelper::encryptId(
                                        \backend\models\NationalLicense::class,
                                        $model->id
                                    ),
                                ],
                                [
                                    'class' => 'btn btn-sm btn-primary',
                                ]
                            );
                        },
                    ],
                ],
            ]); ?>

            <!--            --><?php //Pjax::end(); ?>

        </div>
    </div>
</div>
