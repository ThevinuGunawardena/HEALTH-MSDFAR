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
    <a class="nav-link " href="<?= $webURL ?>/catch-data-request/exporter-view"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Reserve the fish portion.') ?>
        
    </a>
</li>





