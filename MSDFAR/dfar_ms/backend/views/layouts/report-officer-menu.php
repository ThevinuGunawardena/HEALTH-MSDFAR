<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
$where=[];
if (UserTypeUtil::hasType(Constant::DM)) {
//    $where =["fisheries_district"=>Yii::$app->session->get("officer_district")];
}
?><li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/progress-items/index"><i
        class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Progress Dashboard') ?>
    </a>
</li>

