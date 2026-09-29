<?php

/** @var yii\web\View $this */

$this->title = 'Add New Fishing Gear Type';
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Fishing Equipments and Species of Fish</h6>
        <form action="">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="Gear_Cat">Gear Category</label>
                    <select class="form-control">
                    <option>Surrounding</option>
                    <option>Line</option>
                    <option>Others</option>
                    <option>Traps</option>
                    <option>Gillnet</option>
                    
                    </select>
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="gear">Gear</label>
                    <select class="form-control">
                    <option>GNLM</option>
                    <option>GNSM</option>
                    <option>Surrounding-Beach seinee</option>
                    <option>Surrounding-Laila valai</option>
                    
                    
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
                    <label for="units">Units</label>
                    <input type="text" class="form-control">
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="DS_Division">Mesh Size</label>
                    <input type="text" class="form-control" id="M_Size">
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
                    <label for="Diameter">Diameter</label>
                    <input type="text" class="form-control">
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="DS_Division">Height (Meter)</label>
                    <input type="text" class="form-control" id="Height">
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- <div class="row">
          <div class="col-xl-3"></div>
          <div class="col-xl-3"></div>
          <div class="col-xl-3"></div>
          <div class="col-xl-3">
                <a href="#" class="btn btn-primary">Add</a>
                </div>
          </div> -->

        </form>


      </div>
      <div class="card-body">
                
         <div class="card mb-5 shadow-sm">
            <div class="card-body">
            <form action="">
                <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-12">
                  <div class="form-group">
                    <label for="FI_District">Fish Species</label>
                    <select class="form-control">
                    <option>White Snapper</option>
                    <option>Silver Biddy</option>
                    <option>Grouper</option>
                    <option>Milk Fish	</option>
                    
                    
                    </select>
                  </div>
                </div>
                
              </div>
              <div class="row">
                <div class="col-xl-12">
                  <div class="form-group">
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Fish Name</th>
                       
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th scope="row">1</th>
                        <td>White Snapper</td> 
                        <td><a href="#" class="btn btn-danger">Remove</a></td>
                      </tr>
                      
                    </tbody>
                  </table>
                   
                  </div>
                </div>
                
              </div>
            </div>
          </div>


      <div class="card-body">
        <form action="">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-xl-12">
                  <div class="form-group">
                    <h6 for="FI_District">Periods and Time Durations Of Fishing</h6>
                      
                  </div>
                </div>
                
              </div>
              <div class="row">
              <table class="table" id="periods">
                  <thead>
                    <tr id="header-main">
                     
                     <th scope="col" colspan="6" >Fishing Time Periods</th>
                     
                     <th scope="col" colspan="2" >Fishing Time Durations</th>
                    
                    </tr>
                    <tr id="header-main">
                     
                     <th scope="col" colspan="5" >All Over the year</th>
                     <th><input type="checkbox" name="AllYear" /></th>
                     <th scope="col" >All Over the day</th>
                     <th scope="col"><input type="checkbox" name="AllYear" /></th>
                     
                   </tr>
                  </thead>
                  <tbody>
                    <tr class="table-success">
                      <td>January</td>
                      <td><input type="checkbox" name="Jan" /></td>
                      <td>February</td>
                      <td><input type="checkbox" name="Feb" /></td>
                      <td>March</td>
                      <td><input type="checkbox" name="Mar" /></td>
                      <td>4AM to 10AM</td>
                      <td><input type="checkbox" name="4-10" /></td>
                      
                    </tr>
                    <tr class="table-success">
                      <td>April</td>
                      <td><input type="checkbox" name="Apr" /></td>
                      <td>May</td>
                      <td><input type="checkbox" name="May" /></td>
                      <td>June</td>
                      <td><input type="checkbox" name="Jun" /></td>
                      <td>10AM to 4PM	</td>
                      <td><input type="checkbox" name="10-4" /></td>
                    </tr>
                    <tr class="table-success">
                      <td>July</td>
                      <td><input type="checkbox" name="Apr" /></td>
                      <td>August</td>
                      <td><input type="checkbox" name="May" /></td>
                      <td>September</td>
                      <td><input type="checkbox" name="Jun" /></td>
                      <td>4PM to 8PM</td>
                      <td><input type="checkbox" name="4-8" /></td>
                    </tr>

                    <tr class="table-success">
                      <td>October</td>
                      <td><input type="checkbox" name="Apr" /></td>
                      <td>November</td>
                      <td><input type="checkbox" name="May" /></td>
                      <td>December</td>
                      <td><input type="checkbox" name="Jun" /></td>
                      <td>8PM to 4AM	</td>
                      <td><input type="checkbox" name="8-4" /></td>
                    </tr>
                    
                  </tbody>
                </table>
                
              </div>
              
            
          </div>


          


        </form>


      </div>
      </div>




        </form>


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
            
