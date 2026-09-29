<?php

namespace backend\controllers;

use yii\filters\AccessControl;
use Yii;
use backend\components\Controller;

use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\config\UserTypeUtil;
use backend\services\CommonService;
use backend\config\Constant;
use backend\models\ELog;
use backend\models\ELogLongline;
use backend\models\ELogGillnet;
use backend\models\ELogRingnet;
use backend\models\ELogSets;
use backend\models\ELogSetCatch;
use backend\models\MELogFishType;
use backend\models\MELogFishVariant;
use backend\models\DepartureRequests;
use backend\models\MHarbours;
use backend\models\ELogSetDiscardedDead;
use backend\models\ELogSetDiscardedLive;

class ELogEditController extends Controller
{
    // =========================================================================
    // GLOBAL WEIGHT LIMIT
    // Change this one value to update the total weight cap everywhere.
    // =========================================================================
    private const MAX_TOTAL_WEIGHT = 50000; // kg

    // =========================================================================
    // Max weight per individual fish (kg), keyed by fish_variant_id.
    // Variants not listed here are treated as unlimited.
    // This check is skipped entirely for ringnet gear.
    // =========================================================================
    private const VARIANT_MAX_WEIGHT = [
        1 => 60,    // Albacore
        2 => 180,   // Bigeye tuna
        3 => 30,    // Other tuna
        4 => 35,    // Skipjack tuna
        5 => 200,   // Yellowfin tuna
        6 => 1.5,   // Bigeye scad
        7 => 5,     // Carangids
        8 => 20,    // Dolphin fish
        9 => 0.8,   // Indian mackerel
        10 => 1.2,   // Indian scad
        11 => 0.5,   // Needle cuttle fish
        12 => 2,     // Ocean trigger fish
        13 => 10,    // Other fish
        14 => 12,    // Rainbow runner
        15 => 1,     // Toothponies nei
        16 => 8,     // Trevally
        17 => 700,   // Black marlin
        18 => 900,   // Blue marlin
        19 => 300,   // Other bill fish
        20 => 100,   // Sailfish
        21 => 60,    // Shortbill spearfish
        22 => 200,   // Striped marlin
        23 => 650,   // Swordfish
        24 => 400,   // Blue shark
        25 => 150,   // Eagle rays nei
        26 => 350,   // Giant devil ray
        27 => 500,   // Mako shark
        28 => 2000,  // Manta rays
        29 => 800,   // Mantas/devil ray nei
        30 => 100,   // Other rays
        31 => 200,   // Other sharks
        32 => 120,   // Rays/sting rays/mantas nei
        33 => 350,   // Silky shark
        34 => 300,   // Hammerhead shark
        35 => 8,     // Bullet tuna
        36 => 10,    // Frigate tuna
        37 => 12,    // Kawakawa
        38 => 35,    // Longtail tuna
        39 => 70,    // Narrow-barred Spanish mackerel
        40 => 20,    // Other neritic tuna
        41 => 70,    // Spanish mackerel
        42 => 80,    // Wahoo
        43 => 250,   // Thresher shark
        44 => 50,    // Other animals
        45 => 650,   // Dolphin
        46 => 200,   // Oceanic white tip shark
        47 => 12000, // Whale shark
        48 => 200,   // Green sea turtle
        49 => 80,    // Hawksbill sea turtle
        50 => 400,   // Loggerhead sea turtle
        51 => 900,   // Leatherback sea turtle
        52 => 45,    // Olive ridley sea turtle
        53 => 70,    // Flatback sea turtle
        54 => 30,    // Other marine mammals
    ];

