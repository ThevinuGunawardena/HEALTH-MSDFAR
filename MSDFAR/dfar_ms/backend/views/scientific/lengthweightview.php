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
                        <h3 class="card-title">Length & Weight Data : <?= $boat ?></h3>
                    </div>
                </div>


                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <div class="form-group">
                                <h4>Fish Length Weight Details</h4>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <?php foreach ($model as $item) { ?>

                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label name="species_code">Specie Name
                                                            : <?= $item->specie0->name ?? "" ?></label>
                                                        <label name="gear_used2">Gear Used
                                                            : <?= $item->gear0->description ?? "" ?></label><br>
<!--                                                        <label name="no_of_sample">No. of Sampled Specie-->
<!--                                                            : --><?php //= $item->specie_count ?><!--</label><br>-->
                                                        <label name="weight_code2">Weight Code
                                                            : <?= yii::$app->params['weightCode'][$item->weight_code] ?></label><br>
                                                        <label name="weight2">Weight (kg) : <?= $item->weight ?></label><br>

                                                        <label>Length Type
                                                            : <?= $item->length_type == "1" ? "Curve" : "Straight" ?></label><br>


                                                        <label name="length_code">Length Code
                                                            : <?= yii::$app->params['lengthCode'][$item->length_code] ?></label><br>
                                                        <label name="length">Length (cm) : <?= $item->length ?></label>
                                                    </div>
                                                </div>


                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
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

