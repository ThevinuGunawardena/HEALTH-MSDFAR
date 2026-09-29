<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\FishermanRegisterdBoatLicense $model */

$this->title = $model->nid;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fisherman Registerd Boat Licenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="fisherman-registerd-boat-license-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'nid' => $model->nid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'nid' => $model->nid], [
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
            'nid',
            'id',
            'boat_number_id',
            'fisherman_id',
            'insurance_no',
            'call_sign_no',
            'district',
            'division',
            'landing_site',
            'witness_name',
            'witness_address',
            'witness_nic',
            'witness_singing_date',
            'how_propelled',
            'engine_make',
            'fuel_type',
            'engine_type',
            'engine_horsepower',
            'engine_serial_number',
            'communication_equipment',
            'fishing_equipment',
            'navigation_equipment',
            'date_of_construction',
            'date_of_first_registration',
            'mea_report',
            'status',
            'approval_stage',
            'created',
            'approved_time',
            'expire_date',
            'renew',
        ],
    ]) ?>

</div>
