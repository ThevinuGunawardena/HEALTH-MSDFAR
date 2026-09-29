<?php

/** @var yii\web\View $this */

$this->title = 'Fisherman Renewal';
?>
<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
      <h3 class="card-title">Fisherman Registration</h3>

    </div>
  </div>
  <div class="col-xl-8 col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Fisheries Details</h6>
        <form action="">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="FI_District">Fisheries District</label>
                    <select class="form-control">
                      <option>Battcaloa</option>
                      <option>Colombo</option>
                      <option>Trincomalee</option>
                    </select>
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="FI_Div">Fisheries Division</label>
                    <select class="form-control">
                      <option>Battcaloa</option>
                      <option>Colombo</option>
                      <option>Trincomalee</option>
                    </select>
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
                    <label for="GS_Division">GS Division</label>
                    <select class="form-control">
                      <option>Battcaloa</option>
                      <option>Colombo</option>
                      <option>Trincomalee</option>
                    </select>
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="DS_Division">Divisional Secretariat Division</label>
                    <select class="form-control">
                      <option>Battcaloa</option>
                      <option>Colombo</option>
                      <option>Trincomalee</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </form>


      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Profile Image</h6>
        <div style="text-align:center;">
          <img id="img-upload">
          <div id="tem_img">
            <img src="https://www.pngitem.com/pimgs/m/4-42408_vector-art-design-men-fashion-vector-art-illustration.png" width="150px" height="150px" style="border-radius: 50%;">
          </div>
          <br><br>
          <div class="input-group">
            <span class="input-group-btn">
              <span class="btn btn-info btn-file">
                Choose Image (4x6) <input type="file" id="ProImg_File" name="ProImg_File">
              </span>
            </span>
          </div>
        </div>


      </div>
    </div>
  </div>
  <div class="col-xl-8 col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Personal Details</h6>
        <form action="">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="Fish_S_Name">Surname</label>
                    <input type="text" class="form-control" id="Fish_S_Name" readonly>
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="Fish_F_Name">First Name</label>
                    <input type="text" class="form-control" id="Fish_F_Name" readonly>
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
                    <label for="Pre_Name">Preferred Name for ID</label>
                    <input type="text" class="form-control" id="Pre_Name">
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="NIC">NIC No</label>
                    <input type="text" class="form-control" id="NIC">
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
                    <label for="PastP_No">Passport No</label>
                    <input type="text" class="form-control" id="PastP_No" readonly>
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="DOB">Date of Birth</label>
                    <input type="date" class="form-control" id="DOB" readonly>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <label for="Gender">Gender</label>
                  <div>
                    <label class="custom-control custom-radio custom-control-inline">
                      <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">Male</span>
                    </label>
                  </div>
                  <div>
                    <label class="custom-control custom-radio custom-control-inline">
                      <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">Female</span>
                    </label>
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
                <div class="col-xl-12">
                  <div class="form-group">
                    <label for="Per_Addr">Permanent Address</label>
                    <input type="text" class="form-control" id="Per_Addr">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-12">
                  <div class="form-group">
                    <label for="Cur_Addr">Current Address</label>
                    <input type="text" class="form-control" id="Cur_Addr">
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
            <label class="form-check-label" for="inlineCheckbox2">Same as Permanent Address</label>
          </div>

          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="BloodG">Blood Group</label>
                    <select class="form-control" readonly>
                      <option>A+</option>
                      <option>B+</option>
                      <option>AB+</option>
                      <option>O+</option>
                      <option>A-</option>
                      <option>B-</option>
                      <option>AB-</option>
                      <option>O-</option>
                    </select>
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="Mob">Mobile No</label>
                    <input type="text" class="form-control" id="Mob">
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
                    <label for="Tel">Telephone</label>
                    <input type="text" class="form-control" id="Tel">
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-3">

                </div>
                <div class="col-xl-3">

                </div>
                <div class="col-xl-3">

                </div>
                <div class="col-xl-3">
                  <div class="form-group">
                    <a href="fm-register2renew" class="btn btn-primary">Next<i class="fa fa-arrow-right" aria-hidden="true"></i></a>

                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </form>


      </div>
    </div>
  </div>




</div>