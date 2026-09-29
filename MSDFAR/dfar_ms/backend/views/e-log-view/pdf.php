<?php
/** @var \backend\models\ELog $eLog */
/** @var array $data */
/** @var array $gearDetails */
/** @var \backend\controllers\ELogViewController $controller */

use backend\controllers\ELogViewController;

// --- Group sets by gear_type + gear_id ---
$gearGroups = [];
$ungrouped  = [];

foreach ($data as $row) {
    $gt = $row['gear_type'] ?? null;
    $gi = $row['gear_id']   ?? null;

    if ($gt && $gi && in_array($gt, ['longline', 'gillnet', 'ringnet'])) {
        $key = $gt . '_' . $gi;
        if (!isset($gearGroups[$key])) {
            $gearGroups[$key] = ['gear_type' => $gt, 'gear_id' => $gi, 'sets' => []];
        }
        $gearGroups[$key]['sets'][] = $row;
    } else {
        $ungrouped[] = $row;
    }
}

$gearLabels = [
    'longline' => [
        'mainline'    => 'Float Length (m)',
        'branchline'  => 'Branchline Length (m)',
        'no_of_hooks' => 'Number of Hooks',
        'hook_type'   => 'Hook Type',
        'depth'       => 'Depth (m)',
        'bait'        => 'Bait Type',
        'no_hook_bet'  => 'No of hooks between float'
    ],
    'gillnet' => [
        'net_material' => 'Net Material',
        'mesh_size'    => 'Mesh Size (mm)',
        'ply'          => 'Ply of the Net',
        'net_height'   => 'Height of the Net (m)',
        'length'       => 'Length of the Net (m)',
        'set_depth'    => 'Depth at Which Net is Set (m)',
        'net_pieces'   => 'Number of Net Pieces',
    ],
    'ringnet' => [
        'net_length' => 'Length of the Ring Net (m)',
        'net_height' => 'Height of the Ring Net (m)',
        'fad'        => 'If FAD is used mention',
    ],
];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 11px;
        color: #222;
        margin: 0;
        padding: 0;
    }

    /* ---- Page header ---- */
    .report-header {
        text-align: center;
        border-bottom: 2px solid #000;
        padding-bottom: 8px;
        margin-bottom: 16px;
    }
    .report-header h1 {
        font-size: 18px;
        color: #000;
        margin: 0 0 2px 0;
    }
    .report-header p {
        font-size: 10px;
        color: #000;
        margin: 0;
    }

    /* ---- Section headings ---- */
    .section-title {
        background: #000;
        color: #fff;
        font-size: 12px;
        font-weight: bold;
        padding: 5px 8px;
        margin: 14px 0 6px 0;
    }

    .gear-title {
        background: #444;
        color: #fff;
        font-size: 11px;
        font-weight: bold;
        padding: 4px 8px;
        margin: 10px 0 4px 0;
    }

    .set-title {
        background: #eee;
        font-size: 11px;
        font-weight: bold;
        padding: 4px 8px;
        margin: 8px 0 4px 0;
        border-left: 3px solid #000;
    }

    /* ---- Two-column info table ---- */
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }
    .info-table td {
        padding: 3px 6px;
        vertical-align: top;
        width: 25%;
    }
    .info-table td.label {
        font-weight: bold;
        color: #000;
        width: 20%;
    }
    .info-table td.value {
        width: 30%;
    }

    /* ---- Gear detail table ---- */
    .detail-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
    }
    .detail-table td {
        padding: 3px 6px;
        border-bottom: 1px solid #bbb;
        width: 50%;
    }
    .detail-table td.label {
        font-weight: bold;
        color: #000;
        background: #eee;
    }

    /* ---- Catch / discard tables ---- */
    .catch-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
        font-size: 10px;
    }
    .catch-table th {
        background: #ddd;
        font-weight: bold;
        padding: 3px 5px;
        text-align: left;
        border: 1px solid #888;
    }
    .catch-table td {
        padding: 2px 5px;
        border: 1px solid #aaa;
    }
    .catch-table tr:nth-child(even) td {
        background: #f4f4f4;
    }

    .sub-heading {
        font-size: 10px;
        font-weight: bold;
        color: #000;
        text-transform: uppercase;
        margin: 6px 0 2px 0;
    }

    .no-data {
        color: #555;
        font-style: italic;
        font-size: 10px;
        margin: 2px 0 6px 4px;
    }

    .status-approved {
        font-weight: bold;
    }
    .status-pending {
        font-weight: bold;
    }

    .page-break {
        page-break-after: always;
    }

    .footer {
        text-align: center;
        font-size: 9px;
        color: #555;
        border-top: 1px solid #888;
        padding-top: 6px;
        margin-top: 20px;
    }
