<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\RenewBoatRegistration $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Renew Boat Registrations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$webURL = Yii::getAlias('@web');

?>
<div class="renew-boat-registration-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'reg_id',
            'notes',
            'status',
            'approval_statge',
        ],
    ]) ?>


    <hr>
    <div class="row">
        <div class="col-lg-12">
            <h3>Uploaded files</h3>
        </div>
        <div class="col-lg-12">
            <?php
            foreach ($files as $file) { ?>
                <li><?= $file->fileType->discription ?> : <a target="_blank" href="<?=$webURL?>/uploads/files/<?= $file->file_name ?>"><?= $file->file_name ?></a></li>
            <?php }
            ?>
        </div>
    </div>

    <hr>
</div>
