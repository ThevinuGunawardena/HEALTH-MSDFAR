<?php

/** @var yii\web\View $this */

$this->title = 'Export Registration Request';
?>
<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
  <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
  <a href = "ad-exapprovelicense" class="btn btn-outline-primary active">Export Application</a>


  <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
  <a href = "ad-exassigninspectorapprove" class="btn btn-outline-primary">Assign Inspectors</a>

  <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
  <a href = "ad-exinspectionreportnew" class="btn btn-outline-primary">Inspection Report</a>


  <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
  <a href = "ad-exsamplelicensenew" class="btn btn-outline-primary">Sample License</a>

  <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
  <a href = "ad-exadrecommendationnew" class="btn btn-outline-primary">AD Recommendation </a>

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
     <h6 class="text-muted">Telephone No:</h6>
       <input type="email" class="form-control" id="inputEmail4">
     </div>
     <div class="col-xl-6">
     <h6 class="text-muted">Fax No:</h6>
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
       <div class="col-xl-2">
        <h6 class="text-muted">Commercial Name :</h6>
          <div class="form-group">
                    <select class="form-control">
                    <option>Select Name</option>
                    <option>Live Rock</option>
                    </select>
                  </div>
        </div>
        <div class="col-xl-2">
        <h6 class="text-muted">Quantity per Unit :</h6>
          <input id="quUnit" type="number" name="password" class="form-control"  value="0" />
      
        </div>
        <div class="col-xl-2">
        <h6 class="text-muted">Total Weight :</h6>
          <input id="totWeight" type="number" name="password" class="form-control"  value="0" />
        </div>
        <div class="col-xl-2">
        <h6 class="text-muted">Total Number :</h6>
          <input id="totNo" type="number" name="password" class="form-control"  value="0" />
        </div>
        <div class="col-xl-2">
        <h6 class="text-muted">Collected Area :</h6>
        <div class="form-group">
                    <select class="form-control">
                    <option>Select District</option>
                    <option>Ampara</option>
                    <option>Anuradhapura</option>
                    <option>Badulla</option>
                    <option>Baticaloa</option>
                    <option>Colombo</option>
                    <option>Galle</option>
                    <option>Gampaha</option>
                    <option>Hambanthota</option>
                    <option>Jaffna</option>
                    <option>Kaluthara</option>
                    <option>Kandy</option>
                    <option>Galle</option>
                    <option>Kegalle</option>
                    <option>Kilinochchi</option>
                    <option>Kurunegala</option>
                    <option>Mannar</option>
                    <option>Mathale</option>
                    <option>Monaragala</option>
                    <option>Matara</option>
                    <option>Mulathivu</option>
                    <option>Nuwara Eliya</option>
                    <option>Polonnaruwa</option>
                    <option>Puththalama</option>
                    <option>Ratanapura</option>
                    <option>Tharincomalee</option>
                    <option>Vauniya</option>
                    </select>
                  </div>
        </div>
   </div>
   

<br>
<br>
    
              <div class="card-body">
                <table class="table table-dark">
                  <thead>
                    <tr>
                      <th scope="col"><b>Variety (Commercial Name)</b>	</th>
                      <th scope="col"><b>Size</b>	</th>
                      <th scope="col"><b>Weight</b></th>
                      <th scope="col"><b>Total Number</b></th>
                      <th scope="col"><b>Collected Area</b></th>
                     
                    </tr>
                  </thead>
                  </table>
              </div>
            
 <h6 class="text-muted">*Fees Charged shall be Rs10.00 per (kg)</h6>  
 <br>
 <div class="form-group">
       <h6 class="card-subtitle mb-2 text-muted">Charge to be paid :</h6>            
       <input type="number" class="form-control" id="address" rows="2"value="0" />
   </div>


   <div class="form-group">
       <h6 class="card-subtitle mb-2 text-muted">Country of Export :</h6>           
        <input type="text" class="form-control" id="address" rows="2">

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
       
    </div>         
    <div class="col-xl-1">
         <a href="#" class="btn btn-primary">View Document</a>
    </div>
    </div>           
   
 </div> </div></div></div></div>
 

 

