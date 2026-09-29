<?php

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman[] $fishermen */
/** @var bool $pdf */

use backend\config\Constant;
use backend\services\CommonService;
use backend\components\BaminiToUnicode;
use yii\helpers\Html;

$isPdf = isset($pdf) && $pdf == true;

$boxWidth  = 976;
$boxHeight = 639;

/** @var Closure $imageSrc */

if (!isset($imageSrc) || !$imageSrc instanceof \Closure) {
    $imageSrc = static function (
        string $relativePath
    ): string {
        return rtrim(
            Constant::$FILE_VIEW_PATH,
            '/'
        ) . '/' . ltrim($relativePath, '/');
    };
}

/*
 * Change the filename below if your actual back-template
 * filename is different.
 */
$backBgImageUrl = $imageSrc(
    'static/Fisherman_Template_Back.png'
);
?>

<div class="container">
    <table cellpadding="0" cellspacing="0" style="width:100%; table-layout:fixed; border-collapse:collapse;">
        <?php for ($row = 0; $row < 4; $row++): ?>
            <tr>
                <?php for ($col = 0; $col < 2; $col++): ?>
                    <?php
                    $index = ($row * 2) + (1 - $col);
                    $model = $fishermen[$index] ?? null;

                    if (!$model) {
                        echo '<td style="width:' . $boxWidth . 'px; height:' . $boxHeight . 'px;"></td>';
                        continue;
                    }

                    /*
                     * Tamil data in DB is saved as Bamini / Avvaiyar encoded text.
                     *
                     * Web:
                     * Show directly using Avvaiyar font.
                     *
                     * PDF:
                     * Convert Bamini / Avvaiyar encoded text to Unicode Tamil,
                     * then show using NotoSansTamil font.
                     */
                  

                    $categoryText = '';
                    if ((int)$model->category !== 1) {
                        $categoryText = strtoupper(($model->category0->category ?? '') . ' / FISHERMAN');
                    } else {
                        $categoryText = 'FISHERMAN';
                    }

                    $qrFileUrl = '';

                    if (!empty($model->id)) {
                        $hex = bin2hex('{"type":"fisherman-license","id":"' . $model->id . '"}');
                        $qrText = Constant::$BASEURL . "/site/license-validation?token=" . $hex;

                        $qrCode = new \Mpdf\QrCode\QrCode($qrText);
                        $writer = new \Mpdf\QrCode\Output\Png();
                        $pngBinary = $writer->output($qrCode, 315, [255, 255, 255], [0, 0, 0]);

                        $qrDir = Yii::getAlias('@runtime/qr');
                        if (!is_dir($qrDir)) {
                            @mkdir($qrDir, 0775, true);
                        }

                        $qrPath = $qrDir . '/license_qr_' . $model->id . '.png';
                        file_put_contents($qrPath, $pngBinary);

                        $qrFileUrl = 'file:///' . str_replace('\\', '/', $qrPath);
                    }

                    $officer = CommonService::getApprovedOfficer($model->id, "FISHERMAN-REG");
                    ?>

                    <?php
                        $safeBgImageUrl = htmlspecialchars(
                            (string) $backBgImageUrl,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                        <td style="
                            width: <?= (int) $boxWidth ?>px;
                            height: <?= (int) $boxHeight ?>px;
                            border: 2px solid #595958;
                            <?= $safeBgImageUrl !== ''
                                ? "background-image: url('{$safeBgImageUrl}');"
                                : '' ?>
                            background-size: 100% 100%;
                            background-repeat: no-repeat;
                            background-position: center;
                            padding: 0;
                            margin: 0;
                            vertical-align: top;
                            overflow: hidden;
                        ">
                        <table cellpadding="0" cellspacing="0" style="
                            width:100%;
                            height:100%;
                            border-collapse:collapse;
                            table-layout:fixed;
                            padding:0;
                            margin:0;
                        ">
                            <tr>
                                <td style="padding:0; width:50px; height:15px;"></td>
                                <td style="padding:0; width:300px; height:15px;"></td>
                                <td style="padding:0; width:150px; height:15px;"></td>
                                <td style="padding:0; width:239px; height:15px;"></td>
                                <td style="padding:0; width:239px; height:15px;"></td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:15px;"></td>
                                <td style="padding:0; width:300px; height:15px;"></td>
                                <td style="padding:0; width:150px; height:15px;"></td>
                                <td style="padding:0; width:239px; height:15px;"></td>
                                <td style="padding:0; width:239px; height:15px;"></td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:48px;"></td>
                                <td style="padding:0; width:336px; height:48px;"></td>
                                <td style="padding:0; width:150px; height:48px;"></td>
                                <td style="padding:0; width:239px; height:48px;"></td>
                                <td style="padding:0; width:239px; height:48px;"></td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:48px;"></td>
                                <td style="padding:0; width:300px; height:48px;"></td>
                                <td style="padding:0; width:150px; height:48px;"></td>
                                <td style="padding:0; width:239px; height:48px;"></td>
                                <td style="padding:0; width:239px; height:48px;"></td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:55px;"></td>
                                <td style="padding:0; width:300px; height:55px;"></td>
                                <td style="padding:0; width:150px; height:55px;"></td>
                                <td style="padding:0; width:239px; height:55px;"></td>
                                <td style="padding:0; width:239px; height:55px;"></td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:15px;"></td>

                                <td rowspan="10" style="padding:10px 0 0 0; width:300px; height:398px; vertical-align:top;">
                                    <?php if (!empty($qrFileUrl)): ?>
                                        <?= Html::img($qrFileUrl, [
                                            'id' => 'Fisherman',
                                            'style' => 'width:310px; height:310px; display:block;'
                                        ]) ?>
                                    <?php endif; ?>
                                </td>

                                <td style="padding:0; width:150px; height:15px;"></td>
                                <td style="padding:0; width:239px; height:15px;"></td>

                                <td style="padding:0; width:239px; height:38px; font-size:23px; font-weight:bold; text-align:center;">
                                    <?= strtoupper($model->fisherman_uid ?? '') ?>
                                </td>
                            </tr>

                            <!-- Name row -->
                            <tr>
                                <td style="padding:0; width:50px; height:48px;"></td>

                                <td style="padding:0; width:150px; height:48px; font-size:23px; font-weight:bold;">
                                    <?= Html::img(
                                        $imageSrc('static/name_sinhala_and_tamil_text.png'),
                                        ['alt' => 'Name']
                                    ) ?>
                                </td>

                                <td colspan="2" style="padding:0; width:289px; height:48px; font-size:23px; font-weight:bold;">
                                    <?php if (!empty($model->name_sinhala)): ?>
                                        <span style="font-family:dlsarala; vertical-align:top; text-shadow:0.3px 0 0 #000, -0.3px 0 0 #000, 0 0.3px 0 #000, 0 -0.3px 0 #000;">
                                            <?= htmlspecialchars($model->name_sinhala, ENT_QUOTES, 'UTF-8') ?>
                                        </span><br>
                                    <?php endif; ?>

                                   <?php if (!empty($model->name_tamil)): ?>
                                    <?php if ($isPdf): ?>
                                        <span style="font-family: notosanstamil; font-weight: bold; vertical-align: top;text-shadow:0.3px 0 0 #000, -0.3px 0 0 #000, 0 0.3px 0 #000, 0 -0.3px 0 #000;">
                                            <?= htmlspecialchars($model->name_tamil, ENT_QUOTES, 'UTF-8') ?>
                                        </span><br>
                                    <?php else: ?>
                                        <span style="font-family: avvaiyar; font-weight: bold; vertical-align: top; text-shadow:0.3px 0 0 #000, -0.3px 0 0 #000, 0 0.3px 0 #000, 0 -0.3px 0 #000;">
                                            <?= htmlspecialchars($model->name_tamil, ENT_QUOTES, 'UTF-8') ?>
                                        </span><br>
                                    <?php endif; ?>
                                <?php endif; ?>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:15px;"></td>
                                <td style="padding:0; width:150px; height:15px;"></td>
                                <td style="padding:0; width:239px; height:15px;"></td>
                                <td style="padding:0; width:239px; height:15px;"></td>
                            </tr>

                            <!-- Address row -->
                            <tr>
                                <td style="padding:0; width:50px; height:48px;"></td>

                                <td style="padding:0; width:150px; height:48px; font-size:23px; font-weight:bold;">
                                    <?= Html::img(
                                        $imageSrc('static/Sinhala_and_Tamil_Address_topics.png'),
                                        ['alt' => 'Address']
                                    ) ?>
                                </td>

                                <td colspan="2" style="padding:0; width:289px; height:48px; font-size:23px; font-weight:bold; text-shadow:0.3px 0 0 #000, -0.3px 0 0 #000, 0 0.3px 0 #000, 0 -0.3px 0 #000;">
                                    <?php if (!empty($model->address_sinhala)): ?>
                                        <span style="font-family:dlsarala; vertical-align:top; text-shadow:0.3px 0 0 #000, -0.3px 0 0 #000, 0 0.3px 0 #000, 0 -0.3px 0 #000;">
                                            <?= htmlspecialchars($model->address_sinhala, ENT_QUOTES, 'UTF-8') ?>
                                        </span><br>
                                    <?php endif; ?>

                                    <?php if (!empty($model->address_tamil)): ?>
                                        <?php if ($isPdf): ?>
                                            <span style="font-family: notosanstamil; font-weight: bold; vertical-align: top; text-shadow:0.3px 0 0 #000, -0.3px 0 0 #000, 0 0.3px 0 #000, 0 -0.3px 0 #000;">
                                                <?= htmlspecialchars($model->address_tamil, ENT_QUOTES, 'UTF-8') ?>
                                            </span><br>
                                        <?php else: ?>
                                            <span style="font-family: avvaiyar; font-weight: bold; vertical-align: top; text-shadow:0.3px 0 0 #000, -0.3px 0 0 #000, 0 0.3px 0 #000, 0 -0.3px 0 #000;">
                                                <?= htmlspecialchars($model->address_tamil, ENT_QUOTES, 'UTF-8') ?>
                                            </span><br>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:15px;"></td>
                                <td style="padding:0; width:150px; height:15px;"></td>
                                <td style="padding:0; width:239px; height:15px;"></td>
                                <td style="padding:0; width:239px; height:15px;"></td>
                            </tr>

                            <tr>
                                <td style="border:none; height:48px; color:transparent;"></td>

                                <td style="border:none; font-size:23px; font-weight:bold; color:#FFFFFF00;">
                                    DOB
                                </td>

                                <td colspan="2" style="border:none; font-size:23px; font-weight:bold; color:#FFFFFF00;">
                                    <?= htmlspecialchars($model->dob ?? '', ENT_QUOTES, 'UTF-8') ?>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:15px;"></td>
                                <td style="padding:0; width:150px; height:15px;"></td>
                                <td style="padding:0; width:239px; height:15px;"></td>
                                <td style="padding:0; width:239px; height:15px;"></td>
                            </tr>

                            <!-- Officer signature area -->
                            <tr>
                                <td style="padding:0; width:50px; height:111px;"></td>

                                <td colspan="2" rowspan="5" style="
                                    padding:0;
                                    width:200px;
                                    height:111px;
                                    text-align:center;
                                    vertical-align:middle;
                                ">
                                    <?php
                                    $officerSignatureFilename = basename(
                                        (string) ($officer['signature'] ?? '')
                                    );

                                    $officerSignatureSrc =
                                        $officerSignatureFilename !== ''
                                            ? $imageSrc(
                                                'officer/signature/'
                                                . $officerSignatureFilename
                                            )
                                            : '';
                                    ?>

                                    <?php if ($officerSignatureSrc !== ''): ?>
                                        <img
                                            src="<?= Html::encode(
                                                $officerSignatureSrc
                                            ) ?>"
                                            alt="Officer signature"
                                            style="
                                                max-width:170px;
                                                max-height:120px;
                                                width:auto;
                                                height:auto;
                                                display:inline-block;
                                            "
                                        >
                                    <?php endif; ?>
                                </td>

                                <td style="padding:0; width:150px; height:111px;"></td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:0;"></td>
                                <td style="padding:0; width:150px; height:0;"></td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:0;"></td>
                                <td style="padding:0; width:150px; height:0;"></td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:0;"></td>
                                <td style="padding:0; width:150px; height:0;"></td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:50px; height:0;"></td>
                                <td style="padding:0; width:150px; height:0;"></td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:40px; height:23px;"></td>

                                <td style="
                                    padding:0;
                                    width:300px;
                                    height:23px;
                                    text-align:center;
                                    vertical-align:middle;
                                    font-weight:bold;
                                    color:#FFFFFF00;
                                ">
                                    ISSUED DATE&nbsp;&nbsp;&nbsp;
                                    <?= !empty($model->approved_time)
                                        ? date("Y-m-d", strtotime($model->approved_time))
                                        : ''
                                    ?>
                                </td>

                                <td rowspan="2" colspan="2" style="
                                    text-align:center;
                                    vertical-align:middle;
                                    font-weight:bold;
                                ">
                                    <?= Html::img(
                                        $imageSrc('static/fishID_AD_text.png'),
                                        [
                                            'alt' => 'Fisherman ID text',
                                            'style' => '
                                                max-width:100%;
                                                max-height:45px;
                                                display:inline-block;
                                            ',
                                        ]
                                    ) ?>
                                </td>

                                <td style="
                                    padding:0;
                                    width:50px;
                                    height:23px;
                                    font-weight:bold;
                                    text-align:center;
                                    vertical-align:middle;
                                    color:#FFFFFF00;
                                ">
                                    SIGNATURE
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:0; width:40px; height:23px;"></td>

                                <td style="
                                    padding:0;
                                    width:300px;
                                    height:23px;
                                    text-align:center;
                                    vertical-align:middle;
                                    font-weight:bold;
                                    color:#FFFFFF00;
                                ">
                                    ISSUED DATE&nbsp;&nbsp;&nbsp;
                                    <?= !empty($model->approved_time)
                                        ? date("Y-m-d", strtotime($model->approved_time))
                                        : ''
                                    ?>
                                </td>

                                <td style="
                                    padding:0;
                                    width:50px;
                                    height:23px;
                                    font-weight:bold;
                                    text-align:center;
                                    vertical-align:middle;
                                    color:#FFFFFF00;
                                ">
                                    SIGNATURE
                                </td>
                            </tr>
                        </table>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
    </table>
</div>