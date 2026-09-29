<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
        <div class="card-body">
            <h3>User wise report</h3>
            <div class="row"> 
                <div class="col-xl-4">
                      <div class="card-body">  
                        <div class="form-group">
                          <label for="date_from">Date From</label>
                          <input type="date" class="form-control">
                        </div>
                      </div>
                </div>

                <div class="col-xl-4">
                  <div class="card-body">
                    <div class="form-group">
                    <label for="date_to">Date To</label>
                      <input type="date" class="form-control">
                    </div>
                  </div>
                </div>

                <div class="col-xl-4">
                  <div class="card-body">
                    <div class="form-group">
                      <label for="Fi_district">FI District</label>
                      <select name="fi_district" class="form-control">
                        <option value=""  selected hidden>Please Choose...</option>
                        <option>Negombo</option>
                        <option>Trincomalee</option>
                      </select><br>
                      <a href="sdo-scientificreportuser" class="btn btn-primary input-block-level form-control">Search</a>
                    </div>
                  </div>
                </div>
            </div>                
        </div>	
      </div>
</div>


<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
  <div class="card mb-5 shadow-sm">
    <div class="card-body">
      <h4>Download CSV File</h4>
      <div class="row"> 

      <div class="col-xl-4">
          <div class="card-body">  
            <div class="form-group">
              <label for="date_from">Date From</label>
              <input type="date" class="form-control">
            </div>
          </div>
        </div>
        <div class="col-xl-4">
          <div class="card-body">  
            <div class="form-group">
            <label for="date_to">Date To</label>
              <input type="date" class="form-control"><br>
              <a href="#" class="btn btn-primary input-block-level form-control">Download CSV File</a>
            </div>
          </div>
        </div>


        <div class="col-xl-4">
          <div class="card-body">  
            <div class="form-group">
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>