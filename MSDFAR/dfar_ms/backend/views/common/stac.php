<?php

?>
    <div class="card-body">

        <div class="row">
            <!-- ============================================================== -->
            <!-- four widgets   -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- total views   -->
            <!-- ============================================================== -->

            <!-- ============================================================== -->
            <!-- end total views   -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- total followers   -->
            <!-- ============================================================== -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card  mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted mb-3">Pending </h5>
                            <h2 class="mb-0"> <?=$counts['countPending']?></h2>
                        </div>
                        <div class="float-right icon-shape icon-xl rounded-circle  bg-primary-light mt-1">
                            <i class="fa fa-clock fa-fw fa-sm text-primary font-24"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted mb-3">Completed</h5>
                            <h2 class="mb-0"> <?=$counts['countActive']?></h2>
                        </div>
                        <div class="float-right icon-shape icon-xl rounded-circle  bg-info-light mt-1">
                            <i class="fa fa-check fa-fw fa-sm text-info font-24"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end total followers   -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- partnerships   -->
            <!-- ============================================================== -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted mb-3">Final Approval</h5>
                            <h2 class="mb-0"> <?=$counts['countFinalApproval']?></h2>
                        </div>
                        <div class="float-right icon-shape icon-xl rounded-circle  bg-secondary-light mt-1">
                            <i class="fa fa-hourglass-start fa-fw fa-sm text-secondary font-24"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end partnerships   -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- total earned   -->
            <!-- ============================================================== -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                <div class="card mb-5 shadow-sm">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted mb-3">Expired</h5>
                            <h2 class="mb-0">  <?=$counts['countExpired']?></h2>
                        </div>
                        <div class="float-right icon-shape icon-xl rounded-circle bg-warning-light  mt-1">
                            <i class="fa fa-times-circle fa-fw fa-sm text-warning font-24"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end total earned   -->
            <!-- ============================================================== -->
        </div>
    </div>
