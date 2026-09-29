<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\BoatNumberOwnersLog $model */

$this->title = Yii::t('app', 'Add Boat Previous Owners for : ' . $model->boat_number);
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Boat Number Owners Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h1><?= Html::encode($this->title) ?></h1>

            <?= $this->render('_form', [
                'model' => $model,
                'boatRegAvailable' => $boatRegAvailable,
                'boatRegDate' => $boatRegDate,
                'boatDesignLength' => $boatDesignLength,
                'boatNumberLength' => $boatNumberLength,
            ]) ?>

        </div>
    </div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Previous owners</h3>
                </div>
            </div>

                    <div class="row">
                <div class="col-lg-12">
                    <table class="table ">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>NIC</th>
                            <th>From</th>
                            <th>To</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($activeRecords as $activeRecord) { ?>
                            <tr>
                                <td><?= $activeRecord["name"] ?></td>
                                <td><?= $activeRecord["nic"] ?></td>
                                <td><?= $activeRecord["from_date"] ?></td>
                                <td><?= $activeRecord["to_date"] ?></td>

                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>