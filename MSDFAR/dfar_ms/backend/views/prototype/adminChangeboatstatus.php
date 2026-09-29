<?php

/** @var yii\web\View $this */

$this->title = 'Boat Active/ Deactivate';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="card-title">Boat Active/ Deactivate</h3>
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
    
  
        </div>
      <div class="card-body">
      <label>Boat Number:</label><br>
      <div class="input-group mb-3">
                        <input type="text" class="form-control">
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary">Search</button>
                        </div>
                      </div>  
                      <div class="row">
                      <div class="col">
                      <label>Boat Category</label><br>
                  <input type="text" name="LandSite_Code" class="form-control" readonly /><br>   
      
                        </div>
                        <div class="col">
                        <label>Owner Name</label><br>
                  <input type="text" name="LandSite_Code" class="form-control"readonly/><br>   
                        </div>
                      </div>    
     
        <label>Status:<span style="color: red;">*</span></label><br>
      <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Active</option>
            <option>Deactive</option>
        </select> <br>                
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
                    <br> <a href="fm-register2" class="btn btn-success btn-block">Update</a>
                </div>
            </div>
    </div>
  </div>
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
    
  
        </div>
      <div class="card-body">
      


        
                
     </div>
    </div>
  </div>

</div>

