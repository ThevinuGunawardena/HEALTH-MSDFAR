<?php

use backend\config\LeaveTheme;
use backend\models\Leave;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Leave $model */
/** @var bool $compact  optional — tighter spacing for dashboard rows */

$compact = isset($compact) ? (bool) $compact : false;

$steps = $model->workflowSteps();
if (empty($steps)) {
    return;
}

// Step colours come from LeaveTheme so the timeline, the dashboard dots
// and the badges can never drift apart.

$this->registerCss(<<<CSS
.lv-tl { display: flex; flex-direction: row; }
.lv-tl-step { flex: 1 1 0; min-width: 0; }
.lv-tl-step:last-child { flex: 0 0 auto; }
.lv-tl-marker { display: flex; align-items: center; }
.lv-tl-dot {
    width: 26px; height: 26px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .7rem; flex: none;
}
.lv-tl-line { flex: 1 1 auto; height: 2px; min-width: 12px; }
.lv-tl-body { margin-top: .5rem; padding-right: .75rem; }
.lv-tl-label { font-size: .8rem; font-weight: 600; line-height: 1.2; }
.lv-tl-meta { font-size: .72rem; color: #858796; margin-top: .15rem; line-height: 1.25; }

/* Vertical below md — four labels across a phone screen is unreadable. */
@media (max-width: 767.98px) {
    .lv-tl { flex-direction: column; }
    .lv-tl-step { display: flex; align-items: stretch; flex: 0 0 auto; }
    .lv-tl-marker { flex-direction: column; align-items: center; margin-right: .75rem; }
    .lv-tl-line { width: 2px; height: auto; min-height: 16px; flex: 1 1 auto; }
    .lv-tl-body { margin-top: 0; padding-right: 0; padding-bottom: .85rem; }
    .lv-tl-step:last-child .lv-tl-body { padding-bottom: 0; }
}
CSS
);
?>
<div class="card shadow-sm <?= $compact ? 'mb-3' : 'mb-4' ?>">
    <div class="card-body <?= $compact ? 'py-3 px-3' : 'py-3' ?>">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-xs font-weight-bold text-uppercase text-muted">
                <i class="fas fa-stream mr-1"></i>
                <?= Yii::t('app', 'Approval Progress') ?>
            </span>
            <?= LeaveTheme::statusBadge($model) ?>
        </div>

        <div class="lv-tl">
            <?php $last = count($steps) - 1; ?>
            <?php foreach ($steps as $i => $step):
                $s = LeaveTheme::state($step['state']);

                // Timestamp if we have one, otherwise the state note.
                if (!empty($step['at'])) {
                    $meta = date('d M, H:i', strtotime($step['at']));
                } else {
                    $meta = $step['note'] ?? '';
                }
            ?>
            <div class="lv-tl-step">
                <div class="lv-tl-marker">
                    <span class="lv-tl-dot" style="background: <?= $s['bg'] ?>; color: <?= $s['fg'] ?>;">
                        <i class="<?= $s['fa'] ?>"></i>
                    </span>
                    <?php if ($i < $last): ?>
                        <span class="lv-tl-line" style="background: <?= $s['line'] ?>;"></span>
                    <?php endif; ?>
                </div>
                <div class="lv-tl-body">
                    <div class="lv-tl-label" style="color: <?= $step['state'] === 'pending' ? '#858796' : '#5a5c69' ?>;">
                        <?= Html::encode($step['label']) ?>
                    </div>
                    <?php if ($meta !== ''): ?>
                        <div class="lv-tl-meta"><?= Html::encode($meta) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>