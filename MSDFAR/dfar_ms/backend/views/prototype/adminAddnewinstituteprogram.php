<?php

/** @var yii\web\View $this */

$this->title = 'Approved Institute Program';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="card-title">Approved Institute Program</h3>
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
    
  
        </div>
      <div class="card-body">
      <label>Training Institute :<span style="color: red;">*</span></label><br>
      <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>OCEAN INSTITUTE OF SRI LANKA</option>
            <option>CINEC CAMPUS</option>
        </select> <br>
      <label>Training Program :<span style="color: red;">*</span></label><br>
      <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>FISHING VESSELS SKIPPER TRAINING</option>
            <option>SKIPPER TRAINING</option>
        </select> <br>

        <label>Training Period :<span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Code" class="form-control" /><br>          
      
      <label>Description :<span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Code" class="form-control" /><br>          
     
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
      <th scope="col">Institute Name	</th>
      <th scope="col">Program Name	</th>
      <th scope="col">Training Period	</th>
      <th scope="col">Description</th>
      <th scope="col">Status</th>
      <th scope="col">Action</th>



    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">Sri Lanka Fisheries Training Institute	</th>
      <td>Skipper Licence training	</td>
      <td>6</td>
      <td>asdfghj</td>
      <td>Inactive</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">OCEAN UNIVERSITY OF SRI LANKA	</th>
      <td>FISHING VESSELS SKIPPER TRAINING	</td>
      <td>7</td>
      <td>OUSL - FISHING VESSELS SKIPPER TRAINING	</td>
      <td>Active</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">CINEC CAMPUS	</th>
      <td>SKIPPER TRAINING	</td>
      <td>5</td>
      <td>CINEC - SKIPPER TRAINING	</td>
      <td>Active</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    
  </tbody>
</table>


        
                
     </div>
    </div>
  </div>

</div>

