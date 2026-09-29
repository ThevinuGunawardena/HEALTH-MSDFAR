<?php

/** @var yii\web\View $this */

$this->title = 'Boat Cancelation Requests';
?>
<div class="row">

  <div class="col-xl-6">
    <div class="section-block" id="cards">
    </div>
  </div>
  



  <div class="col-xl-12">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by Boat Number</label>
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
            <th scope="col">Applicant Name</th>
            <th scope="col">Boat Number</th>
            
            <th scope="col">View More</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td scope="row">Jayarathna Mudiyanselage</th>
            <td>IMULA5343CBO</td>
            
            <td><a href="ad-bocancelationview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">Jayarathna Mudiyanselage</th>
            <td>IMULA1234MTR</td>
           
            <td><a href="ad-bocancelationview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">Jayarathna Mudiyanselage</th>
            <td>IMULA6343TCO</td>
           
            <td><a href="ad-bocancelationview" class="btn btn-primary">View</a></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

</div>