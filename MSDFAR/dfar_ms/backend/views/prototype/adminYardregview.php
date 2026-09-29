<?php

/** @var yii\web\View $this */

$this->title = 'Yard Registration';
?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
        <h3 class="section-title"><?= Yii::t('app', 'Welcome to the fisheries department online portal') ?></h3></br>
        <h3 class="card-title">Yard Registration</h3>
        </div>

        <div class="card mb-5 shadow-sm">
            <div class="boat-box"><br>
                <center><h3>Yard Name : Chamil Marine</h3>
                <h3>Yard Address : 248/5, St. Nikulas Road, Munnakkaraya, Negombo.</h3>
                <h3>Document Reference Number: DFAR/BY/184</h3>
                <h3>Registered Yard Number : DFAR/FI/NBO/BY/2009/0097</h3></center>
            </div>
        </div>



        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h4>Add Yard Owner/ Owners</h4>
                    <div class="input-group-append">
                        <button type="button" name="yard_owner" class="btn btn-primary">Add</button>
                    </div>
                    <div class="table-responsive ">
                        <table class="table">
                            <thead><br>
                                <tr>
                                    <th scope="col">Fisherman ID</th>
                                    <th scope="col">Owner Name</th>
                                    <th scope="col">NIC</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>FM000001</td>
                                    <td>Negombo</td>
                                    <td>Pitipana</td>
                                    <td><a href="#" class="btn btn-danger">Remove</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                
                    <div class="form-group">
                        <label name="yard_name">Yard Name : Test Yard</label><br>
                        <label name="yard_address" >Yard Address : No.25, Colombo Road, Negombo.</label><br>                      
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <h4>Contact Details</h4>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-4">
                                        <label name="mobile_no">Mobile Number : 0718454628</label> <br>
                                        <label name="email">E-Mail : sunethranatunga@gmail.com</label><br>
                                    </div>

                                    <div class="col-xl-4">
                                        <label name="land_line">Land-Line Number : 01125874562</label><br>
                                        <label name="web">Web : www.fisheries.gov.lk</label>      
                                    </div>

                                    <div class="col-xl-4">
                                        <label name="fax">Fax : 0114258694</label><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <h4>Yard Categories</h4>
                        <div class="row">
                            <div class="col-xl-12">
                                <label class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input"><span class="custom-control-label" onclick="return false;">Mechanized Inboard</span>
                                </label>
                                <label class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input"><span class="custom-control-label" onclick="return false;">Mechanized Outboard</span>
                                </label>

                                <label class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input"><span class="custom-control-label" onclick="return false;">Traditional Category</span>
                                </label>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>              

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <h4>Business Registration</h4>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <label name="br_no">Business Registration Number : Reg.0751</label>
                                    </div>

                                    <div class="col-xl-6">
                                        <label name="br_date">Business Registration Date : 2023-03-17</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="row">
                <div class="col-xl-6">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <div class="form-group">
                                <h4>Land & Yard Information</h4>
                                <label name="ya_ownership">Boat Yard Land Ownership : H.K.D Perera</label><br>
                                <label  name="ownership_date">Ownership Get Date : 2020-06-15</label>
                                <label name="deed_no">Deed Number : 100</label>
                                <label name="area_suare_meter">Land Area in Square meter (max 1000000 perch) : 20000</label>
                                <label name="area_under_roof">Area Under Roof Square meter (max 1000000 perch) : 5000</label>
                                <label name="remarks">Remark : NA</label>                                 
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <div class="form-group">
                                <h4>Location of Boat Yard</h4>
                                <label name="admin_district">Administrative District : Gampaha</label><br>
                                <label name="ds_division">DS Division : Negombo</label>
                                <label name="fi_district">Fisheries District : Negombo</label>  
                                <label name="fi_division">Fisheries Division : Pitipana</label>  
                                <label name="gps_latitude">GPS Latitude : 78.8</label> 
                                <label name="gps_longitude">GPS Longitude :81.56</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h4>Yard Safety Information</h4>
                    <div class="table-responsive ">
                        <table class="table">
                            <thead><br> 
                            <tr>
                                <th scope="col">Yard Safety Type</th>
                                <th scope="col">Number Of Items</th>
                                <th scope="col">Capacity</th>
                                <th scope="col">Location</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>CO2 Flooding</td>
                                <td>10</td>
                                <td>20</td>
                                <td>yard side</td>
                            </tr>
                            <tr>
                                <td>Soda Acid</td>
                                <td>10</td>
                                <td>20</td>
                                <td>yard outside</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>         
                    <!-- <div class="row">   ___________________________________________________ Model ________
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
                            <label>Yard Safety Type</label><br>
                            <select name="ya_safety_type" class="form-control">
                                <option value=""  selected hidden>Please Choose...</option>
                                <option>Form-Chemical</option>
                                <option>Dry Powder</option>
                                <option>Soda Acid</option>
                            </select> <br>                         
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
                            <label>Number Of Items</label><br>
                            <input type="number" name="length" class="form-control"/><br>                        
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
                            <label>Capacity (max 50 Liters)</label><br>
                            <input type="number" name="length" class="form-control"/><br>                                 
                        </div>                        
                    </div> -->            
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h4>Transportation</h4>
                    <label name="tranportation">Transporting Methods : Test</label><br>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h4>Yard Medicine Type</h4>
                    <div class="table-responsive ">
                        <table class="table">
                            <thead><br> 
                            <tr>
                                <th scope="col">Yard Medicine Type</th>
                                <th scope="col">Quantity</th>                               
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Bandage</td>
                                <td>10</td>
                            </tr>
                            <tr>
                                <td>Plaster</td>
                                <td>5</td>
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
                    <div class="form-group">
                        <h4>Distance To Nearest Hospital</h4>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-4">
                                        <label name="rural_hosp">Rural Hospital (km) : 2</label><br>
                                        <label name="dist_hosp">District Hospital (km) : 10</label><br>
                                        <label name="base_hosp">Base Hospital (km) : 14</label><br>
                                    </div>

                                    <div class="col-xl-4">
                                        <label name="teach_hosp">Teaching Hospital (km) : 18</label><br>                  
                                        <label name="gen_hosp">General Hospital (km)</label><br>                    
                                        <label name="fire_brig">Fire Brigade (km) : 20</label><br>   
                                    </div>

                                    <div class="col-xl-4">
                                        <label name="police_station">Police Station (km) : 25</label><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h4>Certificates</h4>
                    <div class="table-responsive ">
                        <table class="table">
                            <thead><br> 
                            <tr>
                                <th scope="col">Certificate Name</th>
                                <th scope="col">Certificate Number</th>  
                                <th scope="col">Certificate Expiry date</th>                                  
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Boat Yard Insurance Policy</td>
                                <td>sas154454</td>
                                <td>12/31/2022</td>
                            </tr>
                            
                            <tr>
                                <td>Boat Yard Insurance Policy</td>
                                <td>00001</td>
                                <td>12/31/2022</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
   
       
        <!-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <h4>Required Documents For Yard Registration</h4>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-4">
                                        <label>Access to boat yard plan</label><br>                             
                                        <button type="button" name="yard_plan" class="btn btn-primary">View Documents</button><br><br>

                                        <label>Physical Application Form </label><br>                             
                                        <button type="button" name="physical_app" class="btn btn-primary">View Documents</button><br><br>

                                        <label>Business certificate</label><br>                             
                                        <button type="button" name="business_cert" class="btn btn-primary">View Documents</button><br><br>

                                        <label>Environment certificate</label><br>                             
                                        <button type="button" name="environment_cert" class="btn btn-primary">View Documents</button><br><br>

                                        <label>Garbage disposal system plan</label><br>                             
                                        <button type="button" name="gar_sys_plan" class="btn btn-primary">View Documents</button><br><br>

                                    </div>

                                    <div class="col-xl-4">
                                        <label>Boat yard insurance policy</label><br>                             
                                        <button type="button" name="yard_insuarance_policy" class="btn btn-primary">View Documents</button><br><br>

                                        <label>Labour insurance policy</label><br>                             
                                        <button type="button" name="labour_insuarance_policy" class="btn btn-primary">View Documents</button><br><br>

                                        <label>Land ownership</label><br>                             
                                        <button type="button" name="land_ownership" class="btn btn-primary">View Documents</button><br><br>

                                        <label>Photograph of the boat yard</label><br>                             
                                        <button type="button" name="photograph_yard" class="btn btn-primary">View Documents</button><br><br>

                                        <label>Area under roof plan</label><br>                             
                                        <button type="button" name="area_roof_plan" class="btn btn-primary">View Documents</button><br><br>

                                    </div>

                                    <div class="col-xl-4">
                                        <label>Sanitary and sewage plan</label><br>                             
                                        <button type="button" name="sanitary_plan" class="btn btn-primary">View Documents</button><br><br>

                                        <label>Utility bill (Water bill)</label><br>                             
                                        <button type="button" name="water_bill" class="btn btn-primary">View Documents</button><br><br>

                                        <label>Utility bill (Electricity bill) </label><br>                             
                                        <button type="button" name="electri_bill" class="btn btn-primary">View Documents</button><br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <form action="">
                    <div class="row">
                        <table class="table" style="border: 1px solid #000;">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Officer Designation	</th>
                                    <th scope="col">Officer Name</th>
                                    <th scope="col">Submitted Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Remark</th>
                                   
                                    <th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">Yard Owner</th>
                                    <td>A.B.C Perera</td>
                                    <td>2022/12/16 10:00 AM	</td>
                                    <td>Requested</td>
                                    <td>N/A</td>
                                </tr>
                             
                            </tbody>
                        </table>
                    </div>

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="row">
                            <div class="col-xl-3">
                            </div>
                            <div class="col-xl-3">
                            </div>
                            <div class="col-xl-3"><br>
                                <div class="form-group">
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#RejectModel">Cancel</button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="RejectModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Remark</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <textarea rows="4" cols="55" id="Remark"></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="button" id="reject" class="btn btn-danger">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3"><br>
                                    <div class="form-group">
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#ApproveModel">Save</button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="ApproveModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Remark</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <textarea rows="4" cols="55" id="Remark"></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="button" id="approve" class="btn btn-primary">Save</button>
                                                </div>
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
</div>