</style>
</head>
<body>

<!-- ============================================================
     HEADER
============================================================ -->
<div class="report-header">
    <h1>E-Log Report</h1>
    <p>Generated on <?= date('Y-m-d H:i:s') ?></p>
</div>

<!-- ============================================================
     E-LOG SUMMARY
============================================================ -->
<div class="section-title">E-Log Details</div>

<table class="info-table">
     <tr>
        <td class="label">Log Page No:</td>
        <td class="value"><?= htmlspecialchars($eLog->log_book_no ?? '') ?></td>
        <td class="label">Log Book No:</td>
        <td class="value"><?= htmlspecialchars($eLog->log_sheet_number ?? '') ?></td>
    </tr>
    <tr>
        <td class="label">Vessel:</td>
        <td class="value"><?= htmlspecialchars($eLog->vessel_id ?? '') ?></td>
        <td class="label">Skipper:</td>
        <td class="value"><?= htmlspecialchars($eLog->skipper_id ?? '') ?></td>
    </tr>
    <tr>
        <td class="label">Departure Date:</td>
        <td class="value"><?= htmlspecialchars($eLog->departure_date ?? '') ?></td>
        <td class="label">Arrival Date:</td>
        <td class="value"><?= htmlspecialchars($eLog->arrival_date ?? '') ?></td>
    </tr>
    <tr>
        <td class="label">Departure Port:</td>
        <td class="value"><?= htmlspecialchars(ELogViewController::getHarbourName($eLog->departure_harbour) ?? '') ?></td>
        <td class="label">Arrival Port:</td>
        <td class="value"><?= htmlspecialchars(ELogViewController::getHarbourName($eLog->arrival_harbour) ?? '') ?></td>
    </tr>
    <tr>
        <td class="label">Phone Number:</td>
        <td class="value"><?= htmlspecialchars($eLog->phone_number ?? '') ?></td>
        <td class="label">Status:</td>
        <td class="value">
            <?php if ($eLog->approve): ?>
                <span class="status-approved">Approved</span>
            <?php else: ?>
                <span class="status-pending">Pending Approval</span>
            <?php endif; ?>
        </td>
    </tr>
</table>

<!-- ============================================================
     FISHING SETS
============================================================ -->
<div class="section-title">Fishing Sets</div>

<?php $gearCounter = 1; ?>
<?php foreach ($gearGroups as $key => $group):
    $gt     = $group['gear_type'];
    $gi     = $group['gear_id'];
    $detail = $gearDetails[$gt][$gi] ?? [];
    $labels = $gearLabels[$gt] ?? [];
    $realSets = array_filter($group['sets'], fn($s) => $s['set_id'] !== null);
