<?php

/** @var yii\web\View $this */

$this->title = 'Widget Privilege';
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
        </select> <br> 

      <label>Widget Code: <span style="color: red;">*</span></label><br>
      <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>HR Approve Widget</option>
            <option>Boat Number Widget</option>
            <option>Fisherman widget</option>
            
        </select> <br> 
      
      <label>Title: <span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Code" class="form-control"/><br>  

                     
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
      <th scope="col">UserRole	</th>
      <th scope="col">Widget</th>
      <th>Action</th>


    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">DDO</th>
      <td>AAG</td>
      <td><a href="#">
          <span style="padding-right:10px" class="fa fa-edit"></span>
          <span style="padding-right:10px" class="fa fa-trash"></span>
        </a></td>    </tr>
    <tr>
      <th scope="row">DAD</th>
      <td>ADPRQTW</td>
      <td><a href="#">
          <span style="padding-right:10px" class="fa fa-edit"></span>
          <span style="padding-right:10px" class="fa fa-trash"></span>
        </a></td>    </tr>
    <tr>
      <th scope="row">DDEAD</th>
      <td>ADPRQTW</td>
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

