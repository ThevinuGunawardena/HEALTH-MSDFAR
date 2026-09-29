<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
?>
  <div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
      <div class="section-block" id="cards">
      </div>

      <div class="col-xl-6">
               
        
        <div class="col-xl-6">
        </div>
      </div>
      </div>  
  </div>

  <div class="row">
      <div class="col-xl-4">
        <div class="card-body">
          <div class="form-group">
            <label for="Fi_district">Search by Date</label>
            <input type="date" class="form-control">
          </div>
        </div>
      </div>

      <div class="col-xl-8">
        <div class="card-body">
          <div class="form-group">
           
          </div>
        </div>
      </div>



  </div>

  <div class="row">
      <div class="col-xl-4">
        <div class="card-body">
          <div class="form-group">
            <label for="Fi_district">Search by Gear Type</label>
            <input type="text" class="form-control">
          </div>
        </div>
      </div>

    <div class="col-xl-4">
      <div class="card-body">
        <div class="form-group">
          <label for="Fi_district">Search by Length/ Weight</label>
          <input type="text" class="form-control">
        </div>
      </div>
    </div>


    <div class="col-xl-4">
      <div class="card-body">
        <div class="form-group">
          <label for="Fi_district">Search by Economic Data</label>
          <input type="text" class="form-control">
        </div>
      </div>
    </div>


    <div class="col-xl-6">
      <div class="card-body">
        <div class="form-group">
          <a href="#" class="btn btn-warning">Search</a>
        </div>
      </div>
    </div>
  </div>

<div class="row">
  <div class="col-xl-3">
  </div>
  <div class="col-xl-3">
  </div>
  <div class="col-xl-3">
  </div><br>
  <div class="col-xl-3">
    <label>Filter By</label>

    <select class="form-control">
      <option>Approved</option>
      <option>Rejected</option>
      <option>In Progress</option>
    </select>
  </div>
</div>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="col-xl-12">
      <table class="table"><br>
        <thead>
          <tr>
            <th scope="col">Document Reference Number </th>          
            <th scope="col">Yard Name</th>
            <th scope="col">Yard Address</th>
            <th scope="col">Contact Number</th>
            <th scope="col">Status</th>
            <th scope="col">View More</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td scope="row">REQ/BYR#200609044413613</th> 
            <td>Samsung yard</td>
            <td>rathgama</td>
            <td>7152334433</td>
            <td>Approved</td>
            <td><a href="nara-scientific" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">REQ/BYR#200609044413613</th>
            <td>Samsung yard</td>
            <td>rathgama</td>
            <td>7152334433</td>
            <td>Approved</td>
            <td><a href="nara-scientific" class="btn btn-primary">View</a></td>
          </tr>

        </tbody>
      </table>
    </div>

  </div>

</div>