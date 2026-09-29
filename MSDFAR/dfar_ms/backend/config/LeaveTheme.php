<?php

namespace backend\config;

use backend\models\Leave;
use yii\helpers\Html;
use Yii;

/**
 * LeaveTheme — the single source of colour, icon and badge markup for the
 * leave module.
 *
 * Before this class the same six leave-type colours were declared twice
 * (as $colorMap in index.php and $typeColors in director-dashboard.php)
 * in two incompatible shapes, and the status label was rendered with
 * ucfirst(strtolower(...)) in three places — which printed "Pending_cc".
 *
 * Everything visual now resolves through here, so one edit changes the
 * index page, the director dashboard and the timeline together.
 *
 * Badge vocabulary — shape carries meaning:
 *   SQUARE badge (4px)  = leave type, coloured per type
 *   ROUNDED pill (99px) = status, role, day count
 * so the type column stays identifiable at a glance.
 */
class LeaveTheme
{
    /**
     * Per leave-type palette.
     *   bg     — tinted background
     *   border — accent / bar segment
     *   icon   — text and icon colour
     *   fa     — Font Awesome class
     */
    const TYPES = [
        'CASUAL'   => ['bg' => '#eef2ff', 'border' => '#6366f1', 'icon' => '#4338ca', 'fa' => 'fas fa-umbrella-beach'],
        'ANNUAL'   => ['bg' => '#e6f6ef', 'border' => '#0d9488', 'icon' => '#0f6e56', 'fa' => 'fas fa-tree'],
        'NO_PAY'   => ['bg' => '#fdf0eb', 'border' => '#f97316', 'icon' => '#993c1d', 'fa' => 'fas fa-money-bill-wave'],
        'SHORT'    => ['bg' => '#e8f1fb', 'border' => '#3b82f6', 'icon' => '#185fa5', 'fa' => 'fas fa-hourglass-half'],
        'DUTY'     => ['bg' => '#fbf0f4', 'border' => '#ec4899', 'icon' => '#993556', 'fa' => 'fas fa-briefcase'],
        'HALF_DAY' => ['bg' => '#fdf6ec', 'border' => '#f59e0b', 'icon' => '#854f0b', 'fa' => 'fas fa-adjust'],
    ];

    /** Fallback for an unknown leave type. */
    const TYPE_FALLBACK = ['bg' => '#f1f3f9', 'border' => '#858796', 'icon' => '#5a5c69', 'fa' => 'fas fa-calendar'];

    /**
     * Workflow step states, used by the timeline circles and the compact
     * progress dots.
     */
    const STATES = [
        'done'    => ['fa' => 'fas fa-check',  'bg' => '#d4edda', 'fg' => '#155724', 'line' => '#28a745', 'dot' => '#28a745'],
        'current' => ['fa' => 'fas fa-clock',  'bg' => '#fff3cd', 'fg' => '#856404', 'line' => '#e3e6f0', 'dot' => '#f6c23e'],
        'pending' => ['fa' => 'far fa-circle', 'bg' => '#eaecf4', 'fg' => '#858796', 'line' => '#e3e6f0', 'dot' => '#dddfeb'],
        'failed'  => ['fa' => 'fas fa-times',  'bg' => '#f8d7da', 'fg' => '#721c24', 'line' => '#e3e6f0', 'dot' => '#e74a3b'],
        // Withdrawn by the applicant — an ending, but not a refusal, so it
        // is grey rather than red.
        'cancelled' => ['fa' => 'fas fa-times', 'bg' => '#eaecf4', 'fg' => '#858796', 'line' => '#e3e6f0', 'dot' => '#b7b9cc'],
    ];

