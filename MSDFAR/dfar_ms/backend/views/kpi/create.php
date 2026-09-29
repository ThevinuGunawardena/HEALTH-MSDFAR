<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Kpi */

$this->title = Yii::t('app', 'Create KPI');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'KPIs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-create container-fluid py-3">

    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-plus-circle mr-2"></i><?= Html::encode($this->title) ?>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?= $this->render('_form', [
                        'model' => $model,
                        'divisions' => $divisions,
                    ]) ?>
                </div>
            </div>

        </div>
    </div>

</div>