    // =========================================================================
    // Min weight per individual fish (kg), keyed by fish_variant_id.
    // Variants not listed here have no minimum enforced.
    // This check is skipped entirely for ringnet gear.
    // =========================================================================
    private const VARIANT_MIN_WEIGHT = [
        1 => 5,     // Albacore
        2 => 5,     // Bigeye tuna
        4 => 1,     // Skipjack tuna
        5 => 5,     // Yellowfin tuna
        6 => 0.05,  // Bigeye scad
        8 => 1,     // Dolphin fish
        9 => 0.05,  // Indian mackerel
        10 => 0.05,  // Indian scad
        11 => 0.05,  // Needle cuttle fish
        12 => 0.1,   // Ocean trigger fish
        14 => 0.1,   // Rainbow runner
        16 => 0.1,   // Trevally
        17 => 5,     // Black marlin
        18 => 5,     // Blue marlin
        20 => 5,     // Sailfish
        21 => 2,     // Shortbill spearfish
        22 => 3,     // Striped marlin
        23 => 3,     // Swordfish
        24 => 3,     // Blue shark
        26 => 5,     // Giant devil ray
        27 => 3,     // Mako shark
        28 => 5,     // Manta rays
        33 => 3,     // Silky shark
        35 => 0.3,   // Bullet tuna
        36 => 0.3,   // Frigate tuna
        37 => 0.5,   // Kawakawa
        38 => 1,     // Longtail tuna
        39 => 0.1,   // Narrow-barred Spanish mackerel
        41 => 0.1,   // Spanish mackerel
        42 => 1,     // Wahoo
    ];

    // =========================================================================
    // Allowed GPS E values per direction + N integer key
    // =========================================================================
    private const GPS_MAP = [
        'N' => [
            0 => [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            1 => [49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92],
            2 => [50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92],
            3 => [50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92],
            4 => [51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91],
            5 => [52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90],
            6 => [52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89],
            7 => [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 78, 79, 81, 82, 83, 84, 85, 86, 87, 88, 89],
            8 => [53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89],
            9 => [54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89],
            10 => [56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88],
            11 => [57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 81, 82, 83, 84, 85, 86, 87, 88],
            12 => [57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 83, 84, 85, 86, 87, 88, 89],
            13 => [57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 83, 84, 85, 86, 87, 88, 89],
            14 => [57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 84, 85, 86, 87, 88, 89],
            15 => [58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 85, 86, 87, 88, 89, 90],
            16 => [59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 86, 87, 88, 89, 90],
            17 => [60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 88, 89, 90],
            18 => [61, 62, 63, 64, 65, 66, 67, 68, 89, 90],
            19 => [62, 63, 64, 65, 66, 67],
            20 => [62, 63, 64, 65, 66],
            21 => [63],
        ],
        'S' => [
            0 => [46,47,48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            1 => [46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94],
            2 => [45, 46, 47, 48, 49, 50, 51, 52, 53, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95],
            3 => [44, 45, 46, 47, 48, 49, 50, 51, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95],
            4 => [44, 45, 46, 47, 48, 49, 50, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97],
            5 => [43, 44, 45, 46, 47, 48, 49, 50, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98],
            6 => [43, 44, 45, 46, 47, 48, 49, 59, 60, 61, 62, 63, 64, 65, 66, 67, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98],
            7 => [43, 44, 45, 46, 47, 48, 49, 59, 60, 61, 62, 63, 64, 65, 66, 67, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98],
            8 => [43, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99],
            9 => [59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100],
            10 => [60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94],
            11 => [59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            12 => [59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            13 => [60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            14 => [61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            15 => [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93],
            16 => [62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94],
            17 => [63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95],
            18 => [65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100],
            19 => [66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100],
            20 => [66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100],
        ],
    ];

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->isGuest) {
                                return false;
                            }
                            return UserTypeUtil::hasType(Constant::HARBOUR_OFFICER)
                                || UserTypeUtil::hasType(Constant::AD_Highseas)
                                || UserTypeUtil::hasType(Constant::ITD);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-longline' => ['POST'],
                    'delete-gillnet' => ['POST'],
                    'delete-ringnet' => ['POST'],
                    'delete-set' => ['POST'],
                ],
            ],
        ]);
    }

