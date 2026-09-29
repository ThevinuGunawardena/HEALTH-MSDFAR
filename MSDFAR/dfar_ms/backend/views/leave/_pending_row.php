<?php

use backend\config\Constant;
use backend\config\LeaveTheme;
use backend\models\Leave;
use backend\models\ProfileOfficers;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Leave $model */
/** @var int $index */

// ── Applicant name + initials for the avatar ────────────────────────
$profile = null;
if ($model->user && $model->user->profile_id) {
    $profile = ProfileOfficers::findOne($model->user->profile_id);
}

$name = $profile
    ? trim($profile->first_name . ' ' . $profile->last_name)
    : '';
if ($name === '') {
    $name = $model->user->nic ?? Yii::t('app', 'Unknown officer');
}

$initials = '';
foreach (preg_split('/\s+/', $name) as $part) {
    if ($part !== '') {
        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }
    if (mb_strlen($initials) >= 2) {
        break;
    }
}

$roleName = Constant::$userTypes[(int) $model->user_type]['name'] ?? '—';

$typeLabels = Leave::leaveTypeOptions();
$typeLabel  = $typeLabels[$model->leave_type] ?? $model->leave_type;
$typeColor  = LeaveTheme::type($model->leave_type);

$days = rtrim(rtrim(number_format((float) $model->total_days, 2), '0'), '.');

// How long it has been sitting on this approver's desk.
$waitDays  = $model->waitingDays();
$waitClass = LeaveTheme::agingClass($waitDays);
$waitLabel = LeaveTheme::agingLabel($waitDays);
?>
<div class="lv-queue-row d-flex flex-wrap align-items-center px-3 py-3 border-bottom">

    <div class="lv-queue-avatar mr-3" style="background: <?= $typeColor['bg'] ?>; color: <?= $typeColor['icon'] ?>;">
        <?= Html::encode($initials ?: '?') ?>
    </div>

    <div class="lv-queue-main mr-3">
        <div class="font-weight-bold text-truncate" style="font-size:.9rem;">
            <?= Html::encode($name) ?>
            <span class="text-muted font-weight-normal">
                &middot; <?= Html::encode($typeLabel) ?>,
                <?= Html::encode($days) ?> <?= Yii::t('app', 'days') ?>
            </span>
        </div>
        <div class="text-muted text-truncate" style="font-size:.78rem;">
            <?= Html::encode(date('M d', strtotime($model->start_date))) ?>
            &rarr;
            <?= Html::encode(date('M d, Y', strtotime($model->end_date))) ?>
            &middot; <?= Html::encode($roleName) ?>
            &middot; <?= Html::encode($model->user->nic ?? '—') ?>
        </div>
    </div>

    <div class="lv-queue-dots mr-3" aria-hidden="true">
        <?= LeaveTheme::dots($model) ?>
    </div>

    <div class="lv-queue-wait mr-3 text-right">
        <div class="<?= $waitClass ?>" style="font-size:.78rem; white-space:nowrap;">
            <i class="fas fa-hourglass-half mr-1"></i><?= Html::encode($waitLabel) ?>
        </div>
    </div>

    <div class="lv-queue-action">
        <?= LeaveTheme::actionButton($model) ?>
    </div>

</div>