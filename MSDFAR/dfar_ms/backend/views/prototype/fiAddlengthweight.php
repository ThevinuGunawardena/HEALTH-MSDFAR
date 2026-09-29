<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
?>

<div class="row Length_Weight">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Length & Weight Data : <span id="craft-number"></span> </h3>
        </div>
    </div>

    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Used Gear</label>
                                <select id="gear_used" name="gear_used"  data-validation="EMPTY"  class="form-control">
                                    <option>Gill net- Small mesh gill net</option>
                                    <option>Gill net- Large mesh gill net/ Ring net</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>No. of species/ Fish </label>
                                <input type="text" id="no_species_fish" name="no_species_fish"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Weight Code</label>
                                <select id="weight_code" name="weight_code"  data-validation="EMPTY"  class="form-control">
                                    <option>Dry Weght</option>
                                    <option>Gilled</option>
                                    <option>Headed</option>
                                    <option>Fish Loins</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Weight (kg)</label>
                                <input type="text" id="weight"  data-validation="EMPTY_NUMBER"  name="weight" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-xl-6">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Fish Length Weight Details</h4>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Specie Name</label>
                                <select id="catch_species_code" name="species_code"  data-validation="EMPTY"  class="form-control lw">
                                    <option>Big Eye Tuna Loins</option>
                                    <option>Barracuda</option>
                                    <option>Barramundi</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <!-- <label>No. of Sampled Specie</label>
                                <input type="text" id="lw_no_of_sample"  data-validation="EMPTY_NUMBER"  class="form-control lw"/> -->
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Weight Code</label>
                                <select id="catch_weight_code"  data-validation="EMPTY"  class="form-control lw">
                                    <option>Dry Weght</option>
                                    <option>Gilled</option>
                                    <option>Headed</option>
                                    <option>Fish Loins</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Weight (kg)</label>
                                <input type="text" id="catch_weight"  data-validation="EMPTY_NUMBER"  class="form-control lw"/>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Length Type</label>
                                <select id="catch_weight_code"  data-validation="EMPTY"  class="form-control lw">
                                    <option>Curve</option>
                                    <option>Straight</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Length Code</label>
                                <select id="lw_length_code" data-validation="EMPTY"   class="form-control lw">
                                    <option>SL</option>
                                    <option>CF</option>
                                    <option>CK</option>
                                    <option>EF</option>
                                    <option>DF</option>
                                    <option>TL</option>
                                    <option>SF</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Length (cm)</label>
                                <input type="text" id="lw_length" data-validation="EMPTY"   class="form-control lw"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="form-group">

                        </div>
                    </div>
                    <button type="button" id="addLengthWeight" name="add_catch" class="btn btn-primary btn-block">Add Fish
                        Lenght
                        Weight Details
                    </button>
                    <div class="table-responsive ">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Species Name</th>
                                <th scope="col">Weight (kg)</th>
                                <th scope="col">Length (cm)</th>
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
                    <a href="fi-scientific" onclick="return addWeight()" class="btn btn-primary btn-block">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>

