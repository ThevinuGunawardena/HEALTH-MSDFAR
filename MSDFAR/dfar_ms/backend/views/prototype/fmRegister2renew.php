<?php

/** @var yii\web\View $this */

$this->title = 'Fisherman Registration Renewal Page 2';
?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Fisherman Registration</h3>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-8 col-lg-12 col-md-12">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Fisheries Details</h6>
                        <form action="">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="row">
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label for="FI_District">Fisheries Zone</label>
                                                <select class="form-control">
												<option>High Seas</option>
												<option>EZD</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label for="FI_Div">Occupation</label>
                                                <select class="form-control">
                                                    <option>Fisherman</option>
                                                    <option>Crew Member</option>
                                                    <option>Boat Owner</option>
													<option>Skipper</option>
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
                                                <label for="GS_Division">Year of Recruitment</label>
                                                <input type="text" class="form-control">
                                            </div>
                                        </div>

								<div class="col-xl-6">

									<div class="form-group">
										<label for="TypeBoats">Types of Boats</label>
										<div class="col-xl-6">
											<div class="row">
											<div class="custom-control custom-checkbox">
											<label class="custom-control custom-checkbox custom-control-inline">
												<input type="checkbox" class="custom-control-input"><span class="custom-control-label">IMUL
												</span>
											</label>

											<label class="custom-control custom-checkbox custom-control-inline">
												<input type="checkbox" class="custom-control-input"><span class="custom-control-label">IDAY</span>
											</label>
											<label class="custom-control custom-checkbox custom-control-inline">
												<input type="checkbox" class="custom-control-input"><span class="custom-control-label">MTRB</span>
											</label>
										</div>
											</div>
										
										</div>
										<div class="col-xl-6">
											<div class="row">
											<div class="custom-control custom-checkbox">
											<label class="custom-control custom-checkbox custom-control-inline">
												<input type="checkbox" class="custom-control-input"><span class="custom-control-label">OFRP</span>
											</label>

											<label class="custom-control custom-checkbox custom-control-inline">
												<input type="checkbox" class="custom-control-input"><span class="custom-control-label">NTRB</span>
											</label>
											<label class="custom-control custom-checkbox custom-control-inline">
												<input type="checkbox" class="custom-control-input"><span class="custom-control-label">NBSB</span>
											</label>
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
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <form action="">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="row">
									<div class="col-xl-6">
                                            <div class="form-group">
                                                <label for="FI_Div">Nature of Occupation</label>
                                                <select class="form-control">
                                                    <option>Fisherman</option>
                                                    <option>Crew Member</option>
                                                    <option>Boat Owner</option>
													<option>Skipper</option>
                                                </select>
                                            </div>
                                        </div>
										<div class="col-xl-6">
                                            <div class="form-group">
                                                <label for="FI_Div">Nature Of Fishing Operations</label>
                                                <select class="form-control">
                                                    <option>One Day</option>
                                                    <option>Multi Day</option>
                                                   
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
                                                <label for="FI_Div">For Associate Occupational Activities</label>
                                                <select class="form-control">
                                                    <option>One Day</option>
                                                    <option>Multi Day</option>
                                                   
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="form-group">
                                                <label for="NIC">Life Insurance No</label>
                                                <input type="text" class="form-control" id="NIC">
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
                                                <label for="PastP_No">Donation Of Last Five Years</label>
                                                <input type="text" class="form-control" id="PastP_No">
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
										<div class="form-group">
                                                <label for="FI_Div">Nature of House Hold</label>
                                                <select class="form-control">
                                                    <option>One Day</option>
                                                    <option>Multi Day</option>
                                                   
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
                                            <label for="Gender">Membership of Fisheries Society</label>
                                            <div>
                                                <label class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">Yes</span>
                                                </label>
                                            </div>
                                            <div>
                                                <label class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">No</span>
                                                </label>
                                            </div>

                                        </div>
										<div class="col-xl-6">
                                            <label for="Gender">If any government subsidies acquired</label>
                                            <div>
                                                <label class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">Yes</span>
                                                </label>
                                            </div>
                                            <div>
                                                <label class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">No</span>
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="row">
									<div class="col-xl-6">
                                            <label for="Gender">Civil Status</label>
                                            <div>
                                                <label class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">Married</span>
                                                </label>
                                            </div>
                                            <div>
                                                <label class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" name="radio-inline" class="custom-control-input"><span class="custom-control-label">Un Married</span>
                                                </label>
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
                                                <label for="Cur_Addr">Annual Income</label>
                                                <input type="text" class="form-control" id="Cur_Addr">
                                            </div>
                                        </div>
										<div class="col-xl-6">
                                            <div class="form-group">
                                                <label for="Cur_Addr">Account Number</label>
                                                <input type="text" class="form-control" id="Cur_Addr">
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
                                                <label for="Cur_Addr">Name of the Bank/ Branch</label>
                                                <input type="text" class="form-control" id="Cur_Addr">
                                            </div>
                                        </div>
										<div class="col-xl-6">
										<div class="form-group">
                                                
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
                                                <label for="FI_Div">Highest Educatinal Qualification</label>
                                                <select class="form-control">
                                                    <option>A/L</option>
                                                    <option>O/L</option>
													<option>Grade 8</option>
                                                   
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
							<div class="row">
                                <div class="col-xl-12">
                                    <div class="row">
                                        <div class="col-xl-6">
										<div class="form-group">
										<label class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input"><span class="custom-control-label">Medical Certificate Submitted</span>
                      </label>
                                            </div>
                                        </div>
                                        <div class="col-xl-6">
                                            
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
                                                <a href="fm-registerrenew" class="btn btn-primary btn-block"><i class="fa fa-arrow-left" aria-hidden="true"></i>
                                                    Back
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-xl-3">
                                            <div class="form-group">
                                                <a href="fm-register3renew" class="btn btn-primary btn-block">
                                                    Next<i class="fa fa-arrow-right" aria-hidden="true"></i>
                                                </a>
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
    <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Profile Image</h6>
                        <div style="text-align:center;">
                            <img id="img-upload">
                            <div id="tem_img">
                                <img src="https://www.pngitem.com/pimgs/m/4-42408_vector-art-design-men-fashion-vector-art-illustration.png" width="150px" height="150px" style="border-radius: 50%;">
                            </div>
                            <br><br>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <span class="btn btn-info btn-file">
                                        Choose Image (4x6) <input type="file" id="ProImg_File" name="ProImg_File">
                                    </span>
                                </span>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>