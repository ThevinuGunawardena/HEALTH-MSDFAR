<?php

use yii\helpers\Html;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ELogTemp;

/** @var \yii\web\View $this */
/** @var \backend\models\ELogTemp $model */
/** @var array $setsData */
/** @var bool $isAdminUser */
/** @var bool $isHarbourOfficer */
/** @var bool $canEdit */

$this->title = 'E-Log Temp View';
$this->params['breadcrumbs'][] = $this->title;

// Field labels per gear type, matching what's stored directly on ELogTemp
$gearLabels = [
    ELogTemp::GEAR_RINGNET => [
        'net_length' => 'Length of the Ring Net (m)',
        'net_height' => 'Height of the Net (m)',
        'fad'        => 'If FAD is used mention',
    ],
    ELogTemp::GEAR_LONGLINE => [
        'mainline'    => 'Float Line Length (m)',
        'branchline'  => 'Branch Line Length (m)',
        'no_of_hooks' => 'Number of Hooks',
        'hook_type'   => 'Hook Type',
        'depth'       => 'Depth (m)',
        'bait'        => 'Bait Type',
        'no_hook_bet' => 'No of Hooks Between Float',
    ],
    ELogTemp::GEAR_GILLNET => [
        'net_material' => 'Net Material',
        'mesh_size'    => 'Mesh Size (mm)',
        'ply'          => 'Ply of the Net',
        'net_height'   => 'Height of the Net (m)',
        'set_depth'    => 'Depth at Which Net is Set (m)',
        'length'       => 'Length of the Net (m)',
        'net_pieces'   => 'Number of Net Pieces',
    ],
];

$labels = $gearLabels[$model->gear_type] ?? [];
?>

<div class="e-log-temp-view container py-4">

    <div class="text-end mb-3">
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <h3 class="text-center mb-4">E-Log Details</h3>

    <!-- Header -->
    <div class="card mb-4 mx-auto" style="max-width: 720px;">
        <div class="card-body">
            <div class="row">
                <div class="col-6 mb-2"><strong>Vessel:</strong> <?= Html::encode($model->vessel_id) ?></div>
                <div class="col-6 mb-2"><strong>Gear Type:</strong> <?= Html::encode(ELogTemp::gearTypeList()[$model->gear_type] ?? $model->gear_type) ?></div>
                <div class="col-6 mb-2"><strong>Arrival Date:</strong> <?= Html::encode($model->arrival_date) ?></div>
                <div class="col-6 mb-2"><strong>Departure Date:</strong> <?= Html::encode($model->departure_date) ?></div>
                <div class="col-6 mb-2"><strong>Arrival Harbour:</strong> <?= Html::encode($model->arrivalHarbour->Name ?? 'N/A') ?></div>
                <div class="col-6 mb-2"><strong>Departure Harbour:</strong> <?= Html::encode($model->departureHarbour->Name ?? 'N/A') ?></div>
                <div class="col-6 mb-2"><strong>Phone Number:</strong> <?= Html::encode($model->phone_number) ?></div>
                <div class="col-6 mb-2">
                    <strong>Status:</strong>
                    <?= $model->approve ? 'Approved' : 'Pending Approval' ?>
                </div>
            </div>

            <div class="text-end mt-3">
                <?php if ($isAdminUser || $isHarbourOfficer): ?>
                    <?php if (!$model->approve): ?>
                        <?= Html::a('Approve', ['e-log-temp/approve', 'id' => $model->id], [
                            'class' => 'btn btn-success',
                            'data'  => ['confirm' => 'Approve this E-Log?', 'method' => 'post'],
                        ]) ?>
                    <?php else: ?>
                        <?= Html::a('Disapprove', ['e-log-temp/disapprove', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data'  => ['confirm' => 'Disapprove this E-Log?', 'method' => 'post'],
                        ]) ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($canEdit): ?>
                    <?= Html::a('Edit E-Log', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
                    <?= Html::a('Delete E-Log', ['delete-elog', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data'  => ['confirm' => 'Are you sure you want to delete this E-Log?', 'method' => 'post'],
                    ]) ?>
                <?php elseif ($isHarbourOfficer): ?>
                    <span class="text-muted small fst-italic">Edit window expired (24 hours passed)</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Gear details -->
    <div class="card mb-4 mx-auto" style="max-width: 720px;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><?= Html::encode(ucfirst($model->gear_type)) ?> Details</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($labels)): ?>
                <dl class="row mb-0">
                    <?php foreach ($labels as $field => $label): ?>
                        <?php if (isset($model->$field) && $model->$field !== null && $model->$field !== ''): ?>
                            <dt class="col-6 text-muted"><?= Html::encode($label) ?></dt>
                            <dd class="col-6"><?= Html::encode($model->$field) ?></dd>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </dl>
            <?php else: ?>
                <p class="text-muted mb-0">No gear details available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sets -->
    <h3 class="text-center mb-3">Fishing Sets (<?= count($setsData) ?>)</h3>

    <?php if (!empty($setsData)): ?>
        <?php foreach ($setsData as $entry):
            $set = $entry['set'];
            $catches = $entry['catches'];
        ?>
            <div class="card mb-3 mx-auto" style="max-width: 720px;">
                <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                    <strong>Set #<?= Html::encode($set->set_number) ?></strong>
                    <div class="d-flex gap-2">
                        <?php if ($canEdit): ?>
                            <?= Html::a('Edit Set', ['edit-set', 'id' => $set->id, 'elogId' => $model->id], ['class' => 'btn btn-info btn-sm']) ?>
                            <?= Html::a('Delete Set', ['delete-set', 'id' => $set->id, 'elogId' => $model->id], [
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
                        <div class="col-6"><strong>Start:</strong> <?= Html::encode($set->start_datetime) ?></div>
                        <div class="col-6"><strong>End:</strong> <?= Html::encode($set->end_datetime) ?></div>
                        <div class="col-6"><strong>Start GPS Direction:</strong> <?= Html::encode($set->start_gps_direction) ?></div>
                        <div class="col-6"><strong>Start GPS N:</strong> <?= Html::encode($set->start_gps_n) ?></div>
                        <div class="col-6"><strong>Start GPS E:</strong> <?= Html::encode($set->start_gps_e) ?></div>
                        <div class="col-6"><strong>End GPS Direction:</strong> <?= Html::encode($set->end_gps_direction) ?></div>
                        <div class="col-6"><strong>End GPS N:</strong> <?= Html::encode($set->end_gps_n) ?></div>
                        <div class="col-6"><strong>End GPS E:</strong> <?= Html::encode($set->end_gps_e) ?></div>
                    </div>

                    <h6 class="text-uppercase text-muted fw-bold mb-2">Catches</h6>
                    <?php if (!empty($catches)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($catches as $c): ?>
                                <li class="list-group-item px-0">
                                    <span class="me-3"><strong>Type:</strong> <?= Html::encode($c['fish_type']) ?></span>
                                    <span class="me-3"><strong>Variant:</strong> <?= Html::encode($c['fish_variant']) ?></span>
                                    <span class="me-3"><strong>Weight:</strong> <?= Html::encode($c['weight']) ?> kg</span>
                                    <span><strong>Count:</strong> <?= Html::encode($c['fish_count']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">No catches recorded for this set.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-muted">No sets recorded for this E-Log.</p>
    <?php endif; ?>

</div>s