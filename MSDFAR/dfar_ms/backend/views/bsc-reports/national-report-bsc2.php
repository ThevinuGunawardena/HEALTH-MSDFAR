<?php

/** @var yii\web\View $this */
/** @var string $form */
/** @var string $periodDate */
/** @var array $byDistrict */
/** @var array $headers */
/** @var array $grandTotals */

$this->title = 'BSC-2 National Report';
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('_national-report-table', [
    'form' => $form,
    'periodDate' => $periodDate,
    'byDistrict' => $byDistrict,
    'headers' => $headers,
    'grandTotals' => $grandTotals,
]);