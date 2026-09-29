<?php

use backend\config\Constant;
use backend\services\CommonService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\MeaBoatRegistration $model */
/** @var yii\widgets\ActiveForm $form */
$model->vessel_type = $boat->boatType->id
?>

<div class="mea-boat-registration-form">

    <?php $form = ActiveForm::begin(['options' => [
        'class' => 'userform'
    ]]); ?>
    <?php

    /** @var yii\web\View $this */

    ?>
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
                                                : <?= $boat->boatType->code ?></label> <br>
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
                                    <td><?= $boat->owner0->first_name ?> <?= $boat->owner0->last_name ?></td>
                                    <td><?= $boat->owner0->mobile ?> </td>
                                    <td><?= $boat->owner0->permanent_address ?> </td>
                                    <td><?= $boat->owner0->nic ?> </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-xl-12">
                                <label name="yard_name">Boat Reg Number
                                    : <?= $boat->boat_number ?></label><br><br>
                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'hull_number')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'vessel_type')->dropDownList(CommonService::getBoatCategoriesArray(), ['prompt' => "select..."]) ?>
                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'reg_fishing_gear')->dropDownList(CommonService::getMainGearTypesArray(), ['prompt' => "select..."]) ?>

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
                            <div class="col-xl-12">
                                <label name="yard_name">Name of Boat Builder/Boat Yard : Test Yard</label><br>
                                <label name="yard_no">Yard Reg. & Design Reg. No. : Test Yard</label><br><br>
                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'light_weight_type_of_vessel')->textInput(['maxlength' => true]) ?>


                            </div>

                            <div class="col-xl-6">


                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-4">
                                <?= $form->field($model, 'commenced_construction_date')->textInput(["type" => "date","max"=> date('Y-m-d')
                                    ]) ?>

                            </div>

                            <div class="col-xl-4">
                                <?= $form->field($model, 'completed_construction_date')->textInput(["type" => "date","max"=> date('Y-m-d')]) ?>

                            </div>

                            <div class="col-xl-4">
                                <?= $form->field($model, 'date_of_build')->textInput(["type" => "date","max"=> date('Y-m-d')]) ?>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <?= $form->field($model, 'material_of_hull_as_approved')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'gross_tonns')->textInput() ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'volume')->textInput() ?>

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
                        <h4>Dimensions</h4>
                        <div class="row">
                            <div class="col-xl-6">
                                <?= $form->field($model, 'overall_length')->textInput() ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'beam')->textInput() ?>

                            </div>

                            <div class="col-xl-6">
                                <?= $form->field($model, 'depth')->textInput() ?>
                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'draught')->textInput() ?>

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
                                <?= $form->field($model, 'engine_type')->dropDownList(Constant::$engineTypes, ["prompt" => "Select"]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'fuel_type')->dropDownList(Constant::$fuelType, ["prompt" => "Select"]) ?>

                            </div>
                            <div class="col-xl-6">

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <?= $form->field($model, 'number_of_cylinders')->textInput() ?>
                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'horse_power_of_engine')->textInput() ?>

                            </div>
                            <div class="col-xl-6">
                                <!-- <label>Make/Model</label><br> -->
                                <!-- <input type="text" name="make_model" class="form-control"/><br> -->
                                <?= $form->field($model, 'engine_model')->dropDownList(Constant::$MEAEngineModel, ["prompt" => "Select"]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'engine_number')->textInput(['maxlength' => true]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'propeller_diameter_pitch_no_of_blades')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'gear_ratio')->textInput(['maxlength' => true]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'steering_gear_type')->textInput(['maxlength' => true]) ?>

                            </div>
                            <!--                            <div class="col-xl-6">-->
                            <!--                                --><?php //= $form->field($model, 'engine_model_number')->textInput(['maxlength' => true]) ?>
                            <!---->
                            <!--                            </div>-->

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
                                <?= $form->field($model, 'fuel_oil')->textInput() ?>


                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'fresh_water')->textInput() ?>


                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'chilled_bath')->textInput() ?>

                            </div>

                            <div class="col-xl-6">
                                <?= $form->field($model, 'fish_hold')->textInput() ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'stores')->dropDownList(Constant::$ScoolingSystem, ['prompt' => "select..."]) ?>

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
                                <?= $form->field($model, 'designed_speed')->textInput(['maxlength' => true]) ?>

                            </div>

                            <div class="col-xl-6">
                                <!-- <label>Inspection Date</label><br>
                                <input type="date" name="inspection_date" class="form-control"/><br> -->
                            </div>
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
                        <!-- <h4>Test Data</h4> -->
                        <div class="row">
                            <div class="col-xl-4">
                                <?= $form->field($model, 'place_of_inspectio')->textInput(['maxlength' => true]) ?>

                            </div>

                            <div class="col-xl-4">
                                <?= $form->field($model, 'inspection_date')->textInput(["type" => "date","min"=> date
                                ('Y-m-d')]) ?>

                            </div>

                            <div class="col-xl-4">
                                <?= $form->field($model, 'vessel_at_the_time_of_inspection')->dropDownList(Constant::$MEAWhereWasVessel, ["prompt" => "Select"]) ?>

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
                        <h4>Construction, Watertight Integrity & Equipment </h4>
                        <div class="row">
                            <div class="col-xl-6">
                                <?= $form->field($model, 'hull_hull_framing')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'inlets_discharges')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'deck_deck_framing')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'bulk_head')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'weather_tight_doors')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'fishing_gear_symbol')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>


                            </div>


                            <div class="col-xl-6">
                                <?= $form->field($model, 'hatch_way_coamings')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>
                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'machinery_space_opening')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'deck_opening')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'pipes_bunkering_inlets')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'freeing_ports')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'gear_marking')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'dehooker_line_cutter')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

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
                            <div class="col-xl-6">
                                <?= $form->field($model, 'bow_height')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'maximum_draught')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'water_tanks_partitions')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'fuel_tanks_partitions')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'portable_fish_hold_divisions')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>


                            <div class="col-xl-6">
                                <?= $form->field($model, 'chilled_bath_partitions')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'bait_hold_arrangement')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'stores_cargo_hold_constructions')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'draught_marks')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'stability_notice')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

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
                            <div class="col-xl-6">
                                <?= $form->field($model, 'layout_of_machinery_space')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'propulsion_machinery_steering_gear')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'engine_console')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'lighting')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'floor')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'ventilation')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'sound_vibration')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'engine_mounting')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>


                            <div class="col-xl-6">
                                <?= $form->field($model, 'console_monitoring_instruments')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'fuel_oil_installation')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'cooling_water_system')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'bilge_pumping_systems')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'exhaust_systems')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'hydraulic_system')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'refrigeration_system')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

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
                            <div class="col-xl-6">
                                <?= $form->field($model, 'main_source_electrical_supply')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'electrical_system')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'direct_current_system')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'alternating_current_system')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'earthing_bonding')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'lighting_system')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'electric_motors')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'conductors_nodes_breakers')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

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
                            <div class="col-xl-6">
                                <?= $form->field($model, 'storage_of_gas_cylinders')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'firefighting_appliances')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'ventilation_system')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'means_of_escape')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'fire_hydrants_fire_horses_nozzles')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

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
                            <div class="col-xl-6">
                                <?= $form->field($model, 'surfaces_of_deck')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'deck_opening_doors')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'bulwark_rails_guards')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'stairways_ladders')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'cooking_facilities')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'deck_machinery_tackles_lifting_gear')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'medical_facilities')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'dangerous_areas')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'life_jackets_personal_flotation_devices')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'lifebuoys')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'distress_signals')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

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
                        <h4>Active Radio Communication & Navigational Equipment</h4>
                        <div class="row">
                            <div class="col-xl-6">
                                <?= $form->field($model, 'source_of_energy')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'radio_installation_equipment')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'magnetic_compass')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'gps_satellite_navigation_system')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'means_depth_finding')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'nautical_instruments_publications')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'signaling_system')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'navigation_bridge_visibility')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'navigation_lights')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'crew_accommodation')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'lighting_heating_ventilating')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'sleeping_spaces')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'eating_spaces_cooking_facilities')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'sanitary_facilities')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'water_facilities')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'ais_buoys')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

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
                            <div class="col-xl-6">
                                <?= $form->field($model, 'secondary_means_of_starting')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'emergency_steering_arrangement')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'emergency_source_of_electrical_power')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <?= $form->field($model, 'essential_machinery_spares_and_tools_required')->dropDownList(Constant::$yesNo, ["prompt" => "Select..."]) ?>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <?= $form->field($model, 'call_sign_number')->textInput(['maxlength' => true]) ?>

                                <!-- <label>Call Sign Number </label><br>
                                <input type="text" name="call_sign" class="form-control"/><br>
                                <label>Call Sign Number </label><br>
                                <input type="text" name="call_sign" class="form-control"/><br> -->
                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'vms_number')->textInput(['maxlength' => true]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'vms_installation')->dropDownList(Constant::$actDeact) ?>
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
                                <?= $form->field($model, 'date_of_repairs_approved_modifications_hull')->textInput(["type" => "date","max"=> date('Y-m-d')]) ?>

                            </div>
                            <div class="col-xl-4">
                                <?= $form->field($model, 'date_of_repairs_approved_modifications_machinery')->textInput(["type" => "date","max"=> date('Y-m-d')]) ?>

                            </div>
                            <div class="col-xl-4">

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="section-block" id="cards">
                <h3 class="card-title"> Section 03</h3>
            </div>
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <h4>Remarks Regarding</h4>
                        <div class="row">
                            <div class="col-xl-6">
                                <?= $form->field($model, 'running_trail_carried_out_on')->textInput(["type" => "date","max"=> date('Y-m-d')]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'maneuverability')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'machinery_operation')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>


                                <!-- <label>Propulsion Efficiency</label><br>
                                <select name="propulsion_efficiency" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Satisfactory</option>
                                    <option>Unsatisfactory</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Fuel Consumption</label><br>
                                <select name="fuel_consumption" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Satisfactory</option>
                                    <option>Unsatisfactory</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> -->
                            </div>


                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <?= $form->field($model, 'declaration')->dropDownList(Constant::$MEADeclaration, ["prompt" => "Select..."]) ?>

                                </label>
                            </div>

                            <div class="col-xl-6">

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <?= $form->field($model, 'next_inspected_date')->textInput(["type" => "date", "min" =>
                                    date('Y-m-d')]) ?>

                            </div>

                            <div class="col-xl-6">