    /**
     * Status palette: soft tint background with darker text of the same hue.
     *
     * Amber is reserved for "waiting on the final approver" (PENDING).
     * PENDING_CC is indigo, so an approver scanning the list can tell which
     * rows are theirs to action and which belong to someone else.
     */
    const STATUSES = [
        Leave::STATUS_DRAFT      => ['bg' => '#f1f3f9', 'fg' => '#5a5c69'],
        Leave::STATUS_PENDING_CC => ['bg' => '#eef2ff', 'fg' => '#4338ca'],
        Leave::STATUS_PENDING    => ['bg' => '#fdf6ec', 'fg' => '#b45309'],
        Leave::STATUS_APPROVED   => ['bg' => '#e6f6ef', 'fg' => '#0f8b62'],
        Leave::STATUS_REJECTED   => ['bg' => '#fdeef2', 'fg' => '#cc2b5e'],
        Leave::STATUS_DECLINED   => ['bg' => '#fdeef2', 'fg' => '#cc2b5e'],
        Leave::STATUS_CANCELLED  => ['bg' => '#f1f3f9', 'fg' => '#6e707e'],
    ];

    /**
     * Shorter status labels for the badge only.
     *
     * "Pending CC Review" in uppercase is roughly 40% wider than "APPROVED"
     * and stretches the column. The full label is still used everywhere
     * else — filters, the timeline heading, exports.
     */
    const STATUS_SHORT = [
        Leave::STATUS_PENDING_CC => 'Pending CC',
    ];

    // ── Shared geometry ────────────────────────────────────────────

    /** Rounded pill: status, role, days. */
    const PILL_CSS = 'display:inline-block; font-size:.7rem; line-height:1.5;'
                   . ' padding:.2rem .7rem; border-radius:99px; white-space:nowrap;';

    /** Square badge: leave type only. */
    const SQUARE_CSS = 'display:inline-block; font-size:.7rem; font-weight:600;'
                     . ' letter-spacing:.03em; text-transform:uppercase; line-height:1.5;'
                     . ' padding:.15rem .5rem; border-radius:4px; white-space:nowrap;';

    // ---------------------------------------------------------------
    // Lookups
    // ---------------------------------------------------------------

    /**
     * Palette for one leave type code (CASUAL, ANNUAL, ...).
     *
     * @param string $code
     * @return array
     */
    public static function type($code)
    {
        return self::TYPES[$code] ?? self::TYPE_FALLBACK;
    }

    /**
     * Colours for one workflow step state.
     *
     * @param string $state  done | current | pending | failed
     * @return array
     */
    public static function state($state)
    {
        return self::STATES[$state] ?? self::STATES['pending'];
    }

    // ---------------------------------------------------------------
    // Badge markup
    // ---------------------------------------------------------------

    /**
     * Status badge — uppercase tinted pill.
     *
     * The old inline ucfirst(strtolower($model->status)) turned the
     * 'pending_cc' status into "Pending_cc" on screen. statusOptions()
     * has held the proper label all along — this uses it.
     *
     * @param Leave $model
     * @return string  HTML
     */
    public static function statusBadge(Leave $model)
    {
        $labels = Leave::statusOptions();
        $label  = self::STATUS_SHORT[$model->status]
            ?? ($labels[$model->status] ?? ucfirst(str_replace('_', ' ', $model->status)));

        $c = self::STATUSES[$model->status] ?? self::STATUSES[Leave::STATUS_DRAFT];

        return '<span style="' . self::PILL_CSS
             . ' font-weight:600; letter-spacing:.04em; text-transform:uppercase;'
             . ' background:' . $c['bg'] . '; color:' . $c['fg'] . ';">'
             . Html::encode($label)
             . '</span>';
    }

    /**
     * Leave-type badge — SQUARE, tinted with that type's own colour, plus
     * the "deducted from" chip for Short / Half Day requests.
     *
     * Square is deliberate: this is the one badge that is neither a status
     * nor a quantity, so a different shape keeps the column scannable.
     *
     * @param Leave $model
     * @return string  HTML
     */
    public static function typeBadge(Leave $model)
    {
        $c      = self::type($model->leave_type);
        $labels = Leave::leaveTypeOptions();
        $label  = $labels[$model->leave_type] ?? $model->leave_type;

        // "Casual Leave" -> "Casual": the column header already says Leave Type.
        $label = trim(preg_replace('/\s*leave\s*$/i', '', $label));

        $out = '<span style="' . self::SQUARE_CSS
             . ' background:' . $c['bg'] . '; color:' . $c['icon']
             . '; border:1px solid ' . self::fade($c['border']) . ';">'
             . Html::encode($label) . '</span>';

        if (in_array($model->leave_type, [Leave::LEAVE_TYPE_SHORT, Leave::LEAVE_TYPE_HALF_DAY], true)
            && !empty($model->deducted_from)) {

            $deductType  = $model->deducted_from === 'CASUAL' ? 'CASUAL' : 'ANNUAL';
            $deductLabel = $model->deducted_from === 'CASUAL'
                ? Yii::t('app', 'Casual')
                : Yii::t('app', 'Vacation');
            $d = self::type($deductType);

            $out .= ' <span style="' . self::SQUARE_CSS
                  . ' background:' . $d['bg'] . '; color:' . $d['icon'] . ';"'
                  . ' title="' . Yii::t('app', 'Deducted from') . '">'
                  . Html::encode($deductLabel) . ' 0.5</span>';
        }

        return $out;
    }

