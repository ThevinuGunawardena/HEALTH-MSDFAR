<?php

/** @var yii\web\View $this */

$this->title = 'Reserve New Boat Number';
?>

<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="section-block" id="cards">
            <h3 class="card-title">Reserve New Boat Number</h3>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h4>Owner Details</h4>
                <div class="table-responsive ">
                    <table class="table">
                        <thead><br>
                            <tr>
                                <th scope="col">Fisherman ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">NIC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>FM000001</td>
                                <td>A.B.C Perera</td>
                                <td>831373547V</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <h4>Boat Details</h4>
                                    <label name="yard_no">Boat Yard Number : Y76566545457</label> <br>
                                    <label name="boat_design_id">Boat Design : B77665888</label><br>
                                    <label name="boat_cat">Boat Category : IMUL</label><br>
                                    <label name="vessel_length">Vessel Length (Meter) : 50</label><br>
                                    <label name="hull_no">Hull Number : 587773</label>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="form-group">
                                    <h4>Boat Propelling Details</h4>
                                    <label name="district">District : Colombo</label><br>
                                    <label name="fi_district">Fisheries District : Negombo</label><br>
                                    <label name="fi_division">Fisheries Division : Pitipana</label><br>
                                    <label name="remarks">Remarks</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <h4>Required Documents For Boat Number Reserve</h4>
                <div class="input-group-append">
                    <button type="button" name="btn_doc_upload" class="btn btn-primary">View Documents</button>
                </div>
            </div>
        </div>
    </div>

            
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card mb-5 shadow-sm">
            <div class="card-body">
                <form action="">
                    <div class="row">
                        <table class="table" style="border: 1px solid #000;">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Officer Designation	</th>
                                    <th scope="col">Officer Name</th>
                                    <th scope="col">Submitted Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Remark</th>
                                   
                                    <th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">Boat Owner</th>
                                    <td>A.B.C Perera</td>
                                    <td>2022/12/16 10:00 AM	</td>
                                    <td>Requested</td>
                                    <td>N/A</td>
                                </tr>
                             
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">                                  
                                <div class="col-xl-3">
                                </div>
                                <div class="col-xl-3">
                                </div>
                                <div class="col-xl-3"><br>
                                <div class="form-group">
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#RejectModel">
                                        Reject
                                    </button>

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
                                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#ApproveModel">
                                        Approve
                                    </button>

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
                </form>
            </div>
        </div>
    </div>
</div>