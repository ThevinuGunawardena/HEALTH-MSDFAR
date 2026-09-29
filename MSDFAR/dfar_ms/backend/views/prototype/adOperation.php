<?php

/** @var yii\web\View $this */

$this->title = 'Operational License Requests ';
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
        <label for="Fi_district">Search by Document Reference Number</label>
        <input type="text" class="form-control" id="YORec">

      </div>

    </div>
  </div>
  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">License Holders Fisheries ID</label>
        <input type="text" class="form-control" id="YORec">

      </div>

    </div>

  </div>
  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by License Holders Name</label>
        <input type="text" class="form-control" id="YORec">

      </div>

    </div>
  </div>
  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by License Holders NIC</label>
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
            <th scope="col">Document Reference Number	</th>
            <th scope="col">License Holders Fisheries ID</th>
            <th scope="col">License Holders Name</th>
            <th scope="col">License Holders NIC	</th>
            <th scope="col">Boat Number</th>
            <th scope="col">License Type</th>
            <th scope="col">View More</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td scope="row">REQ/SKLOPR/87</th>
            <td>FM0001680CBO</td>
            <td>M.M.D.B. DE SILVA	</td>
            <td>840034500V</td>
            <td>IMULA0418KLT</td>
            <td>HIGHSEA</td>
            <td><a href="ad-operationview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
          <td scope="row">REQ/SKLOPR/182</th>
            <td>FM0006075MTR</td>
            <td>Marakkala Manage</td>
            <td>763314448V</td>
            <td>IMULA0387MTR</td>
            <td>HIGHSEA</td>
            <td><a href="ad-operationview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
          <td scope="row">REQ/SKLOPR/3313</th>
            <td>FM0019318CBO</td>
            <td>Ginthota Polwaththage</td>
            <td>902932453v</td>
            <td>IMULA0147CBO</td>
            <td>NATIONAL</td>
            <td><a href="ad-operationview" class="btn btn-primary">View</a></td>
          </tr>
          <td scope="row">REQ/SKLOPR/5365</th>
            <td>FM0034981MTR</td>
            <td>Warnasuriya Patabendige</td>
            <td>196927602673</td>
            <td>IMULA1632MTR</td>
            <td>NATIONAL</td>
            <td><a href="ad-operationview" class="btn btn-primary">View</a></td>
          </tr>
          <td scope="row">REQ/SKLOPR/1052</th>
            <td>FM0017933CHW</td>
            <td>Warnakulasoriya</td>
            <td>197615900847</td>
            <td>IMULA0632CHW</td>
            <td>HIGHSEA</td>
            <td><a href="ad-operationview" class="btn btn-primary">View</a></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

</div>