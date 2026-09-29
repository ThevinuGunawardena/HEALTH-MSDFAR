<?php

use backend\services\CommonService;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumbers $model */
/** @var string $token */

$officer = CommonService::getApprovedOfficer(
    $model->id,
    'BOAT_NUMBER'
);

if ($officer instanceof \yii\db\ActiveRecord) {
    $officer = $officer->toArray();
}

if (!is_array($officer)) {
    $officer = [];
}
?>

<style>
    *,
    *::before,
    *::after {
        box-sizing: unset;
    }
</style>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <?= Html::a(
                Yii::t('app', 'Go Back'),
                [
                    '/boat-numbers/view',
                    'token' => $token,
                ],
                [
                    'class' => 'btn btn-outline-primary mr-2',
                ]
            ) ?>

            <?= Html::a(
                Yii::t('app', 'Download'),
                [
                    '/boat-numbers/license-download',
                    'token' => $token,
                ],
                [
                    'class' => 'btn btn-success',
                ]
            ) ?>
        </div>
    </div>
</div>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="license-view">
                <?= $this->render(
                    'license',
                    [
                        'model' => $model,
                        'token' => $token,
                        'validationToken' => $token,
                        'officer' => $officer,
                        'isPdf' => false,
                        'hasOfficerSignature' =>
                            !empty($officer['signature']),
                    ]
                ) ?>
            </div>
        </div>
    </div>
</div>
