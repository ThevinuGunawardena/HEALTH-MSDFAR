<?php

use frontend\models\SignupForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var SignupForm $model */

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;

$imagePath = Url::to('@web/themeAssets/images/bg5.jpeg');
$logoPath = Url::to('@web/themeAssets/images/login_logo.png');
$formlogoPath = Url::to('@web/themeAssets/images/form_logo.png');
$nationalPath = Url::to('@web/themeAssets/images/national_logo.png');

?>

<style>
    .bg-light {
        background-image: url("<?= $imagePath ?>");
        background-size: cover;
        background-position: center center;
        height: 100vh;
    }
</style>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="row">
        <div class="col-xl-6" id="signup-heading"><br><br>
            <center>
                <?= Html::img($nationalPath, ['class' => 'login-logo', 'id' => 'signup-logo']) ?>
                <h1 id="dfar_heading">Department of Fisheries and Aquatic Resources</h1>
            </center>
            <div class="vision" id="vision_heading">
                <!-- <h2>Our Vision</h2>
                <p id="vision">To provide an optimum contribution to the national economy through strengthening the socio–economic status of the fisher communities while maintaining the fisheries and aquatic resources in a sustainable manner.</p> -->
            </div>
        </div>
        <div class="col-xl-6">
            <div class="splash-container">
                <div class="card shadow-sm signup" id="signup">
                    
                    <div class="card-body">
                    <?= Html::img($formlogoPath, ['class' => 'form-logo', 'id' => 'form-logo']) ?>

                        <p>Please fill out the following fields to signup:</p>

                        <?php $form = ActiveForm::begin(['id' => 'signup-form']); ?>

                        <?= $form->field($model, 'nic')->textInput(['autofocus' => true]) ?>

                        <?= $form->field($model, 'password', [
                            'template' => "{label}\n<div class=\"input-group\">{input}<span class=\"input-group-text toggle-password\">  <i class=\"fa fa-fw fa-eye\"></i></span></div>\n{error}",
                            'inputOptions' => ['class' => 'form-control'],
                            'labelOptions' => ['class' => 'form-label'],
                        ])->passwordInput() ?>

                        <div class="form-group">
                            <?= Html::submitButton('Signup', ['class' => 'btn btn-block btn-primary', 'name' => 'signup-button']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>

                        <?= Html::a('Login', ['site/login'], ['class' => 'btn btn-outline-primary btn-block']) ?>
                        <span class="splash-description">Powered by <a
                                    href="https://www.fisheriesdept.gov.lk/software-development-unit/">IT Division, Department of Fisheries and Aquatic Resources, Sri Lanka.</a></span>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
