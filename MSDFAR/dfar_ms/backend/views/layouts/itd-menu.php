<?php use backend\config\Constant;
use backend\models\BoatNumberCancelRequests;
use backend\models\FishermanRegisterdBoat;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
use backend\models\ProfileFisherman;
// $where=[];
// if ( UserTypeUtil::hasType(Constant::DFI)){
//     $where =["district"=>Yii::$app->session->get("officer_district")];
//     $whereD =["fisheries_district"=>Yii::$app->session->get("officer_district")];}
?>
<?php Yii::$app->user->can("Reports-view") ? require("reports.php") : ""; ?>


<li class="nav-item ">
    <a class="nav-link" href="<?= $webURL ?>/inquiry/index">
        <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Inquiry') ?>
    </a>
</li>

<li class="nav-item ">
    <a class="nav-link" href="<?= $webURL ?>/inquiry/analytics">
        <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Analytics') ?>
    </a>
</li>

<li class="nav-item ">
    <a class="nav-link" href="<?= $webURL ?>/progress-items/index">
        <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'System Progress') ?>
    </a>
</li>

<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/e-log-view/index">
        <i class="fa fa-fw fa-user-circle"></i>E-Log View
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/e-log-edit/main">
        <i class="fa fa-fw fa-user-circle"></i>E-Log Entry
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/fisherman-re-correction/officer-report">
        <i class="fa fa-fw fa-user-circle"></i>Officer Ignore Report
    </a>
</li>