<?php

/** @var yii\web\View $this */

$this->title = 'Yard Owner Dashboard';
?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">

        </div>
    </div>
    <div class="Fisherman-"></div>
    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="image-card">
                            <img src="https://d1csarkz8obe9u.cloudfront.net/posterpreviews/man-vector-design-template-1ba90da9b45ecf00ceb3b8ae442ad32c_screen.jpg?ts=1601484738">
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row" id="categoryName">
                            <div class="col">
                                <div class="category">
                                    <h3>Fisherman</h3>
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="row" id="categoryName">
                            <div class="col">
                                <div class="category" style="margin-top: 5px;">
                                    <h3>Yard Owner</h3>
                                </div>
                            </div>
                            
                        </div>

                        <div class="detail-card">
                            <div class="details">
                                <label id="lbl">Fishermn ID</label>
                                <label id="ProfDetails">FM/MTR/0234</label>
                            </div>
                            <div class="details">
                                <label id="lbl">Name</label>
                                <label id="ProfDetails">Tomas Tenis</label>
                            </div>
                            <div class="details">
                                <label id="lbl">Address</label>
                                <label id="ProfDetails">75/A, Codbay, Trincomalee</label>
                            </div>
                            <div class="details">
                                <label id="lbl">Mobile</label>
                                <label id="ProfDetails">+94 71 875 7854</label>
                            </div>
                            <div class="details">
                                <label id="lbl">Home</label>
                                <label id="ProfDetails">+94 112 754 7845</label>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>


    </div>
    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h3>Family Details</h3>
                <div class="">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col"></th>
                                <th scope="col">Dependent Name</th>
                                <th scope="col"></th>
                                <th scope="col">Dependent Relation</th>
                                <th scope="col"></th>
                                <th scope="col">Dependent NIC</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td></td>
                                <td>Mrs. Nimali Vidarshani</td>
                                <td></td>
                                <td>Wife</td>
                                <td></td>
                                <td>19685474785</td>


                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td></td>
                                <td>Mr. Kasun Kumara</td>
                                <td></td>
                                <td>Son</td>
                                <td></td>
                                <td>200007574784</td>

                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>


    </div>
</div>

<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    </div>

    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h3>Licences</h3>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" colspan="2">Number</th>
                            <th>Category</th>
                            <th></th>
                            <th scope="col" colspan="2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td colspan="2">FM000536</td>
                            <td>Fisherman</td>
                            <td></td>
                            <td><a href="fm-registerrenew" class="btn btn-primary">Renew</a></td>
                        </tr>
                        <tr>
                            <th scope="row">1</th>
                            <td colspan="2">FM002882</td>
                            <td>Skipper</td>
                            <td></td>
                            <td><a href="fm-skipperlicenserenew" class="btn btn-primary">Renew</a></td>
                        </tr>
                        <tr>
                            <th scope="row">1</th>
                            <td colspan="2">IMULA2345MTR</td>
                            <td>Boat</td>
                            <td></td>
                            <td><a href="bo-operationezrenew" class="btn btn-primary">Renew National License</a><br><br><a href="bo-operationhs" class="btn btn-primary">Apply For HighSeas</a></td>
                        </tr>
                        <tr>
                        <th scope="row">1</th>
                            <td colspan="2">IMULA2345MTR</td>
                            <td>Boat</td>
                            <td></td>
                            <td><a href="bo-operationhsrenew" class="btn btn-primary">Renew HighSeas License</a></td>
                        </tr>

                    </tbody>
                </table>


            </div>

        </div>


    </div>
    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h3>Boat Number Details</h3>
                <div class="">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" colspan="2">Number</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td colspan="2">IMULA2525MTR</td>
                                <td><span class="badge badge-success">To be Paid</span></td>
                                <td><a href="#" class="btn btn-primary">Pay Now</a></td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td colspan="2">IMULA2525MTR</td>
                                <td><span class="badge badge-success">To be Paid</span></td>
                                <td><a href="#" class="btn btn-primary">Pay Now</a></td>
                            </tr>
                            <tr>
                                <th scope="row">3</th>
                                <td colspan="2">IMULA2525MTR</td>
                                <td><span class="badge badge-success">Paid</span></td>
                                <td><a href="bo-registration" class="btn btn-primary">Boat Registration</a></td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>


    </div>

</div>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    </div>

    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h3>Boats for First Registration</h3>
                <div class="">
                <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" colspan="2">Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td colspan="2">IMULA2525MTR</td>
                                <td><a href="bo-registration" class="btn btn-primary">Register</a></td>
                            </tr>
                            <tr>
                                <th scope="row">1</th>
                                <td colspan="2">IMULA2525MTR</td>
                                <td><a href="bo-registration" class="btn btn-primary">Register</a></td>
                            </tr>
                            <tr>
                                <th scope="row">1</th>
                                <td colspan="2">IMULA2525MTR</td>
                                <td><a href="bo-registration" class="btn btn-primary">Register</a></td>
                            </tr>
                           
                        </tbody>
                    </table>
                </div>
            </div>

        </div>


    </div>
    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h3>Boats for Renew Registration</h3>
                <div class="">
                <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" colspan="2">Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td colspan="2">IMULA2545MTR</td>
                                <td><a href="bo-renewal" class="btn btn-primary">Renew</a> <a href="fm-bocancelation" class="btn btn-danger">Cancel Boat</a></td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td colspan="2">IMULA2555MTR</td>
                                <td><a href="bo-renewal" class="btn btn-primary">Renew</a> <a href="fm-bocancelation" class="btn btn-danger">Cancel Boat</a></td>
                            </tr>
                            <tr>
                                <th scope="row">3</th>
                                <td colspan="2">IMULA2525MTR</td>
                                <td><a href="bo-renewal" class="btn btn-primary">Renew</a> <a href="fm-bocancelation" class="btn btn-danger">Cancel Boat</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>


    </div>
</div>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    </div>

    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h3>Yard Ownerships</h3>
                <div class="">
                <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" colspan="2">Yard Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td colspan="2">DINUSHA MARINE</td>
                                <td><a href="ya-renewal" class="btn btn-primary">Renew</a></td>
                            </tr>
                           
                        </tbody>
                    </table>
                </div>
            </div>

        </div>


    </div>
   
</div>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    </div>

    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h3>Donations</h3>
                <div class="">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col"></th>
                                <th scope="col">Donation File No</th>
                                <th scope="col"></th>
                                <th scope="col">Amount</th>
                                <th scope="col"></th>
                                <th scope="col">Status</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td></td>
                                <td>DO/DFAR/0252</td>
                                <td></td>
                                <td>Rs.541,000</td>
                                <td></td>
                                <td><a href="">Update</a></td>


                            </tr>
                            <tr>
                                <th scope="row">1</th>
                                <td></td>
                                <td>DO/DFAR/0252</td>
                                <td></td>
                                <td>Rs.541,000</td>
                                <td></td>
                                <td><a href="">Update</a></td>


                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>


    </div>
    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h3>Insurance</h3>
                <div class="">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col"></th>
                                <th scope="col">Insurance Number</th>
                                <th scope="col"></th>
                                <th scope="col">Renewal Date</th>
                                <th scope="col"></th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td></td>
                                <td>BDh/62/ha</td>
                                <td></td>
                                <td>20/05/2025</td>
                                <td></td>


                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td></td>
                                <td>BDh/62/ha</td>
                                <td></td>
                                <td>20/05/2025</td>
                                <td></td>


                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>


    </div>
</div>