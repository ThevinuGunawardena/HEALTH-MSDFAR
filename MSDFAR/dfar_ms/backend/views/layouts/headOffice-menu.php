<?php
    use backend\config\Constant;
    use backend\config\UserTypeUtil;

    $where = [];
    $whereD = [];
    if (UserTypeUtil::hasType(Constant::DIRECTOR)) {
        $where = ["division" => Yii::$app->session->get("officer_division"), 'approval_stage' => Constant::DIRECTOR];
        $whereD = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DIRECTOR];
    }

    // ── Active-tab detection ─────────────────────────────────────────
    // "Leave Management" = the director dashboard.
    // "Request Leave"    = the personal view (leave/index in mode=my).
    $route    = Yii::$app->controller->route;          // e.g. "leave/index"
    $mode     = Yii::$app->request->get('mode');        // "my" on the personal view
    $isDashboardActive   = $route === 'leave/director-dashboard';
    $isRequestLeaveActive = strpos($route, 'leave/') === 0 && !$isDashboardActive;
?>


    

    <?php if (UserTypeUtil::isDirector()): ?>
        <li class="nav-item">
            <a class="nav-link <?= $isDashboardActive ? 'active' : '' ?>"
               href="<?= $webURL ?>/leave/director-dashboard">
                <i class="fa fa-fw fa-tasks"></i><?= Yii::t('app', 'Leave Management') ?>
            </a>
        </li>
    <?php endif; ?>

    <li class="nav-item">
        <a class="nav-link <?= $isRequestLeaveActive ? 'active' : '' ?>"
           href="<?= $webURL ?>/leave/index?mode=my">
            <i class="fa fa-fw fa-user-circle"></i><?= Yii::t('app', 'Request Leave') ?>
        </a>
    </li>

