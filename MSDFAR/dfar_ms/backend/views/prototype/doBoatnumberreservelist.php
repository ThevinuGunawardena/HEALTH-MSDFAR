<?php

/** @var yii\web\View $this */

$this->title = 'Boat Number Requests';
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
        <label for="Fi_district">Search by Yard Request No</label>
        <input type="text" class="form-control">

      </div>

    </div>
  </div>
  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by Boat Number</label>
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
            <th scope="col">Boat Category Code</th>
            <th scope="col">Yard Number</th>
            <th scope="col">Yard ID</th>
            <th scope="col">Yard Name</th>
            <th scope="col">Vessel Length</th>
            <th scope="col">Payment Status</th>
            <th scope="col">Action</th>
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
            <td><h2><span class="badge badge-success">Paid</span></h2></td>
            <td><a href="#" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">REQ/BD/6228</th>
            <td>DFAR/FI/CHW/BY/2009/0013/LO/4.496</td>
            <td>NTRB</td>
            <td>DFAR/FI/CHW/BY/2009/0013</td>
            <td>Fibre</td>
            <td>In Progress</td>
            <td><h2><span class="badge badge-success">Paid</span></h2></td>
            <td><a href="#" class="btn btn-primary">View</a></td>
          </tr>
          <tr>
            <td scope="row">REQ/BD/6228</th>
            <td>DFAR/FI/CHW/BY/2009/0013/LO/4.496</td>
            <td>IMUL</td>
            <td>DFAR/FI/CHW/BY/2009/0013</td>
            <td>Fibre</td>
            <td>In Progress</td>
            <td><h2><span class="badge badge-warning">To be Paid</span></h2></td>
            <td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paynow">Pay</button></td>
          </tr>

        </tbody>
      </table>
    </div>

  </div>

</div><!-- Button trigger modal -->

</button>

<!-- Modal -->
<div class="modal fade" id="paynow" tabindex="-1" role="dialog" aria-labelledby="paynow" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Upload Payment Receipt</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <lable>Upload Payment Receipt</lable>
      <input type="file" class="form-control" id="customFile" /><br>
      <lable>Enter Paid Amount</lable>
      <input type="text" class="form-control" id="amount" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Mark as Paid</button>
      </div>
    </div>
  </div>
</div>