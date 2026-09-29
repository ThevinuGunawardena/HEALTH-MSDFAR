<?php

/** @var yii\web\View $this */

$this->title = 'Export Registration Request';
?>
<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
  <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
  <a href = "ad-exapprovelicense" class="btn btn-outline-primary">Export Application</a>


  <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
  <a href = "ad-exassigninspectorapprove" class="btn btn-outline-primary">Assign Inspectors</a>

  <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
  <a href = "ad-exinspectionreportnew" class="btn btn-outline-primary">Inspection Report</a>


  <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
  <a href = "ad-exsamplelicensenew" class="btn btn-outline-primary">Sample License</a>

  <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
  <a href = "ad-exadrecommendationnew" class="btn btn-outline-primary active">AD Recommendation </a>

</div>
<br>
<br>
<div class="row">
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="section-block" id="cards">
                  <br>
                <h3 class="card-subtitle mb-2 text-muted">Review On Inspection Report & Sample License :</h3>    
        
<br>
            <div class="form-group" >
            <textarea  class="form-control" name="address" rows="4" cols="30" style="border: 2px solid #000" ></textarea>
  </div>
  <div class = "row">
   <div class="col-xl-8"></div>            
    <div class="col-xl-1">
         <a href="#" class="btn btn-primary">Reject</a>
    </div>
    <div class="col-xl-1">
         <a href="#" class="btn btn-primary">Return</a>
    </div>
    <div class="col-xl-1">
         <a href="#" class="btn btn-primary">Approve</a>
    </div>
    </div>   
      
</div>
 

    </div>           
   
 </div> 
 

 