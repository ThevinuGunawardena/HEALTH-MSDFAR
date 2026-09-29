<?php

/** @var yii\web\View $this */
/** @var string $form */
/** @var string $periodDate */
/** @var array $byDistrict districtId => ['name'=>.., 'rows'=>[['name'=>.., 'values'=>[]]], 'totals'=>[]] */
/** @var array $headers */
/** @var array $grandTotals */

use yii\helpers\Html;

$fmt = function ($v) {
    return is_float($v) ? number_format($v, 2) : Html::encode($v);
};
?>

<div class="bsc-national-report">

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <div>
            <h4><?= Html::encode(strtoupper($form)) ?> National Report — <?= Html::encode(date('F Y', strtotime($periodDate))) ?></h4>
            <p class="text-muted mb-0 small">Validated data only. Grouped by district, with an island-wide total at the bottom.</p>
        </div>
        <div>
            <form method="get" class="form-inline d-inline-block mr-2">
                <input type="hidden" name="form" value="<?= Html::encode($form) ?>">
                <input type="month" name="period" class="form-control form-control-sm mr-2"
                       value="<?= Html::encode(date('Y-m', strtotime($periodDate))) ?>">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Go</button>
            </form>
            <button class="btn btn-outline-secondary mr-2" onclick="window.print()">🖨️ Print</button>
            <?= Html::a('⬇️ Download Excel', [
                'export-excel', 'form' => $form, 'period' => date('Y-m', strtotime($periodDate)),
            ], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <h5 class="d-none d-print-block text-center mb-3">
        Department of Fisheries &amp; Aquatic Resources<br>
        <?= Html::encode(strtoupper($form)) ?> National Report — <?= Html::encode(date('F Y', strtotime($periodDate))) ?><br>
        <small>Validated data only</small>
    </h5>

    <?php if (empty($byDistrict)): ?>
        <div class="alert alert-info">No validated <?= Html::encode(strtoupper($form)) ?> returns found for this period yet.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-sm bsc-report-table">
                <thead class="thead-light">
                <tr>
                    <th>District / F.I. Division</th>
                    <?php foreach ($headers as $h): ?><th class="text-right"><?= Html::encode($h) ?></th><?php endforeach; ?>
                </tr>
                </thead>
                    <tbody>
                        <?php foreach ($byDistrict as $district): ?>

                            <tr class="table-secondary font-weight-bold">
                                <td>
                                    <?= Html::encode($district['name']) ?> — District Total

                                    <?= Html::a(
                                        'View Details',
                                        [
                                            'district',
                                            'form' => $form,
                                            'district' => $district['id'],
                                            'period' => date('Y-m', strtotime($periodDate)),
                                        ],
                                        ['class' => 'btn btn-sm btn-outline-primary ml-2']
                                    ) ?>
                                </td>

                                <?php foreach ($district['totals'] as $v): ?>
                                    <td class="text-right"><?= $fmt($v) ?></td>
                                <?php endforeach; ?>
                            </tr>
                            
                        <?php endforeach; ?>

                        <tr class="table-dark text-dark font-weight-bold">
                            <td>ISLAND-WIDE TOTAL</td>

                            <?php foreach ($grandTotals as $v): ?>
                                <td class="text-right"><?= $fmt($v) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<style>
@media print {
    .no-print, .dashboard-header, .nav-left-sidebar, .footer, .page-breadcrumb { display: none !important; }
    .dashboard-wrapper, .dashboard-content, .container-fluid { margin: 0 !important; padding: 0 !important; }
    table { font-size: 11px; }
}
</style>