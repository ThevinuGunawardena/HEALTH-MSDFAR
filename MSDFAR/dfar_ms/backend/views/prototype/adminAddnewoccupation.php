<?php

/** @var yii\web\View $this */

$this->title = 'Add New Occupation';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="card-title">Add New Occupation</h3>
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
    
  
        </div>
      <div class="card-body">
      <label>Occupation Code <span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Dis" class="form-control"/><br>        
        <label>Description: <span style="color: red;">*</span></label><br>
        <input type="text" name="LandSite_Dis" class="form-control"/><br>    

                     

        <label>Occupation Type: <span style="color: red;">*</span></label><br>
        <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Main Occupation</option>
            <option>Assosiate Occupation</option>
        </select> <br>
        <label>Status: <span style="color: red;">*</span></label><br>
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
      <th scope="col">Occupation Code</th>
      <th scope="col">Description</th>
      <th scope="col">Occupation Type	</th>
      <th scope="col">Status</th>
      <th scope="col">Action</th>


    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">AOTH</th>
      <td>Other Associate	</td>
      <td>Associate Occupation	</td>
      <td>Active</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">fishermen</th>
      <td>fishermen</td>
      <td>Main Occupation	</td>
      <td>Active</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">LEIS</th>
      <td>Leisure</td>
      <td>Associate Occupation</td>
      <td>Active</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    
  </tbody>
</table>


        
                
     </div>
    </div>
  </div>

</div>

