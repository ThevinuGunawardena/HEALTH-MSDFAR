<?php

use backend\components\SecurityHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\HighseasLicense $model */

$this->title = Yii::t('app', 'Update Highseas License: {name}', [
    'name' => $model->license_number ?? "",
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Highseas Licenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $model->license_number ?? '',
    'url' => [
        '/highseas-license/view',
        'token' => SecurityHelper::encryptId(
            \backend\models\HighseasLicense::class,
            $model->id
        ),
    ],
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
                'skipperList' => $skipperList,
                'update' => true,
                'errors' => $errors ?? [],
            ]) ?>

        </div>
    </div>
</div>
