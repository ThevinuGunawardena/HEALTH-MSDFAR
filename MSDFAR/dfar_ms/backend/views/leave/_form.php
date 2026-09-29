<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\config\Constant;

/** @var yii\web\View $this */
/** @var backend\models\Leave $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $balanceData */

// Balance data may not be passed in every context — default to empty
$balanceData = $balanceData ?? [];
$balanceJson = json_encode($balanceData);

// ── Resolve display values ───────────────────────────────────────
$isNew    = $model->isNewRecord;
$identity = Yii::$app->user->identity;

$displayNic   = $isNew ? ($identity->nic   ?? '') : $model->nic;
$displayEmail = $isNew ? ($identity->email ?? '') : $model->email;
$displayName  = $isNew ? ($identity->name  ?? '') : ($model->user->name ?? '');
$displayType  = $isNew ? ($identity->type  ?? '') : $model->user_type;

$roleName = isset(Constant::$userTypes[(int)$displayType]['name'])
    ? Constant::$userTypes[(int)$displayType]['name']
    : '—';

// ── Date helpers ─────────────────────────────────────────────────
$today    = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

// Application date shown on the form is always today's date.
$applicationDate = $today;

// ── Officer profile (source of the read-only Employee Information) ──
// Linked via User.profile_id → profile_officer.id. All Employee
// Information below is display-only and mirrors the officer's profile;
// the officer maintains these values on their own profile page.
$officerProfile = null;
$profileId = $identity->profile_id ?? null;
if ($profileId) {
    $officerProfile = \backend\models\ProfileOfficers::findOne($profileId);
}

$fullName = $officerProfile
    ? trim($officerProfile->first_name . ' ' . $officerProfile->last_name)
    : $displayName;

$designation = ($officerProfile && !empty($officerProfile->current_designation))
    ? $officerProfile->current_designation
    : $roleName;

$ministryDept   = $officerProfile->ministry_dept          ?? '';
$firstApptDate  = $officerProfile->dfar_appointment_date  ?? '';
$signatureFile  = $officerProfile->signature             ?? '';

// ── Leave type options ───────────────────────────────────────────
$leaveTypes = \backend\models\Leave::leaveTypeOptions();

// ── Autocomplete suggestions (datalists) ─────────────────────────
// Address: the officer's profile address first, then every distinct
// address this user has used on past leave requests. New addresses are
// remembered automatically because they land in the leave history.
$addressSuggestions = [];
if ($officerProfile && !empty($officerProfile->permanent_address)) {
    $addressSuggestions[] = $officerProfile->permanent_address;
}
$pastAddresses = \backend\models\Leave::find()
    ->select('leave_address')
    ->distinct()
    ->where(['user_id' => Yii::$app->user->id])
    ->andWhere(['not', ['leave_address' => null]])
    ->andWhere(['<>', 'leave_address', ''])
    ->column();
foreach ($pastAddresses as $addr) {
    if (!in_array($addr, $addressSuggestions, true)) {
        $addressSuggestions[] = $addr;
    }
}

// ── Acting officer list ─────────────────────────────────────────────
// Matched to the applicant's POSTING, so the person covering the work is
// someone who actually sits alongside them:
//
//   Head Office     -> other head-office officers
//   District Office -> the same district, and the same division when the
//                      applicant has one
//
// Three earlier faults are closed here:
//   1. The district filter was skipped entirely when the applicant had no
//      district on their profile — and Directors, ICT Officers, CC and KKS
//      are all exempt from that field, so those users were offered every
//      officer in the country.
//   2. Workplace type was ignored, so a head-office officer who happened to
//      carry a district appeared in that district's list.
//   3. Disabled accounts (status <> 10) were offered as acting officers.
$applicantDistrict = $officerProfile->district ?? null;
$applicantDivision = $officerProfile->division ?? null;
$applicantWorkplace = trim((string) ($officerProfile->current_workplace_type ?? ''));
$isHeadOfficeApplicant = ($applicantWorkplace === \backend\config\UserTypeUtil::WORKPLACE_HEAD_OFFICE);

$actingOfficerQuery = \common\models\User::find()
    ->alias('u')
    ->select(['u.id', 'p.first_name', 'p.last_name', 'u.nic'])
    ->innerJoin('profile_officer p', 'p.id = u.profile_id')
    ->andWhere(['<>', 'u.id', Yii::$app->user->id])
    ->andWhere(['u.status' => 10])   // active logins only
    ->orderBy(['p.first_name' => SORT_ASC, 'p.last_name' => SORT_ASC])
    ->asArray();

if ($isHeadOfficeApplicant) {
    // Head office covers for head office. District is irrelevant here.
    $actingOfficerQuery->andWhere([
        'p.current_workplace_type' => \backend\config\UserTypeUtil::WORKPLACE_HEAD_OFFICE,
    ]);
} else {
    // District office: never offer a head-office officer.
    $actingOfficerQuery->andWhere(['or',
        ['<>', 'p.current_workplace_type', \backend\config\UserTypeUtil::WORKPLACE_HEAD_OFFICE],
        ['p.current_workplace_type' => null],
    ]);

    if (!empty($applicantDistrict)) {
        $actingOfficerQuery->andWhere(['p.district' => $applicantDistrict]);

        // Narrow to the division as well when the applicant has one. An
        // officer in Balapitiya should be covered from Balapitiya, not from
        // the other end of Galle district.
        if (!empty($applicantDivision)) {
            $actingOfficerQuery->andWhere(['p.division' => $applicantDivision]);
        }
    } else {
        // No district on the profile: offering every officer in the country
        // is worse than offering none, because the applicant cannot tell
        // which of them is a valid choice.
        $actingOfficerQuery->andWhere('0=1');
    }
}

