<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumbers;
use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\components\RecordLookupRateLimit;
use backend\components\SecurityHelper;
/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoat $model */

$this->title = Yii::t('app', 'Apply for Boat registration ' . ($model->renew == 1 ? 'Renew - ' : '- ') . $boatDetails->boat_number);
if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fisherman Registered Boats'), 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;
$BoatNumberModel = BoatNumbers::findOne(['id' => $boatDetails->id]);

?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>
            <div class="row">
                <div class="col-lg-12">

                    <?= DetailView::widget([
                        'model' => $BoatNumberModel,
                        'attributes' => [
                            'boat_number',
                            [
                                'attribute' => 'boat_design',
                                'format' => 'text',
                                'value' => function ($model) {
                                    return $model->boatDesign->design_notation;
                                }
                            ], [
                                'attribute' => 'owner',
                                'format' => 'text',
                                'label' => 'Owner',
                                'value' => function ($model) {
                                    return $model->owner0->first_name . " " . $model->owner0->last_name;
                                }
                            ],

                            [
                                'attribute' => 'fisheries_district',
                                'format' => 'text',
                                'label' => 'Fisheries district',
                                'value' => function ($model) {
                                    return $model->fisheriesDistrict->name;
                                }
                            ],
                            [
                                'attribute' => 'fisheries_division',
                                'format' => 'text',
                                'label' => 'Fisheries division',
                                'value' => function ($model) {
                                    return $model->fisheriesDivision->name;
                                }
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'text',
                                'label' => 'Status',
                                'value' => function ($model) {
                                    return Constant::$licenseStatus[$model->status];
                                }
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>
