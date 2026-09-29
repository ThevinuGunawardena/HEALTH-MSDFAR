<?php

/** @var yii\web\View $this */

$this->title = 'Reserve New Boat Number';
?>

<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Reserve New Boat Number</h3>
        </div>
    </div>
  
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h4>Owner Details</h4>
                <div class="input-group-append">
                    <button type="button" name="yard_owner" class="btn btn-primary">Add Boat Owner</button>                  
                </div>
        
                <div class="table-responsive ">
                    <table class="table">
                        <thead><br> 
                        <tr>
                            <th scope="col">Fisherman ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">NIC</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>FM000001</td>
                            <td>A.B.C Perera</td>
                            <td>831373547V</td>
                            <td><a href="#" class="btn btn-danger">Remove</a></td>
                        </tr>                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h4>Boat Details</h4>
                <div class="col-xl-12">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                            <label>Boat Yard Number</label><br>
                                                <div class="input-group mb-3">                        
                                                    <input type="text" name="yard_no" class="form-control">
                                            
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary">Search</button><br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                            <label>Boat Yard Name</label><br>
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
                

                <label>Boat Design</label><br>
                <div class="input-group mb-3">                        
                    <input type="text" name="yard_no" class="form-control" readonly>
            
                   
                </div>
            
                <label>Boat Category</label><br>
                <select class="form-control" id="input-select">
                          <option>Select Boat Category</option>
                          <option>IMUL</option>
                          <option>OFRP</option>
                          <option>NTRB</option>
                          <option>MTRB</option>

                        </select><br>
                <label>Vessel Length (Meter)</label><br>
                <input type="text" name="vessel_length" class="form-control"/><br>	
                <label>Hull Number</label><br>
                <input type="text" name="hull_no" class="form-control"/><br>	                   
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h4>Boat Propelling Details</h4>
                <label>District</label><br>
                <select name="district" class="form-control">
                    <option value=""  selected hidden>Please Choose...</option>
                    <option>Gampaha</option>                       
                </select> <br>      

                <label>Fisheries District</label><br>
                <select name="fi_district" class="form-control">
                    <option value=""  selected hidden>Please Choose...</option>
                    <option>Negombo</option>                       
                </select> <br>      

                <label>Fisheries Division</label><br>
                <select name="fi_division" class="form-control">
                    <option value=""  selected hidden>Please Choose...</option>
                    <option>Pitipana</option>                       
                </select> <br>
                <label>Fisheries Management Area</label><br>
        <input type="text" name="fi_district" class="form-control" readonly/><br>	              
                <label>Remarks</label><br>
                <input type="text" name="remarks" class="form-control"/><br>	
                              
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h4>Required Documents For Boat Number Reserve</h4>
                <label>Documents</label><br>

           
                <div class="input-group-append">
                    <button type="button" name="btn_doc_upload" class="btn btn-primary">Upload Documents</button>
                </div>
            </div>
        </div>     
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h4>Amount to be paid after approval</h4>
                <label>Rs.500.00</label><br>

           
                
            </div>
        </div>     
    </div>





    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <button type="button" name= "btncancell" class="btn btn-warning">Cancel</button>
                <button type="button" name= "btnsubmit"  class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>









</div>