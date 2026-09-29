<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
?>

<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">           
            <h3 class="card-title">Length & Weight Data : IMULA0008NBO</h3>
        </div>
    </div>     
    
    
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <h4>Fish Length Weight Details</h4>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label>Specie Name</label>
                                            <select name="species_code" class="form-control">
                                                <option>Big Eye Tuna Loins</option>
                                                <option>Barracuda</option>
                                                <option>Barramundi</option>
                                            </select><br>

                                            <label>Gear Used</label><br>
                                            <select name="gear_used2" class="form-control">
                                                <option>Gill net- Small mesh gill net</option>
                                                <option>Gill net- Large mesh gill net/ Ring net</option>
                                            </select><br>

                                            <label>No. of Sampled Specie</label>
                                            <input type="text" name="no_of_sample" class="form-control"/><br>

                                            <label>Weight Code</label><br>
                                            <select name="weight_code2" class="form-control">
                                                <option>Dry Weght</option>
                                                <option>Gilled</option>
                                                <option>Headed</option>
                                                <option>Fish Loins</option>
                                            </select><br>

                                            <label>Weight (kg)</label>
                                            <input type="text" name="weight2" class="form-control"/><br>

                                            <label>Length Type</label><br>
                                            <label class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">Curve</span>
                                            </label>
                                                                    
                                            <label class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">Stright</span>
                                            </label><br>

                                            <label>Length Code</label><br>
                                            <select name="length_code" class="form-control">
                                                <option>SL</option>
                                                <option>CF</option>
                                                <option>CK</option>
                                                <option>EF</option>
                                                <option>DF</option>
                                                <option>TL</option>
                                                <option>SF</option>
                                            </select><br>

                                            <label>Length (cm)</label>
                                            <input type="text" name="length" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            
                                        </div>
                                    </div>
                                    <button type="button" name="add_catch" class="btn btn-primary btn-block">Add Fish Lenght Weight Details</button>
                                    <div class="table-responsive ">
                                        <table class="table">
                                            <thead><br>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Species Code</th>                                                    
                                                    <th scope="col">Weight (kg)</th>
                                                    <th scope="col">Length (cm)</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Yellow Fin Tuna</td>
                                                    <td>80</td>
                                                    <td>1500</td>
                                                    <td><a href="#" class="btn btn-danger">Remove</a></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Blue Marlin</td>
                                                    <td>100</td>
                                                    <td>500</td>
                                                    <td><a href="#" class="btn btn-danger">Remove</a></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        
                                    </div>
                                </div>
                                
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
            <div class="col-xl-3">
            </div>
            <div class="col-xl-3">
            </div>
            <div class="col-xl-3">
                <div class="form-group">
                                     
                </div>
            </div>
            <div class="col-xl-3">
                <div class="form-group">
                    <br> <a href="fi-scientific" class="btn btn-primary btn-block">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>

