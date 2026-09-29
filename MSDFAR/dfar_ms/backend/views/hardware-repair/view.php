<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\HardwareRepair $model */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Hardware Repairs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$this->registerCss("
    .detail-view-container {
        width: 1000px;
        margin: 20px auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .detail-view-header {
        
        margin-bottom: 20px;
    }
    .detail-view-header h1 {
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }
    .action-buttons {
        display: flex;
        
        gap: 10px;
        margin-bottom: 20px;
    }
    .action-buttons .btn {
        min-width: 120px;
    }
");

?>

<div class="hardware-repair-view detail-view-container">

    <div class="detail-view-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="action-buttons">
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <div class="table-responsive">
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-striped table-bordered detail-view'],
            'attributes' => [
                'id',
                'Name',
                'Phone_number',
                'Email:email',
                'Office',
                'Serial_number',
                'Brand_name',
                'Issue:ntext',
                'Received_date',
                'Status',
                'Remarks:ntext',
            ],
        ]) ?>
    </div>

</div>