    /**
 * Validates that a GPS value's decimal part represents valid minutes (00–59).
 */
private function hasInvalidMinutes($value): bool
{
    if ($value === null || $value === '') {
        return false;
    }
    $str = (string) $value;
    $dotPos = strpos($str, '.');
    if ($dotPos === false) {
        return false;
    }
    $decimals = substr($str, $dotPos + 1);
    $minutes = (int) substr(str_pad($decimals, 2, '0'), 0, 2);
    return $minutes >= 60;
}

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Validates that a GPS E value is allowed for the given direction + N.
     */
    private function isValidGpsE(string $direction, $nRaw, $eRaw): bool
    {
        if (!isset(self::GPS_MAP[$direction])) {
            return false;
        }
        $nKey = (int) floor((float) $nRaw);
        $eKey = (int) floor((float) $eRaw);
        $eList = self::GPS_MAP[$direction][$nKey] ?? [];
        return in_array($eKey, $eList, true);
    }

    /**
     * Validates per-fish weight (min and max) for a single catch/discard row.
     * Returns an error string or null if valid / no limit defined.
     * Always returns null for ringnet gear.
     */
    private function validatePerFishWeight(string $gearType, int $variantId, float $weight, int $count): ?string
    {
        if ($gearType === 'ringnet') {
            return null;
        }
        if ($count <= 0) {
            return null;
        }

        $perFish = $weight / $count;

        // $max = self::VARIANT_MAX_WEIGHT[$variantId] ?? null;
        // if ($max !== null && $perFish > $max) {
        //     return sprintf(
        //         'Each fish would be %.2f kg but the maximum allowed for variant ID %d is %s kg.',
        //         $perFish,
        //         $variantId,
        //         $max
        //     );
        // }
        if($weight > 35000){
           return sprintf(
                'Each fish can only have 35000 toatal weight.',
                $weight
            ); 
        }
        $min = self::VARIANT_MIN_WEIGHT[$variantId] ?? null;
        if ($min !== null && $perFish < $min ) {
            return sprintf(
                'Each fish would be %.2f kg but the minimum allowed for variant ID %d is %s kg.',
                $perFish,
                $variantId,
                $min
            );
        }

        return null;
    }

    /**
     * Validates all fish rows (catches, discarded_dead, discarded_live).
     * Checks per-fish min/max weight AND the global MAX_TOTAL_WEIGHT cap.
     * Returns an array of human-readable error strings.
     */
    private function validateFishRows(string $gearType, array $catches, array $deadRows, array $liveRows): array
    {
        $errors = [];
        $totalWeight = 0.0;

        $sections = [
            'Retained catch' => $catches,
            'Discarded dead' => $deadRows,
            'Discarded live' => $liveRows,
        ];

        foreach ($sections as $label => $rows) {
            foreach ($rows as $i => $row) {
                if (empty($row['fish_type_id']) || empty($row['fish_variant_id'])) {
                    continue;
                }

                $variantId = (int) $row['fish_variant_id'];
                $weight = (float) ($row['weight'] ?? 0);
                $count = (int) ($row['fish_count'] ?? 0);
                $totalWeight += $weight;

                $err = $this->validatePerFishWeight($gearType, $variantId, $weight, $count);
                if ($err !== null) {
                    $errors[] = "{$label} row " . ($i + 1) . ": {$err}";
                }
            }
        }

        if ($totalWeight > self::MAX_TOTAL_WEIGHT) {
            $errors[] = sprintf(
                'Total weight across all fish entries is %.2f kg, which exceeds the maximum allowed of %s kg.',
                $totalWeight,
                self::MAX_TOTAL_WEIGHT
            );
        }

        return $errors;
    }

