<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumbers $model */
/** @var array $yardList */
/** @var array $districtList */
/** @var array $boatTypeArray */

$this->title = Yii::t('app', 'Create Boat Numbers');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Boat Numbers'),
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="boat-numbers-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'token' => null,
        'yardList' => $yardList,
        'districtList' => $districtList,
        'boatTypeArray' => $boatTypeArray,
    ]) ?>
</div>
