<?php

use yii\helpers\Url;

$this->title = Yii::t('app', 'Boat Details');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="col-xl-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <!-- SEARCH -->
            <div class="col-xl-6 mb-3">
                <form method="get" action="<?= Url::to(['boat-registration-licenses/data-view']) ?>">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               name="boat_number" 
                               placeholder="Enter boat number..."
                               value="<?= Yii::$app->request->get('boat_number') ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>

            <?php if (!empty($models)): ?>

                <!-- ================= OWNER HISTORY ================= -->
                <h3>Owner History</h3>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Boat Number</th>
                            <th>Owner Name</th>
                            <th>Owner NIC</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                  <tbody>

<?php 
$count = 1;
$uniqueOwners = [];

// Build current owner map ONCE
$currentOwnerMap = [];

foreach ($boatsMap as $b) {
    $currentOwnerMap[$b->boat_number] = $b->owner;
}

foreach ($models as $model):

    $boat = $boatsMap[$model->boat_number_id] ?? null;
    $owner = $fishermenMap[$model->fisherman_id] ?? null;

    if (!$boat) continue;

    $boatId = $model->boat_number_id;
    $fishermanId = $model->fisherman_id;

    // Remove duplicates
    if (isset($uniqueOwners[$boatId][$fishermanId])) continue;

    $uniqueOwners[$boatId][$fishermanId] = true;

    // Correct current owner
    $currentOwnerId = $currentOwnerMap[$boat->boat_number] ?? null;

    $status = ($fishermanId == $currentOwnerId)
        ? "Current Owner"
        : "Previous Owner";
?>

<tr>
    <td><?= $count++ ?></td>
    <td><?= $boat->boat_number ?? '-' ?></td>
    <td><?= trim(($owner->first_name ?? '-') . ' ' . ($owner->last_name ?? '')) ?></td>
    <td><?= $owner->nic ?? '-' ?></td>
    <td>
        <span class="badge <?= $status == 'Current Owner' ? 'bg-success' : 'bg-secondary' ?>">
            <?= $status ?>
        </span>
    </td>
</tr>

<?php endforeach; ?>

</tbody>
                </table>

                <!-- ================= OPERATIONAL LICENSES ================= -->
                <h3 class="mt-4">Operational Licenses</h3>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Boat</th>
                            <th>Owner</th>
                            <th>NIC</th>
                            <th>Type</th>
                            <th>License No</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php 
                    $seenBoats = [];
                    $count = 1;

                    foreach ($models as $model):

                        if (isset($seenBoats[$model->boat_number_id])) continue;
                        $seenBoats[$model->boat_number_id] = true;

                        $boat = $boatsMap[$model->boat_number_id] ?? null;

                        $nationalList = $nationalLicenseMap[$model->boat_number_id] ?? [];
                        $highseasList = $highseasLicenseMap[$model->boat_number_id] ?? [];

                        if (empty($nationalList) && empty($highseasList)) continue;
                    ?>

                        <!-- NATIONAL LICENSES -->
                        <?php foreach ($nationalList as $n): 
                            $licenseOwner = $fishermenMap[$n->fisherman_id] ?? null;
                        ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= $boat->boat_number ?? '-' ?></td>
                                <td><?= trim(($licenseOwner->first_name ?? '-') . ' ' . ($licenseOwner->last_name ?? '')) ?></td>
                                <td><?= $licenseOwner->nic ?? '-' ?></td>
                                <td><strong>National</strong></td>
                                <td><?= $n->license_number ?? '-' ?></td>
                                <td>
                                    <?= getStatusBadge($n->status) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- HIGHSEAS LICENSES -->
                        <?php foreach ($highseasList as $h): 
                            $licenseOwner = $fishermenMap[$h->fisherman_id] ?? null;
                        ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= $boat->boat_number ?? '-' ?></td>
                                <td><?= trim(($licenseOwner->first_name ?? '-') . ' ' . ($licenseOwner->last_name ?? '')) ?></td>
                                <td><?= $licenseOwner->nic ?? '-' ?></td>
                                <td><strong>Highseas</strong></td>
                                <td><?= $h->license_number ?? '-' ?></td>
                                <td>
                                    <?= getStatusBadge($h->status) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <p class="text-danger">No records found</p>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
function getStatusBadge($status)
{
    switch ($status) {
        case 101:
            return '<span class="badge bg-success text-white">Active</span>';
        case 403:
            return '<span class="badge bg-danger text-white">Expired</span>';
        case 400:
            return '<span class="badge bg-dark text-white">Cancelled</span>';
        default:
            return '<span class="badge bg-secondary text-white">Unknown</span>';
    }
}
?>