<?php

use backend\config\Constant;
use backend\services\CommonService;
use backend\components\BaminiToUnicode;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;
use yii\helpers\Html;

$isPdf = (bool) ($isPdf ?? false);

/*
 * When imageSrc is not passed, this is a normal browser view.
 * Build an authenticated /files/... URL.
 */
if (
    !isset($imageSrc)
    || !$imageSrc instanceof \Closure
) {
    $imageSrc = static function (
        string $relativePath
    ): string {
        $relativePath = trim(
            str_replace('\\', '/', $relativePath),
            '/'
        );

        if (
            $relativePath === ''
            || str_contains($relativePath, '..')
            || str_contains($relativePath, "\0")
        ) {
            return '';
        }

        $encodedPath = implode(
            '/',
            array_map(
                'rawurlencode',
                explode('/', $relativePath)
            )
        );

        return '/files/' . $encodedPath;
    };
}

$frontBackgroundUrl = $imageSrc(
    'static/fishermanFront.jpeg'
);

$backBackgroundUrl = $imageSrc(
    'static/fishermanBack.jpeg'
);

$nameTamil = trim(
    (string) ($model->name_tamil ?? '')
);

$addressTamil = trim(
    (string) ($model->address_tamil ?? '')
);

/*
 * Detect whether the text already contains Unicode Tamil.
 */
$hasUnicodeTamil = static function (string $text): bool {
    return preg_match(
        '/[\x{0B80}-\x{0BFF}]/u',
        $text
    ) === 1;
};

/*
 * Convert only legacy Bamini text.
 * Preserve normal slash characters such as 202/A1.
 */
$prepareTamilText = static function (
    string $text
) use (
    $isPdf,
    $hasUnicodeTamil
): string {
    if ($text === '') {
        return '';
    }

    /*
     * The text is already Unicode Tamil.
     */
    if ($hasUnicodeTamil($text)) {
        return $text;
    }

    /*
     * Browser view can keep the original Bamini text.
     */
    if (!$isPdf) {
        return $text;
    }

    /*
     * Protect slash characters before Bamini conversion.
     */
    $slashMarker = "\u{E000}";

    $protectedText = str_replace(
        '/',
        $slashMarker,
        $text
    );

    $convertedText = BaminiToUnicode::convert(
        $protectedText
    );

    return str_replace(
        $slashMarker,
        '/',
        $convertedText
    );
};

$nameTamilDisplay = $prepareTamilText(
    $nameTamil
);

$addressTamilDisplay = $prepareTamilText(
    $addressTamil
);


$hex = bin2hex('{"type":"fisherman-license","id":"' . $model->id . '"}');
$qrCode = new QrCode(Constant::$BASEURL . "/site/license-validation?token=" . $hex);
$output = new Output\Svg();
$outputCode = str_replace('<?xml version="1.0"?>', '', $output->output($qrCode, 200, 'white', 'black'));

$officer = CommonService::getApprovedOfficer(
    $model->id,
    'FISHERMAN-REG'
);

if ($officer instanceof \yii\db\ActiveRecord) {
    $officer = $officer->toArray();
}

if (!is_array($officer)) {
    $officer = [];
}

$licenseFromDate = $licenseFromDate ?? null;
$licenseExpireDate = $licenseExpireDate ?? '';

$profileFilename = basename(
    (string) ($model->profile_image ?? '')
);

$fishermanSignatureFilename = basename(
    (string) ($model->signature ?? '')
);

$officerSignatureFilename = basename(
    (string) ($officer['signature'] ?? '')
);

$profileImageUrl = $profileFilename !== ''
    ? $imageSrc('fisherman/' . $profileFilename)
    : '';

$fishermanSignatureUrl = $fishermanSignatureFilename !== ''
    ? $imageSrc('fisherman/' . $fishermanSignatureFilename)
    : '';

$officerSignatureUrl = $officerSignatureFilename !== ''
    ? $imageSrc(
        'officer/signature/' . $officerSignatureFilename
    )
    : '';

