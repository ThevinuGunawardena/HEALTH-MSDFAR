<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var int $statusCode
 * @var string $title
 * @var string $message
 * @var string $referenceCode
 * @var string $occurredAt
 */

// Split the comma-separated en, si, ta values
$titleParts = array_map('trim', explode(',', $title));
$messageParts = array_map('trim', explode(',', $message));

$languages = ['en' => 'English', 'si' => 'සිංහල', 'ta' => 'தமிழ்'];
$langKeys = array_keys($languages);

// $this->title = $statusCode . ' - ' . ($titleParts[0] ?? '');

$homeUrl = Url::to(['/site/index']);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-5">

                <div class="card-header bg-white">
                    <h4 class="mb-0 text-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        System Error
                    </h4>
                </div>

                <div class="card-body text-center">
                    <div class="alert alert-danger text-left mb-4">

                        <h5 class="font-weight-bold mb-2">
                            <i class="fas fa-exclamation-circle"></i>
                            Error Reference Code
                        </h5>

                        <h3 class="mb-0 text-danger font-weight-bold">
                            <?= Html::encode($referenceCode) ?>
                        </h3>

        <section class="content-panel">
            <div class="error-badge">
                <span class="error-badge-dot"></span>
                Request could not be completed -  <?= Html::encode($referenceCode) ?>

            </div>
                        <small class="text-muted">
                            Please provide this code to the IT Division when reporting this issue.
                        </small>

                    </div>
                    <div class="mb-4">
                        <h1 class="display-1 font-weight-bold text-danger">
                            <?= Html::encode($statusCode) ?>
                        </h1>

                        <!-- Message box: one block per language -->
                        <div class="text-left mx-auto" style="max-width: 700px;">
                            <?php foreach ($langKeys as $i => $lang): ?>
                                <div class="mb-3">
                                    <h5 class="font-weight-bold mb-1">
                                        <?= Html::encode($titleParts[$i] ?? '') ?>
                                    
                                    </h5>
                                    <p class="text-danger font-weight-bold mb-0" style="font-size: 16px;">
                                        <?= Html::encode($messageParts[$i] ?? '') ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="alert alert-warning text-left">
                        <h5>
                            <i class="fas fa-info-circle"></i>
                            What happened?
                        </h5>
                        <p class="mb-0">
                            The system could not complete your request.
                            The error has been recorded for technical review.
                        </p>
                    </div>

                    <div class="card border">
                        <div class="card-header bg-light">
                            Error Information
                        </div>
                        <div class="card-body text-left">
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Reference Code</div>
                                <div class="col-md-8 text-primary"><?= Html::encode($referenceCode) ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 font-weight-bold">Status Code</div>
                                <div class="col-md-8"><?= Html::encode($statusCode) ?></div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 font-weight-bold">Occurred At</div>
                                <div class="col-md-8"><?= Html::encode($occurredAt) ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <?= Html::a('<i class="fas fa-home"></i> Dashboard', $homeUrl, ['class' => 'btn btn-primary mr-2']) ?>
                        <?= Html::a('<i class="fas fa-redo"></i> Try Again', Yii::$app->request->url, ['class' => 'btn btn-secondary']) ?>
                    </div>

                    <div class="mt-4 text-muted small">
                        <i class="fas fa-shield-alt"></i>
                        DFAR Management System | IT Division
                        <br>
                        Please provide the reference code when contacting support.
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>