<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumbers $model */
/** @var string $token */
/** @var array $yardList */
/** @var array $districtList */
/** @var array $boatTypeArray */

$this->title = Yii::t(
    'app',
    'Update Boat Number: {name}',
    [
        'name' => (string) $model->boat_number,
    ]
);

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Boat Numbers'),
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = [
    'label' => (string) $model->boat_number,
    'url' => [
        'view',
        'token' => $token,
    ],
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="boat-numbers-update">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'token' => $token,
        'yardList' => $yardList,
        'districtList' => $districtList,
        'boatTypeArray' => $boatTypeArray,
    ]) ?>
</div>
