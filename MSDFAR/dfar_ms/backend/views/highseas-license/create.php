<?php

/** @var yii\web\View $this */
/** @var backend\models\HighseasLicense $model */

$model->renew ? $this->title = Yii::t('app', 'Renew Highseas License: {name}', [
    'name' => $model->license_number,
]) : $this->title = Yii::t('app', 'Create Highseas License');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Highseas Licenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= $this->render('_form', [
                'model' => $model,
                'skipperList' => $skipperList,
                'update' => false,
                'fishermanList' => $fishermanList ?? [],
                'boatNumberList' => $boatNumberList ?? [],
                'errors' => $errors ?? [],

            ]) ?>

        </div>
    </div>
</div>
