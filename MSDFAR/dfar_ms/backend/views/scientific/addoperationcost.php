<?php

/** @var yii\web\View $this */

$this->title = 'Scientific Data';
$this->render('_js_config');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
<div class="row operational-cost">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Operational Cost : <span id="craft-number"></span></h3>
        </div>
    </div>


    <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-12">
                        <h3>Fuel (L)</h3>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" min="0" id="fuel_qty" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="number" min="0" id="fuel_val" data-validation="NUMBER"
                                           class="form-control"/>
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
                                    <input type="number" min="0" id="ice_qty" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="number" min="0" id="ice_val" data-validation="NUMBER"
                                           class="form-control"/>
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
                                    <input type="number" min="0" id="bait_qty" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="number" min="0" id="bait_val" data-validation="NUMBER"
                                           class="form-control"/>
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
                                    <input type="number" min="0" id="salt_qty" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="number" min="0" id="salt_val" data-validation="NUMBER"
                                           class="form-control"/>
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
                                    <input type="number" min="0" id="labour_cost" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Food & Drinking Water (LKR)</label>
                                    <input type="number" min="0" id="food_water" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Others (LKR)</label>
                                    <input type="number" min="0" id="others" data-validation="NUMBER"
                                           class="form-control"/>
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
                <hr>

                <div class="row">
                    <div class="col-xl-12">
                        <h3>Utilized for,</h3>

                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label>Species Name</label>
                                    <select id="species_code" data-validation="EMPTY" class="form-control">

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <label class="available-qty" style="display: none">Available Quantity : <strong><span
                                                class="qty-num"></span></strong> </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Export</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Quantity (kg)</label>
                                    <input type="number" min="0" id="export_qty" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="number" min="0" id="export_val" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Local Market</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Quantity (kg)</label>
                                    <input type="number" min="0" id="local_qty" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="number" min="0" id="local_val" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Dried Fish</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Quantity (kg)</label>
                                    <input type="number" min="0" id="dry_qty" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="number" min="0" id="dry_val" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Discards</label>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Quantity (kg)</label>
                                    <input type="number" min="0" id="discard_qty" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Value (LKR)</label>
                                    <input type="number" min="0" id="discard_val" data-validation="NUMBER"
                                           class="form-control"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" id="trash" class="custom-control-input"><span
                                            class="custom-control-label">Trash</span>
                                </label>
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
                            <th scope="col">Species Name</th>
                            <th scope="col">Export</th>
                            <th scope="col">Local Market</th>
                            <th scope="col">Discards</th>
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
    <div class="col-xl-10 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="row">

            <div class="col-xl-6">
                <div class="form-group">
                    <a href="<?= \yii\helpers\Url::to(['/scientific/create']) ?>"
                       class="btn btn-outline-primary btn-block">Back</a>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="form-group">
                    <a href="<?= \yii\helpers\Url::to(['/scientific/create']) ?>" onclick="return addOperatoinCost()" class="btn btn-success btn-block">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>

