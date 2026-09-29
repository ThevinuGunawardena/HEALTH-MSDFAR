<?php

/** @var yii\web\View $this */

$this->title = 'Add New User Role';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="card-title">Add New User Role</h3>
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
    
  
        </div>
      <div class="card-body">
      <label>User Role Code: <span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Code" class="form-control"/><br>   
      
      <label>Description: <span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Code" class="form-control"/><br> 
      
      <label>User Role Category: <span style="color: red;">*</span></label><br>
      <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Mobile</option>
            <option>Web Access</option>
        </select> <br>  

        <label>Status:<span style="color: red;">*</span></label><br>
      <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Active</option>
            <option>In Active</option>
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
      <th scope="col">S.No	</th>
      <th scope="col">User Role Code	</th>
      <th>Description</th>
      <th>Action</th>


    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td>FM</td>
      <td>Fisherman</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>FI</td>
      <td>Fisheries Inspector</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">3</th>
      <td>AD</td>
      <td>Assistant Director</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">4</th>
      <td>DG</td>
      <td>Director General</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">5</th>
      <td>DO</td>
      <td>District Office</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">6</th>
      <td>ADM</td>
      <td>Admin</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">7</th>
      <td>YO</td>
      <td>Yard Owner</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>>
    
  </tbody>
</table>


        
                
     </div>
    </div>
  </div>

</div>

