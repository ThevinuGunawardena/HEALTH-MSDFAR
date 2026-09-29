<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumberCancelRequests;
use backend\models\BoatNumbers;
use backend\models\BoatNumberTransferRequest;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
use backend\models\ProfileFisherman;
use backend\models\Skipper;
use backend\models\SkipperRenew;

$where = [];
$whereD = [];
$orWhereD = [];
if (UserTypeUtil::hasType(Constant::FI)) {
    $where = ["division" => Yii::$app->session->get("officer_division"), 'approval_stage' => Constant::FI];
    $whereD = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::FI];
//    $orWhereD = ["status" => Constant::PaymentPending];

}

if (UserTypeUtil::hasType(Constant::DO)) {
    $where = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DO];
    $whereD = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DO];
}
if (UserTypeUtil::hasType(Constant::DFI)) {
    $where = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DFI];
    $whereD = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DFI];
}

if (UserTypeUtil::hasType(Constant::DG)) {
    $where = ['approval_stage' => Constant::DG];
    $whereD = ['approval_stage' => Constant::DG];
}

if (UserTypeUtil::hasType(Constant::AD)) {
    $where = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::AD];
    $whereD = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::AD];
}
if (UserTypeUtil::hasType(Constant::DM)) {
    $where = ['approval_stage' => Constant::DM];
    $whereD = ['approval_stage' => Constant::DM];
}
    // Active-state flags for the Leave nav items
    $currentController = Yii::$app->controller->id;
    $currentAction     = Yii::$app->controller->action->id;

    $isDashboardActive    = ($currentController === 'leave' && $currentAction === 'director-dashboard');
    $isRequestLeaveActive = ($currentController === 'leave' && $currentAction === 'index'
                            && Yii::$app->request->get('mode') === 'my');
?>



<?php if (UserTypeUtil::hasType(11) && Yii::$app->user->identity->profile_id != 0) { ?>
    <li class="nav-item ">
        <a class="nav-link" href="<?= $webURL ?>/user/index">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'System Users') ?>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link" href="<?= $webURL ?>/error/reader">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Read Yii Error Codes') ?>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link" href="<?= $webURL ?>/user/company-index">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Export license Users') ?>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link" href="<?= $webURL ?>/auth-item/index">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'User Roles and permissions') ?>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link" href="<?= $webURL ?>/boat-design/index">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat design') ?>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/landing-site/index"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Manage Landing sites') ?>

        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/boat-numbers/index"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Numbers') ?>

        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/boat-transfer/add-man-transfer"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Add Manual Tranfers') ?>

        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/skipper/addskipper"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Add Skipper Manual') ?>

        </a>
    </li>

    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/boat-numbers/add-boat-number-manual"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Add Boat Numbers Manual') ?>

        </a>
    </li>
    <li class="nav-title">
    <?= Yii::t('app', 'API Management') ?>
</li>
<li class="nav-item">
    <a class="nav-link" href="<?= $webURL ?>/api-clients/index">
        <i class="fa fa-fw fa-users"></i>
        <?= Yii::t('app', 'Manage API Clients') ?>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?= $webURL ?>/api-keys/index">
        <i class="fa fa-fw fa-key"></i>
        <?= Yii::t('app', 'Manage API Keys') ?>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?= $webURL ?>/api-endpoints/index">
        <i class="fa fa-fw fa-link"></i>
        <?= Yii::t('app', 'Manage API Endpoints') ?>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?= $webURL ?>/api-key-permissions/index">
        <i class="fa fa-fw fa-lock"></i>
        <?= Yii::t('app', 'Manage API Permissions') ?>
    </a>
</li>
<?php } ?>

