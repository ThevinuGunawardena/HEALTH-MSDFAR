<?php

use backend\config\Constant;
use backend\models\Leave;
use yii\helpers\Html;

/**
 * Leave Receipt — overlays applicant data on the physical receipt form image.
 *
 * Page 1 (front): leave_receipt_front.png
 * Page 2 (back):  leave_receipt_back.png
 *
 * Both images must be placed in:
 *   backend/web/img/leave_receipt_front.png
 *   backend/web/img/leave_receipt_back.png
 *
 * Receipt container: 1150px wide × 334px tall (screen).
 * Print scale: 0.690 (= 794 / 1150) so it fits A4 portrait width.
 *
 * Double-sided alignment:
 *   Front → transform-origin: top left  (anchors to left edge of A4)
 *   Back  → transform-origin: top right (anchors to right edge of A4)
 * When paper is flipped, front and back will be aligned.
 *
 * @var yii\web\View          $this
 * @var backend\models\Leave  $model
 */

// ── Applicant profile (loaded from the leave owner, not the logged-in user) ──
$ownerProfile   = null;
$ownerProfileId = $model->user->profile_id ?? null;
if ($ownerProfileId) {
    $ownerProfile = \backend\models\ProfileOfficers::findOne($ownerProfileId);
}

$roleName = isset(Constant::$userTypes[(int)$model->user_type]['name'])
    ? Constant::$userTypes[(int)$model->user_type]['name']
    : '—';

$applicantName = $ownerProfile
    ? trim($ownerProfile->first_name . ' ' . $ownerProfile->last_name)
    : ($model->user->name ?? $model->nic);

$designation = ($ownerProfile && !empty($ownerProfile->current_designation))
    ? $ownerProfile->current_designation
    : $roleName;

$signatureFile = $ownerProfile->signature ?? '';

// ── Acting officer profile (for signature + agreed date on back page) ─────────
$actingProfile       = null;
$actingSignatureFile = '';
$actingName          = $model->acting_officer ?? '';
$actingAgreedDate    = '';

if (!empty($model->acting_officer_user_id)) {
    $actingUser = \common\models\User::findOne((int) $model->acting_officer_user_id);
    if ($actingUser && $actingUser->profile_id) {
        $actingProfile = \backend\models\ProfileOfficers::findOne($actingUser->profile_id);
        if ($actingProfile) {
            $actingSignatureFile = $actingProfile->signature ?? '';
            $actingName = trim($actingProfile->first_name . ' ' . $actingProfile->last_name);
        }
    }
}
if (!empty($model->acting_officer_agreed_at)) {
    $actingAgreedDate = date('Y-m-d', strtotime($model->acting_officer_agreed_at));
}

// ── Approver profile (Head of Department signature on back page) ──────────────
// The Director / DG who approved the leave: approved_by → User → profile → signature.
$approverSignatureFile = '';
$approverName          = '';
if (!empty($model->approved_by)) {
    $approverUser = \common\models\User::findOne((int) $model->approved_by);
    if ($approverUser && $approverUser->profile_id) {
        $approverProfile = \backend\models\ProfileOfficers::findOne($approverUser->profile_id);
        if ($approverProfile) {
            $approverSignatureFile = $approverProfile->signature ?? '';
            $approverName = trim($approverProfile->first_name . ' ' . $approverProfile->last_name);
        }
    }
}

// ── Date helpers ──────────────────────────────────────────────────────────────
$dateParts = function ($dateStr) {
    if (empty($dateStr)) {
        return ['', '', ''];
    }
    $ts = strtotime($dateStr);
    return [
        date('Y', $ts),
        date('m', $ts),
        date('d', $ts),
    ];
};

[$faY, $faM, $faD] = $dateParts($model->first_appointment_date);
[$csY, $csM, $csD] = $dateParts($model->start_date);
[$rdY, $rdM, $rdD] = $dateParts($model->resume_date);

// ── Days-applied columns (C / V / O) ─────────────────────────────────────────
$daysC = '';
$daysV = '';
$daysO = '';
$days  = rtrim(rtrim(number_format((float) $model->total_days, 2), '0'), '.');