$actingOfficerList = [];   // [user_id => 'Name — NIC']
foreach ($actingOfficerQuery->all() as $o) {
    $name = trim(($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? ''));
    if ($name !== '') {
        $actingOfficerList[$o['id']] = $name . ' — ' . ($o['nic'] ?? '');
    }
}

// ── Leave taken in the current year (read-only display) ──────────
// APPROVED leave whose start_date falls in the relevant calendar year.
// Year = this request's start_date year if set, else the current year.
// Mirrors the server-side snapshot in LeaveController::stampLeaveTakenThisYear().
$takenYear = !empty($model->start_date)
    ? (int) date('Y', strtotime($model->start_date))
    : (int) date('Y');

$takenBase = function () use ($takenYear) {
    return \backend\models\Leave::find()
        ->andWhere(['user_id' => Yii::$app->user->id, 'status' => \backend\models\Leave::STATUS_APPROVED])
        ->andWhere(['between', 'start_date', "$takenYear-01-01", "$takenYear-12-31"]);
};
$takenShortHalf = function ($bucket) use ($takenBase) {
    return (int) $takenBase()
        ->andWhere(['in', 'leave_type', ['SHORT', 'HALF_DAY']])
        ->andWhere(['deducted_from' => $bucket])
        ->count();
};
$charge = \backend\models\Leave::SHORT_HALF_DAY_CHARGE;

$takenCasual   = (float) ($takenBase()->andWhere(['leave_type' => 'CASUAL'])->sum('total_days') ?: 0)
                 + ($charge * $takenShortHalf('CASUAL'));
$takenVacation = (float) ($takenBase()->andWhere(['leave_type' => 'ANNUAL'])->sum('total_days') ?: 0)
                 + ($charge * $takenShortHalf('ANNUAL'));
$takenOther    = (float) ($takenBase()
                 ->andWhere(['not in', 'leave_type', ['CASUAL', 'ANNUAL', 'SHORT', 'HALF_DAY']])
                 ->sum('total_days') ?: 0);

// Date of resuming duties — default to end_date + 1 (editable, required).
$resumeDefault = $model->resume_date
    ?: (!empty($model->end_date)
        ? date('Y-m-d', strtotime($model->end_date . ' +1 day'))
        : '');

// ── Short Leave monthly limit (upfront warning) ──────────────────
// Build a per-month count of this user's APPROVED + PENDING short leaves
// (matches the server-side limit, which is keyed on the request's
// start_date month). The form JS uses this to warn — and block submit —
// the moment a "Short Leave" date in a full month is chosen, before submit.
$shortLimit = \backend\models\Leave::SHORT_MONTHLY_LIMIT;
$shortRows = \backend\models\Leave::find()
    ->select(['start_date'])
    ->where(['user_id' => Yii::$app->user->id])
    ->andWhere(['leave_type' => 'SHORT'])
    ->andWhere(['in', 'status', [\backend\models\Leave::STATUS_APPROVED, \backend\models\Leave::STATUS_PENDING]])
    ->andWhere(['<>', 'id', $model->id ?: 0])
    ->asArray()
    ->all();
$shortMonthCounts = []; // 'YYYY-MM' => count
foreach ($shortRows as $r) {
    if (empty($r['start_date'])) {
        continue;
    }
    $m = date('Y-m', strtotime($r['start_date']));
    $shortMonthCounts[$m] = ($shortMonthCounts[$m] ?? 0) + 1;
}
$shortMonthCountsJson = json_encode($shortMonthCounts ?: new \stdClass());
?>

<?php
// ── Sidebar balance figures ─────────────────────────────────────────
// Same numbers the old three disabled boxes showed, but expressed as
// remaining-of-entitlement, which is what an applicant is actually
// checking before they submit.
$casualMax    = \backend\models\Leave::CASUAL_MAX;
$annualMax    = \backend\models\Leave::ANNUAL_MAX;
$casualLeft   = max(0, $casualMax - $takenCasual);
$vacationLeft = max(0, $annualMax - $takenVacation);
$fmt = function ($n) {
    return rtrim(rtrim(number_format((float) $n, 2), '0'), '.');
};

// The workflow line under the Submit button. AD / ITD skip the CC stage, so
// their request goes straight from the acting officer to the DG. KKS has no
// acting officer at all, so that clause is dropped from the hint.
$ccExempt        = in_array((int) $displayType, Constant::CC_EXEMPT_TYPES, true);
$noActingOfficer = ((int) $displayType === Constant::KKS);

if ($noActingOfficer) {
    $flowHint = $ccExempt
        ? Yii::t('app', 'Goes to the Director General for approval.')
        : Yii::t('app', 'Goes to the supervising officer, then the approving officer.');
} else {
    $flowHint = $ccExempt
        ? Yii::t('app', 'Goes to your acting officer to confirm, then to the Director General for approval.')
        : Yii::t('app', 'Goes to your acting officer to confirm, then the supervising officer, then the approving officer.');
}

