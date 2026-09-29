<?php

use backend\components\SecurityHelper;
use backend\models\FishermanRegisterdBoatLicense;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var FishermanRegisterdBoatLicense $model */
/** @var bool $first */
/** @var bool $isPdf */
/** @var array $officer */
/** @var bool $hasOfficerSignature */
/** @var bool $hasFishermanSignature */
/** @var \Closure|null $imageSrc */

$first = (bool) ($first ?? false);
$isPdf = (bool) ($isPdf ?? false);

$officer = is_array($officer ?? null)
    ? $officer
    : [];

$hasOfficerSignature = (bool) (
    $hasOfficerSignature ?? false
);

$hasFishermanSignature = (bool) (
    $hasFishermanSignature ?? false
);

$imageSrc = $imageSrc ?? null;

/*
 * Validate the licence model identifier before
 * creating the download token.
 */
$licenseNid = (int) ($model->nid ?? 0);

if ($licenseNid <= 0) {
    throw new \yii\web\NotFoundHttpException(
        Yii::t(
            'app',
            'Boat licence record was not found.'
        )
    );
}

/*
 * The token model must match the model used by:
 *
 * actionLicenseDownload()
 * actionFirstLicenseDownload()
 */
$licenseToken = SecurityHelper::encryptId(
    FishermanRegisterdBoatLicense::class,
    $licenseNid
);

$viewFile = $first
    ? 'licenseFirst'
    : 'license';

$downloadRoute = $first
    ? '/boat-registration/first-license-download'
    : '/boat-registration/license-download';
?>

<style>
    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }
</style>

<?php if (!$isPdf): ?>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">

            <div class="card-body">

                <?= Html::a(
                    Yii::t(
                        'app',
                        'Download'
                    ),
                    [
                        $downloadRoute,
                        'token' => $licenseToken,
                    ],
                    [
                        'class' => 'btn btn-success',
                    ]
                ) ?>

            </div>

        </div>

    </div>

<?php endif; ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">

        <div class="card-body">

            <div class="license-view">

                <?= $this->render(
                    $viewFile,
                    [
                        'model' => $model,
                        'officer' => $officer,
                        'first' => $first,
                        'isPdf' => $isPdf,
                        'hasOfficerSignature' =>
                            $hasOfficerSignature,
                        'hasFishermanSignature' =>
                            $hasFishermanSignature,
                        'imageSrc' => $imageSrc,
                    ]
                ) ?>

            </div>

        </div>

    </div>

</div>