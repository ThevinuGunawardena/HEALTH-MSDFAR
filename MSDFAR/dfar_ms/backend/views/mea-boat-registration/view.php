<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumbers;
use backend\services\Util;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\MeaBoatRegistration $model */

$this->title = $model->mea_certificate_number;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Mea Boat Registrations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

$boatNumber = BoatNumbers::findOne($model->boat_reg_number);
?>
<style>
    span {
        font-weight: bold;
    }
</style>
<div class="mea-boat-registration-view">

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">

                <?= Util::editPermission() ? Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : "" ?>


            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="section-block" id="cards">
                <h3 class="card-title"> Section 01</h3>
            </div>

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <h4>Type of Boat</h4>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <label name="boat_type">Boat Type
                                                : <span>
                                                    <?= $boatNumber->boatType->code ?></span></label> <br>
                                        </div>

                                        <div class="col-xl-8">

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <h4>Boat Owner/ Owners</h4>
                        <div class="table-responsive ">
                            <table class="table">
                                <thead><br>
                                <tr>
                                    <th scope="col">Owner Name</th>
                                    <th scope="col">Mobile</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">NIC</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><span>
                                            <?= $boatNumber->owner0->first_name ?></span>
                                        <span><?= $boatNumber->owner0->last_name ?></span></td>
                                    <td><span>
                                            <?= $boatNumber->owner0->mobile ?></span></td>
                                    <td><span><?= $boatNumber->owner0->permanent_address ?></span></td>
                                    <td><span>
                                            <?= $boatNumber->owner0->nic ?></span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xl-6">
                                    <label name="yard_name">Boat Reg Number
                                        : <span><?= $boatNumber->boat_number ?></span></label><br>
                                    <label name="hull_number">Hull number of vessel
                                        : <span>
                                            <?= $model->hull_number ?></span> </label><br>
                                    <label name="vessel_type">Type of Vessel of Vessel
                                        : <span>
                                            <?= $model->hull_number ?></span></label><br>
                                </div>
                                <div class="col-xl-6">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <h4>Builder & Design Details</h4>
                            <div class="row">
                                <div class="col-xl-6">
                                    <label name="yard_name">Name of Boat Builder/Boat Yard
                                        : <span>
                                            <?= $boatNumber->yard ?></span></label><br>
                                    <label name="yard_no">Yard Reg. & Design Reg. No. : </label><br>
                                    <label name="length_weight">Light Weight & Type of Vessel
                                        :<span>
                                            <?= $model->light_weight_type_of_vessel ?></span></label><br>
                                </div>
                                <div class="col-xl-6">

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <label name="commence_contruct_date">Commenced Construction Date
                                        : <span>
                                            <?= $model->commenced_construction_date ?></span></label><br>
                                    <label name="completed_contruct_date">Completed Construction Date
                                        : <span>
                                            <?= $model->completed_construction_date ?></span></label><br>
                                    <label name="date_build">Date of Build : <span>
                                            <?= $model->date_of_build ?></span></label><br>
                                </div>
                                <div class="col-xl-6">
                                    <label name="mat_of_hull">Material of Hull as Approved
                                        : <span><?= $model->material_of_hull_as_approved ?></span></label><br>
                                    <label name="gross_tons">Gross Tonns (tons) : <span>
                                            <?= $model->gross_tonns ?></span></label><br>
                                    <label name="volume">Volume (m3) : <span><?= $model->volume ?></span></label><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <h4>Dimensions</h4>
                            <div class="row">
                                <div class="col-xl-6">
                                    <label name="overall_length">Overall Length (m)
                                        : <span>
                                            <?= $model->overall_length ?></span></label><br>
                                    <label name="beam">Beam (m) : <span>
                                            <?= $model->beam ?></span></label><br>
                                </div>

                                <div class="col-xl-6">
                                    <label name="depth">Depth (m) : <span><?= $model->depth ?></span></label><br>
                                    <label name="draught">Draught(m) : <span>
                                            <?= $model->draught ?></span></label><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <h4>Machinery</h4>
                            <div class="row">
                                <div class="col-xl-6">
                                    <label name="engine_type">Engine Type
                                        : <span><?= Constant::$engineTypes[$model->engine_type] ?></span></label><br>
                                    <label name="fuel_type">Fuel Type
                                        : <span>
                                            <?= Constant::$fuelType[$model->fuel_type] ?></span></label><br>
                                </div>
                                <div class="col-xl-6">

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <label name="no_cylinder">Number of Cylinders
                                        : <span>
                                            <?= $model->number_of_cylinders ?? "" ?></span></label><br>
                                    <label name="hp">Horse Power of Engine
                                        : <span>
                                            <?= $model->horse_power_of_engine ?></span></label><br>
                                    <label name="make_model">Make/ Model
                                        : <span>
                                            <?= Constant::$MEAEngineModel[$model->engine_model] ?></span></label><br>
                                    <label name="engine_no">Engine Number : <span>
                                            <?= $model->engine_number ?></span></label><br>
                                </div>
                                <div class="col-xl-6">
                                    <label name="propeller_diameter">Propeller Diameter/ Pitch & No. of Blades
                                        : <span><?= $model->propeller_diameter_pitch_no_of_blades ?></span></label><br>
                                    <label name="gear_ratio">Gear Ratio : <span>
                                            <?= $model->gear_ratio ?></span></label><br>
                                    <label name="steering_gear_type">Steering Gear Type
                                        : <span><?= $model->steering_gear_type ?></span></label><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <h4>Full Capacities of Tanks</h4>
                            <div class="row">
                                <div class="col-xl-6">
                                    <label name="fuel">Fuel Oil (L) : <span>
                                            <?= $model->fuel_oil ?></span></label><br>
                                    <label name="fresh_water">Fresh Water (L) : <span><?= $model->fresh_water ?></span></label><br>
                                    <label name="chilled_bath">Chilled Bath (m3)
                                        : <span>
                                            <?= $model->chilled_bath ?></span></label><br>
                                </div>
                                <div class="col-xl-6">
                                    <label name="fish_hold">Fish Hold (m3) : <span>
                                            <?= $model->fish_hold ?></span></label><br>
                                    <label name="stores">Cooling System : <span><?= $model->stores ?></span></label><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <h4>Test Data</h4>
                            <div class="row">
                                <div class="col-xl-6">
                                    <label name="design_speed">Designed Speed
                                        : <span>
                                            <?= $model->designed_speed ?></span></label><br>
                                </div>
                                <div class="col-xl-6">
                                    <label name="inspection_date">Inspection Date
                                        : <span>
                                            <?= $model->inspection_date ?></span></label><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <h4>Auxiliary Cooling Systems</h4>
                            <div class="row">
                                <div class="col-xl-4">
                                    <label name="cooling_sys_type">Type
                                        : <span>
                                            <?= $model->cooling_water_system ?></span></label><br>
                                </div>
                                <!---->
                                <!--                                <div class="col-xl-4">-->
                                <!--                                    <label name="cooling_sys_model">Model : </label><br>-->
                                <!--                                </div>-->
                                <!---->
                                <!--                                <div class="col-xl-4">-->
                                <!--                                    <label name="cooling_capacity">Cooling Capacity : </label><br>-->
                                <!--                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="section-block" id="cards">
                    <h3 class="card-title"> Section 02</h3>
                </div>
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="form-group">
                            <h4>Test Data</h4>
                            <div class="row">
                                <div class="col-xl-4">
                                    <label name="place_inpection">Place of Inspection
                                        : <span>
                                            <?= $model->place_of_inspectio ?></span></label><br>
                                </div>

                                <div class="col-xl-4">
                                    <label name="inspection_date2">Inspection Date
                                        :<span>
                                            <?= $model->inspection_date ?></span></label><br>
                                </div>

                                <div class="col-xl-4">
                                    <label name="fuel_type">Was vessel at the time of
                                        inspection? : <span>
                                            <?= Constant::$MEAWhereWasVessel[$model->vessel_at_the_time_of_inspection] ?? "" ?></span></label><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($model->renew == 1) { ?></div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <h4>Condition of</h4>
                        <div class="row">
                            <div class="col-xl-4">
                                <label>Hull</label><br>
                                <label name="hull">Internal : <span>
                                                <?= Constant::$MEAInspection[$model->condition_hull_internal] ?? "" ?></span></label><br>
                            </div>
                            <div class="col-xl-4">
                                <br><label name="hull_external">External :
                                    <span><?= Constant::$MEAInspection[$model->condition_hull_external] ?? "" ?></span></label><br>
                            </div>
                            <div class="col-xl-4">
                                <br><label name="hull_sheathing">Sheathing : <span>
                                                <?= Constant::$MEAInspection[$model->condition_hull_sheathing] ?? "" ?></span></label><br>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <br> <label name="deck">Decks :
                                    <span><?= Constant::$MEAInspection[$model->condition_decks] ?? "" ?></span></label><br>
                                <label name="cargo">Cargo Compartment : <span>
                                                <?= Constant::$MEAInspection[$model->condition_cargo_compartment] ?? "" ?></span></label><br>
                                <label name="framework_tim">FRamework, Timbers & Internals :
                                    <span><?= Constant::$MEAInspection[$model->condition_framework_timbers_internals] ?? "" ?></span></label><br>
                                <label name="machinery">Machinery : <span>
                                                <?= Constant::$MEAInspection[$model->condition_machinery] ?? "" ?></span></label><br>
                                <label name="rudder">Rudder :
                                    <span><?= Constant::$MEAInspection[$model->condition_rudder] ?? "" ?></span></label><br>
                            </div>

                            <div class="col-xl-6">
                                <br> <label name="gear">Steering Gear : <span>
                                                <?= Constant::$MEAInspection[$model->condition_steering_gear] ?? "" ?></span></label><br>
                                <label name="anchor">Anchor & Cables :
                                    <span><?= Constant::$MEAInspection[$model->condition_anchor_cables] ?? "" ?></span></label><br>
                                <label name="nav_lights">Navigation Lights : <span>
                                                <?= Constant::$MEAInspection[$model->condition_navigation_lights] ?? "" ?></span></label><br>
                                <label name="last_overhaul">Date of last overhaul :
                                    <span><?= $model->condition_date_last_overhaul ?></span></label><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <h4>Is the vessel equipped with any of the following equipment if so </h4>
                        <div class="row">
                            <div class="col-xl-6">
                                <label name="compass">Compass : <span>
                                                <?= Constant::$MEAInspection[$model->equipped_compass] ?? "" ?></span></label><br>
                                <label name="life_saving">Life Saving Appliances :
                                    <span><?= Constant::$MEAInspection[$model->equipped_life_saving_appliances] ?? "" ?></span></label><br>
                                <label name="fire_exting">Fire Extingulshers Type : <span>
                                                <?= Constant::$MEAInspection[$model->equipped_fire_extingulshers_type] ?? "" ?></span></label><br>
                                <label name="bilge_pump">Bilge Pump :
                                    <span><?= Constant::$MEAInspection[$model->equipped_bilge_pump] ?? "" ?></span></label><br>
                            </div>


                            <div class="col-xl-6">
                                <label name="chill_bath">Bailers : <span>
                                                <?= Constant::$MEAInspection[$model->equipped_bailers] ?? "" ?></span></label><br>
                                <label name="first_aid">First Aid Equipment :
                                    <span><?= Constant::$MEAInspection[$model->equipped_first_aid_equipment] ?? "" ?></span></label><br>
                                <label name="navi_equip">Navigation Equipment : <span>
                                                <?= Constant::$MEAInspection[$model->equipped_navigation_equipment] ?? "" ?></span></label><br>
                                <label name="gps">GPS available or not :
                                    <span><?= Constant::$MEAInspection[$model->equipped_gps_available] ?? "" ?></span></label><br>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>

        <?php } ?></div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Construction, Watertight Integrity & Equipment </h4>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="hull">Hull & Hull Framing
                                : <span>
                                            <?= Constant::$MEAInspection[$model->hull_hull_framing] ?></span></label><br>
                            <label name="inlets">Inlets & Discharges
                                : <span>
                                            <?= Constant::$MEAInspection[$model->inlets_discharges] ?></span></label><br>
                            <label name="deck">Deck & Deck Framing
                                : <span>
                                            <?= Constant::$MEAInspection[$model->deck_deck_framing] ?></span></label><br>
                            <label name="bulk_head">Bulk Head
                                : <span>
                                            <?= Constant::$MEAInspection[$model->bulk_head] ?></span></label><br>

                        </div>
                        <div class="col-xl-4">
                            <label name="door_windows">Weather Tight Doors/ Windows
                                : <span>
                                            <?= Constant::$MEAInspection[$model->weather_tight_doors] ?></span></label><br>
                            <label name="hatch">Hatch Way Coamings
                                : <span>
                                            <?= Constant::$MEAInspection[$model->hatch_way_coamings] ?></span></label><br>
                            <label name="machinery_space">Machinery Space Opening
                                : <span>
                                            <?= Constant::$MEAInspection[$model->machinery_space_opening] ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="deck_opening">Deck Opening
                                : <span>
                                            <?= Constant::$MEAInspection[$model->deck_opening] ?></span></label><br>
                            <label name="pipes_bunkering">Pipes, Bunkering Inlets
                                : <span>
                                            <?= Constant::$MEAInspection[$model->pipes_bunkering_inlets] ?></span></label><br>
                            <label name="freeing_ports">Freeing Ports
                                : <span>
                                            <?= Constant::$MEAInspection[$model->freeing_ports] ?></span></label><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Stability & Associated Seaworthiness</h4>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="bow_height">Bow Height
                                : <span>
                                            <?= Constant::$MEAInspection[$model->bow_height] ?></span></label><br>
                            <label name="max_draught">Maximum Draught
                                : <span>
                                            <?= Constant::$MEAInspection[$model->maximum_draught] ?></span></label><br>
                            <label name="water_tank">Water Tanks Partitions
                                : <span>
                                            <?= Constant::$MEAInspection[$model->water_tanks_partitions] ?></span></label><br>
                            <label name="fuel_tank">Fuel Tanks Partitions
                                : <span>
                                            <?= Constant::$MEAInspection[$model->fuel_tanks_partitions] ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="pot_fish_hold">Portable Fish Hold Divisions
                                : <span>
                                            <?= Constant::$MEAInspection[$model->portable_fish_hold_divisions] ?></span></label><br>
                            <label name="chill_bath">Chilled Bath Partitions
                                : <span>
                                            <?= Constant::$MEAInspection[$model->chilled_bath_partitions] ?></span></label><br>
                            <label name="bait_hold">Bait Hold Arrangement
                                : <span>
                                            <?= Constant::$MEAInspection[$model->bait_hold_arrangement] ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="stores_cargo">Stores/ Cargo Hold Constructions
                                : <span>
                                            <?= Constant::$MEAInspection[$model->stores_cargo_hold_constructions] ?></span></label><br>
                            <label name="draught_marks">Draught Marks
                                : <span>
                                            <?= Constant::$MEAInspection[$model->draught_marks] ?></span></label><br>
                            <label name="stability_notice">Stability Notice
                                : <span>
                                            <?= Constant::$MEAInspection[$model->stability_notice] ?></span></label><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Machinery Installation</h4>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="layout_machine_space">Layout of Machinery Space
                                : <span>
                                            <?= Constant::$MEAInspection[$model->layout_of_machinery_space] ?></span></label><br>
                            <label name="propulsion_machinary">Propulsion Machinery & Steering Gear
                                : <span>
                                            <?= Constant::$MEAInspection[$model->propulsion_machinery_steering_gear] ?></span></label><br>
                            <label name="engine_console">Engine Console
                                : <span>
                                            <?= Constant::$MEAInspection[$model->engine_console] ?></span></label><br>
                            <label name="lighting">Lighting
                                : <span>
                                            <?= Constant::$MEAInspection[$model->lighting] ?></span></label><br>
                            <label name="floor">Floor
                                : <span>
                                            <?= Constant::$MEAInspection[$model->floor] ?></span></label><br>
                            <label name="gear_marking">Gear Marking :
                                <span><?= Constant::$MEAInspection[$model->gear_marking] ?? "" ?></span></label><br>
                        </div>


                        <div class="col-xl-4">
                            <label name="ventilation">Ventilation
                                : <span>
                                            <?= Constant::$MEAInspection[$model->ventilation] ?></span></label><br>
                            <label name="floor">Sound & Vibration
                                : <span>
                                            <?= Constant::$MEAInspection[$model->sound_vibration] ?></span></label><br>
                            <label name="engine_mounting">Engine Mounting
                                : <span>
                                            <?= Constant::$MEAInspection[$model->engine_mounting] ?></span></label><br>
                            <label name="console_monitoring">Console & Monitoring Instruments
                                : <span>
                                            <?= Constant::$MEAInspection[$model->console_monitoring_instruments] ?></span></label><br>
                            <label name="fuel_install">Fuel Oil Installation
                                : <span>
                                            <?= Constant::$MEAInspection[$model->fuel_oil_installation] ?></span></label><br>
                            <label name="line_cutter">Dehooker and Line Cutter :
                                <span><?= Constant::$MEAInspection[$model->dehooker_line_cutter] ?? "" ?></label><br>
                        </div>

                        <div class="col-xl-4">
                            <label name="cooling_water">Cooling Water System
                                : <span>
                                            <?= Constant::$MEAInspection[$model->cooling_water_system] ?></span></label><br>
                            <label name="bilge_pumping">Bilge Pumping Systems
                                : <span>
                                            <?= Constant::$MEAInspection[$model->bilge_pumping_systems] ?></span></label><br>
                            <label name="exhaust_systems">Exhaust Systems
                                : <span>
                                            <?= Constant::$MEAInspection[$model->exhaust_systems] ?></span></label><br>
                            <label name="hydraulic_system">Hydraulic System
                                : <span>
                                            <?= Constant::$MEAInspection[$model->hydraulic_system] ?></span></label><br>
                            <label name="refrigeration_system">Refrigeration System
                                : <span>
                                            <?= Constant::$MEAInspection[$model->refrigeration_system] ?></span></label><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Electrical Installation</h4>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="electrical_supply">Main Source Electrical Supply
                                : <span>
                                            <?= Constant::$MEAInspection[$model->main_source_electrical_supply] ?></span></label><br>
                            <label name="electrical_system">Electrical system
                                : <span>
                                            <?= Constant::$MEAInspection[$model->electrical_system] ?></span></label><br>
                            <label name="direct_current_sys">Direct Current System
                                : <span>
                                            <?= Constant::$MEAInspection[$model->direct_current_system] ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="alternating_current_sys">Alternating Current System
                                : <span>
                                            <?= Constant::$MEAInspection[$model->alternating_current_system] ?></span></label><br>
                            <label name="earthing_bonding">Earthing & Bonding
                                : <span>
                                            <?= Constant::$MEAInspection[$model->earthing_bonding] ?></span></label><br>
                            <label name="lighting_system">Lighting System
                                : <span>
                                            <?= Constant::$MEAInspection[$model->lighting_system] ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="electric_motors">Electric Motors
                                : <span>
                                            <?= Constant::$MEAInspection[$model->electric_motors] ?></span></label><br>
                            <label name="Conductors_nodes">Conductors, Nodes, Breakers
                                : <span>
                                            <?= Constant::$MEAInspection[$model->conductors_nodes_breakers] ?></span></label><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Fire Protection & Fire Fighting</h4>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="storage_gas">Storage of Gas Cylinders
                                : <span>
                                            <?= Constant::$MEAInspection[$model->storage_of_gas_cylinders] ?></span></label><br>
                            <label name="firefighting_app">Firefighting Appliances
                                : <span>
                                            <?= Constant::$MEAInspection[$model->firefighting_appliances] ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="ventilation_sys">Ventilation System
                                : <span>
                                            <?= Constant::$MEAInspection[$model->ventilation_system] ?></span></label><br>
                            <label name="means_escape">Means of Escape
                                : <span>
                                            <?= Constant::$MEAInspection[$model->means_of_escape] ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="fire_hydrants">Fire Hydrants, Fire Horses & Nozzles
                                : <span>
                                            <?= Constant::$MEAInspection[$model->fire_hydrants_fire_horses_nozzles] ?></span></label><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Protection of Crew & Lifesaving Appliances</h4>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="surfaces_deck">Surfaces of Deck
                                : <span>
                                            <?= Constant::$MEAInspection[$model->surfaces_of_deck] ?></span></label><br>
                            <label name="deck_opening_doors">Deck Opening & Doors
                                : <span>
                                            <?= Constant::$MEAInspection[$model->deck_opening_doors] ?></span></label><br>
                            <label name="bulwark_rails">Bulwark, Rails, & Guards
                                : <span>
                                            <?= Constant::$MEAInspection[$model->bulwark_rails_guards] ?></span></label><br>
                            <!-- <label name="stairways_ladders">Stairways & Ladders : Yes</label><br> -->
                        </div>
                        <div class="col-xl-4">
                            <label name="cooking_facilities">Cooking Facilities
                                : <span>
                                            <?= Constant::$MEAInspection[$model->cooking_facilities] ?></span></label><br>
                            <label name="deck_machinery">Deck Machinery, Tackles & Lifting Gear
                                : <span>
                                            <?= Constant::$MEAInspection[$model->deck_machinery_tackles_lifting_gear] ?></span></label><br>
                            <label name="medical_facilities">Medical facilities
                                : <span>
                                            <?= Constant::$MEAInspection[$model->medical_facilities] ?></span></label><br>
                            <!-- <label name="dangerous_areas">Dangerous Areas : Yes</label><br> -->
                        </div>
                        <div class="col-xl-4">
                            <!-- <label name="life_jackets">Life Jackets & Personal Flotation Devices : Yes</label><br>
                            <label name="lifebuoys">Lifebuoys : Yes</label><br> -->
                            <label name="distress_signals">Distress Signals
                                : <span>
                                            <?= Constant::$MEAInspection[$model->distress_signals] ?></span></label><br>
                            <label name="stairways_ladders">Stairways & Ladders
                                : <span>
                                            <?= Constant::$MEAInspection[$model->stairways_ladders] ?></span></label><br>
                            <label name="dangerous_areas">Dangerous Areas
                                : <span>
                                            <?= Constant::$MEAInspection[$model->dangerous_areas] ?></span></label><br>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6">
                            <label name="life_jackets">Life Jackets & Personal Flotation Devices comply with
                                instruction manual and routing service and maintains have been done
                                : <span>
                                            <?= Constant::$MEAInspection[$model->life_jackets_personal_flotation_devices] ?></span></label><br>
                        </div>
                        <div class="col-xl-6">
                            <label name="lifebuoys">No. of Lifebuoys is dependend on the length of boats and
                                routing service and maintenance have been done
                                : <span>
                                            <?= Constant::$MEAInspection[$model->lifebuoys] ?></span></label><br>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Radio Communication & Navigational Equipment</h4>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="source_energy">Source of Energy
                                : <span>
                                            <?= Constant::$MEAInspection[$model->source_of_energy] ?></span></label><br>
                            <label name="radio_installation">Radio Installation & Equipment
                                : <span>
                                            <?= Constant::$MEAInspection[$model->radio_installation_equipment] ?></span></label><br>
                            <label name="magnetic_compass">Magnetic Compass
                                : <span>
                                            <?= Constant::$MEAInspection[$model->magnetic_compass] ?></span></label><br>
                            <label name="gps_satellite">GPS/Satellite Navigation System
                                : <span>
                                            <?= Constant::$MEAInspection[$model->gps_satellite_navigation_system] ?></span></label><br>
                            <label name="depth_finding">Means Depth Finding
                                : <span>
                                            <?= Constant::$MEAInspection[$model->means_depth_finding] ?></span></label><br>
                            <label name="ais_buoys">AIS with Buoys : <span>
                                            <?= Constant::$MEAInspection[$model->ais_buoys] ?? "" ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="nautical_instrument">Nautical Instruments & Publications
                                : <span>
                                            <?= Constant::$MEAInspection[$model->nautical_instruments_publications] ?></span></label><br>
                            <label name="signaling_system">Signaling System
                                : <span>
                                            <?= Constant::$MEAInspection[$model->signaling_system] ?></span></label><br>
                            <label name="navigation_bridge">Navigation Bridge Visibility
                                : <span>
                                            <?= Constant::$MEAInspection[$model->navigation_bridge_visibility] ?></span></label><br>
                            <label name="navigation_lights">Navigation Lights
                                : <span>
                                            <?= Constant::$MEAInspection[$model->navigation_lights] ?></span></label><br>
                            <label name="crew_accommodation">Crew Accommodation
                                : <span>
                                            <?= Constant::$MEAInspection[$model->crew_accommodation] ?></span></label><br>
                            <label name="vms_install">VMS Installation (Active/ Deactive) :-</label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="lighting_heating">Lighting, Heating, & Ventilating
                                : <span>
                                            <?= Constant::$MEAInspection[$model->lighting_heating_ventilating] ?></span></label><br>
                            <label name="sleeping_spaces">Sleeping Spaces
                                : <span>
                                            <?= Constant::$MEAInspection[$model->sleeping_spaces] ?></span></label><br>
                            <label name="eating_spaces">Eating Spaces & Cooking Facilities
                                : <span>
                                            <?= Constant::$MEAInspection[$model->eating_spaces_cooking_facilities] ?></span></label><br>
                            <label name="sanitary_facilities">Sanitary Facilities
                                : <span>
                                            <?= Constant::$MEAInspection[$model->sanitary_facilities] ?></span></label><br>
                            <label name="water_facilities">Water Facilities
                                : <span>
                                            <?= Constant::$MEAInspection[$model->water_facilities] ?></span></label><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Emergency Measures</h4>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="Sec_means_starting">Secondary Means of Starting
                                : <span>
                                            <?= Constant::$MEAInspection[$model->secondary_means_of_starting] ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="Emergency_ste_arrangement">Emergency Steering Arrangement
                                : <span>
                                            <?= Constant::$MEAInspection[$model->emergency_steering_arrangement] ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="source_elec_power">Emergency Source of Electrical Power
                                : <span>
                                            <?= Constant::$MEAInspection[$model->emergency_source_of_electrical_power] ?></span></label><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <br><label name="machinery_spares">Does the vessel carry the essential machinery
                                spares and tools required to carry out minor and/or emergency repairs
                                : <span>
                                            <?= $model->essential_machinery_spares_and_tools_required ?></span></label><br>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="call_sign">Call Sign Number
                                : <span>
                                            <?= $model->call_sign_number ?></span></label><br>
                            <label name="ais_model">Radio Model & Serial No. :-</label><br>
                        </div>
                        <div class="col-xl-4">
                            <!-- <label name="vms_number">VMS Number : 123456</label><br> -->
                            <label name="vms_number">VMS Model & Serial No.
                                : <span>
                                            <?= $model->vms_number ?></span></label><br>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Date Of Repairs/ Approved Modifications Carried Out </h4>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="hull">Hull
                                : <span>
                                            <?= $model->date_of_repairs_approved_modifications_hull ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="machinery">Machinery
                                : <span>
                                            <?= $model->date_of_repairs_approved_modifications_machinery ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="running_trail">Running Trail Carried Out On
                                : <span>
                                            <?= $model->running_trail_carried_out_on ?></span></label><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Remarks Regarding</h4>
                    <div class="row">
                        <div class="col-xl-6">
                            <label name="machine_operation">Machinery Operation
                                : <span>
                                            <?= Constant::$MEAInspection[$model->machinery_operation] ?></span></label><br>
                            <label name="maneuverability">Maneuverability :
                                <span><?= Constant::$MEAInspection[$model->maneuverability]
                                    ?></span></label><br>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <label>This is to certify that the above named/numbered vessel was dully inspected
                                by me and found to be in a fit and seaworthy condition to operate as a within
                                the limit of
                                <span>
                                            <?= Constant::$MEADeclaration[$model->declaration] ?></span>
                            </label><br>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-10">
                            <label name="next_inspect">Date on or before which the vessel should be next
                                inspected : <span>
                                            <?= $model->next_inspected_date ?></span></label><br>
                            <label name="next_inspect">Certificate Number
                                : <span>
                                            <?= $model->mea_certificate_number ?></span></label><br>
                            <label name="prof_crew">Number of Professional & Competent Crew (Minimum)
                                : <span>
                                            <?= $model->number_of_professional_competent_crew ?></span></label><br>
                        </div>
                        <div class="col-xl-2">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Total Number of Passengers (Maximum)</h4>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="coxswain">Name of Coxswain/ Certificate No.
                                : <span>
                                            <?= $model->name_of_coxswain_certificate_no ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="name_engine_driver">Name of Engine Driver/ Certificate No.
                                : <span>
                                            <?= $model->name_of_engine_driver_certificate_no ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="other_info">Other Information
                                : <span>
                                            <?= $model->other_information ?></span></label><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Value of Boat </h4>
                    <div class="row">
                        <div class="col-xl-6">
                            <label name="hull_val">Hull : <span>
                                            <?= $model->value_of_boat_hull ?></span></label><br>
                            <label name="engine_val">Engine :
                                <span><?= $model->value_of_boat_engine ?></span></label><br>
                        </div>
                        <div class="col-xl-6">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Value of Vessel Equipment</h4>
                    <div class="row">
                        <div class="col-xl-4">
                            <label name="compass">Compass : <span>
                                            <?= $model->compass ?></span></label><br>
                            <label name="radio">Radio : <span><?= $model->radio ?></span></label><br>
                            <label name="gps">GPS : <span>
                                            <?= $model->gps ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="radar">RADAR : <span><?= $model->radar ?></span></label><br>
                            <label name="ais">AIS : <span>
                                            <?= $model->ais ?></span></label><br>
                        </div>
                        <div class="col-xl-4">
                            <label name="winch">Winch : <span><?= $model->winch ?></span></label><br>
                            <label name="vms">VMS : <span>
                                            <?= $model->vms ?></span></label><br>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="form-group">
                    <h4>Fishing Gear</h4>
                    <div class="row">
                        <div class="col-xl-6">
                            <label name="gillnets">Gillnets : <span><?= $model->gillnets ?></span></label><br>
                            <label name="longlines">Longlines : <span>
                                            <?= $model->longlines ?></span></label><br>
                            <label name="other">Other : <span><?= $model->other ?></span></label><br>
                            <label name="total">Total : <span>
                                            <?= $model->total ?></span></label><br>
                        </div>
                        <div class="col-xl-6">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--    --><?php //Pjax::begin(['id' => 'boat-numbers-log']); ?>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">


                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="card-subtitle mb-2 text-muted">Activity Log</h3>
                            </div>
                            <div class="col-lg-12">

                                <table class="table ">
                                    <thead class="table-dark">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Remarks</th>
                                        <th scope="col">Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($approvalHistory != null) foreach ($approvalHistory as $item) { ?>
                                        <tr>
                                            <th scope="row"><?= $item->doneBy->nic ?> </th>
                                            <td><?= UserTypeUtil::getTypeNames($item->doneBy->type) ?? "Undefined" ?> </td>
                                            <td><?= $item->status ?></td>
                                            <td><?= $item->remark ?></td>
                                            <td><?= $item->date_time ?></td>
                                        </tr>
                                    <?php } ?>


                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (Util::editPermission() && $showApproveBtn) { ?>
                            <hr>
                            <?php $form = ActiveForm::begin(['options' => [
                                'class' => 'userform'
                            ]]); ?>
                            <div class="col-xl-12">
                                <div class="row">

                                    <div class="col-xl-3">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select id="status-approval"
                                                    name="status-approval" <?= !$validated ? "disabled" : "" ?>
                                                    class="form-control">
                                                <option value="approve">Approve</option>
                                                <option value="reject">Reject</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-xl-9">
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <textarea id="remarks-approval"
                                                      name="remarks-approval" <?= !$validated ? "disabled" : "" ?> class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <?php
                                            if ($validated) {
                                                ?>
                                                <input type="submit" id="status-approval-btn" value="Submit"
                                                       class="btn btn-primary btn-block mt-5">
                                                <?php
                                            } else {
                                                ?>
                                                <label class="btn btn-danger">Please fill out all required file and
                                                    upload
                                                    all required documents to continue</label>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                        <?php } ?>


                    </div>
                </div>


                <div class="card mb-5 shadow-sm">
                    <div class="card-body">



                        <?php if ($model->status == 100) { ?>
                            <hr>
                            <?php $form = ActiveForm::begin(['options' => [
                                'class' => 'userform'
                            ]]); ?>
                            <div class="col-xl-12">
                                <div class="row">

                                    <div class="col-xl-3">
                                        <input type="hidden" value="<?= $model->id ?>" id="highseas_license_id"/>
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select id="yard-payment-approval" name="status-approval"
                                                    class="form-control">
                                                <option value="approve">Approve</option>
                                                <option value="reject">Reject</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-xl-9">
                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <textarea id="yard-payment-remarks-approval" name="remarks-approval"
                                                      class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <button type="button" id="highseas-payment-approval-btn"
                                                    class="btn btn-primary btn-block mt-5">Submit
                                            </button>


                                        </div>
                                    </div>

                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                        <?php } ?>


                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--    --><?php //Pjax::end(); ?>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="row">
            <div class="col-xl-3">
            </div>
            <div class="col-xl-3">
            </div>
            <div class="col-xl-3"><br>
                <div class="form-group">
                </div>
            </div>
            <div class="col-xl-3"><br>
                <div class="form-group">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal"
                            data-target="#ApproveModel">Close
                    </button>
                </div>
            </div>
        </div>
    </div>


</div>

