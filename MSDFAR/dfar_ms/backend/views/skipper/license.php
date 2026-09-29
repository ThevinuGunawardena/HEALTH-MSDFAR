<?php

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\models\Skipper;
use backend\models\SkipperRenew;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

/** @var Skipper|SkipperRenew $model */
/** @var bool $pdf */
/** @var array|null $officer */
/** @var \Closure|null $imageSrc */

$isPdf = isset($pdf) && $pdf == true;

if (!isset($imageSrc) || !$imageSrc instanceof \Closure) {
    $imageSrc = static function (string $relativePath): string {
        $relativePath = trim(str_replace('\\', '/', $relativePath), '/');

        if (
            $relativePath === ''
            || str_contains($relativePath, '..')
            || str_contains($relativePath, "\0")
        ) {
            return '';
        }

        $encodedPath = implode(
            '/',
            array_map('rawurlencode', explode('/', $relativePath))
        );

        return rtrim(Constant::$FILE_VIEW_PATH, '/')
            . '/'
            . $encodedPath;
    };
}

$isRenewal = $model instanceof SkipperRenew;
$workflowType = $isRenewal
    ? 'SKIPPER_LICENCE_RENEW'
    : 'SKIPPER_LICENCE';

$officer = $officer
    ?? CommonService::getApprovedOfficer(
        $model->id,
        $workflowType
    );

if (empty($officer) && $isRenewal) {
    $officer = CommonService::getApprovedOfficer(
        $model->id,
        'SKIPPER_LICENCE'
    );
}

if ($officer instanceof \yii\db\ActiveRecord) {
    $officer = $officer->toArray();
}

if (!is_array($officer)) {
    $officer = [];
}

$frontBackgroundSrc = $imageSrc('static/skipperLicensefront2.jpg');
$backBackgroundSrc = $imageSrc('static/skipperLicenseBack2.jpg');

$profileImageFilename = basename(
    (string) ($model->fisherman->profile_image ?? '')
);
$profileImageSrc = $profileImageFilename !== ''
    ? $imageSrc('fisherman/' . $profileImageFilename)
    : '';

$fishermanSignatureFilename = basename(
    (string) ($model->fisherman->signature ?? '')
);
$fishermanSignatureSrc = $fishermanSignatureFilename !== ''
    ? $imageSrc('fisherman/' . $fishermanSignatureFilename)
    : '';

$officerSignatureFilename = basename(
    (string) ($officer['signature'] ?? '')
);
$officerSignatureSrc = $officerSignatureFilename !== ''
    ? $imageSrc(
        'officer/signature/' . $officerSignatureFilename
    )
    : '';

$tokenModelClass = $isRenewal
    ? SkipperRenew::class
    : Skipper::class;

$validationType = $isRenewal
    ? 'skipper-renew'
    : 'skipper';

$validationToken = SecurityHelper::encryptId(
    $tokenModelClass,
    $model->id
);

$qrCode = new QrCode(
    rtrim(Constant::$BASEURL, '/')
    . '/site/license-validation?type='
    . rawurlencode($validationType)
    . '&token='
    . rawurlencode($validationToken)
);

$output = new Output\Svg();

$outputCode = str_replace(
    '<?xml version="1.0"?>',
    '',
    $output->output(
        $qrCode,
        110,
        'white',
        'black'
    )
);

$topSpacerHeight = 30;

$frontBackgroundStyle = implode(';', array_filter([
    $frontBackgroundSrc !== ''
        ? "background-image:url('{$frontBackgroundSrc}')"
        : null,
    'width:996px',
    'height:629px',
    'background-size:contain',
    'background-repeat:no-repeat',
    'background-position:center',
    'display:inline-block',
]));

$backBackgroundStyle = implode(';', array_filter([
    $backBackgroundSrc !== ''
        ? "background-image:url('{$backBackgroundSrc}')"
        : null,
    'width:996px',
    'height:629px',
    'background-size:contain',
    'background-repeat:no-repeat',
    'background-position:center',
    'display:inline-block',
]));
?>

