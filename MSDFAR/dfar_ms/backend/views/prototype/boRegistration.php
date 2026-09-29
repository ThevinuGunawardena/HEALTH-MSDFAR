<?php

/** @var yii\web\View $this */

$this->title = 'Boat Registration';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="card-title">Boat Registration</h3>
    </div>
  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">

       

        <label>Boat Reg. No.</label><br>
        <div class="input-group mb-3">                       
          <input type="text" name="boat_number" value="IMULA2222MTR" class="form-control" readonly>
         
        </div> 

        <label>Boat Name</label><br>
        <input type="text" name="boat_name" class="form-control" value="IMULA2525MTRs" readonly/><br>	
                    
        <div class="table-responsive ">
          <table class="table">
            <thead><br> 
              <tr>
                <th scope="col">Fisherman ID</th>
                <th scope="col">Name</th>
                <th scope="col">NIC</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>FM000001</td>
                <td>A.B.C Perera</td>
                <td>831373547V</td>
              </tr>                            
            </tbody>
          </table>
        </div>



        <label>Fisheries District</label><br>
        <input type="text" name="fi_district" value="Colombo" class="form-control" readonly/><br>	
        <label>Fisheries Management Area</label><br>
        <input type="text" name="fi_ManArea" value="Colombo" class="form-control" readonly/><br>	
        <label>Fisheries Division</label><br>
        <input type="text" name="fi_division" value="Pitipana" class="form-control" readonly/><br>	
     </div>
    </div>
  </div>
                       
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
    </div>
  </div>
             
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Yard Details</h4>
        <label>Yard No.</label><br>
        <div class="input-group mb-3">                        
          <input type="text" name="yard_no" class="form-control" readonly>
        </div>
      
        <label>Yard Name</label><br>
        <input type="text" name="yard_name" value="SBY Boat Yard" class="form-control" readonly/><br>	
        <label>Yard Address</label><br>
        <input type="text" name="yard_address" value="125/85, Galle Road, Galle." class="form-control" readonly/><br>	
        <label>Yard Contact No.</label><br>
        <input type="text" name="yard_contact_no" value="0758458958" class="form-control" readonly/><br>	                   
      </div>
    </div>
  </div>


  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Boat Design Details</h4>                               
        <label>Boat Category</label><br>
        <input type="text" name="boat_cat_code" value="IMUL" class="form-control" readonly/><br>	

       

        <label>Boat Category Code</label><br>
        <input type="text" name="boat_cat_code" value="IMUL" class="form-control" readonly/><br>	

        <label>Boat Design ID.</label><br>
        <div class="input-group mb-3">                        
          <input type="text" name="boat_design_id" value="2548" class="form-control" readonly>
          <div class="input-group-append">
                          <button type="button" class="btn btn-primary">View Design</button>
                        </div>
        </div>
        
        <label>Boat Design Name</label><br>
        <input type="text" name="boat_design_name" value="IMUL" class="form-control" readonly/><br>

        <label>Length (Meter)</label><br>
        <input type="text" name="length" class="form-control" value="2.5m" readonly/><br>	

        <label>Height (Meter)</label><br>
        <input type="text" name="height" class="form-control" value="0.75m" readonly/><br>	

        <label>Width (Meter)</label><br>
        <input type="text" name="width" class="form-control" value="0.75m"  readonly/><br>	
      </div>
    </div>
  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
      <h4>Boat Insurance Details</h4>
        <label>Insurance Policy No.</label><br>
        <input type="text" name="insuarance" value="INS/001"  class="form-control" readonly/><br>		

        <h4>Call Sign</h4>
          <label>Call Sign No.</label><br>
          <input type="text" name="call_sign" value="CL/088631" class="form-control" readonly/><br>	              
      </div>
    </div>
  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Boat Meterial</h4>
          <label>Hull Meterial</label><br>
          <input type="text" name="hull_meterial" value="Fibre" class="form-control" readonly/><br>                                    
      </div>
    </div>
  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Particulars of the Fishing Boat</h4>
        <label>Length (Meter)</label><br>
        <input type="text" name="length" value="2m" class="form-control" readonly/><br>	

        <label>Year of Construction</label><br>
        <input type="text" name="year_construct" value="2020" class="form-control" readonly/><br>
      </div>
    </div>
  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>How the Boat is propelled</h4>
        <div class="row">
                <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-6">
                    <div class="form-group">
                        <label>Engine Category</label>
                        <input type="text" name="engine" value="Inboard Motor" class="form-control" readonly/><br>
                    </div>
                    </div>
                    <div class="col-xl-6">
                    <div class="form-group">
                        <label>Make</label>
                        <input type="text" name="make" value="2018" class="form-control" readonly/><br>
                    </div>
                    </div>
                </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-xl-6">
                        <div class="form-group">
                        <label>Horsepower</label>
                        <input type="text" name="make" value="9.9hp" class="form-control" readonly/><br>
                        </div>
                        </div>
                        <div class="col-xl-6">
                        <div class="form-group">
                            <label>Engine Serial Number</label>
                            <input type="text" name="engine_serial" value="#32964279ajks9128" class="form-control" readonly/>
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
        <h4>Navigation, Communication and Fishing Equipment</h4>
        <label>Communication Equipment</label><br>
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">   
            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" checked disabled>
                <span class="custom-control-label">SSB</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" disabled>
                <span class="custom-control-label">Test VMS</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" checked disabled>
                <span class="custom-control-label">VHF</span>
            </label>
        </div>

        <br><label>Fishing Equipment</label><br>
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" checked disabled>
                <span class="custom-control-label">Fish Finder</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" disabled>
                <span class="custom-control-label">Line Hauler</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" checked disabled>
                <span class="custom-control-label">Net Hauler</span>
            </label>        
        </div>

        <label>Navigation Equipment</label><br>
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" checked disabled>
                <span class="custom-control-label">Depth Sounder</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" disabled>
                <span class="custom-control-label">Rader</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" disabled>
                <span class="custom-control-label">Sattelite Navigation</span>
            </label>    
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Place where the boat is kept at most times</h4>
        <label>Fi-District of landing site</label><br>
        <input type="text" name="engine_serial" value="Colombo" class="form-control" readonly/><br> 
                      
        <label>landing site</label><br>
        <input type="text" name="engine_serial" value="Pitipana South" class="form-control" readonly/>

        
        
        <!-- <div class="table-responsive ">
          <table class="table">
            <thead><br> 
              <tr>
                <th scope="col">Landing Site Code</th>
                <th scope="col">Name</th>
                <th scope="col">Action</th>               
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>0001</td>
                <td>Pitipana South</td>
                <td><a href="#" class="btn btn-danger">Remove</a></td>       
              </tr>                            
            </tbody>
          </table>
        </div> -->
      </div>
    </div>    
  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Witness Details</h4>
        <div class="row">
                <div class="col-xl-12">
                <div class="row">
                    <div class="col-xl-6">
                    <div class="form-group">
                        <label>Witness Name</label>
                        <input type="text" name="witness_name" value="V.H. Kumaradasa" class="form-control" readonly/>
                    </div>
                    </div>
                    <div class="col-xl-6">
                    <div class="form-group">
                        <label>Witness Address</label>
                        <input type="text" name="witness_address" value="54/A, Pitipana, Colombo" class="form-control" readonly/>
                    </div>
                    </div>
                </div>
                </div>
            </div>

            <div class="row">
              <div class="col-xl-12">
                <div class="row">
                  <div class="col-xl-6">
                    <div class="form-group">
                      <label>Witness NIC</label>
                      <input type="text" name="witness_nic" value="19558762536223" class="form-control" readonly/>
                    </div>
                  </div>
                  <div class="col-xl-6">
                    <div class="form-group">
                      <label>Witness Signing Date</label>
                      <input type="date"  name="wit_sign_date" value="2022-10-10" class="form-control" readonly/>
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
        <h4>Payments</h4>
        <div class="col-xl-6">
      <label>Amount (LKR)</label><br>
        <input type="text" name="amount" value="Rs.500.00" class="form-control" readonly/><br>
      </div>
      </div>
    </div>
  </div>             
                      
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h4>Required Documents For Boat Registration</h4><br>
                    <div class="row">
                        <div class="col-xl-3">
                            <label>Completed Fisherman 49 Form <span style="color: red;">*</span></label><br><br>
                            <button type="button" name="yard_plan" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Valid Insurance Certificate in case of mechanical vessel <span style="color: red;">*</span></label><br><br><br>
                            <button type="button" name="physical_app" class="btn btn-primary">Upload Documents</button><br><br>

                          
                            
                        </div>

                        <div class="col-xl-3">
                            <label>A receipt of purchase of the vessel or proof of ownership <span style="color: red;">*</span></label><br>
                            <button type="button" name="yard_insuarance_policy" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Receipt of engine purchase or proof of ownership <span style="color: red;">*</span></label><br><br><br>
                            <button type="button" name="labour_insuarance_policy" class="btn btn-primary">Upload Documents</button><br><br>

                           
                        </div>
                        

                        <div class="col-xl-3">
                            <label>NIC Copy of Owner <span style="color: red;">*</span></label><br><br>
                            <button type="button" name="sanitary_plan" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>An affidavit that the vessel is not previously registered if the date of manufacture is more than 6 months. <span style="color: red;">*</span></label><br><br>
                            <button type="button" name="water_bill" class="btn btn-primary">Upload Documents</button><br><br>

                           
                        </div>

                        <div class="col-xl-3">
                            <label>Marine Engineer Certificate in case of mechanical vessel <span style="color: red;">*</span></label><br>
                            <button type="button" name="sanitary_plan" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>If the registration number belongs to another district, the certificate of the concerned Assistant Director that the vessel is not registered in that district <span style="color: red;">*</span></label><br>
                            <button type="button" name="water_bill" class="btn btn-primary">Upload Documents</button><br><br>

                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
  <div class="row">
                                        <div class="col-xl-3">
                                        </div>
                                        <div class="col-xl-3">
                                        </div>
                                        <div class="col-xl-3">
                                            <div class="form-group">
                                                <a href="fm-index" class="btn btn-danger btn-block">Cancel</a>
                                            </div>
                                        </div>
                                        <div class="col-xl-3">
                                            <div class="form-group">
                                                <a href="fm-register3" class="btn btn-primary btn-block">
                                                    Apply Now</a>
                                            </div>
                                        </div>
                                    </div>
</div></div>

