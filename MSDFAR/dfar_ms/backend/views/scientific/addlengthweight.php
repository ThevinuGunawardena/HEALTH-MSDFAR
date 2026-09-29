<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
$this->render('_js_config');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
<div class="row lenght-weight">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Length & Weight Data : <span id="craft-number"></span></h3>
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
    <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Catch data by Specie </h4>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Specie Name*</label>
                                <select id="lw_species_code" name="species_code" data-validation="EMPTY"
                                        class="form-control lw">

                                </select>
                            </div>
                        </div>


                        <div class="col-lg-6">
                            <div class="form-group">
                                <!-- <label>No. of Sampled Specie</label>
                                <input type="text" id="lw_no_of_sample" name="no_of_sample" data-validation="EMPTY"
                                       class="form-control"/> -->
                                <label>Weight (kg) *</label>
                                <input type="number" id="lw_weight" min="0" data-validation="EMPTY"
                                       class="form-control lw"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Weight Code*</label>
                                <select id="lw_weight_code" data-validation="EMPTY" class="form-control lw">
                                    <?php foreach (yii::$app->params['weightCode'] as $index => $param) {
                                        echo '<option value="'.$index.'">'.$param.'</option>';
                                    } ?>

                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <!-- <label>Weight (kg) *</label>
                                <input type="number" id="lw_weight" min="0" data-validation="EMPTY"
                                       class="form-control lw"/> -->
                                <label>Length Code</label><br>
                                <select id="lw_length_code" class="form-control">
                                    <?php foreach (yii::$app->params['lengthCode'] as $index => $param) {
                                        echo '<option value="'.$index.'">'.$param.'</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Length Type</label><br>
                                <select id="lw_length_type" data-validation="EMPTY" class="form-control lw">
                                    <option value="1">Curve</option>
                                    <option value="2">Straight</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <!-- <label>Length Code</label><br>
                                <select id="lw_length_code" class="form-control">
                                    <?php foreach (yii::$app->params['lengthCode'] as $index => $param) {
                                    echo '<option value="'.$index.'">'.$param.'</option>';
                                } ?>
                                </select> -->

                                <label>Length (cm) *</label>
                                <input type="text" id="lw_length" class="form-control"  data-validation="EMPTY"/>

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <!-- <label>Length (cm)</label>
                                <input type="text" id="lw_length" class="form-control"/> -->
                            </div>
                        </div>

                    </div>

                    <button type="button" id="add_lw" name="add_catch" class="btn btn-primary btn-block">Add
                        Fish
                        Length
                        Weight Details
                    </button>
                    <div class="table-responsive ">
                        <table class="table">
                            <thead><br>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Species Name</th>
                                <th scope="col">Weight (kg)</th>
                                <th scope="col">Length (cm)</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody class=" lengthWeight-table">

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

