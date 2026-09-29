<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
?>

<div class="row operational-cost">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Operational Cost : <span id="craft-number"></span> </h3>
        </div>
    </div>


    <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-12">
                        <h3>Fuel (L)</h3>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="text" id="fuel_qty"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="text" id="fuel_val"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="row">
                    <div class="col-xl-12">
                        <h3>Ice (honders)</h3>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="text" id="ice_qty"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="text" id="ice_val"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xl-12">
                        <h3>Bait (kg)</h3>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="text" id="bait_qty"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="text" id="bait_val"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xl-12">
                        <h3>Salt (kg)</h3>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="text" id="salt_qty"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="text" id="salt_val"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Labour Cost (LKR) (Crew, Load, Unload)</label>
                                    <input type="text" id="labour_cost"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Food & Drinking Water (LKR)</label>
                                    <input type="text" id="food_water"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Others (LKR)</label>
                                    <input type="text" id="others"  data-validation="EMPTY_NUMBER"  class="form-control"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="form-group">
                            <label>Remarks</label>
                            <input type="text" id="remarks" class="form-control"/>

                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xl-12">
                        <h3>Utilized for,</h3>

                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label>Species Name</label>
                                    <select id="species_code" data-validation="EMPTY" class="form-control">
                                        <option>Big Eye Tuna Loins</option>
                                        <option>Barracuda</option>
                                        <option>Barramundi</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4">
                                <label>Export</label>
                            </div>
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label>Quantity (kg)</label>
                                    <input type="text" id="export_qty" data-validation="EMPTY_NUMBER" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="text" id="export_val"  data-validation="EMPTY_NUMBER" class="form-control"/>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-xl-4">
                                <label>Local Market</label>
                            </div>
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label>Quantity (kg)</label>
                                    <input type="text" id="local_qty"  data-validation="EMPTY_NUMBER" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="text" id="local_val"  data-validation="EMPTY_NUMBER" class="form-control"/>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-xl-4">
                                <label>Dried Fish</label>
                            </div>
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label>Quantity (kg)</label>
                                    <input type="text" id="dry_qty"  data-validation="EMPTY_NUMBER" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="text" id="dry_val"  data-validation="EMPTY_NUMBER" class="form-control"/>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-xl-4">
                                <label>Discards</label>
                                <div class="form-group">
                                <label class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" id="trash" class="custom-control-input"><span
                                            class="custom-control-label">Trash</span>
                                </label>
                                </div>

                            </div>
                            <div class="col-xl-4">
                                <div class="form-group">
                                <label>Quantity (kg)</label>
                                <input type="text" id="discard_qty"  data-validation="EMPTY_NUMBER" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <div class="form-group">
                                <label>Value (LKR)</label>
                                <input type="text" id="discard_val"  data-validation="EMPTY_NUMBER" class="form-control"/>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>



                <div class="row">
                    <div class="col-xl-12">

                        <button type="button" id="add_catch" class="btn btn-primary btn-block">Add Fish Catch Details
                        </button>
                    </div>
                </div>


                <div class="table-responsive ">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Species Code</th>
                            <th scope="col">No. of Species</th>
                            <th scope="col">Weight (kg)</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody class="catch-table">

                        </tbody>
                    </table>
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
                     <a href="fi-scientific" onclick="return addOperatoinCost()" class="btn btn-primary btn-block">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>