    // =========================================================================
    // FORM
    // =========================================================================
    public function actionIndex($dep_id = null, $log_sheet_number = null, $log_page_number = null)
    {
        $model = new ELog();
        $departure = null;
        $longline = false;
        $gillnet = false;
        $ringnet = false;

        if ($dep_id) {
            $departure = DepartureRequests::findOne($dep_id);
            if ($departure) {
                $harbour = MHarbours::find()
                    ->where(['like', 'Name', trim($departure->harbor)])
                    ->one();

                $model->vessel_id = $departure->boat_no;
                $model->skipper_id = $departure->skipper_no;
                $model->phone_number = $departure->contact_no;
                $model->departure_date = substr($departure->action_date, 0, 10);
                $model->departure_harbour = $harbour ? $harbour->Id : null;
                $model->log_book_no = $log_page_number;
                $model->log_sheet_number = $log_sheet_number;

                $longline = !empty($departure->length_longline);
                $gillnet = !empty($departure->length_gillnet);
                $ringnet = !empty($departure->length_ringnet);
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect([
                    'geardata',
                    'id' => $model->id,
                    'dep_id' => $dep_id,
                    'longline' => $longline ? 1 : 0,
                    'gillnet' => $gillnet ? 1 : 0,
                    'ringnet' => $ringnet ? 1 : 0,
                ]);
            } else {
                echo "<pre>";
                print_r($model->errors);
                echo "</pre>";
                exit;
            }
        }

        return $this->render('index', [
            'model' => $model,
            'dep_id' => $dep_id,
            'longline' => $longline,
            'gillnet' => $gillnet,
            'ringnet' => $ringnet,
        ]);
    }

    public function actionMain()
    {
        $model = new DepartureRequests();
        $results = [];
        $boat_no = null;
        $year = null;
        $month = null;
        $duplicateError = null;

        if (Yii::$app->request->isPost) {
            $boat_no = Yii::$app->request->post('vessel_id');
            $year = Yii::$app->request->post('year');
            $month = Yii::$app->request->post('month');
            $logSheetNumber = Yii::$app->request->post('log_sheet_number');
            $logPageNumber = Yii::$app->request->post('log_page_number');

            $exists = ELog::find()
                ->where(['log_book_no' => $logSheetNumber, 'log_sheet_number' => $logPageNumber])
                ->exists();

            if ($exists) {
                $duplicateError = "This Log Sheet Number ({$logSheetNumber}) and Log Page Number ({$logPageNumber}) combination already exists.";
            } else {
                $results = DepartureRequests::find()
                    ->where(['boat_no' => $boat_no])
                    ->andWhere(['YEAR(action_date)' => $year])
                    ->andWhere(['MONTH(action_date)' => $month])
                    ->orderBy(['action_date' => SORT_ASC])
                    ->all();
            }
        }

        return $this->render('main', [
            'model' => $model,
            'results' => $results,
            'boat_no' => $boat_no,
            'year' => $year,
            'month' => $month,
            'duplicateError' => $duplicateError,
        ]);
    }

    // =========================================================================
    // BOAT / SKIPPER SEARCH (AJAX)
    // =========================================================================
   public function actionBoatSearch($q = null)
{
    Yii::$app->response->format = Response::FORMAT_JSON;

    $query = DepartureRequests::find()
        ->select([
            'boat_no AS id',
            'boat_no AS text',
        ])
        ->distinct()
        ->limit(20)
        ->groupBy(['boat_no']);

    if (!empty($q)) {
        $query->andWhere(['like', 'boat_no', $q]);
    }

    $results = $query->asArray()->all();

    return ['results' => $results];
}
    public function actionSkipperSearch($q = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = DepartureRequests::find()
            ->select([
                'skipper_no AS id',
                "CONCAT(skipper_no, ' - ', skipper) AS text",
            ])
            ->limit(20);

        if ($q) {
            $query->andWhere(['like', 'skipper_no', $q]);
        }

        return ['results' => $query->asArray()->all()];
    }

    // =========================================================================
    // FISH VARIANTS (AJAX)
    // =========================================================================
    public function actionFishVariants($typeId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return MELogFishVariant::find()
            ->where(['fish_type_id' => $typeId])
            ->orderBy('name')
            ->asArray()
            ->all();
    }

    // =========================================================================
    // VIEW PAGE
    // =========================================================================
    public function actionView($id)
    {
        $model = ELog::findOne($id);

        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Record not found.');
        }

