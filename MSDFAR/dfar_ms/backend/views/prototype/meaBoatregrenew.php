<?php

/** @var yii\web\View $this */

$this->title = 'MEA Inspection - Boat Renewal';
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
                                <label name="yard_name">Boat Reg Number : IMULA08888NBO</label><br>
                                <label  name="hull_number">Hull number of vessel : 26456456378 </label><br>
                                <label name="gear_ratio">Gear Ratio : Test</label><br>
                            </div> 
                            <div class="col-xl-6">
                                <label name="vessel_type">Type of Vessel : Test</label><br> 
                                <label name="reg_fishing_gear">Registered Fishing Gear :Test</label><br>  
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
                                <label name="yard_no">Yard Reg. & Design Reg. No. : Test Yard</label><br>                          
                                <label name="length_weight">Light Weight & Type of Vessel : 25</label><br><br>
                           </div>

                            <div class="col-xl-6">                                
                            
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-4">
                                <label>Commenced Construction Date </label><br>
                                <input type="date" name="commence_contruct_date" class="form-control" readonly/><br>    
                            </div>

                            <div class="col-xl-4">
                                <label>Completed Construction Date</label><br>
                                <input type="date" name="completed_contruct_date" class="form-control" readonly/><br>
                            </div>

                            <div class="col-xl-4">
                                <label>Date of Build </label><br>
                                <input type="date" name="date_build" class="form-control" readonly/><br>                            
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <label name="mat_of_hull">Material of Hull as Approved : Test</label><br>
                                <label name="gross_tons">Gross Tonns (tons) : 10</label><br>                                
                                <label name="volume">Volume (m3) : 100</label><br>
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
                                <label name="overall_length">Overall Length (feet) :50</label><br>                                
                                <label name="beam">Beam (feet) :20</label><br>                              
                            </div>

                            <div class="col-xl-6">
                                <label name="depth">Depth  (feet) :20</label><br>
                                <label name="draught">Draught(feet) :10</label><br>
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
                            </div>
                            <div class="col-xl-6">
                                <label>Fuel Type</label><br>
                                <select name="fuel_type" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Diesel</option>
                                    <option>Petrol</option>
                                    <option>Kerosene</option>
                                    <option>N/A</option>
                                </select><br>       

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <label>Number of Cylinders</label><br>
                                <input type="text" name="no_cylinder" class="form-control"/><br>
                                <label>Horse Power of Engine</label><br>
                                <input type="text" name="hp" class="form-control"/><br>
                                <label name="capacity_fuel">Fuel Tank Capacity :10</label><br>
                                <label name="design_speed">Designed Speed :100	</label><br>                  
                               
                            </div>
                            <div class="col-xl-6">
                            <label>Engine Model</label><br>
                                <select name="engine_model" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Isuzu</option>
                                    <option>Honda</option>
                                    <option>Hundai</option>
                                    <option>Other</option>
                                </select><br>          
                                <label>Maker's Name & Engine Number</label><br>
                                <input type="text" name="engine_no" class="form-control"/><br>  
                                
                                
                                <label name="model_no">Maximum Range on Fuel Tank :20</label><br>
                                <label name="steering_gear_type">Steering Gear Type :Test</label><br>
                                
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
                        <h4>Propeller</h4>
                        <div class="row">
                            <div class="col-xl-6">
                                <label>Diameter</label><br>
                                <input type="text" name="diameter" class="form-control"/><br>
                                <label>Pitch</label><br>
                                <input type="text" name="pitch" class="form-control"/><br>                   
                            </div>

                            <div class="col-xl-6">
                                <label>No. of Blades</label><br>
                                <input type="text" name="blades" class="form-control"/><br> 
                                <label>Material of Approved</label><br>
                                <input type="text" name="material" class="form-control"/><br> 
                                <!-- <label>Gear Ratio</label><br>
                                <input type="text" name="gear_ratio" class="form-control"/><br>  -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <h4>Propeller</h4>
                        <div class="row">
                            <div class="col-xl-6">
                               
                                <label>Geat</label><br>
                                <input type="text" name="pitch" class="form-control"/><br>                   
                            </div>

                            <div class="col-xl-6">
                                <label>Gear Ratio</label><br>
                                <input type="text" name="gear_ratio" class="form-control"/><br> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->


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
                        <h4>Condition of</h4>
                        <div class="row">
                            <div class="col-xl-4">
                                <label>Hull</label><br>
                                <label>Internal</label><br>
                                <select name="hull" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                            </div>
                            <div class="col-xl-4">
                                <br><label>External</label><br>
                                <select name="hull" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>                            
                            </div>
                            <div class="col-xl-4">
                                <br><label>Sheathing</label><br>
                                <select name="hull" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>  
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <label>Decks</label><br>
                                <select name="deck" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Cargo Compartment</label><br>
                                <select name="cargo" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>                                
                                

                                <label>FRamework, Timbers & Internals</label><br>
                                <select name="framework_tim" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Machinery</label><br>
                                <select name="machinery" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Rudder</label><br>
                                <select name="rudder" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 
                            </div>
                            
                            <div class="col-xl-6">
                                <label>Steering Gear</label><br>
                                <select name="gear" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Anchor & Cables</label><br>
                                <select name="anchor" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Navigation Lights</label><br>
                                <select name="nav_lights" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Date of last overhaul</label><br>
                                <input type="date" name="last_overhaul" class="form-control"/><br>  
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
                                <label>Compass</label><br>
                                <select name="compass" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Life Saving Appliances</label><br>
                                <select name="life_saving" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                
                                <label>Fire Extingulshers Type</label><br>
                                <select name="fire_exting" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>Bilge Pump</label><br>
                                <select name="bilge_pump" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>VMS Installation</label><br>
                                <select name="vms_install" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Activate</option>
                                    <option>Deactivate</option>
                                </select><br>

                            </div>

                           
                            <div class="col-xl-6">
                                <label>Bailers</label><br>
                                <select name="chill_bath" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>

                                <label>First Aid Equipment</label><br>
                                <select name="first_aid" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br>
                                                                                                
                                <label>Navigation Equipment</label><br>
                                <select name="navi_equip" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                                <label>GPS available or not</label><br>
                                <select name="draught_marks	" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br> 

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <label>Does the vessel carry the essential machinery spares & tools required to carry out miner & or emergency repairs</label><br>
                                <!-- <input type="text" name="no_cylinder" class="form-control"/><br> -->
                                <select name="draught_marks	" class="form-control">
                                    <option value=""  selected hidden>Please Choose...</option>
                                    <option>Yes, Satisfactory</option>
                                    <option>Yes, Not Satisfactory</option>
                                    <option>No</option>
                                    <option>Not Available</option>
                                    <option>Not Applicable</option>
                                </select><br><br>  

                                <label>Repairs Carried Out On</label><br>
                                <label>Hull</label><br>
                                <input type="date" name="hull" class="form-control"/><br>

                                <label>Machinery</label><br>
                                <input type="date" name="machinery" class="form-control"/><br>
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
                        <h4>Fishing Gear & Equipment</h4>
                        <div class="row">
                            <div class="col-xl-6">
                                <label>Gear Marking</label><br>
                                <input type="text" name="gear_marking" class="form-control"/><br>                                             
                            </div>

                            <div class="col-xl-6">
                                <label>Dehooker & Line Cutter</label><br>
                                <input type="text" name="dehooker" class="form-control"/><br> 
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
                        <h4>Number of Passengers (Maximum)</h4>
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