<div class="license-view">
    <div style="<?= Html::encode($frontBackgroundStyle) ?>">
        <table style="padding:0;border: 0px solid black;">
            <tr>
                <td style="padding:0;border: 0px solid black;width: 60px;height:<?= (int) $topSpacerHeight ?>px;"></td>
                <td style="padding:0;border: 0px solid black;width: 80px"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width: 30px"></td>
                <td style="padding:0;border: 0px solid black;width: 30px"></td>
            </tr>
            <tr>
                <td style="padding:0;border: 0px solid black;height: 60px;width:30px"></td>
                <td style="padding:0;border: 0px solid black;width:200px"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>

            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;width:30px;height: 30px;"></td>
                <td style="padding:0;border: 0px solid black;width:80px"></td>
                <td style="padding:0;border: 0px solid black;width:10px"></td>
                <td style="padding:0;border: 0px solid black;width:30px"></td>
                <td style="padding:0;border: 0px solid black;width:30px"></td>

            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;height: 30px;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width:480px"></td>
                <td style="padding:0;border: 0px solid black;font-weight:bold; font-size: 50px"><span
                            style="font-weight:bold; font-size: 23px"><?= Html::encode((string) ($model->fisherman->fisherman_uid ?? '')) ?></span>
                </td>

            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;height: 30px;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width: 200px;"></td>

            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;height: 55px;"></td>
                <td rowspan="8" style="padding:0; border:0; vertical-align:top;">
                    <?php if ($profileImageSrc !== ''): ?>
                        <img
                            src="<?= Html::encode($profileImageSrc) ?>"
                            style="
                                width:185px;
                                height:220px;
                                object-fit:cover;
                                object-position:center;
                                display:block;
                            "
                            alt="Fisherman profile"
                        >
                    <?php endif; ?>
                </td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td
                        style="padding:0;border: 0px solid black;vertical-align: top"><span
                            style="font-weight:bold; font-size: 23px"><?= Html::encode(strtoupper((string) ($model->fisherman->preferred_name_for_id ?? ''))) ?></span>
                </td>
                <td rowspan="6" style="padding:0;border: 0px solid black;">                    <?= $outputCode ?>
                </td>

            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;height: 25px;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"></td>


            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;height: 30px;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"><span
                            style="font-weight:bold; font-size: 23px"><?= Html::encode((string) ($model->fisherman->nic ?? '')) ?></span></td>

            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;height: 65px;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"><span style="font-weight:bold; font-size: 23px"><span
                                style="font-weight:bold; font-size: 23px"><?= Html::encode((string) ($model->fisherman->dob ?? '')) ?></span></span>
                </td>

            </tr>
            <tr>
                <td style="padding:0;border: 0px solid black;height: 10px;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"></td>

            </tr>
            <tr>
                <td style="padding:0;border: 0px solid black;height: 30px;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"><span
                            style="font-weight:bold; font-size: 23px"><?= Html::encode((string) ($model->category0->category ?? '')) ?> / Skipper</span>
                </td>
            </tr>
            <tr>
                <td style="padding:0;border: 0px solid black;height: 30px;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td rowspan="1"
                    style="padding:0;border: 0px solid black;"></td>

            </tr>

            <tr>
                <td style="padding:0;border: 0px solid black;height: 70px;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;vertical-align: top"><span
                            style="font-weight:bold; font-size: 23px ;vertical-align: top"><?= Html::encode((string) ($model->fisherman->permanent_address ?? '')) ?></span>
                </td>
                <td style="padding:0;border: 0px solid black;"></td>

            </tr>

            <tr>
                <td style="padding:0;border: 0px solid black;height: 30px;"></td>
                <td style="padding:0;border: 0px solid black;"><span
                            style="font-weight:bold; font-size: 23px"><?= !empty($model->approved_time) ? Html::encode(date('Y-m-d', strtotime($model->approved_time))) : '' ?></span>
                </td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;text-align: center"><span
                            style="font-weight:bold; font-size: 23px"><?= Html::encode((string) ($model->expire_date ?? '')) ?></span></td>
                <td style="padding:0; border:0; text-align:center;">
                    <?php if ($fishermanSignatureSrc !== ''): ?>
                        <img
                            src="<?= Html::encode($fishermanSignatureSrc) ?>"
                            style="
                                max-width:100px;
                                max-height:50px;
                                width:auto;
                                height:auto;
                            "
                            alt="Fisherman signature"
                        >
                    <?php endif; ?>
                </td>


            </tr>
            <tr>
                <td style="padding:0;border: 0px solid black;height: 30px;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"></td>

            </tr>

        </table>
    </div>
    <div style="<?= Html::encode($backBackgroundStyle) ?>">
        <table style="padding:0;border: 0px solid black;">
            <tr>
                <td style="padding:0;border: 0px solid black;width: 100px;height: 30px;"></td>
                <td style="padding:0;border: 0px solid black;width: 80px"></td>
                <td style="padding:0;border: 0px solid black;width: 30px"></td>
                <td style="padding:0;border: 0px solid black;width: 30px"></td>
                <td style="padding:0;border: 0px solid black;width: 30px"></td>
            </tr>
            <tr>
                <td style="padding:0;border: 0px solid black;height: 25px;width:30px"></td>
                <td style="padding:0;border: 0px solid black;width:150px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>

            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;height: 30px;width:30px"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width:600px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>

            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;height: 30px;width:30px"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>

            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;height: 30px;width:30px"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>

            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;height: 20px;width:30px"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>

            </tr>
            <tr style="height: 30px;">
                <td style="padding:0;border: 0px solid black;height: 30px;width:30px"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>

            </tr>
            <tr style="height:30px;">
                <td style="padding:0;border:0;height:115px;width:30px;"></td>
                <td style="padding:0;border:0;"></td>

                <td
                    colspan="3"
                    style="
                        padding:0;
                        border:0;
                        font-size:30px;
                        font-weight:bold;
                        color:#ffffff;
                        vertical-align:top;
                        line-height:1.20;
                    "
                >
                  <?php if (!empty($model->name_sinhala)): ?>
    <span
        lang="si"
        class="sinhala-text"
        style="
            font-family: dlsarala;
            vertical-align: top;
            text-shadow:
                0.3px 0 0 #000,
                -0.3px 0 0 #000,
                0 0.3px 0 #000,
                0 -0.3px 0 #000;
        "
    >
        <?= htmlspecialchars(
            (string) $model->name_sinhala,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </span>
    <br>
<?php endif; ?>

<?php if (!empty($model->name_tamil)): ?>
    <?php if ($isPdf): ?>
        <span
            lang="ta"
            class="tamil-text"
            style="
                font-family: notosanstamil;
                font-weight: bold;
                vertical-align: top;
                text-shadow:
                    0.3px 0 0 #000,
                    -0.3px 0 0 #000,
                    0 0.3px 0 #000,
                    0 -0.3px 0 #000;
            "
        >
            <?= htmlspecialchars(
                (string) $model->name_tamil,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </span>
    <?php else: ?>
        <span
            lang="ta"
            style="
                font-family: avvaiyar;
                font-weight: bold;
                vertical-align: top;
                text-shadow:
                    0.3px 0 0 #000,
                    -0.3px 0 0 #000,
                    0 0.3px 0 #000,
                    0 -0.3px 0 #000;
            "
        >
            <?= htmlspecialchars(
                (string) $model->name_tamil,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </span>
    <?php endif; ?>

    <br>
<?php endif; ?>

        <td
    colspan="2"
    style="
        padding: 0;
        width: 289px;
        height: 48px;
        font-size: 23px;
        font-weight: bold;
        vertical-align: top;
        text-shadow:
            0.3px 0 0 #000,
            -0.3px 0 0 #000,
            0 0.3px 0 #000,
            0 -0.3px 0 #000;
    "
>
    <?php if (!empty($model->address_sinhala)): ?>
        <span
            lang="si"
            class="sinhala-text"
            style="
                font-family: dlsarala;
                vertical-align: top;
                text-shadow:
                    0.3px 0 0 #000,
                    -0.3px 0 0 #000,
                    0 0.3px 0 #000,
                    0 -0.3px 0 #000;
            "
        >
            <?= htmlspecialchars(
                (string) $model->address_sinhala,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </span>
        <br>
    <?php endif; ?>

    <?php if (!empty($model->address_tamil)): ?>
        <?php if ($isPdf): ?>
            <span
                lang="ta"
                class="tamil-text"
                style="
                    font-family: notosanstamil;
                    font-weight: bold;
                    vertical-align: top;
                    text-shadow:
                        0.3px 0 0 #000,
                        -0.3px 0 0 #000,
                        0 0.3px 0 #000,
                        0 -0.3px 0 #000;
                "
            >
                <?= htmlspecialchars(
                    (string) $model->address_tamil,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        <?php else: ?>
            <span
                lang="ta"
                style="
                    font-family: avvaiyar;
                    font-weight: bold;
                    vertical-align: top;
                    text-shadow:
                        0.3px 0 0 #000,
                        -0.3px 0 0 #000,
                        0 0.3px 0 #000,
                        0 -0.3px 0 #000;
                "
            >
                <?= htmlspecialchars(
                    (string) $model->address_tamil,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        <?php endif; ?>

        <br>
    <?php endif; ?>
</td>
            </tr>

            <tr>
                <td style="padding:0;border: 0px solid black;height: 30px;width:30px"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>

            </tr>

            <tr>
                <td style="padding:0;border: 0px solid black;height: 30px;width:30px"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>

            </tr>

            <tr>
                <td style="padding:0;border: 0px solid black;height: 30px;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;">
                </td>
                <td style="padding:0;border: 0px solid black;">
                </td>


            </tr>
            <tr>
                <td style="padding:0;border: 0px solid black;height: 30px;width:30px"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0 0 0 151px; border:0; width:50px;">
                    <?php if ($officerSignatureSrc !== ''): ?>
                        <img
                            src="<?= Html::encode($officerSignatureSrc) ?>"
                            style="
                                max-width:150px;
                                max-height:75px;
                                width:auto;
                                height:auto;
                            "
                            alt="Officer signature"
                        >
                    <?php endif; ?>
                </td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>

            </tr>
            <tr>
                <td style="padding:0;border: 0px solid black;height: 30px;width:30px"></td>
                <td style="padding:0;border: 0px solid black;"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>
                <td style="padding:0;border: 0px solid black;width:50px"></td>

            </tr>


        </table>
    </div>
</div>