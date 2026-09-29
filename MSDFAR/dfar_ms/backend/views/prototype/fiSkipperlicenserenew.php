<?php

/** @var yii\web\View $this */

$this->title = 'Skipper License Renewal Requests ';
?>
<div class="row">

  <div class="col-xl-6">
    <div class="section-block" id="cards">
    </div>
  </div>
  <div class="col-xl-6">
  </div>




  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by Request Number</label>
        <input type="text" class="form-control" id="YORec">

      </div>

    </div>
  </div>
  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Fisherman ID</label>
        <input type="text" class="form-control" id="YORec">

      </div>

    </div>

  </div>
  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by NIC</label>
        <input type="text" class="form-control" id="YORec">

      </div>

    </div>
  </div>
  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by Skipper Name</label>
        <input type="text" class="form-control" id="YORec">

      </div>

    </div>
  </div>
  <div class="col-xl-3">
    <div class="card-body">
      <div class="form-group">
        <a href="#" class="btn btn-warning">Search</a>
      </div>

    </div>
  </div>
</div>

<div class="row">
  
  <div class="col-xl-6">
  <h3 class="card-subtitle mb-2 text-muted">Operational License Requests In Progress</h3>

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
            <th scope="col">Tracking Number</th>
            <th scope="col">Fisherman Id</th>
            <th scope="col">Fisherman Name</th>
            <th scope="col">Educational Qualification</th>
            <th scope="col">View More</th>
           
          </tr>
        </thead>
        <tbody>
          <tr>
            <td scope="row">REQ/SKL/2011021054151115</th>
            <td>FM0010566TCO</td>
            <td>S.H. JAYARATHNA</td>
            <td>Eight</td>
            <td><a href="fi-skipperlicenserenewview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
          <td scope="row">REQ/SKL/2011021246431143</th>
            <td>FM0009679PTM</td>
            <td>M.S.P.K. PERERA</td>
            <td>OL</td>
            <td><a href="fi-skipperlicenserenewview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
          <td scope="row">REQ/SKL/2011021248111111</th>
            <td>FM0009680PTM</td>
            <td>S. SELVANADAN</td>
            <td>OL</td>
            <td><a href="fi-skipperlicenserenewview" class="btn btn-primary">View</a></td>
          </tr>
         
         
        </tbody>
      </table>
    </div>

  </div>

</div>