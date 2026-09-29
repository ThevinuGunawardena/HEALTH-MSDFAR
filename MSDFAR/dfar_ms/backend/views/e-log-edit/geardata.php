<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\ELogLongline;
use backend\models\ELogGillnet;

$this->title = 'Gear Data';

$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$hasLonglineRecord = !empty($longlines);
$hasGillnetRecord  = !empty($gillnets);
$hasRingnetRecord  = !empty($ringnets);

// Determine which gear type was added first (locked-in type)
if ($hasLonglineRecord) {
    $lockedType = 'longline';
} elseif ($hasGillnetRecord) {
    $lockedType = 'gillnet';
} elseif ($hasRingnetRecord) {
    $lockedType = 'ringnet';
} else {
    $lockedType = null; // nothing added yet, all allowed
}
?>

<div class="container mt-4">

    <?php if ($hasLonglineRecord || $hasGillnetRecord || $hasRingnetRecord): ?>
    <div class="card-footer text-end">
        <?= Html::a('Go to set Data', ['setdata', 'id' => $model->id], ['class' => 'btn btn-primary ms-2']) ?>
    </div>
<?php endif; ?>

    <!-- Add New Buttons -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Add New Gear</h5>
        </div>

        <div class="card-body text-center">

            <?php if ($longline): ?>
                <?php if ($lockedType === null || $lockedType === 'longline'): ?>
                    <?= Html::a(
                        '+ Longline',
                        ['e-log-edit/longline', 'id' => $model->id, 'dep_id' => $dep_id, 'longline' => 1, 'gillnet' => $gillnet ? 1 : 0, 'ringnet' => $ringnet ? 1 : 0],
                        ['class' => 'btn btn-primary m-2']
                    ) ?>
                <?php else: ?>
                    <span class="btn btn-secondary m-2 disabled">+ Longline</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($gillnet): ?>
                <?php if ($lockedType === null || $lockedType === 'gillnet'): ?>
                    <?= Html::a(
                        '+ Gillnet',
                        ['e-log-edit/gillnet', 'id' => $model->id, 'dep_id' => $dep_id, 'longline' => $longline ? 1 : 0, 'gillnet' => 1, 'ringnet' => $ringnet ? 1 : 0],
                        ['class' => 'btn btn-primary m-2']
                    ) ?>
                <?php else: ?>
                    <span class="btn btn-secondary m-2 disabled">+ Gillnet</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($ringnet): ?>
                <?php if ($lockedType === null || $lockedType === 'ringnet'): ?>
                    <?= Html::a(
                        '+ Ringnet',
                        ['e-log-edit/ringnet', 'id' => $model->id, 'dep_id' => $dep_id, 'longline' => $longline ? 1 : 0, 'gillnet' => $gillnet ? 1 : 0, 'ringnet' => 1],
                        ['class' => 'btn btn-primary m-2']
                    ) ?>
                <?php else: ?>
                    <span class="btn btn-secondary m-2 disabled">+ Ringnet</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!$longline && !$gillnet && !$ringnet): ?>
                <div class="alert alert-info mb-0">No gear types configured for this E-Log.</div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Longline Records -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Longline</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($longlines)): ?>
                <p class="text-muted p-3 mb-0">No longline records found.</p>
            <?php else: ?>
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Float Line (m)</th>
                            <th>Branchline (m)</th>
                            <th>No. of Hooks</th>
                            <th>Hook Type</th>
                            <th>Bait</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($longlines as $row): ?>
                            <tr>
                                <td><?= $row->mainline ?></td>
                                <td><?= $row->branchline ?></td>
                                <td><?= $row->no_of_hooks ?></td>
                                <td><?= ELogLongline::hookTypeList()[$row->hook_type] ?? $row->hook_type ?></td>
                                <td><?= ELogLongline::baitList()[$row->bait] ?? $row->bait ?></td>
                                <td>
                                    <?= Html::a('Edit', ['e-log-edit/edit-longline', 'id' => $row->id, 'elogId' => $model->id, 'dep_id' => $dep_id, 'longline' => $longline ? 1 : 0, 'gillnet' => $gillnet ? 1 : 0, 'ringnet' => $ringnet ? 1 : 0], ['class' => 'btn btn-sm btn-warning']) ?>
                                    <?= Html::a('Delete', ['e-log-edit/delete-longline', 'id' => $row->id, 'elogId' => $model->id, 'dep_id' => $dep_id, 'longline' => $longline ? 1 : 0, 'gillnet' => $gillnet ? 1 : 0, 'ringnet' => $ringnet ? 1 : 0], [
                                        'class' => 'btn btn-sm btn-danger',
                                        'data' => [
                                            'confirm' => 'Are you sure you want to delete this record?',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Gillnet Records -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Gillnet</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($gillnets)): ?>
                <p class="text-muted p-3 mb-0">No gillnet records found.</p>
            <?php else: ?>
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Material</th>
                            <th>Mesh Size (mm)</th>
                            <th>Ply</th>
                            <th>Height (m)</th>
                            <th>Set Depth (m)</th>
                            <th>Pieces</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gillnets as $row): ?>
                            <tr>
                                <td><?= ELogGillnet::materialList()[$row->net_material] ?? $row->net_material ?></td>
                                <td><?= $row->mesh_size ?></td>
                                <td><?= $row->ply ?></td>
                                <td><?= $row->net_height ?></td>
                                <td><?= $row->set_depth ?></td>
                                <td><?= $row->net_pieces ?></td>
                                <td>
                                    <?= Html::a('Edit', ['e-log-edit/edit-gillnet', 'id' => $row->id, 'elogId' => $model->id, 'dep_id' => $dep_id, 'longline' => $longline ? 1 : 0, 'gillnet' => $gillnet ? 1 : 0, 'ringnet' => $ringnet ? 1 : 0], ['class' => 'btn btn-sm btn-warning']) ?>
                                    <?= Html::a('Delete', ['e-log-edit/delete-gillnet', 'id' => $row->id, 'elogId' => $model->id, 'dep_id' => $dep_id, 'longline' => $longline ? 1 : 0, 'gillnet' => $gillnet ? 1 : 0, 'ringnet' => $ringnet ? 1 : 0], [
                                        'class' => 'btn btn-sm btn-danger',
                                        'data' => [
                                            'confirm' => 'Are you sure you want to delete this record?',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ringnet Records -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Ring Net</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($ringnets)): ?>
                <p class="text-muted p-3 mb-0">No ring net records found.</p>
            <?php else: ?>
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Length (m)</th>
                            <th>Height (m)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ringnets as $row): ?>
                            <tr>
                                <td><?= $row->net_length ?></td>
                                <td><?= $row->net_height ?></td>
                                <td>
                                    <?= Html::a('Edit', ['e-log-edit/edit-ringnet', 'id' => $row->id, 'elogId' => $model->id, 'dep_id' => $dep_id, 'longline' => $longline ? 1 : 0, 'gillnet' => $gillnet ? 1 : 0, 'ringnet' => $ringnet ? 1 : 0], ['class' => 'btn btn-sm btn-warning']) ?>
                                    <?= Html::a('Delete', ['e-log-edit/delete-ringnet', 'id' => $row->id, 'elogId' => $model->id, 'dep_id' => $dep_id, 'longline' => $longline ? 1 : 0, 'gillnet' => $gillnet ? 1 : 0, 'ringnet' => $ringnet ? 1 : 0], [
                                        'class' => 'btn btn-sm btn-danger',
                                        'data' => [
                                            'confirm' => 'Are you sure you want to delete this record?',
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</div>