$height = $isPdf ? '65px' : '40px';
?>
<style>
   .sinhala-text {
    font-family: dlsarala, sans-serif !important;
    font-size: 30px;
    font-weight: bold;
    color: #ffffff !important;
    line-height: 1.2;
}

.tamil-text {
    font-family: notosanstamil, sans-serif !important;
    font-size: 24px;
    font-weight: bold;
    color: #ffffff !important;
    line-height: 1.3;
}

.tamil-text-web {
    font-family: bamini, sans-serif !important;
    font-size: 30px;
    font-weight: bold;
    color: #ffffff !important;
    line-height: 1.2;
}

.tamil-unicode-web {
    font-family: "Noto Sans Tamil", "Nirmala UI", Arial, sans-serif !important;
    font-size: 24px;
    font-weight: bold;
    color: #ffffff !important;
    line-height: 1.3;
}
</style>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <div id="licensePrint" class="license-view">
                    <div style="
                        background-image: url('<?= Html::encode(
                            $frontBackgroundUrl
                        ) ?>');
                        width: 996px;
                        height: 629px;
                        background-size: contain;
                        background-repeat: no-repeat;
                        background-position: center;
                        display: inline-block;
                    ">                 
                    <!--        <img src="--><?php //=$webURL?><!--/uploads/fisherman/-->
                    <?php //= $model->profile_image ?><!--" style="width: 112px;height: 143px;z-index: 1;margin: 113px  0px 0px 24px;"/>-->
                    <!--        <img src="-->
                    <?php //=$webURL?><!--/license/fishermanLicenseFront.png" style=" width: auto;height: 9cm;grid-row-start: 1;grid-column-start: 1;"/>-->
                    <table style="padding:0;border: 0px solid black;">
                        <tr>
                            <td style="padding:0;border: 0px solid black;width: 60px;height: <?= $height ?>;"></td>
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
                            <td style="padding:0;border: 0px solid black;;font-size: 50px;font-weight: bold"><span
                                        style="font-size: 23px;font-weight: bold"><?= Html::encode((string) ($model->fisherman_uid ?? '')) ?></span>
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
                            <td rowspan="8" style="padding:0px 0 0 0px ;border: 0px solid black;vertical-align: top">
                                <?php if ($profileImageUrl !== ''): ?>
                                    <img
                                        style="width: 185px; height: 220px;"
                                        src="<?= Html::encode(
                                            $profileImageUrl
                                        ) ?>"
                                        alt="Fisherman Profile"
                                    >
                                <?php endif; ?>
                            </td>
                            <td style="padding:0;border: 0px solid black;"></td>
                            <td
                                    style="padding:0;border: 0px solid black;vertical-align: top"><span
                                        style="font-size: 23px;font-weight: bold"><?= Html::encode(strtoupper((string) ($model->preferred_name_for_id ?? ''))) ?></span>
                            </td>
                            <td rowspan="6"
                                style="padding:0;border: 0px solid black;">                    <?= $outputCode ?>
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
                                        style="font-size: 23px;font-weight: bold"><?= Html::encode((string) ($model->nic ?? '')) ?></span></td>

                        </tr>
                        <tr style="height: 30px;">
                            <td style="padding:0;border: 0px solid black;height: 65px;"></td>
                            <td style="padding:0;border: 0px solid black;"></td>
                            <td style="padding:0;border: 0px solid black;"><span
                                        style="font-size: 23px;font-weight: bold"><span
                                            style="font-size: 23px;font-weight: bold"><?= Html::encode((string) ($model->dob ?? '')) ?></span></span>
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
                                        style="font-size: 23px;font-weight: bold"><?= Html::encode(strtoupper((string) ($model->category0->category ?? ''))) ?> / FISHERMAN</span>
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
                                        style="font-size: 23px;font-weight: bold ;vertical-align: top"><?= Html::encode(strtoupper((string) ($model->permanent_address ?? ''))) ?></span>
                            </td>
                            <td style="padding:0;border: 0px solid black;"></td>

                        </tr>

                        <tr>
                            <td style="padding:0;border: 0px solid black;height: 30px;"></td>
                            <td style="padding:0;border: 0px solid black;"><span
                                        style="font-size: 23px;font-weight: bold"><?= Html::encode(!empty($licenseFromDate) ? date('Y-m-d', strtotime((string) $licenseFromDate)) : '') ?></span>
                            </td>
                            <td style="padding:0;border: 0px solid black;"></td>
                            <td style="padding:0;border: 0px solid black;text-align: center"><span
                                        style="font-size: 23px;font-weight: bold"><?= Html::encode((string) $licenseExpireDate) ?></span></td>
                            <td style="padding:0; border:0 solid black; text-align:center;">
                                <?php if ($fishermanSignatureUrl !== ''): ?>
                                    <img
                                        style="max-width:100px; max-height:50px;"
                                        src="<?= Html::encode(
                                            $fishermanSignatureUrl
                                        ) ?>"
                                        alt="Fisherman Signature"
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
                <div style="
                    background-image: url('<?= Html::encode(
                        $backBackgroundUrl
                    ) ?>');
                    width: 996px;
                    height: 629px;
                    background-size: contain;
                    background-repeat: no-repeat;
                    background-position: center;
                    display: inline-block;
                ">
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
                            <td style="padding:0;border: 0px solid black;height: 40px;width:30px"></td>
                            <td style="padding:0;border: 0px solid black;"></td>
                            <td style="padding:0;border: 0px solid black;width:50px"></td>
                            <td style="padding:0;border: 0px solid black;width:50px"></td>
                            <td style="padding:0;border: 0px solid black;width:50px"></td>

                        </tr>
                        <tr style="height: 30px;">
                            <td style="padding:0;border: 0px solid black;height:90px;width:30px"></td>
                            <td style="padding:0;border: 0px solid black;"></td>
                           <td style="padding:0; border:0px solid black; width:50px; vertical-align:top;">
                                <?php if (!empty($model->name_sinhala)): ?>
                                    <span class="sinhala-text">
                                        <?= htmlspecialchars($model->name_sinhala, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <br>
                                <?php endif; ?>

                                <?php if (!empty($model->name_tamil)): ?>
                                <span class="<?= $isPdf
                                    ? 'tamil-text'
                                    : 'tamil-unicode-web' ?>">
                                    <?= htmlspecialchars(
                                        $nameTamilDisplay,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>
                            <?php endif; ?>
                            </td>
                            <td style="padding:0;border: 0px solid black;width:50px"></td>
                            <td style="padding:0;border: 0px solid black;width:50px"></td>
                        </tr>

                        <tr>
                            <td style="padding:0;border: 0px solid black;height: 80px;width:30px"></td>
                            <td style="padding:0;border: 0px solid black;"></td>
                          <td style="padding:0; border:0px solid black; width:50px; vertical-align:top;">
                                <?php if (!empty($model->address_sinhala)): ?>
                                    <span class="sinhala-text">
                                        <?= htmlspecialchars($model->address_sinhala, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <br>
                                <?php endif; ?>

                              <?php if (!empty($model->address_tamil)): ?>
                                <span class="<?= $isPdf
                                    ? 'tamil-text'
                                    : 'tamil-unicode-web' ?>">
                                    <?= htmlspecialchars(
                                        $addressTamilDisplay,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>
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
                            <td style="padding:0 0 0 151px; border:0 solid black; width:50px;">
                                <?php if ($officerSignatureUrl !== ''): ?>
                                    <img
                                        style="max-width:150px; max-height:75px;"
                                        src="<?= Html::encode(
                                            $officerSignatureUrl
                                        ) ?>"
                                        alt="Officer Signature"
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

        </div>
    </div>
</div>