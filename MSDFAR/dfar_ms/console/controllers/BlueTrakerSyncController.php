<?php

namespace console\controllers;

use Yii;
use RuntimeException;
use Throwable;
use yii\console\Controller;
use yii\console\ExitCode;
use backend\models\BluetrakerReports;
use backend\models\BlutrackerApiSyncStatus;

class BlueTrakerSyncController extends Controller
{
    /**
     * Synchronize BlueTraker reports.
     */
    public function actionIndex(): int
    {
        $this->writeLog('BlueTraker sync started.');

        $username = Yii::$app->params['blueTrackerUsername'] ?? null;
        $password = Yii::$app->params['blueTrackerPassword'] ?? null;
        $apiUrl = Yii::$app->params['blueTrackerApiUrl'] ?? null;

        if (
            empty($username)
            || empty($password)
            || empty($apiUrl)
        ) {
            $this->writeLog(
                'ERROR: BlueTraker API configuration is missing.'
            );

            return ExitCode::CONFIG;
        }

        $syncStatus = BlutrackerApiSyncStatus::find()
            ->where([
                'ApiName' => 'BlueTrakerSriLanka',
            ])
            ->one();

        if ($syncStatus === null) {
            $this->writeLog(
                'ERROR: Sync status record was not found.'
            );

            return ExitCode::DATAERR;
        }

        $lastMessageId = (int) $syncStatus->LastMessageId;

        do {
            $batchStartMessageId = $lastMessageId;

            $this->writeLog(
                "Fetching from MessageId: {$lastMessageId}"
            );

            try {
                $reports = $this->fetchReports(
                    $apiUrl,
                    $username,
                    $password,
                    $lastMessageId
                );
            } catch (Throwable $exception) {
                $this->writeLog(
                    'ERROR: ' . $exception->getMessage()
                );

                return ExitCode::UNAVAILABLE;
            }

            if (empty($reports)) {
                $this->writeLog('No new reports found.');

                /*
                 * Record that the API was checked successfully,
                 * even when there were no new reports.
                 */
                $syncStatus->LastSyncTime = date(
                    'Y-m-d H:i:s'
                );

                if (!$syncStatus->save()) {
                    $this->writeLog(
                        'ERROR: Failed to update LastSyncTime: '
                        . json_encode(
                            $syncStatus->errors,
                            JSON_UNESCAPED_UNICODE
                            | JSON_UNESCAPED_SLASHES
                        )
                    );

                    return ExitCode::DATAERR;
                }

                break;
            }

            /*
             * Sort the batch from the lowest MessageId
             * to the highest MessageId.
             */
            usort(
                $reports,
                static function (
                    array $first,
                    array $second
                ): int {
                    return (
                        (int) ($first['MessageId'] ?? 0)
                    ) <=> (
                        (int) ($second['MessageId'] ?? 0)
                    );
                }
            );

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $maxMessageId = $lastMessageId;
                $savedCount = 0;
                $existingCount = 0;
                $missingVesselNameCount = 0;
                $skippedMissingIdentifierCount = 0;

                foreach ($reports as $item) {
                    $messageId = isset($item['MessageId'])
                        ? (int) $item['MessageId']
                        : 0;

                    if ($messageId <= 0) {
                        throw new RuntimeException(
                            'API returned an invalid MessageId.'
                        );
                    }

                    /*
                     * Track the highest API MessageId even when
                     * the record already exists or must be skipped.
                     */
                    if ($messageId > $maxMessageId) {
                        $maxMessageId = $messageId;
                    }

                    $exists = BluetrakerReports::find()
                        ->where([
                            'MessageId' => $messageId,
                        ])
                        ->exists();

                    if ($exists) {
                        $existingCount++;
                        continue;
                    }

                    $vesselName = trim(
                        (string) (
                            $item['VesselName'] ?? ''
                        )
                    );

                    $publicDeviceId = trim(
                        (string) (
                            $item['PublicDeviceId'] ?? ''
                        )
                    );

                    /*
                     * The database model requires VesselName.
                     * Use PublicDeviceId as a fallback when
                     * VesselName is missing.
                     */
                    if ($vesselName === '') {
                        $missingVesselNameCount++;

                        if ($publicDeviceId !== '') {
                            $vesselName =
                                'DEVICE-' . $publicDeviceId;
                        } else {
                            /*
                             * The report cannot be associated with
                             * a boat when both identifiers are absent.
                             */
                            $skippedMissingIdentifierCount++;
                            continue;
                        }
                    }

                    $model = new BluetrakerReports();

                    $model->MessageId = $messageId;
                    $model->VesselName = $vesselName;
                    $model->PublicDeviceId =
                        $publicDeviceId !== ''
                            ? $publicDeviceId
                            : null;
                    $model->CreatedGpsTime =
                        $item['CreatedGpsTime'] ?? null;
                    $model->ReceiveTime =
                        $item['ReceiveTime'] ?? null;
                    $model->Longitude =
                        $item['Longitude'] ?? null;
                    $model->Latitude =
                        $item['Latitude'] ?? null;
                    $model->Heading =
                        $item['Heading'] ?? null;
                    $model->Speed =
                        $item['Speed'] ?? null;
                    $model->Event =
                        $item['Event'] ?? null;

                    if (!$model->save()) {
                        throw new RuntimeException(
                            "Save failed for MessageId "
                            . "{$messageId}: "
                            . json_encode(
                                $model->errors,
                                JSON_UNESCAPED_UNICODE
                                | JSON_UNESCAPED_SLASHES
                            )
                        );
                    }

                    $savedCount++;
                }

                /*
                 * Show one warning summary per batch instead of
                 * printing one warning for every API report.
                 */
                if ($missingVesselNameCount > 0) {
                    $usedFallbackCount =
                        $missingVesselNameCount
                        - $skippedMissingIdentifierCount;

                    $this->writeLog(
                        "WARNING: {$missingVesselNameCount} "
                        . "reports had no VesselName. "
                        . "{$usedFallbackCount} used "
                        . "PublicDeviceId as the fallback."
                    );
                }

                if ($skippedMissingIdentifierCount > 0) {
                    $this->writeLog(
                        "WARNING: "
                        . "{$skippedMissingIdentifierCount} "
                        . "reports were skipped because both "
                        . "VesselName and PublicDeviceId "
                        . "were missing."
                    );
                }

                /*
                 * Prevent an infinite loop if the API returns
                 * 1,000 records without a newer MessageId.
                 */
                if (
                    count($reports) === 1000
                    && $maxMessageId <= $batchStartMessageId
                ) {
                    throw new RuntimeException(
                        'MessageId did not advance. '
                        . 'Sync stopped to prevent an '
                        . 'infinite loop.'
                    );
                }

                /*
                 * Update the sync position only after the entire
                 * database batch is processed successfully.
                 */
                $syncStatus->LastMessageId = $maxMessageId;
                $syncStatus->LastSyncTime = date(
                    'Y-m-d H:i:s'
                );

                if (!$syncStatus->save()) {
                    throw new RuntimeException(
                        'Failed to update sync status: '
                        . json_encode(
                            $syncStatus->errors,
                            JSON_UNESCAPED_UNICODE
                            | JSON_UNESCAPED_SLASHES
                        )
                    );
                }

                $transaction->commit();

                $lastMessageId = $maxMessageId;

                $this->writeLog(
                    'Batch completed. '
                    . 'Received: ' . count($reports)
                    . ", Saved: {$savedCount}"
                    . ", Already existing: {$existingCount}"
                    . ', Skipped: '
                    . $skippedMissingIdentifierCount
                    . ", Last MessageId: {$lastMessageId}"
                );
            } catch (Throwable $exception) {
                if ($transaction->isActive) {
                    $transaction->rollBack();
                }

                $this->writeLog(
                    'ERROR: Batch was rolled back. '
                    . $exception->getMessage()
                );

                /*
                 * LastMessageId is not advanced when a batch fails.
                 * The same batch will be attempted again.
                 */
                return ExitCode::DATAERR;
            }
        } while (count($reports) === 1000);

