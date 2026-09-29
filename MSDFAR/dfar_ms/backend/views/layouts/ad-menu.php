<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\FishermanRegisterdBoat;
use backend\models\ProfileFisherman;
$where = [];
$whereD = [];
if (UserTypeUtil::hasType(Constant::AD)) {
    $where = ["district" => Yii::$app->session->get("officer_district")];
    $whereD = ["fisheries_district" => Yii::$app->session->get("officer_district")];
}
?>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/analytics/ad-dashboard"><i
            class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Dashboard') ?></a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-numbers/index"><i
            class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Numbers') ?>
        <?= ($count = \backend\models\BoatNumbers::find()->where(['status' => Constant::Pending])->andWhere($whereD)->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>

<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/fisherman/index"><i
            class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Fisherman profiles') ?>
        <?= ($count = ProfileFisherman::find()->where(['status' => Constant::Pending])->andWhere($where)->andWhere(['approval_stage' => Constant::AD])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-registration/index"><i
            class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Register') ?>
        <?= ($count = FishermanRegisterdBoat::find()->where(['status' => Constant::Pending])->andWhere($where)->andWhere(['approval_stage' => Constant::AD])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>

<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-registration-renew/index"><i
            class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Register Renew') ?>
        <?= ($count = \backend\models\FishermanRegisterdBoatRenew::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/national-license/index"><i
            class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'National License') ?>
        <?= ($count = \backend\models\NationalLicense::find()->where(['status' => Constant::Pending])->andWhere($whereD)->andWhere(['approval_stage' => Constant::AD])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/highseas-license/index"><i
            class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Highseas License') ?>
        <?= ($count = \backend\models\HighseasLicense::find()->where(['status' => Constant::Pending])->andWhere($where)->andWhere(['approval_stage' => Constant::AD])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-cancel/index">
        <i class="fa fa-fw fa-user-circle"></i>
        <?= Yii::t('app', 'Boat Cancel Requests') ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/skipper/index"><i
            class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Skipper Licence') ?>
        <?= ($count = \backend\models\Skipper::find()->where(['status' => Constant::Pending])->andWhere($whereD)->andWhere(['approval_stage' => Constant::AD])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>

<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-transfer/index"><i
            class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Transfer Requests') ?></a>
</li>



