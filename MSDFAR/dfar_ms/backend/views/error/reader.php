<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Variables received from the controller:
 *
 * @var yii\web\View $this
 * @var string $referenceCode
 * @var array|null $result
 * @var string|null $errorMessage
 * @var bool $searched
 */

$this->title = 'System Error Reader';
$this->params['breadcrumbs'][] = $this->title;

/*
 * Register all styles inside this view.
 * No external CSS file is required.
 */
$this->registerCss(<<<CSS

/* ============================================================
   SYSTEM ERROR READER
   ============================================================ */

.error-reader-page {
    padding-bottom: 40px;
}

/* Header */

.error-reader-header {
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
    padding: 30px;
    border-radius: 14px;
    background: linear-gradient(135deg, #0f172a 0%, #164e63 55%, #0f766e 100%);
    color: #ffffff;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.18);
}

.error-reader-header::before {
    position: absolute;
    top: -80px;
    right: -50px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.07);
    content: "";
}

.error-reader-header::after {
    position: absolute;
    right: 110px;
    bottom: -90px;
    width: 170px;
    height: 170px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    content: "";
}

.error-reader-header-content {
    position: relative;
    z-index: 1;
}

.error-reader-title {
    margin: 0 0 8px;
    color: #ffffff;
    font-size: 29px;
    font-weight: 700;
    line-height: 1.25;
}

.error-reader-subtitle {
    max-width: 760px;
    margin: 0;
    color: rgba(255, 255, 255, 0.82);
    font-size: 15px;
    line-height: 1.7;
}

.error-reader-header-badge {
    display: inline-block;
    margin-bottom: 14px;
    padding: 6px 12px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.10);
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

/* Security notice */

.error-security-notice {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 24px;
    padding: 16px 18px;
    border: 1px solid #bae6fd;
    border-left: 5px solid #0284c7;
    border-radius: 10px;
    background: #f0f9ff;
    color: #0c4a6e;
}

.error-security-icon {
    display: flex;
    flex: 0 0 34px;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #0284c7;
    color: #ffffff;
    font-size: 17px;
    font-weight: 700;
}

.error-security-content strong {
    display: block;
    margin-bottom: 3px;
    color: #075985;
}

.error-security-content p {
    margin: 0;
    font-size: 13px;
    line-height: 1.6;
}

/* Cards */

.error-reader-card {
    overflow: hidden;
    margin-bottom: 24px;
    border: 0;
    border-radius: 13px;
    background: #ffffff;
    box-shadow: 0 5px 22px rgba(15, 23, 42, 0.08);
}

.error-reader-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 17px 21px;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
}

.error-reader-card-title {
    margin: 0;
    color: #0f172a;
    font-size: 16px;
    font-weight: 700;
}

.error-reader-card-subtitle {
    margin-top: 3px;
    color: #64748b;
    font-size: 12px;
}

.error-reader-card-body {
    padding: 22px;
}

/* Search form */

.error-search-label {
    display: block;
    margin-bottom: 8px;
    color: #334155;
    font-size: 14px;
    font-weight: 700;
}

.error-search-input {
    height: 48px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    color: #0f172a;
    font-family: Consolas, Monaco, "Courier New", monospace;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    box-shadow: none;
}

.error-search-input:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.13);
}

.error-search-help {
    display: block;
    margin-top: 7px;
    color: #64748b;
    font-size: 12px;
}

.error-search-actions {
    display: flex;
    gap: 9px;
}

.error-search-button,
.error-clear-button {
    min-height: 48px;
    padding-right: 22px;
    padding-left: 22px;
    border-radius: 8px;
    font-weight: 700;
}

.error-search-button {
    border-color: #0f766e;
    background: #0f766e;
    color: #ffffff;
}

.error-search-button:hover,
.error-search-button:focus {
    border-color: #115e59;
    background: #115e59;
    color: #ffffff;
}

.error-clear-button {
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
}

.error-clear-button:hover,
.error-clear-button:focus {
    border-color: #94a3b8;
    background: #f8fafc;
    color: #1e293b;
}

/* Alerts */

.error-reader-alert {
    margin-bottom: 24px;
    padding: 16px 18px;
    border-radius: 10px;
}

.error-reader-alert-warning {
    border: 1px solid #fde68a;
    border-left: 5px solid #d97706;
    background: #fffbeb;
    color: #92400e;
}

.error-reader-alert-danger {
    border: 1px solid #fecaca;
    border-left: 5px solid #dc2626;
    background: #fef2f2;
    color: #991b1b;
}

/* Result header */

.error-result-status {
    display: inline-block;
    padding: 6px 11px;
    border-radius: 999px;
    background: #fee2e2;
    color: #991b1b;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.error-result-information {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.error-result-information-item {
    padding: 16px 20px;
    border-right: 1px solid #e2e8f0;
}

.error-result-information-item:last-child {
    border-right: 0;
}

.error-result-label {
    display: block;
    margin-bottom: 5px;
    color: #64748b;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.error-result-value {
    display: block;
    color: #1e293b;
    font-family: Consolas, Monaco, "Courier New", monospace;
    font-size: 13px;
    font-weight: 600;
    overflow-wrap: anywhere;
}

