<?php

use backend\models\FishermanRegisterdBoat;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\NationalLicense $model */

$this->title = Yii::t('app', 'Update National License: {name}', [
    'name' => $model->license_number ?? "Not Generated",
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'National Licenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->license_number ?? "Not Generated", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
$boatRegModel = FishermanRegisterdBoat::findOne(['id' => $model->boat_registration_id])

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


                        ],
                    ]) ?>
                </div>
            </div>


            <?= $this->render('_form', [
                'model' => $model,
                'divisionGearData' => $divisionGearData,
                'gearTypes' => $gearTypes,
                'districtList' => $districtList,
                'landingSite' => $landingSite,
                'readOnly' => $readOnly,
                'boatRegistration' => $boatRegistration,
            ]) ?>

        </div>
    </div>
</div>
