<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InquirySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $this->title = 'Inquiry Alert System Dashboard';
// $this->params['breadcrumbs'][] = $this->title;

// Increase refresh rate to 30 seconds
$this->registerMetaTag(['http-equiv' => 'refresh', 'content' => '30']);

$this->registerCss("
    :root {
        --surface: #ffffff;
        --surface-alt: #f5f6fa;
        --border-subtle: #e3e6ef;
        --text: #0f172a;
        --muted: #65748b;
        --brand: linear-gradient(135deg, #2563eb, #7c3aed);
        --danger: linear-gradient(135deg, #ef4444, #f97316);
        --warning: linear-gradient(135deg, #fbbf24, #f59e0b);
        --success: linear-gradient(135deg, #22c55e, #14b8a6);
    }

    body {
        background:rgb(185, 185, 185);
        margin: 0;
    }

    .wrapper {
        min-height: 100vh;
        padding: 1.5rem 1rem;
        width: 100%;
        background: #eef2f9;
        display: flex;
        justify-content: center;
    }

    .content {
        max-width: 1200px;
        width: 100%;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: clamp(1.75rem, 3vw, 2.4rem);
        font-weight: 700;
        color: var(--text);
        margin-bottom: 0.25rem;
    }

    .page-header p {
        color: var(--muted);
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .chip {
        background: #e0e7ff;
        border-radius: 999px;
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
        color: #3730a3;
        font-weight: 500;
    }

    .refresh-btn {
        background: var(--brand);
        border: none;
        color: white;
        border-radius: 999px;
        padding: 0.7rem 1.5rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.25);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .refresh-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 30px rgba(37, 99, 235, 0.35);
    }

    .last-sync {
        font-size: 0.85rem;
        color: var(--muted);
        margin-top: 0.35rem;
    }

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: var(--surface);
        border-radius: 24px;
        padding: 1.75rem;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.5);
        position: relative;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        inset: 0;
        opacity: 0.08;
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.12);
    }

    .stat-card.danger::after,
    .stat-card.warning::after,
    .stat-card.brand::after,
    .stat-card.success::after {
        background: transparent;
    }

    .stat-card.danger {
        background: #D62728;
        color: #fff;
    }

    .stat-card.warning {
        background: #FF7F0E;
        color: #fff;
    }

    .stat-card.brand {
        background: #1F77B4;
        color: #fff;
    }

    .stat-card.success {
        background: #2CA02C;
        color: #fff;
    }

    .stat-card .icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #fff;
        margin-bottom: 1.25rem;
        position: relative;
        z-index: 1;
    }

    .stat-card.danger .icon,
    .stat-card.warning .icon,
    .stat-card.brand .icon,
    .stat-card.success .icon {
        background: rgba(255, 255, 255, 0.25);
        box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.12);
    }

    .stat-card .icon svg {
        width: 26px;
        height: 26px;
        fill: #fff;
    }

    .stat-card.danger h5,
    .stat-card.warning h5,
    .stat-card.brand h5,
    .stat-card.success h5,
    .stat-card.danger .trend,
    .stat-card.warning .trend,
    .stat-card.brand .trend,
    .stat-card.success .trend {
        color: rgba(255, 255, 255, 0.95);
    }

    .stat-card.danger p,
    .stat-card.warning p,
    .stat-card.brand p,
    .stat-card.success p {
        color: rgba(255, 255, 255, 0.9);
    }

    .stat-card.danger h2,
    .stat-card.warning h2,
    .stat-card.brand h2,
    .stat-card.success h2 {
        color: #fff;
    }


    .stat-card h5 {
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--muted);
        margin-bottom: 0.35rem;
        position: relative;
        z-index: 1;
    }

    .stat-card h2 {
        font-size: clamp(2.25rem, 4vw, 3rem);
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
        color: var(--text);
    }

    .trend {
        font-size: 0.85rem;
        color: var(--muted);
        margin-top: 0.4rem;
        position: relative;
        z-index: 1;
    }

    .progress {
        height: 12px;
        width: 100%;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 0.75rem;
    }

    .progress-bar {
        height: 100%;
        border-radius: inherit;
        background: rgba(255, 255, 255, 0.35);
        transition: width 0.4s ease;
    }

    .status-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .status-chip {
        border: 1px solid var(--border-subtle);
        border-radius: 999px;
        padding: 0.5rem 1.1rem;
        font-weight: 600;
        font-size: 0.9rem;
        background: var(--surface);
        color: var(--muted);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .status-chip.active {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #fff;
        box-shadow: 0 10px 20px rgba(29, 78, 216, 0.25);
    }

    .status-chip.active-link {
        color: #1d4ed8;
        background: rgba(37, 99, 235, 0.1);
        border-color: rgba(37, 99, 235, 0.2);
        text-decoration: none;
    }

    .status-chip.active-link:hover {
        background: rgba(37, 99, 235, 0.2);
    }

    .insights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .insight-card {
        background: var(--surface);
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }

    .insight-card h4 {
        margin: 0;
        font-weight: 700;
        color: var(--text);
    }

    .insight-card p {
        margin: 0.35rem 0 1rem;
        color: var(--muted);
    }

    .distribution-bar {
        width: 100%;
        height: 18px;
        border-radius: 999px;
        background: #e2e8f0;
        display: flex;
        overflow: hidden;
        margin-bottom: 0.75rem;
    }

    .distribution-segment {
        height: 100%;
    }

    .distribution-segment.open {
        background: #D62728;
    }

    .distribution-segment.progress {
        background: #FF7F0E;
    }

    .distribution-segment.completed {
        background: #2CA02C;
    }

    .legend {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.35rem;
    }

    .legend li {
        font-size: 0.9rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .legend .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .dot.open { background: #D62728; }
    .dot.progress { background: #FF7F0E; }
    .dot.completed { background: #2CA02C; }

    .focus-list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
    }

    .focus-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .focus-item .label {
        font-weight: 600;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .focus-item small {
        color: var(--muted);
    }

    .inquiry-panel {
        background: var(--surface);
        border-radius: 28px;
        padding: 1.5rem;
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .panel-header h3 {
        margin: 0;
        font-weight: 700;
        color: var(--text);
    }

    .panel-header .muted,
    .muted {
        margin: 0;
        color: var(--muted);
    }

    .grid-view table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .row-open td {
        background: rgba(214, 39, 40, 0.05);
    }

    .row-progress td {
        background: rgba(255, 127, 14, 0.05);
    }

    .row-completed td {
        background: rgba(44, 160, 44, 0.05);
    }

    .grid-view th {
        background: var(--surface-alt);
        color: var(--muted);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border: none;
        padding: 0.9rem;
    }

    .grid-view td {
        border-bottom: 1px solid var(--border-subtle);
        padding: 1rem 0.9rem;
        color: var(--text);
        font-weight: 500;
    }

    .grid-view tr:last-of-type td {
        border-bottom: none;
    }

    .status-badge {
        padding: 0.35rem 0.9rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .status-badge.open {
        background: rgba(214, 39, 40, 0.12);
        color: #D62728;
    }

    .status-badge.progress {
        background: rgba(255, 127, 14, 0.15);
        color: #FF7F0E;
    }

    .status-badge.completed {
        background: rgba(44, 160, 44, 0.15);
        color: #2CA02C;
    }

    .action-link {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-weight: 600;
        color: #2563eb;
        text-decoration: none;
    }

    .action-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    @media (max-width: 992px) {
        .wrapper {
            padding: 1.5rem;
        }
    }

    @media (max-width: 640px) {
        .page-header,
        .panel-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            flex-direction: column;
            align-items: flex-start;
        }

        .refresh-btn {
            width: 100%;
            justify-content: center;
        }

        .stat-card {
            padding: 1.25rem;
        }
    }

    @keyframes pulseCard {
        0% {
            transform: scale(1);
            box-shadow: 0 4px 12px rgba(214, 39, 40, 0.3);
        }
        50% {
            transform: scale(1.02);
            box-shadow: 0 8px 24px rgba(214, 39, 40, 0.5);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 4px 12px rgba(214, 39, 40, 0.3);
        }
    }

    .stat-card.danger.pulse {
        animation: pulseCard 1s infinite;
    }

    .stat-card.danger.new-inquiry {
        position: relative;
    }

    .stat-card.danger.new-inquiry::before {
        content: 'New';
        position: absolute;
        top: 16px;
        right: 16px;
        background: #D62728;
        color: white;
        padding: 0.2rem 0.65rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        z-index: 2;
        box-shadow: 0 2px 10px rgba(220, 38, 38, 0.3);
    }
");
?>

<?php
$totalInquiries = max(1, isset($counts['countTotal']) ? $counts['countTotal'] : ($counts['countOpen'] + $counts['countInProgress'] + $counts['countCompleted']));
// Calculate completion rate: if there are open or in-progress inquiries, it cannot be 100%
$completionRate = round(($counts['countCompleted'] / $totalInquiries) * 100);
// Ensure completion rate is not 100% if there are any open or in-progress inquiries
if (($counts['countOpen'] > 0 || $counts['countInProgress'] > 0) && $completionRate >= 100) {
    $completionRate = 99; // Cap at 99% if there are pending inquiries
}
$openPercent = round(($counts['countOpen'] / $totalInquiries) * 100);
$inProgressPercent = round(($counts['countInProgress'] / $totalInquiries) * 100);
$completedPercent = 100 - $openPercent - $inProgressPercent;
?>

<div class="wrapper">
    <div class="content">
        <div class="page-header">
            <div>
                <h1>Inquiry Management Dashboard</h1>
                <p>Real-time visibility into inquiry workloads and SLA progress.</p>
            </div>

        </div>

        <div class="dashboard-cards">
            <div class="stat-card danger <?= isset($hasNewInquiry) && $hasNewInquiry ? 'new-inquiry pulse' : '' ?>">
                <div class="icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z" />
                    </svg>
                </div>
                <h5>Open</h5>
                <h2 id="countOpen"><?= $counts['countOpen'] ?></h2>
            </div>

            <div class="stat-card warning">
                <div class="icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M7 6c-1.1 0-1.99.9-1.99 2L5 17c0 1.1.9 2 2 2h3v-9H7V6zm10 0h-3v4h-3v9h3c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-5-4h-2v4h2V2zm6 0h-2v4h2V2zm-12 0H4v4h2V2z" />
                    </svg>
                </div>
                <h5>In Progress</h5>
                <h2 id="countInProgress"><?= $counts['countInProgress'] ?></h2>
            </div>

            <div class="stat-card success">
                <div class="icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-4-4 1.41-1.41L11 14.17l5.59-5.59L18 10l-7 7z" />
                    </svg>
                </div>
                <h5>Closed this month</h5>
                <h2 id="countCompletedLastMonth"><?= $counts['countCompletedLastMonth'] ?></h2>
            </div>

            <div class="stat-card brand">
                <div class="icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M3 17h2v2H3c-1.1 0-2-.9-2-2V5c0-1.1.9-2 2-2h2v2H3v12zm18-2h2v4c0 1.1-.9 2-2 2h-4v-2h4v-4zm-6.59-6.41L11 12.17l-2.41-2.42L5 13.34V11l3.59-3.59L11 12l3.59-3.59L21 15v2l-6.59-6.41z" />
                    </svg>
                </div>
                <h5>Completion rate</h5>
                <h2 id="completionRate"><?= $completionRate ?>%</h2>
                <div class="progress">
                    <div class="progress-bar" style="width: <?= $completionRate ?>%"></div>
                </div>
            </div>
        </div>

        <div class="insights-grid">
            <div class="insight-card">
                <h4>Workload mix</h4>
                <div class="distribution-bar" aria-label="Workload distribution">
                    <span class="distribution-segment open" style="width: <?= $openPercent ?>%"></span>
                    <span class="distribution-segment progress" style="width: <?= $inProgressPercent ?>%"></span>
                    <span class="distribution-segment completed" style="width: <?= $completedPercent ?>%"></span>
                </div>
                <ul class="legend">
                    <li><span class="dot open"></span>Open • <?= $counts['countOpen'] ?> (<?= $openPercent ?>%)</li>
                    <li><span class="dot progress"></span>In progress • <?= $counts['countInProgress'] ?>
                        (<?= $inProgressPercent ?>%)</li>
                    <li><span class="dot completed"></span>Resolved • <?= $counts['countCompleted'] ?>
                        (<?= $completedPercent ?>%)</li>
                </ul>
            </div>

            <div class="insight-card">
                <h4>Response focus</h4>
                <ul class="focus-list">
                    <li class="focus-item">
                        <span class="label"><span class="dot open"></span>Open queue</span>
                        <div>
                            <strong><?= $counts['countOpen'] ?></strong>
                            <small>(<?= $openPercent ?>%)</small>
                        </div>
                    </li>
                    <li class="focus-item">
                        <span class="label"><span class="dot progress"></span>Active work</span>
                        <div>
                            <strong><?= $counts['countInProgress'] ?></strong>
                            <small>(<?= $inProgressPercent ?>%)</small>
                        </div>
                    </li>
                    <li class="focus-item">
                        <span class="label"><span class="dot completed"></span>Ready to close</span>
                        <div>
                            <strong><?= $counts['countCompleted'] ?></strong>
                            <small>(<?= $completedPercent ?>%)</small>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="status-filters">
            <button class="status-chip active" data-status="">All</button>
            <button class="status-chip" data-status="Open">Open</button>
            <button class="status-chip" data-status="In Progress">In Progress</button>
            <button class="status-chip" data-status="Completed">Completed</button>
        </div>

        <div class="inquiry-panel">
            <div class="panel-header">
                <div>
                    <h3>Inquiry backlog</h3>
                    <p class="muted">Click a row to review details and respond.</p>
                </div>
                <?= Html::a('View all inquiries', ['inquiry/index'], ['class' => 'status-chip active-link']) ?>
            </div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'rowOptions' => function ($model) {
                                $class = '';
                                switch ($model->Inquiry_Status) {
                                    case 'Open':
                                        $class = 'row-open';
                                        break;
                                    case 'In Progress':
                                        $class = 'row-progress';
                                        break;
                                    case 'Completed':
                                        $class = 'row-completed';
                                        break;
                                }
                                return ['class' => $class];
                            },
                'columns' => [
                    'Name',
                    'phone_number',
                    'Office',
                    [
                        'attribute' => 'Inquiry_Status',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        $status = $model->Inquiry_Status;
                                        $class = 'open';
                                        if ($status === 'Completed') {
                                            $class = 'completed';
                                        } elseif ($status === 'In Progress') {
                                            $class = 'progress';
                                        }
                                        return '<span class=\"status-badge ' . $class . '\">' . Html::encode($status) . '</span>';
                                    },
                        'filter' => ['Completed' => 'Completed', 'Open' => 'Open', 'In Progress' => 'In Progress'],
                        'contentOptions' => ['style' => 'width:160px;'],
                    ],
                    [
                        'attribute' => 'Action',
                        'format' => 'raw',
                        'value' => function ($model) {
                                        return Html::a('View details <i class=\"bi bi-arrow-right\"></i>', ['inquiry/view', 'id' => $model->Inquiry_ID], [
                                            'class' => 'action-link',
                                            'encode' => false,
                                        ]);
                                    }
                    ],
                ],
                'tableOptions' => ['class' => 'table table-hover align-middle'],
                'options' => ['class' => 'grid-view table-responsive'],
            ]); ?>
        </div>
    </div>

    <script>
        let previousCountOpen = Number(<?= $counts['countOpen'] ?>);
        let previousCountInProgress = Number(<?= $counts['countInProgress'] ?>);
        let previousCountCompleted = Number(<?= $counts['countCompleted'] ?>);
        let countCompletedLastMonth = Number(<?= $counts['countCompletedLastMonth'] ?>);

        const alertSound = new Audio('<?= Url::to('@web/sounds/alert.mp3') ?>');



        function updateDashboard() {
            $.ajax({
                url: '<?= Url::to(['inquiry/dashboard']) ?>',
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response && typeof response.countOpen !== 'undefined') {
                        // Update counts
                        const countOpen = Number(response.countOpen);
                        const countInProgress = Number(response.countInProgress);
                        const countCompleted = Number(response.countCompleted);
                        const countTotal = Math.max(1, response.countTotal ? Number(response.countTotal) : (countOpen + countInProgress + countCompleted));
                        
                        $('#countOpen').text(countOpen);
                        $('#countInProgress').text(countInProgress);
                        $('#countCompleted').text(countCompleted);
                        $('#countCompletedLastMonth').text(response.countCompletedLastMonth);
                        
                        // Calculate completion rate - dynamically based on open and in-progress counts
                        // Completion rate = (Completed / Total) * 100
                        // It will automatically decrease when open/in-progress increase
                        let completionRate = Math.round((countCompleted / countTotal) * 100);
                        // Ensure completion rate is not 100% if there are any open or in-progress inquiries
                        if ((countOpen > 0 || countInProgress > 0) && completionRate >= 100) {
                            completionRate = 99; // Cap at 99% if there are pending inquiries
                        }
                        $('#completionRate').text(completionRate + '%');
                        $('.progress-bar').css('width', completionRate + '%');
                        
                        // Update percentages for distribution and insights
                        const openPercent = Math.round((countOpen / countTotal) * 100);
                        const inProgressPercent = Math.round((countInProgress / countTotal) * 100);
                        const completedPercent = Math.round((countCompleted / countTotal) * 100);
                        
                        // Update distribution bar
                        $('.distribution-segment.open').css('width', openPercent + '%');
                        $('.distribution-segment.progress').css('width', inProgressPercent + '%');
                        $('.distribution-segment.completed').css('width', completedPercent + '%');
                        
                        // Update legend percentages
                        $('.legend li:eq(0)').html('<span class="dot open"></span>Open • ' + countOpen + ' (' + openPercent + '%)');
                        $('.legend li:eq(1)').html('<span class="dot progress"></span>In progress • ' + countInProgress + ' (' + inProgressPercent + '%)');
                        $('.legend li:eq(2)').html('<span class="dot completed"></span>Resolved • ' + countCompleted + ' (' + completedPercent + '%)');
                        
                        // Update focus list percentages
                        $('.focus-item:eq(0) strong').text(countOpen);
                        $('.focus-item:eq(0) small').text('(' + openPercent + '%)');
                        $('.focus-item:eq(1) strong').text(countInProgress);
                        $('.focus-item:eq(1) small').text('(' + inProgressPercent + '%)');
                        $('.focus-item:eq(2) strong').text(countCompleted);
                        $('.focus-item:eq(2) small').text('(' + completedPercent + '%)');

                        const openCard = $('.stat-card.danger');

                        // Check if open count is 0
                        if (Number(response.countOpen) === 0) {
                            // Remove animation and new tag
                            openCard.removeClass('new-inquiry pulse');
                            localStorage.removeItem('hasNewInquiry');
                        }
                        // Check for new inquiries only if count is greater than 0
                        else if (Number(response.countOpen) > previousCountOpen) {

                            // Play alert sound (will play automatically if browser allows)
                            alertSound.play().catch(error => {
                                console.log('Audio autoplay blocked by browser:', error);
                            });

                            // Add animation classes
                            openCard.addClass('new-inquiry pulse');

                            // Store the new inquiry state in localStorage
                            localStorage.setItem('hasNewInquiry', 'true');

                            // Flash effect
                            openCard.fadeOut(100).fadeIn(100);
                        }

                        // Update previous counts
                        previousCountOpen = Number(response.countOpen);
                        previousCountInProgress = Number(response.countInProgress);
                        previousCountCompleted = Number(response.countCompleted);
                        countCompletedLastMonth = Number(response.countCompletedLastMonth);
                        updateLastSync();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Failed to fetch data:', textStatus, errorThrown);
                }
            });
        }

        function updateLastSync() {
            const label = document.getElementById('lastSyncLabel');
            if (label) {
                label.textContent = new Date().toLocaleTimeString();
            }
        }

        function bindStatusFilters() {
            const chips = $('.status-chip');
            const statusField = $('#inquirysearch-inquiry_status, [name=\"InquirySearch[Inquiry_Status]\"]').first();
            chips.on('click', function () {
                chips.removeClass('active');
                $(this).addClass('active');
                const status = $(this).data('status');
                if (statusField.length) {
                    statusField.val(status);
                    statusField.trigger('change');
                }
            });
        }

        // Check for new inquiries on page load
        $(document).ready(function () {
            const openCount = Number($('#countOpen').text());
            if (localStorage.getItem('hasNewInquiry') === 'true' && openCount > 0) {
                $('.stat-card.danger').addClass('new-inquiry pulse');
            }

            $('#manualRefresh').on('click', function () {
                updateDashboard();
            });

            bindStatusFilters();
            updateLastSync();
        });

        // Remove animation when clicking the View button
        $(document).on('click', 'a[href*=\"view\"]', function () {
            $('.stat-card.danger').removeClass('new-inquiry pulse');
            localStorage.removeItem('hasNewInquiry');
        });

        // Set interval for dashboard updates (every 5 seconds for real-time updates)
        setInterval(updateDashboard, 5000);
    </script>

</div>
</div>
