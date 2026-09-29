<?php

/** @var yii\web\View $this */

/** @var backend\models\NationalLicense $model */

use backend\config\Constant;
use backend\models\FishermanRegisterdBoatLicense;
use yii\widgets\DetailView;

$model->renew ? $this->title = Yii::t('app', 'Renew National License:  {name}', ['name' => $model->license_number,]) :
    $this->title = Yii::t('app', 'Apply for National License ');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'National Licenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$boatRegModel = FishermanRegisterdBoatLicense::find()->where(['id' => $model->boat_registration_id])->one();

?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">

                <div class="col-lg-12">
                    <h3> Boat Registration details </h3>
                </div>
                <div class="col-lg-12">
                    <?= DetailView::widget([
                        'model' => $boatRegModel,
                        'attributes' => [
                            [
                                'attribute' => 'boat_number_id',
                                'format' => 'text',
                                'label' => 'Boat number',
                                'value' => function ($model) {
                                    return $model->boatNumber->boat_number;
                                }
                            ],
                            [
                                'attribute' => 'fisherman_id',
                                'format' => 'text',
                                'label' => 'Fisherman',
                                'value' => function ($model) {
                                    return $model->fisherman->fisherman_uid;
                                }
                            ],
                            [
                                'attribute' => 'district',
                                'format' => 'text',
                                'value' => function ($model) {
                                    return $model->district0->name;
                                }
                            ],
                            [
                                'attribute' => 'division',
                                'format' => 'text',
                                'value' => function ($model) {
                                    return $model->division0->name;
                                }
                            ],

                            [
                                'attribute' => 'landing_site',
                                'format' => 'text',
                                'value' => function ($model) {
                                    return $model->landingSite->name ?? "";
                                }
                            ],
                            'insurance_no',
                            'call_sign_no',
                            [
                                'attribute' => 'engine_make',
                                'format' => 'text',
                                'value' => function ($model) {
                                    return Constant::$engineMake[$model->engine_make] ?? "";
                                }
                            ], 'engine_horsepower',
                            'engine_serial_number',
                            'communication_equipment',
                            'fishing_equipment',
                            'navigation_equipment',
                            'mea_report',
                            'witness_name',
                            'witness_nic',

                        ],
                    ]) ?>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">
                <?= $this->render('_form', [
                   'model' => $model,
                    'gearTypes' => $gearTypes,
                    'divisionGearData' => $divisionGearData,
                    'districtList' => $districtList,
                    'landingSite' => $landingSite,
                    'readOnly' => $readOnly,
                    'errors' => $errors,
                    'boatRegistration' => $boatRegistration,
                ]) ?>

            </div>
        </div>
    </div>
</div>
