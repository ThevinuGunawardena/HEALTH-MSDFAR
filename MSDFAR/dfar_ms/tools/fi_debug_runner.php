<?php
// tools/fi_debug_runner.php
// Bootstraps the backend web app, selects an FI profile and runs AnalyticsController::actionFiDebug()

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// merge configs like backend/web/index.php
$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../common/config/main.php',
    file_exists(__DIR__ . '/../common/config/main-local.php') ? require __DIR__ . '/../common/config/main-local.php' : [],
    require __DIR__ . '/../backend/config/main.php',
    file_exists(__DIR__ . '/../backend/config/main-local.php') ? require __DIR__ . '/../backend/config/main-local.php' : []
);

// Remove behaviors that expect HTTP request context (safe for CLI runner)
if (isset($config['as beforeRequest'])) {
    unset($config['as beforeRequest']);
}

// Force DB to use TCP loopback (avoid socket "No such file or directory" on macOS XAMPP)
if (isset($config['components']['db'])) {
    $config['components']['db']['dsn'] = 'mysql:host=127.0.0.1;dbname=dfar_ms;port=3306';
    // keep username/password from config if present, otherwise default to root/
    if (empty($config['components']['db']['username'])) {
        $config['components']['db']['username'] = 'root';
    }
    if (!isset($config['components']['db']['password'])) {
        $config['components']['db']['password'] = '';
    }
}

// Remove web-only components that conflict with console Application
if (isset($config['components']['errorHandler'])) {
    unset($config['components']['errorHandler']);
}
if (isset($config['components']['request'])) {
    unset($config['components']['request']);
}
if (isset($config['components']['session'])) {
    unset($config['components']['session']);
}
if (isset($config['components']['urlManager'])) {
    unset($config['components']['urlManager']);
}

// create a console application to provide Yii::$app and db access in CLI
$application = new yii\console\Application($config);

// Require constant and model files used by the FI debug logic
$baseModelPath = __DIR__ . '/../backend/models/';
$models = [
    'ProfileOfficer.php',
    'Skipper.php',
    'SkipperRenew.php',
    'BoatNumbers.php',
    'FishermanRegisterdBoatLicense.php',
    'BoatNumberCancelRequests.php',
    'BoatNumberTransferRequest.php',
    'NationalLicense.php',
    'HighseasLicense.php',
];
foreach ($models as $mfile) {
    $p = $baseModelPath . $mfile;
    if (file_exists($p)) {
        require_once $p;
    }
}

// Ensure Constant class is available
$constantPath = __DIR__ . '/../backend/config/Constant.php';
if (file_exists($constantPath)) {
    require_once $constantPath;
}

// find an FI profile (ProfileOfficer) with a division assigned
$profile = \backend\models\ProfileOfficer::find()->where(['not', ['division' => null]])->one();
if (!$profile) {
    fwrite(STDERR, "No ProfileOfficer with a division found in DB.\n");
    exit(1);
}

// Inline the FI debug logic (mirror actionFiDebug) using ActiveRecord
$fiDivision = $profile->division;

$modelsMap = [
    'skipper' => ['\backend\models\Skipper', '\backend\models\SkipperRenew'],
    'boat_numbers' => ['\backend\models\BoatNumbers'],
    'boat_registration' => ['\backend\models\FishermanRegisterdBoatLicense'],
    'national_license' => ['\backend\models\NationalLicense'],
    'highseas_license' => ['\backend\models\HighseasLicense'],
    'boat_cancel' => ['\backend\models\BoatNumberCancelRequests'],
    'boat_transfer' => ['\backend\models\BoatNumberTransferRequest'],
];

$debug = ['fiDivision' => $fiDivision, 'models' => []];