if ($model->leave_type === Leave::LEAVE_TYPE_CASUAL) {
    $daysC = $days;
} elseif ($model->leave_type === Leave::LEAVE_TYPE_ANNUAL) {
    $daysV = $days;
} elseif (in_array($model->leave_type, [Leave::LEAVE_TYPE_HALF_DAY])) {
    if ($model->deducted_from === Leave::LEAVE_TYPE_CASUAL) {
        $daysC = $days;
    } elseif ($model->deducted_from === Leave::LEAVE_TYPE_ANNUAL) {
        $daysV = $days;
    } else {
        $daysO = $days;
    }
} else {
    $daysO = $days;
}

// ── Leave taken this year ─────────────────────────────────────────────────────
$takenC = rtrim(rtrim(number_format((float) $model->taken_casual,   2), '0'), '.') ?: '0';
$takenV = rtrim(rtrim(number_format((float) $model->taken_vacation, 2), '0'), '.') ?: '0';
$takenO = rtrim(rtrim(number_format((float) $model->taken_other,    2), '0'), '.') ?: '0';

// ── Approval decision text ────────────────────────────────────────────────────
$isApproved      = $model->status === Leave::STATUS_APPROVED;
$isPendingStage  = $model->status === Leave::STATUS_PENDING;     // Director reviewing, not yet approved
$isPendingCcStage = $model->status === Leave::STATUS_PENDING_CC; // CC reviewing
$allowedText     = $isApproved ? 'Allowed' : 'Not Allowed';

// ── CC profile (Supervising Officer signature on back page) ───────────────────
$ccSignatureFile = '';
$ccName          = '';
if (!empty($model->cc_recommended_by)) {
    $ccUser = \common\models\User::findOne((int) $model->cc_recommended_by);
    if ($ccUser && $ccUser->profile_id) {
        $ccProfile = \backend\models\ProfileOfficers::findOne($ccUser->profile_id);
        if ($ccProfile) {
            $ccSignatureFile = $ccProfile->signature ?? '';
            $ccName = trim($ccProfile->first_name . ' ' . $ccProfile->last_name);
        }
    }
}

// Additional Director (3) / IT Director (15) requests never pass through a
// CC, so the Supervising Officer line is genuinely Not Applicable and is
// marked "N/A" on the form. A missing CC signature on ANY OTHER request is
// missing data — not N/A — so that case is deliberately left blank.
$ccNotApplicable = $model->isCcExempt();

// KKS (21) requests have no acting-officer stage at all, so those lines are
// genuinely Not Applicable and are marked "N/A" on the form. A blank acting
// officer on ANY OTHER request is missing data — not N/A — so that case is
// deliberately left blank, matching the CC rule above.
//
// Historical KKS records raised before the acting-officer stage was removed
// still carry a real name and signature; those are printed as normal, since
// "N/A" is only used where nothing was ever recorded.
$actingNotApplicable = ((int) $model->user_type === Constant::KKS);

// ── Application date ──────────────────────────────────────────────────────────
$appDate = $model->created_at ? date('Y-m-d', strtotime($model->created_at)) : date('Y-m-d');

// ── Image base path ───────────────────────────────────────────────────────────
$imgBase = Yii::$app->request->baseUrl . '/img/';
$imgUrl = Yii::getAlias('@web') . '/img/graphic-signature-style.png';
?>
<!DOCTYPE html>
<html lang="en" class="size-a5">

