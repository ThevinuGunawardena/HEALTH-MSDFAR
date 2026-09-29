<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Skipper;
use backend\models\SkipperRenew;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Skipper|SkipperRenew $model */
/** @var bool $pdf */
/** @var array|null $officer */
/** @var \Closure|null $imageSrc */

$isPdf = (bool) ($pdf ?? false);

$isRenewal = $model instanceof SkipperRenew;
$tokenModelClass = $isRenewal
    ? SkipperRenew::class
    : Skipper::class;
$routePrefix = $isRenewal
    ? '/skipper-renew'
    : '/skipper';

$recordToken = SecurityHelper::encryptId(
    $tokenModelClass,
    $model->id
);

$isCompletedPrintable =
    $model->approval_stage === 'Completed'
    && UserTypeUtil::hasType(Constant::PRINT);

$viewFile = $isCompletedPrintable
    ? 'license'
    : 'license-temp';
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

                <?php if (!$isRenewal): ?>
                    <?= Html::a(
                        Yii::t(
                            'app',
                            'Add Name and Address in Sinhala and Tamil'
                        ),
                        [
                            '/fisherman/update-native-data',
                            'id' => $model->fisherman->id,
                            'skipper' => $model->id,
                        ],
                        ['class' => 'btn btn-primary mb-1']
                    ) ?>
                <?php endif; ?>

                <?= Html::a(
                    Yii::t('app', 'Download'),
                    [
                        $routePrefix . '/license-download',
                        'token' => $recordToken,
                    ],
                    ['class' => 'btn btn-success mb-1']
                ) ?>

                <?php if ((int) ($model->printed ?? 0) === 0): ?>
                    <?= Html::a(
                        Yii::t('app', 'Mark as Printed'),
                        [
                            $routePrefix . '/printed',
                            'token' => $recordToken,
                        ],
                        [
                            'class' => 'btn btn-danger mb-1',
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
                    <button class="btn btn-info mb-1" type="button" disabled>
                        <?= Html::encode(
                            Yii::t('app', 'Licence Printed')
                        ) ?>
                    </button>
                <?php endif; ?>
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
                        'pdf' => $isPdf,
                        'officer' => $officer ?? [],
                        'imageSrc' => $imageSrc ?? null,
                    ]
                ) ?>
            </div>
        </div>
    </div>
</div>
