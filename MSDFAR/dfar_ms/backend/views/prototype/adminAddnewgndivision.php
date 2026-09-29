<?php

/** @var yii\web\View $this */

$this->title = 'Add New GN Division';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="card-title">Add New GN Division</h3>
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
    
  
        </div>
      <div class="card-body">
      <label>GN Division Code: <span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Code" class="form-control"/><br>    
      <label>GN Division Description: <span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Code" class="form-control"/><br>    
      <label>District:<span style="color: red;">*</span></label><br>
      <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Ampara</option>
            <option>Anuradapura</option>
        </select> <br>      
        <label>FI District: <span style="color: red;">*</span></label><br>
        <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Batticaloa</option>
            <option>Colombo</option>
        </select> <br>
        <label>Secretary Division: <span style="color: red;">*</span></label><br>
        <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Batticaloa</option>
            <option>Colombo</option>
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
      <th scope="col">GN Code		</th>
      <th scope="col">Description	</th>
      <th scope="col">District</th>
      <th scope="col">FI District</th>
      <th scope="col">Secretary Division</th>
      <th>Status</th>
      <th>Action</th>


    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">ATGND</th>
      <td>Ampara Test GND	</td>
      <td>Ampara</td>
      <td>Test District	</td>
      <td></td>
      <td>Active</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">ATGND</th>
      <td>Ampara Test GND	</td>
      <td>Ampara</td>
      <td>Test District	</td>
      <td></td>
      <td>Active</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">ATGND</th>
      <td>Ampara Test GND	</td>
      <td>Ampara</td>
      <td>Test District	</td>
      <td></td>
      <td>Active</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    
  </tbody>
</table>


        
                
     </div>
    </div>
  </div>

</div>

