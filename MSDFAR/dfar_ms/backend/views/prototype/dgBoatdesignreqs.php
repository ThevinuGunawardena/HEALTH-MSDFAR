<?php

/** @var yii\web\View $this */

$this->title = 'Boat Design Requests';
?>
<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
      
    </div>
  </div>

  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by Yard Request No</label>
        <input type="text" class="form-control">

      </div>

    </div>
  </div>
  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by Yard Name</label>
        <input type="text" class="form-control">

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
            <th scope="col">Design Module Name</th>
            <th scope="col">Boat Category Code</th>
            <th scope="col">Yard ID</th>
            <th scope="col">Design Hull Material</th>
            <th scope="col">Status</th>
            <th scope="col">View More</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td scope="row">REQ/BD/6257</th>
            <td>DFAR/FI/KLT/BY/2009/0030/Lo/test/15.8</td>
            <td>IMUL</td>
            <td>DFAR/FI/KLT/BY/2009/0030</td>
            <td>Fibre</td>
            <td>Approved</td>
            <td><a href="dg-boatdesignview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">REQ/BD/6228</th>
            <td>DFAR/FI/CHW/BY/2009/0013/LO/4.496</td>
            <td>NTRB</td>
            <td>DFAR/FI/CHW/BY/2009/0013</td>
            <td>Fibre</td>
            <td>In Progress</td>
            <td><a href="dg-boatdesignview" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">REQ/BD/6228</th>
            <td>DFAR/FI/CHW/BY/2009/0013/LO/4.496</td>
            <td>IMUL</td>
            <td>DFAR/FI/CHW/BY/2009/0013</td>
            <td>Fibre</td>
            <td>In Progress</td>
            <td><a href="dg-boatdesignview" class="btn btn-primary">View</a></td>
          </tr>

        </tbody>
      </table>
    </div>

  </div>

</div>