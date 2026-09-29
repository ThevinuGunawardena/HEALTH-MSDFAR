<?php

/** @var yii\web\View $this */

$this->title = 'Add New Yard Safety Type';
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
      <label>Yard Safety Type Code:  <span style="color: red;">*</span></label><br>
      <input type="text" class="form-control"><br>    

      <label>Description:  <span style="color: red;">*</span></label><br>
      <input type="text" class="form-control"><br>    
      
        
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
      <th scope="col">S.No		</th>
      <th scope="col">Yard Safety Type Code</th>
      <th>Yard Safety Type Description	</th>
      <th>Action</th>



    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1	</th>
      <td>YS001</td>
      <td>Form-Chemical	</td>
      <td><a href="#">
          <span style="padding-right:10px" class="fa fa-edit"></span>
        </a></td>
      </tr>
      <tr>
      <th scope="row">2</th>
      <td>YS002</td>
      <td>Foam-Gas Pres	</td>
      <td><a href="#">
          <span style="padding-right:10px" class="fa fa-edit"></span>
        </a></td>
      </tr>
      <tr>
      <th scope="row">3</th>
      <td>YS003		</td>
      <td>Mechanical Foam	</td>
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

