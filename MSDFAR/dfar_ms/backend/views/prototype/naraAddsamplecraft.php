<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
?>

<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
           <h3 class="card-title">Boat & Gear Data - IMULA8888NBO</h3>
        </div>
    </div>    

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <label name="fishery_type">Fishery Type : High Seas</label><br>
                <label name="boat_type2">Boat Type : IMUL</label><br>
                
                <label name="sub_cat2">Sub Category : IMULA_1</label><br>
                <label name="hp">Engine HP : 50</label><br>
                
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <h4>Departure</h4>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <label name="dep_date">Date 2023-04-24</label><br>	
                                        </div>

                                        <div class="col-xl-6">
                                            <label name="time">Time 14:20</label><br>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <label name="fi_district">Fisheries District : Negombo</label><br>
                            <label name="fi_division">FI Division : Pitipana</label><br>
                            <label name="landing_place">Departure Place/ Port : Palagathuraya</label><br>
                            <div class="row">
                            <label>Weather Occurred</label><br>
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-4">                                
                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="clear" onclick="return false;" class="custom-control-input"><span class="custom-control-label">Clear</span>
                                        </label><br>

                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="windy" onclick="return false;" class="custom-control-input"><span class="custom-control-label">Windy</span>
                                        </label>
                                    </div>

                                    <div class="col-xl-4">
                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="rainy" onclick="return false;" class="custom-control-input"><span class="custom-control-label">Rainy</span>
                                        </label><br>

                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="thunder" onclick="return false;" class="custom-control-input"><span class="custom-control-label">Thunder</span>
                                        </label>
                                    </div>

                                    <div class="col-xl-4">
                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="other" onclick="return false;" class="custom-control-input"><span class="custom-control-label">Other</span>
                                        </label>                               
                                    </div>                          
                                </div>
                                <div class="row">
                                    <div class="col-xl-6"> 
                                        <br><label name="arrival_date">Arrival Date : 2023-04-28</label><br>
                                        <label name="remarks">Remarks : Test</label><br>
                                    </div>      
                                    <div class="col-xl-6"> 
                                    </div>                                         
                                </div>
                            </div>
                        </div>
                    </div>	
                </div>
                </div>
                <label name="no_crew">Number of Crew Members : 8</label><br>               
              
                <label>Unloading Type</label><br>
                <label class="custom-control custom-radio custom-control-inline">
                    <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">All</span>
                </label>
                                           
                <label class="custom-control custom-radio custom-control-inline">
                    <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">Partial</span>
                </label><br>
                                          
                <br><label name="gear_set_time">Gear Setting Time : Day</label><br>                
                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-2">
                                <label name="days">Days : 4</label><br>
                            </div>
                            <div class="col-xl-2">
                                <label name="hours">Hours : 8</label><br>
                            </div>
                            <div class="col-xl-8">
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
        <h4>Fishing Gears</h4>
        <label name="main_gear"><b>Main Fishing Gear : Test</b></label><br>        
        <label name="target_species_main">Main Target Species : Yellow Fin Tuna - Whole H & G/ G & G</label><br>
        <label name="no_trip_main">How Many Operations Per Trip : 5</label><br>
        <div class="row">
            <div class="col-xl-12">
                <label>True Fishing Time</label>
                <div class="row">                            
                    <div class="col-xl-2">
                        <label name="days_main">Days : 50</label>
                    </div>

                    <div class="col-xl-2">
                        <label name="hours_main">Hours : 25</label><br>
                    </div>
                    <div class="col-xl-8">
                    </div>
                </div>
            </div>
        </div>      

        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-3">
                    <label name="fishing_depth_main">Fishing Depth (Fathoms) : Test</label><br>
                    </div>
                    <div class="col-xl-3">                      
                        <label name="NS_val_main">7.8731° N, 80.7718° E</label><br>
                        <!-- <select name="NS_main" class="form-control">
                            <option>N</option>
                            <option>S</option>
                        </select> 
                        <input type="text" name="NS_val_main" class="form-control"/><br>-->
                    </div>
                    <div class="col-xl-3">
                        <!-- <label>E</label><br>
                        <input type="text" name="e_main" class="form-control"/><br> -->
                        <label>G Code : Test</label><br>
                    </div>
                    <div class="col-xl-3">
                        <!-- <label>G Code</label><br>
                        <input type="text" name="g_code_main" class="form-control"/><br>                          -->
                    </div>
                    <div class="col-xl-3">
                    
                    </div>                   
                </div>
            </div>
        </div>

        <br><label name="secondary_gear"><b>Secondary Fishing Gear : Test1</b></label><br>
        <label name="target_species_second">Main Target Species : Yellow Fin Tuna - Loins</label><br>
        <label name="no_trip_second">How Many Operations Per Trip : 20</label><br>     

        <div class="row">
            <div class="col-xl-12">
                <label>True Fishing Time</label>
                <div class="row">                            
                    <div class="col-xl-2">
                        <label name="days_second">Days : 4</label>
                    </div>

                    <div class="col-xl-2">
                        <label name="hours_second">Hours : 10</label><br>
                    </div>
                    <div class="col-xl-8">
                    </div>
                </div>
            </div>
        </div>      

        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-3">
                    <label name="fishing_depth_second">Fishing Depth (Fathoms) : Test</label><br>
                    </div>
                    <div class="col-xl-3">                      
                        <label name="NS_second">7.8731° N, 80.7718° E</label><br>                        
                    </div>
                    <div class="col-xl-3">
                        <label>G Code : Test</label><br>                                    
                    </div>
                    <div class="col-xl-3">
                                              
                    </div>
                    <div class="col-xl-3">
                    
                    </div>                    
                </div>
            </div>
        </div>


        <br><label name="third_gear"><b>Third Fishing Gear : Test1</b></label><br>        
        <label name="target_species_third">Main Target Species : Big Eye Tuna - Loins</label><br>
        <label name="no_trip_third">How Many Operations Per Trip : 5</label><br>
        <div class="row">
            <div class="col-xl-12">
                <label>True Fishing Time</label>
                <div class="row">                            
                    <div class="col-xl-2">
                        <label name="days_third">Days : 10</label>
                    </div>
                    <div class="col-xl-2">
                    <label name="hours_third">Hours : 10</label><br>
                    </div>
                    <div class="col-xl-8">
                        
                    </div>
                </div>
            </div>
        </div>      

        <div class="row">
            <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-3">
                    <label name="fishing_depth_third">Fishing Depth (Fathoms) : Test</label><br>
                    </div>
                    <div class="col-xl-3">                      
                        <label name="NS_val_third">7.8731° N, 80.7718° E</label><br>                        
                    </div>
                    <div class="col-xl-3">
                        <label  name="g_code_third">G Code : Test</label><br>                                       
                    </div>
                  
                    <div class="col-xl-3">
                    
                    </div>                    
                </div>
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
                    <br> <a href="" class="btn btn-primary btn-block">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

