<?php

/** @var yii\web\View $this */

$this->title = 'Add New User';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="card-title">Add New User</h3>
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
        <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by Employee number</label>
        <div class="input-group mb-3">                       
          <input type="text" name="SerEmp_Num" class="form-control">
          <div class="input-group-append">
            <button type="button" class="btn btn-primary">Search</button>
          </div>
        </div> 
        

      </div>

    </div>
  </div>
  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by Employee Name</label>
        <div class="input-group mb-3">                       
          <input type="text" name="SerEmp_Name" class="form-control">
          <div class="input-group-append">
            <button type="button" class="btn btn-primary">Search</button>
          </div>
        </div> 
      </div>

    </div>

  </div>
        </div>
      <div class="card-body">
      <label>User Name</label><br>
        <input type="text" name="username" class="form-control"/><br>
        <label>User Role</label><br>
        <select name="user_roll" class="form-control">
          <option value=""  selected hidden>Please Choose...</option>
          <option>Fisheries Inspector</option>
          <option>Assistant Director</option>
          <option>Director General</option>
          <option>District Officer</option>
          <option>Admin</option>
        </select> <br>
        
       
        <label>Status</label><br>
        <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Active</option>
            <option>De-Active</option>
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
                    <br> <a href="fm-register2" class="btn btn-success btn-block">Create</a>
                </div>
            </div>
    </div>
  </div>

</div>

