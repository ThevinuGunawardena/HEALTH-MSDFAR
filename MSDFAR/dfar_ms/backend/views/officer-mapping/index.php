<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $divisionsList */

$this->title = 'Officer KPI Division Mapping';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="officer-mapping-index container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted small">Quickly link master KPI Divisions to individual official accounts.</p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover table-borderless mb-0 align-middle'],
                'layout' => "{items}\n<div class='p-3 d-flex justify-content-between align-items-center'>{summary}{pager}</div>",
                'columns' => [
                    [
                        'attribute' => 'id',
                        'label' => 'Profile ID',
                        'headerOptions' => ['style' => 'width: 100px; padding-left: 20px;'],
                        'contentOptions' => ['style' => 'padding-left: 20px; font-weight: 600;'],
                    ],
                    [
                        'label' => 'Officer Name',
                        'value' => function ($model) {
                            return trim($model->first_name . ' ' . $model->last_name);
                        },
                    ],
                    [
                        'attribute' => 'kpi_division_id',
                        'label' => 'Assigned KPI Division / District',
                        'format' => 'raw',
                        'value' => function ($model) use ($divisionsList) {
                            // Render a small discrete inline assignment form button block for each row
                            $formHtml = Html::beginForm(['assign-division', 'id' => $model->id], 'post', ['class' => 'd-flex align-items-center']);
                            
                            $formHtml .= Html::dropDownList(
                                'kpi_division_id', 
                                $model->kpi_division_id, 
                                $divisionsList, 
                                [
                                    'class' => 'form-control custom-select form-control-sm mr-2',
                                    'style' => 'max-width: 320px;',
                                    'prompt' => '-- Unassigned / Select Division --'
                                ]
                            );
                            
                            $formHtml .= Html::submitButton('<i class="fas fa-save"></i> Save', [
                                'class' => 'btn btn-sm btn-success px-3 shadow-sm'
                            ]);
                            
                            $formHtml .= Html::endForm();
                            return $formHtml;
                        }
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>

<?php
// Inject simple styling to support full alignment layout normalization
$this->registerCss("
    .align-middle td {
        vertical-align: middle !important;
    }
");
?>