<?php

/** @var yii\web\View $this */

$this->title = 'Re-Export Registration Request';
?>
<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
  <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
  <a href = "re-application" class="btn btn-outline-primary">Re-Export Application</a>


  <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
  <a href = "re-assigninspector" class="btn btn-outline-primary">Assign Inspectors</a>

  
  <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
  <a href = "re-inspectionreport" class="btn btn-outline-primary">Inspection Report</a>

  
  <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
  <a href = "re-samplelicense" class="btn btn-outline-primary">Sample License</a>

  
  <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
  <a href = "re-dmrecommendation" class="btn btn-outline-primary">DM Reccomendation</a>

  
  <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
  <a href = "re-dgrecommendation" class="btn btn-outline-primary">DG Reccomendation</a>


</div>

<br>
<br>
<br>






<div class="row">
              <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="section-block" id="cards">
                  
        
 <div class="card mb-5 shadow-sm">
      <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Applicants / Institution's Full Name :</h6>
        <div class="form-group">
                    <input type="text" class="form-control" id="name">
<br>

       <div class="form-group">
       <h6 class="card-subtitle mb-2 text-muted">Address :</h6>
                    
                    <textarea  class="form-control" name="address" rows="4" cols="30"></textarea>

                  </div>
       
</div>
 
<div class="row">
<div class="col-xl-6">
     <h6 class="text-muted">Mobile No:</h6>
       <input type="email" class="form-control" id="inputEmail4">
     </div>
     <div class="col-xl-6">
     <h6 class="text-muted">Land No:</h6>
       <input type="password" class="form-control" id="inputPassword4">
     </div>
     
</div> 
<br>
<div class="row">
    <div class="col-xl-6">
     <h6 class="text-muted">E-mail :</h6>
       <input type="email" class="form-control" id="inputEmail4">
     </div>
     <div class="col-xl-6">
     <h6 class="text-muted">Business Registration No :</h6>
       <input type="password" class="form-control" id="inputregNo">
     </div>
    </div>

    <br>
 <div class="form-group">
        <h6 class="card-subtitle mb-2 text-muted">Permit Type :</h6>
        <div class="form-group">
                    <select class="form-control">
                    <option>Select Permit Type</option>
                    <option>Bache-de-mer</option>
                    <option>Bache-de-mer(Pawakka)</option>
                    <option>Chank</option>
                    <option>Lobster</option>
                    <option>Live Fish</option>
                    <option>Live Rock</option>
                    </select>
                  </div>
      </div>

      <h6 class="text-muted">Details of consignment :</h6>
      <br>
  
    <div class="row">
       <div class="col-xl-4">
        <h6 class="text-muted">Commercial Name :</h6>
          <div class="form-group">
                    <select class="form-control">
                    <option>Select Name</option>
                   
                    </select>
                  </div>
        </div>
        
        <div class="col-xl-4">
        <h6 class="text-muted">Total Weight :</h6>
          <input id="totWeight" type="number" name="password" class="form-control"  value="0" />
        </div>
        <div class="col-xl-4">
        <h6 class="text-muted">Imported Country :</h6>
          <input id="totNo" type="text" name="password" class="form-control"  >
        </div>
        
        
   </div>
 

<br>
<br>
    
              <div class="card-body">
                <table class="table table-dark">
                  <thead>
                    <tr>
                    <th scope="col"><b>Commercial Name</b>	</th>
                      <th scope="col"><b>Quantity Imported</b>	</th>
                      <th scope="col"><b>Imported Country</b></th>
                     
                    </tr>
                  </thead>
                  </table>
              </div>
            
 
 <br>
 <div class="form-group">
       <h6 class="card-subtitle mb-2 text-muted">Charge to be paid :</h6>            
       <input type="number" class="form-control" id="address" rows="2"value="0" />
   </div>

   <div class="form-group">
       <h6 class="card-subtitle mb-2 text-muted">Additional Information :</h6>           
       <textarea  class="form-control" name="addinfor" rows="4" cols="30"></textarea>

   </div>

   <h6 class="text-muted">Upload Document*</h6>  
 <br>

 <div class="row">
                <div class="col-xl-12">
                  <div class="form-group">
                  <a href="#" class="btn btn-primary"><input type="file"></a>
                   
                  </div>
                </div>
                
              </div>
 
              <div class = "row">
   <div class="col-xl-9"></div>            
    <div class="col-xl-1">
         <a href="#" class="btn btn-primary">View Document</a>
    </div>
    </div>           
   
 </div> </div></div></div></div>
 

 

