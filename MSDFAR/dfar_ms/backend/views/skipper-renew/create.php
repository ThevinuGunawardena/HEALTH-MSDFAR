<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Skipper|backend\models\SkipperRenew $model */

$this->title = Yii::t(
    'app',
    'Create Skipper Renewal'
);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t(
        'app',
        'Skipper Renewals'
    ),
    'url' => ['/skipper-renew/index'],
];

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <h1>
                <?= Html::encode($this->title) ?>
            </h1>

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
                    'renew' =>
                        (bool) ($renew ?? true),
                ]
            ) ?>

        </div>
    </div>
</div>
