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
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="col-xl-6">                               
                                <label name="gear_used">Used Gear : Gill net- Small mesh gill net</label><br>
                                <label name="no_species_fish">No. of species/ Fish : 2</label><br>

                                <label name="weight_code">Weight Code : Gilled</label><br>
                                <label name="weight">Weight (kg) : 800</label>
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
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <div class="form-group">
                        <h4>Fish Length Weight Details</h4>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            <label name="species_code">Specie Name : Big Eye Tuna Loins</label>
                                            <label name="gear_used2">Gear Used : Gill net- Small mesh gill net</label><br>
                                            <label name="no_of_sample">No. of Sampled Specie : 2</label><br>
                                            <label name="weight_code2">Weight Code : Gilled</label><br>
                                            <label name="weight2">Weight (kg) : 1000</label><br><br>

                                            <label>Length Type</label><br>
                                            <label class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" name="radio-inline" onclick="return false;" class="custom-control-input"><span class="custom-control-label">Curve</span>
                                            </label>
                                                                    
                                            <label class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" name="radio-inline" onclick="return false;" class="custom-control-input"><span class="custom-control-label">Stright</span>
                                            </label><br><br>

                                            <label name="length_code">Length Code : SL</label><br>                                           
                                            <label name="length">Length (cm) : 50</label>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-group">
                                            
                                        </div>
                                    </div>
                                    <div class="table-responsive ">
                                        <table class="table">
                                            <thead><br>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Species Code</th>                                                    
                                                    <th scope="col">Weight (kg)</th>
                                                    <th scope="col">Length (cm)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Yellow Fin Tuna</td>
                                                    <td>80</td>
                                                    <td>1500</td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Blue Marlin</td>
                                                    <td>100</td>
                                                    <td>500</td>
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
                    <br> <a href="fi-scientific" class="btn btn-primary btn-block">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

