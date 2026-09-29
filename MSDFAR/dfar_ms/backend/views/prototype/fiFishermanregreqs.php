<?php

/** @var yii\web\View $this */

$this->title = 'Fisherman Register Requests';
?>
<div class="row">

  <div class="col-xl-6">
    <div class="section-block" id="cards">
    </div>
  </div>
  <div class="col-xl-6">
    <div class="col-xl-4"><a href="fm-register" class="btn btn-primary">Add New</a></div>
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
            <th scope="col">Applicant Name</th>
            <th scope="col">Other Name/s</th>
            <th scope="col">NIC</th>
            <th scope="col">Status</th>
            <th scope="col">View More</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td scope="row">Jayarathna Mudiyanselage</th>
            <td>Buddhika Tharanga</td>
            <td>961564387v</td>
            <td>Approved</td>
            <td><a href="fi-fishermanregreqsview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">Jayarathna Mudiyanselage</th>
            <td>Buddhika Tharanga</td>
            <td>961564387v</td>
            <td>Rejected</td>
            <td><a href="fi-fishermanregreqsview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">Jayarathna Mudiyanselage</th>
            <td>Buddhika Tharanga</td>
            <td>961564387v</td>
            <td>Rejected</td>
            <td><a href="fi-fishermanregreqsview" class="btn btn-primary">View</a></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

</div>