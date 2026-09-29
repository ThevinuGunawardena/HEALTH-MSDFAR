<?php

use backend\config\Constant;
use backend\controllers\FishermanController;
use kartik\export\ExportMenu;
use yii\bootstrap4\LinkPager;
use yii\grid\GridView;
use yii\helpers\Json;
use yii\helpers\Url;
use backend\components\SecurityHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFishermanSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Fishermen Profiles');
$this->params['breadcrumbs'][] = $this->title;

$displayRecord = function ($model) {
    if (
        (int)$model->renew === 1
        && !empty($model->renew_id)
        && $model->renewalRecord !== null
    ) {
        return $model->renewalRecord;
    }

    return $model;
};

$exportMenu = [
    'fisherman_uid',
    'first_name',
    'last_name',
    'nic',
    'gender',
    [
        'attribute' => 'district',
        'format' => 'text',
        'label' => 'District',
        'value' => function ($model) {
            return $model->district0->name ?? '';
        }
    ],
    [
        'attribute' => 'division',
        'format' => 'text',
        'label' => 'Division',
        'value' => function ($model) {
            return $model->division0->name ?? '';
        }
    ],
    [
        'attribute' => 'landing_site',
        'format' => 'text',
        'label' => 'Landing Site',
        'value' => function ($model) {
            return $model->landingSite->name ?? '';
        }
    ],
    [
        'label' => 'Request Type',
        'value' => function ($model) {
            return (
                (int)$model->renew === 1
                && !empty($model->renew_id)
                && $model->renewalRecord !== null
            ) ? 'Renewal' : 'New License';
        }
    ],
    [
        'attribute' => 'status',
        'format' => 'text',
        'value' => function ($model) use ($displayRecord) {
            $record = $displayRecord($model);

            return Constant::$licenseStatus[$record->status] ?? $record->status;
        }
    ],
    [
        'attribute' => 'created',
        'label' => 'Created Date',
        'value' => function ($model) use ($displayRecord) {
            $record = $displayRecord($model);

            return $record->created ?? '';
        }
    ],
    [
        'attribute' => 'expire_date',
        'label' => 'Expire Date',
        'value' => function ($model) use ($displayRecord) {
            $record = $displayRecord($model);

            return $record->expire_date ?? '';
        }
    ],
    [
        'attribute' => 'approval_stage',
        'label' => 'Approval Stage',
        'value' => function ($model) use ($displayRecord) {
            $record = $displayRecord($model);

            return Constant::$userTypes[$record->approval_stage]['name']
                ?? $record->approval_stage;
        }
    ],
];

$gridColumns = [
    'fisherman_uid',
    'first_name',
    'last_name',
    'nic',
    'gender',
    
    [
        'attribute' => 'expire_date',
        'format' => ['date', 'php:Y-m-d'],
        'label' => 'Expire Date',
        'value' => function ($model) use ($displayRecord) {
            $record = $displayRecord($model);

            return $record->expire_date ?? null;
        }
    ],
    [
        'attribute' => 'status',
        'format' => 'text',
        'value' => function ($model) use ($displayRecord) {
            $record = $displayRecord($model);

            return Constant::$licenseStatus[$record->status] ?? $record->status;
        }
    ],
    [
        'attribute' => 'approval_stage',
        'format' => 'text',
        'value' => function ($model) use ($displayRecord) {
            $record = $displayRecord($model);

            return Constant::$userTypes[$record->approval_stage]['name']
                ?? $record->approval_stage;
        }
    ],
    [
    'attribute' => 'Action',
    'format' => 'raw',
    'value' => static function ($model) {
        return Html::a(
            'View',
            [
                '/fisherman/view',
                'token' => SecurityHelper::encryptId(
                    \backend\models\ProfileFisherman::class,
                    $model->id
                ),
            ],
            [
                'class' => 'btn btn-sm btn-primary',
            ]
        );
    },
],
];
?>
<?php echo $this->render('../common/stac', ["counts" => FishermanController::getStacs()]); ?>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <?= ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $exportMenu,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
                'dropdownOptions' => [
                    'label' => 'Export All',
                    'class' => 'btn btn-outline-secondary btn-default'
                ]
            ]);
            ?>

            <?= GridView::widget([
                'pager' => ['class' => LinkPager::class, 'firstPageLabel' => 'First', 'lastPageLabel' => 'Last'],
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns
            ]);


            ?>


        </div>
    </div>
