<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
$this->render('_js_config');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
<div class="row Catch-data">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Fish Catch Data : <span id="craft-number"></span></h3>
        </div>
    </div>

    <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <div class="row">

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Used Gear*</label>
                                <select id="gear_used" name="gear_used" data-validation="EMPTY" class="form-control">
                                </select>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Catch data by Specie </h4>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Species Name*</label>
                                <select id="catch_species_code" name="species_code" data-validation="EMPTY"
                                        class="form-control lw">

                                </select>
                            </div>
                        </div>


                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Weight Code*</label>
                                <select id="catch_weight_code" data-validation="EMPTY" class="form-control lw">
                                    <?php foreach (yii::$app->params['weightCode'] as $index => $param) {
                                        echo '<option value="'.$index.'">'.$param.'</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Weight (kg) *</label>
                                <input type="number" id="catch_weight" min="0" data-validation="EMPTY"
                                       class="form-control lw"/>
                            </div>
                        </div>

                    </div>

                    <button type="button" id="addCatch" name="add_catch" class="btn btn-primary btn-block">Add catch details
                    </button>
                    <div class="table-responsive ">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Species Name</th>
                                <th scope="col">Weight (kg)</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody class="lengthWeight-table">

                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="row">

            <div class="col-xl-6">
                <div class="form-group">
                    <a href="<?= \yii\helpers\Url::to(['/scientific/create']) ?>"
                       class="btn btn-outline-primary btn-block">Back</a>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="form-group">

                    <a href="<?= \yii\helpers\Url::to(['/scientific/create']) ?>" class="btn btn-success btn-block">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

