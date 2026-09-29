<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
?>

<div id="scientific-page" class="row scientific">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Scientific Data</h3>
            <div class="alert alert-info unsubmited" style="display:none;">
                <strong>Info!</strong> Currently you are editing un-submitted scientific report.
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h2>General Details</h2>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Fisheries District</label><br>
                            <select id="fiDistrict" name="fiDistrict" class="form-control scientific-drop-down">

                            </select>
                        </div>
                    </div>


                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>FI Division</label><br>
                            <select id="fi_division" name="fi_division" class="form-control scientific-drop-down">

                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Name of the landing place</label><br>
                            <select id="landing_place" name="landing_place" class="form-control scientific-drop-down">

                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12"><h2>Fleet Details </h2><br>
                        <h3>Number of craft operated</h3></div>
                </div>
                <div class="row">
                    <div class="col-lg-6"><label>Boat Type</label><br>
                        <select id="boat_type"   data-validation="EMPTY"  name="boat_type" class="form-control">
                            <option value="" selected hidden>Please Choose...</option>
                            <option>IMUL</option>
                            <option>IDAY</option>
                            <option>OFRP</option>
                            <option>MTRB</option>
                            <option>NTRB</option>
                            <option>NBSB</option>
                            <option>FWCR</option>
                        </select></div>
                    <div class="col-lg-6"><label>Sub Category</label><br>
                        <select id="sub_cat" name="sub_cat"  data-validation="EMPTY"  class="form-control">
                            <option value="" selected hidden>Please Choose...</option>
                            <option>IMULA_1</option>
                            <option>IMULA_2</option>
                            <option>IMULA_3</option>
                            <option>IMULA_4</option>
                        </select></div>
                    <div class="col-lg-6"><label>Gear Type</label><br>
                        <select id="gear_type" name="gear_type"  data-validation="EMPTY"  class="form-control">
                            <option value="" selected hidden>Please Choose...</option>
                            <option>Gill Net</option>
                            <option>Line - Tuna Long Line</option>
                            <option>Line - Bottom Long Line</option>
                        </select></div>
                    <div class="col-lg-6"><label>Number of Boats</label><br>
                        <div class="input-group mb-3">
                            <input id="no_of_boats" type="number"  data-validation="EMPTY_NUMBER"  name="no_of_boats" class="form-control">

                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="input-group-append">
                            <button id="add-fleet" type="button" class="btn btn-primary ">Add</button>
                            <br>
                        </div>
                    </div>
                </div>
                <br>

                <br>

                <br>


                <div class="table-responsive ">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Boat Type</th>
                            <th scope="col">Sub Category</th>
                            <th scope="col">No. of Boats</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody class="fleet-table_body">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h2>Sampling Crafts Details</h2>
                <div class="row mb-5">
                    <div class="col-xl-12">
                        <label>Boat No.: Main Category</label>
                        <div class="row">
                            <div class="col-xl-3">
                                <select id="main_cat" name="main_cat" data-validation="EMPTY"  class="form-control">
                                    <option value="" selected hidden>Please Choose...</option>
                                    <option>IMUL</option>
                                    <option>OFRP</option>
                                    <option>MTRB</option>
                                    <option>NTRB</option>
                                </select>
                            </div>
                            <div class="col-xl-2">
                                <select id="sub_cat-sampling" data-validation="EMPTY"  name="sub_cat-sampling" class="form-control">
                                    <option>A</option>
                                    <option>B</option>
                                    <option>C</option>
                                    <option>D</option>
                                </select>
                            </div>
                            <div class="col-xl-3">
                                <input type="text" id="boat_no-sampling" data-validation="EMPTY"  name="boat_no-sampling" class="form-control">
                            </div>
                            <div class="col-xl-2">
                                <select id="boat-district" data-validation="EMPTY"  name="boat-district" class="form-control">
                                    <option>BCO</option>
                                    <option>cHW</option>
                                    <option>CBO</option>
                                    <option>GLE</option>
                                </select>
                            </div>

                            <div class="col-xl-2">
                                <button type="button" class="btn btn-primary btn-block add-sampling">Add</button></td>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-12 mt-3 mb-6"><h3>Sampling craft list</h3></div>
                </div>
                <div class="row">
                    <div class="col-lg-12 sampling-table">

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
                    <br> <a href="" class="btn btn-primary btn-block">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>

