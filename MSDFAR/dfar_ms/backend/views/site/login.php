

<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var LoginForm $model */

use common\models\LoginForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = 'Login';
$imagePath = Url::to('@web/themeAssets/images/bg5.jpeg');
$logoPath = Url::to('@web/themeAssets/images/login_logo.png');
$formlogoPath = Url::to('@web/themeAssets/images/form_logo.png');
$nationalPath = Url::to('@web/themeAssets/images/national_logo.png');

if (!empty(Yii::$app->params['turnstile']['enabled'])) {
    $this->registerJsFile(
        'https://challenges.cloudflare.com/turnstile/v0/api.js',
        [
            'position' => \yii\web\View::POS_HEAD,
            'async' => true,
            'defer' => true,
        ]
    );
}
?>
<style>
   
    .d-flex.flex-column.h-100{
        background-image: url("<?= $imagePath ?>");
        background-size: cover;
        background-position: center center;
        height: 100vh;
    }
</style>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="row">
        <div class="col-xl-6" id ="login-heading"><br><br>
           <center>
           <?= Html::img($nationalPath, ['class' => 'login-logo', 'id' => 'login-logo']) ?>
           <h1 id="dfar_heading">Department of Fisheries and Aquatic Resources</h1></center>
           <div class="vision" id="vision_heading">
           <!-- <h2>Our Vision</h2>
           <p id="vision">To provide an optimum contribution to the national economy through strengthening the socio–economic status of the fisher communities while maintaining the fisheries and aquatic resources in a sustainable manner.</p> -->
           </div>
        </div>
        <div class="col-xl-6"><div class="splash-container">
        <div class="card shadow-sm login">
            
            <div class="card-body">
            <?= Html::img($formlogoPath, ['class' => 'form-logo', 'id' => 'form-logo']) ?>

                <p>Please fill out the following fields to login:</p>

                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'nic')->textInput(['autofocus' => true, 'autocomplete' => 'username']) ?>

                <?= $form->field($model, 'password', [
                        'template' => "{label}\n<div class=\"input-group\">{input}<span class=\"input-group-text toggle-password\"><i class=\"fa fa-fw fa-eye\"></i></span></div>",
                        'inputOptions' => [
                            'class' => 'form-control',
                            'autocomplete' => 'current-password',
                        ],
                        'labelOptions' => [
                            'class' => 'form-label',
                        ],
                    ])->passwordInput() ?>

                    <?php if ($model->hasErrors('password')): ?>
                        <div class="alert alert-danger py-2 mt-2 mb-3" role="alert">
                            <?= Html::encode(
                                $model->getFirstError('password')
                            ) ?>
                        </div>
                    <?php endif; ?>

                <?= $form->field($model, 'rememberMe')->checkbox() ?>

              <?php if (!empty(Yii::$app->params['turnstile']['enabled'])): ?>
              <div class="form-group">
                <?= Html::tag('div', '', [
                    'class' => 'cf-turnstile',
                    'data-sitekey' =>
                        Yii::$app->params['turnstile']['siteKey'] ?? '',
                    'data-action' => 'login',
                    'data-theme' => 'auto',
                ]) ?>
              </div>
              <?php endif; ?>

            <div class="form-group">
                <?= Html::submitButton(
                    'Login',
                    [
                        'class' => 'btn btn-primary btn-block',
                        'name' => 'login-button',
                    ]
                ) ?>

                <div class="mt-2">
                    If you forgot your password, you can
                    <?= Html::a(
                        'reset it',
                        ['site/request-password-reset']
                    ) ?>.
                </div>
            </div>

                <?php ActiveForm::end(); ?>

                <a href="signup" class="btn btn-block btn-outline-primary">Fisherman signup</a>
                <a href="inquiry/create" class="btn btn-block btn-outline-info">Report Inquiry</a>
                <a href="departure/create" class="btn btn-block btn-outline-info">Departure Request</a>

                <span class="splash-description mt-2">Powered by <a
                            href="https://www.fisheriesdept.gov.lk/software-development-unit/">IT Division, Department of Fisheries and Aquatic Resources, Sri Lanka.</a></span>
            </div>
        </div>
            </div>
        </div>

    </div>

</div>

