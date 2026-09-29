<?php

/** @var yii\web\View $this */

$this->title = 'Update License Prices';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="card-title">Update License Prices</h3>
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
    <div class="row">
        <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Select License Main Category</label>
        <select class="form-control" id="input-select">
              <option>Choose License Main Catogory</option>
              <option>Export License </option>
              <option>Import License </option>
              <option>Skipper License</option>

        </select>
        

      </div>

    </div>
  </div>
  <div class="col-xl-6">
    <div class="card-body">
    <div class="form-group">
        <label for="Fi_district">Select License Main Category</label>
        <select class="form-control" id="input-select">
              <option>Choose License Sub Catogory</option>
              <option>Indunisia </option>
              <option>Ukrain </option>

        </select>
        

      </div>

    </div>

  </div>
        </div>

        <div class="row">
        <div class="col-xl-3"></div>
        <div class="col-xl-3"></div>
        <div class="col-xl-2"></div>
            <div class="col-xl-3">
                <div class="form-group">
                    <br> <a href="fm-register2" class="btn btn-primary btn-block">Search</a>
                </div>
            </div>
    </div>
      <div class="card-body">
      <label>Current Price</label><br>
        <input type="text" name="username" class="form-control" value="Rs.500.00" readonly/><br>
        <label>Enter Updated Price</label><br>
        <input type="text" name="username" class="form-control"/><br>
        
       
                   
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
                    <br> <a href="fm-register2" class="btn btn-success btn-block">Update</a>
                </div>
            </div>
    </div>
  </div>

</div>

