<?php

/** @var yii\web\View $this */

$this->title = 'Boat Ownership Transfer Request';
?>

<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Boat Ownership Transfer Request</h3>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <label>Boat Reg. No.</label><br>
                <div class="input-group mb-3">                       
                    <input type="text" name="boat_number" class="form-control">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary">Search</button>
                    </div>
                </div> 

                <label>Boat Name</label><br>
                <input type="text" name="boat_name" class="form-control" readonly/><br>	
                <label>Current Owners Of The Boat</label><br>
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
            </div>	
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>New Owners Of The Boat</h4>
        <div class="input-group-append">
          <button type="button" name="yard_owner" class="btn btn-primary">Search</button>                  
        </div>
        
        <div class="table-responsive ">
          <table class="table">
            <thead><br> 
              <tr>
                <th scope="col">Fisherman ID</th>
                <th scope="col">Name</th>
                <th scope="col">NIC</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>FM000001</td>
                <td>A.B.C Perera</td>
                <td>831373547V</td>
                <td><a href="#" class="btn btn-danger">Remove</a></td>
              </tr>                            
            </tbody>
          </table>
        </div>                           
      </div>
    </div>
  </div>

  


                       
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
    </div>
  </div>
             
  <!-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Yard Details</h4>
        <label>Yard No.</label><br>                  
        <input type="text" name="yard_no" class="form-control" readonly/><br>	    
        <label>Yard Name</label><br>
        <input type="text" name="yard_name" class="form-control" readonly/><br>	
        <label>Yard Address</label><br>
        <input type="text" name="yard_address" class="form-control" readonly/><br>	
        <label>Yard Contact No.</label><br>
        <input type="text" name="yard_contact_no" class="form-control" readonly/><br>	                   
      </div>
    </div>
  </div> -->


  <!-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Boat Design Details</h4>
        <label>Boat Category Code</label><br>
        <input type="text" name="boat_cat_code" class="form-control" readonly/><br>	
        <label>Boat Category</label><br>
        <input type="text" name="boat_cat_name" class="form-control" readonly/><br>
        <label>Boat Design ID.</label><br>                     
        <input type="text" name="boat_design_id" class="form-control" readonly/><br>
        <label>Boat Design Name</label><br>
        <input type="text" name="boat_design_name" class="form-control" readonly/><br>
        <label>Length (Meter)</label><br>
        <input type="text" name="length" class="form-control" readonly/><br>
        <label>Height (Meter)</label><br>
        <input type="text" name="height" class="form-control" readonly/><br>	
        <label>Width (Meter)</label><br>
        <input type="text" name="width" class="form-control" readonly/><br>
        <label>Meterial</label><br>
        <input type="text" name="meterial" class="form-control" readonly/><br>		
      </div>
    </div>
  </div> -->

  <!-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
      <h4>Boat Insurance Details</h4>
        <label>Insurance Policy No.</label><br>
        <input type="text" name="insuarance" class="form-control" readonly/><br>		

        <h4>Call Sign</h4>
          <label>Call Sign No.</label><br>
          <input type="text" name="caii_sign" class="form-control" readonly/><br>	              
      </div>
    </div>
  </div> -->

  <!-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
      <h4>Fisheries District</h4>
        <label>Fisheries District</label><br>
        <input type="text" name="fi_district" class="form-control" readonly/><br>		

        <h4>Boat Meterial</h4>
          <label>Hull Meterial</label><br>
          <input type="text" name="hull_mat" class="form-control" readonly/><br>	              
      </div>
    </div>
  </div> -->

  <!-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Particulars of the Fishing Boat</h4>
        <label>Length (Meter)</label><br>
        <input type="text" name="length" class="form-control" readonly/><br>	

        <label>Year of Construction</label><br>
        <input type="text" name="year_construct" class="form-control" readonly/><br>
      </div>
    </div>
  </div> -->
<!-- 
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
  </div> -->


  <!-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
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
  </div> -->

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Place where the boat is kept at most times</h4>
        <label>Fi-District of landing site</label><br>
        <select name="fi_disttrict_landing" class="form-control">
          <option value=""  selected hidden>Please Choose...</option>
          <option>Negombo</option>
          <option>Trincomalee</option>
          <option>Galle</option>
        </select> <br> 
                      
        <label>Select landing site</label><br>
        <select name="fi_disttrict_landing" class="form-control">
          <option value=""  selected hidden>Please Choose...</option>
          <option>Negombo</option>
          <option>Trincomalee</option>
          <option>Galle</option>
        </select> <br>
        <button type="button" name="landingsite" class="btn btn-primary">Add Landing Site</button>  
        
        
        <div class="table-responsive ">
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
        </div>
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
                        <input type="text" name="witness_name" class="form-control"/>
                    </div>
                    </div>
                    <div class="col-xl-6">
                    <div class="form-group">
                        <label>Witness Address</label>
                        <input type="text" name="witness_address" class="form-control"/>
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
                      <input type="text" name="witness_nic" class="form-control"/>
                    </div>
                  </div>
                  <div class="col-xl-6">
                    <div class="form-group">
                      <label>Witness Signing Date</label>
                      <input type="date"  name="wit_sign_date" class="form-control">
                    </div>
                  </div>
                  <div class="card-body">
        <h4>Documents About Witness</h4>
        <div class="input-group mb-3">                        
                         
      <div class="input-group-append">
        <input type="file" class="btn btn-primary"/>
      </div>
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
        <label>Payment Category</label><br>
        <select name="payment_cat" class="form-control">
          <option value=""  selected hidden>Please Choose...</option>
          <option>Select Payment Category</option>                       
        </select> <br>                  

        <label>Amount (LKR)</label><br>
        <input type="text" name="amount" class="form-control"/><br>

        <label>Remarks</label><br>
        <input type="text" name="remarks" class="form-control"/><br>

        <label>Payment Reference Number</label><br>
        <input type="text"  name="pay_ref_no" class="form-control"><br>

      </div>
    </div>
  </div>             
                      
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Required Documents For Boat Transfer Registration</h4>
        <div class="input-group mb-3">                        
                         
      <div class="input-group-append">
        <button type="button" class="btn btn-primary">Upload Documents</button>
      </div>
    </div>     
  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
  <div class="card mb-5 shadow-sm">
    <div class="card-body">
      <button type="button" class="btn btn-warning">Cancel</button>
      <button type="button" class="btn btn-primary">Submit</button>
    </div>
  </div>
</div>


</div>

