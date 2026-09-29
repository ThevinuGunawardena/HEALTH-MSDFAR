<?php

use backend\config\Constant;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\MeaBoatRegistration $model */
/** @var yii\widgets\ActiveForm $form */
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
                                                : <?= $boat->boatNumber->boatType->code ?? "" ?></label> <br>
                                            : <?= $boat->boatNumber->boatType->code ?? "" ?></label> <br>

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

                        <!--                        <div class="form-group">-->
                        <!--                            <div class="row">-->
                        <!--                                <div class="col-xl-6">-->
                        <!--                                    <label name="yard_name">Boat Reg Number : IMULA08888NBO</label><br>-->
                        <!--                                    <label  name="hull_number">Hull number of vessel : 26456456378 </label><br>-->
                        <!--                                    <label name="gear_ratio">Gear Ratio : Test</label><br>-->
                        <!--                                </div>-->
                        <!--                                <div class="col-xl-6">-->
                        <!--                                    <label name="vessel_type">Type of Vessel : Test</label><br>-->
                        <!--                                    <label name="reg_fishing_gear">Registered Fishing Gear :Test</label><br>-->
                        <!--                                </div>-->
                        <!--                            </div>-->
                        <!--                        </div>-->
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
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'engine_model_number')->textInput(['maxlength' => true]) ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!--        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">-->
            <!--            <div class="card mb-5 shadow-sm">-->
            <!--                <div class="card-body">-->
            <!--                    <div class="form-group">-->
            <!--                        <h4>Full Capacities of Tanks</h4>-->
            <!--                        <div class="row">-->
            <!--                            <div class="col-xl-6">-->
            <!---->
            <!--                                <label name="capacity_fuel">Fuel Tank Capacity :10</label><br>-->
            <!--                                <label name="design_speed">Designed Speed :100	</label><br>-->
            <!---->
            <!--                            </div>-->
            <!--                            <div class="col-xl-6">-->
            <!---->
            <!---->
            <!--                                <label name="model_no">Maximum Range on Fuel Tank :20</label><br>-->
            <!--                                <label name="steering_gear_type">Steering Gear Type :Test</label><br>-->
            <!---->
            <!--                            </div>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->


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
                                    <?= $form->field($model, 'inspection_date')->textInput(["type" => "date"]) ?>

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
                            <h4>Condition of </h4>
                            <div class="row">
                                <div class="col-xl-4">
                                    <?= $form->field($model, 'condition_hull_internal')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-4">
                                    <?= $form->field($model, 'condition_hull_external')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-4">
                                    <?= $form->field($model, 'condition_hull_sheathing')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                            </div>
                            <div class="row">

                                <div class="col-xl-6">

                                    <?= $form->field($model, 'condition_decks')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">

                                    <?= $form->field($model, 'condition_steering_gear')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">

                                    <?= $form->field($model, 'condition_cargo_compartment')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">

                                    <?= $form->field($model, 'condition_anchor_cables')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">

                                    <?= $form->field($model, 'condition_framework_timbers_internals')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>


                                </div>


                                <div class="col-xl-6">
                                    <?= $form->field($model, 'condition_navigation_lights')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>
                                </div>
                                <div class="col-xl-6">

                                    <?= $form->field($model, 'condition_machinery')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'condition_rudder')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'condition_date_last_overhaul')->textInput(["type" => "date"]) ?>

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
                            <h4>Is the vessel equipped with any of the following equipment if so</h4>
                            <div class="row">
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'equipped_compass')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'equipped_bailers')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'equipped_life_saving_appliances')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'equipped_first_aid_equipment')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'equipped_fire_extingulshers_type')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>


                                <div class="col-xl-6">
                                    <?= $form->field($model, 'equipped_navigation_equipment')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'equipped_bilge_pump')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'equipped_gps_available')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'equipped_vms_installation')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'equipped_emergency_repairs')->dropDownList(Constant::$MEAInspection, ["prompt" => "Select..."]) ?>

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
                                    <?= $form->field($model, 'date_of_repairs_approved_modifications_hull')->textInput(["type" => "date"]) ?>

                                </div>
                                <div class="col-xl-4">
                                    <?= $form->field($model, 'date_of_repairs_approved_modifications_machinery')->textInput(["type" => "date"]) ?>

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
                                    <?= $form->field($model, 'running_trail_carried_out_on')->textInput(["type" => "date"]) ?>

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
                                    <?= $form->field($model, 'next_inspected_date')->textInput(["type" => "date"]) ?>

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
                            <h4>Vessel Equipment</h4>
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

                                    <?= $form->field($model, 'total')->textInput(['maxlength' => true]) ?>
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
