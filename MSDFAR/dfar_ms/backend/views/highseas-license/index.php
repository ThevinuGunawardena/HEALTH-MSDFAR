<?php


use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\controllers\HighseasLicenseController;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\HighseasLicenseSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Highseas Licenses');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php echo $this->render('../common/stac', ["counts" => HighseasLicenseController::getStacs()]); ?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">


            <!--            --><?php //Pjax::begin(); ?>
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
                // 'filterModel' => $searchModel,
                'columns' => [

                    'license_number',
                    [
                        'attribute' => 'fisherman_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->fisherman->fisherman_uid . "-" . $model->fisherman->first_name . " " . $model->fisherman->last_name;
                        }
                    ],
                    [
                        'attribute' => 'boat_registration_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            return $model->boatRegistration->boatNumber->boat_number;
                        }
                    ],

                    [
                        'attribute' => 'skipper_id',
                        'format' => 'text',
                        'value' => function ($model) {
                            if ($model->skipper->fisherman != null) {
                                return $model->skipper->skipper_uid . " - " . $model->skipper->fisherman->first_name . " " . $model->skipper->fisherman->last_name;

                            } else {
                                return "Skipper not available";
                            }
                        }
                    ],
//            'prevouse_boat_flag',
                    //'no_if_crew_members',
                    //'fishing_gear_type',
                    [
                        'attribute' => 'approval_stage',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$userTypes[$model->approval_stage]['name'] ?? $model->approval_stage;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'text',
                        'value' => function ($model) {
                            return Constant::$licenseStatus[$model->status];
                        }
                    ],
                    //'approval_stage',
                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => static function ($model) {
                            return Html::a(
                                Yii::t('app', 'View'),
                                [
                                    '/highseas-license/view',
                                    'token' => SecurityHelper::encryptId(
                                        \backend\models\HighseasLicense::class,
                                        $model->id
                                    ),
                                ],
                                ['class' => 'btn btn-sm btn-primary']
                            );
                        }
                    ],
                ],
            ]); ?>

            <!--            --><?php //Pjax::end(); ?>


        </div>
    </div>
</div>
