<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap4\LinkPager;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $rows array */
/* @var $harbours array */
/* @var $fishVariants array */
/* @var $years array */
/* @var $months array */
/* @var $fiveByFiveOptions array */
/* @var $oneByOneOptions array */
/* @var $filters array */

$this->title = 'E-Log Catch Report';
$this->params['breadcrumbs'][] = ['label' => 'E-Log Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Group totals by fish variant
$totals = [];
foreach ($rows as $row) {
    $key = $row['fish_variant'] ?? 'Unknown';
    if (!isset($totals[$key])) {
        $totals[$key] = ['weight' => 0, 'count' => 0];
    }
    $totals[$key]['weight'] += (float) $row['weight'];
    $totals[$key]['count']  += (int) $row['fish_count'];
}
ksort($totals);
?>

<div class="container mt-3 e-log-view">

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="glyphicon glyphicon-filter"></i> Filters</h5>
        </div>
        <div class="card-body">
            <?php $formFilter = ActiveForm::begin([
                'method' => 'get',
                'action' => Url::to(['e-log-view/report-view']),
                'options' => ['class' => 'row g-3'],
            ]); ?>

            <div class="col-md-3">
                <label class="form-label">Departure Harbour</label>
                <?= Html::dropDownList(
                    'departure_harbour',
                    $filters['departure_harbour'],
                    $harbours,
                    ['class' => 'form-select', 'prompt' => 'Any']
                ) ?>
            </div>

            <div class="col-md-3">
                <label class="form-label">Arrival Harbour</label>
                <?= Html::dropDownList(
                    'arrival_harbour',
                    $filters['arrival_harbour'],
                    $harbours,
                    ['class' => 'form-select', 'prompt' => 'Any']
                ) ?>
            </div>

            <div class="col-md-3">
                <label class="form-label">Fish Variant</label>
                <?= Html::dropDownList(
                    'fish_variant_id',
                    $filters['fish_variant_id'],
                    $fishVariants,
                    ['class' => 'form-select', 'prompt' => 'Any']
                ) ?>
            </div>

            <div class="col-md-3">
                <label class="form-label">Five by Five</label>
                <?= Html::dropDownList(
                    'five_by_five',
                    $filters['five_by_five'],
                    $fiveByFiveOptions,
                    ['class' => 'form-select', 'prompt' => 'Any']
                ) ?>
            </div>

            <div class="col-md-3">
                <label class="form-label">One by One</label>
                <?= Html::dropDownList(
                    'one_by_one',
                    $filters['one_by_one'],
                    $oneByOneOptions,
                    ['class' => 'form-select', 'prompt' => 'Any']
                ) ?>
            </div>

            <div class="col-12"><hr class="my-2"></div>

            <div class="col-12"><strong>Departure Date</strong></div>

            <div class="col-md-3">
                <label class="form-label">Year</label>
                <?= Html::dropDownList(
                    'departure_year',
                    $filters['departure_year'],
                    array_combine($years, $years),
                    ['class' => 'form-select', 'prompt' => 'Any']
                ) ?>
            </div>

            <div class="col-md-3">
                <label class="form-label">Month</label>
                <?= Html::dropDownList(
                    'departure_month',
                    $filters['departure_month'],
                    $months,
                    ['class' => 'form-select', 'prompt' => 'Any']
                ) ?>
            </div>

            <div class="col-md-3">
                <label class="form-label">Exact Date</label>
                <?= Html::input('date', 'departure_date', $filters['departure_date'], ['class' => 'form-control']) ?>
            </div>

            <div class="col-12"><hr class="my-2"></div>

            <div class="col-12"><strong>Arrival Date</strong></div>

            <div class="col-md-3">
                <label class="form-label">Year</label>
                <?= Html::dropDownList(
                    'arrival_year',
                    $filters['arrival_year'],
                    array_combine($years, $years),
                    ['class' => 'form-select', 'prompt' => 'Any']
                ) ?>
            </div>

            <div class="col-md-3">
                <label class="form-label">Month</label>
                <?= Html::dropDownList(
                    'arrival_month',
                    $filters['arrival_month'],
                    $months,
                    ['class' => 'form-select', 'prompt' => 'Any']
                ) ?>
            </div>

            <div class="col-md-3">
                <label class="form-label">Exact Date</label>
                <?= Html::input('date', 'arrival_date', $filters['arrival_date'], ['class' => 'form-control']) ?>
            </div>

            <div class="col-12 mt-3">
                <?= Html::submitButton('Apply Filters', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Reset', ['e-log-view/report-view'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- TOTALS SUMMARY -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Totals by Fish Variant</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fish Variant</th>
                        <th>Total Weight (kg)</th>
                        <th>Total Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($totals)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">No data.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($totals as $variant => $sum): ?>
                            <tr>
                                <td><?= Html::encode($variant) ?></td>
                                <td><?= number_format($sum['weight'], 2) ?></td>
                                <td><?= $sum['count'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- DETAIL ROWS -->
    <!-- DETAIL ROWS -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Catch Details</h5>
            <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['attribute' => 'e_log_id', 'label' => 'E-Log ID'],
                    ['attribute' => 'vessel_id', 'label' => 'Vessel'],
                    ['attribute' => 'fish_variant', 'label' => 'Fish Variant'],
                    ['attribute' => 'weight', 'label' => 'Weight (kg)'],
                    ['attribute' => 'fish_count', 'label' => 'Qty'],
                ],
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                ],
                'dropdownOptions' => [
                    'label' => 'Export',
                    'class' => 'btn btn-outline-secondary btn-sm',
                ],
                'showColumnSelector' => false,
                'container' => ['class' => 'mb-0'],
            ]); ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'pager' => [
                        'class' => LinkPager::class,
                        'firstPageLabel' => 'First',
                        'lastPageLabel' => 'Last',
                    ],
                    'tableOptions' => ['class' => 'table table-bordered table-striped mb-0'],
                    'headerRowOptions' => ['class' => 'primary'],
                    'columns' => [
                        [
                            'attribute' => 'e_log_id',
                            'label' => 'E-Log ID',
                            'format' => 'raw',
                            'value' => function ($row) {
                                return Html::a(
                                    Html::encode($row['e_log_id']),
                                    ['e-log-view/view', 'id' => $row['e_log_id']]
                                );
                            },
                        ],
                        [
                            'attribute' => 'vessel_id',
                            'label' => 'Vessel',
                        ],
                        [
                            'attribute' => 'fish_variant',
                            'label' => 'Fish Variant',
                        ],
                        [
                            'attribute' => 'weight',
                            'label' => 'Weight (kg)',
                        ],
                        [
                            'attribute' => 'fish_count',
                            'label' => 'Qty',
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>

</div>

</div>