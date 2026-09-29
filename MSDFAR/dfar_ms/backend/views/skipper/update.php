<?php

use backend\components\SecurityHelper;
use backend\models\Skipper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var Skipper $model */

$token = SecurityHelper::encryptId(
    Skipper::class,
    $model->id
);

$this->title = Yii::t(
    'app',
    'Update Skipper License: {name}',
    [
        'name' => (string) (
            $model->skipper_uid
            ?? Yii::t('app', 'Not Generated')
        ),
    ]
);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Skippers'),
    'url' => ['/skipper/index'],
];
$this->params['breadcrumbs'][] = [
    'label' => (string) (
        $model->skipper_uid
        ?? 'SKP-' . sprintf('%05d', (int) $model->id)
    ),
    'url' => [
        '/skipper/view',
        'token' => $token,
    ],
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <?php Pjax::begin(['id' => 'skipper-update-form']); ?>

            <?= $this->render('_form', [
                'model' => $model,
                'fishermanList' => $fishermanList ?? [],
                'educationQualification' => $educationQualification ?? [],
                'districtList' => $districtList ?? [],
                'skipperTranings' => $skipperTranings,
                'instituteList' => $instituteList ?? [],
                'renew' => (bool) ($renew ?? false),
            ]) ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
