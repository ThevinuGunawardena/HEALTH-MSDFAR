<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
?>

<div class="row sampling">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Boat & Gear Data - <span id="craft-number"></span> </h3>
        </div>
    </div>

    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Fishery Type</label>
                            <select id="fishery_type" data-validation="EMPTY" name="fishery_type" class="form-control">
                                <option value="" selected hidden>Please Choose...</option>
                                <option>Inland</option>
                                <option>Lagoon</option>
                                <option>Coastal</option>
                                <option>Within EEZ</option>
                                <option>High Seas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Sub Category</label>
                            <select id="sub_cat2" data-validation="EMPTY" name="sub_cat2" class="form-control">
                                <option value="" selected hidden>Please Choose...</option>
                                <option>IMULA_1</option>
                                <option>IMULA_2</option>
                                <option>IMULA_3</option>
                                <option>IMULA_4</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Engine HP</label>
                            <input type="text" id="hp" data-validation="EMPTY" name="hp" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Number of Crew Members</label>
                            <input type="text" id="no_crew" data-validation="EMPTY" name="no_crew" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Unloading Type</label>
                            <label class="custom-control custom-radio custom-control-inline">
                                <input type="radio" name="radio-inline" data-validation="EMPTY" class="custom-control-input radio-inline"><span
                                        class="custom-control-label">All</span>
                            </label>

                            <label class="custom-control custom-radio custom-control-inline">
                                <input type="radio" name="radio-inline" data-validation="EMPTY" class="custom-control-input radio-inline"><span
                                        class="custom-control-label">Partial</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Gear Setting Time</label>
                            <select id="gear_set_time" name="gear_set_time" data-validation="EMPTY" class="form-control">
                                <option value="" selected hidden>Please Choose...</option>
                                <option>Day</option>
                                <option>Night</option>
                                <option>Both</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Days</label>
                            <input type="text" id="days" name="days" data-validation="EMPTY" class="form-control"/>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Hours</label>
                            <input type="text" id="hours" name="hours" data-validation="EMPTY" class="form-control"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h4>Departure</h4>
                <div class="row">

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" id="dep_date" name="dep_date" data-validation="EMPTY" class="form-control"/>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Time</label>
                            <input type="time" name="time" id="dep_time" data-validation="EMPTY" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Fisheries District</label>
                            <select name="fi_district" id="dep_fi_district" data-validation="EMPTY" class="form-control">
                                <option value="" selected hidden>Please Choose...</option>
                                <option>Negombo</option>
                                <option>Trincomalee</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>FI Division</label>
                            <select id="dep_fi_division" name="fi_division" data-validation="EMPTY" class="form-control">
                                <option value="" selected hidden>Please Choose...</option>
                                <option>Pitipana</option>
                                <option>Town 1</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Departure Place/ Port</label>
                            <select id="dep_landing_place" name="landing_place" data-validation="EMPTY" class="form-control">
                                <option value="" selected hidden>Please Choose...</option>
                                <option>Palliya Pitupasa</option>
                                <option>Poruthota Thotupala</option>
                                <option>Palagathuraya</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Weather Occurred</label>
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-4">
                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" value="clear" data-validation="EMPTY"
                                                   class="custom-control-input Weather"><span
                                                    class="custom-control-label">Clear</span>
                                        </label><br>

                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" value="windy"
                                                   class="custom-control-input Weather"><span
                                                    class="custom-control-label">Windy</span>
                                        </label>
                                    </div>

                                    <div class="col-xl-4">
                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" value="rainy"
                                                   class="custom-control-input Weather"><span
                                                    class="custom-control-label">Rainy</span>
                                        </label><br>

                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" value="thunder" class="custom-control-input Weather"><span
                                                    class="custom-control-label">Thunder</span>
                                        </label>
                                    </div>

                                    <div class="col-xl-4">
                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" value="other"
                                                   class="custom-control-input Weather"><span
                                                    class="custom-control-label">Other</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Arrival Date</label>
                            <input type="date" name="arrival_date" id="arrival_date" data-validation="EMPTY" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Remarks</label>
                            <input type="text" name="remarks" id="remarks" data-validation="EMPTY" class="form-control"/>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>


    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
        <h3>Fishing Gears</h3>
    </div>

    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">

                <h3>Main Fishing </h3>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Gear</label>
                        <select name="main_gear" id="main_gear" data-validation="EMPTY" class="form-control">
                            <option value="" selected hidden>Please Choose...</option>
                            <option>Test1</option>
                            <option>Test2</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Main Target Species</label>
                        <select name="target_species_main" data-validation="EMPTY" id="main_target_species_main" class="form-control">
                            <option value="" selected hidden>Please Choose...</option>
                            <option>Yellow Fin Tuna - Whole H & G/ G & G</option>
                            <option>Yellow Fin Tuna - Loins</option>
                            <option>Big Eye Tuna - Loins</option>
                            <option>Barramundii</option>
                            <option>Cuttle Fish</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>How Many Operations Per Trip</label>
                        <input type="text" name="no_trip_main" data-validation="EMPTY" id="main_operatoin_no" class="form-control"/>
                    </div>
                </div>

                <div class="col-lg-12">
                    <p>True Fishing Time</p>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">

                                <label>Days</label>
                                <input type="text" name="days_main" data-validation="EMPTY" id="main_days" class="form-control"/>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">

                                <label>Hours</label>
                                <input type="text" name="hours_main" data-validation="EMPTY" id="main_hours" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">

                                <label>Fishing Depth (Fathoms)</label>
                                <input type="text" name="fishing_depth_main"  data-validation="EMPTY" id="main_fishing_depth" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">

                                <label>G Code</label>
                                <input type="text" id="main_g_code" data-validation="EMPTY" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <br>
                                <button class="btn btn-primary btn-block mt-2">Select G code on map</button>
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                                    Launch demo modal
                                </button>


                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
        <div class="card-body">

            <h3>Second Fishing </h3>
            <div class="col-lg-12">
                <div class="form-group">
                    <label>Gear</label>
                    <select name="main_gear" id="Second_gear" class="form-control">
                        <option value="" selected hidden>Please Choose...</option>
                        <option>Test1</option>
                        <option>Test2</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Main Target Species</label>
                    <select name="target_species_main" id="Second_target_species_main" class="form-control">
                        <option value="" selected hidden>Please Choose...</option>
                        <option>Yellow Fin Tuna - Whole H & G/ G & G</option>
                        <option>Yellow Fin Tuna - Loins</option>
                        <option>Big Eye Tuna - Loins</option>
                        <option>Barramundii</option>
                        <option>Cuttle Fish</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label>How Many Operations Per Trip</label>
                    <input type="text" name="no_trip_main" id="Second_operatoin_no" class="form-control"/>
                </div>
            </div>

            <div class="col-lg-12">
                <p>True Fishing Time</p>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">

                            <label>Days</label>
                            <input type="text" name="days_main" id="Second_days" class="form-control"/>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">

                            <label>Hours</label>
                            <input type="text" name="hours_main" id="Second_hours" class="form-control"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">

                            <label>Fishing Depth (Fathoms)</label>
                            <input type="text" name="fishing_depth_main"  id="Second_fishing_depth" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">

                            <label>G Code</label>
                            <input type="text" id="Second_g_code" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <br>
                            <button class="btn btn-primary btn-block mt-2">Select G code on map</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>


    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">

                <h3>Third Fishing </h3>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Gear</label>
                        <select name="main_gear" id="Third_gear" class="form-control">
                            <option value="" selected hidden>Please Choose...</option>
                            <option>Test1</option>
                            <option>Test2</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Main Target Species</label>
                        <select name="target_species_main" id="Third_target_species_main" class="form-control">
                            <option value="" selected hidden>Please Choose...</option>
                            <option>Yellow Fin Tuna - Whole H & G/ G & G</option>
                            <option>Yellow Fin Tuna - Loins</option>
                            <option>Big Eye Tuna - Loins</option>
                            <option>Barramundii</option>
                            <option>Cuttle Fish</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>How Many Operations Per Trip</label>
                        <input type="text" name="no_trip_main" id="Third_operatoin_no" class="form-control"/>
                    </div>
                </div>

                <div class="col-lg-12">
                    <p>True Fishing Time</p>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">

                                <label>Days</label>
                                <input type="text" name="days_main" id="Third_days" class="form-control"/>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">

                                <label>Hours</label>
                                <input type="text" name="hours_main" id="Third_hours" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">

                                <label>Fishing Depth (Fathoms)</label>
                                <input type="text" name="fishing_depth_main"  id="Third_fishing_depth" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">

                                <label>G Code</label>
                                <input type="text" id="Third_g_code" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <br>
                                <button class="btn btn-primary btn-block mt-2">Select G code on map</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
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
                    <br> <a href="fi-scientific" onclick="return addSamplingData()" class="btn btn-primary btn-block">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"  data-backdrop="static"  data-keyboard="false" aria-hidden="true">
    <div class="modal-dialog  modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="overflow: scroll; position: relative ; height: 600px">
                    <div style="height:580px;width:727px;background-image: url('../gcode.jpg'); background-repeat: no-repeat; background-size: contain;position: absolute">
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" name="radio-inline" data-validation="EMPTY" class="custom-control-input radio-inline"><span
                                    class="custom-control-label">All</span>
                        </label>

                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" name="radio-inline" data-validation="EMPTY" class="custom-control-input radio-inline"><span
                                    class="custom-control-label">Partial</span>
                        </label>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>