$this->registerCss(<<<CSS
.lv-form-shell { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(240px, 1fr); gap: 1rem; align-items: start; }
@media (max-width: 991.98px) { .lv-form-shell { grid-template-columns: 1fr; } }

.lv-side { position: sticky; top: 1rem; }
@media (max-width: 991.98px) { .lv-side { position: static; } }

.lv-panel { background: #fff; border: 1px solid #e3e6f0; border-radius: .35rem; padding: .9rem; margin-bottom: .75rem; }
.lv-panel-label { font-size: .625rem; color: #858796; text-transform: uppercase; letter-spacing: .05em; font-weight: 700; margin-bottom: .5rem; }

.lv-bal-row { margin-bottom: .75rem; }
.lv-bal-row:last-child { margin-bottom: 0; }
.lv-bal-head { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: .25rem; }
.lv-bal-name { font-size: .65rem; font-weight: 700; letter-spacing: .03em; text-transform: uppercase; }
.lv-bal-fig { font-size: .75rem; color: #858796; }
.lv-bal-fig strong { color: #5a5c69; }

.lv-readonly { background: #f8f9fc !important; color: #6e707e; }

/* Submit: solid light green — the primary action, so it carries the
   only solid fill. Cancel: outlined in light red, lighter than the
   REJECTED pill so it does not read as an error state, and unfilled so
   it stays visibly secondary to Submit. */
.lv-btn-submit { background: #4CC38A; border-color: #4CC38A; color: #fff; font-weight: 500; }
.lv-btn-submit:hover, .lv-btn-submit:focus { background: #3BB77C; border-color: #3BB77C; color: #fff; }
.lv-btn-submit:focus { box-shadow: 0 0 0 .2rem rgba(76,195,138,.3); }

.lv-btn-cancel { background: #fff; border: 1px solid #f2b9c8; color: #e2607f; font-weight: 500; }
.lv-btn-cancel:hover, .lv-btn-cancel:focus { background: #fdeef2; border-color: #eda6ba; color: #d14e6f; }
.lv-btn-cancel:focus { box-shadow: 0 0 0 .2rem rgba(226,96,127,.2); }
CSS
);
?>
<div class="leave-form">

<?php $form = ActiveForm::begin(); ?>

    <!-- ── Hidden fields ── -->
    <?= $form->field($model, 'user_id')
        ->hiddenInput(['value' => $isNew ? ($identity->id ?? '') : $model->user_id])
        ->label(false) ?>
    <?= $form->field($model, 'nic')
        ->hiddenInput(['value' => $displayNic])
        ->label(false) ?>
    <?= $form->field($model, 'email')
        ->hiddenInput(['value' => $displayEmail])
        ->label(false) ?>
    <?= $form->field($model, 'user_type')
        ->hiddenInput(['value' => $displayType])
        ->label(false) ?>

    <!-- ══ Employee information — unchanged; sourced from the officer
         profile and only editable there. ══════════════════════════ -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h6 class="text-muted font-weight-bold text-uppercase mb-3" style="font-size:.7rem; letter-spacing:.05em;">
                <i class="fas fa-user mr-1"></i>
                <?= Yii::t('app', 'Employee Information') ?>
            </h6>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?= Yii::t('app', 'Name') ?></label>
                        <input type="text" class="form-control lv-readonly"
                               value="<?= Html::encode($fullName) ?>" disabled>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?= Yii::t('app', 'Designation') ?></label>
                        <input type="text" class="form-control lv-readonly"
                               value="<?= Html::encode($designation) ?>" disabled>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?= Yii::t('app', 'Ministry / Department') ?></label>
                        <input type="text" class="form-control lv-readonly"
                               value="<?= Html::encode($ministryDept) ?>" disabled>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?= Yii::t('app', 'Date of First Appointment') ?></label>
                        <input type="text" class="form-control lv-readonly"
                               value="<?= Html::encode($firstApptDate ?: '—') ?>" disabled>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?= Yii::t('app', 'Date') ?></label>
                        <input type="text" class="form-control lv-readonly"
                               value="<?= Html::encode($applicationDate) ?>" disabled>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label d-block"><?= Yii::t('app', 'Signature of Applicant') ?></label>
                        <?php if (!empty($signatureFile)): ?>
                            <img src="<?= Constant::$FILE_VIEW_PATH ?>officer/signature/<?= Html::encode($signatureFile) ?>"
                                 alt="<?= Yii::t('app', 'Signature') ?>"
                                 class="border rounded bg-light p-1"
                                 style="max-height:48px; max-width:100%;">
                        <?php else: ?>
                            <input type="text" class="form-control lv-readonly"
                                   value="<?= Html::encode($fullName) ?>" disabled>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ Two-column shell: form on the left, live summary on the right ══ -->
    <div class="lv-form-shell mb-4">

        <!-- ── Leave details ─────────────────────────────────────── -->
        <div class="card shadow-sm mb-0">
            <div class="card-body">
                <h6 class="text-muted font-weight-bold text-uppercase mb-3" style="font-size:.7rem; letter-spacing:.05em;">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <?= Yii::t('app', 'Leave Details') ?>
                </h6>

                <div class="row">

                    <!-- Leave type (drives the conditional blocks below) -->
                    <div class="col-12">
                        <?= $form->field($model, 'leave_type')->dropDownList(
                            $leaveTypes,
                            [
                                'prompt' => Yii::t('app', 'Select Leave Type'),
                                'id'     => 'leave-leave-type',
                            ]
                        )->label(Yii::t('app', 'Leave Type') . ' <span class="text-danger">*</span>', ['encode' => false]) ?>

                        <!-- Live balance hint — shown when a bucket-related leave type is picked -->
                        <div id="balance-hint" class="alert py-2 px-3 mt-1 mb-2" style="display:none; font-size:0.85rem;">
                            <span id="balance-hint-text"></span>
                        </div>

                        <!-- Short Leave limit warning — shown when the chosen month is full -->
                        <div id="short-limit-warning" class="alert alert-danger py-2 px-3 mt-1 mb-2"
                             style="display:none; font-size:0.85rem;">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <span id="short-limit-warning-text"></span>
                        </div>

                        <!-- Half day session picker — shown only when HALF_DAY is selected -->
                        <div id="half-day-session" style="display:none;" class="mt-2 mb-3">
                            <label class="control-label">
                                <?= Yii::t('app', 'Select Session') ?>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="row mt-1">
                                <div class="col-6">
                                    <div class="custom-control custom-radio border rounded p-3 text-center">
                                        <input type="radio" id="session-morning" name="Leave[session]"
                                               value="MORNING"
                                               <?= ($model->session ?? '') === 'MORNING' ? 'checked' : '' ?>
                                               class="custom-control-input">
                                        <label class="custom-control-label w-100" for="session-morning">
                                            <strong><?= Yii::t('app', 'Morning') ?></strong><br>
                                            <small class="text-muted">8:00 AM – 12:00 PM</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="custom-control custom-radio border rounded p-3 text-center">
                                        <input type="radio" id="session-afternoon" name="Leave[session]"
                                               value="AFTERNOON"
                                               <?= ($model->session ?? '') === 'AFTERNOON' ? 'checked' : '' ?>
                                               class="custom-control-input">
                                        <label class="custom-control-label w-100" for="session-afternoon">
                                            <strong><?= Yii::t('app', 'Afternoon') ?></strong><br>
                                            <small class="text-muted">1:00 PM – 5:00 PM</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deduct From picker — shown only when HALF_DAY is selected -->
                        <div id="deduct-from-section" style="display:none;" class="mb-3">
                            <label class="control-label">
                                <?= Yii::t('app', 'Deduct From') ?>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="row mt-1">
                                <div class="col-6">
                                    <div class="custom-control custom-radio border rounded p-3 text-center">
                                        <input type="radio" id="deduct-casual" name="Leave[deducted_from]"
                                               value="CASUAL"
                                               <?= ($model->deducted_from ?? '') === 'CASUAL' ? 'checked' : '' ?>
                                               class="custom-control-input">
                                        <label class="custom-control-label w-100" for="deduct-casual">
                                            <strong><?= Yii::t('app', 'Casual Leave') ?></strong><br>
                                            <small class="text-muted"><?= Yii::t('app', 'Max 21 days') ?></small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="custom-control custom-radio border rounded p-3 text-center">
                                        <input type="radio" id="deduct-annual" name="Leave[deducted_from]"
                                               value="ANNUAL"
                                               <?= ($model->deducted_from ?? '') === 'ANNUAL' ? 'checked' : '' ?>
                                               class="custom-control-input">
                                        <label class="custom-control-label w-100" for="deduct-annual">
                                            <strong><?= Yii::t('app', 'Vacation Leave') ?></strong><br>
                                            <small class="text-muted"><?= Yii::t('app', 'Max 24 days') ?></small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <small class="text-info mt-1 d-block">
                                <i class="fas fa-info-circle mr-1"></i>
                                <?= Yii::t('app', 'This leave will be counted as 0.5 day from the selected bucket.') ?>
                            </small>
                        </div>
                    </div>

                    <!-- Dates, grouped -->
                    <div class="col-md-6" id="start-date-wrapper">
                        <?= $form->field($model, 'start_date')->input('date', [
                            'class' => 'form-control',
                            'id'    => 'leave-start-date',
                            'min'   => $tomorrow,
                        ])->label('<span id="start-date-label">' . Yii::t('app', 'Start Date') . '</span>', ['encode' => false]) ?>
                    </div>

                    <div class="col-md-6" id="end-date-wrapper">
                        <?= $form->field($model, 'end_date')->input('date', [
                            'class' => 'form-control',
                            'id'    => 'leave-end-date',
                            'min'   => $tomorrow,
                        ])->label(Yii::t('app', 'End Date')) ?>
                    </div>

                    <!-- Resume date stays a required input; the JS still suggests
                         end date + 1, and the hint says where that came from. -->
                    <div class="col-md-6">
                        <?= $form->field($model, 'resume_date', [
                                'template' => "{label}\n{input}\n<small class=\"text-muted\">"
                                            . Yii::t('app', 'Suggested from your end date — change it if you return later.')
                                            . "</small>\n{error}",
                            ])->input('date', [
                            'id'    => 'leave-resume-date',
                            'value' => $resumeDefault,
                        ])->label(Yii::t('app', 'Date of Resuming Duties') . ' <span class="text-danger">*</span>', ['encode' => false]) ?>
                    </div>

                    <!-- Acting officer — omitted entirely for KKS applicants -->
                    <?php if (!$noActingOfficer): ?>
                    <div class="col-md-6" id="acting-officer-wrapper">
                        <?php
                        // Acting officer uses a searchable text input (datalist) like the
                        // address field, but must submit the user ID — not the text. So we
                        // pair a visible text input (search by name or NIC) with a hidden
                        // field holding acting_officer_user_id. JS maps the chosen text → id.
                        $actingDisplay = '';
                        if (!empty($model->acting_officer_user_id)
                            && isset($actingOfficerList[$model->acting_officer_user_id])) {
                            $actingDisplay = $actingOfficerList[$model->acting_officer_user_id];
                        } elseif (!empty($model->acting_officer)) {
                            $actingDisplay = $model->acting_officer;
                        }
                        ?>
                        <div class="form-group">
                            <label>
                                <?= Yii::t('app', 'Acting Officer') ?>
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   id="acting-officer-search"
                                   class="form-control"
                                   list="acting-officer-list"
                                   value="<?= Html::encode($actingDisplay) ?>"
                                   placeholder="<?= Yii::t('app', 'Search officer by name or NIC') ?>"
                                   autocomplete="off">

                            <datalist id="acting-officer-list">
                                <?php foreach ($actingOfficerList as $uid => $label): ?>
                                    <option data-id="<?= $uid ?>" value="<?= Html::encode($label) ?>"></option>
                                <?php endforeach; ?>
                            </datalist>

                            <?php if (empty($actingOfficerList)): ?>
                                <div class="alert alert-warning py-2 px-3 mt-2 mb-0" style="font-size:.8rem;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <?= empty($applicantDistrict) && !$isHeadOfficeApplicant
                                        ? Yii::t('app', 'No acting officers can be listed until your profile has a workplace type and district. Please update your profile first.')
                                        : Yii::t('app', 'No other active officer is registered at your posting, so no acting officer can be selected. Please contact the administration.') ?>
                                </div>
                            <?php endif; ?>

                            <?= Html::activeHiddenInput($model, 'acting_officer_user_id', [
                                'id' => 'acting-officer-id',
                            ]) ?>

                            <?php if ($model->hasErrors('acting_officer_user_id')): ?>
                                <div class="text-danger" style="font-size:0.85rem;">
                                    <?= Html::encode($model->getFirstError('acting_officer_user_id')) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!$model->isNewRecord && !empty($model->acting_officer)): ?>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <?= Yii::t('app', 'Currently selected: {name}', ['name' => Html::encode($model->acting_officer)]) ?>
                                </small>
                            <?php endif; ?>
                            <?php if (!$model->isNewRecord && $model->status === \backend\models\Leave::STATUS_DRAFT
                                      && empty($model->acting_officer_agreed_at)): ?>
                                <div class="alert alert-warning py-2 px-3 mt-2" style="font-size:0.85rem;">
                                    <i class="fas fa-hourglass-half mr-1"></i>
                                    <?= Yii::t('app', 'Waiting for the acting officer to confirm.') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Address when on leave -->
                    <div class="col-12">
                        <?= $form->field($model, 'leave_address')->textInput([
                            'maxlength'    => true,
                            'placeholder'  => Yii::t('app', 'Address where you can be reached during leave'),
                            'list'         => 'leave-address-list',
                            'autocomplete' => 'off',
                        ])->label(
                            Yii::t('app', 'Address when on Leave') . ' <span class="text-danger">*</span>',
                            ['encode' => false]
                        ) ?>
                        <datalist id="leave-address-list">
                            <?php foreach ($addressSuggestions as $addr): ?>
                                <option value="<?= Html::encode($addr) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <!-- Reason -->
                    <div class="col-12">
                        <?= $form->field($model, 'reason')->textarea([
                            'rows'        => 3,
                            'class'       => 'form-control',
                            'placeholder' => Yii::t('app', 'Briefly describe the reason for your leave request...'),
                        ])->label(Yii::t('app', 'Reason')) ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- ── Live summary sidebar ──────────────────────────────── -->
        <div class="lv-side">

            <!-- This request. Total Days keeps its original element ids so
                 the existing calculation JS keeps driving it. -->
            <div class="lv-panel">
                <div class="lv-panel-label"><?= Yii::t('app', 'This Request') ?></div>
                <div>
                    <span id="leave-total-days-display" class="font-weight-bold"
                          style="font-size:1.9rem; line-height:1; color:#4f46e5;">
                        <?= $model->total_days ?? 0 ?>
                    </span>
                    <span class="text-muted" style="font-size:.8rem;"><?= Yii::t('app', 'days') ?></span>
                </div>
                <input type="hidden" id="leave-total-days"
                       name="Leave[total_days]"
                       value="<?= $model->total_days ?? 0 ?>">

                <div class="text-muted mt-2" style="font-size:.75rem;">
                    <div id="request-range">—</div>
                    <div id="request-resume"></div>
                </div>
            </div>

            <!-- Leave taken this year, as remaining-of-entitlement -->
            <div class="lv-panel">
                <div class="lv-panel-label">
                    <?= Yii::t('app', 'Leave Taken in {year}', ['year' => $takenYear]) ?>
                </div>

                <?php
                $bars = [
                    ['name' => Yii::t('app', 'Casual'),   'type' => 'CASUAL', 'taken' => $takenCasual,   'max' => $casualMax, 'left' => $casualLeft],
                    ['name' => Yii::t('app', 'Vacation'), 'type' => 'ANNUAL', 'taken' => $takenVacation, 'max' => $annualMax, 'left' => $vacationLeft],
                ];
                foreach ($bars as $b):
                    $c   = \backend\config\LeaveTheme::type($b['type']);
                    $pct = $b['max'] > 0 ? min(100, round(($b['taken'] / $b['max']) * 100)) : 0;
                ?>
                <div class="lv-bal-row">
                    <div class="lv-bal-head">
                        <span class="lv-bal-name" style="color: <?= $c['icon'] ?>;"><?= Html::encode($b['name']) ?></span>
                        <span class="lv-bal-fig">
                            <strong><?= $fmt($b['left']) ?></strong>
                            <?= Yii::t('app', 'of {max} left', ['max' => $b['max']]) ?>
                        </span>
                    </div>
                    <div class="progress" style="height:5px;">
                        <div class="progress-bar" role="progressbar"
                             style="width: <?= $pct ?>%; background: <?= $c['border'] ?>;"
                             aria-valuenow="<?= $b['taken'] ?>" aria-valuemin="0" aria-valuemax="<?= $b['max'] ?>"></div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="lv-bal-head mt-2">
                    <span class="lv-bal-fig"><?= Yii::t('app', 'Other') ?></span>
                    <span class="lv-bal-fig"><strong><?= $fmt($takenOther) ?></strong> <?= Yii::t('app', 'days') ?></span>
                </div>
            </div>

            <!-- Actions -->
            <?= Html::submitButton(
                '<i class="fas fa-paper-plane mr-1"></i> ' . Yii::t('app', 'Submit Request'),
                ['class' => 'btn btn-block lv-btn-submit']
            ) ?>
            <?= Html::a(
                '<i class="fas fa-times mr-1"></i> ' . Yii::t('app', 'Cancel'),
                ['index', 'mode' => 'my'],
                ['class' => 'btn btn-block lv-btn-cancel mt-2']
            ) ?>
            <p class="text-muted mt-3 mb-0" style="font-size:.72rem; line-height:1.5;">
                <?= Html::encode($flowHint) ?>
            </p>

        </div>

    </div>

<?php ActiveForm::end(); ?>

</div>

<?php
$script = <<<JS

var leaveBalances     = {$balanceJson};
var leaveTypeSelect   = document.getElementById('leave-leave-type');
var halfDaySection    = document.getElementById('half-day-session');
var deductFromSection = document.getElementById('deduct-from-section');
var balanceHint       = document.getElementById('balance-hint');
var balanceHintText   = document.getElementById('balance-hint-text');
var startInput        = document.getElementById('leave-start-date');
var endInput          = document.getElementById('leave-end-date');
var startWrapper      = document.getElementById('start-date-wrapper');
var endWrapper        = document.getElementById('end-date-wrapper');
var startLabel        = document.getElementById('start-date-label');
var todayDate         = new Date();
var tomorrowDate      = new Date();
tomorrowDate.setDate(tomorrowDate.getDate() + 1);
var todayStr          = todayDate.toLocaleDateString('en-CA');
var tomorrowStr       = tomorrowDate.toLocaleDateString('en-CA');

// ── 1. Update start date minimum based on leave type ─────────────
function updateStartDateMin() {
    var val        = leaveTypeSelect.value;
    var allowToday = val === 'SHORT' || val === 'HALF_DAY';
    var newMin     = allowToday ? todayStr : tomorrowStr;

    startInput.min = newMin;

    if (!startInput.value || startInput.value < newMin) {
        startInput.value = newMin;
    }

    endInput.min = startInput.value;
    if (endInput.value && endInput.value < startInput.value) {
        endInput.value = startInput.value;
    }

    calcLeaveDays();
}

// ── 2. Auto-calculate total days ─────────────────────────────────
function calcLeaveDays() {
    var start   = startInput.value;
    var end     = endInput.value;
    var display = document.getElementById('leave-total-days-display');
    var hidden  = document.getElementById('leave-total-days');
    var val     = leaveTypeSelect.value;

    if (start) {
        endInput.min = start;
        if (end && end < start) {
            endInput.value = start;
            end = start;
        }
    }

    // SHORT counts as 1 day; HALF_DAY is 0.5. end_date mirrors start_date.
    if (val === 'SHORT' || val === 'HALF_DAY') {
        if (start) {
            endInput.value = start;  // keep end_date in sync with the single date
        }
        var oneOrHalf       = (val === 'SHORT') ? '1' : '0.5';
        display.textContent = oneOrHalf;
        hidden.value        = oneOrHalf;
        return;
    }

    if (start && end) {
        var s    = new Date(start);
        var e    = new Date(end);
        var diff = e >= s ? Math.round((e - s) / (1000 * 60 * 60 * 24)) + 1 : 0;
        display.textContent = diff;
        hidden.value        = diff;
    } else {
        display.textContent = '0';
        hidden.value        = '0';
    }
}

startInput.addEventListener('change', calcLeaveDays);
endInput.addEventListener('change', calcLeaveDays);

// ── 2a-bis. Date of resuming duties = end_date + 1 (editable) ────
// Auto-tracks the end date until the user edits it manually; after a
// manual edit we stop overwriting so officers can resume on a weekend.
var resumeInput = document.getElementById('leave-resume-date');
var resumeEdited = false;
if (resumeInput) {
    resumeInput.addEventListener('input', function () { resumeEdited = true; });
}
function updateResumeDate() {
    if (!resumeInput || resumeEdited) {
        return;
    }
    // For SHORT/HALF_DAY the single date lives in startInput; otherwise use end.
    var val  = leaveTypeSelect.value;
    var base = (val === 'SHORT' || val === 'HALF_DAY') ? startInput.value : endInput.value;
    if (!base) {
        return;
    }
    var d = new Date(base);
    d.setDate(d.getDate() + 1);
    resumeInput.value = d.toISOString().slice(0, 10);
}
startInput.addEventListener('change', updateResumeDate);
endInput.addEventListener('change', updateResumeDate);

// ── 2b. Toggle Single-Date vs Date-Range layout ──────────────────
function toggleDateLayout() {
    var val        = leaveTypeSelect.value;
    var singleDate = (val === 'SHORT' || val === 'HALF_DAY');

    if (singleDate) {
        // Hide End Date, widen Start Date, relabel to "Select Date"
        endWrapper.style.display = 'none';
        startLabel.textContent   = 'Select Date';
        startWrapper.classList.remove('col-md-2');
        startWrapper.classList.add('col-md-4');

        // Mirror start into end so DB + validation get a valid value
        endInput.value = startInput.value;
    } else {
        // Restore End Date + original Start Date label/width
        endWrapper.style.display = '';
        startLabel.textContent   = 'Start Date';
        startWrapper.classList.remove('col-md-4');
        startWrapper.classList.add('col-md-2');
    }
}

// ── 3. Show / hide Half-Day session + Deduct-From pickers ─────────
function toggleSession() {
    var val         = leaveTypeSelect.value;
    var isHalfDay   = val === 'HALF_DAY';
    // Only Half-Day deducts from a bucket now. Short Leave is capped
    // per month and does not affect Casual/Vacation.
    var needsDeduct = isHalfDay;

    // Half-Day session (Morning/Afternoon)
    var sessionEls = halfDaySection.querySelectorAll('input[type=radio]');
    halfDaySection.style.display = isHalfDay ? 'block' : 'none';
    sessionEls.forEach(function (r) {
        r.required = isHalfDay;
        if (!isHalfDay) r.checked = false;
    });

    // Deduct From (Casual / Vacation)
    var deductEls = deductFromSection.querySelectorAll('input[type=radio]');
    deductFromSection.style.display = needsDeduct ? 'block' : 'none';
    deductEls.forEach(function (r) {
        r.required = needsDeduct;
        if (!needsDeduct) r.checked = false;
    });
}

// ── 3b. Live balance hint ────────────────────────────────────────
function bucketForCurrentSelection() {
    var val = leaveTypeSelect.value;
    if (val === 'CASUAL' || val === 'ANNUAL') {
        return val;
    }
    if (val === 'HALF_DAY') {
        // bucket comes from the chosen Deduct-From radio
        var checked = deductFromSection.querySelector('input[type=radio]:checked');
        return checked ? checked.value : null;
    }
    return null;
}

function updateBalanceHint() {
    var bucket = bucketForCurrentSelection();

    if (!bucket || !leaveBalances[bucket]) {
        balanceHint.style.display = 'none';
        return;
    }

    var b         = leaveBalances[bucket];
    var label     = (bucket === 'CASUAL') ? 'Casual Leave' : 'Vacation Leave';
    var approved  = parseFloat(b.approved);
    var pending   = parseFloat(b.pending);
    var max       = parseFloat(b.max);
    var available = Math.max(0, max - approved - pending);

    // Color: green if comfortable, yellow if low, red if none
    balanceHint.classList.remove('alert-success', 'alert-warning', 'alert-danger');
    if (available <= 0) {
        balanceHint.classList.add('alert-danger');
    } else if (available <= 3) {
        balanceHint.classList.add('alert-warning');
    } else {
        balanceHint.classList.add('alert-success');
    }

    var usedTxt = approved + ' of ' + max + ' day(s) used';
    var pendTxt = pending > 0 ? ' &middot; ' + pending + ' pending' : '';
    var availTxt = ' &middot; <strong>' + available + ' available</strong>';

    balanceHintText.innerHTML =
        '<i class="fas fa-info-circle mr-1"></i>' +
        '<strong>' + label + ':</strong> ' + usedTxt + pendTxt + availTxt;

    balanceHint.style.display = 'block';
}
// ── Acting officer: not needed for Short Leave ───────────────────
// Short Leave skips the acting-officer step, so hide the field and
// clear any selection so no stale user id is submitted.
var actingWrapper = document.getElementById('acting-officer-wrapper');
function toggleActingOfficer() {
    if (!actingWrapper) return;
    var isShort = leaveTypeSelect.value === 'SHORT';
    actingWrapper.style.display = isShort ? 'none' : '';

    if (isShort) {
        var search = document.getElementById('acting-officer-search');
        var hidden = document.getElementById('acting-officer-id');
        if (search) search.value = '';
        if (hidden) hidden.value = '';
    }
}

// ── 4. Change listeners ──────────────────────────────────────────
leaveTypeSelect.addEventListener('change', function () {
    toggleSession();
    toggleDateLayout();
    updateStartDateMin();
    updateBalanceHint();
    toggleActingOfficer();
});

// Update hint when the Deduct-From bucket changes (Short / Half Day)
deductFromSection.querySelectorAll('input[type=radio]').forEach(function (r) {
    r.addEventListener('change', updateBalanceHint);
});

// ── 5. Run on page load ──────────────────────────────────────────
toggleSession();
toggleDateLayout();
updateStartDateMin();
calcLeaveDays();
updateBalanceHint();

// ── 6. SAFETY NET: sync end_date before the form submits ─────────
// For SHORT and HALF_DAY the End Date field is hidden, so we must
// ── Short Leave monthly limit: warn + block before submit ─────────
var shortMonthCounts = {$shortMonthCountsJson};
var shortLimit       = {$shortLimit};
var shortWarnBox     = document.getElementById('short-limit-warning');
var shortWarnText    = document.getElementById('short-limit-warning-text');

function monthLabel(ymd) {
    // ymd = 'YYYY-MM-DD' → 'Month YYYY'
    var d = new Date(ymd);
    if (isNaN(d)) return '';
    return d.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
}

// Returns true if the currently-selected Short Leave date is in a month
// that has already reached the limit (APPROVED + PENDING).
function isShortLimitReached() {
    if (leaveTypeSelect.value !== 'SHORT' || !startInput.value) {
        return false;
    }
    var ym   = startInput.value.slice(0, 7); // 'YYYY-MM'
    var used = shortMonthCounts[ym] || 0;
    return used >= shortLimit;
}

function updateShortWarning() {
    if (!shortWarnBox) return;
    if (isShortLimitReached()) {
        var label = monthLabel(startInput.value);
        shortWarnText.textContent =
            'You have already used all ' + shortLimit + ' short leaves for '
            + (label || 'this month')
            + ' (including any pending requests). You cannot apply for another short leave this month.';
        shortWarnBox.style.display = 'block';
    } else {
        shortWarnBox.style.display = 'none';
    }
}

leaveTypeSelect.addEventListener('change', updateShortWarning);
startInput.addEventListener('change', updateShortWarning);
updateShortWarning(); // run once on load (e.g. when re-opening the form)

// make sure end_date carries the same value as the chosen date,
// otherwise the model's required/compare validation fails silently.
var leaveForm = startInput.closest('form');
if (leaveForm) {
    leaveForm.addEventListener('submit', function (e) {
        var val = leaveTypeSelect.value;

        // Block submission when the Short Leave monthly cap is reached.
        if (isShortLimitReached()) {
            e.preventDefault();
            updateShortWarning();
            if (shortWarnBox) {
                shortWarnBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }

        if (val === 'SHORT' || val === 'HALF_DAY') {
            if (startInput.value) {
                endInput.value = startInput.value;
            }
            // Keep the hidden total in sync (server recalculates anyway):
            // SHORT = 1 day, HALF_DAY = 0.5 day.
            var hidden = document.getElementById('leave-total-days');
            if (hidden) {
                hidden.value = (val === 'SHORT') ? '1' : '0.5';
            }
        }
    });
}

JS;

$this->registerJs($script);

// ── Acting Officer search → hidden ID mapping ────────────────────────────────
// The visible text input lets the user search by name or NIC (with datalist
// suggestions). When they pick/type a value that matches an option, we copy
// that option's data-id into the hidden acting_officer_user_id field so the
// correct user ID is submitted with the form.
$this->registerJs(<<<AOJS
(function () {
    var search = document.getElementById('acting-officer-search');
    var hidden = document.getElementById('acting-officer-id');
    var list   = document.getElementById('acting-officer-list');

    if (!search || !hidden || !list) return;

    // Build a lookup: option text (lowercased) → user id
    var map = {};
    Array.prototype.forEach.call(list.options, function (opt) {
        map[opt.value.toLowerCase()] = opt.getAttribute('data-id');
    });

    function sync() {
        var val = search.value.toLowerCase().trim();
        if (map.hasOwnProperty(val)) {
            hidden.value = map[val];               // exact match → set id
        } else {
            // try a partial match (user typed only part of name/NIC)
            var found = '';
            for (var key in map) {
                if (map.hasOwnProperty(key) && key.indexOf(val) !== -1 && val !== '') {
                    found = map[key];
                    break;
                }
            }
            hidden.value = found;                  // '' if nothing matches
        }
    }

    search.addEventListener('input', sync);
    search.addEventListener('change', sync);
    search.addEventListener('blur', sync);
})();
AOJS
);

// ── Sidebar summary: mirror the chosen dates into the "This request"
//    panel. Purely additive — it only reads the existing date inputs.
$this->registerJs(<<<SUMJS
(function () {
    var s  = document.getElementById('leave-start-date');
    var e  = document.getElementById('leave-end-date');
    var r  = document.getElementById('leave-resume-date');
    var ro = document.getElementById('request-range');
    var rr = document.getElementById('request-resume');
    if (!s || !ro) { return; }

    function fmt(v) {
        if (!v) { return ''; }
        var d = new Date(v + 'T00:00:00');
        if (isNaN(d.getTime())) { return v; }
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function paint() {
        var a = fmt(s.value);
        // End date is hidden for SHORT / HALF_DAY, so fall back to one day.
        var b = (e && e.offsetParent !== null) ? fmt(e.value) : '';
        ro.textContent = a ? (b && b !== a ? a + ' \u2013 ' + b : a) : '\u2014';
        if (rr) {
            rr.textContent = (r && r.value) ? 'Back on ' + fmt(r.value) : '';
        }
    }

    ['change', 'input'].forEach(function (ev) {
        s.addEventListener(ev, paint);
        if (e) { e.addEventListener(ev, paint); }
        if (r) { r.addEventListener(ev, paint); }
    });
    paint();
})();
SUMJS
);
?>