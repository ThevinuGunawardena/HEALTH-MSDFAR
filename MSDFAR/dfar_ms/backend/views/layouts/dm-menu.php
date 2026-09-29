<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
$where=[];
if (UserTypeUtil::hasType(Constant::DM)) {
//    $where =["fisheries_district"=>Yii::$app->session->get("officer_district")];
}
?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= $webURL ?>/analytics/dm-dashboard">
                                        <i class="fa fa-fw fa-tachometer-alt"></i> <?= Yii::t('app', 'Dashboard') ?>
                                    </a>
                                </li>

<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/national-license/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'National License') ?>
        <?= ($count= NationalLicense::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/highseas-license/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Highseas License') ?>
        <?= ($count= HighseasLicense::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>

<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/skipper/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Skipper Licence') ?>
        <?= ($count= \backend\models\Skipper::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-numbers/index"><i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Numbers') ?>
        <?= ($count= \backend\models\BoatNumbers::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>



