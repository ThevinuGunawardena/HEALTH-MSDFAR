<?php

/** @var yii\web\View $this */

$this->title = 'Add New Fish Species';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="card-title">Add New Fish Species</h3>
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
    
  
        </div>
      <div class="card-body">
      <label>Fish Code:<span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Code" class="form-control" /><br>     
      <label>Fish Name:<span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Code" class="form-control" /><br>   
      <label>HS Code:<span style="color: red;">*</span></label><br>
      <input type="text" name="LandSite_Code" class="form-control" /><br>          

      <br>
      <label>Stattus :<span style="color: red;">*</span></label><br>
      <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Approved</option>
            <option>Rejected</option>
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
      <th scope="col">Fish Code	</th>
      <th scope="col">Fish Name	</th>
      <th scope="col">Hs Code	</th>
      <th scope="col">Status</th>
      <th scope="col">Action</th>



    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td>WAH</td>
      <td>Wahoo</td>
      <td>301</td>
      <td>Approved</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>TUM</td>
      <td>Yellow Tail Scad	</td>
      <td>301</td>
      <td>Approved</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    <tr>
      <th scope="row">3</th>
      <td>FRI</td>
      <td>Frigate Tuna	</td>
      <td>0307 51	</td>
      <td>Approved</td>
      <td><i class="fa fa-edit"></i></td>
    </tr>
    
  </tbody>
</table>


        
                
     </div>
    </div>
  </div>

</div>

