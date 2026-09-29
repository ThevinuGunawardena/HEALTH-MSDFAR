<?php

use yii\helpers\Html;

/**
 * Styled HTML email body for leave notifications.
 *
 * @var yii\web\View         $this
 * @var backend\models\Leave $model
 * @var string               $intro          lead paragraph
 * @var array                $badges         list of ['label','bg','text'] badges
 * @var string               $applicantName
 * @var string               $typeLabel      human-readable leave type
 * @var string|null          $ctaUrl         deep link into the system
 * @var string|null          $ctaLabel       button text
 */

// Optional, so any caller that does not pass them still renders.
$ctaUrl   = $ctaUrl   ?? null;
$ctaLabel = $ctaLabel ?? null;

$days  = rtrim(rtrim(number_format((float) $model->total_days, 2), '0'), '.');
$from  = $model->start_date ? date('M d, Y', strtotime($model->start_date)) : '—';
$to    = $model->end_date ? date('M d, Y', strtotime($model->end_date)) : '—';
$resume = $model->resume_date ? date('M d, Y', strtotime($model->resume_date)) : '—';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0; padding:0; background:#f4f5f7; font-family:Arial, Helvetica, sans-serif; color:#333;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                       style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:#1a56db; padding:20px 28px;">
                            <div style="color:#ffffff; font-size:18px; font-weight:bold;">
                                <?= Html::encode(Yii::t('app', 'DFAR Leave Management System')) ?>
                            </div>
                            <div style="color:#cdd9f5; font-size:13px; margin-top:4px;">
                                <?= Html::encode(Yii::t('app', 'Department of Fisheries and Aquatic Resources')) ?>
                            </div>
                        </td>
                    </tr>

                    <!-- Status badges -->
                    <tr>
                        <td style="padding:18px 28px 0;">
                            <?php foreach (($badges ?? []) as $b): ?>
                                <?php if (!empty($b['sep'])): ?>
                                <span style="font-size:13px; color:#6b7280; margin:0 4px;"><?= Html::encode($b['sep']) ?></span>
                                <?php endif; ?>
                            <span style="display:inline-block; background:<?= $b['bg'] ?>; color:<?= $b['text'] ?>;
                                         font-size:13px; font-weight:bold; padding:6px 14px; border-radius:20px; margin-right:6px;">
                                <?= Html::encode($b['label']) ?>
                            </span>
                            <?php endforeach; ?>
                        </td>
                    </tr>

                    <!-- Intro paragraph -->
                    <tr>
                        <td style="padding:16px 28px 0; font-size:14px; line-height:1.6; color:#444;">
                            <?= Html::encode($intro) ?>
                        </td>
                    </tr>

                    <!-- Leave details table -->
                    <tr>
                        <td style="padding:20px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                   style="border:1px solid #e5e7eb; border-radius:6px; border-collapse:separate; overflow:hidden;">
                                <?php
                                $rows = [
                                    Yii::t('app', 'Applicant')    => $applicantName,
                                    Yii::t('app', 'NIC')          => $model->nic,
                                    Yii::t('app', 'Leave Type')   => $typeLabel,
                                    Yii::t('app', 'From')         => $from,
                                    Yii::t('app', 'To')           => $to,
                                    Yii::t('app', 'Total Days')   => $days,
                                    Yii::t('app', 'Resume Date')  => $resume,
                                ];
                                $i = 0;
                                foreach ($rows as $label => $value):
                                    $bg = ($i % 2 === 0) ? '#ffffff' : '#f9fafb';
                                    $i++;
                                ?>
                                <tr style="background:<?= $bg ?>;">
                                    <td style="padding:9px 14px; font-size:13px; color:#6b7280; width:40%; border-bottom:1px solid #f0f0f0;">
                                        <?= Html::encode($label) ?>
                                    </td>
                                    <td style="padding:9px 14px; font-size:13px; color:#111; font-weight:bold; border-bottom:1px solid #f0f0f0;">
                                        <?= Html::encode($value) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (!empty($model->reason)): ?>
                                <tr>
                                    <td style="padding:9px 14px; font-size:13px; color:#6b7280; vertical-align:top;">
                                        <?= Html::encode(Yii::t('app', 'Reason')) ?>
                                    </td>
                                    <td style="padding:9px 14px; font-size:13px; color:#111;">
                                        <?= nl2br(Html::encode($model->reason)) ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </td>
                    </tr>

                    <?php if (!empty($ctaUrl)): ?>
                    <!-- Call to action -->
                    <!-- Table-wrapped and inline-styled: Outlook ignores most
                         CSS on an <a>, so the padding has to sit on a cell. -->
                    <tr>
                        <td style="padding:0 28px 22px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                <tr>
                                    <td align="center" bgcolor="#1a56db"
                                        style="border-radius:6px; background:#1a56db;">
                                        <a href="<?= Html::encode($ctaUrl) ?>"
                                           target="_blank"
                                           style="display:inline-block; padding:11px 26px; font-family:Arial, Helvetica, sans-serif;
                                                  font-size:14px; font-weight:bold; color:#ffffff; text-decoration:none;
                                                  border-radius:6px;">
                                            <?= Html::encode($ctaLabel ?: Yii::t('app', 'Open the leave system')) ?>
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <?php // A real link, not grey text. Outlook and some corporate
                                  // filters flatten a styled button into plain words, which
                                  // would leave nothing clickable — this is the fallback, so
                                  // it has to be clickable itself. Showing the full URL also
                                  // lets the reader see it points at msdfar.com, which matters
                                  // on a government system where staff are told to distrust
                                  // buttons that hide their destination. ?>
                            <div style="margin-top:10px; font-size:11px; color:#9ca3af; line-height:1.5; word-break:break-all;">
                                <?= Html::encode(Yii::t('app', 'If the button does not work, use this link:')) ?><br>
                                <a href="<?= Html::encode($ctaUrl) ?>" target="_blank"
                                   style="color:#1a56db; text-decoration:underline; word-break:break-all;">
                                    <?= Html::encode($ctaUrl) ?>
                                </a>
                            </div>

                            <div style="margin-top:8px; font-size:11px; color:#9ca3af; line-height:1.5;">
                                <?= Html::encode(Yii::t('app', 'You will be asked to sign in first, then taken straight to this request.')) ?>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:16px 28px 24px; border-top:1px solid #f0f0f0;">
                            <div style="font-size:12px; color:#9ca3af; line-height:1.5;">
                                <?= Html::encode(Yii::t('app',
                                    'This is an automated message from the DFAR Leave Management System. Please do not reply to this email.'
                                )) ?>
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>