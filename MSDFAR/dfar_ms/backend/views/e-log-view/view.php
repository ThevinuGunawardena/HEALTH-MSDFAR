<?php

use yii\helpers\Html;
use backend\controllers\ELogViewController;
use backend\config\Constant;
use backend\config\UserTypeUtil;

/** @var \yii\web\View $this */
/** @var \backend\models\ELog $eLog */
/** @var array $data */
/** @var array $gearDetails */
/** @var int $userType */

$this->title = 'E-Log View';
$this->params['breadcrumbs'][] = $this->title;

$userType = $userType ?? null;

// --- Role checks using UserTypeUtil to handle comma-separated types ---


$isAdminUser = \backend\config\UserTypeUtil::hasType(Constant::AD_Highseas) || \backend\config\UserTypeUtil::hasType(Constant::ITD);
$isHarbourOfficer = \backend\config\UserTypeUtil::hasType(Constant::HARBOUR_OFFICER);

// 24 hour window only applies to harbour officer
$withinEditWindow = false;
if ($isHarbourOfficer && !empty($eLog->created_at)) {
    $withinEditWindow = (time() - strtotime($eLog->created_at)) < 86400;
}

// Admins can always edit, harbour officer only within 24 hours
$canEdit = $isAdminUser || $withinEditWindow;

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