<!--                                --><?php //= $form->field($model, 'mea_certificate_number')->textInput(['maxlength' => true]) ?>

                                <!-- (Format Year + BoatNo + FI_DivisionNo + 0001)  Auto Generate -->
                            </div>
                            <div class="col-xl-6">

                                <!-- <label>Number of Crew (Minimum) </label><br> -->
                                <?= $form->field($model, 'number_of_professional_competent_crew')->textInput() ?>

                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'number_of_professional_competent_crew_minimum')->textInput() ?>
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
                        <!--                        <h4>Total Number of Passengers (Maximum)</h4>-->
                        <div class="row">
                            <div class="col-xl-6">
                                <?= $form->field($model, 'name_of_coxswain_certificate_no')->textInput(['maxlength' => true]) ?>

                            </div>
                            <div class="col-xl-6">


                                <?= $form->field($model, 'name_of_engine_driver_certificate_no')->textInput(['maxlength' => true]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'other_information')->textInput(['maxlength' => true]) ?>

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
                        <h4>Value of Boat </h4>
                        <div class="row">
                            <div class="col-xl-6">
                                <?= $form->field($model, 'value_of_boat_hull')->textInput() ?>
                            </div>

                            <div class="col-xl-6">

                                <?= $form->field($model, 'value_of_boat_engine')->textInput() ?>

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
                            <div class="col-xl-6">
                                <?= $form->field($model, 'compass')->textInput(['maxlength' => true]) ?>

                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'radio')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'gps')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'radar')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-xl-6">
                                <?= $form->field($model, 'ais')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-xl-6">

                                <?= $form->field($model, 'winch')->textInput(['maxlength' => true]) ?>
                            </div>

                            <div class="col-xl-6">

                                <?= $form->field($model, 'vms')->textInput(['maxlength' => true]) ?>
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
                                <?= $form->field($model, 'gillnets')->textInput(['maxlength' => true]) ?>

                                <?= $form->field($model, 'longlines')->textInput(['maxlength' => true]) ?>

                                <?= $form->field($model, 'other')->textInput(['maxlength' => true]) ?>

                                <?= $form->field($model, 'total')->textInput(['maxlength' => true,"readOnly"=>true]) ?>
                            </div>
                            <div class="col-xl-6">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>


<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

</div>