<head>
    <meta charset="utf-8">
    <title><?= Yii::t('app', 'Leave Receipt') ?> — <?= Html::encode($applicantName) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* ── Reset ─────────────────────────────────────────────── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #ccc;
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #111;
        }

        /* ── Print button ────────────────────────────────────────── */
        .no-print {
            text-align: center;
            padding: 18px 0 10px;
        }

        /* Generic .no-print button styles intentionally removed: the only
           buttons in .no-print are the print-size tiles below, fully styled
           by .size-btn. A generic `.no-print button` rule is MORE specific
           than `.size-btn`, so it silently overrode the tiles (e.g. forcing
           the A4 tile blue with unreadable text). */

        /* ── Print size choice (A5 recommended / A4) ─────────────── */
        .print-choose {
            text-align: center;
        }

        .print-choose .eyebrow {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .size-options {
            display: inline-flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .size-btn {
            position: relative;
            width: 172px;
            text-align: left;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px 16px 14px;
            cursor: pointer;
            font-family: inherit;
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }

        .size-btn:hover {
            border-color: #c3ccd8;
            box-shadow: 0 6px 16px rgba(17, 24, 39, .09);
            transform: translateY(-1px);
        }

        .size-btn:focus-visible {
            outline: 2px solid #1a56db;
            outline-offset: 2px;
        }

        .size-btn.a5 {
            border-color: #1a56db;
            background: #f5f8ff;
        }

        .size-btn.a5:hover {
            box-shadow: 0 8px 20px rgba(26, 86, 219, .18);
        }

        .size-btn .pill {
            position: absolute;
            top: -10px;
            left: 14px;
            background: #dcfce7;
            color: #166534;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 2px 9px;
            border-radius: 999px;
        }

        .size-row {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .size-btn .glyph {
            flex: none;
            background: #fff;
            border: 1.5px solid #9aa4b2;
            border-radius: 2px;
        }

        .size-btn.a5 .glyph {
            width: 28px;
            height: 19px;
            border-color: #1a56db;
        }

        /* landscape */
        .size-btn.a4 .glyph {
            width: 19px;
            height: 26px;
        }

        /* portrait  */
        .size-name {
            font-size: 20px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
        }

        .size-btn.a5 .size-name {
            color: #1a3fb0;
        }

        .size-sub {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
        }

        .size-action {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 12px;
            font-size: 13px;
            font-weight: 700;
            color: #4b5563;
        }

        .size-btn.a5 .size-action {
            color: #1a56db;
        }

        .print-hint {
            color: #374151;
            font-size: 12px;
            margin-top: 16px;
        }

        /* ── Screen: page-sheet is invisible on screen ───────────── */
        .page-sheet {
            display: contents;
        }

        /* ── Screen: receipt container ───────────────────────────── */
        .receipt-page {
            position: relative;
            width: 1150px;
            height: 334px;
            margin: 0 auto 30px;
            background: #fff;
        }

        .receipt-page img.bg {
            display: block;
            width: 100%;
            height: auto;
        }

        /* ── Overlay text spans ──────────────────────────────────── */
        .ov {
            position: absolute;
            font-size: 15px;
            color: #000;
            white-space: nowrap;
            line-height: 1;
        }

        .ov.small {
            font-size: 11px;
        }

        .ov.center {
            text-align: center;
        }

        .ov.wrap {
            white-space: normal;
            line-height: 1.35;
        }

        /* ════════════════════════════════════════════════════════════
           TEMPORARY DEBUG BORDER — shows the exact box of every overlay
           field so you can adjust width / height / top / left visually.
           DELETE this whole block once the sizes are correct.
           (It also prints, so remove it before real printing.)
           ════════════════════════════════════════════════════════════ */
        /*.ov {
            border: 1px solid red;
            background: rgba(255, 0, 0, 0.06);
        }*/
        /* ════════════════ END TEMPORARY DEBUG BORDER ════════════════ */

        /* ── Strike-through line overlay ─────────────────────────── */
        /* A thin black bar drawn over the wording that does not apply
           (e.g. "not allowed", "Not recommended") to mimic a manual
           pen strike on the paper form. Positioned absolutely like .ov.
           Uses border-top (not background) because browsers strip
           background colors when printing but keep borders. */
        .strike {
            position: absolute;
            height: 0;
            border-top: 1px solid #000;
            transform-origin: left center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Print styles ────────────────────────────────────────── */
        /*
         * A4 portrait = 794px × 1123px at 96dpi.
         * Receipt width = 1150px.
         * Scale = 794 / 1150 = 0.690.
         *
         * Each .page-sheet is exactly one A4 page (794×1123px).
         * page-break-after: always on .page-sheet forces Chrome/Edge
         * to start a new page after each wrapper — this is more
         * reliable than putting page-break on position:absolute children.
         *
         * Inside each sheet the receipt is position:absolute so left/top
         * are relative to the A4 page itself, not to surrounding elements.
         *
         * Front: transform-origin top left  → scales inward from left edge
         * Back:  transform-origin top right → scales inward from right edge
         *        left = -(1150 - 794) = -356px moves element so its right
         *        edge sits exactly at 794px (A4 right edge) before scaling.
         */
        /* @page size is injected at print time by JS (#dynamicPage) so
           the user can choose A5 landscape (default, recommended) or
           A4 portrait. Both share the same 794px width, so the receipt
           scale below is unchanged for either. */

        @media print {

            html,
            body {
                margin: 0;
                padding: 0;
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            <?php if (!$isApproved): ?>

            /* Not yet approved — block Ctrl+P printing entirely */
            .page-sheet {
                display: none !important;
            }

            <?php endif; ?>

            /* One sheet per page. A4 portrait and A5 landscape share the
               same 794px width, so only the sheet HEIGHT changes. */
            .page-sheet {
                display: block;
                position: relative;
                width: 794px;
                page-break-after: always;
                page-break-inside: avoid;
                overflow: hidden;
            }

            html.size-a4 .page-sheet {
                height: 1123px;
            }

            /* full A4 portrait   */
            html.size-a5 .page-sheet {
                height: 559px;
            }

            /* A5 landscape = ½ A4 */
            .page-sheet:last-of-type {
                page-break-after: avoid;
            }

            /* reduce top spacing on both pages */
            .receipt-page {
                position: absolute;
                width: 1150px;
                height: 334px;
                top: 20px;
                /* ← was 60px, now much smaller */
                margin: 0;
                transform: scale(0.690);
                page-break-after: unset;
            }

            /* Front: small margin from left edge */
            #page-front {
                left: 0;
                transform-origin: top left;
            }

            /* Back: same small margin from right edge
            left = -(1150 - 794 + 20) = -376px */
            #page-back {
                left: -360px;
                transform-origin: top right;
            }

            /* ── A5-ONLY adjustment ───────────────────────────────────────
               A4 is left at the original size above (it prints fine).
               At full scale the form's outer box is ~203mm wide, but A5's
               printer reserves a wider non-printable margin, so the box is
               too wide to fit and the left border line lands in the dead
               zone. For A5 ONLY: shrink ~2.6% (visually near-identical) and
               centre the form so every border line sits ~6mm inside the
               paper. Front/back stay edge-registered for the duplex flip. */
            html.size-a5 .receipt-page {
                transform: scale(0.672);
            }

            html.size-a5 #page-front {
                left: 15px;
            }

            html.size-a5 #page-back {
                left: -371px;
            }
        }
    </style>

    <!-- @page size is swapped here by printReceipt(); default = A5 landscape -->
    <style id="dynamicPage">
        @page {
            size: A5 landscape;
            margin: 0;
        }
    </style>
</head>

<body>

    <div class="no-print">
        <?php if ($isApproved): ?>
            <div class="print-choose">
                <div class="eyebrow"><?= Yii::t('app', 'Choose paper size') ?></div>
                <div class="size-options">
                    <button type="button" class="size-btn a5" onclick="printReceipt('a5')">
                        <span class="pill"><?= Yii::t('app', 'Recommended') ?></span>
                        <span class="size-row">
                            <span class="glyph"></span>
                            <span class="size-name">A5</span>
                        </span>
                        <span class="size-sub"><?= Yii::t('app', 'Landscape · half sheet') ?></span>
                        <span class="size-action">🖨️ <?= Yii::t('app', 'Print') ?></span>
                    </button>
                    <button type="button" class="size-btn a4" onclick="printReceipt('a4')">
                        <span class="size-row">
                            <span class="glyph"></span>
                            <span class="size-name">A4</span>
                        </span>
                        <span class="size-sub"><?= Yii::t('app', 'Portrait · full sheet') ?></span>
                        <span class="size-action">🖨️ <?= Yii::t('app', 'Print') ?></span>
                    </button>
                </div>
                <p class="print-hint"><?= Yii::t('app', 'A5 is half of an A4 sheet — same receipt, far less wasted paper.') ?></p>
            </div>
        <?php elseif ($isPendingStage): ?>
            <p style="color:#1e40af; background:#eff6ff; border:1px solid #3b82f6; display:inline-block; padding:8px 18px; border-radius:5px; font-size:0.9rem;">
                👁 <?= Yii::t('app', 'Preview only — printing will be available once you approve this leave request.') ?>
            </p>
        <?php else: ?>
            <p style="color:#9a3412; background:#fff7ed; border:1px solid #f97316; display:inline-block; padding:8px 18px; border-radius:5px; font-size:0.9rem;">
                <?= Yii::t('app', 'Printing will be available once the Director approves this leave request.') ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- ══════════════════════════════════════════════════════════════════
     PAGE 1 — FRONT
     ══════════════════════════════════════════════════════════════════ -->
    <div class="page-sheet">
        <div class="receipt-page" id="page-front">
            <img class="bg" src="<?= $imgBase ?>leave_receipt_front.png" alt="Leave Receipt Front">

            <!-- ── Name ─────────────────────────────── top-left blank area -->
            <!-- wrap + height keeps long names inside the box instead of overflowing -->
            <span class="ov wrap" style="top:47px; left:90px; width:385px; height:32px; overflow:hidden;">
                <?= Html::encode($applicantName) ?>
            </span>

            <!-- ── Designation ──────────────────────── second row left -->
            <span class="ov wrap" style="top:98px; left:130px; width:345px; height:40px; overflow:hidden;">
                <?= Html::encode($designation) ?>
            </span>

            <!-- ── Ministry / Dept ──────────────────── third row left -->
            <span class="ov wrap" style="top:156px; left:200px; width:275px; height:30px; overflow:hidden;">
                <?= Html::encode($model->ministry_dept ?? '—') ?>
            </span>

            <!-- ── Days applied for — C / V / O ─────── fourth row, three cells -->
            <span class="ov center" style="top:228px; left:290px; width:55px;"><?= Html::encode($daysC) ?></span>
            <span class="ov center" style="top:228px; left:350px; width:58px;"><?= Html::encode($daysV) ?></span>
            <span class="ov center" style="top:228px; left:415px; width:58px;"><?= Html::encode($daysO) ?></span>

            <!-- ── Leave taken this year — C / V / O ── fifth row -->
            <span class="ov center" style="top:270px; left:290px; width:55px;"><?= Html::encode($takenC) ?></span>
            <span class="ov center" style="top:270px; left:350px; width:58px;"><?= Html::encode($takenV) ?></span>
            <span class="ov center" style="top:270px; left:415px; width:58px;"><?= Html::encode($takenO) ?></span>

            <!-- ── Date (bottom left) ───────────────── last row -->
            <span class="ov" style="top:308px; left:190px; width:100px;">
                <?= Html::encode($appDate) ?>
            </span>

            <!-- ── Date of First Appointment — Y / M / D ── right section row 1 -->
            <span class="ov center" style="top:78px; left:718px; width:50px;"><?= Html::encode($faY) ?></span>
            <span class="ov center" style="top:78px; left:768px; width:42px;"><?= Html::encode($faM) ?></span>
            <span class="ov center" style="top:78px; left:810px; width:42px;"><?= Html::encode($faD) ?></span>

            <!-- ── Date of commencing leave — Y / M / D ── right section row 2 -->
            <span class="ov center" style="top:125px; left:718px; width:50px;"><?= Html::encode($csY) ?></span>
            <span class="ov center" style="top:125px; left:768px; width:42px;"><?= Html::encode($csM) ?></span>
            <span class="ov center" style="top:125px; left:810px; width:42px;"><?= Html::encode($csD) ?></span>

            <!-- ── Date of resuming duties — Y / M / D ── right section row 3 -->
            <span class="ov center" style="top:180px; left:718px; width:50px;"><?= Html::encode($rdY) ?></span>
            <span class="ov center" style="top:180px; left:768px; width:42px;"><?= Html::encode($rdM) ?></span>
            <span class="ov center" style="top:180px; left:810px; width:42px;"><?= Html::encode($rdD) ?></span>

            <!-- ── Reasons for leave ────────────────── right section row 4 -->
            <span class="ov wrap" style="top:226px; left:640px; width:212px; height:48px; overflow:hidden;">
                <?= Html::encode($model->reason ?? '') ?>
            </span>

            <!-- ── Signature of Applicant ───────────── bottom right of front -->
            <span class="ov" style="top:278px; left:740px; width:130px;">
                <?php if (!empty($signatureFile)): ?>
                    <img src="<?= Constant::$FILE_VIEW_PATH ?>officer/signature/<?= Html::encode($signatureFile) ?>"
                        alt="<?= Yii::t('app', 'Signature') ?>"
                        style="max-height:25px; max-width:200px; vertical-align:middle;">

                <?php else: ?>
                    <?= Html::encode($applicantName) ?>
                <?php endif; ?>
            </span>


        </div>
    </div>


    <!-- ══════════════════════════════════════════════════════════════════
     PAGE 2 — BACK
     ══════════════════════════════════════════════════════════════════ -->
    <div class="page-sheet">
        <div class="receipt-page" id="page-back">
            <img class="bg" src="<?= $imgBase ?>leave_receipt_back.png" alt="Leave Receipt Back">

            <!-- ── Acting Officer name ───────────────── "Officer Acting" label area -->
            <span class="ov wrap" style="top:117px; left:50px; width:360px; height:50px; overflow:hidden;">
                <?php if ($actingName !== ''): ?>
                    <?= Html::encode($actingName) ?>
                <?php elseif ($actingNotApplicable): ?>
                    <?= Yii::t('app', 'N/A') ?>
                <?php endif; ?>
            </span>

            <!-- ── Acting Officer signature ─────────── "Signature/Date" cell top -->
            <span class="ov" style="top:88px; left:460px; width:90px;">
                <?php if (!empty($actingSignatureFile)): ?>
                    <img src="<?= Constant::$FILE_VIEW_PATH ?>officer/signature/<?= Html::encode($actingSignatureFile) ?>"
                        alt="<?= Yii::t('app', 'Signature') ?>"
                        style="max-height:25px; max-width:200px; vertical-align:middle;">
                <?php elseif ($actingName !== ''): ?>
                    <span style="font-size:13px;"><?= Html::encode($actingName) ?></span>
                <?php elseif ($actingNotApplicable): ?>
                    <span style="font-size:15px;"><?= Yii::t('app', 'N/A') ?></span>
                <?php endif; ?>
            </span>

            <!-- ── Acting Officer agreed date ────────── "Signature/Date" cell bottom -->
            <span class="ov" style="top:95px; left:525px; width:80px; font-size:13px;">
                <?= Html::encode($actingAgreedDate) ?>
            </span>

            <?php if ($isApproved): ?>
                <!-- ════════════════════════════════════════════════════════════
         STRIKE-THROUGH LINES (approved → cross out the negatives).
         The form already prints "Leave allowed/not allowed" and
         "Recommended/Not recommended" — we only strike the negatives.
         Adjust top/left/width after a test print to sit on the words.
         ════════════════════════════════════════════════════════════ -->

                <!-- Strike: "අනුමත කරනු" (Sinhala, top line of the cell) -->
                <span class="strike" style="top:38px; left:812px; width:100px;"></span>

                <!-- Strike: "නොලැබේ" (Sinhala, top line of the cell) -->
                <span class="strike" style="top:53px; left:610px; width:80px;"></span>

                <!-- Strike: "அனுமதி இல்லை" (Tamil, middle line) -->
                <span class="strike" style="top:76px; left:685px; width:136px;"></span>

                <!-- Strike: "not allowed" (English, bottom line) -->
                <span class="strike" style="top:100px; left:720px; width:90px;"></span>
            <?php endif; ?>

            <!-- Strike: "නොකරමි / பரிந்துரைக்கப்படவில்லை / Not recommended" left column -->
            <!-- Shown in both PENDING (Director preview) and APPROVED stages —
         CC has already recommended at this point. -->
            <?php if ($isApproved || $isPendingStage): ?>
                <span class="strike" style="top:193px; left:143px; width:75px;"></span>
                <span class="strike" style="top:206px; left:155px; width:135px;"></span>
                <span class="strike" style="top:226px; left:150px; width:135px;"></span>
            <?php endif; ?>

            <!-- ── Address when on leave ─────────────── bottom-left row 3 -->
            <?php if ($isApproved || $isPendingStage || $isPendingCcStage): ?>
                <span class="ov wrap" style="top:250px; left:208px; width:202px; height:75px; line-height:1.3; overflow:hidden;">
                    <?= Html::encode($model->leave_address ?? '') ?>
                </span>
            <?php endif; ?>

            <!-- ── CC signature (Supervising Officer) ──── middle column, bottom dotted line
         Shown in both PENDING and APPROVED stages. -->
            <?php if (($isApproved || $isPendingStage) && !empty($ccSignatureFile)): ?>
                <span class="ov" style="top:195px; left:475px; width:150px;">
                    <img src="<?= Constant::$FILE_VIEW_PATH ?>officer/signature/<?= Html::encode($ccSignatureFile) ?>"
                        alt="<?= Yii::t('app', 'Supervisor Signature') ?>"
                       style="max-height:30px; max-width:200px; vertical-align:middle;">
                </span>
            <?php elseif (($isApproved || $isPendingStage) && !empty($ccName)): ?>
                <span class="ov" style="top:210px; left:425px; width:220px; font-size:13px;">
                    <?= Html::encode($ccName) ?>
                </span>
            <?php elseif (($isApproved || $isPendingStage) && $ccNotApplicable): ?>
                <!-- AD / ITD: no CC stage exists for this request. -->
                <span class="ov center" style="top:210px; left:425px; width:220px; font-size:13px;">
                    <?= Yii::t('app', 'N/A') ?>
                </span>
            <?php endif; ?>

            <!-- ── Head of Department signature (approver) ── right column, top dotted line
         APPROVED only — not shown in Director preview. -->
            <?php if ($isApproved && !empty($approverSignatureFile)): ?>
                <span class="ov" style="top:30px; left:1020px; width:160px;">
                    <img src="<?= Constant::$FILE_VIEW_PATH ?>officer/signature/<?= Html::encode($approverSignatureFile) ?>"
                        alt="<?= Yii::t('app', 'Approver Signature') ?>"
                        style="max-height:20px; max-width:200px; vertical-align:middle;">
                </span>
            <?php endif; ?>

            <!-- ── Applicant signature ─────────────────── right column, middle dotted line
         APPROVED only — not shown in Director preview. -->
            <?php if ($isApproved): ?>
                <span class="ov" style="top:120px; left:1020px; width:160px;">
                    <?php if (!empty($signatureFile)): ?>
                        <img src="<?= Constant::$FILE_VIEW_PATH ?>officer/signature/<?= Html::encode($signatureFile) ?>"
                            alt="<?= Yii::t('app', 'Applicant Signature') ?>"
                            style="max-height:25px; max-width:200px; vertical-align:middle;">
                    <?php else: ?>
                        <span style="font-size:13px;"><?= Html::encode($applicantName) ?></span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>

            <!-- Leave Clerk → manual pen sign-off -->
        </div>
    </div>

    <script>
        // Print-size selector. A5 landscape (half an A4) is the recommended
        // default; A4 portrait is offered with a paper-waste warning.
        var A4_WARNING = <?= json_encode(Yii::t('app', 'Printing on A4 leaves a large blank area on the sheet and wastes paper. A5 is recommended for this receipt. Print on A4 anyway?'), JSON_UNESCAPED_UNICODE) ?>;

        function applyPrintSize(size) {
            var page = document.getElementById('dynamicPage');
            var html = document.documentElement;
            if (size === 'a4') {
                page.textContent = '@page { size: A4 portrait; margin: 0; }';
                html.classList.remove('size-a5');
                html.classList.add('size-a4');
            } else {
                page.textContent = '@page { size: A5 landscape; margin: 0; }';
                html.classList.remove('size-a4');
                html.classList.add('size-a5');
            }
        }

        function printReceipt(size) {
            if (size === 'a4' && !window.confirm(A4_WARNING)) {
                return; // cancelled → keep A5 as the default
            }
            applyPrintSize(size);
            window.print();
        }
    </script>
</body>

</html>