// --- Label maps for gear fields ---
$gearLabels = [
    'longline' => [
        'mainline'     => 'Float Length (m)',
        'branchline'   => 'Branchline Length (m)',
        'no_of_hooks'  => 'Number of Hooks',
        'hook_type'    => 'Hook Type',
        'depth'        => 'Depth (m)',
        'bait'         => 'Bait Type',
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

$renderSet = function ($row) use ($eLog, $userType, $canEdit) { ?>
    <div class="card mb-3">
        <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
            <strong>Set #<?= Html::encode($row['set_number']) ?></strong>
            <div class="d-flex gap-2">
                <?php if ($canEdit): ?>
                    <?= Html::a('Edit Set', ['e-log-view/edit-set', 'id' => $row['set_id'], 'elogId' => $eLog->id], ['class' => 'btn btn-info btn-sm']) ?>
                    <?= Html::a('Delete Set', ['e-log-view/delete-set', 'id' => $row['set_id'], 'elogId' => $eLog->id], [
                        'class' => 'btn btn-danger btn-sm',
                        'data'  => [
                            'confirm' => 'Are you sure you want to delete this set?',
                            'method'  => 'post',
                        ],
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body pb-2">
            <div class="row mb-3">
                <div class="col-6"><strong>Start:</strong> <?= Html::encode($row['start_datetime']) ?></div>
                <div class="col-6"><strong>End:</strong> <?= Html::encode($row['end_datetime']) ?></div>
                <div class="col-6"><strong>Start GPS Direction:</strong> <?= Html::encode($row['start_gps_direction']) ?></div>
                <div class="col-6"><strong>Start GPS :</strong> <?= Html::encode($row['start_gps_n']) ?></div>
                <div class="col-6"><strong>Start GPS E:</strong> <?= Html::encode($row['start_gps_e']) ?></div>
                <div class="col-6"><strong>End GPS Direction:</strong> <?= Html::encode($row['end_gps_direction']) ?></div>
                <div class="col-6"><strong>End GPS :</strong> <?= Html::encode($row['end_gps_n']) ?></div>
                <div class="col-6"><strong>End GPS E:</strong> <?= Html::encode($row['end_gps_e']) ?></div>
            </div>

            <h6 class="text-uppercase text-muted fw-bold mb-2">Catches</h6>
            <?php if (!empty($row['catches'])): ?>
                <ul class="list-group list-group-flush mb-3">
                    <?php foreach ($row['catches'] as $c): ?>
                        <li class="list-group-item px-0">
                            <span class="me-3"><strong>Type:</strong> <?= ELogViewController::getFishTypeName($c['fish_type']) ?></span>
                            <span class="me-3"><strong>Variant:</strong> <?= ELogViewController::getFishVariantName($c['variant']) ?></span>
                            <span class="me-3"><strong>Weight:</strong> <?= Html::encode($c['weight']) ?> kg</span>
                            <span><strong>Count:</strong> <?= Html::encode($c['count']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted mb-3">No catches</p>
            <?php endif; ?>

            <h6 class="text-uppercase text-muted fw-bold mb-2">Discarded Dead</h6>
            <?php if (!empty($row['discarded_dead'])): ?>
                <ul class="list-group list-group-flush mb-3">
                    <?php foreach ($row['discarded_dead'] as $d): ?>
                        <li class="list-group-item px-0">
                            <span class="me-3"><strong>Type:</strong> <?= ELogViewController::getFishTypeName($d['fish_type']) ?></span>
                            <span class="me-3"><strong>Variant:</strong> <?= ELogViewController::getFishVariantName($d['variant']) ?></span>
                            <span class="me-3"><strong>Weight:</strong> <?= $d['weight'] ?? '-' ?></span>
                            <span><strong>Count:</strong> <?= $d['count'] ?? '-' ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted mb-3">No discarded dead fish</p>
            <?php endif; ?>

            <h6 class="text-uppercase text-muted fw-bold mb-2">Discarded Live</h6>
            <?php if (!empty($row['discarded_live'])): ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($row['discarded_live'] as $l): ?>
                        <li class="list-group-item px-0">
                            <span class="me-3"><strong>Type:</strong> <?= ELogViewController::getFishTypeName($l['fish_type']) ?></span>
                            <span class="me-3"><strong>Variant:</strong> <?= ELogViewController::getFishVariantName($l['variant']) ?></span>
                            <span class="me-3"><strong>Weight:</strong> <?= $l['weight'] ?? '-' ?></span>
                            <span><strong>Count:</strong> <?= $l['count'] ?? '-' ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted mb-0">No discarded live fish</p>
            <?php endif; ?>
        </div>
    </div>
<?php };
?>

<div class="e-log-view container py-4">

    <div class="text-end mb-3">
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
    <?= Html::a(
        'Print',
        ['e-log-view/export-pdf', 'id' => $eLog->id, 'print' => 1],
        [
            'class'  => 'btn btn-outline-secondary',
            'target' => '_blank',
            'title'  => 'Open PDF in browser and print',
        ]
    ) ?>
 
    </div>

    <h3 class="text-center mb-4">E-Log Details</h3>

    <!-- E-Log Header -->
    <div class="card mb-4 mx-auto" style="max-width: 720px;">
        <div class="card-body">
            <div class="row">
                <div class="col-6 mb-2"><strong>Log Page No:</strong> <?= Html::encode($eLog->log_book_no) ?></div>
                <div class="col-6 mb-2"><strong>Log Book No:</strong> <?= Html::encode($eLog->log_sheet_number) ?></div>
                <div class="col-6 mb-2"><strong>Vessel:</strong> <?= Html::encode($eLog->vessel_id) ?></div>
                <div class="col-6 mb-2"><strong>Skipper:</strong> <?= Html::encode($eLog->skipper_id) ?></div>
                <div class="col-6 mb-2"><strong>Arrival Date:</strong> <?= Html::encode($eLog->arrival_date) ?></div>
                <div class="col-6 mb-2"><strong>Departure Date:</strong> <?= Html::encode($eLog->departure_date) ?></div>
                <div class="col-6 mb-2"><strong>Departure Port:</strong> <?= ELogViewController::getharbourname($eLog->departure_harbour) ?></div>
                <div class="col-6 mb-2"><strong>Arrival Port:</strong> <?= ELogViewController::getharbourname($eLog->arrival_harbour) ?></div>
                <div class="col-6 mb-2"><strong>Phone Number:</strong> <?= Html::encode($eLog->phone_number) ?></div>
                <div class="col-6 mb-2">
                    <strong>Status:</strong>
                    <?= $eLog->approve ? 'Approved' : 'Pending Approval' ?>
                </div>
            </div>

            <div class="text-end mt-3">
                <?php if ($isAdminUser || $isHarbourOfficer): ?>
                    <?php if (!$eLog->approve): ?>
                        <?= Html::a('Approve', ['e-log-view/approve', 'id' => $eLog->id], [
                            'class' => 'btn btn-success',
                            'data'  => ['confirm' => 'Approve this E-Log?', 'method' => 'post'],
                        ]) ?>
                    <?php else: ?>
                        <?= Html::a('Disapprove', ['e-log-view/disapprove', 'id' => $eLog->id], [
                            'class' => 'btn btn-danger',
                            'data'  => ['confirm' => 'Disapprove this E-Log?', 'method' => 'post'],
                        ]) ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($canEdit): ?>
                    <?= Html::a('Edit E-Log', ['e-log-view/edit-elog', 'id' => $eLog->id], ['class' => 'btn btn-warning']) ?>
                    <?= Html::a('Delete E-Log', ['e-log-view/delete-elog', 'id' => $eLog->id], [
                        'class' => 'btn btn-danger',
                        'data'  => ['confirm' => 'Are you sure you want to delete this E-Log?', 'method' => 'post'],
                    ]) ?>
                <?php elseif ($isHarbourOfficer): ?>
                    <span class="text-muted small fst-italic">Edit window expired (24 hours passed)</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h3 class="text-center mb-3">Fishing Sets</h3>

    <?php if (!empty($gearGroups)): ?>
        <?php $gearCounter = 1; ?>
        <?php foreach ($gearGroups as $key => $group):
            $gt     = $group['gear_type'];
            $gi     = $group['gear_id'];
            $detail = $gearDetails[$gt][$gi] ?? [];
            $labels = $gearLabels[$gt] ?? [];
            $realSets = array_filter($group['sets'], fn($s) => $s['set_id'] !== null);
        ?>
            <div class="card mb-4 mx-auto" style="max-width: 720px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-2"><?= ucfirst($gt) ?> #<?= $gearCounter++ ?></h5>

                    <?php if ($canEdit): ?>
                        <?php if ($gt == 'longline'): ?>
                            <div class="text-end mb-3">
                                <?= Html::a('Add a Set', ['e-log-edit/setsview', 'id' => $eLog->id, 'type' => $gt, 'gearId' => $gi, 'page' => true], ['class' => 'btn btn-success btn-sm']) ?>
                                <?= Html::a('Edit Longline', ['e-log-view/edit-longline', 'id' => $gi, 'elogId' => $eLog->id], ['class' => 'btn btn-info btn-sm']) ?>
                                <?= Html::a('Delete Longline', ['e-log-edit/delete-longline', 'id' => $gi, 'elogId' => $eLog->id, 'type' => true], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data'  => ['confirm' => 'Are you sure you want to delete this longline?', 'method' => 'post'],
                                ]) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($gt == 'gillnet'): ?>
                            <div class="text-end mb-3">
                                <?= Html::a('Add a Set', ['e-log-edit/setsview', 'id' => $eLog->id, 'type' => $gt, 'gearId' => $gi, 'page' => true], ['class' => 'btn btn-success btn-sm']) ?>
                                <?= Html::a('Edit Gillnet', ['e-log-view/edit-gillnet', 'id' => $gi, 'elogId' => $eLog->id], ['class' => 'btn btn-info btn-sm']) ?>
                                <?= Html::a('Delete Gillnet', ['e-log-edit/delete-gillnet', 'id' => $gi, 'elogId' => $eLog->id, 'type' => true], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data'  => ['confirm' => 'Are you sure you want to delete this gillnet?', 'method' => 'post'],
                                ]) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($gt == 'ringnet'): ?>
                            <div class="text-end mb-3">
                                <?= Html::a('Add a Set', ['e-log-edit/setsview', 'id' => $eLog->id, 'type' => $gt, 'gearId' => $gi, 'page' => true], ['class' => 'btn btn-success btn-sm']) ?>
                                <?= Html::a('Edit Ringnet', ['e-log-view/edit-ringnet', 'id' => $gi, 'elogId' => $eLog->id], ['class' => 'btn btn-info btn-sm']) ?>
                                <?= Html::a('Delete Ringnet', ['e-log-edit/delete-ringnet', 'id' => $gi, 'elogId' => $eLog->id, 'type' => true], [
                                    'class' => 'btn btn-danger btn-sm',
                                    'data'  => ['confirm' => 'Are you sure you want to delete this ringnet?', 'method' => 'post'],
                                ]) ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="card-body border-bottom">
                    <?php if (!empty($detail) && !empty($labels)): ?>
                        <dl class="row mb-0">
                            <?php foreach ($labels as $field => $label): ?>
                                <?php if (isset($detail[$field]) && $detail[$field] !== null && $detail[$field] !== ''): ?>
                                    <dt class="col-6 text-muted"><?= Html::encode($label) ?></dt>
                                    <dd class="col-6"><?= Html::encode($detail[$field]) ?></dd>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </dl>
                    <?php else: ?>
                        <p class="text-muted mb-0">No gear details available.</p>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <h6 class="text-uppercase text-muted fw-bold mb-3">Sets (<?= count($realSets) ?>)</h6>
                    <?php if (empty($realSets)): ?>
                        <p class="text-muted mb-0">No sets recorded for this gear.</p>
                    <?php else: ?>
                        <?php foreach ($realSets as $row): ?>
                            <?= $renderSet($row) ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Ungrouped Sets -->
    <?php if (!empty($ungrouped)): ?>
        <div class="card mb-4 mx-auto" style="max-width: 720px;">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Ungrouped Sets</h5>
            </div>
            <div class="card-body">
                <?php foreach ($ungrouped as $row): ?>
                    <?= $renderSet($row) ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($gearGroups) && empty($ungrouped)): ?>
        <p class="text-center text-muted">No fishing data found.</p>
    <?php endif; ?>

</div>