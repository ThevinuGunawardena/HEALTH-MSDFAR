<?php

/** @var yii\web\View $this */

$this->title = 'My Gear Type List';
?>
<div class="row">

  

  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by Gear Type</label>
        <div class="input-group mb-3">                        
                    <input type="text" name="yard_no" class="form-control">
            
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary">Search</button><br>
                    </div>
                </div> 

      </div>

    </div>
  </div>
  
 
</div>



<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="col-xl-12">
      <table class="table"><br>
        <thead>
          <tr>
            <th scope="col">Gear Type Code</th>
           
            <th scope="col">View & Update</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Line_Chillaw_Type1</td>
           
            <td><a href="fi-addnewgeartype" class="btn btn-primary">View & Update</a></td>
          </tr>
          <tr>
            <td>Line_Chillaw_Type2</td>
            
            <td><a href="fi-addnewgeartype" class="btn btn-primary">View & Update</a></td>
          </tr>
          <tr>
            <td>Line_Chillaw_Type3</td>
            
            <td><a href="fi-addnewgeartype" class="btn btn-primary">View & Update</a></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

</div>