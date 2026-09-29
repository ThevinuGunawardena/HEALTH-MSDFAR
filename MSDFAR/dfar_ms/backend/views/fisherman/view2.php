<?php

use backend\config\UserTypeUtil;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman $model */

$this->title = "View Fishermen profile: ".$model->first_name . " " . $model->last_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Fishermen'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="profile-fisherman-view">

<!--    <h1>--><?php //= Html::encode($this->title) ?><!--</h1>-->

    <p>
<!--        --><?php //= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
<!--        --><?php //= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
//                'method' => 'post',
//            ],
//        ]) ?>
    </p>

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="section-block" id="cards">
                <h3 class="card-title">Fisherman Registration</h3>

            </div>
        </div>
        <div class="col-xl-8">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Fisheries Details</h6>


                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="GS_Division">Name As ID
                                            : <?= $model->preferred_name_for_id ?></label>
                                        <label></label>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="GS_Division">Name In Sinhala :</label>
                                        <label></label>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="DS_Division">Name In Tamil :</label>
                                        <label></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="GS_Division">Applicant Name :</label>
                                        <label><?= $model->first_name ?> <?= $model->last_name ?></label>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="DS_Division">Other Name/s :</label>
                                        <label>Varatharajan</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="GS_Division"> Date of Birth :</label>
                                        <label><?= $model->dob ?></label>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="DS_Division">Passport :</label>
                                        <label> <?= $model->passport ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="GS_Division"> Gender :</label>
                                        <label><?= $model->gender ?></label>
                                    </div>
                                </div>
                                <div class="col-xl-6">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="FI_District">Fisheries District : </label>
                                        <label><?= $model->district ?></label>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="FI_Div">Fisheries Division :</label>
                                        <label><?= $model->division ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="FI_District">Year of Joined : </label>
                                        <label>1993</label>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="FI_Div">Civil Status :</label>
                                        <label><?= $model->civil ?></label>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="FI_District">Life Insurance Number : </label>
                                        <label></label>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="FI_Div">Education Level :</label>
                                        <label>Grade 8 Passed</label>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="FI_District">Nature Of Resident : </label>
                                        <label>permanent</label>
                                    </div>
                                </div>
                                <div class="col-xl-6">

                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card mb-5 shadow-sm">
                <div class="card-body" id="profilecard">
                    <div>
                        <div class="GovLogo">
                            <img src="http://msdfar.com/images/sllogocolor.png" width="20%" height="20%">
                            <h4>DEPARTMENT OF FISHERIES<br> AND AQUATIC RESOURCES</h4>
                        </div>
                        <br>
                        <div id="tem_img">
                            <div class="row">
                                <div class="col-xl-4">
                                    <img src="https://www.pngitem.com/pimgs/m/4-42408_vector-art-design-men-fashion-vector-art-illustration.png"
                                         width="100px" height="100px" style="border-radius: 20%;">
                                </div>
                                <div class="col-xl-8" id="FishmanDel">
                                    <h5>Fisherman Name :</h5><label><?= $model->first_name ?> <?= $model->last_name ?>
                                    </label>
                                    <br>
                                    <h5>NIC :</h5><label><?= $model->nic ?></label>
                                    <br>
                                    <h5>DOB :</h5><label><?= $model->dob ?></label>
                                    <br>
                                    <h5>Category :</h5><label><?= $model->category ?></label>
                                    <br>
                                    <h5>Address :</h5><label><?= $model->permanent_address ?></label>
                                </div>
                            </div>

                        </div>
                        <br><br>

                    </div>


                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="card mb-5 shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Contact Details</h6>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-xl-7">
                                    <div class="form-group">
                                        <label for="FI_District">Permanent Address : </label>
                                        <label><?= $model->permanent_address ?></label>
                                    </div>
                                </div>
                                <div class="col-xl-5">
                                    <div class="form-group">
                                        <label for="FI_Div">Mobile Number :</label>
                                        <label><?= $model->mobile ?></label>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-7">
                                    <div class="form-group">
                                        <label for="FI_District"> Land Line : </label>
                                        <label><?= $model->fixed_line ?></label>
                                    </div>
                                </div>
                                <div class="col-xl-5">
                                    <div class="form-group">
                                        <label for="FI_Div">Email :</label>
                                        <label><?= $model->email ?></label>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <label for="FI_District">Temporary Address : </label>
                                        <label><?= $model->current_address ?></label>
                                    </div>
                                </div>

                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-xl-7">
                                    <div class="form-group">
                                        <label for="FI_District">Name Of Spouse : </label>
                                        <label>V.Sathiyakala</label>
                                    </div>
                                </div>
                                <div class="col-xl-5">
                                    <div class="form-group">
                                        <label for="FI_Div">NIC Of Spouse :</label>
                                        <label>745032940V</label>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-7">
                                    <div class="form-group">
                                        <label for="FI_District">Blood Group : </label>
                                        <label>O+</label>
                                    </div>
                                </div>
                                <div class="col-xl-5">
                                    <div class="form-group">
                                        <label for="FI_Div">Medical Details :</label>
                                        <label></label>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-7">
                                    <div class="form-group">
                                        <label for="FI_District">Donation Last Five Years : </label>
                                        <label></label>
                                    </div>
                                </div>
                                <div class="col-xl-5">
                                    <div class="form-group">
                                        <label for="FI_Div">Society Membership :</label>
                                        <label>Veechukalmunai Rfo</label>

                                    </div>
                                </div>
                            </div>



                            <hr>
                            <h6 class="card-subtitle mb-2 text-muted">Fisherman's Children Details</h6>

                            <div class="row">
                                <div class="col-xl-7">
                                    <div class="form-group">
                                        <label for="FI_District">Child Name : </label>
                                        <label>V.Stabi</label>
                                    </div>
                                </div>
                                <div class="col-xl-5">
                                    <div class="form-group">
                                        <label for="FI_Div">Date of Birth :</label>
                                        <label>2010-09-19</label>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-7">
                                    <div class="form-group">
                                        <label for="FI_District">Child Name : </label>
                                        <label> V.Arinsha</label>
                                    </div>
                                </div>
                                <div class="col-xl-5">
                                    <div class="form-group">
                                        <label for="FI_Div">Date of Birth :</label>
                                        <label> 2010-09-19</label>

                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card mb-5 shadow-sm">

            <div class="card mb-5 shadow-sm">
                <div class="card-body">


                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="card-subtitle mb-2 text-muted">Activity Log</h3>
                        </div>
                        <div class="col-lg-12">

                            <table class="table ">
                                <thead class="table-dark">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Position</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Remarks</th>
                                    <th scope="col">Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($approvalHistory != null) foreach ($approvalHistory as $item) { ?>
                                    <tr>
                                        <th scope="row"><?=  $item->doneBy->nic ?> </th>
                                        <td><?= UserTypeUtil::getTypeNames($item->doneBy->type) ?? "Undefined" ?> </td>
                                        <td><?=$item->status  ?></td>
                                        <td><?=$item->remark  ?></td>
                                        <td><?= $item->date_time ?></td>
                                    </tr>
                                <?php } ?>


                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if ($showApproveBtn) { ?>
                        <hr>
                        <?php $form = ActiveForm::begin(['options' => [
                            'class' => 'userform'
                        ]]); ?>
                        <div class="col-xl-12">
                            <div class="row">

                                <div class="col-xl-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select id="status-approval" name="status-approval" class="form-control">
                                            <option value="approve">Approve</option>
                                            <option value="reject">Reject</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-xl-9">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea id="remarks-approval" name="remarks-approval" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <input type="submit" id="status-approval-btn" value="Submit"
                                               class="btn btn-primary btn-block mt-5">


                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    <?php } ?>


                </div>
            </div>

        </div>


    </div>

</div>
