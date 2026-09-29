<?php

/** @var yii\web\View $this */

$this->title = 'Add New User Privilege';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
    
  
        </div>
      <div class="card-body">
      <label>System User: <span style="color: red;">*</span></label><br>
      <div class="input-group mb-3">
                        <input type="text" class="form-control">
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary">Search System User</button>
                        </div>
                      </div>

      <label>User Role: <span style="color: red;">*</span></label><br>
      <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Fisherman</option>
            <option>Fisheries Inspector</option>
            <option>Assistant Director</option>
            <option>Director General</option>
            <option>District Office</option>
            <option>Admin</option>
            <option>Yard Owner</option>
        </select><br> 
      
        <label>Module: <span style="color: red;">*</span></label><br>
        <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>HR Approv e Widget</option>
            <option>Boat Number Widget</option>
            <option>Fisherman widget</option>
            
        </select> <br>

        <label>Category: <span style="color: red;">*</span></label><br>
        <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Flow</option>
            <option>Widget</option>
            <option>Link</option>
            
        </select> <br>

        <label>Privileges: <span style="color: red;">*</span></label><br>
        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Select All</span>
                      </label>
        <div class="row" style="border-style: solid;">
        
        <div class="col">
        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Annual Activity Plan AD Approval</span>
                      </label>
        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Annual Activity Plan Approved Widget</span>
                      </label>
        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Assigned Annual Activity Plan</span>
                      </label>
                      <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Boat Active/DeActivate</span>
                      </label>
        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Boat Owner</span>
                      </label>
                      <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Add New Employee</span>
                      </label>
        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Add Export Country</span>
                      </label>
        </div>

        <div class="col">
        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Annual Activity Plan AD Approval</span>
                      </label>
        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Annual Activity Plan Approved Widget</span>
                      </label>
        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Assigned Annual Activity Plan</span>
                      </label>
                      <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Boat Active/DeActivate</span>
                      </label>
        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Boat Owner</span>
                      </label>
                      <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Add New Employee</span>
                      </label>
        <label class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" ><span class="custom-control-label">Add Export Country</span>
                      </label>
        </div>
        </div>


                     
     </div>
    </div>
  </div>
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="row">
        <div class="col-xl-3"></div>
        <div class="col-xl-3"></div>
        <div class="col-xl-3">
            <div class="form-group">
                <br> <a href="" class="btn btn-danger btn-block">Cancel</a>                                    
            </div>
        </div>
            <div class="col-xl-3">
                <div class="form-group">
                    <br> <a href="fm-register2" class="btn btn-success btn-block">Submit</a>
                </div>
            </div>
    </div>
  </div>
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
    
  
        </div>
      <div class="card-body">
      <table class="table">
  <thead  style="background-color:#563D7C; color:#fff;" >
    <tr>
      <th scope="col">System User</th>
      <th scope="col">Employee Name	</th>
      <th>Privilege</th>
      <th>Module</th>
      <th>Action</th>



    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">kds_wijayawardena	</th>
      <td>KETAWALAGE DON SHANAKA WIJAYAWARDENA	</td>
      <td>Boat All Search Widget	</td>
      <td>Boat</td>
      <td><a href="#">
          <span style="padding-right:10px" class="fa fa-edit"></span>
          <span style="padding-right:10px" class="fa fa-trash"></span>
        </a></td>
      </tr>
      <tr>
      <th scope="row">kds_wijayawardena	</th>
      <td>KETAWALAGE DON SHANAKA WIJAYAWARDENA	</td>
      <td>Boat All Search Widget	</td>
      <td>Boat</td>
      <td><a href="#">
          <span style="padding-right:10px" class="fa fa-edit"></span>
          <span style="padding-right:10px" class="fa fa-trash"></span>
        </a></td>
      </tr>
      <tr>
      <th scope="row">kds_wijayawardena	</th>
      <td>KETAWALAGE DON SHANAKA WIJAYAWARDENA	</td>
      <td>Boat All Search Widget	</td>
      <td>Boat</td>
      <td><a href="#">
          <span style="padding-right:10px" class="fa fa-edit"></span>
          <span style="padding-right:10px" class="fa fa-trash"></span>
        </a></td>
      </tr>
    
  </tbody>
</table>


        
                
     </div>
    </div>
  </div>

</div>

