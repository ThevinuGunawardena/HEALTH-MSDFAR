<?php

/** @var yii\web\View $this */

$this->title = 'Boat Register Requests';
?>
<div class="row">
<div class="col-xl-6">
    <div class="section-block" id="cards">
      <h3 class="section-title"><?= Yii::t('app', 'Welcome to the fisheries department online portal') ?></h3>
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
            <th scope="col">Document Reference Number</th>
            <th scope="col">Boat Category Code</th>
            <th scope="col">Yard Number</th>
            <th scope="col">Yard Name</th>
            <th scope="col">Vessel Length </th>
            <th scope="col">Status</th>
            <th scope="col">View More</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td scope="row">REQ/BNR/7261</th>
            <td>IMUL</td>
            <td>DFAR/FI/TLE/BY/2009/0045</td>
            <td>NIMIDULA MARINE KUDAWELLA</td>
            <td>14.40</td>
            <td>Approved</td>
            <td><a href="fi-boatregreqsview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">REQ/BNR/7427</th>
            <td>IMUL</td>
            <td>DFAR/FI/MTR/BY/2009/0051</td>
            <td>Chamod Marine</td>
            <td>14.40</td>
            <td>In Progross</td>
            <td><a href="fi-boatregreqsview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">REQ/BNR/7261</th>
            <td>IMUL</td>
            <td>DFAR/FI/TLE/BY/2009/0045</td>
            <td>NIMIDULA MARINE KUDAWELLA</td>
            <td>14.40</td>
            <td>Approved</td>
            <td><a href="fi-boatregreqsview" class="btn btn-primary">View</a></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

</div>