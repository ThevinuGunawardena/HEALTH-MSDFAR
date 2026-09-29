<?php
$webURL = Yii::getAlias('@web');
use backend\components\SecurityHelper;
use yii\helpers\Html;

?>
<style>
    *, *::before, *::after {
        box-sizing: unset;
    }
</style>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
        <?= Html::a('Download', ['license-download', 'token' => SecurityHelper::encryptId(\backend\models\NationalLicense::class, $model->id)], ['class' => 'btn btn-success']) ?>        </div>
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
