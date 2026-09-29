<?php
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
<?= Html::a(
    Yii::t('app', 'Download'),
    [
        'license-download',
        'id' => (int) $model->id,
    ],
    [
        'class' => 'btn btn-success',
    ]
) ?>        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="license-view">
                <?= $this->render('license', [
                    'model' => $model,
                    'storePlaces' => $storePlaces,
                ]) ?>

            </div>
        </div>
    </div>
</div>
