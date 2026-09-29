<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\User;
use yii\helpers\Html;
use yii\web\YiiAsset;

/** @var yii\web\View $this */
/** @var backend\models\ProfileOfficer $model */
$webURL = Yii::getAlias('@web');

$this->title = "Profile - " . $model->first_name . " " . $model->last_name;
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Profile Officers'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

$user = User::find()->where(['nic' => Yii::$app->user->identity->nic])->one();
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <!--                <p>-->
                <?= Html::a(Yii::t('app', 'Update Other Details'),
                    ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= (!UserTypeUtil::hasType(Constant::ADMIN)) ? Html::a(Yii::t('app', 'Update Password'), ['password-update'], ['class' => 'btn btn-warning']) : "" ?>
                <!--                </p>-->
            </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="profile-officer-view">
                <?php
                if (!UserTypeUtil::hasType(Constant::ADMIN) && $model->agreement == "") {
                    ?>
                    <div class="alert alert-warning">
                        <strong>🚨
                            Please upload the agreement soft copy here in order to activate the profile</strong>
                    </div>

                    <?php
                }
                ?>

                <div class="row ">
                    <div class="col-lg-12">


                        <div class="row">
                            <div class="col-lg-2">
                                <img src="<?= Constant::$FILE_VIEW_PATH
                                ?>officer/profile/<?=
                                    $model->profile_image ?? "avatar-1.png" ?>" style="width: inherit"
                                     alt="Profile Picture"
                                     class="profile-img
<!--                                <img src=" -->
                                <?php //= $webURL ?><!--/avatar-1.png" style="width: inherit" alt="Profile Picture"-->
                                <!--                                     class="profile-img-->
                                <!--                        mx-auto">-->
                            </div>
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h3 class="mt-3"><?= strtoupper($model->first_name) ?? "" ?> <?=
                                                strtoupper($model->last_name) ?? "" ?>
                                            <?= Constant::$USER_RANKS_LABEL[$model->user_level]
                                                ?? ""
                                            ?></h3>
                                    </div>
                                </div>

                                <!--                                    <p class="text-muted"><strong>Rank:</strong> -->
                                <?php //= Constant::$USER_RANKS[$model->user_level]
                                //                                                ?? "" ?><!--</p>-->
                                <hr>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <ul class="list-unstyled text-start">
                                            <li><strong>NIC: </strong> <?= $user->nic ?? "" ?></li>
                                            <li><strong>Email: </strong> <?= $user->email ?? "" ?></li>
                                            <li><strong>Contact Number: </strong> <?= $model->contact_number ?? "" ?>

                                            </li>
                                            <li><strong>Rank:</strong> <?= Constant::$USER_RANKS[$model->user_level]
                                                    ?? "" ?></li>
                                            <?php if (!UserTypeUtil::hasType(Constant::DG) && !UserTypeUtil::hasType
                                                (Constant::DM)) { ?>
                                                <li><strong>District: </strong> <?= $model->district0->name ?? "" ?>
                                                </li>
                                            <?php } ?>
                                            <?php if (UserTypeUtil::hasType(Constant::FI)) { ?>
                                                <li><strong>Division: </strong> <?= $model->division0->name ?? "" ?>
                                                </li>
                                            <?php } ?>



                                        </ul>
                                    </div>
                                    <div class="col-lg-6">
                                        <ul class="list-unstyled text-start">
                                            <li><strong>Device Serial No.: </strong> <?= $model->device_serial ?? "" ?>
                                            <li><strong>Officer
                                                    agreement: </strong> <?php if ($model->agreement != "") { ?>
                                                    <a target="_blank"
                                                       href="<?= Constant::$FILE_VIEW_PATH
                                                       ?>officer/agreement/<?=
                                                       $model->agreement ?>">view</a> <?php
                                                } else echo "Not available" ?></li>
                                            <li><strong> IT evaluation result sheet: </strong> <?php if
                                                ($model->it_result_sheet !=
                                                    "") { ?>
                                                    <a target="_blank"
                                                       href="<?= Constant::$FILE_VIEW_PATH
                                                       ?>officer/cetificate/<?=
                                                       $model->it_result_sheet ?>">view</a> <?php
                                                } else {
                                                    echo "Not available";
                                                } ?></li>
                                            <li><strong>Certificate: </strong> <?php if ($model->cetificate !=
                                                    "") { ?>
                                                    <a target="_blank"
                                                       href="<?= Constant::$FILE_VIEW_PATH
                                                       ?>officer/cetificate/<?=
                                                       $model->cetificate ?>">view</a> <?php
                                                } else {
                                                    echo "Not available";
                                                } ?></li>

                                        </ul>
                                    </div>
                                    <div class="col-lg-4">
                                        <ul class="list-unstyled text-start">


                                            <li><strong>Signature: </strong> <br><?php if ($model->signature !=
                                                    "") { ?>
                                                    <img src="<?= Constant::$FILE_VIEW_PATH
                                                    ?>officer/signature/<?=
                                                    $model->signature ?>" style="width: 150px" alt="signature"
                                                         class="profile-img mx-auto"> <?php
                                                } else {
                                                    echo "Not available";
                                                } ?></li>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <hr>

            </div>
        </div>
    </div>
</div>