<?php if (!UserTypeUtil::hasType(Constant::MEA)) { ?>

<li class="nav-item ">
    <a class="nav-link " href="<?= $webURL ?>/progress-items/index"><i
        class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Progress Dashboard') ?>
    </a>
</li>
    
    <?php
    if (UserTypeUtil::isDG()
        || UserTypeUtil::hasType(Constant::ITD)
        || UserTypeUtil::hasType(Constant::AD)):
    ?>
        <li class="nav-item">
            <a class="nav-link <?= $isDashboardActive ? 'active' : '' ?>"
               href="<?= $webURL ?>/leave/director-dashboard">
                <i class="fa fa-fw fa-tasks"></i><?= Yii::t('app', 'Leave Management') ?>
            </a>
        </li>
    <?php endif; ?>

    <?php
    // ── Request Leave (personal view) ───────────────────────────────
    // CC and KKS are NOT listed here: main.php loads headOffice-menu.php for
    // them instead of this file, so their leave link lives there. The DG is
    // not listed either — the DG applies for leave outside this system.
    if (UserTypeUtil::hasType(Constant::DIRECTOR)
        || UserTypeUtil::hasType(Constant::ITD)
        || UserTypeUtil::hasType(Constant::AD)
        || UserTypeUtil::hasType(Constant::ICT_OFFICER)
        || UserTypeUtil::hasType(Constant::ICT_ASSISTANT)
        || UserTypeUtil::hasType(Constant::FISHERIES_OFFICER)
        || UserTypeUtil::hasType(Constant::DEVELOPMENT_OFFICER)
        || UserTypeUtil::hasType(Constant::MANAGEMENT_SERVICE_OFFICER)) { ?>

        <li class="nav-item">
            <a class="nav-link <?= $isRequestLeaveActive ? 'active' : '' ?>"
                href="<?= $webURL ?>/leave/index?mode=my">
                <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Request Leave') ?>
            </a>
        </li>

    <?php } ?>

    <li class="nav-item ">
        <a class="nav-link" href="<?= $webURL ?>/scientific/index">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Scientific Inspections') ?>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/fisherman/index"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Fisherman Register') ?>
            <?= ($count = ProfileFisherman::find()->where(['status' => Constant::Pending])->andWhere($where)->orWhere($orWhereD)->count()) > 0 ?
                '<span class="badge badge-success">' . $count . '</span>' : '' ?>
        </a>
    </li>

    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/skipper/index"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Skipper Licence') ?>
            <?= ($count = Skipper::find()->where(['status' => Constant::Pending])->andWhere($whereD)->orWhere
            ($orWhereD)->count()) > 0 ?
                '<span class="badge badge-success">' . $count . '</span>' : '' ?>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/skipper-renew/index"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Skipper Licence Renew') ?>
            <?= ($count = SkipperRenew::find()->where(['status' => Constant::Pending])->andWhere($whereD)->orWhere($orWhereD)->count()) > 0 ?
                '<span class="badge badge-success">' . $count . '</span>' : '' ?>
        </a>
    </li>

    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/boat-numbers/index"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Numbers') ?>
            <?= ($count = BoatNumbers::find()->where(['status' => Constant::Pending])->andWhere($whereD)->orWhere($orWhereD)->count()) > 0 ?
                '<span class="badge badge-success">' . $count . '</span>' : '' ?>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/departure-boat/index"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Status') ?>

        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/boat-registration-licenses/license"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Registration License') ?>
            <?= ($count = FishermanRegisterdBoatLicense::find()->where(['status' => Constant::Pending])->andWhere($where)->orWhere($orWhereD)->count()) > 0 ?
                '<span class="badge badge-success">' . $count . '</span>' : '' ?>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/boat-cancel/index">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Cancel Requests') ?>
            <?= ($count = BoatNumberCancelRequests::find()->where(['status' => Constant::Pending])->orWhere($orWhereD)->count()) > 0 ?
                '<span class="badge badge-success">' . $count . '</span>' : '' ?>
        </a>
    </li>


    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/boat-transfer/index"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Boat Transfer Requests') ?>
            <?= ($count = BoatNumberTransferRequest::find()->where(['status' => Constant::Pending])->orWhere($orWhereD)->count()) > 0 ?
                '<span class="badge badge-success">' . $count . '</span>' : '' ?>
        </a>
    </li>

    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/national-license/index"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'National License') ?>
            <?= ($count = NationalLicense::find()->where(['status' => Constant::Pending])->andWhere($whereD)->orWhere($orWhereD)->count()) > 0 ?
                '<span class="badge badge-success">' . $count . '</span>' : '' ?>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/highseas-license/index"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Highseas License') ?>
            <?= ($count = HighseasLicense::find()->where(['status' => Constant::Pending])->andWhere($where)->orWhere($orWhereD)->count()) > 0 ?
                '<span class="badge badge-success">' . $count . '</span>' : '' ?>
        </a>
    </li>

    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/fuel-data/index"><i
                    class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Fuel Data Requests') ?>
            
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="<?= $webURL ?>/kpi/index">
            <i class="fa fa-fw fa-tasks"></i><?= Yii::t('app', 'KPI Dashboard') ?>
        </a>
    </li>

    <li class="nav-item">
    <a class="nav-link" href="#" data-toggle="collapse" aria-expanded="false"
       data-target="#submenu-1-9" aria-controls="submenu-1-4">Detail view on Fisherman and Boat Registration</a>
    <div id="submenu-1-9" class="collapse submenu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link"
                   href="<?= $webURL ?>/fisherman/data-view">Fisherman Data</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   href="<?= $webURL ?>/boat-registration-licenses/data-view">Boat Registration Data</a>
            </li>
           
        </ul>
    </div>
