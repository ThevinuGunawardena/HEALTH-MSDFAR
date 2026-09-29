<?php

use backend\components\SecurityHelper;
use backend\models\ProfileFisherman;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var ProfileFisherman $model
 * @var mixed|null $renewModel
 */

$renewModel = $renewModel ?? null;

$licenseStatusModel =
    $renewModel ?? $model;

$isCompleted =
    (string) (
        $licenseStatusModel->approval_stage ?? ''
    ) === 'Completed';

$fishermanId =
    (int) ($model->id ?? 0);

if ($fishermanId <= 0) {
    throw new InvalidConfigException(
        'A valid fisherman profile is required.'
    );
}

/*
 * SecurityHelper expects:
 *
 * encryptId(
 *     ModelClass::class,
 *     numeric ID
 * )
 */
$fishermanToken =
    SecurityHelper::encryptId(
        ProfileFisherman::class,
        $fishermanId
    );

$isPrinted =
    (int) ($model->printed ?? 0) !== 0;

$dlsaralaFontUrl =
    Url::to('@web/DLSarala.ttf');

$avvaiyarFontUrl =
    Url::to('@web/Avvaiyar.otf');

?>

<style>
    @font-face {
        font-family: "dlsarala";
        src: url(
            "<?= Html::encode($dlsaralaFontUrl) ?>"
        ) format("truetype");
        font-style: normal;
        font-weight: normal;
        font-display: swap;
    }

    @font-face {
        font-family: "avvaiyar";
        src: url(
            "<?= Html::encode($avvaiyarFontUrl) ?>"
        ) format("opentype");
        font-style: normal;
        font-weight: normal;
        font-display: swap;
    }
</style>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">

        <div class="card-body">

            <?= Html::a(
                Yii::t(
                    'app',
                    'Add Name and Address in Sinhala and Tamil'
                ),
                [
                    '/fisherman/update-native-data',
                    'token' => $fishermanToken,
                ],
                [
                    'class' => 'btn btn-primary',
                ]
            ) ?>

            <span class="mx-1">|</span>

            <?= Html::a(
                Yii::t(
                    'app',
                    'Download'
                ),
                [
                    '/fisherman/license-download',
                    'token' => $fishermanToken,
                ],
                [
                    'class' => 'btn btn-success',
                ]
            ) ?>

            <span class="mx-1">|</span>

            <?php if (!$isPrinted): ?>

                <?= Html::a(
                    Yii::t(
                        'app',
                        'Mark as Printed'
                    ),
                    [
                        '/fisherman/printed',
                        'token' => $fishermanToken,
                    ],
                    [
                        'class' => 'btn btn-danger',

                        'data' => [
                            'confirm' => Yii::t(
                                'app',
                                'Are you sure you want to mark this as printed?'
                            ),

                            'method' => 'post',
                        ],
                    ]
                ) ?>

            <?php else: ?>

                <button
                    type="button"
                    class="btn btn-info"
                    disabled
                >
                    <?= Html::encode(
                        Yii::t(
                            'app',
                            'Licence printed'
                        )
                    ) ?>
                </button>

            <?php endif; ?>

        </div>

    </div>

</div>

<div class="license-view">

    <?php if ($isCompleted): ?>

        <?= $this->render(
            'license',
            [
                'model' => $model,
                'renewModel' => $renewModel,
                'isPdf' => false,
            ]
        ) ?>

    <?php else: ?>

        <?= $this->render(
            'license-temp',
            [
                'model' => $model,
                'renewModel' => $renewModel,
                'isPdf' => false,
                'hasProfileImage' => false,
            ]
        ) ?>

    <?php endif; ?>

</div>