/* Log toolbar */

.error-log-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 13px 18px;
    border-bottom: 1px solid #1e293b;
    background: #111827;
}

.error-log-toolbar-title {
    color: #cbd5e1;
    font-family: Consolas, Monaco, "Courier New", monospace;
    font-size: 12px;
}

.error-log-actions {
    display: flex;
    gap: 8px;
}

.error-log-action-button {
    padding: 6px 11px;
    border: 1px solid #475569;
    border-radius: 6px;
    background: #1e293b;
    color: #e2e8f0;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}

.error-log-action-button:hover,
.error-log-action-button:focus {
    border-color: #64748b;
    background: #334155;
    color: #ffffff;
    outline: none;
}

.error-copy-message {
    display: none;
    color: #86efac;
    font-size: 12px;
}

/* Log output */

.error-log-output {
    max-height: 650px;
    min-height: 240px;
    margin: 0;
    padding: 24px;
    overflow: auto;
    border: 0;
    border-radius: 0 0 13px 13px;
    background: #0f172a;
    color: #dbeafe;
    font-family: Consolas, Monaco, "Courier New", monospace;
    font-size: 13px;
    line-height: 1.7;
    tab-size: 4;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

/* Empty state */

.error-empty-state {
    padding: 55px 25px;
    text-align: center;
}

.error-empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    margin: 0 auto 17px;
    border-radius: 50%;
    background: #ecfdf5;
    color: #0f766e;
    font-size: 27px;
    font-weight: 700;
}

.error-empty-title {
    margin: 0 0 7px;
    color: #1e293b;
    font-size: 19px;
    font-weight: 700;
}

.error-empty-description {
    max-width: 540px;
    margin: 0 auto;
    color: #64748b;
    font-size: 14px;
    line-height: 1.7;
}

/* Footer note */

.error-reader-footer-note {
    margin-top: 18px;
    color: #64748b;
    font-size: 12px;
    line-height: 1.6;
    text-align: center;
}

/* Responsive styles */

@media (max-width: 991px) {
    .error-result-information {
        grid-template-columns: 1fr;
    }

    .error-result-information-item {
        border-right: 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .error-result-information-item:last-child {
        border-bottom: 0;
    }
}

@media (max-width: 767px) {
    .error-reader-header {
        padding: 23px;
    }

    .error-reader-title {
        font-size: 23px;
    }

    .error-reader-card-header {
        align-items: flex-start;
        flex-direction: column;
        gap: 10px;
    }

    .error-search-actions {
        margin-top: 12px;
    }

    .error-search-button,
    .error-clear-button {
        width: 100%;
    }

    .error-log-toolbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .error-log-actions {
        width: 100%;
    }

    .error-log-action-button {
        flex: 1;
    }

    .error-log-output {
        padding: 18px;
        font-size: 12px;
    }
}

CSS);

/*
 * JavaScript for copying and selecting the error details.
 */
$copySuccessMessage = 'Error details copied to clipboard.';

$this->registerJs(<<<JS

(function () {
    var copyButton = document.getElementById('copy-error-log');
    var selectButton = document.getElementById('select-error-log');
    var logOutput = document.getElementById('error-log-output');
    var copyMessage = document.getElementById('error-copy-message');

    if (copyButton && logOutput) {
        copyButton.addEventListener('click', function () {
            var logText = logOutput.textContent || '';

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(logText).then(function () {
                    showCopyMessage();
                });
            } else {
                var temporaryTextArea = document.createElement('textarea');

                temporaryTextArea.value = logText;
                temporaryTextArea.style.position = 'fixed';
                temporaryTextArea.style.left = '-9999px';

                document.body.appendChild(temporaryTextArea);

                temporaryTextArea.focus();
                temporaryTextArea.select();

                try {
                    document.execCommand('copy');
                    showCopyMessage();
                } catch (error) {
                    console.error('Unable to copy log content.', error);
                }

                document.body.removeChild(temporaryTextArea);
            }
        });
    }

    if (selectButton && logOutput) {
        selectButton.addEventListener('click', function () {
            var selection = window.getSelection();
            var range = document.createRange();

            range.selectNodeContents(logOutput);
            selection.removeAllRanges();
            selection.addRange(range);
        });
    }

    function showCopyMessage() {
        if (!copyMessage) {
            return;
        }

        copyMessage.textContent = '{$copySuccessMessage}';
        copyMessage.style.display = 'inline';

        window.setTimeout(function () {
            copyMessage.style.display = 'none';
        }, 3000);
    }
})();

JS);

?>