</li>
<?php } ?>



<?php if (UserTypeUtil::hasType(Constant::FI)) { ?>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/district-gear-types/index">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Division Gear Types') ?>
        </a>
    </li>
<?php } ?>

<?php if (UserTypeUtil::hasType(Constant::FI) || UserTypeUtil::hasType(Constant::AD)) { ?>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/bsc-forms/index">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Bsc Forms') ?>
            <?php if (UserTypeUtil::hasType(Constant::AD)):
                $bscPendingCount = 0;
                $bscPendingCount = \backend\models\Bsc1Submission::find()
                    ->joinWith('division')
                    ->where(['m_division.district_id' => Yii::$app->session->get('officer_district')])
                    ->andWhere(['approval_stage' => (string) Constant::AD])
                    ->count();
            ?>
                <?= $bscPendingCount > 0 ? '<span class="badge badge-success">' . $bscPendingCount . '</span>' : '' ?>
            <?php endif; ?>
        </a>
    </li>
<?php } ?>

<?php if (UserTypeUtil::hasType(Constant::DEVELOPMENT_DIVISION) || UserTypeUtil::hasType(Constant::DEVELOPMENT_OFFICER)) { ?>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/bsc-reports/national?form=bsc1">
            <i class="fa fa-fw fa-chart-bar"></i> <?= Yii::t('app', 'BSC-1 National Report') ?>
        </a>
    </li>
    <li class="nav-item ">
        <a class="nav-link " href="<?= $webURL ?>/bsc-reports/national?form=bsc2">
            <i class="fa fa-fw fa-chart-bar"></i> <?= Yii::t('app', 'BSC-2 National Report') ?>
        </a>
    </li>
<?php } ?>

<?php if (UserTypeUtil::hasType(Constant::MEA) || UserTypeUtil::hasType(Constant::AD)) { ?>

    <li class="nav-item ">
        <a class="nav-link" href="<?= $webURL ?>/mea-boat-registration/index">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'MEA Boat registration') ?>
        </a>
    </li>
<?php } ?>




<?php if (UserTypeUtil::hasType(Constant::AD_Highseas)) { ?>
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

<?php } ?>

<?php if (UserTypeUtil::hasType(Constant::DIRECTOR)
        || UserTypeUtil::hasType(Constant::ICT_OFFICER)
        || UserTypeUtil::hasType(Constant::ICT_ASSISTANT)
        || UserTypeUtil::hasType(Constant::FISHERIES_OFFICER)
        || UserTypeUtil::hasType(Constant::DEVELOPMENT_OFFICER)
        || UserTypeUtil::hasType(Constant::MANAGEMENT_SERVICE_OFFICER)) { ?>

    <li class="nav-item">
        <a class="nav-link <?= $isRequestLeaveActive ? 'active' : '' ?>"
           href="<?= $webURL ?>/leave/index?mode=my">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Request Leave') ?>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="<?= $webURL ?>/inquiry/index">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Inquiry') ?>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="<?= $webURL ?>/inquiry/analytics">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Analytics') ?>
        </a>
    </li>

<?php } ?>
