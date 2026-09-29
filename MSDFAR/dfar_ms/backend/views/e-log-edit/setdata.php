<?php
use yii\helpers\Html;
use backend\models\ELogLongline;
use backend\models\ELogGillnet;

$this->title = 'Set Data';
$this->params['breadcrumbs'][] = [
    'label' => 'E-Log',
    'url'   => ['e-log-edit/other', 'id' => $model->id]
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container mt-3">
   
    <div class="row mt-3">

        <?php if (!empty($longlines)): ?>
        <!-- LONGLINE -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">Longline Records</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($longlines as $index => $row): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong>Record #<?= $index + 1 ?></strong>
                                <?= Html::a(
                                    'Add Sets',
                                    ['setsview', 'id' => $model->id, 'type' => 'longline', 'gearId' => $row->id],
                                    ['class' => 'btn btn-sm btn-outline-primary']
                                ) ?>
                            </div>
                            <div class="small text-muted">
                                <div><strong>Float Line:</strong> <?= $row->mainline ?> m</div>
                                <div><strong>Branchline:</strong> <?= $row->branchline ?> m</div>
                                <div><strong>No. of Hooks:</strong> <?= $row->no_of_hooks ?></div>
                                <div><strong>Hook Type:</strong> <?= ELogLongline::hookTypeList()[$row->hook_type] ?? $row->hook_type ?></div>
                                <div><strong>Bait:</strong> <?= ELogLongline::baitList()[$row->bait] ?? $row->bait ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($gillnets)): ?>
        <!-- GILLNET -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">Gillnet Records</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($gillnets as $index => $row): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong>Record #<?= $index + 1 ?></strong>
                                <?= Html::a(
                                    'Add Sets',
                                    ['setsview', 'id' => $model->id, 'type' => 'gillnet', 'gearId' => $row->id],
                                    ['class' => 'btn btn-sm btn-outline-success']
                                ) ?>
                            </div>
                            <div class="small text-muted">
                                <div><strong>Material:</strong> <?= ELogGillnet::materialList()[$row->net_material] ?? $row->net_material ?></div>
                                <div><strong>Mesh Size:</strong> <?= $row->mesh_size ?> mm</div>
                                <div><strong>Ply:</strong> <?= $row->ply ?></div>
                                <div><strong>Height:</strong> <?= $row->net_height ?> m</div>
                                <div><strong>Set Depth:</strong> <?= $row->set_depth ?> m</div>
                                <div><strong>Pieces:</strong> <?= $row->net_pieces ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($ringnets)): ?>
        <!-- RINGNET -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-warning">Ringnet Records</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($ringnets as $index => $row): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong>Record #<?= $index + 1 ?></strong>
                                <?= Html::a(
                                    'Add Sets',
                                    ['setsview', 'id' => $model->id, 'type' => 'ringnet', 'gearId' => $row->id],
                                    ['class' => 'btn btn-sm btn-outline-warning']
                                ) ?>
                            </div>
                            <div class="small text-muted">
                                <div><strong>Length:</strong> <?= $row->net_length ?> m</div>
                                <div><strong>Height:</strong> <?= $row->net_height ?> m</div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>