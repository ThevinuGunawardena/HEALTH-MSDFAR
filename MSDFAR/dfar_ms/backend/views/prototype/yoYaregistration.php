<?php

/** @var yii\web\View $this */

$this->title = 'Yard Registration';
?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Yard Registration</h3>
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
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>FM000001</td>
                                    <td>Negombo</td>
                                    <td>Pitipana</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group">
                        <label>Yard Name</label>
                        <input type="text" name="yard_name" class="form-control" /><br>

                        <label>Yard Address</label>
                        <input type="text" name="yard_address" class="form-control" />
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
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>Mobile Number</label>
                                            <input type="text" name="mobile_no" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>Land-Line Number</label>
                                            <input type="text" name="land_line" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <label>E-Mail</label>
                                <input type="text" name="email" class="form-control" /><br>

                                <label>Web </label>
                                <input type="text" name="web" class="form-control" /><br>

                                <label>Fax</label>
                                <input type="text" name="fax" class="form-control" /><br>
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
                                    <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Mechanized Inboard</span>
                                </label>

                                <label class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Mechanized Outboard</span>
                                </label>

                                <label class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Traditional Category</span>
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
                                <label>Business Registration Number</label>
                                <input type="text" name="br_no" class="form-control" /><br>

                                <label>Business Registration Date</label>
                                <input type="date" name="br_date" class="form-control">
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
                        <h4>Land & yard information</h4>
                        <div class="row">
                            <div class="col-xl-12">
                                <label>Boat Yard Land Ownership</label>
                                <input type="text" name="ya_ownership" class="form-control" /><br>

                                <label>Deed Number</label>
                                <input type="text" name="deed_no" class="form-control" /><br>

                                <label>Ownership Get Date</label>
                                <input type="date" name="ownership_date" class="form-control"><br>

                                <label>Land Area in Square meter (max 1000000 perch)</label>
                                <input type="text" name="area_suare_meter" class="form-control" /><br>

                                <label>Area Under Roof Square meter (max 1000000 perch)</label>
                                <input type="text" name="area_under_roof" class="form-control" /><br>

                                <label>Remark</label>
                                <input type="text" name="remarks" class="form-control" /><br>
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
                        <h4>Location Details</h4>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>Administrative District</label>
                                            <select name="admin_district" class="form-control">
                                                <option value="" selected hidden>Please Choose...</option>
                                                <option>Ampara</option>
                                                <option>Anuradhapura</option>
                                                <option>Badulla</option>
                                                <option>Batticaloa</option>
                                                <option>Colombo </option>
                                                <option>Galle</option>
                                                <option>Gampaha</option>
                                                <option>Hambantota</option>
                                                <option>Jaffna</option>
                                                <option>Kalutara</option>
                                                <option>Kandy</option>
                                                <option>Kegalle</option>
                                                <option>Kilinochchi</option>
                                                <option>Kurunegala</option>
                                                <option>Mannar</option>
                                                <option>Matale</option>
                                                <option>Matara</option>
                                                <option>Monaragala</option>
                                                <option>Mullaitivu</option>
                                                <option>Nuwara Eliya</option>
                                                <option>Polonnaruwa</option>
                                                <option>Puttalam</option>
                                                <option>Ratnapura</option>
                                                <option>Trincomalee</option>
                                                <option>Vavuniya</option>
                                            </select>

                                            <br><label>DS Division</label>
                                            <select name="ds_division" class="form-control">
                                                <option value="" selected hidden>Please Choose...</option>
                                                <option>Balapitiya DS Office</option>
                                                <option>Colombo DS Office</option>
                                            </select>

                                            <br><label>Fisheries District</label>
                                            <select name="fi_district" class="form-control">
                                                <option value="" selected hidden>Please Choose...</option>
                                                <option>Negombo</option>
                                                <option>Colombo</option>
                                            </select>

                                            <br><label>Fisheries Division</label>
                                            <select name="fi_division" class="form-control">
                                                <option value="" selected hidden>Please Choose...</option>
                                                <option>Pitipana</option>
                                                <option>Duwa</option>
                                            </select>

                                            <br><label>GPS Latitude</label>
                                            <input type="text" name="gps_latitude" class="form-control" />

                                            <br><label>GPS Longitude</label>
                                            <input type="text" name="gps_longitude " class="form-control" /><br>
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
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h4>Yard Safety Information</h4>
                    <div class="input-group-append">
                        <button type="button" name="yard_safety" class="btn btn-primary">Add Details</button>
                    </div>
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
                    <label>Transporting Methods</label><br>
                    <select name="tranportation" class="form-control">
                        <option value="" selected hidden>Please Choose...</option>
                        <option>Select Transporting Method</option>
                    </select> <br>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h4>Yard Medicine Type</h4>
                    <div class="input-group-append">
                        <button type="button" name="yard_medicine" class="btn btn-primary">Add Details</button>
                    </div>

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
                    <h4>Distance To Nearest Hospital</h4>
                    <label>Rural Hospital (km)</label><br>
                    <input type="number" name="rural_hosp" step="0.001" class="form-control" /><br>

                    <label>District Hospital (km)</label><br>
                    <input type="number" name="dist_hosp" step="0.001" class="form-control" /><br>

                    <label>Base Hospital (km)</label><br>
                    <input type="number" name="base_hosp" step="0.001" class="form-control" /><br>

                    <label>Teaching Hospital (km)</label><br>
                    <input type="number" name="teach_hosp" step="0.001" class="form-control" /><br>

                    <label>General Hospital (km)</label><br>
                    <input type="number" name="gen_hosp" step="0.001" class="form-control" /><br>

                    <label>Fire Brigade (km)</label><br>
                    <input type="number" name="fire_brig" step="0.001" class="form-control" /><br>

                    <label>Police Station (km)</label><br>
                    <input type="number" name="police_station" step="0.001" class="form-control" /><br>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h4>Certificates</h4>
                    <div class="input-group-append">
                        <button type="button" name="certificate" class="btn btn-primary">Add Details</button>
                    </div>

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


        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h4>Required Documents For Yard Registration</h4><br>
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
                            <label>Access to boat yard plan</label><br>
                            <button type="button" name="yard_plan" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Physical Application Form </label><br>
                            <button type="button" name="physical_app" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Business certificate</label><br>
                            <button type="button" name="business_cert" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Environment certificate</label><br>
                            <button type="button" name="environment_cert" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Garbage disposal system plan</label><br>
                            <button type="button" name="gar_sys_plan" class="btn btn-primary">Upload Documents</button><br><br>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
                            <label>Boat yard insurance policy</label><br>
                            <button type="button" name="yard_insuarance_policy" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Labour insurance policy</label><br>
                            <button type="button" name="labour_insuarance_policy" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Land ownership</label><br>
                            <button type="button" name="land_ownership" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Photograph of the boat yard</label><br>
                            <button type="button" name="photograph_yard" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Area under roof plan</label><br>
                            <button type="button" name="area_roof_plan" class="btn btn-primary">Upload Documents</button><br><br>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4">
                            <label>Sanitary and sewage plan</label><br>
                            <button type="button" name="sanitary_plan" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Utility bill (Water bill)</label><br>
                            <button type="button" name="water_bill" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Utility bill (Electricity bill) </label><br>
                            <button type="button" name="electri_bill" class="btn btn-primary">Upload Documents</button><br><br>

                            <label>Other Documents </label><br>
                            <button type="button" name="other_doc" class="btn btn-primary">Upload Documents</button><br><br>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <button type="button" name="cancel" class="btn btn-warning">Cancel</button>
                    <button type="button" name="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>

    </div>
</div>