    /**
     * Role — neutral uppercase pill.
     *
     * Role is an attribute, not a state, so it carries no colour: every
     * role is the same grey. In this table, colour means status.
     *
     * @param int|string $userType
     * @return string  HTML
     */
    public static function roleText($userType)
    {
        $name = Constant::$userTypes[(int) $userType]['name'] ?? '—';

        return '<span style="' . self::PILL_CSS
             . ' font-weight:500; letter-spacing:.03em; text-transform:uppercase;'
             . ' background:#eaecf4; color:#6e707e;">'
             . Html::encode($name) . '</span>';
    }

    /**
     * Day count — lavender pill reading "17 days" / "1 day" / "0.5 days".
     *
     * @param Leave $model
     * @return string  HTML
     */
    public static function daysCell(Leave $model)
    {
        $value = (float) $model->total_days;

        // 1, 0.5, 11 — strip trailing .00 / .0
        $days = rtrim(rtrim(number_format($value, 2), '0'), '.');
        $unit = ($value == 1.0) ? Yii::t('app', 'day') : Yii::t('app', 'days');

        return '<span style="' . self::PILL_CSS
             . ' font-weight:500; font-variant-numeric:tabular-nums;'
             . ' background:#eef2ff; color:#4f46e5;">'
             . Html::encode($days . ' ' . $unit) . '</span>';
    }

