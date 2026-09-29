<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\ProfileOfficer;

/* @var $this yii\web\View */
/* @var $model backend\models\Kpi */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'KPIs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="kpi-view container-fluid py-4">

    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2 mb-0 text-gray-800"><?= Html::encode($this->title) ?></h1>
                <div>
                    <?php if (!Yii::$app->user->isGuest && in_array((int)Yii::$app->user->identity->type, [
                            \backend\config\Constant::ITD, // Evaluates explicitly to 15
                            \backend\config\Constant::AD,   // Evaluates explicitly to 3
                            \backend\config\Constant::DIRECTOR   // Evaluates explicitly to 19
                        ])): ?>
                        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->KPIId], ['class' => 'btn btn-primary px-3 shadow-sm mr-2']) ?>
                        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->KPIId], [
                            'class' => 'btn btn-danger px-3 shadow-sm mr-2',
                            'data' => [
                                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                'method' => 'post',
                            ],
                        ]) ?>
                    <?php endif; ?>

                    <?= Html::a(Yii::t('app', 'Back to List'), ['index'], ['class' => 'btn btn-secondary px-3 shadow-sm']) ?>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0"> <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-striped table-bordered detail-view mb-0'], // mb-0 ensures no extra bottom gaps
                        'attributes' => [
                            [
                                'attribute' => 'KPIId',
                                'label' => 'KPI ID',
                            ],
                            'title',
                            'indicator:ntext',
                            [
                                'attribute' => 'rational',
                                'label' => 'Rational / Justification',
                                'format' => 'ntext',
                            ],
                            'unit',
                            'target',
                            'progress',
                            'status',
                            'targetType',
                            'targetDate',
                            // Maps the dynamic relationship string name inside the data grid overview
                            [
                                'attribute' => 'divisionId',
                                'label' => 'Division',
                                'value' => function ($model) {
                                    return $model->division ? $model->division->divisionName : '(not set)';
                                },
                            ],
                            [
                                'attribute' => 'supervisor',
                                'label' => 'Supervisor',
                                'value' => function ($model) {
                                    if (!empty($model->supervisor)) {
                                        $profile = ProfileOfficer::find()
                                            ->alias('p')
                                            ->innerJoin('user u', 'u.profile_id = p.id')
                                            ->where(['u.id' => $model->supervisor])
                                            ->one();
                                            
                                        return $profile ? $profile->first_name . ' ' . $profile->last_name : 'Unknown Officer (' . $model->supervisor . ')';
                                    }
                                    return 'Not Assigned';
                                },
                            ],
                            [
                                'attribute' => 'responsibility',
                                'label' => 'Responsibility',
                                'value' => function ($model) {
                                    if (!empty($model->responsibility)) {
                                        $profile = ProfileOfficer::find()
                                            ->alias('p')
                                            ->innerJoin('user u', 'u.profile_id = p.id')
                                            ->where(['u.id' => $model->responsibility])
                                            ->one();
                                            
                                        return $profile ? $profile->first_name . ' ' . $profile->last_name : 'Unknown Officer (' . $model->responsibility . ')';
                                    }
                                    return 'Not Assigned';
                                },
                            ],
                            'createdDate',
                            'assignedDate',
                        ],
                    ]) ?>
                </div>
            </div>

        </div>
    </div>

</div>