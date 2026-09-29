<?php

use backend\config\Constant;
use backend\models\BoatNumberOwnersLog;
use backend\models\MApprovalWorkflow;
use backend\services\CommonService;
use Mpdf\QrCode\Output;
use Mpdf\QrCode\QrCode;

$webURL = Yii::getAlias('@web');
$boatNumber = $model->boatNumber;
$boatDesign = $model->boatNumber->boatDesign;
$fishermen = $model->fisherman;
$isPdf = (bool) ($isPdf ?? false);
$officer = $officer ?? [];
$hasOfficerSignature =
    (bool) ($hasOfficerSignature ?? false);
$hasFishermanSignature =
    (bool) ($hasFishermanSignature ?? false);
if ($model->transered_license == 1) {
    $previousOwner = BoatNumberOwnersLog::find()->where(["boat_number" => $boatNumber->boat_number])
        ->orderBy(['id' => SORT_DESC])
        ->one();
}
$approvalWorkflow = MApprovalWorkflow::findOne(['type' => $model->renew == 1 ? "BOAT_REGISTER_ReNEW" : "BOAT_REGISTER"]);
$hex = bin2hex('{"type":"BOAT_REGISTER","id":"' . $model->nid . '"}');
$qrCode = new QrCode(Constant::$BASEURL . "/site/license-validation?token=" . $hex);
$output = new Output\Svg();
$outputCode = str_replace('<?xml version="1.0"?>', '', $output->output($qrCode, 110, 'white', 'black'));

if ($model->renew == 1 && $model->transered_license == 1) {
    $renewLabel = "[Transfer]";
    $showExpire = false;
} else if ($model->renew == 1) {
    $renewLabel = "[Renew]";
    $showExpire = true;

} else {
    $renewLabel = "";
    $showExpire = false;
}

/*
 * Image source resolver.
 *
 * Browser view:
 *   /files/static/image.jpg
 *
 * PDF view:
 *   file:///var/mountpoint/uploads/static/image.jpg
 *
 * Local file paths keep the generated HTML small and avoid the
 * mPDF pcre.backtrack_limit error caused by Base64 data URIs.
 */
if (!isset($imageSrc) || !$imageSrc instanceof \Closure) {
    $imageSrc = static function (
        string $relativePath
    ) use ($isPdf): string {
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

        if (!$isPdf) {
            $encodedPath = implode(
                '/',
                array_map(
                    'rawurlencode',
                    explode('/', $relativePath)
                )
            );

            return rtrim(
                Constant::$FILE_VIEW_PATH,
                '/'
            ) . '/' . $encodedPath;
        }

        $uploadRoot = realpath(
            '/var/mountpoint/uploads'
        );

        if ($uploadRoot === false) {
            Yii::error(
                'Uploads directory was not found.',
                __FILE__
            );

            return '';
        }

        $requestedPath = $uploadRoot
            . DIRECTORY_SEPARATOR
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $relativePath
            );

        $realPath = realpath($requestedPath);

        if (
            $realPath === false
            || !is_file($realPath)
            || !is_readable($realPath)
        ) {
            Yii::warning(
                'Licence image is missing or unreadable: '
                . $requestedPath,
                __FILE__
            );

            return '';
        }

        $allowedPrefix = $uploadRoot
            . DIRECTORY_SEPARATOR;

        if (!str_starts_with($realPath, $allowedPrefix)) {
            Yii::warning(
                'Blocked licence image path: ' . $realPath,
                __FILE__
            );

            return '';
        }

        return 'file:///' . ltrim(
            str_replace('\\', '/', $realPath),
            '/'
        );
    };
}

$uploadImageSrc = static function (
    string $relativePath
) use ($imageSrc): string {
    return $imageSrc($relativePath);
};

$licenseImageSrc = static function (
    string $filename
) use ($imageSrc): string {
    return $imageSrc(
        'static/' . basename($filename)
    );
};

$officerSignatureFilename = basename(
    (string) ($officer['signature'] ?? '')
);

$officerSignatureSrc = $officerSignatureFilename !== ''
    ? $uploadImageSrc(
        'officer/signature/'
        . $officerSignatureFilename
    )
    : '';

$fishermanSignatureFilename = basename(
    (string) ($fishermen?->signature ?? '')
);

$fishermanSignatureSrc = $fishermanSignatureFilename !== ''
    ? $uploadImageSrc(
        'fisherman/'
        . $fishermanSignatureFilename
    )
    : '';
