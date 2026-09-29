<?php

/** @var View $this */

/** @var string $content */

use backend\assets\AppAsset;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use common\components\WebUser;
use common\widgets\Alert;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\web\View;

AppAsset::register($this);
$webURL = Yii::getAlias('@web');

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">

    <head>

        <link rel="shortcut icon" href="ftco-32x32.png">
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.12/clipboard.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <link type="text/css"
              href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css"
              rel="stylesheet">
        <script type="text/javascript"
                src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    </head>
    <?php
    echo '<body class="bg-light">';

    ?>


    <?php $this->beginBody() ?>
    <?php if (!Yii::$app->user->isGuest) { ?>
        <!-- ============================================================== -->
        <!-- main wrapper -->
        <!-- ============================================================== -->
    <?php
    /** @var WebUser $webUser */
    $webUser = Yii::$app->getUser();
    $mainIdentityId = $webUser->getMainIdentityId();

    if ($mainIdentityId != Yii::$app->user->identity->getId()) { ?>

    <div class="dashboard-main-wrapper">
        <div class="" style="   min-height: 40px;
    width: 100%;
    background-color: red;
    text-align: center; padding-top: 12px; padding-right: 10px">

            <p style="color: white; font-weight: bold">Currently, You are viewing a profile as someone else. <a
                        href="../site/stop-impersonating" style="color:#f7f700;" class=""> Click here
                    to go back to your
                    original profile.</a>
            </p>

        </div>
        <?php }

        ?>
        <!-- ============================================================== -->
        <!-- navbar -->
        <!-- ============================================================== -->
        <div class="dashboard-header">

            <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
                <a class="navbar-brand" href="<?= $webURL ?>">
                    <h1>DFAR </h1>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="ml-auto" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto navbar-right-top flex-row ">
                        <!--                        <li class="nav-item dropdown connection">-->
                        <!--                            --><?php
                        //                            foreach (yii::$app->params['languages'] as $key => $lanuage) {
                        //                                echo ' <a href="' . $webURL . '/site/language?id=' . $key . '" class="language btn" id="' . $key . '">' . $lanuage . '</button>';
                        //                            }
                        //                            ?>
                        <!---->
                        <!--                        </li>-->
                        <li class="nav-item dropdown nav-user order-lg-4 ">
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                                        src="<?= $webURL ?>/avatar-1.png" alt="" class="avatar-xs rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown"
                                 aria-labelledby="navbarDropdownMenuLink2">
                                <div class="nav-user-info">
                                    <h5 class="mb-0 text-white nav-user-name"><?= Yii::$app->user->identity->nic ?></h5>
                                    <span class="status"></span><span><?= Constant::$userTypes[Yii::$app->user->identity->type]['name'] ?? "" ?></span>
                                    <!--                                </div>-->
                                    <HR>
                                    <!--                                <div class="nav-user-info">-->
                                    <p style="padding: 0; margin: 0">
                                        <?php
                                        if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
                                            foreach (yii::$app->params['languages'] as $key => $lanuage) {
                                                echo ' <a href="' . $webURL . '/site/language?id=' . $key . '" class="language" id="' . $key . '">' . $lanuage . '</button>';
                                            }
                                        }
                                        //                            ?>
                                    </p>
                                </div>
                                <?php
                                if (!UserTypeUtil::hasType(Constant::FISHERMAN))
                                    echo '<a class="dropdown-item" href="' . $webURL . '/officer/update?id=' . Yii::$app->user->identity->profile_id . '"><i class="fas fa-user mr-2"></i>Profile
                                    Edit</a>'
                                ?>

                                <?php echo '<a class="dropdown-item">'
                                    . Html::beginForm(['/site/logout'], 'post')
                                    . Html::submitButton(
                                        'Logout',
                                        ['class' => 'dropdown-item']
                                    )
                                    . Html::endForm()
                                    . '</a>'; ?>
                            </div>

                        </li>
                    </ul>
                </div>
            </nav>
        </div>


        <!-- ============================================================== -->
        <!-- end navbar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- left sidebar -->
        <!-- ============================================================== -->
        <div class="nav-left-sidebar sidebar-dark">
            <div class="menu-list">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="d-xl-none d-lg-none text-white" href="#"></a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">


                        <ul class="navbar-nav flex-column">
                            <li class="nav-divider">
                                <img src="" style="    width: 100%;
        background: #ffffffba;
        padding: 10px;
        border-radius: 5px;">
                            </li>
                            <?php if (!UserTypeUtil::hasType(Constant::FISHERMAN) &&
                                Yii::$app->user->identity->profile_id != 0) {
                                require("officer-menu.php");
                            } ?>
                            <!--                            --><?php //if (UserTypeUtil::hasType(3 && Yii)
                            //::$app->user->identity->profile_id != 0) {
                            //                                require("ad-menu.php");
                            //                            } ?>
                            <!--                            --><?php //if (UserTypeUtil::hasType(Constant::FI) &&
                            // Constant::FI && Yii::$app->user->identity->profile_id != 0) {
                            //                                require("fi-menu.php");
                            //                            } ?>
                            <!--                            --><?php //if (UserTypeUtil::hasType(11 && Yii)
                            //::$app->user->identity->profile_id != 0) {
                            //                                require("admin-menu.php");
                            //                            } ?>
                            <!--                            --><?php //if (UserTypeUtil::hasType(Constant::DG) &&
                            // Yii::$app->user->identity->profile_id != 0) {
                            //                                require("dg-menu.php");
                            //                            } ?>
                            <!--                            --><?php //if (UserTypeUtil::hasType(Constant::DM) &&
                            // Yii::$app->user->identity->profile_id != 0) {
                            //                                require("dm-menu.php");
                            //                            } ?>
                            <!--                            --><?php //if (UserTypeUtil::hasType(Constant::MEA) &&
                            // Yii::$app->user->identity->profile_id != 0) {
                            //                                require("mea-menu.php");
                            //                            } ?>

                            <?php if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
                                require("fisherman-menu.php");
                            } ?>
                            <!--                            --><?php //if (UserTypeUtil::hasType(Constant::DFI)) {
                            //                                require("dfi-menu.php");
                            //                            } ?>
                            <!--                            --><?php //if (!UserTypeUtil::hasType
                            //(Constant::FISHERMAN) && Constant::FI && Yii::$app->user->identity->profile_id != 0) {
                            //                                require("officer-menu.php");
                            //                            } ?>
                        </ul>


                    </div>
                </nav>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- end left sidebar -->


        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- wrapper  -->
        <!-- ============================================================== -->
        <div class="dashboard-wrapper">
            <div class="container-fluid  dashboard-content">
                <!-- ============================================================== -->
                <!-- pageheader -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title"><?= $this->title ?> </h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <?= Breadcrumbs::widget([
                                        'itemTemplate' => '<li class="breadcrumb-item">{link}</li>',
                                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                        // 'options' => ['class'=>"breadcrumb-link"],
                                    ]) ?>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader -->


                <!-- <div class="container"> -->

                <div class="row">


                    <?php } ?>
                    <?= Alert::widget() ?>

                    <?= $content ?>


                    <?php if (!Yii::$app->user->isGuest) { ?>
                    <!-- </div> -->
                    <!--                            </div>-->

                    <!-- ============================================================== -->
                    <!-- end basic table  -->
                    <!-- ============================================================== -->
                </div>
            </div>


            <div class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            2023 © hyNetz systems - Designed and Developed by<a href="http://hynez.com" target="_blank"
                                                                                class="ml-1">hyNetz</a>.
                        </div>

                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <?php $this->endBody() ?>
    </body>

    </html>
<?php $this->endPage() ?>