        return $this->render('view', ['model' => $model]);
    }

    // =========================================================================
    // OTHER DATA PAGE
    // =========================================================================
    public function actionOther($id)
    {
        $model = ELog::findOne($id);

        return $this->render('other', ['model' => $model]);
    }

    // =========================================================================
    // GEAR DATA PAGE
    // =========================================================================
    public function actionGeardata($id, $dep_id = null, $longline = 0, $gillnet = 0, $ringnet = 0)
    {
        $eLog = ELog::findOne($id);
        if ($eLog === null) {
            throw new \yii\web\NotFoundHttpException('E-Log not found.');
        }

        $longlines = ELogLongline::find()->where(['e_log_id' => $id])->all();
        $gillnets = ELogGillnet::find()->where(['e_log_id' => $id])->all();
        $ringnets = ELogRingnet::find()->where(['e_log_id' => $id])->all();

        return $this->render('geardata', [
            'model' => $eLog,
            'longlines' => $longlines,
            'gillnets' => $gillnets,
            'ringnets' => $ringnets,
            'dep_id' => $dep_id,
            'longline' => (bool) $longline,
            'gillnet' => (bool) $gillnet,
            'ringnet' => (bool) $ringnet,
        ]);
    }

    // =========================================================================
    // ADD LONGLINE / GILLNET / RINGNET
    // =========================================================================
    public function actionLongline($id, $dep_id = null, $longline = 0, $gillnet = 0, $ringnet = 0)
    {
        $eLog = ELog::findOne($id);
        if ($eLog === null) {
            throw new \yii\web\NotFoundHttpException('E-Log not found.');
        }

        $model = new ELogLongline();
        $model->e_log_id = $id;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect([
                    'geardata',
                    'id' => $id,
                    'dep_id' => $dep_id,
                    'longline' => $longline,
                    'gillnet' => $gillnet,
                    'ringnet' => $ringnet,
                ]);
            }
        }

        return $this->render('longline', [
            'model' => $model,
            'eLogId' => $id,
            'dep_id' => $dep_id,
            'longline' => $longline,
            'gillnet' => $gillnet,
            'ringnet' => $ringnet,
        ]);
    }

    public function actionGillnet($id, $dep_id = null, $longline = 0, $gillnet = 0, $ringnet = 0)
    {
        $eLog = ELog::findOne($id);
        if ($eLog === null) {
            throw new \yii\web\NotFoundHttpException('E-Log not found.');
        }

        $model = new ELogGillnet();
        $model->e_log_id = $id;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect([
                    'geardata',
                    'id' => $id,
                    'dep_id' => $dep_id,
                    'longline' => $longline,
                    'gillnet' => $gillnet,
                    'ringnet' => $ringnet,
                ]);
            }
        }

        return $this->render('gillnet', [
            'model' => $model,
            'eLogId' => $id,
            'dep_id' => $dep_id,
            'longline' => $longline,
            'gillnet' => $gillnet,
            'ringnet' => $ringnet,
        ]);
    }

    public function actionRingnet($id, $dep_id = null, $longline = 0, $gillnet = 0, $ringnet = 0)
    {
        $eLog = ELog::findOne($id);
        if ($eLog === null) {
            throw new \yii\web\NotFoundHttpException('E-Log not found.');
        }

        $model = new ELogRingnet();
        $model->e_log_id = $id;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect([
                    'geardata',
                    'id' => $id,
                    'dep_id' => $dep_id,
                    'longline' => $longline,
                    'gillnet' => $gillnet,
                    'ringnet' => $ringnet,
                ]);
            }
        }

        return $this->render('ringnet', [
            'model' => $model,
            'eLogId' => $id,
            'dep_id' => $dep_id,
            'longline' => $longline,
            'gillnet' => $gillnet,
            'ringnet' => $ringnet,
        ]);
    }

    // =========================================================================
    // DELETE GEAR RECORDS
    // =========================================================================
    public function actionDeleteLongline($id, $elogId, $type = false, $dep_id = null, $longline = 0, $gillnet = 0, $ringnet = 0)
    {
        ELogLongline::findOne($id)->delete();
        CommonService::addApprovalLog("E-log", "delete", "longline", $id);
        if ($type) {
            return $this->redirect(['e-log-view/view', 'id' => $elogId]);
        }
        return $this->redirect(['geardata', 'id' => $elogId, 'dep_id' => $dep_id, 'longline' => $longline, 'gillnet' => $gillnet, 'ringnet' => $ringnet]);
    }

    public function actionDeleteGillnet($id, $elogId, $type = false, $dep_id = null, $longline = 0, $gillnet = 0, $ringnet = 0)
    {
        ELogGillnet::findOne($id)->delete();
        CommonService::addApprovalLog("E-log", "delete", "gillnet", $id);
        if ($type) {
            return $this->redirect(['e-log-view/view', 'id' => $elogId]);
        }
        return $this->redirect(['geardata', 'id' => $elogId, 'dep_id' => $dep_id, 'longline' => $longline, 'gillnet' => $gillnet, 'ringnet' => $ringnet]);
    }

    public function actionDeleteRingnet($id, $elogId, $type = false, $dep_id = null, $longline = 0, $gillnet = 0, $ringnet = 0)
    {
        ELogRingnet::findOne($id)->delete();
        CommonService::addApprovalLog("E-log", "delete", "ringnet", $id);
        if ($type) {
            return $this->redirect(['e-log-view/view', 'id' => $elogId]);
        }
        return $this->redirect(['geardata', 'id' => $elogId, 'dep_id' => $dep_id, 'longline' => $longline, 'gillnet' => $gillnet, 'ringnet' => $ringnet]);
    }

    // =========================================================================
    // EDIT GEAR RECORDS
    // =========================================================================
    public function actionEditLongline($id, $elogId, $dep_id = null, $longline = 0, $gillnet = 0, $ringnet = 0)
    {
        $model = ELogLongline::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Record not found.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['geardata', 'id' => $elogId, 'dep_id' => $dep_id, 'longline' => $longline, 'gillnet' => $gillnet, 'ringnet' => $ringnet]);
        }

        return $this->render('edit-longline', ['model' => $model, 'elogId' => $elogId, 'dep_id' => $dep_id, 'longline' => $longline, 'gillnet' => $gillnet, 'ringnet' => $ringnet]);
    }

    public function actionEditGillnet($id, $elogId, $dep_id = null, $longline = 0, $gillnet = 0, $ringnet = 0)
    {
        $model = ELogGillnet::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Record not found.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['geardata', 'id' => $elogId, 'dep_id' => $dep_id, 'longline' => $longline, 'gillnet' => $gillnet, 'ringnet' => $ringnet]);
        }

        return $this->render('edit-gillnet', ['model' => $model, 'elogId' => $elogId, 'dep_id' => $dep_id, 'longline' => $longline, 'gillnet' => $gillnet, 'ringnet' => $ringnet]);
    }

    public function actionEditRingnet($id, $elogId, $dep_id = null, $longline = 0, $gillnet = 0, $ringnet = 0)
    {
        $model = ELogRingnet::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Record not found.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['geardata', 'id' => $elogId, 'dep_id' => $dep_id, 'longline' => $longline, 'gillnet' => $gillnet, 'ringnet' => $ringnet]);
        }

        return $this->render('edit-ringnet', ['model' => $model, 'elogId' => $elogId, 'dep_id' => $dep_id, 'longline' => $longline, 'gillnet' => $gillnet, 'ringnet' => $ringnet]);
    }

    // =========================================================================
    // SET DATA — PICK GEAR
    // =========================================================================
    public function actionSetdata($id)
    {
        $eLog = ELog::findOne($id);
        if ($eLog === null) {
            throw new \yii\web\NotFoundHttpException('E-Log not found.');
        }

        $longlines = ELogLongline::find()->where(['e_log_id' => $id])->all();
        $gillnets = ELogGillnet::find()->where(['e_log_id' => $id])->all();
        $ringnets = ELogRingnet::find()->where(['e_log_id' => $id])->all();

        return $this->render('setdata', [
            'elog_id' => $id,
            'model' => $eLog,
            'longlines' => $longlines,
            'gillnets' => $gillnets,
            'ringnets' => $ringnets,
        ]);
    }

    // action set helper ofr grid value
    private function calcGridCode($direction, $n, $e, int $sizeCode, int $cellSize): ?int
{
    if ($direction === null || $direction === '' ||
        $n === null || $n === '' ||
        $e === null || $e === '') {
        return null;
    }

    $lat = (int) floor((float) $n);
    $lon = (int) floor((float) $e);

    // snap down to the grid cell's lower boundary
    $lat = intdiv($lat, $cellSize) * $cellSize;
    $lon = intdiv($lon, $cellSize) * $cellSize;

    // Quadrant: 1 = N, 2 = S (longitude assumed always East)
    $quadrant = ($direction === 'S') ? 1 : 2;

    $code = sprintf('%d%d%02d%03d', $sizeCode, $quadrant, $lat, $lon);

    return (int) $code;
}
    // =========================================================================
    // VIEW / ADD SETS FOR GEAR
    // =========================================================================
    public function actionSetsview($id, $type, $gearId, $page = false)
{
    $this->validateGearType($type);

    $sets = ELogSets::find()
        ->where(['gear_id' => $gearId, 'gear_type' => $type])
        ->orderBy(['set_number' => SORT_ASC])
        ->all();

    $model = new ELogSets();
    $model->gear_id = $gearId;
    $model->gear_type = $type;
    $model->set_number = count($sets) + 1;

    $fishTypes = ArrayHelper::map(
        MELogFishType::find()->orderBy('name')->all(),
        'id',
        'name'
    );

    if ($model->load(Yii::$app->request->post())) {

        // -----------------------------------------------------------------
        // SERVER-SIDE VALIDATION — GPS minutes (.00–.59 only)
        // -----------------------------------------------------------------
        $validationErrors = [];

        $startDir = $model->start_gps_direction;
        $startN   = $model->start_gps_n;
        $startE   = $model->start_gps_e;
        $endDir   = $model->end_gps_direction;
        $endN     = $model->end_gps_n;
        $endE     = $model->end_gps_e;
        

        if ($this->hasInvalidMinutes($startN)) {
            $validationErrors[] = "Start GPS N ({$startN}): decimal part must be .00–.59 (minutes cannot be 59 or above).";
        }
        if ($this->hasInvalidMinutes($startE)) {
            $validationErrors[] = "Start GPS E ({$startE}): decimal part must be .00–.59 (minutes cannot be 59 or above).";
        }
        if ($this->hasInvalidMinutes($endN)) {
            $validationErrors[] = "End GPS N ({$endN}): decimal part must be .00–.59 (minutes cannot be 59 or above).";
        }
        if ($this->hasInvalidMinutes($endE)) {
            $validationErrors[] = "End GPS E ({$endE}): decimal part must be .00–.59 (minutes cannot be 59 or above).";
        }

        // -----------------------------------------------------------------
        // SERVER-SIDE VALIDATION — GPS E range
        // -----------------------------------------------------------------
        if ($startDir && $startN !== '' && $startN !== null && $startE !== '' && $startE !== null) {
            if (!$this->isValidGpsE($startDir, $startN, $startE)) {
                $validationErrors[] = "Start GPS E value ({$startE}) is not valid for direction {$startDir} and N {$startN}.";
            }
        } else {
            $validationErrors[] = 'Start GPS direction, N, and E are all required.';
        }

        if ($endDir && $endN !== '' && $endN !== null && $endE !== '' && $endE !== null) {
            if (!$this->isValidGpsE($endDir, $endN, $endE)) {
                $validationErrors[] = "End GPS E value ({$endE}) is not valid for direction {$endDir} and N {$endN}.";
            }
        }

        // -----------------------------------------------------------------
        // SERVER-SIDE VALIDATION — Per-fish weight + total weight cap
        // -----------------------------------------------------------------
        $catches  = Yii::$app->request->post('catches', []);
        $deadRows = Yii::$app->request->post('discarded_dead', []);
        $liveRows = Yii::$app->request->post('discarded_live', []);

        $weightErrors = $this->validateFishRows($type, $catches, $deadRows, $liveRows);
        $validationErrors = array_merge($validationErrors, $weightErrors);

        // -----------------------------------------------------------------
        // If any server-side errors: flash and re-render
        // -----------------------------------------------------------------
        if (!empty($validationErrors)) {
            foreach ($validationErrors as $err) {
                Yii::$app->session->addFlash('error', $err);
            }

            return $this->render('setsview', [
                'elogId'         => $id,
                'type'           => $type,
                'gearId'         => $gearId,
                'sets'           => $sets,
                'model'          => $model,
                'fishTypes'      => $fishTypes,
                'page'           => $page,
                'maxTotalWeight' => self::MAX_TOTAL_WEIGHT,
            ]);
        }

        // -----------------------------------------------------------------
        // All validations passed — save the set and fish rows
        // -----------------------------------------------------------------
        $model->five_by_five = $this->calcGridCode($startDir, $startN, $startE, 6, 5);
        $model->one_by_one   = $this->calcGridCode($startDir, $startN, $startE, 5, 1);

        if ($model->save()) {

            foreach ($catches as $catchData) {
                if (empty($catchData['fish_type_id']) || empty($catchData['fish_variant_id']))
                    continue;
                $catch = new ELogSetCatch();
                $catch->e_log_set_id    = $model->id;
                $catch->fish_type_id    = $catchData['fish_type_id'];
                $catch->fish_variant_id = $catchData['fish_variant_id'];
                $catch->weight          = $catchData['weight'];
                $catch->fish_count = $catchData['fish_count'] ?? 0;
                $catch->save();
            }

            foreach ($deadRows as $row) {
                if (empty($row['fish_type_id']) || empty($row['fish_variant_id']))
                    continue;
                $dead = new ELogSetDiscardedDead();
                $dead->e_log_set_id    = $model->id;
                $dead->fish_type_id    = $row['fish_type_id'];
                $dead->fish_variant_id = $row['fish_variant_id'];
                $dead->weight          = $row['weight'];
                $dead->fish_count = $row['fish_count'] ?? 0;
                $dead->save();
            }

            foreach ($liveRows as $row) {
                if (empty($row['fish_type_id']) || empty($row['fish_variant_id']))
                    continue;
                $live = new ELogSetDiscardedLive();
                $live->e_log_set_id    = $model->id;
                $live->fish_type_id    = $row['fish_type_id'];
                $live->fish_variant_id = $row['fish_variant_id'];
                $live->weight          = $row['weight'];
                $live->fish_count = $row['fish_count'] ?? 0;
                $live->save();
            }

            Yii::$app->session->setFlash('success', "Set #{$model->set_number} saved.");
            return $this->redirect([
                'setsview',
                'id'     => $id,
                'type'   => $type,
                'gearId' => $gearId,
                'page'   => $page,
            ]);
        }
    }

    return $this->render('setsview', [
        'elogId'         => $id,
        'type'           => $type,
        'gearId'         => $gearId,
        'sets'           => $sets,
        'model'          => $model,
        'fishTypes'      => $fishTypes,
        'page'           => $page,
        'maxTotalWeight' => self::MAX_TOTAL_WEIGHT,
    ]);
}

    // =========================================================================
    // DELETE SET
    // =========================================================================
    public function actionDeleteSet($id, $type, $gearId, $elogId)
    {
        ELogSets::findOne($id)->delete();
        return $this->redirect([
            'setsview',
            'id' => $elogId,
            'type' => $type,
            'gearId' => $gearId,
        ]);
    }

    // =========================================================================
    // GEAR TYPE VALIDATOR
    // =========================================================================
    private function validateGearType($type)
    {
        if (!in_array($type, ['longline', 'gillnet', 'ringnet'])) {
            throw new \yii\web\NotFoundHttpException('Invalid gear type.');
        }
    }
}