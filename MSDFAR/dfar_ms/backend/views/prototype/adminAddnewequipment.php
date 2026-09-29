<?php

/** @var yii\web\View $this */

$this->title = 'Add New Equipment';
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
      <label>Equipment Code:  <span style="color: red;">*</span></label><br>
      <input type="text" class="form-control"><br>    
                        

      <label>Equipment Description: <span style="color: red;">*</span></label><br>
      <input type="text" class="form-control">
<br> 
      
        <label>Equipment Category: <span style="color: red;">*</span></label><br>
        <select name="Status" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Comunication Equipment</option>
            <option>Fishing Equipment</option>
            <option>Navigation Equipment</option>

            
        </select> <br>
        <label>Status: <span style="color: red;">*</span></label><br>
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
      <th scope="col">Equipment Code	</th>
      <th scope="col">Equipment Description		</th>
      <th>Equipment Category	</th>
      <th>Status</th>
      <th>Action</th>



    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">DP-SOUNDER		</th>
      <td>DEPTH SOUNDER	</td>
      <td>NAVIGATION EQUIPMENTS	</td>
      <td>Approved  </td>
      <td><a href="#">
          <span style="padding-right:10px" class="fa fa-edit"></span>
        </a></td>
      </tr>
      <tr>
      <th scope="row">FH-FINDER		</th>
      <td>FISH FINDER		</td>
      <td>FISHING EQUIPMENT	</td>
      <td>Approved</td>
      <td><a href="#">
          <span style="padding-right:10px" class="fa fa-edit"></span>
        </a></td>
      </tr>
      <tr>
      <th scope="row">FH-FINDER		</th>
      <td>FISH FINDER		</td>
      <td>FISHING EQUIPMENT	</td>
      <td>Approved</td>
      <td><a href="#">
          <span style="padding-right:10px" class="fa fa-edit"></span>
        </a></td>
      </tr>
    
  </tbody>
</table>


        
                
     </div>
    </div>
  </div>

</div>

