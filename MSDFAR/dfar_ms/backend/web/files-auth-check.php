<?php

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

$app = new yii\web\Application($config);

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('Content-Type: text/plain; charset=UTF-8');

/*
 * Nginx auth_request should only make GET or HEAD requests.
 */
if (!in_array($_SERVER['REQUEST_METHOD'] ?? '', ['GET', 'HEAD'], true)) {
    http_response_code(405);
    exit;
}

/*
 * Accessing Yii's user component restores the user from
 * the existing Yii session or identity cookie.
 */
if (Yii::$app->user->isGuest) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    http_response_code(403);
    exit;
}

/*
 * Release the PHP session lock immediately.
 */
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

http_response_code(204);
exit;