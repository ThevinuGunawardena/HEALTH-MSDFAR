<?php

/** @var yii\web\View $this */

$this->title = 'High Seas Operation License';
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
                    <label>FM/TLE/8536</label>
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="Boat_Nums">Boat Number</label>
                    <input type="text" class="form-control" value="IMULA2345MTR" id="BoatNo" readonly>

                  </div>
                </div>
               
              </div>
            </div>
          </div>
                    
                  </div>
                </div>
                

                
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Fisheries Details</h6>
        <form action="">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-12">
                  <div class="form-group">
                    <label for="Fisherman">Select Skipper</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control">
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary">Search</button>
                        </div>
                      </div>
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
                    <label for="NoCrew">Skipper Fisherman ID : </label>
                    <label>FM/TLE/8536</label>
                  </div>
                  <div class="form-group">
                    <label for="NoCrew">Skipper Name : </label>
                    <label>W.T.C. Sitipala</label>
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
                    <label for="GS_Division">Previous Boat Flags</label>
                    <select class="form-control">
                    <option>N/A</option>
                    <option>Sri Lanka</option>
                    <option>Indoonesia</option>
                    <option>Taiwan</option>
                    <option>Vietnam</option>
                    <option>Thailand</option>
                    <option>Seychelles</option>
                    <option>Maldives</option>
                    <option>China</option>
                    </select>
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="NoCrew">No. of Crew Members</label>
                    <input type="text" class="form-control" id="NoCrew">
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
        <h6 class="card-subtitle mb-2 text-muted">Unloading Location</h6>
        <form action="">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="Fi_district">Landing Site</label>
                    <select class="form-control">
                    <option>Colombo</option>
                    <option>Galle</option>
                    <option>Mathara</option>
                    <option>Negombo</option>
                    
                    </select>
                  </div>
                </div>
                <div class="col-xl-6">
                
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
                        <th scope="col">#</th>
                        <th scope="col">Fisheries District</th>
                        <th scope="col">Landing Site</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th scope="row">1</th>
                        <td>Negambo</td>
                        <td>Pitipana</td>
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
      
      
        

      <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
 

    

    
        <div class="card-body">
        <form action="">
          <div class="row">

            <div class="col-xl-12">
              <div class="row">
                
                <div class="col-xl-12">
                  <div class="form-group">
        <h4 class="card-subtitle mb-2 text-muted">Required Documents For Operational License</h4>
        <label for="Docs">Operational License Request Documents*</label>
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
                  <a href="#" class="btn btn-primary">Apply Now</a>
                   
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

               
  </div>
</div>
