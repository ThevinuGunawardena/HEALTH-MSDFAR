<?php

/** @var yii\web\View $this */
/** @var backend\models\Skipper|backend\models\SkipperRenew $model */
/** @var bool $renew */

$renew = (bool) ($renew ?? false);

$this->title = Yii::t(
    'app',
    $renew ? 'Renew Skipper License' : 'Create Skipper License'
);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Skippers'),
    'url' => ['/skipper/index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <?= $this->render('_form', [
                'model' => $model,
                'fishermanList' => $fishermanList ?? [],
                'educationQualification' => $educationQualification ?? [],
                'districtList' => $districtList ?? [],
                'skipperTranings' => $skipperTranings,
                'instituteList' => $instituteList ?? [],
                'renew' => $renew,
            ]) ?>
        </div>
    </div>
</div>
