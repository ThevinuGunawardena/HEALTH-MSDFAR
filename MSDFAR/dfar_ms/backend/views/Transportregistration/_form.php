<?php

/** @var yii\web\View $this */

$this->title = 'Transport Registration Request';
?>
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
     <h6 class="text-muted">E-mail :</h6>
       <input type="password" class="form-control" id="inputPassword4">
     </div>
     
</div> 
<br>
<div class="row">
    <div class="col-xl-6">
     <h6 class="text-muted">National Identity Card Number :</h6>
       <input type="email" class="form-control" id="inputEmail4">
     </div>
     <div class="col-xl-6">
     <h6 class="text-muted">Business Registration No:</h6>
       <input type="password" class="form-control" id="inputregNo">
     </div>
    </div>

    <br>
    <div class="form-group">
       <h6 class="card-subtitle mb-2 text-muted">Mailing Address :</h6>
                    
                    <textarea  class="form-control" name="address" rows="4" cols="30"></textarea>

                  </div>
                  <br>
 <div class="form-group">
        <h6 class="card-subtitle mb-2 text-muted">Permit Type :</h6>
        <div class="form-group">
                    <select class="form-control">
                    <option>Select Permit Type</option>
                    <option>Bache-de-mer(Pawakka)</option>
                    <option>Bache-de-mer</option>
                    <option>Chank</option>
                    <option>Dead Shelld(Animal Feed)</option>
                    <option>Dead Shelld(Ornamental)</option>
                    <option>Lobster</option>
                    <option>Live Fish</option>
                    </select>
                  </div>
      </div>

      <h6 class="text-muted">Places of purchase, Quantity and Vehicle Number :</h6>
      <br>
    <div class="row">
       <div class="col-xl-4">
        <h6 class="text-muted">Type :</h6>
          <div class="form-group">
                    <select class="form-control">
                    <option>Select Name</option>
                   
                    </select>
                  </div>
        </div>
        
        <div class="col-xl-4">
        <h6 class="text-muted">Quantity :</h6>
          <input id="totWeight" type="number" name="password" class="form-control"  value="0" />
        </div>

        <div class="col-xl-4">
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

        <div class="col-xl-4">
        <h6 class="text-muted">Vehicle Number :</h6>
          <input id="totWeight" type="text" name="password" class="form-control">
        </div>

        <div class="col-xl-8">
        <h6 class="text-muted">Intermediate Destination District :</h6>
        <textarea  class="form-control" name="addinfor" rows="2" cols="30"></textarea>
        </div>
        <br>
        
          
        <div class="col-xl-4">
        <h6 class="text-muted">Final product storing Area : </h6>
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
        <div class="col-xl-8">
        <h6 class="text-muted">Address of final store place : </h6>
        <textarea  class="form-control" name="addinfor" rows="2" cols="30"></textarea>
        </div>
        
        </div>
        <br>
        <br>
        

   <div class = "row">
   <div class="col-xl-9"></div>    
   <div class="col-xl-1">
         <a href="#" class="btn btn-info">Reset</a>
    </div>         
    <div class="col-xl-1">
         <a href="#" class="btn btn-primary">Add</a>
    </div>
    </div>

<br>
<br>
    
              <div class="card-body">
                <table class="table table-dark">
                  <thead>
                    <tr>
                      <th scope="col"><b>Type</b>	</th>
                      <th scope="col"><b>Quantity</b>	</th>
                      <th scope="col"><b>Collected Area</b></th>
                      <th scope="col"><b>Vehicle Number</b></th>
                      <th scope="col"><b>Intermediate Destination District</b></th>
                      <th scope="col"><b>Final product storing area</b></th>
                      <th scope="col"><b>Address of final store</b></th>
                    </tr>
                  </thead>
                  </table>
              </div>
            

 <br>
 <div class="form-group">
       <h6 class="card-subtitle mb-2 text-muted">Name of country/countries of import :</h6>            
       <input type="text" class="form-control" id="address" rows="2">
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
         <a href="#" class="btn btn-danger">Clear</a>
    </div>         
    <div class="col-xl-1">
         <a href="#" class="btn btn-primary">Submit</a>
    </div>
    </div>           
   
 </div> </div></div></div></div>
 

 

