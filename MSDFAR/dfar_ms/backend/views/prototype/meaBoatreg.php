<?php

/** @var yii\web\View $this */

$this->title = 'MEA Inspection - Boat Registration';
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
                                        <label name="boat_type">Boat Type : IMUL</label> <br>
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
                                    <td>A.B.C Perera</td>
                                    <td>0112589654</td>
                                    <td>No.125, Colombo Road, Negombo.</td>
                                    <td>785241258V</td>
                                </tr>                            
                            </tbody>
                        </table>
                    </div>
                
                    <div class="form-group">                        
                        <div class="row">
                            <div class="col-xl-6">
                                <label name="yard_name">Boat Reg Number : IMULA08888NBO</label><br><br>
                                <label>Hull number of vessel </label><br>
                                <input type="text" name="hull_number" class="form-control"/><br> 

                                <label>Type of Vessel</label><br> 
                                <select name="vessel_type" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>AAAAAAAAAAA</option>
                                    <option>BBBBBBBBBBB</option>
                                    <option>CCCCCCCCCCC</option>
                                </select> <br>

                                <label>Registered Fishing Gear</label><br>
                                <input type="text" name="reg_fishing_gear" class="form-control" readonly/><br> 

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
                                <label name="yard_name">Name of Boat Builder/Boat Yard  : Test Yard</label><br>
                                <label name="yard_no">Yard Reg. & Design Reg. No. : Test Yard</label><br><br>                           
                                
                                <label>Light Weight & Type of Vessel</label><br>
                                <input type="text" name="length_weight" class="form-control"/><br>                           
                                                      
                            </div>

                            <div class="col-xl-6">
                                
                            
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-4">
                                <label>Commenced Construction Date </label><br>
                                <input type="date" name="commence_contruct_date" class="form-control"/><br>    
                            </div>

                            <div class="col-xl-4">
                                <label>Completed Construction Date</label><br>
                                <input type="date" name="completed_contruct_date" class="form-control"/><br>
                            </div>

                            <div class="col-xl-4">
                                <label>Date of Build </label><br>
                                <input type="date" name="date_build" class="form-control"/><br>                            
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <label>Material of Hull as Approved</label><br>
                                <input type="text" name="mat_of_hull" class="form-control"/><br> 
                                
                                <label>Gross Tonns (tons)</label><br>
                                <input type="text" name="gross_tons" class="form-control"/><br>  
                                
                                <label>Volume (m3)</label><br>
                                <input type="text" name="volume" class="form-control"/><br>
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
                                <label>Overall Length (feet)</label><br>
                                <input type="text" name="overall_length" class="form-control"/><br>

                                <label>Beam (feet)</label><br>
                                <input type="text" name="beam" class="form-control"/><br>
                            </div>

                            <div class="col-xl-6">
                                <label>Depth  (feet)</label><br>
                                <input type="text" name="depth" class="form-control"/><br>

                                <label>Draught(feet)</label><br>
                                <input type="text" name="draught" class="form-control"/><br>
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
                                <label>Engine Type</label><br>
                                <select name="engine_type" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Inboard</option>
                                    <option>Outboard</option>
                                    <option>N/A</option>
                                </select> <br>

                                <label>Fuel Type</label><br>
                                <select name="fuel_type" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Diesel</option>
                                    <option>Petrol</option>
                                    <option>Kerosene</option>
                                    <option>N/A</option>
                                </select><br>                                
                            </div>
                            <div class="col-xl-6">
                                
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <label>Number of Cylinders</label><br>
                                <input type="text" name="no_cylinder" class="form-control"/><br>
                                <label>Horse Power of Engine</label><br>
                                <input type="text" name="hp" class="form-control"/><br>
                                <!-- <label>Make/Model</label><br> -->
                                <!-- <input type="text" name="make_model" class="form-control"/><br> -->
                                <label>Engine Model</label><br>
                                <select name="engine_model" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Isuzu</option>
                                    <option>Honda</option>
                                    <option>Hundai</option>
                                    <option>Other</option>
                                </select><br>          
                                <label>Engine Number</label><br>
                                <input type="text" name="engine_no" class="form-control"/><br>
                            </div>
                            <div class="col-xl-6">
                                <label>Propeller Diameter/ Pitch & No. of Blades</label><br>
                                <input type="text" name="propeller_diameter" class="form-control"/><br>
                                <label>Gear Ratio</label><br>
                                <input type="text" name="gear_ratio" class="form-control"/><br>
                                <label>Steering Gear Type</label><br>
                                <input type="text" name="steering_gear_type" class="form-control"/><br>
                                <label>Engine Model Number</label><br>
                                <input type="text" name="model_no" class="form-control"/><br>                                                
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
                                <label>Fuel Oil	(L)</label><br>
                                <input type="text" name="fuel" class="form-control"/><br>

                                <label>Fresh Water (L)</label><br>
                                <input type="text" name="fresh_water" class="form-control"/><br>

                                <label>Chilled Bath (m3)</label><br>
                                <input type="text" name="chilled_bath" class="form-control"/><br>
                            </div>

                            <div class="col-xl-6">
                                <label>Fish Hold (m3)</label><br>
                                <input type="text" name="fish_hold" class="form-control"/><br>

                                <label>Stores (m3)</label><br>
                                <input type="text" name="stores" class="form-control"/><br>
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
                                <label>Designed Speed	</label><br>
                                <input type="text" name="design_speed" class="form-control"/><br>                               
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
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <h4>Auxiliary Cooling Systems</h4>
                        <div class="row">
                            <div class="col-xl-6">
                                <label>Type</label><br>
                                <input type="text" name="cooling_sys_type" class="form-control"/><br> 
                                
                                <label>Cooling Capacity</label><br>
                                <input type="text" name="cooling_capacity" class="form-control"/><br>   
                            </div>

                            <div class="col-xl-6">
                                <label>Model</label><br>
                                <input type="text" name="cooling_sys_model" class="form-control"/><br>   
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
                                <label>Place of Inspection</label><br>
                                <input type="text" name="place_inpection" class="form-control"/><br>
                            </div>

                            <div class="col-xl-4">
                                <label>Inspection Date</label><br>
                                <input type="date" name="inspection_date2" class="form-control"/><br>
                            </div>

                            <div class="col-xl-4">
                                <label>Was vessel at the time of inspection?</label><br>
                                <select name="fuel_type" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Afloat</option>
                                    <option>Beached</option>
                                    <option>Slipway</option>
                                    <option>Dry-dock</option>
                                    <option>N/A</option>
                                </select><br>   
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
                                <label>Hull & Hull Framing</label><br>
                                <select name="hull" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Inlets & Discharges</label><br>
                                <select name="inlets" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Deck & Deck Framing	</label><br>
                                <select name="deck" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Bulk Head</label><br>
                                <select name="bulk_head" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Weather Tight Doors/ Windows</label><br>
                                <select name="door_windows" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <!-- <label>Fishing Gear Symbol</label><br>
                                <select name="fishing_gear_symbol" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>  -->


                                <label>Gear Marking</label><br>
                                <select name="gear_marking" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 
                            </div>

                            
                            <div class="col-xl-6">
                                <label>Hatch Way Coamings</label><br>
                                <select name="hatch" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Machinery Space Opening</label><br>
                                <select name="machinery_space" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Deck Opening</label><br>
                                <select name="deck_opening" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Pipes, Bunkering Inlets</label><br>
                                <select name="pipes_bunkering" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Freeing Ports</label><br>
                                <select name="freeing_ports" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Dehooker and Line Cutter</label><br>
                                <select name="line_cutter" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 
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
                                <label>Bow Height</label><br>
                                <select name="bow_height" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Maximum Draught</label><br>
                                <select name="max_draught" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Water Tanks Partitions</label><br>
                                <select name="water_tank" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Fuel Tanks Partitions</label><br>
                                <select name="fuel_tank" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Portable Fish Hold Divisions</label><br>
                                <select name="pot_fish_hold" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 
                            </div>

                           
                            <div class="col-xl-6">
                                <label>Chilled Bath Partitions</label><br>
                                <select name="chill_bath" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Bait Hold Arrangement	</label><br>
                                <select name="bait_hold" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                                                                                
                                <label>Stores/ Cargo Hold Constructions	</label><br>
                                <select name="stores_cargo" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Draught Marks</label><br>
                                <select name="draught_marks	" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Stability Notice	</label><br>
                                <select name="stability_notice" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 
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
                                <label>Layout of Machinery Space</label><br>
                                <select name="layout_machine_space" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Propulsion Machinery & Steering Gear	</label><br>
                                <select name="propulsion_machinary" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Engine Console</label><br>
                                <select name="engine_console" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Lighting</label><br>
                                <select name="lighting" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Floor </label><br>
                                <select name="floor" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Ventilation</label><br>
                                <select name="ventilation" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Sound & Vibration </label><br>
                                <select name="floor" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Engine Mounting </label><br>
                                <select name="engine_mounting" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 
                            </div>

                           
                            <div class="col-xl-6">
                                <label>Console & Monitoring Instruments</label><br>
                                <select name="console_monitoring" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Fuel Oil Installation</label><br>
                                <select name="fuel_install" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                                                                                
                                <label>Cooling Water System	</label><br>
                                <select name="cooling_water" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Bilge Pumping Systems	</label><br>
                                <select name="bilge_pumping	" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Exhaust Systems</label><br>
                                <select name="exhaust_systems" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Hydraulic System</label><br>
                                <select name="hydraulic_system" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Refrigeration System</label><br>
                                <select name="refrigeration_system" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
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
                                <label>Main Source Electrical Supply</label><br>
                                <select name="electrical_supply" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Electrical system</label><br>
                                <select name="electrical_system" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Direct Current System</label><br>
                                <select name="direct_current_sys" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Alternating Current System</label><br>
                                <select name="alternating_current_sys" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 
                            </div>                           
                            <div class="col-xl-6">
                                <label>Earthing & Bonding</label><br>
                                <select name="earthing_bonding" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Lighting System</label><br>
                                <select name="lighting_system" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                                                                                
                                <label>Electric Motors</label><br>
                                <select name="electric_motors" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Conductors, Nodes, Breakers</label><br>
                                <select name="Conductors_nodes" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 
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
                                <label>Storage of Gas Cylinders	</label><br>
                                <select name="storage_gas" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Firefighting Appliances	</label><br>
                                <select name="firefighting_app" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Ventilation System</label><br>
                                <select name="ventilation_sys" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 
                            </div>                           
                            <div class="col-xl-6">
                                <label>Means of Escape</label><br>
                                <select name="means_escape" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Fire Hydrants, Fire Horses & Nozzles</label><br>
                                <select name="fire_hydrants" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
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
                                <label>Surfaces of Deck</label><br>
                                <select name="surfaces_deck" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Deck Opening & Doors</label><br>
                                <select name="deck_opening_doors" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Bulwark, Rails, & Guards</label><br>
                                <select name="bulwark_rails" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 
                                <label>Stairways & Ladders</label><br>
                                <select name="stairways_ladders" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Cooking Facilities</label><br>
                                <select name="cooking_facilities" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <!-- <label>Deck Machinery, Tackles & Lifting Gear</label><br>
                                <select name="deck_machinery" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> -->

                                <label>Life Jackets & Personal Flotation Devices comply with instruction manual and routing service and maintains have been done.</label><br>
                                <select name="life_jackets" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>No. of Lifebuoys is dependend on the length of boats and routing service and maintenance have been done</label><br>
                                <select name="lifebuoys" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>


                            </div>                           
                            <div class="col-xl-6">
                                <label>Medical facilities</label><br>
                                <select name="medical_facilities" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Dangerous Areas</label><br>
                                <select name="dangerous_areas" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <!-- <label>Life Jackets & Personal Flotation Devices</label><br>
                                <select name="life_jackets" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Lifebuoys</label><br>
                                <select name="lifebuoys" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> -->


                              
                                <label>Distress Signals</label><br>
                                <select name="distress_signals" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>      

                                <label>Deck Machinery, Tackles & Lifting Gear</label><br>
                                <select name="deck_machinery" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>


                                


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
                                <label>Source of Energy</label><br>
                                <select name="source_energy" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Radio Installation & Equipment</label><br>
                                <select name="radio_installation" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Magnetic Compass</label><br>
                                <select name="magnetic_compass" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>GPS/Satellite Navigation System</label><br>
                                <select name="gps_satellite" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Means Depth Finding</label><br>
                                <select name="depth_finding" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Nautical Instruments & Publications</label><br>
                                <select name="nautical_instrument" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Signaling System</label><br>
                                <select name="signaling_system" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Navigation Bridge Visibility</label><br>
                                <select name="navigation_bridge" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>AIS with Buoys</label><br>
                                <select name="ais_buoys" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                            </div>                           
                            <div class="col-xl-6">
                                <label>Navigation Lights</label><br>
                                <select name="navigation_lights" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Crew Accommodation</label><br>
                                <select name="crew_accommodation" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Lighting, Heating, & Ventilating</label><br>
                                <select name="lighting_heating" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Sleeping Spaces</label><br>
                                <select name="sleeping_spaces" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Eating Spaces & Cooking Facilities</label><br>
                                <select name="eating_spaces" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Sanitary Facilities</label><br>
                                <select name="sanitary_facilities" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>Water Facilities</label><br>
                                <select name="water_facilities" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>VMS Installation (Active/ Deactive)</label><br>
                                <select name="vms_install" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Activate</option>
                                    <option>Deactivate</option>
                                </select><br>

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
                                <label>Secondary Means of Starting</label><br>
                                <select name="Sec_means_starting" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Emergency Steering Arrangement</label><br>
                                <select name="Emergency_ste_arrangement" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Emergency Source of Electrical Power</label><br>
                                <select name="source_elec_power" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 
                            </div>                           
                            <div class="col-xl-6">
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <label>Does the vessel carry the essential machinery spares and tools required to carry out minor and/or emergency repairs</label><br>
                                <input type="text" name="machinery_spares" class="form-control"/><br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <label>Call Sign Number </label><br>
                                <input type="text" name="call_sign" class="form-control"/><br>
                                <!-- <label>Call Sign Number </label><br>
                                <input type="text" name="call_sign" class="form-control"/><br>
                                <label>Call Sign Number </label><br>
                                <input type="text" name="call_sign" class="form-control"/><br> -->
                                <label>AIS Model and Code No.</label><br>
                                <input type="text" name="ais_model_code" class="form-control"/><br>
                            </div>
                            <div class="col-xl-6">
                                <!-- <label>VMS Number</label><br> -->
                                <label>VMS Model and Serial No.</label><br>
                                <input type="text" name="vms_model_serial" class="form-control"/><br>

                                <label>Radio Model and Serial No.</label><br>
                                <input type="text" name="radio_model_serial" class="form-control"/><br>
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
                                <label>Hull</label><br>
                                <input type="date" name="hull" class="form-control"/><br>   
                            </div>                           
                            <div class="col-xl-4">
                                <label>Machinery</label><br>
                                <input type="date" name="machinery" class="form-control"/><br>
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
                                <label>Running Trail Carried Out On	</label><br>
                                <input type="date" name="running_trail" class="form-control"/><br>                                      

                                <label>Maneuverability</label><br>
                                <select name="maneuverability" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Satisfactory</option>
                                    <option>Unsatisfactory</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                            </div>                           
                            <div class="col-xl-6">
                                    <label>Machinery Operation</label><br>
                                    <select name="machine_operation" class="form-control">
                                        <option value=""  selected hidden>Please Choose...</option>
                                        <option>Satisfactory</option>
                                        <option>Unsatisfactory</option>
                                        <option>Not Available</option>
                                        <option>Not Applicable</option>
                                    </select><br>

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
                            <div class="col-xl-6">
                                <label>This is to certify that the above named/numbered vessel was dully inspected by me and found to be in a fit and seaworthy condition to operate as a within the limit of 
                                    N/A.
                                    <select name="condition" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Sri Lankan Waters Only</option>
                                    <option>International Waters Only</option>
                                    <option>Both Sri Lankan  and  International Waters</option>                    
                                </select><br>                                
                                </label>
                            </div>

                            <div class="col-xl-6">
                               
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <label>Date on or before which the vessel should be next inspected</label><br>
                                <input type="date" name="next_inspect" class="form-control"/><br>

                                <label>MEA Certificate Number</label><br>  
                                <!-- (Format Year + BoatNo + FI_DivisionNo + 0001)  Auto Generate -->
                                <input type="text" name="next_inspect" class="form-control"/><br>

                                <!-- <label>Number of Crew (Minimum) </label><br> -->
                                <label>Number of Professional & Competent Crew (Minimum)</label><br>
                                <input type="text" name="prof_crew" class="form-control"/><br>
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
                                <label>Name of Coxswain/ Certificate No.</label><br>
                                <input type="text" name="coxswain" class="form-control"/><br>
                                
                                <label>Name of Engine Driver/ Certificate No.</label><br>
                                <input type="text" name="name_engine_driver" class="form-control"/><br>

                                <label>Other Information</label><br>
                                <input type="text" name="other_info" class="form-control"/><br>
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
                                <label>Hull</label><br>
                                <input type="text" name="hull_val" class="form-control"/><br>
                                
                                <label>Engine</label><br>
                                <input type="text" name="engine_val" class="form-control"/><br>
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
                                <label>Compass</label><br>
                                <input type="text" name="compass" class="form-control"/><br>
                                
                                <label>Radio</label><br>
                                <input type="text" name="radio" class="form-control"/><br>

                                <label>GPS</label><br>
                                <input type="text" name="gps" class="form-control"/><br>

                                <label>RADAR</label><br>
                                <input type="text" name="radar" class="form-control"/><br>
                            </div>                           
                            <div class="col-xl-6">
                                <label>AIS</label><br>
                                <input type="text" name="ais" class="form-control"/><br>

                                <label>Winch</label><br>
                                <input type="text" name="winch" class="form-control"/><br>

                                <label>VMS</label><br>
                                <input type="text" name="vms" class="form-control"/><br>
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
                                <label>Gillnets</label><br>
                                <input type="text" name="gillnets" class="form-control"/><br>
                                
                                <label>Longlines</label><br>
                                <input type="text" name="longlines" class="form-control"/><br>

                                <label>Other</label><br>
                                <input type="text" name="other" class="form-control"/><br>

                                <label>Total</label><br>
                                <input type="text" name="total" class="form-control"/><br>
                            </div>                           
                            <div class="col-xl-6">
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="row">
                <div class="col-xl-3">
                </div>
                <div class="col-xl-3">
                </div>
                <div class="col-xl-3"><br>
                    <div class="form-group">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#RejectModel">Reject</button>
                        <!-- Modal -->
                        <div class="modal fade" id="RejectModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Remark</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <textarea rows="4" cols="55" id="Remark"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="button" id="reject" class="btn btn-danger">Reject</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3"><br>
                    <div class="form-group">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#ApproveModel">Submit</button>
                        <!-- Modal -->
                        <div class="modal fade" id="ApproveModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Remark</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <textarea rows="4" cols="55" id="Remark"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="button" id="approve" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>


       







    </div>              
</div>
