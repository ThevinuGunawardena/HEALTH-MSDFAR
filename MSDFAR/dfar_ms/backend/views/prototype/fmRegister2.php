<?php

/** @var yii\web\View $this */

$this->title = 'Fisherman Registration';
?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
       
    </div>
</div>
<div class="row">
    <div class="col-xl-8 col-lg-12 col-md-12">
        <div class="row">
            <div class="col-lg-12">
                
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
                                                <label for="FI_Div">For Associate Occupational Activities</label>
                                                <select class="form-control">
                                                    <option>One Day</option>
                                                    <option>Multi Day</option>
                                                   
                                                </select>
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
                                                <label for="FI_Div">Highest Educatinal Qualification</label>
                                                <select class="form-control">
                                                    <option>A/L</option>
                                                    <option>O/L</option>
													<option>Grade 8</option>
                                                   
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
                                        <div class="col-xl-3"><br><br>
                                            <div class="form-group">
                                                <a href="fm-register" class="btn btn-primary btn-block"><i class="fa fa-arrow-left" aria-hidden="true"></i>
                                                    Back
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-xl-3"><br><br>
                                            <div class="form-group">
                                                <a href="fm-index" class="btn btn-primary btn-block">
                                                    Submit<i class="fa fa-arrow-right" aria-hidden="true"></i>
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
                           
                        </div>


                    </div>
                </div>
            </div> <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.3.5/signature_pad.min.js" integrity="sha512-kw/nRM/BMR2XGArXnOoxKOO5VBHLdITAW00aG8qK4zBzcLVZ4nzg7/oYCaoiwc8U9zrnsO9UHqpyljJ8+iqYiQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
       var canvas = document.getElementById("signature-pad");

       function resizeCanvas() {
           var ratio = Math.max(window.devicePixelRatio || 1, 1);
           canvas.width = canvas.offsetWidth * ratio;
           canvas.height = canvas.offsetHeight * ratio;
           canvas.getContext("2d").scale(ratio, ratio);
       }
       window.onresize = resizeCanvas;
       resizeCanvas();

       var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(250,250,250)'
       });

       document.getElementById("clear").addEventListener('click', function(){
        signaturePad.clear();
       })
   </script>
        </div>
    </div>
</div>