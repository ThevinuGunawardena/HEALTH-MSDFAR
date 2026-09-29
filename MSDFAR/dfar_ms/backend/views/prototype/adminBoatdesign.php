<?php

/** @var yii\web\View $this */

$this->title = 'Boat Design';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="section-title"><?= Yii::t('app', 'Welcome to the fisheries department online portal') ?></h3></br>
        <h3 class="card-title">Boat Design Request</h3>
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">

       

        <label>Yard Name</label><br>
        <select name="yard_name" class="form-control">
          <option value=""  selected hidden>Please Choose...</option>
          <option>Test Yard1</option>
          <option>Test Yard2</option>
          <option>Test Yard3</option>
          <option>Test Yard4</option>
        </select> <br>

        <label>Status</label><br>
        <select name="yard_name" class="form-control">
          <option value=""  selected hidden>Please Choose...</option>
          <option>Permanent</option>
          <option>Temporary</option>       
        </select> <br>


        <label>Boat Category</label><br>
        <select name="Hull_Meterial" class="form-control">
          <option value=""  selected hidden>Please Choose...</option>
          <option>IMUL</option>
          <option>OFRP</option>
          <option>MTRB</option>
          <option>NTRB</option>
        </select> <br>
        
       
        <label>Hull Meterial</label><br>
        <select name="hull_meterial" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Fiber</option>
            <option>Steel</option>
            <option>Timber</option>
        </select> <br> 

        <label>Engine Type</label>
        <select name="engine_type" class="form-control">
            <option value=""  selected hidden>Please Choose...</option>
            <option>Inboard Motor</option>
            <option>Outboard Motor</option>
            <option>Oars</option>
            <option>Sail</option>
        </select> <br> 

       
        <label>Boat Design Notation</label><br>
        <input type="text" name="design_notation" class="form-control"/><br>

        <label>Length (Meter)</label><br>
        <input type="text" name="length" class="form-control"/><br>

        <label>Width (Meter)</label><br>
        <input type="text" name="width" class="form-control"/><br>

        <label>Height (Meter)</label><br>
        <input type="text" name="height" class="form-control"/><br>

        <label>Draft (Meter)</label><br>
        <input type="number" name="draft" min="0" step="0.001" max="99" class="form-control"/><br>

        <label>Remark</label><br>
        <input type="text" name="remark" class="form-control"/><br>              
     </div>
    </div>
  </div>


  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Boat Design Images</h4>
        <div class="input-group mb-3">                        
                         
            <div class="input-group-append">
                <button type="button" class="btn btn-primary">Upload Images</button>
            </div>
        </div>     
      </div>
    </div>  
  </div>


  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h4>Required Documents For Boat Design</h4>
        <div class="input-group mb-3">                        
                         
            <div class="input-group-append">
                <button type="button" class="btn btn-primary">Upload Documents</button>
            </div>
        </div>     
      </div>
    </div> 

    
    
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
                <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#RejectModel">Reject</button>
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
                                <button type="button" id="reject" class="btn btn-danger">Reject</button>
                            </div>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3"><br>
                <div class="form-group">
                  <!-- Button trigger modal -->
                  <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#ApproveModel">Approve</button>
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
                                  <button type="button" id="approve" class="btn btn-primary">Approve</button>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
            </div>
      </div>  
  </div>

</div>

