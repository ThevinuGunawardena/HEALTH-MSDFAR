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
                                <label  name="engine_type">Engine Type : Inboard</label><br>                                                                  
                            </div>
                            <div class="col-xl-6">
                                <label name="fuel_type">Fuel Type : Diesel</label><br> 
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <label name="no_cylinder">Number of Cylinders : 20</label><br>                              
                                <label name="hp">Horse Power of Engine : 60</label><br>
                                <label name="capacity_fuel">Fuel Tank Capacity :10</label><br>
                                <label name="design_speed">Designed Speed :100	</label><br>                  
                               
                            </div>
                            <div class="col-xl-6">
                                <label name="engine_model">Engine Model : Honda</label><br>                              
                                <label name="engine_no">Engine Number : 66457326</label><br>
                                <label name="model_no">Maximum Range on Fuel Tank :20</label><br>
                                <label name="steering_gear_type">Steering Gear Type :Test</label><br>                                
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
                                <label name="place_inpection">Place of Inspection : Test</label><br>
                            </div>

                            <div class="col-xl-4">
                                <label name="inspection_date2">Inspection Date : 2023-07-24</label><br>
                            </div>

                            <div class="col-xl-4">
                                <label name="fuel_type">Was vessel at the time of inspection? Afloat</label><br>                            
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
                                <label name="hull">Internal : Yes, Satisfactory</label><br>                                
                            </div>
                            <div class="col-xl-4">
                                <br><label name="hull_external">External : Yes, Satisfactory</label><br>                                   
                            </div>
                            <div class="col-xl-4">
                                <br><label name="hull_sheathing">Sheathing : Yes, Satisfactory</label><br>                               
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                             <br>   <label name="deck">Decks : Yes, Satisfactory</label><br>
                                <label name="cargo">Cargo Compartment : Yes, Satisfactory</label><br>
                                <label name="framework_tim">FRamework, Timbers & Internals : Yes, Satisfactory</label><br>                               
                                <label name="machinery">Machinery : Yes, Satisfactory</label><br>                            
                                <label name="rudder">Rudder : Yes, Satisfactory</label><br>
                            </div>
                            
                            <div class="col-xl-6">
                            <br>    <label name="gear">Steering Gear : Yes, Satisfactory</label><br>                                
                                <label name="anchor">Anchor & Cables : Yes, Satisfactory</label><br>                                
                                <label name="nav_lights">Navigation Lights</label><br>
                                <label name="last_overhaul">Date of last overhaul : 2023-07-20</label><br>
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
                                <label name="compass">Compass : Yes, Satisfactory</label><br>                           
                                <label name="life_saving">Life Saving Appliances : Yes, Satisfactory</label><br>
                                <label name="fire_exting">Fire Extingulshers Type : Yes, Satisfactory</label><br>
                                <label name="bilge_pump">Bilge Pump : Yes, Satisfactory</label><br>
                            </div>

                           
                            <div class="col-xl-6">
                                <label name="chill_bath">Bailers : Yes, Satisfactory</label><br>
                                <label name="first_aid">First Aid Equipment : Yes, Satisfactory</label><br>                                                                                                
                                <label name="navi_equip">Navigation Equipment : Yes, Satisfactory</label><br>
                                <label name="gps">GPS available or not : Yes, Satisfactory</label><br>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <label name="machinery">Does the vessel carry the essential machinery spares & tools required to carry out miner & or emergency repairs : Yes, Not Satisfactory</label><br>
                                <label>Repairs Carried Out On</label><br>
                                <label name="hull_date">Hull : 2023-07-24</label><br> 
                                <label name="machinery_date">Machinery : 2023-07-24</label><br>                              
                            </div>                            
                            <div class="col-xl-6">
                             
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
                                <label name="running_trail">Running Trail Carried Out On : 2023-07-24</label><br>
                                <label name="maneuverability">Maneuverability : Satisfactory</label><br>
                            </div>                           
                            <div class="col-xl-6">
                                    <label name="machine_operation">Machinery Operation : Satisfactory</label><br>
                            </div>
                            
                            
                        </div>
                        <div class="row">
                            <div class="col-xl-6">
                                <label>This is to certify that the above named/numbered vessel was dully inspected by me and found to be in a fit and seaworthy condition to operate as a within the limit of 
                                    N/A. : Sri Lankan Waters Only</label><br> 
                            </div>
                            <div class="col-xl-6">
                               
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <label name="next_inspect">Date on or before which the vessel should be next inspected : 2023-10-30</label><br>
                                <label name="next_inspect">MEA Certificate Number : 20238888NBO01250001</label><br>  
                                <!-- (Format Year + BoatNo + FI_DivisionNo + 0001)  Auto Generate -->
                                <label name="prof_crew">Number of Professional & Competent Crew (Minimum) : 10</label><br>
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
                                <label name="coxswain">Name of Coxswain/ Certificate No. : 1234</label><br>
                                <label name="name_engine_driver">Name of Engine Driver/ Certificate No. : 8765</label><br>
                                <label name="other_info">Other Information : Test</label><br>
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
                            <div class="col-xl-2">
                                <label>Hull</label><br>
                                <label>Engine</label><br>
                            </div>  
                            <div class="col-xl-4">
                                <label name="hull_val">: 1000</label><br>
                                <label name="engine_val">: 1000</label><br>
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
                            <div class="col-xl-2">
                                <label name="compass">Compass</label><br>
                                <label name="radio">Radio</label><br>
                                <label name="gps">GPS</label><br>
                                <label name="radar">RADAR</label><br>
                            </div>     
                            
                            <div class="col-xl-4">
                                <label name="compass">: 8000</label><br>
                                <label name="radio">: 25000</label><br>
                                <label name="gps">: 50000</label><br>
                                <label name="radar">: 85000</label><br>
                            </div> 


                            
                            <div class="col-xl-2">
                                <label name="ais">AIS</label><br>
                                <label name="winch">Winch</label><br>
                                <label name="vms">VMS</label><br>
                            </div>
                            <div class="col-xl-4">
                                <label name="ais">: 45000</label><br>
                                <label name="winch">: 80000</label><br>
                                <label name="vms">: 40000</label><br>
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
                            <div class="col-xl-2">
                                <label>Gillnets</label><br>
                                <label>Longlines</label><br>
                                <label>Other</label><br>
                                <label>Total</label><br>
                            </div>    
                            <div class="col-xl-4">
                            <label name="gillnets">: 15000</label><br>
                                <label name="longlines">: 12500</label><br>
                                <label name="other">: 6000</label><br>
                                <label name="total">: 250000</label><br>
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
