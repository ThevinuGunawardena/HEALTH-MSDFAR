<?php

use backend\components\SecurityHelper;
use yii\helpers\Html;

$webURL = Yii::getAlias('@web');

?>
<style>
    *, *::before, *::after {
        box-sizing: unset;
    }
</style>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <?= Html::a('Download', ['/highseas-license/license-download', 'token' => SecurityHelper::encryptId(\backend\models\HighseasLicense::class, $model->id)], ['class' => 'btn btn-success']) ?>
        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <div class="license-view" style="overflow: auto">
                <?= $this->render('license', [
                    'model' => $model,
                ]) ?>

            </div>
        </div>
    </div>
</div>
