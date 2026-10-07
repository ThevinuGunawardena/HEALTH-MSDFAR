<?php

ini_set('max_execution_time', '300');
ini_set('memory_limit', '512M');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);


defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev'); 

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

$loader = require __DIR__ . '/../../vendor/autoload.php';
$loader->addPsr4('common\\', dirname(dirname(__DIR__)) . '/common');
$loader->addPsr4('backend\\', dirname(dirname(__DIR__)) . '/backend');
$loader->addPsr4('frontend\\', dirname(dirname(__DIR__)) . '/frontend');
$loader->addPsr4('console\\', dirname(dirname(__DIR__)) . '/console');

require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

(new yii\web\Application($config))->run();