        /*
         * Cleanup runs only after all API batches have
         * completed successfully.
         */
        $cleanupTransaction =
            Yii::$app->db->beginTransaction();

        try {
            $deletedCount =
                $this->removeOldBlueTrackerReports();

            $cleanupTransaction->commit();

            $this->writeLog(
                'Cleanup completed. '
                . "Deleted old records: {$deletedCount}"
            );
        } catch (Throwable $exception) {
            if ($cleanupTransaction->isActive) {
                $cleanupTransaction->rollBack();
            }

            $this->writeLog(
                'ERROR: Cleanup failed. '
                . $exception->getMessage()
            );

            return ExitCode::DATAERR;
        }

        $this->writeLog('BlueTraker sync completed.');

        return ExitCode::OK;
    }

    /**
     * Fetch reports from the BlueTraker API.
     */
    private function fetchReports(
        string $apiUrl,
        string $username,
        string $password,
        int $lastMessageId
    ): array {
        $url = rtrim($apiUrl, '?&')
            . '?'
            . http_build_query([
                'messageId' => $lastMessageId,
            ]);

        $ch = curl_init();

        if ($ch === false) {
            throw new RuntimeException(
                'Unable to initialize CURL.'
            );
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD =>
                $username . ':' . $password,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
            ],
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $curlError = curl_error($ch);

            curl_close($ch);

            throw new RuntimeException(
                'CURL request failed: ' . $curlError
            );
        }

        $httpCode = (int) curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new RuntimeException(
                "BlueTraker API returned HTTP "
                . "{$httpCode}."
            );
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                'Invalid JSON response: '
                . json_last_error_msg()
            );
        }

        if (!is_array($data)) {
            throw new RuntimeException(
                'The API response is not a valid object.'
            );
        }

        if (!isset($data['Reports'])) {
            return [];
        }

        if (!is_array($data['Reports'])) {
            throw new RuntimeException(
                'The Reports value is not an array.'
            );
        }

        return $data['Reports'];
    }

    /**
     * Keep only the latest two records for every boat and event.
     *
     * Primary boat identifier:
     * PublicDeviceId
     *
     * Fallback boat identifier:
     * VesselName, only when PublicDeviceId is unavailable.
     */
   private function removeOldBlueTrackerReports(): int
{
    $deletedCount = 0;

    /*
     * Group only by the real VesselName and Event.
     *
     * PublicDeviceId is intentionally not used because:
     * 1. One vessel can have multiple device IDs.
     * 2. One device ID can be assigned to different vessels.
     *
     * DEVICE-* fallback records are excluded because they cannot
     * be safely linked to the real vessel.
     */
    $vesselGroups = BluetrakerReports::find()
        ->select([
            'VesselName',
            'Event',
        ])
        ->where([
            'is not',
            'VesselName',
            null,
        ])
        ->andWhere([
            '<>',
            'VesselName',
            '',
        ])
        ->andWhere([
            'not like',
            'VesselName',
            'DEVICE-%',
            false,
        ])
        ->groupBy([
            'VesselName',
            'Event',
        ])
        ->asArray()
        ->all();

    foreach ($vesselGroups as $group) {
        /*
         * Keep:
         * 1. Highest MessageId
         * 2. Second-highest MessageId
         *
         * Delete everything from offset 2 onwards.
         */
        $oldMessageIds = BluetrakerReports::find()
            ->select('MessageId')
            ->where([
                'VesselName' => $group['VesselName'],
                'Event' => $group['Event'],
            ])
            ->andWhere([
                'not like',
                'VesselName',
                'DEVICE-%',
                false,
            ])
            ->orderBy([
                'MessageId' => SORT_DESC,
            ])
            ->offset(2)
            ->column();

        if (empty($oldMessageIds)) {
            continue;
        }

        foreach (
            array_chunk($oldMessageIds, 500)
            as $messageIdBatch
        ) {
            $deletedCount += BluetrakerReports::deleteAll([
                'MessageId' => $messageIdBatch,
            ]);
        }
    }

    return $deletedCount;
}

    /**
     * Print a timestamped message to the cron log.
     */
    private function writeLog(string $message): void
    {
        echo '['
            . date('Y-m-d H:i:s')
            . '] '
            . $message
            . PHP_EOL;
    }
}