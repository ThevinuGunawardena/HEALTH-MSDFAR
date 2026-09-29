<?php

/** @var yii\web\View $this */

$this->title = 'Fisherman Registration Renewal Page 3';
?>
<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
      <h3 class="card-title">Fisherman Registration</h3>

    </div>
  </div>
  <div class="col-xl-8">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Spouse Details</h6>
        <form action="">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="SpouseName">Name of the beneficiary / Spouse</label>
                    <input type="text" class="form-control" id="SpouseName">

                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="SpouseNameNIC">NIC of the beneficiary / Spouse</label>
                    <input type="text" class="form-control" id="SpouseNameNIC">

                  </div>
                </div>
              </div>
            </div>
          </div>

        </form>


      </div>
    </div>
  </div>

  <div class="col-xl-8">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Children Details</h6>
        <form action="">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="ChildName">Name</label>
                    <input type="text" class="form-control" id="ChildName">

                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="ChildDOB">Birthday</label>
                    <input type="date" class="form-control" id="ChildDOB">

                  </div>
                </div>

              </div>
            </div>

            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-12">
                  <div class="row">
                    <div class="col-xl-10">

                    </div>
                    <div class="col-xl-2">
                      <div class="form-group">

                        <button type="button" class="btn btn-primary">Add<i class="fa fa-plus" aria-hidden="true"></i>


                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>



          </div>

        </form>


      </div>
    </div>
  </div>

  <div class="col-xl-8">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Details of Other Dependents</h6>
        <form action="">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="OtherName">Name</label>
                    <input type="text" class="form-control" id="OtherName">

                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="OtherDOB">Birthday</label>
                    <input type="date" class="form-control" id="OtherDOB">

                  </div>
                </div>

              </div>
            </div>

            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-12">
                  <div class="row">
                    <div class="col-xl-10">

                    </div>
                    <div class="col-xl-2">
                      <div class="form-group">

                        <button type="button" class="btn btn-primary">Add<i class="fa fa-plus" aria-hidden="true"></i>


                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>

        </form>


      </div>
    </div>
  </div>

  <div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">

        <form action="">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <h6 class="card-subtitle mb-2 text-muted">Declaration of Applicant</h6>
                  <label for="declaration">I declare that the above said information are true and accurate.</label>
                  <div>
                    <label class="custom-control custom-radio custom-control-inline">
                      <input type="radio" name="radio-inline" checked="" class="custom-control-input"><span class="custom-control-label">Manual Signature</span>
                    </label>
                    <label class="custom-control custom-radio custom-control-inline">
                      <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">Capture Signature</span>
                    </label>
                  </div>
                  <div class="col-xl-12">

                    <canvas id="signature-pad" width="400" height="400" style="margin-top: 6%;"></canvas>
                    <div class="col-xl-6">
                      <div class="form-group">
                        <button type="button" class="btn btn-primary">Save<i class="fa fa-check" aria-hidden="true"></i></button>
                        <button type="button" id="clear" class="btn btn-primary">Clear<i class="fa fa-eraser" aria-hidden="true"></i></button>
                      </div>
                    </div>

                  </div>


                </div>

                <div class="col-xl-6"></div>
                <div class="col-xl-6"></div>
                <div class="col-xl-3"></div>

                <div class="col-xl-3">
                  <div class="form-group">
                    <div class="row">
                      <div class="col-xl-6">
                      <a href="fm-register2renew" class="btn btn-primary btn-block"><i class="fa fa-arrow-left" aria-hidden="true"></i>Back</a>                    
                      </div>
                      <div class="col-xl-6">
                      <a href="" class="btn btn-primary">Submit<i class="fa fa-arrow-right" aria-hidden="true"></i></a>

                      </div>
                    </div>

                  </div>
                </div>

              </div>
            </div>

        </form>


      </div>
    </div>
  </div>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.3.5/signature_pad.min.js" integrity="sha512-kw/nRM/BMR2XGArXnOoxKOO5VBHLdITAW00aG8qK4zBzcLVZ4nzg7/oYCaoiwc8U9zrnsO9UHqpyljJ8+iqYiQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
       var canvas = document.getElementById("signature-pad");

       function resizeCanvas() {
           var ratio = Math.max(window.devicePixelRatio || 1, 1);
           canvas.width = canvas.offsetWidth * ratio;
           canvas.height = canvas.offsetHeight * ratio;
           canvas.getContext("2d").scale(ratio, ratio);
       }
       window.onresize = resizeCanvas;
       resizeCanvas();

       var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(250,250,250)'
       });

       document.getElementById("clear").addEventListener('click', function(){
        signaturePad.clear();
       })
   </script>
  <!-- <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12">
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
  </div> -->

</div>




</div>