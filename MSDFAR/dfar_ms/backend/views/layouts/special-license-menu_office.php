<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Applicationexportbechedemer;
use backend\models\Applicationexportchank;
use backend\models\Applicationexportlivefish;
use backend\models\Applicationexportlobster;
use backend\models\Applicationexportnakla;
use backend\models\Applicationtransportbechedemer;
use backend\models\Applicationtransportchank;
use backend\models\Applicationtransportlivefish;
use backend\models\Applicationtransportlobster;
use backend\models\Applicationtransportnakla;

$where = [];
$whereD = [];
$orWhereD = [];
if (UserTypeUtil::hasType(Constant::FI) && UserTypeUtil::hasType(Constant::SPECIAL_LICENCE)) {
    $where = ['approval_stage' => Constant::SPECIAL_LICENCE];
//    $orWhereD = ["status" => Constant::PaymentPending];

}

if (UserTypeUtil::hasType(Constant::DO)) {
    $where = ['approval_stage' => Constant::DO];
}
if (UserTypeUtil::hasType(Constant::DFI)) {
    $where = ['approval_stage' => Constant::DFI];
}

if (UserTypeUtil::hasType(Constant::DG)) {
    $where = ['approval_stage' => Constant::DG];
}

if (UserTypeUtil::hasType(Constant::AD)) {
    $where = ['approval_stage' => Constant::AD];
}
if (UserTypeUtil::hasType(Constant::DM)) {
    $where = ['approval_stage' => Constant::DM];
}

if (Yii::$app->user->can("Export-license-controller")) {
    ?>

    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false"
           data-target="#submenu-1-5" aria-controls="submenu-1-5">Special License</a>
        <div id="submenu-1-5" class="collapse submenu">
            <ul class="nav flex-column">
                <li class="nav-item ">
                    <a class="nav-link" href="<?= $webURL ?>/applicationexportbechedemer/index">
                        <i class="fa fa-fw fa-user-circle"></i>Export
                        Beche-de-mer <?= ($count = Applicationexportbechedemer::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
                            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link " href="<?= $webURL ?>/applicationtransportbechedemer/index">
                        <i class="fa fa-fw fa-user-circle"></i>Transport Beche-de-mer <?= ($count =
                            Applicationtransportbechedemer::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
                            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link " href="<?= $webURL ?>/applicationexportchank/index">
                        <i class="fa fa-fw fa-user-circle"></i>Export Chank <?= ($count =
                            Applicationexportchank::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
                            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="<?= $webURL ?>/applicationtransportchank/index"><i
                                class="fa fa-fw fa-user-circle"></i>Transport Chank <?= ($count =
                            Applicationtransportchank::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
                            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="<?= $webURL ?>/applicationexportlivefish/index">
                        <i class="fa fa-fw fa-user-circle"></i>Export Live Fish <?= ($count =
                            Applicationexportlivefish::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
                            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="<?= $webURL ?>/applicationtransportlivefish/index">
                        <i class="fa fa-fw fa-user-circle"></i>Transport Live Fish <?= ($count =
                            Applicationtransportlivefish::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
                            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="<?= $webURL ?>/applicationexportlobster/index">
                        <i class="fa fa-fw fa-user-circle"></i>Export Lobster <?= ($count =
                            Applicationexportlobster::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
                            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
                    </a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link " href="<?= $webURL ?>/applicationtransportlobster/index">
                        <i class="fa fa-fw fa-user-circle"></i>Transport Lobster <?= ($count =
                            Applicationtransportlobster::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
                            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link " href="<?= $webURL ?>/applicationexportnakla/index">
                        <i class="fa fa-fw fa-user-circle"></i>Export
                        Nakla <?= ($count = Applicationexportnakla::find()->where
                        (['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
                            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
                    </a>
                </li>

                <li class="nav-item ">
                    <a class="nav-link " href="<?= $webURL ?>/applicationtransportnakla/index">
                        <i class="fa fa-fw fa-user-circle"></i>Transport
                        Nakla <?= ($count = Applicationtransportnakla::find()->where(['status' => Constant::Pending])->andWhere($where)->count()) > 0 ?
                            '<span class="badge badge-success">' . $count . '</span>' : '' ?>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <?php
}
?>