</div>
<?php

$localizedBackfillUrl =
    \yii\helpers\Json::htmlEncode(
        \yii\helpers\Url::to([
            '/fisherman/backfill-localized-details',
        ])
    );

$localizedBackfillScript = <<<JS
(function () {
    'use strict';

    const BATCH_SIZE = 10
    const DEFAULT_REQUESTS_PER_MINUTE = 10;

    let backfillRequestRunning = false;

    window.fishermanLocalizedBackfillUrl =
        {$localizedBackfillUrl};

    /**
     * Converts a value into an integer.
     */
    function toInteger(value) {
        const number = Number(value);

        return Number.isInteger(number)
            ? number
            : null;
    }

    /**
     * Validates a start and end ID.
     */
    function validateIdRange(
        startId,
        endId
    ) {
        if (
            startId === null ||
            startId <= 0
        ) {
            console.error(
                'Please provide a valid start ID.'
            );

            return false;
        }

        if (
            endId === null ||
            endId <= 0
        ) {
            console.error(
                'Please provide a valid end ID.'
            );

            return false;
        }

        if (endId < startId) {
            console.error(
                'The end ID must be greater than or equal to the start ID.'
            );

            return false;
        }

        return true;
    }

    /**
     * Returns the local-storage key used for
     * a manually selected range.
     */
    function getRangeCursorKey(
        startId,
        endId
    ) {
        return 'localizedBackfillCursor_'
            + startId
            + '_'
            + endId;
    }

    /**
     * Returns the local-storage key used for
     * descending five-ID processing.
     */
    function getDescendingCursorKey(
        lastId,
        firstId
    ) {
        return 'localizedBackfillDescending_'
            + firstId
            + '_'
            + lastId;
    }

    /**
     * Displays the result returned by the controller.
     */
    function displayBackfillResponse(
        response,
        durationSeconds
    ) {
        console.log(
            'Full response:',
            response
        );

        console.table(
            response.errors || []
        );

        console.table([
            {
                startId:
                    response.startId || 0,

                endId:
                    response.endId || 0,

                processed:
                    response.processed || 0,

                updated:
                    response.updated || 0,

                unchanged:
                    response.unchanged || 0,

                failed:
                    response.failed || 0,

                nextAfterId:
                    response.nextAfterId || 0,

                hasMore:
                    response.hasMore === true,

                durationSeconds:
                    durationSeconds
            }
        ]);
    }

    /**
     * Sends one backfill request to the controller.
     */
    async function sendLocalizedBackfillRequest(
        startId,
        endId,
        afterId,
        dryRun,
        requestsPerMinute
    ) {
        if (backfillRequestRunning) {
            console.warn(
                'Another localized backfill request is already running.'
            );

            return null;
        }

        backfillRequestRunning = true;

        const startedAt = Date.now();

        console.log(
            'Starting localized backfill batch:',
            {
                startId: startId,
                endId: endId,
                afterId: afterId,
                limit: BATCH_SIZE,
                requestsPerMinute:
                    requestsPerMinute,
                dryRun: dryRun
            }
        );

        try {
            const response = await $.ajax({
                url:
                    window.fishermanLocalizedBackfillUrl,

                type:
                    'POST',

                dataType:
                    'json',

                /*
                 * Allow the DRP requests to complete.
                 */
                timeout:
                    300000,

                data: {
                    startId:
                        startId,

                    endId:
                        endId,

                    afterId:
                        afterId,

                    limit:
                        BATCH_SIZE,

                    requestsPerMinute:
                        requestsPerMinute,

                    dryRun:
                        dryRun,

                    [yii.getCsrfParam()]:
                        yii.getCsrfToken()
                }
            });

            const durationSeconds = Math.round(
                (Date.now() - startedAt) /
                1000
            );

            if (
                !response ||
                response.success !== true
            ) {
                console.error(
                    'Backfill was not successful:',
                    response
                );

                return response || null;
            }

            displayBackfillResponse(
                response,
                durationSeconds
            );

            return response;
        } catch (xhr) {
            console.error(
                'Localized backfill failed:',
                {
                    status:
                        xhr.status,

                    statusText:
                        xhr.statusText || '',

                    response:
                        xhr.responseText || '',

                    startId:
                        startId,

                    endId:
                        endId,

                    afterId:
                        afterId,

                    durationSeconds:
                        Math.round(
                            (
                                Date.now() -
                                startedAt
                            ) / 1000
                        )
                }
            );

            return null;
        } finally {
            backfillRequestRunning = false;
        }
    }

    /**
     * Runs one five-record batch inside a manually
     * selected start-ID and end-ID range.
     *
     * Example:
     * await runLocalizedBackfillRange(
     *     112603,
     *     112608,
     *     false
     * );
     *
     * Run the same command again when hasMore=true.
     */
    window.runLocalizedBackfillRange =
    async function (
        startId,
        endId,
        dryRun = false,
        requestsPerMinute =
            DEFAULT_REQUESTS_PER_MINUTE
    ) {
        startId = toInteger(startId);
        endId = toInteger(endId);

        requestsPerMinute =
            toInteger(requestsPerMinute);

        if (
            !validateIdRange(
                startId,
                endId
            )
        ) {
            return null;
        }

        if (
            requestsPerMinute === null ||
            requestsPerMinute < 1 ||
            requestsPerMinute > 60
        ) {
            console.error(
                'Requests per minute must be between 1 and 60.'
            );

            return null;
        }

        const cursorKey =
            getRangeCursorKey(
                startId,
                endId
            );

        const defaultAfterId =
            startId - 1;

        const storedCursor =
            localStorage.getItem(
                cursorKey
            );

        let afterId =
            defaultAfterId;

        if (storedCursor !== null) {
            const parsedCursor =
                toInteger(storedCursor);

            if (
                parsedCursor !== null &&
                parsedCursor >=
                    defaultAfterId &&
                parsedCursor <= endId
            ) {
                afterId =
                    parsedCursor;
            }
        }

        const response =
            await sendLocalizedBackfillRequest(
                startId,
                endId,
                afterId,
                Boolean(dryRun),
                requestsPerMinute
            );

        if (
            !response ||
            response.success !== true
        ) {
            return response;
        }

        /*
         * Save progress only after a real update.
         * Dry runs never move the cursor.
         */
        if (dryRun === false) {
            localStorage.setItem(
                cursorKey,
                String(
                    response.nextAfterId
                )
            );
        }

        if (response.hasMore === true) {
            console.log(
                dryRun
                    ? 'Dry run completed. Run the same range with dryRun=false.'
                    : 'Five eligible records completed. Run the same command again for the next batch.'
            );
        } else {
            console.log(
                'The selected ID range has been completed.'
            );
        }

        return response;
    };

    /**
     * Resets a manually selected ID range.
     */
    window.resetLocalizedBackfillRange =
    function (
        startId,
        endId
    ) {
        startId = toInteger(startId);
        endId = toInteger(endId);

        if (
            !validateIdRange(
                startId,
                endId
            )
        ) {
            return;
        }

        const cursorKey =
            getRangeCursorKey(
                startId,
                endId
            );

        localStorage.removeItem(
            cursorKey
        );

        console.log(
            'Backfill cursor reset:',
            {
                startId: startId,
                endId: endId
            }
        );
    };

    /**
     * Displays the saved cursor for a manually
     * selected range.
     */
    window.getLocalizedBackfillCursor =
    function (
        startId,
        endId
    ) {
        startId = toInteger(startId);
        endId = toInteger(endId);

        if (
            !validateIdRange(
                startId,
                endId
            )
        ) {
            return null;
        }

        const cursor =
            localStorage.getItem(
                getRangeCursorKey(
                    startId,
                    endId
                )
            );

        console.log(
            'Saved range cursor:',
            cursor
        );

        return cursor;
    };

    /**
     * Processes one group of five IDs, starting
     * from the newest ID and moving backward.
     *
     * First call:
     * 112773 - 112777
     *
     * Second call:
     * 112768 - 112772
     *
     * Continue calling the same command.
     */
    window.runNextLocalizedBackfill5Descending =
    async function (
        lastId,
        firstId = 1,
        dryRun = false,
        requestsPerMinute =
            DEFAULT_REQUESTS_PER_MINUTE
    ) {
        lastId = toInteger(lastId);
        firstId = toInteger(firstId);

        requestsPerMinute =
            toInteger(requestsPerMinute);

        if (
            !validateIdRange(
                firstId,
                lastId
            )
        ) {
            return null;
        }

        if (
            requestsPerMinute === null ||
            requestsPerMinute < 1 ||
            requestsPerMinute > 60
        ) {
            console.error(
                'Requests per minute must be between 1 and 60.'
            );

            return null;
        }

        const cursorKey =
            getDescendingCursorKey(
                lastId,
                firstId
            );

        const storedCursor =
            localStorage.getItem(
                cursorKey
            );

        let currentEndId =
            lastId;

        if (storedCursor !== null) {
            const parsedCursor =
                toInteger(storedCursor);

            if (parsedCursor !== null) {
                currentEndId =
                    parsedCursor;
            }
        }

        if (currentEndId < firstId) {
            console.log(
                'All selected registrations have been completed.'
            );

            return {
                success: true,
                completed: true,
                hasMore: false,
                firstId: firstId,
                lastId: lastId
            };
        }

        const currentStartId =
            Math.max(
                firstId,
                currentEndId -
                    BATCH_SIZE +
                    1
            );

        /*
         * This range contains at most five IDs,
         * so the request begins before its first ID.
         */
        const afterId =
            currentStartId - 1;

        const response =
            await sendLocalizedBackfillRequest(
                currentStartId,
                currentEndId,
                afterId,
                Boolean(dryRun),
                requestsPerMinute
            );

        if (
            !response ||
            response.success !== true
        ) {
            return response;
        }

        if (dryRun === false) {
            const nextEndId =
                currentStartId - 1;

            localStorage.setItem(
                cursorKey,
                String(nextEndId)
            );

            if (nextEndId >= firstId) {
                console.log(
                    'Next five-ID batch will end at ID:',
                    nextEndId
                );
            } else {
                console.log(
                    'All selected registrations have been completed.'
                );
            }
        } else {
            console.log(
                'Dry run completed. Run the same command with dryRun=false.'
            );
        }

        return response;
    };

    /**
     * Resets newest-to-oldest processing.
     */
    window.resetLocalizedBackfillDescending =
    function (
        lastId,
        firstId = 1
    ) {
        lastId = toInteger(lastId);
        firstId = toInteger(firstId);

        if (
            !validateIdRange(
                firstId,
                lastId
            )
        ) {
            return;
        }

        const cursorKey =
            getDescendingCursorKey(
                lastId,
                firstId
            );

        localStorage.removeItem(
            cursorKey
        );

        console.log(
            'Descending backfill cursor reset:',
            {
                firstId: firstId,
                lastId: lastId
            }
        );
    };

    /**
     * Displays the next end ID for newest-to-oldest
     * processing.
     */
    window.getLocalizedBackfillDescendingCursor =
    function (
        lastId,
        firstId = 1
    ) {
        lastId = toInteger(lastId);
        firstId = toInteger(firstId);

        if (
            !validateIdRange(
                firstId,
                lastId
            )
        ) {
            return null;
        }

        const cursor =
            localStorage.getItem(
                getDescendingCursorKey(
                    lastId,
                    firstId
                )
            );

        console.log(
            'Saved descending cursor:',
            cursor === null
                ? lastId
                : cursor
        );

        return cursor === null
            ? String(lastId)
            : cursor;
    };

    /**
     * Generates individual JavaScript commands
     * from the newest ID to the oldest ID.
     *
     * It returns the generated commands as text.
     */
    window.generateLocalizedBackfillCommands =
    function (
        lastId,
        firstId = 1,
        dryRun = false
    ) {
        lastId = toInteger(lastId);
        firstId = toInteger(firstId);

        if (
            !validateIdRange(
                firstId,
                lastId
            )
        ) {
            return '';
        }

        const commands = [];

        let currentEndId =
            lastId;

        while (currentEndId >= firstId) {
            const currentStartId =
                Math.max(
                    firstId,
                    currentEndId -
                        BATCH_SIZE +
                        1
                );

            commands.push(
                'await runLocalizedBackfillRange('
                + currentStartId
                + ', '
                + currentEndId
                + ', '
                + (
                    dryRun
                        ? 'true'
                        : 'false'
                )
                + ');'
            );

            currentEndId =
                currentStartId - 1;
        }

        const output =
            commands.join('\\n');

        console.log(output);

        return output;
    };
})();
JS;

$this->registerJs(
    $localizedBackfillScript,
    \yii\web\View::POS_READY
);
?>