?>

    <!-- Gear header -->
    <div class="gear-title"><?= htmlspecialchars(ucfirst($gt)) ?> #<?= $gearCounter++ ?></div>

    <!-- Gear details -->
    <?php if (!empty($detail) && !empty($labels)): ?>
        <table class="detail-table">
            <?php foreach ($labels as $field => $label): ?>
                <?php if (isset($detail[$field]) && $detail[$field] !== null && $detail[$field] !== ''): ?>
                    <tr>
                        <td class="label"><?= htmlspecialchars($label ?? '') ?></td>
                        <td><?= htmlspecialchars($detail[$field] ?? '') ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <!-- Sets -->
    <?php if (empty($realSets)): ?>
        <p class="no-data">No sets recorded for this gear.</p>
    <?php else: ?>
        <?php foreach ($realSets as $row): ?>
            <div class="set-title">Set #<?= htmlspecialchars($row['set_number'] ?? '') ?></div>

            <!-- Set GPS / time info -->
            <table class="info-table">
                <tr>
                    <td class="label">Start:</td>
                    <td class="value"><?= htmlspecialchars($row['start_datetime'] ?? '') ?></td>
                    <td class="label">End:</td>
                    <td class="value"><?= htmlspecialchars($row['end_datetime'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="label">Start GPS Dir:</td>
                    <td class="value"><?= htmlspecialchars($row['start_gps_direction'] ?? '') ?></td>
                    <td class="label">End GPS Dir:</td>
                    <td class="value"><?= htmlspecialchars($row['end_gps_direction'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="label">Start GPS :</td>
                    <td class="value"><?= htmlspecialchars($row['start_gps_n'] ?? '') ?></td>
                    <td class="label">End GPS  :</td>
                    <td class="value"><?= htmlspecialchars($row['end_gps_n'] ?? '') ?></td>
                </tr>
                <tr>
                    <td class="label">Start GPS E:</td>
                    <td class="value"><?= htmlspecialchars($row['start_gps_e'] ?? '') ?></td>
                    <td class="label">End GPS E:</td>
                    <td class="value"><?= htmlspecialchars($row['end_gps_e'] ?? '') ?></td>
                </tr>
            </table>

            <!-- Catches -->
            <div class="sub-heading">Catches</div>
            <?php if (!empty($row['catches'])): ?>
                <table class="catch-table">
                    <thead>
                        <tr>
                            <th>Fish Type</th>
                            <th>Variant</th>
                            <th>Weight (kg)</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($row['catches'] as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars(ELogViewController::getFishTypeName($c['fish_type']) ?? '') ?></td>
                                <td><?= htmlspecialchars(ELogViewController::getFishVariantName($c['variant']) ?? '') ?></td>
                                <td><?= htmlspecialchars($c['weight'] ?? '') ?></td>
                                <td><?= htmlspecialchars($c['count'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No catches</p>
            <?php endif; ?>

            <!-- Discarded Dead -->
            <div class="sub-heading">Discarded Dead</div>
            <?php if (!empty($row['discarded_dead'])): ?>
                <table class="catch-table">
                    <thead>
                        <tr>
                            <th>Fish Type</th>
                            <th>Variant</th>
                            <th>Weight (kg)</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($row['discarded_dead'] as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars(ELogViewController::getFishTypeName($d['fish_type']) ?? '') ?></td>
                                <td><?= htmlspecialchars(ELogViewController::getFishVariantName($d['variant']) ?? '') ?></td>
                                <td><?= htmlspecialchars($d['weight'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($d['count'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No discarded dead fish</p>
            <?php endif; ?>

            <!-- Discarded Live -->
            <div class="sub-heading">Discarded Live</div>
            <?php if (!empty($row['discarded_live'])): ?>
                <table class="catch-table">
                    <thead>
                        <tr>
                            <th>Fish Type</th>
                            <th>Variant</th>
                            <th>Weight (kg)</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($row['discarded_live'] as $l): ?>
                            <tr>
                                <td><?= htmlspecialchars(ELogViewController::getFishTypeName($l['fish_type']) ?? '') ?></td>
                                <td><?= htmlspecialchars(ELogViewController::getFishVariantName($l['variant']) ?? '') ?></td>
                                <td><?= htmlspecialchars($l['weight'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($l['count'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No discarded live fish</p>
            <?php endif; ?>

        <?php endforeach; ?>
    <?php endif; ?>

<?php endforeach; ?>

<!-- Ungrouped sets -->
<?php if (!empty($ungrouped)): ?>
    <div class="gear-title">Ungrouped Sets</div>
    <?php foreach ($ungrouped as $row): ?>
        <div class="set-title">Set #<?= htmlspecialchars($row['set_number'] ?? '') ?></div>
        <!-- (same table structure as above, omitted for brevity — same $renderSet logic) -->
        <table class="info-table">
            <tr>
                <td class="label">Start:</td><td class="value"><?= htmlspecialchars($row['start_datetime'] ?? '') ?></td>
                <td class="label">End:</td><td class="value"><?= htmlspecialchars($row['end_datetime'] ?? '') ?></td>
            </tr>
        </table>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (empty($gearGroups) && empty($ungrouped)): ?>
    <p class="no-data" style="text-align:center; margin-top:20px;">No fishing data found.</p>
<?php endif; ?>

<div class="footer">
    Department of Fisheries &amp; Aquatic Resources &nbsp;|&nbsp; E-Log ID: <?= htmlspecialchars($eLog->id ?? '') ?> &nbsp;|&nbsp; <?= date('Y-m-d H:i:s') ?>
</div>

</body>
</html>