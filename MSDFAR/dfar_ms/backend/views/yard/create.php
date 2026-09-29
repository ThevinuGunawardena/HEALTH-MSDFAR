<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ProfileYard $model */

$this->title = Yii::t('app', 'Create Profile Yard');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Yards'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-yard-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'fishermanList' => $fishermanList,
        'districtList' => $districtList,

    ]) ?>

</div>
