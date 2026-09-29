<?php use backend\config\Constant;
use backend\config\UserTypeUtil;

if (UserTypeUtil::hasType(Constant::DO)) {
    $where =["fisheries_district"=>Yii::$app->session->get("officer_district")];
}
?>


<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-numbers/index"><i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Numbers') ?>
        <?= ($count= \backend\models\BoatNumbers::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
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
    <a class="nav-link " href="<?= $webURL ?>/boat-transfer/index"><i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Transfer Requests') ?>
        <?= ($count= \backend\models\BoatNumberTransferRequest::find()->where(['status' => Constant::Pending])->count()) > 0 ?
            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
    </a>
</li>
<!--//===============-->
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/fisherman/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Fisherman Register ') ?>

    </a>
</li>


<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-registration/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Register') ?>

    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/national-license/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'National License') ?>

    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/highseas-license/index"><i
                class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Highseas License') ?>

    </a>
</li>
<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/boat-cancel/index">
        <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Cancel Requests') ?>

    </a>
</li>

