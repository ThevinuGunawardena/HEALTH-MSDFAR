<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\RenewBoatRegistration $model */

$this->title = Yii::t('app', 'Create Renew Boat Registration');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Renew Boat Registrations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="renew-boat-registration-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