<div class="error-reader-page">

    <!-- Page header -->
    <div class="error-reader-header">

        <div class="error-reader-header-content">

            <span class="error-reader-header-badge">
                DFAR IT Administration
            </span>

            <h1 class="error-reader-title">
                <?= Html::encode($this->title) ?>
            </h1>

            <p class="error-reader-subtitle">
                Search the protected application log using the error reference
                code shown on the system error page.
            </p>

        </div>

    </div>

    <!-- Security notice -->
    <div class="error-security-notice">

        <div class="error-security-icon">
            !
        </div>

        <div class="error-security-content">

            <strong>Restricted technical information</strong>

            <p>
                This page may display internal application routes, exception
                messages, source file paths and stack traces. Access must be
                limited to authorized DFAR IT administrators.
            </p>

        </div>

    </div>

    <!-- Search card -->
    <div class="error-reader-card">

        <div class="error-reader-card-header">

            <div>
                <h2 class="error-reader-card-title">
                    Search Error Reference
                </h2>

                <div class="error-reader-card-subtitle">
                    Enter the complete reference code generated by the system.
                </div>
            </div>

        </div>

        <div class="error-reader-card-body">

            <?= Html::beginForm(
                [Yii::$app->controller->route],
                'get',
                [
                    'id' => 'error-reader-search-form',
                    'autocomplete' => 'off',
                ]
            ) ?>

            <div class="row align-items-end">

                <div class="col-lg-8 col-md-7">

                    <?= Html::label(
                        'Error reference code',
                        'reference-code',
                        [
                            'class' => 'error-search-label',
                        ]
                    ) ?>

                    <?= Html::textInput(
                        'reference_code',
                        $referenceCode,
                        [
                            'id' => 'reference-code',
                            'class' =>
                                'form-control error-search-input',
                            'placeholder' =>
                                'ERR-20260711-143239-A81F9C20',
                            'maxlength' => 50,
                            'required' => true,
                            'spellcheck' => 'false',
                            'autofocus' => true,
                        ]
                    ) ?>

                    <small class="error-search-help">
                        Example:
                        ERR-20260711-143239-A81F9C20
                    </small>

                </div>

                <div class="col-lg-4 col-md-5">

                    <div class="error-search-actions">

                        <?= Html::submitButton(
                            'Search Error',
                            [
                                'class' =>
                                    'btn error-search-button',
                            ]
                        ) ?>

                        <?php if ($referenceCode !== ''): ?>

                            <?= Html::a(
                                'Clear',
                                [Yii::$app->controller->route],
                                [
                                    'class' =>
                                        'btn error-clear-button',
                                ]
                            ) ?>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

            <?= Html::endForm() ?>

        </div>

    </div>

    <!-- Search error/warning message -->
    <?php if ($errorMessage !== null): ?>

        <div class="error-reader-alert error-reader-alert-warning">

            <strong>Search result:</strong>

            <?= Html::encode($errorMessage) ?>

        </div>

    <?php endif; ?>

    <!-- Matching log result -->
    <?php if ($result !== null): ?>

        <div class="error-reader-card">

            <div class="error-reader-card-header">

                <div>
                    <h2 class="error-reader-card-title">
                        Technical Error Details
                    </h2>

                    <div class="error-reader-card-subtitle">
                        Matching record retrieved from the protected application
                        log.
                    </div>
                </div>

                <span class="error-result-status">
                    Restricted
                </span>

            </div>

            <!-- Result metadata -->
            <div class="error-result-information">

                <div class="error-result-information-item">

                    <span class="error-result-label">
                        Reference Code
                    </span>

                    <span class="error-result-value">
                        <?= Html::encode($referenceCode) ?>
                    </span>

                </div>

                <div class="error-result-information-item">

                    <span class="error-result-label">
                        Source Log File
                    </span>

                    <span class="error-result-value">
                        <?= Html::encode(
                            $result['fileName'] ?? 'Not available'
                        ) ?>
                    </span>

                </div>

                <div class="error-result-information-item">

                    <span class="error-result-label">
                        Log Last Modified
                    </span>

                    <span class="error-result-value">
                        <?= Html::encode(
                            $result['modifiedAt'] ?? 'Not available'
                        ) ?>
                    </span>

                </div>

            </div>

            <!-- Log toolbar -->
            <div class="error-log-toolbar">

                <div>
                    <span class="error-log-toolbar-title">
                        app.log — matching entry
                    </span>

                    <span
                        id="error-copy-message"
                        class="error-copy-message"
                    ></span>
                </div>

                <div class="error-log-actions">

                    <button
                        type="button"
                        id="select-error-log"
                        class="error-log-action-button"
                    >
                        Select All
                    </button>

                    <button
                        type="button"
                        id="copy-error-log"
                        class="error-log-action-button"
                    >
                        Copy Details
                    </button>

                </div>

            </div>

            <!-- Technical log output -->
            <pre
                id="error-log-output"
                class="error-log-output"
            ><?= Html::encode(
                $result['content'] ?? 'No log content available.'
            ) ?></pre>

        </div>

    <!-- Initial empty state -->
    <?php elseif (!$searched): ?>

        <div class="error-reader-card">

            <div class="error-empty-state">

                <div class="error-empty-icon">
                    &gt;_
                </div>

                <h2 class="error-empty-title">
                    Enter an error reference code
                </h2>

                <p class="error-empty-description">
                    The system will search the current application log and
                    rotated log files for the matching technical error entry.
                </p>

            </div>

        </div>

    <?php endif; ?>

    <div class="error-reader-footer-note">
        DFAR Management System | IT Division<br>
        Technical error information must not be shared with unauthorized users.
    </div>

</div>