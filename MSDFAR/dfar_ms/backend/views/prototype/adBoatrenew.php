<?php

/** @var yii\web\View $this */

$this->title = 'Boat Renewal Requests';
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
        <label for="Fi_district">Search by NIC</label>
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
  <div class="col-xl-3">
    <div class="card-body">
      <div class="form-group">
        <a href="bo-renewal" class="btn btn-warning">Search</a>
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
            <th scope="col">Document Reference Number</th>
            <th scope="col">Boat Number</th>
            <th scope="col">Year Of Made</th>
            <th scope="col">Boat Category</th>
            <th scope="col">Status</th>
            <th scope="col">View More</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td scope="row">REQ/BNR/7261</th>
            <td>IMULA8888NBO</td>
            <td>2022</td>
            <td>IMUL</td>
            <td>Approved</td>
            <td><a href="ad-boatrenewview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">REQ/BNR/7427</th>
            <td>IMULA6565BCO</td>
            <td>2023</td>
            <td>IMUL</td>
            <td>In Progross</td>
            <td><a href="ad-boatrenewview" class="btn btn-primary">View</a></td>
          </tr>         
        </tbody>
      </table>
    </div>

  </div>

</div>