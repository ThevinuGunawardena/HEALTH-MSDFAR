<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoatRenew $model */

$this->title = Yii::t('app', 'Create Fisherman Registerd Boat Renew');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fisherman Registerd Boat Renews'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fisherman-registerd-boat-renew-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
