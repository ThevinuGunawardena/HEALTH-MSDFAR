<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Kpi $model */
/** @var array $officerList */
/** @var array $divisions */

$this->title = Yii::t('app', 'Assign KPI: {name}', [
    'name' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'KPIs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->KPIId]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Assign');
?>

<div class="kpi-update container-fluid py-3">

    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-plus-circle mr-2"></i><?= Html::encode($this->title) ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                <?= $this->render('_assignForm', [
                    'model' => $model,
                    'officerList' => $officerList,
                    'divisions' => $divisions,
                ]) ?>
                </div>
            </div>

        </div>
    </div>

</div>