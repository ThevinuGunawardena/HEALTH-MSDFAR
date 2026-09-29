<?php

use backend\config\Constant;
use backend\controllers\FishermanController;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFishermanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Fishermen Profiles');
$this->params['breadcrumbs'][] = $this->title;

?>
<?php echo $this->render('_search_fisherman', ['model' => $searchModel]); ?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
       <div class="card-body">

    <?php if ($model): ?>
        <h4>Fisherman Details</h4>


        <?php if ($model): ?>

    <table class="table table-bordered table-striped">
        <tbody>
            <!-- <tr>
                <th style="width: 200px;">ID</th>
                <td><?= $model->id ?></td>
                
            </tr> -->
            <tr>
                <th>Fisherman ID Number</th>
                <td><?= $model->fisherman_uid ?></td>
                
            </tr>
            <tr>
                <th>NIC</th>
                <td><?= $model->nic ?></td>
            </tr>
            <tr>
                <th>Name</th>
                <td><?= $model->first_name . ' ' . $model->last_name ?></td>
            </tr>
            <tr>
                <th>Mobile</th>
                <td><?= $model->mobile ?></td>
            </tr>

             <tr>
                <th>Address</th>
                <td><?= $model->permanent_address ?></td>
            </tr>
            <?php if ($model->skipper): ?>
            <tr>
                <th>Skipper ID</th>
                <td><?= $model->skipper->skipper_uid ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

        <h4>Boat Registration Details</h4>

    <?php if (!empty($boatdetails)): ?>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Boat Number</th>
            <th>Boat Registration Valid From</th>
            <th>Boat Registration Expire On</th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($boatdetails as $index => $boat): ?>

    <?php
        $isExpired = false;

        if (!empty($boat->expire_date)) {
            $isExpired = strtotime($boat->expire_date) < strtotime(date('Y-m-d'));
        }
      
    ?>

    <tr>
        <td><?= $index + 1 ?></td>
        <td><?= $boat->boatNumber->boat_number ?? '-' ?></td>
        <td><?= $boat->approved_time ? date('Y-m-d', strtotime($boat->approved_time)) : '-' ?></td>

        <td style="<?= $isExpired ? 'color:red; font-weight:bold;' : 'color:green; font-weight:bold;' ?>">
            <?= $boat->expire_date ? date('Y-m-d', strtotime($boat->expire_date)) : '-' ?>
        </td>
    </tr>

<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
    <p style="color: gray;">No boat registration records found.</p>
<?php endif; ?>

<?php else: ?>
    <p style="color: gray;">No fisherman selected.</p>
<?php endif; ?>

    <?php else: ?>
        <p style="color: gray;">No fisherman selected.</p>
    <?php endif; ?>

</div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

   
</div>