?>


<div class="">
    <div class="page" style="
    /*margin: 1cm;*/
        /*width: 19.0cm;*/
        /*height: 28.7cm;*/
        border: 1px solid black; padding: 2px; font-size: 10px;
        padding: 1cm;
position: relative"
    >
        <div style="text-align: center ;width: 760px;" id="license_header">

            <table>
                <tr>
                    <td style="width: 80%">
                        <div id="license_header">
                            <p><img style="width: 50px ; " src="<?= htmlspecialchars($licenseImageSrc('national_Logo2.jpg'), ENT_QUOTES, 'UTF-8') ?>">
                                <img style="width: 400px" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_1.jpg'), ENT_QUOTES, 'UTF-8') ?>"></p>
                        </div>
                    </td>
                    <td style="width: 20%">

                        <div>
                            <?= $outputCode ?>
                        </div>
                    </td>
                </tr>
            </table>

        </div>

        <!--        <div style="position: absolute; text-align: center;    right: 15px;    top: 15px;">-->
        <!--            <h5>--><?php //= $boatNumber->boat_number ?><!--</h5>-->
        <!--        </div>-->
        <div style="    font-size: 12px;    line-height: 5px;    text-align: center;">
            <p><img style="height: 20px;width: auto"
                    src="<?= htmlspecialchars($licenseImageSrc('boatlicense_28.jpg'), ENT_QUOTES, 'UTF-8') ?>"><strong><?= $renewLabel ?></strong>
            </p>
        </div>
        <div>
            <table style="border-collapse: collapse">
                <tr>
                    <th style="border: 1px solid black; padding: 2px; font-size: 10px">1</th>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_2.jpg'), ENT_QUOTES, 'UTF-8') ?>">
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $model->boatNumber->boat_number ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $model->boatNumber->boat_number ?></td>


                </tr>
                <tr style="height: 30px;">
                    <th style="border: 1px solid black; padding: 2px; font-size: 10px;">2</th>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_3.jpg'), ENT_QUOTES, 'UTF-8') ?>">
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $model->date_of_first_registration ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $model->boatNumber->boat_number ?></td>


                </tr>
                <tr>
                    <th style="border: 1px solid black; padding: 2px; font-size: 10px">3</th>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="5"><img
                                style="height: 12px;width: auto"
                                src="<?= htmlspecialchars($licenseImageSrc('boatlicense_4.jpg'), ENT_QUOTES, 'UTF-8') ?>">
                    </td>


                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="3"><img
                                style="height:50px;width: auto"
                                src="<?= htmlspecialchars($licenseImageSrc('boatlicense_5.jpg'), ENT_QUOTES, 'UTF-8') ?>">
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="1"><?= $boatDesign->hull_material != 0 ? Constant::$hullMaterials[$boatDesign->hull_material] : "" ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="1"><img
                                style="height:50px;width: auto"
                                src="<?= htmlspecialchars($licenseImageSrc('boatlicense_6.jpg'), ENT_QUOTES, 'UTF-8') ?>">
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="1"><?= $model->date_of_construction ?></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="3">
                        <img style="height: 40px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_7.jpg'), ENT_QUOTES, 'UTF-8') ?>">
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="1"><?= $boatNumber->boatType->code ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="1">
                        <img style="height: 40px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_8.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="1"><?= $boatDesign->length ?>
                    </td>
                </tr>

                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" rowspan="3"></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" rowspan="2">
                        <img style="height: 90px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_9.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="4">
                        <img style="height: 30px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_10.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">
                        <img style="height: 50px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_11.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">
                        <img style="height: 50px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_12.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">
                        <img style="height: 50px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_13.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">
                        <img style="height: 50px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_14.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>

                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"> <?= $model->how_propelled ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= $model->engine_serial_number ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= Constant::$engineMake[$model->engine_make] ?? "" ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= $model->engine_horsepower ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= $model->fuel_type ?></td>

                </tr>

                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_15.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_16.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size:
                    10px"><?= $model->boatNumber->hull_number ?? "" ?></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_17.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= $model->fishing_equipment ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 40px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_18.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"><?= $model->navigation_equipment ?>
                        / <?= $model->communication_equipment ?></td>
                </tr>
            </table>

            <div style="    font-size: 12px;    line-height: 5px;    text-align: center;margin-top: 12px">
                <p>
                    <img style="height: 20px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_19.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                </p>
            </div>
            <table style="width: 100%; border-collapse: collapse">
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">1./2.</td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 30px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_20.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 30px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_21.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 30px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_22.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>


                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $fishermen->preferred_name_for_id ?>
                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $fishermen->nic ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $fishermen->permanent_address ?>.
                    </td>


                </tr>
                <?php if ($model->transered_license == 1) { ?>
                    <tr>
                        <td style="border: 1px solid black; padding: 2px; font-size: 10px">1./3.</td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                            <strong>Previous owner's name</strong>
                        </td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                            NIC

                        </td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">

                        </td>


                    </tr>
                    <tr>
                        <td style="border: 1px solid black; padding: 2px; font-size: 10px"></td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                            colspan="2"><?= $previousOwner->name ?? "" ?>
                        </td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                            colspan="2"><?= $previousOwner->nic ?? "" ?></td>
                        <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        </td>


                    </tr>
                <?php } ?>

                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px">3</td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 30px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_23.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="4"><?= $model->insurance_no ?></td>


                </tr>
                <tr>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                        <img style="height: 30px;width: auto" src="<?= htmlspecialchars($licenseImageSrc('boatlicense_24.jpg'), ENT_QUOTES, 'UTF-8') ?>">

                    </td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px"
                        colspan="2"><?= $model->landingSite->name ?? "" ?></td>
                    <td style="border: 1px solid black; padding: 2px; font-size: 10px" colspan="2">
                    </td>


                </tr>

            </table>
            <div>

            <br>
                <br>
                <br>
                <br>
                <br>


                <br>
                <table style="width: 100%; border: none ;text-align: center">
                    <tbody>
                    <tr>


                        <td style="width: 33.3%;">

                            <p style="text-align: center">
                                <?PHP if ($officer != null && !empty($officer)) { ?>
                                     <?php if (
                                        $officerSignatureFilename !== ''
                                        && (!$isPdf || $hasOfficerSignature)
                                    ): ?>
                                        <img
                                            src="<?= htmlspecialchars(
                                                $officerSignatureSrc,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            style="max-width: 100px; max-height: 50px;"
                                            alt="Officer Signature"
                                        >
                                        <br>
                                    <?php endif; ?>
                                    <br><span
                                            style="font-size: 10px;"> <?= $officer["first_name"] ?>  <?= $officer["last_name"] ?></span>
                                <?PHP } ?>
                                <br> <img style="height: 30px;width: auto"
                                          src="<?= htmlspecialchars($licenseImageSrc('boatlicense_25.jpg'), ENT_QUOTES, 'UTF-8') ?>">
                            </p>
                        </td>

                        <td style="width: 33.3%;"><p style="text-align: center">.............................
                                <br>
                                <img style="height: 30px;width: auto"
                                     src="<?= htmlspecialchars($licenseImageSrc('boatlicense_26.jpg'), ENT_QUOTES, 'UTF-8') ?>">
                            </p>
                        </td>
                        <td style="width: 33.3%;"><p style="text-align: center">
                               <?php if (
                                $fishermanSignatureFilename !== ''
                                && (!$isPdf || $hasFishermanSignature)
                            ): ?>
                                <img
                                    src="<?= htmlspecialchars(
                                        $fishermanSignatureSrc,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    style="max-width: 100px; max-height: 50px;"
                                    alt="Fisherman Signature"
                                >
                            <?php endif; ?>

                                <br>
                                <img style="height: 30px;width: auto"
                                     src="<?= htmlspecialchars($licenseImageSrc('boatlicense_27.jpg'), ENT_QUOTES, 'UTF-8') ?>">
                            </p>
                        </td>
                    </tr>

                    </tbody>
                </table>

                <br>

                <table style="width: 100%; text-align: center">
                    <tbody>
                    <tr style="font-size: 12px">
                        <td style="font-size: 10px">

                            Valid From
                            : <?= date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d', strtotime('-' . $approvalWorkflow->expired_in . ' month', strtotime($model->expire_date)))))); ?>
                            <?php if ($showExpire) { ?> To : <?= date("Y-m-d", strtotime($model->expire_date)) ?>
                            <?php } ?>
                        </td>
                        <td style="text-align: right;">
                            <p style="font-size: 10px">...................................................... <br>Issuer's
                                signature and
                                stamp</p>
                            <p style=" font-size: 8px">(The license is only valid with the issuer's signature and
                                stamp.)</p>

                        </td>

                    </tr>
                    </tbody>
                </table>

            </div>

        </div>

    </div>