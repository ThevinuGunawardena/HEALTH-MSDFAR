<?php

/** @var yii\web\View $this */
/** @var backend\models\MeaBoatRegistration $model */

/** @var boolean $renew */

$this->title = Yii::t('app', $renew ? 'Renew MEA Boat Registration report' : 'New MEA Boat Registration report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Mea Boat Registrations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mea-boat-registration-create">


    <?= $renew ?
        $this->render('_form_renew', [
            'model' => $model,
            'boat' => $boat,
        ]) :
        $this->render('_form', [
            'model' => $model,
            'boat' => $boat,
        ]) ?>

</div>
