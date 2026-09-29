<?php

/** @var yii\web\View $this */

$this->title = 'Boat Details Update';
?>
<div class="row">

  

  <div class="col-xl-6">
    <div class="card-body">
      <div class="form-group">
        <label for="Fi_district">Search by Boat Number</label>
        <div class="input-group mb-3">                        
                    <input type="text" name="yard_no" class="form-control">
            
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary">Search</button><br>
                    </div>
                </div> 

      </div>

    </div>
  </div>
  
 
</div>



<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="col-xl-12">
      <table class="table"><br>
        <thead>
          <tr>
            <th scope="col">Boat Category Code</th>
            <th scope="col">Boat Number</th>
            <th scope="col">Yard Name</th>
            <th scope="col">Vessel Length </th>
            <th scope="col">View More</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>IMUL</td>
            <td>IMULA2345MTR</td>
            <td>DFAR/FI/TLE/BY/2009/0045</td>
            <td>14.40</td>
            <td><a href="fi-boatregistrationupdate" class="btn btn-primary">View & Update</a></td>
          </tr>
          <tr>
            <td>IMUL</td>
            <td>IMULA2345MTR</td>
            <td>DFAR/FI/MTR/BY/2009/0051</td>
            <td>14.40</td>
            <td><a href="fi-boatregistrationupdate" class="btn btn-primary">View & Update</a></td>
          </tr>
          <tr>
            <td>IMUL</td>
            <td>IMULA2345MTR</td>
            <td>DFAR/FI/TLE/BY/2009/0045</td>
            <td>14.40</td>
            <td><a href="fi-boatregistrationupdate" class="btn btn-primary">View & Update</a></td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

</div>