    /**
     * Registers the responsive-table treatment for the leave module.
     *
     * Every grid here is wrapped in Bootstrap's .table-responsive, which
     * only gives horizontal scrolling — so on a phone the Actions column of
     * an 8-to-10 column table sits several swipes off-screen. That matters
     * most on the acting-officer queue, which people open from an email on
     * their phone specifically to tap Agree.
     *
     * Below 768px any table carrying .lv-rtable collapses into one card per
     * row, with each cell labelled. Labels are copied from the <thead> by
     * the script below rather than hand-written onto every column, so a
     * table opts in with a single class and no PHP changes.
     *
     * Registered under a key, so calling it from several views emits it once.
     *
     * @return void
     */
    public static function registerTableCss()
    {
        $view = Yii::$app->getView();

        $view->registerCss(<<<CSS
@media (max-width: 767.98px) {
    /* The scroll wrapper must stop clipping once rows become blocks. */
    .table-responsive:has(> .lv-rtable) { overflow-x: visible; }

    .lv-rtable thead { display: none; }
    .lv-rtable, .lv-rtable tbody, .lv-rtable tr, .lv-rtable td { display: block; width: 100%; }

    .lv-rtable tr {
        background: #fff;
        border: 1px solid #e3e6f0;
        border-radius: .35rem;
        padding: .6rem .8rem;
        margin-bottom: .6rem;
    }
    .lv-rtable tr:last-child { margin-bottom: 0; }

    .lv-rtable td {
        border: 0;
        padding: .3rem 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        text-align: right;
        min-height: 0;
    }

    /* The label, lifted from the column header. */
    .lv-rtable td::before {
        content: attr(data-label);
        flex: none;
        text-align: left;
        font-size: .625rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #858796;
    }

    /* First cell is the applicant — it carries its own name and avatar, so
       a label above it would just repeat the obvious. */
    .lv-rtable td:first-child { text-align: left; padding-bottom: .5rem; }
    .lv-rtable td:first-child::before { content: none; }

    /* Actions get the full width, so buttons are thumb-sized rather than
       squeezed into a right-hand column. */
    .lv-rtable td.lv-rtable-actions {
        display: block;
        text-align: left;
        margin-top: .4rem;
        padding-top: .6rem;
        border-top: 1px solid #eaecf4;
    }
    .lv-rtable td.lv-rtable-actions::before { content: none; }
    .lv-rtable td.lv-rtable-actions > div { display: flex; flex-wrap: wrap; gap: .4rem; }
    .lv-rtable td.lv-rtable-actions form { flex: 1 1 120px; margin: 0; }
    .lv-rtable td.lv-rtable-actions form .btn,
    .lv-rtable td.lv-rtable-actions > a.btn,
    .lv-rtable td.lv-rtable-actions > div > a.btn { width: 100%; margin: 0; }

    /* An empty cell would otherwise show a lone label and a blank line. */
    .lv-rtable td:empty { display: none; }

    /* Pagination and the summary line, centred once the table is cards. */
    .lv-rtable + .pagination, .grid-view .pagination { justify-content: center; }
}
CSS
        , [], 'lv-responsive-tables');

        // Copy each column header into its cells as data-label, and tag the
        // Actions cell. Doing it here keeps the markup free of per-column
        // attributes — a table opts in with the .lv-rtable class alone.
        $view->registerJs(<<<RTJS
(function () {
    document.querySelectorAll('table.lv-rtable').forEach(function (table) {
        var heads = Array.prototype.map.call(
            table.querySelectorAll('thead th'),
            function (th) { return th.textContent.replace(/\s+/g, ' ').trim(); }
        );

        table.querySelectorAll('tbody tr').forEach(function (row) {
            Array.prototype.forEach.call(row.children, function (cell, i) {
                var label = heads[i] || '';
                if (label && !cell.hasAttribute('data-label')) {
                    cell.setAttribute('data-label', label);
                }
                if (/^(actions?)\$/i.test(label)) {
                    cell.classList.add('lv-rtable-actions');
                }
            });
        });
    });
})();
RTJS
        , \yii\web\View::POS_END, 'lv-responsive-tables-js');
    }

