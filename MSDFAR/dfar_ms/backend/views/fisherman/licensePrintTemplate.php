<?php

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman[] $fishermen */
/** @var array $renewalByFishermanId */
/** @var bool $pdf */
/** @var \Closure $imageSrc */

use backend\config\Constant;
use yii\helpers\Html;

$isPdf = (bool) ($pdf ?? false);

$boxWidth = 976;
$boxHeight = 639;
// $bgImageUrl = Yii::getAlias('@web') . '/themeAssets/images/Fisherman_Template_Front.png';
if (!isset($imageSrc) || !$imageSrc instanceof \Closure) {
    /*
     * Fallback for normal browser rendering.
     */
    $imageSrc = static function (
        string $relativePath
    ): string {
        return rtrim(
            Constant::$FILE_VIEW_PATH,
            '/'
        ) . '/' . ltrim($relativePath, '/');
    };
}

$bgImageUrl = $imageSrc(
    'static/Fisherman_Template_Front.png'
);



?>

<div class="container">
    <table cellpadding="0"  cellspacing="0" style="width: 100%; table-layout: fixed; border-collapse: collapse;">
        <?php for ($row = 0; $row < 4; $row++): ?>
            <tr>
                <?php for ($col = 0; $col < 2; $col++): ?>
                    <?php
                        $index = ($row * 2) + $col;
                        $model = $fishermen[$index] ?? null;

                        if ($model === null) {
                            echo '<td style="width:' . (int) $boxWidth
                                . 'px; height:' . (int) $boxHeight
                                . 'px; padding:0; margin:0;"></td>';
                            continue;
                        }

                        $renewalModel =
                            $renewalByFishermanId[(string) $model->id] ?? null;

                    /*
                    * Use renewal dates whenever a renewal record exists.
                    * Otherwise use normal fisherman dates.
                    */
                    $dateModel = $renewalModel ?? $model;

                    $issuedDate = $dateModel->approved_time ?? null;
                    $expiryDate = $dateModel->expire_date ?? null;
                        // ✅ mPDF-safe category text
                        $categoryText = '';
                        if ($model) {
                            if ((int) $model->category !== 1) {
                                $categoryText = strtoupper(
                                    ($model->category0->category ?? '') . ' / FISHERMAN'
                                );
                            } else {
                                $categoryText = 'FISHERMAN';
                            }
                        }



                    ?>

                   <?php
                    $safeBgImageUrl = htmlspecialchars(
                        (string) $bgImageUrl,
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
                                <td style="padding:0; width:50px;  height:15px;"></td>
                                <td style=" padding:0; width:300px; height:15px;"></td>
                                <td style=" padding:0; width:150px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                            </tr>
                             <tr>
                                <td style=" padding:0; width:50px;  height:15px;"></td>
                                <td style=" padding:0; width:300px; height:15px;"></td>
                                <td style=" padding:0; width:150px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                            </tr>

                            <!-- Row 1 -->
                            <tr>
                                <td style=" padding:0; width:50px;  height:48px;"></td>
                                <td style=" padding:0; width:336px; height:48px;"></td>
                                <td style=" padding:0; width:150px; height:48px;"></td>
                                <td style=" padding:0; width:239px; height:48px;"></td>
                                <td style=" padding:0; width:239px; height:48px;"></td>
                            </tr>

                            <!-- Row 2 -->
                            <tr>
                                <td style=" padding:0; width:50px;  height:48px;"></td>
                                <td style=" padding:0; width:300px; height:48px;"></td>
                                <td style=" padding:0; width:150px; height:48px;"></td>
                                <td style=" padding:0; width:239px; height:48px;"></td>
                                <td style=" padding:0; width:239px; height:48px;"></td>
                            </tr>

                            <!-- Row 3 -->
                            <tr>
                                <td style=" padding:0; width:50px;  height:55px;"></td>
                                <td style=" padding:0; width:300px; height:55px;"></td>
                                <td style=" padding:0; width:150px; height:55px;"></td>
                                <td style=" padding:0; width:239px; height:55px;"></td>
                                <td style=" padding:0; width:239px; height:55px;"></td>
                            </tr>

                             <tr>
                                <td style=" padding:0; width:50px;  height:15px;"></td>
                                <td rowspan="10" style=" padding: 10px 0 0 0; width:300px; height:398px; vertical-align:top;">

                                <?php
                                $profileFilename = basename(
                                    (string) ($model->profile_image ?? '')
                                );

                                $profileImageSrc = $profileFilename !== ''
                                    ? $imageSrc(
                                        'fisherman/' . $profileFilename
                                    )
                                    : '';
                                ?>

                                <?php if ($profileImageSrc !== ''): ?>
                                    <?= Html::img(
                                        $profileImageSrc,
                                        [
                                            'id' => 'Fisherman',
                                            'alt' => 'Fisherman profile image',
                                            'style' => '
                                                width:310px;
                                                height:310px;
                                                object-fit:cover;
                                                object-position:center;
                                                display:block;
                                            ',
                                        ]
                                    ) ?>
                                <?php endif; ?>
                                </td>
                                <td style=" padding:0; width:150px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:38px; font-size:23px; font-weight:bold; text-align: center;">
                                    <?= $model ? strtoupper($model->fisherman_uid  ?? '') : '' ?>

                                </td>
                            </tr>

                            

                            <!-- Row 4 -->
                            

                            <!-- Row 5 (NAME) -->
                            <tr>
                                <td style=" padding:0; width:50px;  height:48px;"></td>
                                <td style=" padding:0; width:150px; height:48px; font-size:23px; font-weight:bold;">
                                        NAME
                                </td>
                                <td colspan="2" style=" padding:0; width:289px; height:48px; font-size:23px; font-weight:bold;">
                                <?= $model->preferred_name_for_id ?>                                
                            </td>
                            </tr>

                            <!-- Row 6 -->
                            <tr>
                                <td style=" padding:0; width:50px;  height:15px;"></td>
                                <td style=" padding:0; width:150px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                            </tr>

                            <!-- Row 7 (DOB) -->
                            <tr>
                                <td style=" padding:0; width:50px;  height:48px;"></td>
                                <td style=" padding:0; width:150px; height:48px; font-size:23px; font-weight:bold;">
                                    NIC
                                </td>                                
                                 <td colspan="2" style=" padding:0; width:289px; height:48px; font-size:23px; font-weight:bold;">
                                <?= $model->nic ?>                                
                           
                            </td>
                            </tr>

                            <!-- Row 8 (CATEGORY) -->
                           
                            <tr>
                                <td style=" padding:0; width:50px;  height:15px;"></td>
                                <td style=" padding:0; width:150px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                            </tr>


                            <!-- Row 9 (ADDRESS) -->
                            <tr>
                                <td style=" padding:0; width:50px;  height:48px;"></td>
                                <td style=" padding:0; width:150px; height:48px; font-size:23px; font-weight:bold;">
                                    DOB
                                </td>                                
                                 <td colspan="2" style=" padding:0; width:289px; height:48px; font-size:23px; font-weight:bold;">
                                <?= $model->dob ?>                                
                           
                            </td>
                            </tr>


                            <!-- Row 10 -->
                            <tr>
                                <td style=" padding:0; width:50px;  height:15px;"></td>
                                <td style=" padding:0; width:150px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                            </tr>
                            
                            <tr>
                                <td style=" padding:0; width:50px;  height:48px;"></td>
                                <td style=" padding:0; width:150px; height:48px; font-size:23px; font-weight:bold;">
                                    CATEGORY
                                </td>                                
                                 <td colspan="2" style=" padding:0; width:289px; height:48px; font-size:23px; font-weight:bold;">
                                 <?php
                                    if ($model) {
                                            if ((int)($model->category ?? 0) !== 1) {
                                                $categoryText = strtoupper(($model->category0->category ?? '') . ' / FISHERMAN');
                                            } else {
                                                $categoryText = 'FISHERMAN';
                                            }
                                        }
                                    echo $categoryText;

                                   ?>                             
                           
                            </td>
                            </tr>
                            <tr>
                                <td style=" padding:0; width:50px;  height:15px;"></td>
                                <td style=" padding:0; width:150px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                                <td style=" padding:0; width:239px; height:15px;"></td>
                            </tr>
                            <!-- Row 11 -->
                            <tr>
                                <td style=" padding:0; width:50px;  height:48px;"></td>
                                <td style=" padding:0; width:150px; height:48px; font-size:23px; font-weight:bold;">
                                    ADDRESS
                                </td>                                
                                 <td colspan="2" style=" padding:0; width:289px; height:48px; font-size:18px; font-weight:bold;">
                                <?= $model ? strtoupper($model->permanent_address ?? '') : '' ?>
                           
                           
                            </td>
                            </tr>


                            <!-- Row 12 -->
                            <tr>
                                <td style=" padding:0; width:50px;  height:45px;"></td>
                                <td style=" padding:0; width:239px; height:45px;"></td>
                                <td style=" padding:0; width:50px;  height:45px;"></td>
                                <td style=" padding:0; width:239px; height:45px;"></td>
                                <td rowspan="2"
                                            style="
                                                
                                                padding:0;
                                                width:50px;
                                                height:45px;
                                                text-align:center;
                                                vertical-align:middle;
                                            ">

                                        <?php
                                        $signatureFilename = basename(
                                            (string) ($model->signature ?? '')
                                        );

                                        $signatureImageSrc = $signatureFilename !== ''
                                            ? $imageSrc(
                                                'fisherman/' . $signatureFilename
                                            )
                                            : '';
                                        ?>

                                        <?php if ($signatureImageSrc !== ''): ?>
                                            <img
                                                src="<?= Html::encode($signatureImageSrc) ?>"
                                                alt="Fisherman signature"
                                                style="
                                                    max-width:100%;
                                                    max-height:45px;
                                                    width:auto;
                                                    height:auto;
                                                    display:inline-block;
                                                "
                                            >
                                        <?php endif; ?>

                                        </td>
                            </tr>

                            <!-- Row 13 -->
                            
                           
                           <tr>
                                <td style=" padding:0; width:50px;  height:48px;"></td>
                                <td style=" padding:0; width:50px;  height:48px;"></td>
                                <td style=" padding:0; width:50px;  height:48px;"></td>
                                <td style=" padding:0; width:50px;  height:48px;"></td>

                            </tr>

                             <tr>
                                <td style=" padding:0; width:40px;  height:23px;"></td>
                                <td style="
                                    
                                    padding:0;
                                    width:300px;
                                    height:23px;
                                    text-align:center;
                                    vertical-align:middle;
                                    font-weight:bold;
                                ">
                                    ISSUED DATE&nbsp;&nbsp;&nbsp;
                                    <?= !empty($issuedDate)
                                        ? date('Y-m-d', strtotime($issuedDate))
                                        : ''
                                    ?>
                                </td>

                                <td colspan="2" style="
                                    text-align:center;
                                    vertical-align:middle;
                                    font-weight:bold;

                                ">
                                    EXPIRY DATE&nbsp;&nbsp;&nbsp;
                                    <?= !empty($expiryDate)
                                        ? date('Y-m-d', strtotime($expiryDate))
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