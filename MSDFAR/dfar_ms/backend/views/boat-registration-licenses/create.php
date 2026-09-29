<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoatLicense $model */

$this->title = Yii::t('app', 'Create Fisherman Registerd Boat License');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fisherman Registerd Boat Licenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fisherman-registerd-boat-license-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