    /**
     * Registers the module's button styles once per page.
     *
     * These live in a stylesheet rather than in style="" attributes for one
     * reason: an inline style cannot carry :hover or :focus, so every
     * inline-styled button in this module was completely inert on hover.
     * The key makes the registration idempotent, so calling it from several
     * helpers and views still emits the CSS only once.
     *
     * @return void
     */
    public static function registerButtonCss()
    {
        Yii::$app->getView()->registerCss(<<<CSS
.lv-btn { font-weight: 600; border-radius: .35rem; transition: background-color .15s ease, box-shadow .15s ease, transform .05s ease; }
.lv-btn:active { transform: translateY(1px); }

/* Tinted indigo — receipts, review/view links, update. */
.lv-btn-tint { background: #eef2ff; color: #4f46e5; border: 0; }
.lv-btn-tint:hover, .lv-btn-tint:focus { background: #dfe4ff; color: #3f37c9; box-shadow: 0 2px 6px rgba(79,70,229,.18); }
.lv-btn-tint:focus { outline: 0; box-shadow: 0 0 0 .2rem rgba(79,70,229,.25); }

/* Solid light green — starting or submitting a request. */
.lv-btn-green { background: #4CC38A; border-color: #4CC38A; color: #fff; }
.lv-btn-green:hover, .lv-btn-green:focus { background: #3BB77C; border-color: #3BB77C; color: #fff; box-shadow: 0 2px 6px rgba(76,195,138,.3); }
.lv-btn-green:focus { outline: 0; box-shadow: 0 0 0 .2rem rgba(76,195,138,.3); }

/* Outlined light red — cancelling. */
.lv-btn-cancel-outline { background: #fff; color: #e2607f; border: 1px solid #f2b9c8; font-weight: 500; }
.lv-btn-cancel-outline:hover, .lv-btn-cancel-outline:focus { background: #fdeef2; border-color: #eda6ba; color: #d14e6f; }
.lv-btn-cancel-outline:focus { outline: 0; box-shadow: 0 0 0 .2rem rgba(226,96,127,.2); }
CSS
        , [], 'lv-button-styles');
    }

    /**
     * Row action — tinted button linking to the request.
     *
     * Replaces the old disabled "Waiting" button on pending rows. A disabled
     * control sat in an Actions column without being an action, duplicated
     * the Status cell on the same row, and left the user nowhere to go. The
     * label carries the distinction instead: a request still awaiting a
     * decision is "Review", a finished one is "View".
     *
     * @param Leave  $model
     * @param string $route  defaults to the leave view page
     * @return string  HTML
     */
    public static function actionButton(Leave $model, $route = '/leave/view')
    {
        $isOpen = in_array(
            $model->status,
            [Leave::STATUS_PENDING, Leave::STATUS_PENDING_CC, Leave::STATUS_DRAFT],
            true
        );

        $label = $isOpen ? Yii::t('app', 'Review') : Yii::t('app', 'View');

        // Self-registering, so a grid that uses this helper never has to
        // remember to pull the stylesheet in.
        self::registerButtonCss();

        return Html::a(
            '<i class="fas fa-eye mr-1"></i>' . Html::encode($label),
            [$route, 'id' => $model->id],
            [
                'class' => 'btn btn-sm lv-btn lv-btn-tint',
                'style' => 'font-size:.75rem; padding:.25rem .7rem;',
            ]
        );
    }

    /**
     * Officer cell — initials avatar plus the officer's name.
     *
     * The avatar is tinted with the request's own leave-type colour, so a
     * row is identifiable by hue before any text is read.
     *
     * @param Leave $model
     * @return string  HTML
     */
    public static function officerCell(Leave $model)
    {
        $name = self::userName($model->user_id);
        $c    = self::type($model->leave_type);

        return '<span style="display:inline-flex; align-items:center; gap:8px;">'
             . self::avatar($name, $c['bg'], $c['icon'], 28)
             . '<span style="font-weight:600; color:#5a5c69;">' . Html::encode($name) . '</span>'
             . '</span>';
    }

    /**
     * Display name for a user id, resolved through their officer profile.
     * Falls back to the NIC, then to a placeholder — never to a blank cell.
     *
     * Used wherever the UI previously showed a raw NIC, which is an
     * identifier rather than something a reader recognises.
     *
     * @param int|null $userId
     * @return string
     */
    public static function userName($userId)
    {
        if (empty($userId)) {
            return Yii::t('app', 'Unknown officer');
        }

        $user = \common\models\User::findOne((int) $userId);
        if ($user && !empty($user->profile_id)) {
            $p = \backend\models\ProfileOfficers::findOne($user->profile_id);
            if ($p) {
                $name = trim($p->first_name . ' ' . $p->last_name);
                if ($name !== '') {
                    return $name;
                }
            }
        }

        return ($user && !empty($user->nic)) ? $user->nic : Yii::t('app', 'Unknown officer');
    }

    /**
     * First letter of the first two words: "Vishwa Botheju" -> VB
     *
     * @param string $name
     * @return string
     */
    public static function initials($name)
    {
        $out = '';
        foreach (preg_split('/\s+/', (string) $name) as $part) {
            if ($part !== '') {
                $out .= mb_strtoupper(mb_substr($part, 0, 1));
            }
            if (mb_strlen($out) >= 2) {
                break;
            }
        }
        return $out !== '' ? $out : '?';
    }

    /**
     * Circular initials avatar.
     *
     * @param string $name
     * @param string $bg
     * @param string $fg
     * @param int    $size  pixels
     * @return string  HTML
     */
    public static function avatar($name, $bg = '#eaecf4', $fg = '#6e707e', $size = 30)
    {
        return '<span style="width:' . $size . 'px; height:' . $size . 'px; border-radius:50%;'
             . ' flex:none; display:inline-flex; align-items:center; justify-content:center;'
             . ' font-size:' . ($size <= 28 ? '.62rem' : '.68rem') . '; font-weight:700;'
             . ' background:' . $bg . '; color:' . $fg . ';">'
             . Html::encode(self::initials($name)) . '</span>';
    }

    /**
     * Waiting cell — how long this request has sat at its current stage,
     * escalating from muted to amber to red.
     *
     * @param Leave $model
     * @return string  HTML
     */
    public static function waitingCell(Leave $model)
    {
        $days = $model->waitingDays();

        return '<span class="' . self::agingClass($days) . '" style="font-size:.75rem; white-space:nowrap;">'
             . '<i class="fas fa-hourglass-half mr-1"></i>'
             . Html::encode(self::agingLabel($days))
             . '</span>';
    }

    /**
     * Lightens a hex colour towards white by 60%, used for the hairline
     * border on a tinted badge so the outline reads as a soft edge rather
     * than a hard rule.
     *
     * @param string $hex  e.g. #6366f1
     * @return string
     */
    protected static function fade($hex)
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) {
            return '#' . $hex;
        }

        $out = '#';
        for ($i = 0; $i < 3; $i++) {
            $v    = hexdec(substr($hex, $i * 2, 2));
            $v    = (int) round($v + ((255 - $v) * 0.60));
            $out .= str_pad(dechex($v), 2, '0', STR_PAD_LEFT);
        }
        return $out;
    }

    /**
     * Compact progress dots — one small circle per workflow step, driven by
     * Leave::workflowSteps(). Lets an approver see at a glance that the
     * acting officer agreed and the CC recommended, without opening the
     * request. Inline styles only, so it works in a GridView cell.
     *
     * @param Leave $model
     * @return string  HTML
     */
    public static function dots(Leave $model)
    {
        $steps = $model->workflowSteps();
        $title = [];
        $out   = '';

        foreach ($steps as $step) {
            $s       = self::state($step['state']);
            $title[] = $step['label'];
            $out    .= '<i style="display:inline-block; width:7px; height:7px;'
                     . ' border-radius:50%; margin-right:3px;'
                     . ' background:' . $s['dot'] . ';"></i>';
        }

        return '<span title="' . Html::encode(implode(' → ', $title)) . '"'
             . ' style="white-space:nowrap;">' . $out . '</span>';
    }

    /**
     * District ids that currently have an Assistant Director account.
     *
     * A thin pass-through to UserTypeUtil so views do not have to reach into
     * the routing layer directly; the underlying lookup is memoised there.
     *
     * @return int[]
     */
    public static function adDistricts()
    {
        return \backend\config\UserTypeUtil::districtsWithAd();
    }

    /**
     * Is this request only on the DG's dashboard because its district has
     * no AD account?
     *
     * Head-office requests never fall back (the ITD covers them), and
     * requests from AD / ITD / Director accounts belong to the DG by design
     * — neither is a coverage gap.
     *
     * @param Leave $model
     * @param int[] $adDistricts  from adDistricts()
     * @return bool
     */
    public static function isDgFallback(Leave $model, array $adDistricts)
    {
        // Roles the DG approves by design, not by fallback.
        if (in_array((int) $model->user_type, Constant::DG_APPROVAL_TYPES, true)) {
            return false;
        }

        $workplace = trim((string) $model->applicant_workplace_type);
        if ($workplace === \backend\config\UserTypeUtil::WORKPLACE_HEAD_OFFICE) {
            return false;
        }

        // No district at all is a profile problem, still a fallback.
        if (empty($model->applicant_district)) {
            return true;
        }

        return !in_array((int) $model->applicant_district, $adDistricts, true);
    }

    /**
     * Bootstrap text class for a waiting time, so an old request stands out.
     *   under 2 days  — muted
     *   2 to 5 days   — amber
     *   over 5 days   — red
     *
     * @param int|null $days
     * @return string
     */
    public static function agingClass($days)
    {
        if ($days === null) {
            return 'text-muted';
        }
        if ($days > 5) {
            return 'text-danger font-weight-bold';
        }
        if ($days >= 2) {
            return 'text-warning font-weight-bold';
        }
        return 'text-muted';
    }

    /**
     * "Today" / "1 day" / "6 days" for a waiting time.
     *
     * @param int|null $days
     * @return string
     */
    public static function agingLabel($days)
    {
        if ($days === null) {
            return '—';
        }
        if ($days < 1) {
            return Yii::t('app', 'Today');
        }
        return $days === 1
            ? Yii::t('app', '1 day')
            : Yii::t('app', '{n} days', ['n' => $days]);
    }
}