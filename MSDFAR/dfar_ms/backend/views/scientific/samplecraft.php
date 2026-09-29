<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
$this->render('_js_config');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row sampling">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="section-block" id="cards">
                        <h3 class="card-title">Boat & Gear Data - <span id="craft-number"></span></h3>
                    </div>
                </div>

                <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fishery Type *</label>
                                        <select id="fishery_type" data-validation="EMPTY" name="fishery_type"
                                                class="form-control">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Sub Category *</label>
                                        <select id="sub_cat2" data-validation="" name="sub_cat2" class="form-control">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Engine HP *</label>
                                        <input type="text" id="hp" data-validation="EMPTY" name="hp"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Number of Crew Members *</label>
                                        <input type="text" id="no_crew" data-validation="EMPTY" name="no_crew"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Unloading Type *</label>
                                        <label class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" name="unloading-type" data-validation="EMPTY"
                                                   value="All"
                                                   class="custom-control-input radio-inline unloading_type"><span
                                                    class="custom-control-label">All</span>
                                        </label>

                                        <label class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" name="unloading-type" data-validation="EMPTY"
                                                   value="Partial"
                                                   class="custom-control-input radio-inline unloading_type"><span
                                                    class="custom-control-label">Partial</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-12 event_box" style="display:none;">
                                    <div class="form-group">
                                        <label>Event *</label>
                                        <input type="text" id="event" name="event" data-validation="EMPTY"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Gear Setting Time *</label>
                                        <select id="gear_set_time" name="gear_set_time" data-validation="EMPTY"
                                                class="form-control">
                                            <option value="" selected hidden>Please Choose...</option>
                                            <option value="1">Day</option>
                                            <option value="2">Night</option>
                                            <option value="3">Both</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <p>True Fishing Time</p>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Days *</label>
                                                <input type="number" id="days" min="0" value="0" name="days"
                                                       data-validation="NUMBER_EMPTY"
                                                       class="form-control"/>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Hours *</label>
                                                <input type="number" min="0" id="hours" value="0" name="hours"
                                                       data-validation="NUMBER_EMPTY" class="form-control"/>
                                            </div>
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
                            <h4>Departure</h4>
                            <div class="row">

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Date *</label>
                                        <input type="date" id="dep_date" name="dep_date" data-validation="EMPTY"
                                               class="form-control" max="<?= date("Y-m-d") ?>"/>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Time *</label>
                                        <input type="time" name="time" id="dep_time" data-validation="EMPTY"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Fisheries District *</label>
                                        <select name="fi_district" id="dep_fi_district" data-validation="EMPTY"
                                                class="form-control">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>FI Division *</label>
                                        <select id="dep_fi_division" name="fi_division" data-validation="EMPTY"
                                                class="form-control">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Departure Place/ Port *</label>
                                        <select id="dep_landing_place" name="landing_place" data-validation="EMPTY"
                                                class="form-control">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Weather Occurred *</label>
                                        <div class="col-xl-12">
                                            <div class="row" id="weather">
                                                <div class="col-xl-12">
                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="checkbox" value="clear" data-validation="EMPTY"
                                                               class="custom-control-input Weather"><span
                                                                class="custom-control-label">Clear</span>
                                                    </label>

                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="checkbox" value="windy"
                                                               class="custom-control-input Weather"><span
                                                                class="custom-control-label">Windy</span>
                                                    </label>

                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="checkbox" value="rainy"
                                                               class="custom-control-input Weather"><span
                                                                class="custom-control-label">Rainy</span>
                                                    </label>

                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="checkbox" value="thunder"
                                                               class="custom-control-input Weather"><span
                                                                class="custom-control-label">Thunder</span>
                                                    </label>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Arrival Date *</label>
                                        <input type="date" name="arrival_date" id="arrival_date" data-validation="EMPTY"
                                               class="form-control" max="<?= date("Y-m-d") ?>"/>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <input type="text" name="remarks" id="remarks"
                                               class="form-control"/>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>


                <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-12">
                    <h3>Fishing Gears</h3>
                </div>

                <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">

                            <h3>Main Fishing Gear</h3>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Gear *</label>
                                    <select name="main_gear" id="main_gear" data-validation="EMPTY"
                                            class="form-control">

                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="extra-data-main row">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Main Target Species *</label>
                                    <select name="target_species_main" data-validation="EMPTY"
                                            id="main_target_species_main"
                                            class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>How Many Operations Per Trip *</label>
                                    <input type="number" min="0" name="no_trip_main" data-validation="EMPTY"
                                           id="main_operatoin_no"
                                           class="form-control"/>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <p>True Fishing Time</p>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">

                                            <label>Days *</label>
                                            <input type="number" min="0" value="0" name="days_main"
                                                   data-validation="EMPTY_NUMBER"
                                                   id="main_days"
                                                   class="form-control"/>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">

                                            <label>Hours*</label>
                                            <input type="number" min="0" value="0" name="hours_main"
                                                   data-validation="EMPTY"
                                                   id="main_hours"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">

                                            <label>Fishing Depth (Fathoms) *</label>
                                            <input type="text" name="fishing_depth_main" data-validation="EMPTY"
                                                   id="main_fishing_depth" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">

                                            <label>G Code *</label>
                                            <input type="text" id="main_g_code" readonly data-validation="EMPTY"
                                                   class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <br>
                                            <button type="button" class="btn btn-primary btn-block mt-2" onclick="openGcode('1')">
                                                Select G-Code
                                            </button>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card-body">

                        <h3>Secondary Fishing Gear</h3>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Gear*</label>
                                <select name="main_gear" id="Second_gear" class="form-control">

                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="extra-data-second row">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Main Target Species*</label>
                                <select name="target_species_main" id="Second_target_species_main" class="form-control">

                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>How Many Operations Per Trip*</label>
                                <input type="number" min="0" name="no_trip_main" id="Second_operatoin_no"
                                       data-validation="EMPTY"
                                       class="form-control"/>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <p>True Fishing Time</p>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">

                                        <label>Days*</label>
                                        <input type="number" min="0" value="0" data-validation="EMPTY" name="days_main"
                                               id="Second_days" class="form-control"/>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">

                                        <label>Hours*</label>
                                        <input type="number" min="0" value="0" data-validation="EMPTY" name="hours_main"
                                               id="Second_hours" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">

                                        <label>Fishing Depth (Fathoms)*</label>
                                        <input type="text" name="fishing_depth_main" data-validation="EMPTY"
                                               id="Second_fishing_depth"
                                               class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">

                                        <label>G Code*</label>
                                        <input type="text" id="Second_g_code" data-validation="EMPTY" readonly
                                               class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <br>
                                        <button type="button" class="btn btn-primary btn-block mt-2" onclick="openGcode('2')">Select
                                            G-Code
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>


                <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">

                            <h3>Third Fishing Gear </h3>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Gear*</label>
                                    <select name="main_gear" id="Third_gear" data-validation="EMPTY"
                                            class="form-control">

                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="extra-data-third row">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Main Target Species*</label>
                                    <select name="target_species_main" data-validation="EMPTY"
                                            id="Third_target_species_main"
                                            class="form-control">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>How Many Operations Per Trip*</label>
                                    <input type="number" min="0" name="no_trip_main" data-validation="EMPTY"
                                           id="Third_operatoin_no"
                                           class="form-control"/>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <p>True Fishing Time</p>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">

                                            <label>Days*</label>
                                            <input type="number" min="0" value="0" data-validation="EMPTY"
                                                   name="days_main"
                                                   id="Third_days" class="form-control"/>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">

                                            <label>Hours*</label>
                                            <input type="number" min="0" value="0" data-validation="EMPTY"
                                                   name="hours_main"
                                                   id="Third_hours" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">

                                            <label>Fishing Depth (Fathoms)*</label>
                                            <input type="text" name="fishing_depth_main" data-validation="EMPTY"
                                                   id="Third_fishing_depth"
                                                   class="form-control"/>
                                        </div>
                                    </div>


                                    <div class="col-lg-4">
                                        <div class="form-group">

                                            <label>G Code*</label>
                                            <input type="text" id="Third_g_code" readonly class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <br>
                                            <button type="button" class="btn btn-primary btn-block mt-2" onclick="openGcode('3')">
                                                Select G-Code
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

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
                                <a href="<?= \yii\helpers\Url::to(['/scientific/create']) ?>" onclick="return addSamplingData()"
                                   class="btn btn-success btn-block">Submit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Modal -->
            <div class="modal fade" id="gcodeMoal" tabindex="-1" aria-labelledby="exampleModalLabel"
                 data-backdrop="static"
                 data-keyboard="false" aria-hidden="true">
                <div class="modal-dialog  modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Select a g-code</h5>

                        </div>
                        <div class="modal-body">
                            <input type="hidden" class="gear-number">
                            <div style="overflow: scroll; position: relative ; height: 600px">
                                <div style="height:580px;width:727px;background-image: url('../gcode.jpg'); background-repeat: no-repeat; background-size: contain;position: absolute">
                                    <label style=" top: 25px;left: 20px;"
                                           class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>

                                    <label style=" top: 25px;left: 95px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" data-validation="EMPTY" value="19"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 25px;left: 170px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" value="26" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 25px;left: 245px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="33" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 25px;left: 475px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="54" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 25px;left:  551px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="61" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 100px;left: 20px;"
                                           class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" data-validation="EMPTY" value="11"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>

                                    <label style=" top: 100px;left: 95px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" data-validation="EMPTY" value="16"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top:100px;left: 170px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" value="25" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 100px;left: 245px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="32" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 100px;left: 399px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="46" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 100px;left: 475px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="53" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 100px;left:  551px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="60" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 100px;left: 627px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="67" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>


                                    <label style=" top: 177px;left: 20px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="10" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 177px;left: 95px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" data-validation="EMPTY" value="17"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top:177px;left: 170px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" value="24" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 177px;left: 246px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="31" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>

                                    <label style=" top: 177px;left: 323px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="38" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>

                                    <label style=" top: 177px;left: 399px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="45" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 177px;left: 475px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="52" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 177px;left:  551px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="59" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 177px;left: 627px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="66" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>


                                    <label style=" top: 254px;left: 20px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="9" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 254px;left: 95px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" data-validation="EMPTY" value="16"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top:254px;left: 170px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" value="23" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 254px;left: 246px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="30" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 254px;left: 323px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="37" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 254px;left: 399px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="44" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 254px;left: 475px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="51" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 254px;left:  551px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="58" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 254px;left: 627px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="65" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>


                                    <label style=" top: 330px;left: 20px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="8" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 330px;left: 95px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" data-validation="EMPTY" value="15"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top:330px;left: 170px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" value="22" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 330px;left: 246px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="29" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 330px;left: 323px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="36" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 330px;left: 399px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="43" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 330px;left: 475px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="50" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 330px;left:  551px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="57" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 330px;left: 627px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="64" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>


                                    <label style=" top: 405px;left: 20px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="7" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 405px;left: 95px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" data-validation="EMPTY" value="14"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top:405px;left: 170px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" value="21" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 405px;left: 246px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="28" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 405px;left: 323px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="35" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 405px;left: 399px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="42" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 405px;left: 475px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="49" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 405px;left:  551px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="56" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 405px;left: 627px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="63" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>


                                    <label style=" top: 480px;left: 20px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="6" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 480px;left: 95px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" data-validation="EMPTY" value="13"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top:480px;left: 170px;" class=" custom-radio  gcode">
                                        <input type="radio" name="radio-gcode" value="20" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 480px;left: 246px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="27" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 480px;left: 323px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="34" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 480px;left: 399px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="41" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 480px;left: 475px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="48" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 480px;left:  551px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="55" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                    <label style=" top: 480px;left: 627px;" class=" custom-radio gcode">
                                        <input type="radio" name="radio-gcode" value="62" data-validation="EMPTY"
                                               class="custom-control-input radio-inline"><span
                                                class="custom-control-label"></span>
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeGcode()">Close</button>
                            <button type="button" onclick="setGcode()" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


