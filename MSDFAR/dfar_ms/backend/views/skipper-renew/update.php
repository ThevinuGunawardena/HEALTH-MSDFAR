<?php

use backend\components\SecurityHelper;
use backend\models\SkipperRenew;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var SkipperRenew $model */

$token = SecurityHelper::encryptId(
    SkipperRenew::class,
    $model->id
);

$reference = (string) (
    $model->skipper_uid
    ?? Yii::t('app', 'Not Generated')
);

$this->title = Yii::t(
    'app',
    'Update Skipper Renewal: {reference}',
    [
        'reference' => $reference,
    ]
);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t(
        'app',
        'Skipper Renewals'
    ),
    'url' => ['/skipper-renew/index'],
];

$this->params['breadcrumbs'][] = [
    'label' => $reference,
    'url' => [
        '/skipper-renew/view',
        'token' => $token,
    ],
];

$this->params['breadcrumbs'][] =
    Yii::t('app', 'Update');
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <h1>
                <?= Html::encode($this->title) ?>
            </h1>

            <?php Pjax::begin([
                'id' => 'skipper-renew-update-form',
            ]); ?>

            <?= $this->render(
                '_form',
                [
                    'model' => $model,
                    'fishermanList' =>
                        $fishermanList ?? [],
                    'educationQualification' =>
                        $educationQualification ?? [],
                    'districtList' =>
                        $districtList ?? [],
                    'skipperTranings' =>
                        $skipperTranings,
                    'instituteList' =>
                        $instituteList ?? [],
                    'renew' => true,
                ]
            ) ?>

            <?php Pjax::end(); ?>

        </div>
    </div>
</div>
