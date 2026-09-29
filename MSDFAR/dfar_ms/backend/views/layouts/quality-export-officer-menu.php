<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\FishermanRegisterdBoat;
use backend\models\ProfileFisherman;
$where=[];
$whereD=[];

?>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/catch-data-request/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Export Sales Records') ?>
        
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/catch-data-request/quality-officer-view"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'View Catch Details By Log Book Number') ?>
        
    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/e-log-view/index">
        <i class="fa fa-fw fa-user-circle"></i>E-Log View
    </a>
</li>
<!-- <li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/e-log-edit/index">
        <i class="fa fa-fw fa-user-circle"></i>E-Log Entry
    </a>
</li> -->





