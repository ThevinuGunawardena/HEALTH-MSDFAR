<?php

/** @var yii\web\View $this */

$this->title = 'Boat Design';
?>

<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="section-block" id="cards">
        <h3 class="card-title">Boat Design</h3>
    </div>

  </div>
  
  <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card mb-5 shadow-sm">
      <div class="card-body">

       

        <label>Boat Yard No.</label><br>
        <div class="input-group mb-3">                       
          <input type="text" name="yard_number" class="form-control">
          <div class="input-group-append">
            <button type="button" class="btn btn-primary">Search</button>
          </div>
        </div> 

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

        <label>Fisheries District</label><br>
        <select name="fi_disttrict" class="form-control">
          <option value=""  selected hidden>Please Choose...</option>
          <option>Negombo</option>
          <option>Trincomalee</option>
          <option>Galle</option>
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
    <div class="card mb-5 shadow-sm">
        <div class="card-body">
            <button type="button" class="btn btn-warning">Cancel</button>
            <button type="button" class="btn btn-primary">Submit</button>
        </div>
    </div>
  </div>

</div>

