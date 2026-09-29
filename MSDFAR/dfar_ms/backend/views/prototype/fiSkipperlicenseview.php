<?php

/** @var yii\web\View $this */

$this->title = 'Skipper License Request ';
?>
<div class="row">
    <div class="col-xl-6">
        <div class="section-block" id="cards">
        </div>
    </div>




    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card mb-5 shadow-sm">
            <div class="boat-box"><br>
                <center>
                    <h3>Skipper's Name : K.D.S.Mendis</h3>
                    <h3>Skipper's Address : 254/C,Doowa Road,Akurala,Kahawa</h3>
                    <h3>Document Reference Number: REQ/SKL/9703</h3>
                </center>
            </div>
        </div>

        <div class="card mb-5 shadow-sm">
            <div class="col-xl-12">
                <div class="form-group">
                    <br>


                </div>
                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-xl-6">
                            <h6 class="card-subtitle mb-2 text-muted">Skipper's Details</h6>

                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label for="GS_Division">Fisheries District: Galle</label>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label for="GS_Division">Gender : Male</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label for="GS_Division">NIC: 970832297v</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="form-group">
                                            <label for="GS_Division">Mobile Number 0753388709</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-xl-6">

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <hr>
        <div class="card mb-5 shadow-sm">

            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Training Programs</h6>

                <form action="">


                    <div class="row">
                        <table class="table" style="border: 1px solid #000;">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Program Name</th>
                                    <th scope="col">Institute Name Id</th>
                                    <th scope="col">Trained Period</th>
                                    <th scope="col">Certified Date</th>

                                    <th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">SKIPPER TRAINING</th>
                                    <td>CINEC CAMPUS</td>
                                    <td>5</td>
                                    <td>2022/01/31</td>

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
                                <div class="col-xl-3">
                                </div>

                            </div>
                        </div>


                </form>


            </div>
        </div>
        <hr>



            <div class="card-body">
                <form action="">


                    <div class="row">
                        <table class="table" style="border: 1px solid #000;">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Category </th>
                                    <th scope="col">Fisherman Name</th>
                                    <th scope="col">Submitted Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Remark</th>
                                    <th scope="col">Document</th>
                                    <th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">Fisherman</th>
                                    <td>K.D.S.Mendis</td>
                                    <td>2023/03/08 03:49 AM </td>
                                    <td>Requested</td>
                                    <td>N/A</td>
                                    <td><button type="button" class="btn btn-primary">Show Documents</button></td>
                                </tr>






                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-3">
                                    </div>
                                    <div class="col-xl-3">
                                    </div>
                                    <div class="col-xl-3">
                                        <div class="form-group">
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-danger btn-block btn-sm" data-toggle="modal" data-target="#RejectModel">
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
                                    <div class="col-xl-3">
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






</div>