foreach ($modelsMap as $key => $modelList) {
    foreach ($modelList as $model) {
        try {
            // determine division field same as FI dashboard
            $divisionField = null;
            if ($model === '\\backend\\models\\Skipper' || $model === '\\backend\\models\\SkipperRenew') {
                $divisionField = 'fisheries_division';
            } elseif ($model === '\\backend\\models\\BoatNumbers') {
                $divisionField = 'fisheries_division';
            } elseif ($model === '\\backend\\models\\FishermanRegisterdBoatLicense') {
                $divisionField = 'division';
            } elseif ($model === '\\backend\\models\\NationalLicense' || $model === '\\backend\\models\\HighseasLicense') {
                $divisionField = 'division';
            } elseif ($model === '\\backend\\models\\BoatNumberCancelRequests' || $model === '\\backend\\models\\BoatNumberTransferRequest') {
                $divisionField = null;
            }

            $base = $model::find();
            $total = $base->count();

            // status-only pending (no approval_stage filter)
            $q = $model::find()->where(['status' => \backend\config\Constant::Pending]);
            if ($divisionField && $fiDivision !== null) {
                $q->andWhere([$divisionField => $fiDivision]);
            } elseif (!$divisionField && $fiDivision !== null && ($model === '\\backend\\models\\BoatNumberCancelRequests' || $model === '\\backend\\models\\BoatNumberTransferRequest')) {
                $boatTable = \backend\models\BoatNumbers::tableName();
                $q->joinWith('boatNumber')->andWhere(["{$boatTable}.fisheries_division" => $fiDivision]);
            }
            $pending_status_only = $q->count();

            // exact approval_stage = 'FI' or numeric constant
            $q2 = $model::find()->where(['status' => \backend\config\Constant::Pending])
                ->andWhere(['or', ['approval_stage' => \backend\config\Constant::FI], ['approval_stage' => (string) \backend\config\Constant::FI], ['approval_stage' => 'FI']]);
            if ($divisionField && $fiDivision !== null) {
                $q2->andWhere([$divisionField => $fiDivision]);
            } elseif (!$divisionField && $fiDivision !== null && ($model === '\\backend\\models\\BoatNumberCancelRequests' || $model === '\\backend\\models\\BoatNumberTransferRequest')) {
                $boatTable = \backend\models\BoatNumbers::tableName();
                $q2->joinWith('boatNumber')->andWhere(["{$boatTable}.fisheries_division" => $fiDivision]);
            }
            $pending_exact = $q2->count();

            // FIND_IN_SET('FI', approval_stage)
            $expr = new \yii\db\Expression('FIND_IN_SET("FI", approval_stage)');
            $q3 = $model::find()->where(['status' => \backend\config\Constant::Pending])->andWhere($expr);
            if ($divisionField && $fiDivision !== null) {
                $q3->andWhere([$divisionField => $fiDivision]);
            } elseif (!$divisionField && $fiDivision !== null && ($model === '\\backend\\models\\BoatNumberCancelRequests' || $model === '\\backend\\models\\BoatNumberTransferRequest')) {
                $boatTable = \backend\models\BoatNumbers::tableName();
                $q3->joinWith('boatNumber')->andWhere(["{$boatTable}.fisheries_division" => $fiDivision]);
            }
            $pending_find_in_set = $q3->count();

            // LIKE '%FI%'
            $q4 = $model::find()->where(['status' => \backend\config\Constant::Pending])->andWhere(['like', 'approval_stage', 'FI']);
            if ($divisionField && $fiDivision !== null) {
                $q4->andWhere([$divisionField => $fiDivision]);
            } elseif (!$divisionField && $fiDivision !== null && ($model === '\\backend\\models\\BoatNumberCancelRequests' || $model === '\\backend\\models\\BoatNumberTransferRequest')) {
                $boatTable = \backend\models\BoatNumbers::tableName();
                $q4->joinWith('boatNumber')->andWhere(["{$boatTable}.fisheries_division" => $fiDivision]);
            }
            $pending_like = $q4->count();

            $debug['models'][$model] = [
                'total' => (int) $total,
                'pending_status_only' => (int) $pending_status_only,
                'pending_exact_FI' => (int) $pending_exact,
                'pending_find_in_set_FI' => (int) $pending_find_in_set,
                'pending_like_FI' => (int) $pending_like,
            ];
        } catch (\Exception $e) {
            $debug['models'][$model] = ['error' => $e->getMessage()];
        }
    }
}

echo json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";

// end
