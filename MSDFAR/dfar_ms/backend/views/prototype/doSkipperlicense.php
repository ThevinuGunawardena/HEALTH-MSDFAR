<?php

/** @var yii\web\View $this */

$this->title = 'Skipper License';
?>
<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">

    </div>
  </div>
  <div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Fisherman Details</h6>

        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="NoCrew">Fisherman ID : </label>
                  <div class="input-group mb-3">                        
                    <input type="text" name="yard_no" class="form-control">
            
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary">Search</button><br>
                    </div>
                </div>
                </div>
                <div class="form-group">
                  <label for="NoCrew">Name : </label>
                  <label> I.Arasarathinam Ilasumanan</label>
                </div>
                <div class="form-group">
                  <label for="NoCrew">NIC : </label>
                  <label>621812619v</label>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>



    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Fisherman Education Details</h6>
        <form action="">

          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-12">
                  <div class="form-group">
                    <label for="Edu_Qual">Highest Education Qualification</label>
                    <select class="form-control">
                      <option>A/L Qualified</option>
                      <option>O/L Qualified</option>
                      <option>Grade 8 Qualified</option>
                      <option>Other</option>

                    </select>
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
                    <label for="OtherQual">Other Qualifications</label>
                    <input type="text" class="form-control">
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
                    <label for="Fi_district">Fisheries District</label>
                    <select class="form-control">
                      <option>Colombo</option>
                      <option>Galle</option>
                      <option>Mathara</option>
                      <option>Negombo</option>

                    </select>
                  </div>
                </div>

                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="Fi_division">Fisheries Division</label>
                    <select class="form-control">
                      <option>Colombo</option>
                      <option>Galle</option>
                      <option>Mathara</option>
                      <option>Negombo</option>

                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </form>


      </div>
    </div>

    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Fishermen Training Programs</h6>
        <form action="">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="Institute">Institute</label>
                    <select class="form-control">
                      <option>CINEC</option>
                      <option>OCEAN UNIVERSITY</option>


                    </select>
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="Fi_district">Program Name</label>
                    <select class="form-control">
                      <option>SKIPPER TRAINING</option>
                      <option>FISHING VESSELS SKIPPER TRAINING</option>

                    </select>
                  </div>
                </div>


              </div>
            </div>

            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="Institute">Training Period</label>
                    <input type="text" class="form-control">

                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="DOC">Date of Certified</label>
                    <input type="date" class="form-control">

                  </div>
                </div>


              </div>
            </div>

            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">

                </div>
                <div class="col-xl-3">

                </div>
                <div class="col-xl-3">
                  <a href="#" class="btn btn-primary">Add</a>
                </div>


                <div class="card-body">
                  <div class="table-responsive ">
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">Institute</th>
                          <th scope="col">Program Name</th>
                          <th scope="col">Trained Period</th>
                          <th scope="col">Date Of Certified</th>
                          <th></th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <th scope="row">OCEAN UNIVERSITY OF SRI LANKA </th>
                          <td>FISHING VESSELS SKIPPER TRAINING </td>
                          <td>7</td>
                          <td>2023-03-01</td>
                          <td></td>
                          <td><a href="#" class="btn btn-danger">Remove</a></td>
                        </tr>

                      </tbody>
                    </table>



        </form>


      </div>


    </div>


  </div>
</div>




</form>


</div>
</div>
</div>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">


  <div class="card mb-5 shadow-sm">
    <div class="card-body">
      <form action="">
        <div class="row">
          <div class="col-xl-12">
            <div class="row">
              <div class="col-xl-12">
                <div class="form-group">
                  <label for="Payment">Payments</label>
                  <select class="form-control">
                    <option>Select Payment Type</option>
                    <option>Cash</option>
                    <option>Online Transfer</option>



                  </select>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-xl-12">
                <div class="form-group">
                  <label for="PDate">Payment Date</label>
                  <input type="date" class="form-control">


                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-xl-12">
                <div class="form-group">
                  <label for="Remarks">Remarks</label>
                  <input type="text" class="form-control" id="Remarks">


                </div>
              </div>

            </div>
          </div>
        </div>





      </form>


    </div>
  </div>

  <div class="card mb-5 shadow-sm">
    <div class="card-body">
      <form action="">
        <div class="row">

          <div class="col-xl-12">
            <div class="row">

              <div class="col-xl-12">
                <div class="form-group">
                  <h4 class="card-subtitle mb-2 text-muted">Skipper License Request Documents</h4>
                </div>
              </div>

            </div>
            <div class="row">
              <div class="col-xl-12">
                <div class="form-group">
                  <a href="#" class="btn btn-primary"><input type="file"></a>

                </div>
              </div>

            </div>

            <div class="row">
              <div class="col-xl-10">

              </div>
              <div class="col-xl-2">
                <div class="form-group">
                  <a href="#" class="btn btn-primary">Submit</a>

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
</div>