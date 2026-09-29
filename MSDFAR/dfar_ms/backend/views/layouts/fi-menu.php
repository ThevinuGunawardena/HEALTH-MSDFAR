<?php use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumberCancelRequests;
use backend\models\FishermanRegisterdBoat;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
use backend\models\ProfileFisherman;
$where=[];
if (UserTypeUtil::hasType(Constant::FI)) {
    $where =["division"=>Yii::$app->session->get("officer_division")];
}
?>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/analytics/fi-dashboard"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Dashboard') ?></a>
</li>

<li class="nav-item ">
    <a class="nav-link" href="<?= $webURL ?>/scientific/index">
        <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Scientific Inspections') ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/fisherman/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Fisherman Register') ?>
        <?=($count= ProfileFisherman::find()->where(['status' => Constant::Pending])->andWhere($where)->andWhere(['approval_stage'=>Constant::FI])->orWhere(["status"=>Constant::PaymentPending])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-registration/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Register') ?>
        <?= ($count= FishermanRegisterdBoat::find()->where(['status' => Constant::Pending])->andWhere($where)->andWhere(['approval_stage'=>Constant::FI])->orWhere(["status"=>Constant::PaymentPending])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-registration-renew/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Register Renew') ?>
        <?= ($count= \backend\models\FishermanRegisterdBoatRenew::find()->where(['status' => Constant::Pending])->andWhere($where)->andWhere(['approval_stage'=>Constant::FI])->orWhere(["status"=>Constant::PaymentPending])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>


<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/national-license/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'National License') ?>
        <?= ($count= NationalLicense::find()->where(['status' => Constant::Pending])->andWhere($where)->andWhere(['approval_stage'=>Constant::FI])->orWhere(["status"=>Constant::PaymentPending])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/highseas-license/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Highseas License') ?>
        <?= ($count= HighseasLicense::find()->where(['status' => Constant::Pending])->andWhere($where)->andWhere(['approval_stage'=>Constant::FI])->orWhere(["status"=>Constant::PaymentPending])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/district-gear-types/index">
        <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Division Gear Types') ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-cancel/index">
        <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Cancel Requests') ?>
        <?= ($count= BoatNumberCancelRequests::find()->where(['status' => Constant::Pending])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>


