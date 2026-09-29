<?php

/** @var yii\web\View $this */

$this->title = 'Add New Payment Category';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="card-title">Add New Payment Category</h3>
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
    
  
        </div>
      <div class="card-body">
      <label>Description:<span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Code" class="form-control"/><br>    
        <label>Amount: <span style="color: red;">*</span></label><br>
        <input type="text" name="LandSite_Code" class="form-control"/><br>    
        <label>Price Category::<span style="color: red;">*</span></label><br>
      <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Payment For Highseas License</option>
            <option>Payment For National License</option>
            <option>Payment For Boat Registration Process</option>
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
      <th scope="col">Description</th>
      <th scope="col">Amount</th>
      <th scope="col">Price Category</th>
      <th scope="col">Status</th>
      <th scope="col">Action</th>


    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">Payment Amount For IMUL Boat Registration	</th>
      <td>0</td>
      <td>Payment for Boat Registration Process	</td>
      <td>Active</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">Payment Amount For NBSB Registration	</th>
      <td>0</td>
      <td></td>
      <td>Active</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">Payment Amount For IMUL Boat Renewal	</th>
      <td>0</td>
      <td>Payment For Boat Renewal	</td>
      <td>Active</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    
  </tbody>
</table>


        
                
     </div>
    </div>
  </div>

</div>

