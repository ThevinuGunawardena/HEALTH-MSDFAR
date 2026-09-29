<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Leave $model */
/** @var array $balanceData */

$this->title = Yii::t('app', 'Update Leave Request: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Leave Requests'), 'url' => ['index', 'mode' => 'my']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="leave-update">
    <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center mb-3">
        <?php // The layout prints the title; this line describes the page. ?>
        <div class="mb-2 mb-md-0" style="font-size:1.05rem; font-weight:600; color:#5a5c69; line-height:1.35;">
            <?= Yii::t('app', 'Change the details below and resubmit') ?>
            <span class="text-muted" style="font-weight:500;">
                &middot; <?= Yii::t('app', 'submitted {date}', [
                    'date' => $model->created_at ? date('d M Y', strtotime($model->created_at)) : '—',
                ]) ?>
            </span>
        </div>
        <?php // Matches the Back button on the view and create pages. ?>
        <?= Html::a(
            '<i class="fas fa-arrow-left mr-2"></i>' . Yii::t('app', 'Back to list'),
            ['view', 'id' => $model->id],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
    </div>
    <?= $this->render('_form', [
        'model'       => $model,
        'balanceData' => $balanceData,
    ]) ?>
</div>