<?php

/** @var yii\web\View $this */

$this->title = 'Add New Employee';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
      <h3 class="card-title">Add New Employee</h3>
    </div>

  </div>

  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">


        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="GS_Division">Employee Number</label>
                  <input type="text" class="form-control" id="Emp_Number">

                </div>
              </div>
              <div class="col-xl-6">

              </div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="GS_Division">Employee Name</label>
          <input type="text" class="form-control" id="Emp_Name">
        </div>
        <div class="form-group">
          <label for="GS_Division">Address</label>
          <input type="text" class="form-control" id="Emp_Addr">
        </div>
        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="GS_Division">Contact Number 01</label>
                  <input type="text" class="form-control" id="Con_Number1">

                </div>
              </div>
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="GS_Division">Contact Number 02</label>
                  <input type="text" class="form-control" id="Con_Number2">

                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="GS_Division">Email</label>
                  <input type="text" class="form-control" id="Con_Number1">

                </div>
              </div>
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="GS_Division">Designation</label>
                  <select name="user_roll" class="form-control">
                    <option value="" selected hidden>Please Choose...</option>
                    <option>Fisheries Inspector</option>
                    <option>Assistant Director</option>
                    <option>Director General</option>
                    <option>District Officer</option>
                    <option>Admin</option>
                  </select> <br>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="GS_Division">First Appointment Date</label>
                  <input type="date" class="form-control" id="firstAppointdate">

                </div>
              </div>
              <div class="col-xl-6">

              </div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="GS_Division">Professional Qualification</label>
          <input type="text" class="form-control" id="ProQual">
        </div>

        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="GS_Division">Office Category</label>
                  <select name="user_roll" class="form-control">
                    <option value="" selected hidden>Please Choose...</option>
                    <option>Fisheries Inspector</option>
                    <option>Assistant Director</option>
                    <option>Director General</option>
                    <option>District Officer</option>
                    <option>Admin</option>
                  </select> <br>
                </div>
              </div>
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="GS_Division">Division</label>
                  <select name="user_roll" class="form-control">
                    <option value="" selected hidden>Please Choose...</option>
                    <option>Fisheries Inspector</option>
                    <option>Assistant Director</option>
                    <option>Director General</option>
                    <option>District Officer</option>
                    <option>Admin</option>
                  </select> <br>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="GS_Division">District Office</label>
                  <select name="user_roll" class="form-control">
                    <option value="" selected hidden>Please Choose...</option>
                    <option>Fisheries Inspector</option>
                    <option>Assistant Director</option>
                    <option>Director General</option>
                    <option>District Officer</option>
                    <option>Admin</option>
                  </select> <br>
                </div>
              </div>
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="GS_Division">Feild Office</label>
                  <select name="user_roll" class="form-control">
                    <option value="" selected hidden>Please Choose...</option>
                    <option>Fisheries Inspector</option>
                    <option>Assistant Director</option>
                    <option>Director General</option>
                    <option>District Officer</option>
                    <option>Admin</option>
                  </select> <br>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="GS_Division">Status</label>
                  <select name="user_roll" class="form-control">
                    <option value="" selected hidden>Please Choose...</option>
                    <option>Active</option>
                    <option>De-Active</option>

                  </select> <br>
                </div>
              </div>
              <div class="col-xl-6">

              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="Fi_district">Reporting Manager Employee Number</label>
                  <div class="input-group mb-3">
                    <input type="text" name="RepManEmpNum" class="form-control">
                    <div class="input-group-append">
                      <button type="button" class="btn btn-primary">Search Employee</button>
                    </div>
                  </div>


                </div>
              </div>
              <div class="col-xl-6">

              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="Fi_district">Employee Signature</label>
                  <div class="input-group mb-3">
                    <input type="file" name="SerEmp_Num" class="form-control">

                  </div>


                </div>
              </div>
              <div class="col-xl-6">

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="row">
      <div class="col-xl-3"></div>
      <div class="col-xl-3"></div>
      <div class="col-xl-3">
        <div class="form-group">
          <br> <a href="" class="btn btn-danger btn-block">Cancel</a>
        </div>
      </div>
      <div class="col-xl-3">
        <div class="form-group">
          <br> <a href="fm-register2" class="btn btn-success btn-block">Create</a>
        </div>
      </div>
    </div>
  </div>

</div>