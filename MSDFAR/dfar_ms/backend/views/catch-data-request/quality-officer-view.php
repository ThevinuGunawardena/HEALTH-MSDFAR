<?php

use yii\helpers\Html;
use backend\config\Constant;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\Url;
use backend\models\BoatNumbers;
use backend\models\MHarbours;
use backend\models\MMainGearTypes;
use backend\models\CatchDataFishcatch;

/** @var yii\web\View $this */
/** @var backend\models\CatchDataRequest $model */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Catch Data Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$CatchDataPurchaseDetailsModel = new \backend\models\CatchDataPurchaseDetails();
?>

<!--  SEARCH SECTION -->

    <div class="col-md-6">
        <label>Log Sheet Number</label>
        <input type="text" class="form-control" id="logBookNo" placeholder="Enter Log Sheet Number">
    </div>

    <div class="col-md-6">
        <label>Log Sheet Page Number</label>
        <input type="text" class="form-control" id="logSheetNo" placeholder="Enter Log Sheet Page Number">
    </div>

    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="button" class="btn btn-success" id="searchBtn">Search</button>
    </div>


<hr>

<?php if (!empty($logbookdata)): ?>

    <h4 class="mt-4">Logsheet Details</h4>

    <table class="table table-bordered">
        <tr>
            <th>Log Book No</th>
            <td><?= $logbookdata->log_book_no ?></td>
        </tr>
        <tr>
            <th>Page No</th>
            <td><?= $logbookdata->log_book_page_no ?></td>
        </tr>
        <tr>
            <th>Boat</th>
          <td>
            <?php
                $boat = \backend\models\BoatNumbers::findOne($logbookdata->boat_registration_id);
                echo $boat ? $boat->boat_number : 'N/A';
            ?>
        </td>
        </tr>
        <tr>
            <th>Landed On</th>
            <td><?= $logbookdata->landing_date ?></td>

        </tr>
        <tr>
            <th>Landed At</th>
           <td><?php
                $harbour = \backend\models\MHarbours::findOne($logbookdata->unloading_harbour);
                echo $harbour ? $harbour->Name : 'N/A'; 
            ?></td>

        </tr>
    </table>

<?php else: ?>

    <?php if (Yii::$app->request->get('logBNo')): ?>
        <p class="text-danger mt-3">No data found</p>
    <?php endif; ?>

<?php endif; ?>

<hr>

<?php if (!empty($catchdata)): ?>

    <h4 class="mt-4">Fish Catch Details</h4>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Fish Type</th>
                <th>Number Of Fish</th>
                <th>Weight Of Fish</th>
                <th>Remain Of Fish</th>
                <th>Remain Weight Of Fish</th>

            </tr>
        </thead>

        <tbody>
            <?php $i = 1; foreach ($catchdata as $row): ?>
                <tr>
                    <td><?= $i++ ?></td>

                    <td>
                        <?= \backend\config\Constant::$exportFishTypes[$row->fish_type] ?? 'N/A' ?>
                    </td>

                    <td><?= $row->num_of_fish_act ?? 0 ?></td>

                    <td><?= $row->weight_of_fish_act ?? 0 ?> KG</td>

                    <td><?= $row->remain_fish_count ?? 0 ?></td>

                    <td><?= $row->remain_weight ?? 0 ?> KG</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php else: ?>

    <?php if (Yii::$app->request->get('logBNo')): ?>
        <p class="text-danger mt-3">No fish catch data found</p>
    <?php endif; ?>

<?php endif; ?>


<?php if (!empty($purcheseDeatils)): ?>


    <?php if (!empty($purcheseDeatils)): ?>

    <h4 class="mt-4">Fish Catch Breakdown</h4>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Fish Type</th>
                <th>Number Of Fish</th>
                <th>Weight Of Fish</th>
                <th>Exporter</th>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1; foreach ($purcheseDeatils as $row): ?>
                <tr>
                    <td><?= $i++ ?></td>

                    <td>
                        <?= \backend\config\Constant::$exportFishTypes[$row->fish_type] ?? 'N/A' ?>
                    </td>

                    <td><?= $row->No_of_Fish ?? 0 ?></td>

                    <td><?= $row->Weight_of_Fish ?? 0 ?> KG</td>

                    <td>
    <?= isset($row->user->officerProfile)
        ? $row->user->officerProfile->first_name . ' ' . $row->user->officerProfile->last_name
        : 'N/A' ?>
</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php else: ?>

    <?php if (Yii::$app->request->get('logBNo')): ?>
        <p class="text-danger mt-3">No purchase details found</p>
    <?php endif; ?>

<?php endif; ?>

<?php endif; ?>
<!-- 🔥 SCRIPT -->
<script>
document.getElementById('searchBtn').addEventListener('click', function () {

    let logBNo = document.getElementById('logBookNo').value;
    let logSNo = document.getElementById('logSheetNo').value;

    if (!logBNo || !logSNo) {
        alert('Please enter both values');
        return;
    }

    window.location.href = '<?= \yii\helpers\Url::to(["catch-data-request/get-logsheet-officer-data"]) ?>'
        + '?logBNo=' + encodeURIComponent(logBNo)
        + '&logSNo=' + encodeURIComponent(logSNo);
});
</script>