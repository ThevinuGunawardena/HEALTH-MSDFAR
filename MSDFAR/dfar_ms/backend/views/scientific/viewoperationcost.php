<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

/** @var string $parentToken */

$this->title = 'Scientific Data';
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Operational Cost : <?= $boat ?></h3>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-12">
                        <label><b>Fuel (L)</b></label>
                        <div class="row">
                            <div class="col-xl-3">
                                <label name="fuel_qty">Quantity : <?= $model->fuel_qty ?? "N/A" ?></label><br>
                            </div>

                            <div class="col-xl-3">
                                <label name="fuel_val">Value (LKR) : <?= $model->fuel_price ?? "N/A" ?></label><br>
                            </div>
                            <div class="col-xl-6">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <br><label><b>Ice (honders)</b></label>
                        <div class="row">
                            <div class="col-xl-3">
                                <label name="ice_qty">Quantity : <?= $model->ice_qty ?? "N/A" ?></label><br>
                            </div>

                            <div class="col-xl-3">
                                <label name="ice_val">Value (LKR) : <?= $model->ice_price ?? "N/A" ?></label><br>
                            </div>
                            <div class="col-xl-6">

                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xl-12">
                        <br><label><b>Bait (kg)</b></label>
                        <div class="row">
                            <div class="col-xl-3">
                                <label name="bait_qty">Quantity : <?= $model->bait_qty ?? "N/A" ?></label><br>
                            </div>

                            <div class="col-xl-3">
                                <label name="bait_val">Value (LKR) : <?= $model->bait_price ?? "N/A" ?></label><br>
                            </div>
                            <div class="col-xl-6">

                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-4">
                                <label name="labour_cost">Labour Cost (LKR) (Crew, Load, Unload)
                                    : <?= $model->labour_cost ?? "N/A" ?></label><br>
                            </div>
                            <div class="col-xl-4">
                                <label name="food_water">Food & Drinking Water (LKR)
                                    : <?= $model->food_water ?? "N/A" ?></label><br>
                            </div>
                            <div class="col-xl-4">
                                <label name="others">Others (LKR) : <?= $model->other ?? "N/A" ?></label><br>
                            </div>
                        </div>
                    </div>
                </div>
                <br><label name="remarks">Remarks : <?= $model->remark ?? "N/A" ?></label><br> <br>


                <div class="table-responsive ">
                    <table class="table">
                        <thead><br>
                        <tr>
                        <tr>
                            <th scope="col">Species Name</th>
                            <th scope="col">Local market</th>
                            <th scope="col">Dried Fish</th>
                            <th scope="col">Discards</th>
                        </tr>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if (!empty($model->scientificSamplingOperationUtilizations)) foreach ($model->scientificSamplingOperationUtilizations as $item) { ?>
                            <tr>
                                <td><?= $item->specie0->name ?></td>
                                <td><?= $item->local_qty ?> KG / LKR: <?= $item->local_value ?></td>
                                <td><?= $item->dried_qty ?> KG / LKR: <?= $item->dried_value ?></td>
                                <td><?= $item->discard_qty ?> KG /
                                    LKR: <?= $item->discard_value ?> <?= $item->trash ? "Trash" : "" ?></td>
                            </tr>
                            <?php

                        } ?>

                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-xl-12">
        <div class="row">
            <div class="col-xl-3">
            </div>
            <div class="col-xl-3">
            </div>
            <div class="col-xl-3">
                <div class="form-group">

                </div>
            </div>
            <div class="col-xl-3">
                <div class="form-group">
                    <br> <?= Html::a(
                                    Yii::t('app', 'Close'),
                                    ['/scientific/view', 'token' => $parentToken],
                                    ['class' => 'btn btn-primary btn-block']
                                ) ?>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

