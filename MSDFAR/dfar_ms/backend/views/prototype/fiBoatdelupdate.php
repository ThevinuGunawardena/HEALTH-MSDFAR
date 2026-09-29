<?php

/** @var yii\web\View $this */

$this->title = 'Boat Profile';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
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
        <input type="text" name="boat_name" class="form-control"/><br>	
                    
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



        <label>Fisherman Name</label><br>
        <input type="text" name="fisherman_name" class="form-control"/><br>	
        <label>Fisheries District</label><br>
        <input type="text" name="fi_district" class="form-control"/><br>	
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
          <input type="text" name="yard_no" class="form-control">
        </div>
      
        <label>Yard Name</label><br>
        <input type="text" name="yard_name" class="form-control" readonly/><br>	
        <label>Yard Address</label><br>
        <input type="text" name="yard_address" class="form-control" readonly/><br>	
        <label>Yard Contact No.</label><br>
        <input type="text" name="yard_contact_no" class="form-control" readonly/><br>	                   
      </div>
    </div>
  </div>


  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Boat Design Details</h4>                               
        <label>Boat Category</label><br>
        <select class="form-control" id="input-select">
                          <option>IMUL</option>
                          <option>OFRP</option>
                          <option>MTRB</option>
                          <option>NTRB</option>

                        </select><br>
       

        <label>Boat Category Code</label><br>
        <input type="text" name="boat_cat_code" value="IMUL" class="form-control"/><br>	

        <label>Boat Design ID.</label><br>
        <div class="input-group mb-3">                        
          <input type="text" name="boat_design_id" value="2548" class="form-control">
          <div class="input-group-append">
                          <button type="button" class="btn btn-primary">Search Design</button>
                        </div>
        </div>
        
        <label>Boat Design Name</label><br>
        <input type="text" name="boat_design_name" class="form-control"/><br>

        <label>Length (Meter)</label><br>
        <input type="text" name="length" class="form-control"/><br>	

        <label>Height (Meter)</label><br>
        <input type="text" name="height" class="form-control"/><br>	

        <label>Width (Meter)</label><br>
        <input type="text" name="width" class="form-control"/><br>	
      </div>
    </div>
  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
      <h4>Boat Insurance Details</h4>
        <label>Insurance Policy No.</label><br>
        <input type="text" name="insuarance" class="form-control"/><br>		

        <h4>Call Sign</h4>
          <label>Call Sign No.</label><br>
          <input type="text" name="caii_sign" class="form-control"/><br>	              
      </div>
    </div>
  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Boat Meterial</h4>
          <label>Hull Meterial</label><br>
          <select class="form-control" id="input-select">
                          <option>Fibre</option>
                          <option>Steel</option>
                          <option>Timber</option>

                        </select><br>
                      </div>
    </div>
  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Particulars of the Fishing Boat</h4>
        <label>Length (Meter)</label><br>
        <input type="text" name="length"  class="form-control"/><br>	

        <label>Year of Construction</label><br>
        <input type="text" name="year_construct"  class="form-control"/><br>
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
                        <select class="form-control" id="input-select">
                          <option>Inboard Motor</option>
                          <option>Outboard Motor</option>

                        </select>
                    </div>
                    </div>
                    <div class="col-xl-6">
                    <div class="form-group">
                        <label>Make</label>
                        <input type="text" name="make" class="form-control"/><br>
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
                        <input type="text" name="make"class="form-control"/><br>
                        </div>
                        </div>
                        <div class="col-xl-6">
                        <div class="form-group">
                            <label>Engine Serial Number</label>
                            <input type="text" name="engine_serial"class="form-control"/>
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
                <input type="checkbox" class="custom-control-input">
                <span class="custom-control-label">SSB</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input">
                <span class="custom-control-label">Test VMS</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input">
                <span class="custom-control-label">VHF</span>
            </label>
        </div>

        <br><label>Fishing Equipment</label><br>
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input">
                <span class="custom-control-label">Fish Finder</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input">
                <span class="custom-control-label">Line Hauler</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input">
                <span class="custom-control-label">Net Hauler</span>
            </label>        
        </div>

        <label>Navigation Equipment</label><br>
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input">
                <span class="custom-control-label">Depth Sounder</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input">
                <span class="custom-control-label">Rader</span>
            </label>

            <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input">
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
        <select class="form-control" id="input-select">
                          <option>Colombo</option>
                          <option>Trincomalee</option>

                        </select>   <br>                   
        <label>landing site</label><br>
        <select class="form-control" id="input-select">
                          <option>Pitipana</option>
                          <option>Negambo</option>

                        </select>
        
        
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
                      <input type="date"  name="wit_sign_date" class="form-control"/>
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
        <h4>Required Documents For Boat Registration</h4>
        <label>Boat Registration Request Documents</label><br>
      <div class="input-group mb-3">                        
                         
      <div class="input-group-append">
        <button type="button" class="btn btn-primary">Upload Documents</button>
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
                                                    Update</a>
                                            </div>
                                        </div>
                                    </div>
</div>


</div>

