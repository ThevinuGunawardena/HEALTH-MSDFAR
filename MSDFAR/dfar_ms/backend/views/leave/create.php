<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Leave $model */
/** @var array $balanceData */

$this->title = Yii::t('app', 'Create Leave Request');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Leave Requests'), 'url' => ['index', 'mode' => 'my']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-create">
    <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-3">
        <?php // The layout prints the title; this line describes the page. ?>
        <div class="mb-2 mb-md-0" style="font-size:1.05rem; font-weight:600; color:#5a5c69; line-height:1.35;">
            <?= Yii::t('app', 'Fill in the details below and submit for approval') ?>
        </div>
        <?php // Same outline treatment as the Back button on the view page. ?>
        <?= Html::a(
            '<i class="fas fa-arrow-left mr-2"></i>' . Yii::t('app', 'Back to list'),
            ['index', 'mode' => 'my'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
    </div>
    <?= $this->render('_form', [
        'model'       => $model,
        'balanceData' => $balanceData,
    ]) ?>
</div>