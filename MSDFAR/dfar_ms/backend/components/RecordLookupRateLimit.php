<?php

namespace backend\components;

use Yii;
use yii\base\ActionFilter;
use yii\base\InvalidConfigException;
use yii\caching\CacheInterface;
use yii\web\TooManyRequestsHttpException;

final class RecordLookupRateLimit extends ActionFilter
{
    /**
     * Maximum requests allowed within the window.
     */
    public int $limit = 30;

    /**
     * Window duration in seconds.
     */
    public int $window = 60;

    /**
     * Separate name when multiple limits are attached.
     */
    public string $bucketName = 'default';

    /**
     * Yii cache component name.
     */
    public string $cache = 'cache';

    public function beforeAction($action): bool
    {
        if ($this->limit < 1 || $this->window < 1) {
            throw new InvalidConfigException(
                'Rate-limit values must be greater than zero.'
            );
        }

        $cache = Yii::$app->get($this->cache);

        if (!$cache instanceof CacheInterface) {
            throw new InvalidConfigException(
                'The configured cache component must implement '
                . CacheInterface::class . '.'
            );
        }

        $request = Yii::$app->request;
        $response = Yii::$app->response;

        /*
         * Use user ID and IP address.
         *
         * Do not use the URL token because an attacker could change
         * the token on every request and bypass the limit.
         */
        $userId = Yii::$app->user->isGuest
            ? 'guest'
            : (string) Yii::$app->user->id;

        $ipAddress = (string) ($request->userIP ?? 'unknown');
        $route = $action->getUniqueId();
        $currentTime = time();

        /*
         * Fixed-window bucket.
         */
        $windowNumber = intdiv(
            $currentTime,
            $this->window
        );

        $cacheKey = [
            'record-lookup-rate-limit',
            $this->bucketName,
            $route,
            $userId,
            hash('sha256', $ipAddress),
            $windowNumber,
        ];

        $currentCount = $cache->get($cacheKey);

        $currentCount = $currentCount === false
            ? 1
            : (int) $currentCount + 1;

        $windowEndsAt =
            ($windowNumber + 1) * $this->window;

        $secondsUntilReset = max(
            1,
            $windowEndsAt - $currentTime
        );

        /*
         * Keep the counter slightly longer than the window.
         */
        $cache->set(
            $cacheKey,
            $currentCount,
            $secondsUntilReset + 5
        );

        $remaining = max(
            0,
            $this->limit - $currentCount
        );

        /*
         * Helpful headers for testing and monitoring.
         */
        $response->headers->set(
            'X-RateLimit-Limit',
            (string) $this->limit
        );

        $response->headers->set(
            'X-RateLimit-Remaining',
            (string) $remaining
        );

        $response->headers->set(
            'X-RateLimit-Reset',
            (string) $secondsUntilReset
        );

        if ($currentCount > $this->limit) {
            $response->headers->set(
                'Retry-After',
                (string) $secondsUntilReset
            );

            Yii::warning([
                'message' =>
                    'Record lookup rate limit exceeded.',

                'route' =>
                    $route,

                'userId' =>
                    $userId,

                'ipHash' =>
                    hash('sha256', $ipAddress),

                'requestCount' =>
                    $currentCount,

                'limit' =>
                    $this->limit,

                'windowSeconds' =>
                    $this->window,

                'retryAfter' =>
                    $secondsUntilReset,
            ], 'record-lookup-rate-limit');

            throw new TooManyRequestsHttpException(
                'Too many record requests. Please try again later.'
            );
        }

        return parent::beforeAction($action);
    }
}