<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\FishermanRegisterdBoat;
use backend\models\ProfileFisherman;
$where=[];
$whereD=[];
if (UserTypeUtil::hasType(Constant::PRINT_OFFICER)) {
    $where =["district"=>Yii::$app->session->get("officer_district")];
    $whereD =["fisheries_district"=>Yii::$app->session->get("officer_district")];
}
?>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/fisherman/fisherman-print"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Fisherman profiles For Print') ?>
        <?=($count= ProfileFisherman::find()->where(['status' => Constant::Pending])->andWhere($where)->andWhere(['approval_stage'=>Constant::AD])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>

<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/fisherman/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Fisherman Register') ?>
    </a>
</li>

<li class="nav-item ">
    <a class="nav-link "  href="<?= $webURL ?>/reports/fishermen-registration-not-printed"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', ' Boat Owners / Fishermen Without IDs') ?>
    </a>
</li>
<li class="nav-item">
                <a class="nav-link"
                   href="<?= $webURL ?>/reports/fishermen-registration-printed">Fisherman
                    printed</a>
            </li>




