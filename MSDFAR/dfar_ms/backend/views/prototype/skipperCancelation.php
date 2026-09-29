<?php
/** @var yii\web\View $this */

$this->title = 'Skipper Cancellations';
?>



            <form action="">
              <div class="row">
                  <div class="col-xl-12">
                      <div class="row">
                          <div class="col-xl-6">
                              <div class="form-group">
                                  <label for="Fish_S_Name">Skipper Number</label>
                                  <select class="form-control">
                                                    <option>FM0008KLT</option>
                                                    <option>FM0009KLT</option>
                                                    <option>FM0008KLT</option>
                                                    <option>FM0008KLT</option>
                                                    <option>FM0008KLT</option>
                                                   
                                                </select>                                   </div>
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
                                  <label for="Fish_S_Name">Skipper Name</label>
                                  <input type="text" class="form-control" readonly>
                               </div>
                          </div>
                          <div class="col-xl-6">
                          <div class="form-group">
                                  <label for="Fish_S_Name">NIC</label>
                                  <input type="text" class="form-control" readonly>
                         
                               </div>       
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-xl-6">
                              <div class="form-group">
                                  <label for="Fish_S_Name">Choose Status</label>
                                  <select class="form-control">
                                                    <option>Cancel Skipper</option>
                                                    <option>Re Activate Skipper</option>
                                                   
                                                </select>                              
                               </div>
                          </div>
                          <div class="col-xl-6">
                          <div class="form-group">
                          <label for="Fish_S_Name">Choose Reasons</label>
                                  <select class="form-control">
                                                    <option>VMS technical issues</option>
                                                    <option>Act violation investigation</option>
                                                    <option>Human Smuggling</option>
                                                    <option>Drug trafficking</option>
                                                    <option>Due to none copliance of fisheries law</option>
                                                    <option>Due to none payments of VMS charges</option>
                                                    <option>Investigation related to VMS</option>
                                                </select>
                               </div>       
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-xl-6">
                              <div class="form-group">
                              <label for="Fish_S_Name">Remark</label>
                                  <div class="">
                                        <textarea rows="4" cols="55" id="Remark"></textarea>
</div>                             
                               </div>
                          </div>
                          <div class="col-xl-6">
                          <div class="form-group">
                                 
                               </div>       
                          </div>
                      </div>


                      <div class="row">
                          <div class="col-xl-6">
                              <div class="form-group">
                         
                               </div>
                          </div>
                          <div class="col-xl-4"></div>
                          <div class="col-xl-2">
                          <button type="button" class="btn btn-success" data-dismiss="modal">Submit</button>                          </div>
                      </div>
                    </div>
                </div>
              </form>