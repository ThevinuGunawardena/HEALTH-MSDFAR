<?php

namespace backend\controllers\api;

use Yii;

use backend\components\Controller;
use backend\models\BoatNumbers;
use backend\models\DepartureBoats;
use backend\models\DepatureBoatPayment;
use backend\models\ReportWebsite;
use backend\models\MHarbours;
use backend\models\MELogFishType;
use backend\models\MELogFishVariant;
use backend\models\DeparureRequestCrew;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\web\Response;

class V1Controller extends Controller
{
    // Disable CSRF for API
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'corsFilter' => [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],                          // or specific: ['https://yoursite.com']
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],  // or specific: ['Content-Type', 'Authorization']
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Max-Age' => 86400,
            ],
        ],
            // Force JSON responses
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * GET /api/v1/vessels  (example endpoint)
     */

public function beforeAction($action)
{
    Yii::$app->response->format =
        \yii\web\Response::FORMAT_JSON;

    /*
     * Allow the CORS filter to process browser
     * OPTIONS preflight requests.
     */
    if (Yii::$app->request->isOptions) {
        return parent::beforeAction($action);
    }

    $auth = $this->validateApiAccess();

    if ($auth['success'] === false) {
        Yii::$app->response->data = $auth;

        return false;
    }

    return parent::beforeAction($action);
}

private function saveApiRequestLog($clientId = null, $apiKeyId = null, $endpointId = null, $statusCode = 200, $message = null)
{
    try {
        Yii::$app->db->createCommand()->insert('api_request_logs', [
            'client_id' => $clientId,
            'api_key_id' => $apiKeyId,
            'api_endpoint_id' => $endpointId,
            'route' => Yii::$app->request->pathInfo,
            'method' => Yii::$app->request->method,
            'ip_address' => Yii::$app->request->userIP,
            'status_code' => $statusCode,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
        ])->execute();

    } catch (\Throwable $e) {
        Yii::error('API request log save failed: ' . $e->getMessage(), 'api');

        return false;
    }

    return true;
}

 private function validateApiAccess()
{
    $headers = Yii::$app->request->headers;

    $plainApiKey = trim((string)$headers->get('X-API-KEY'));

    $clientCode = $headers->get('X-CLIENT-ID');

    if (empty($clientCode)) {
        $clientCode = $headers->get('client_id');
    }

    $clientCode = trim((string)$clientCode);

    if (empty($plainApiKey) || empty($clientCode)) {
        Yii::$app->response->statusCode = 401;

        $this->saveApiRequestLog(
            null,
            null,
            null,
            401,
            'Missing authentication headers'
        );

        return [
            'success' => false,
            'message' => 'Missing authentication headers. X-API-KEY and X-CLIENT-ID are required.',
        ];
    }

    $client = (new \yii\db\Query())
        ->from('api_clients')
        ->where([
            'client_code' => $clientCode,
            'status' => 1,
        ])
        ->one();

    if (!$client) {
        Yii::$app->response->statusCode = 403;

        $this->saveApiRequestLog(
            null,
            null,
            null,
            403,
            'Invalid or inactive client: ' . $clientCode
        );

        return [
            'success' => false,
            'message' => 'Invalid or inactive client.',
        ];
    }

    // Get active API keys for this client
    $apiKeys = (new \yii\db\Query())
        ->from('api_keys')
        ->where([
            'client_id' => $client['id'],
            'status' => 1,
        ])
        ->andWhere([
            'or',
            ['expires_at' => null],
            ['>', 'expires_at', date('Y-m-d H:i:s')]
        ])
        ->all();

    $matchedApiKey = null;

    foreach ($apiKeys as $key) {
        if (Yii::$app->security->validatePassword($plainApiKey, $key['api_key_hash'])) {
            $matchedApiKey = $key;
            break;
        }
    }

    if (!$matchedApiKey) {
        Yii::$app->response->statusCode = 403;

        return [
            'success' => false,
            'message' => 'Invalid or expired API key.',
        ];
    }

    $currentRoute = Yii::$app->request->pathInfo;
    $requestMethod = Yii::$app->request->method;

    $apiEndpoint = (new \yii\db\Query())
        ->from('api_endpoints')
        ->where([
            'route' => $currentRoute,
            'method' => $requestMethod,
            'status' => 1,
        ])
        ->one();

    if (!$apiEndpoint) {
        Yii::$app->response->statusCode = 404;

        return [
            'success' => false,
            'message' => 'API endpoint is not registered or inactive.',
            'route' => $currentRoute,
            'method' => $requestMethod,
        ];
    }

    $hasPermission = (new \yii\db\Query())
        ->from('api_key_permissions')
        ->where([
            'api_key_id' => $matchedApiKey['id'],
            'api_endpoint_id' => $apiEndpoint['id'],
            'status' => 1,
        ])
        ->exists();

    if (!$hasPermission) {
        Yii::$app->response->statusCode = 403;

        return [
            'success' => false,
            'message' => 'This API key does not have permission to access this API.',
            'route' => $currentRoute,
        ];
    }

    return [
        'success' => true,
        'client' => $client,
        'api_key' => $matchedApiKey,
        'api_endpoint' => $apiEndpoint,
    ];
}

    public function actionReportWebsite()
    {
        $boat = Yii::$app->request->get('boat');
        $nic  = Yii::$app->request->get('nic');

        $query = ReportWebsite::find();

        if ($boat !== null) {
            $query->andWhere(['like', 'boat_number', $boat]);
        }
        if ($nic !== null) {
            $query->andWhere(['like', 'nic', $nic]);
        }

        $query->orderBy(['boat_number' => SORT_ASC]);

        $data = $query->asArray()->all(); // returns plain array, perfect for JSON

        return [
            'success' => true,
            'count'   => count($data),
            'data'    => $data,
        ];
    }

//    public function actionCatchProduction()
// {
//     Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

//     $data = (new \yii\db\Query())
//         ->select([
//             'sd.id AS trip_id',

//             // scientific_data fields
//             'sd.start_time',
//             'sd.end_time',

//             // boat number from scientific_sampling_data
//             'ssd.boat_number',

//             // landing site name from scientific_data.landing_site
//             'landing_site.name AS landing_site_name',

//             // fishery type name
//             'mfd.name AS fisheryType',

//             // boat gear data
//             'ssbgd.departure_date',
//             'ssbgd.departure_time',

//             // departure district and division names
//             'mdis.name AS Departure_District',
//             'mdiv.name AS Departure_Division',

//             // DB column is depature_port
//             'ssbgd.depature_port AS departure_port_id',
//             'departure_site.name AS departure_port',

//             'ssbgd.arrival_date',
//             'ssbgd.crew_members_count',

//             // gear data
//             'ssgd.id AS gear_data_id',
//             'ssgd.boat_data_id',

//             // catch data
//             'sscd.id AS catch_data_id',
//             'sscd.craft_id',

//             // specie ID and name
//             // 'sscd.specie AS specie_id',
//             'fish_type.name AS speciesName',
//         ])
//         ->from(['ssd' => 'scientific_sampling_data'])

//         // scientific_sampling_data.scientific_id = scientific_data.id
//         ->leftJoin(
//             ['sd' => 'scientific_data'],
//             'sd.id = ssd.scientific_id'
//         )

//         // scientific_data.landing_site = m_landing_site.id
//         ->leftJoin(
//             ['landing_site' => 'm_landing_site'],
//             'landing_site.id = sd.landing_site'
//         )

//         // scientific_sampling_data.id = scientific_sampling_boat_gear_data.sampling_data_id
//         ->leftJoin(
//             ['ssbgd' => 'scientific_sampling_boat_gear_data'],
//             'ssbgd.sampling_data_id = ssd.id'
//         )

//         // departure district
//         ->leftJoin(
//             ['mdis' => 'm_fi_district'],
//             'mdis.id = ssbgd.departure_district'
//         )

//         // departure division
//         ->leftJoin(
//             ['mdiv' => 'm_division'],
//             'mdiv.id = ssbgd.departure_division'
//         )

//         // departure port / landing site
//         ->leftJoin(
//             ['departure_site' => 'm_landing_site'],
//             'departure_site.id = ssbgd.depature_port'
//         )

//         // fishery type
//         ->leftJoin(
//             ['mfd' => 'm_fishery_types'],
//             'mfd.id = ssbgd.fishey_type'
//         )

//         // scientific_sampling_boat_gear_data.id = scientific_sampling_gear_data.boat_data_id
//         ->leftJoin(
//             ['ssgd' => 'scientific_sampling_gear_data'],
//             'ssgd.boat_data_id = ssbgd.id'
//         )

//         // scientific_sampling_data.id = scientific_sampling_catch_data.craft_id
//         ->leftJoin(
//             ['sscd' => 'scientific_sampling_catch_data'],
//             'sscd.craft_id = ssd.id'
//         )

//         // scientific_sampling_catch_data.specie = m_fish_types.id
//         ->leftJoin(
//             ['fish_type' => 'm_fish_types'],
//             'fish_type.id = sscd.specie'
//         )

//         ->orderBy(['ssd.id' => SORT_DESC])
//         ->all();

//     return [
//         'success' => true,
//         'count' => count($data),
//         'data' => $data,
//     ];
// }

public function actionCatchProduction()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    // ==============================
    // API DATA QUERY
    // ==============================
    $rows = (new \yii\db\Query())
        ->select([
            'ssd.id AS trip_id',

            'sd.start_time',
            'sd.end_time',
            'ssd.boat_number',

            'landing_site.name AS landing_site_name',
            'mfd.name AS fisheryType',

            'ssbgd.id AS boat_gear_data_id',
            'ssbgd.departure_date',
            'ssbgd.departure_time',
            'mdis.name AS Departure_District',
            'mdiv.name AS Departure_Division',
            'departure_site.name AS departure_port',
            'ssbgd.arrival_date',
            'ssbgd.crew_members_count',

            // Internal use only for grouping
            'ssgd.id AS internal_gear_data_id',

            // Gear type name only
            'mgt.description AS gear_type_name',

            // Species name only
            'fish_type.name AS speciesName',
        ])
        ->from(['ssd' => 'scientific_sampling_data'])

        ->leftJoin(
            ['sd' => 'scientific_data'],
            'sd.id = ssd.scientific_id'
        )

        ->leftJoin(
            ['landing_site' => 'm_landing_site'],
            'landing_site.id = sd.landing_site'
        )

        ->leftJoin(
            ['ssbgd' => 'scientific_sampling_boat_gear_data'],
            'ssbgd.sampling_data_id = ssd.id'
        )

        ->leftJoin(
            ['mdis' => 'm_fi_district'],
            'mdis.id = ssbgd.departure_district'
        )

        ->leftJoin(
            ['mdiv' => 'm_division'],
            'mdiv.id = ssbgd.departure_division'
        )

        ->leftJoin(
            ['departure_site' => 'm_landing_site'],
            'departure_site.id = ssbgd.depature_port'
        )

        ->leftJoin(
            ['mfd' => 'm_fishery_types'],
            'mfd.id = ssbgd.fishey_type'
        )

        ->leftJoin(
            ['ssgd' => 'scientific_sampling_gear_data'],
            'ssgd.boat_data_id = ssbgd.id'
        )

        ->leftJoin(
            ['mgt' => 'm_gear_types'],
            'mgt.id = ssgd.gear'
        )

        ->leftJoin(
            ['sscd' => 'scientific_sampling_catch_data'],
            'sscd.craft_id = ssd.id'
        )

        ->leftJoin(
            ['fish_type' => 'm_fish_types'],
            'fish_type.id = sscd.specie'
        )

        ->orderBy(['ssd.id' => SORT_DESC])
        ->all();

    // ==============================
    // GROUP DATA BY TRIP ID
    // ==============================
    $result = [];

    foreach ($rows as $row) {
        $tripId = $row['trip_id'];

        if (!isset($result[$tripId])) {
            $result[$tripId] = [
                'trip_id' => $row['trip_id'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'boat_number' => $row['boat_number'],
                'landing_site_name' => $row['landing_site_name'],
                'fisheryType' => $row['fisheryType'],
                'departure_date' => $row['departure_date'],
                'departure_time' => $row['departure_time'],
                'Departure_District' => $row['Departure_District'],
                'Departure_Division' => $row['Departure_Division'],
                'departure_port' => $row['departure_port'],
                'arrival_date' => $row['arrival_date'],
                'crew_members_count' => $row['crew_members_count'],

                'gear_data' => [],
                'catch_data' => [],
            ];
        }

        // Add only gear type name
        if (!empty($row['gear_type_name'])) {
            $gearKey = $row['gear_type_name'];

            $result[$tripId]['gear_data'][$gearKey] = [
                'gear_type_name' => $row['gear_type_name'],
            ];
        }

        // Add only species name
        if (!empty($row['speciesName'])) {
            $speciesKey = $row['speciesName'];

            $result[$tripId]['catch_data'][$speciesKey] = [
                'speciesName' => $row['speciesName'],
            ];
        }
    }

    foreach ($result as &$trip) {
        $trip['gear_data'] = array_values($trip['gear_data']);
        $trip['catch_data'] = array_values($trip['catch_data']);
    }

    $finalData = array_values($result);

    return [
        'success' => true,
        'count' => count($finalData),
        'data' => $finalData,
    ];
}

public function actionUpdateVmsPayment()
{
    $request = Yii::$app->request;
    $response = Yii::$app->response;

    $response->format = \yii\web\Response::FORMAT_JSON;

    if (!$request->isPost) {
        $response->statusCode = 405;

        return [
            'success' => false,
            'message' => 'Only POST requests are allowed.',
        ];
    }

    try {
        /*
         * First try Yii parsed body parameters.
         */
        $payload = $request->bodyParams;

        /*
         * If bodyParams is empty, manually decode the raw JSON body.
         * This also works when Postman sends an incorrect Content-Type.
         */
        if (!is_array($payload) || empty($payload)) {
            $rawBody = trim((string)$request->getRawBody());

            if ($rawBody === '') {
                $response->statusCode = 400;

                return [
                    'success' => false,
                    'message' => 'The request body is empty.',
                    'content_type' => $request->contentType,
                ];
            }

            try {
                $decodedBody = json_decode(
                    $rawBody,
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
            } catch (\JsonException $e) {
                $response->statusCode = 400;

                return [
                    'success' => false,
                    'message' => 'The request body contains invalid JSON.',
                    'error' => $e->getMessage(),
                ];
            }

            if (!is_array($decodedBody)) {
                $response->statusCode = 400;

                return [
                    'success' => false,
                    'message' => 'The JSON request body must be an object.',
                ];
            }

            $payload = $decodedBody;
        }

        /*
         * Support nested JSON:
         *
         * {
         *     "DepatureBoatPayment": {
         *         "boat_number": "IMULA0158BCO"
         *     }
         * }
         */
        if (
            isset($payload['DepatureBoatPayment']) &&
            is_array($payload['DepatureBoatPayment'])
        ) {
            $payload = $payload['DepatureBoatPayment'];
        }

        /*
         * Read API values.
         */
        $boatNumber = trim(
            (string)($payload['boat_number'] ?? '')
        );

        $amount = $payload['amount'] ?? null;

        $fromMonthInput = trim(
            (string)($payload['from_month'] ?? '')
        );

        $toMonthInput = trim(
            (string)($payload['to_date'] ?? '')
        );

        $referenceCode = trim(
            (string)(
                $payload['ReferenceCode']
                ?? $payload['reference_code']
                ?? ''
            )
        );

        /*
         * Request validation.
         */
        $errors = [];

        if ($boatNumber === '') {
            $errors['boat_number'][] =
                'Boat number is required.';
        }

        if (
            $amount === null ||
            trim((string)$amount) === ''
        ) {
            $errors['amount'][] =
                'Amount is required.';
        } elseif (!is_numeric($amount)) {
            $errors['amount'][] =
                'Amount must be a valid number.';
        }
        //  elseif ((float)$amount <= 0) {
        //     $errors['amount'][] =
        //         'Amount must be greater than zero.';
        // }

        if ($fromMonthInput === '') {
            $errors['from_month'][] =
                'From Month is required.';
        }

        if ($toMonthInput === '') {
            $errors['to_date'][] =
                'To Month is required.';
        }

        if ($referenceCode === '') {
            $errors['reference_code'][] =
                'ReferenceCode is required.';
        }

        if (!empty($errors)) {
            $response->statusCode = 422;

            return [
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $errors,
            ];
        }

        /*
         * Resolve boat_reg_id:
         *
         * boat_numbers.boat_number = API boat_number
         * boat_numbers.id = departure_boats.boat_number_id
         * departure_boats.id = boat_reg_id
         */
        $boatRecord = (new \yii\db\Query())
            ->select([
                'boat_reg_id' => 'db.id',
                'boat_number_id' => 'bn.id',
                'boat_number' => 'bn.boat_number',
            ])
            ->from([
                'bn' => 'boat_numbers',
            ])
            ->innerJoin(
                ['db' => 'fisherman_registerd_boat'],
                'db.boat_number_id = bn.id'
            )
            ->where([
                'bn.boat_number' => $boatNumber,
                // 'bn.status' => 101,
            ])
            ->orderBy([
                'bn.id' => SORT_DESC,
                'db.id' => SORT_DESC,
            ])
            ->limit(1)
            ->one();

        if (!$boatRecord) {
            $response->statusCode = 404;

            return [
                'success' => false,
                'message' => 'Boat number was not found.',
                'errors' => [
                    'boat_number' => [
                        'No departure boat registration was found for the provided boat number.',
                    ],
                ],
            ];
        }

        /*
         * Normalise payment months.
         */
        $fromMonth = $this->normalizeVmsPaymentMonth(
            $fromMonthInput
        );

        $toMonth = $this->normalizeVmsPaymentMonth(
            $toMonthInput
        );

        if ($fromMonth === false) {
            $errors['from_month'][] =
                'From Month must use the YYYY-MM format.';
        }

        if ($toMonth === false) {
            $errors['to_date'][] =
                'To Month must use the YYYY-MM format.';
        }

        if (
            $fromMonth !== false &&
            $toMonth !== false &&
            strtotime($fromMonth) > strtotime($toMonth)
        ) {
            $errors['to_date'][] =
                'To Month must be greater than or equal to From Month.';
        }

        $addedBy =
            Yii::$app->params['apiSystemUserId'] ?? null;

        if (empty($addedBy)) {
            $errors['added_by'][] =
                'The API system user has not been configured.';
        }

        if (!empty($errors)) {
            $response->statusCode = 422;

            return [
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $errors,
            ];
        }

        /*
         * Create payment record.
         */
        $paymentLog = new DepatureBoatPayment();

        $paymentLog->boat_reg_id =
            (int)$boatRecord['boat_reg_id'];

        $paymentLog->amount =
            (float)$amount;

        $paymentLog->from_month =
            $fromMonth;

        $paymentLog->to_date =
            $toMonth;

        $paymentLog->reference_code =
            $referenceCode;

        $paymentLog->added_by =
            (int)$addedBy;

        /*
         * Validate only attributes stored in this table.
         * boat_number is not included because it is only
         * used to resolve boat_reg_id.
         */
        $attributesToValidate = [
            'boat_reg_id',
            'amount',
            'from_month',
            'to_date',
            'reference_code',
            'added_by',
        ];

        $paymentLog->validate(
            $attributesToValidate
        );

        if ($paymentLog->hasErrors()) {
            $response->statusCode = 422;

            return [
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $paymentLog->getErrors(),
            ];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$paymentLog->save(false)) {
                throw new \RuntimeException(
                    'The VMS payment record could not be saved.'
                );
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            if ($transaction->isActive) {
                $transaction->rollBack();
            }

            throw $e;
        }

        $response->statusCode = 201;

        return [
            'success' => true,
            'message' => 'VMS payment record created successfully.',
            'data' => [
                'id' => $paymentLog->getPrimaryKey(),
                'boat_number' => $boatRecord['boat_number'],
                'boat_number_id' =>
                    (int)$boatRecord['boat_number_id'],
                'boat_reg_id' =>
                    (int)$paymentLog->boat_reg_id,
                'amount' =>
                    (float)$paymentLog->amount,
                'from_month' =>
                    $paymentLog->from_month,
                'to_date' =>
                    $paymentLog->to_date,
                'ReferenceCode' =>
                    $paymentLog->reference_code,
                'added_by' =>
                    (int)$paymentLog->added_by,
            ],
        ];
    } catch (\Throwable $e) {
        Yii::error(
            $e->__toString(),
            'update-vms-payment-api'
        );

        $response->statusCode = 500;

        return [
            'success' => false,
            'message' => 'The VMS payment record could not be saved.',
            'error' => YII_DEBUG
                ? $e->getMessage()
                : null,
        ];
    }
}

private function normalizeVmsPaymentMonth($value)
{
    $value = trim((string)$value);

    if (
        preg_match(
            '/^\d{4}-(0[1-9]|1[0-2])$/',
            $value
        )
    ) {
        return $value . '-01';
    }

    if (
        preg_match(
            '/^\d{4}-(0[1-9]|1[0-2])-01$/',
            $value
        )
    ) {
        return $value;
    }

    return false;
}


public function actionProductionData()
{
    Yii::$app->response->format = Response::FORMAT_JSON;

    try {
        $request = Yii::$app->request;

        $page = max(
            1,
            (int) $request->get('page', 1)
        );

        $pageSize = max(
            1,
            min(
                (int) $request->get('pageSize', 100),
                1000
            )
        );

        $offset = ($page - 1) * $pageSize;

        $tripId = $request->get('tripId');

        $fromDate = trim(
            (string) $request->get('fromDate', '')
        );

        $toDate = trim(
            (string) $request->get('toDate', '')
        );

        $approved = $request->get(
            'approved',
            '1'
        );

        /*
         * Temporary debugging:
         *
         * ?debug=1
         */
        $debug = (
            (int) $request->get(
                'debug',
                0
            ) === 1
        );

        $debugErrors = [];


        /*
         * =====================================================
         * MAIN PRODUCTION QUERY
         * =====================================================
         */

        $query = (new Query())
            ->select([

                /*
                 * Catch
                 */
                'catch_id' =>
                    'catch_data.id',

                'fish_type_id' =>
                    'catch_data.fish_type_id',

                'fish_variant_id' =>
                    'catch_data.fish_variant_id',

                'production_quantity_numbers' =>
                    'catch_data.fish_count',

                'production_quantity_kg' =>
                    'catch_data.weight',


                /*
                 * Set
                 */
                'set_id' =>
                    'elog_set.id',

                'gear_type' =>
                    'elog_set.gear_type',

                'set_number' =>
                    'elog_set.set_number',

                'start_datetime' =>
                    'elog_set.start_datetime',

                'end_datetime' =>
                    'elog_set.end_datetime',

                'start_gps_direction' =>
                    'elog_set.start_gps_direction',

                'start_gps_n' =>
                    'elog_set.start_gps_n',

                'start_gps_e' =>
                    'elog_set.start_gps_e',

                'five_by_five' =>
                    'elog_set.five_by_five',

                'one_by_one' =>
                    'elog_set.one_by_one',


                /*
                 * Trip ID
                 */
                'trip_id' =>
                    new Expression(
                        '
                        COALESCE(
                            longline.e_log_id,
                            gillnet.e_log_id,
                            ringnet.e_log_id
                        )
                        '
                    ),


                /*
                 * E-Log
                 */
                'vessel_id' =>
                    'elog.vessel_id',

                'skipper_id' =>
                    'elog.skipper_id',

                'departure_harbour_id' =>
                    'elog.departure_harbour',

                'arrival_harbour_id' =>
                    'elog.arrival_harbour',

                'departure_date' =>
                    'elog.departure_date',

                'arrival_date' =>
                    'elog.arrival_date',

                'request_date' =>
                    'elog.created_at',

                'approve' =>
                    'elog.approve',


                /*
                 * Harbour
                 */
                'departure_harbour_name' =>
                    'departure_harbour.Name',

                'arrival_harbour_name' =>
                    'arrival_harbour.Name',


                /*
                 * Fish
                 */
                'fish_type_name' =>
                    'fish_type.name',

                'fish_variant_name' =>
                    'fish_variant.name',
            ])


            ->from([
                'catch_data' =>
                    'e_log_set_catch',
            ])


            /*
             * Catch -> Set
             */
            ->innerJoin(
                [
                    'elog_set' =>
                        'e_log_sets',
                ],
                '
                    elog_set.id =
                    catch_data.e_log_set_id
                '
            )


            /*
             * Longline
             */
            ->leftJoin(
                [
                    'longline' =>
                        'e_log_longline',
                ],
                "
                    longline.id =
                    elog_set.gear_id

                    AND

                    elog_set.gear_type =
                    'longline'
                "
            )


            /*
             * Gillnet
             */
            ->leftJoin(
                [
                    'gillnet' =>
                        'e_log_gillnet',
                ],
                "
                    gillnet.id =
                    elog_set.gear_id

                    AND

                    elog_set.gear_type =
                    'gillnet'
                "
            )


            /*
             * Ringnet
             */
            ->leftJoin(
                [
                    'ringnet' =>
                        'e_log_ringnet',
                ],
                "
                    ringnet.id =
                    elog_set.gear_id

                    AND

                    elog_set.gear_type =
                    'ringnet'
                "
            )


            /*
             * Main E-Log
             */
            ->innerJoin(
                [
                    'elog' =>
                        'e_log',
                ],
                '
                    elog.id =
                    COALESCE(
                        longline.e_log_id,
                        gillnet.e_log_id,
                        ringnet.e_log_id
                    )
                '
            )


            /*
             * Departure harbour
             */
            ->leftJoin(
                [
                    'departure_harbour' =>
                        'm_harbours',
                ],
                '
                    departure_harbour.Id =
                    elog.departure_harbour
                '
            )


            /*
             * Arrival harbour
             */
            ->leftJoin(
                [
                    'arrival_harbour' =>
                        'm_harbours',
                ],
                '
                    arrival_harbour.Id =
                    elog.arrival_harbour
                '
            )


            /*
             * Fish type
             */
            ->leftJoin(
                [
                    'fish_type' =>
                        'm_e_log_fish_type',
                ],
                '
                    fish_type.id =
                    catch_data.fish_type_id
                '
            )


            /*
             * Fish variant
             */
            ->leftJoin(
                [
                    'fish_variant' =>
                        'm_e_log_fish_variant',
                ],
                '
                    fish_variant.id =
                    catch_data.fish_variant_id
                '
            );


        /*
         * =====================================================
         * FILTERS
         * =====================================================
         */

        if ($approved !== 'all') {

            $query->andWhere([
                'elog.approve' =>
                    (int) $approved,
            ]);
        }


        if (
            $tripId !== null
            &&
            $tripId !== ''
            &&
            is_numeric($tripId)
        ) {

            $query->andWhere([
                'elog.id' =>
                    (int) $tripId,
            ]);
        }


        if (
            $fromDate !== ''
            &&
            preg_match(
                '/^\d{4}-\d{2}-\d{2}$/',
                $fromDate
            )
        ) {

            $query->andWhere([
                '>=',
                'elog.departure_date',
                $fromDate,
            ]);
        }


        if (
            $toDate !== ''
            &&
            preg_match(
                '/^\d{4}-\d{2}-\d{2}$/',
                $toDate
            )
        ) {

            $query->andWhere([
                '<=',
                'elog.departure_date',
                $toDate,
            ]);
        }


        /*
         * =====================================================
         * TOTAL
         * =====================================================
         */

        $total = (int) (clone $query)->count(
            '*',
            Yii::$app->db
        );


        /*
         * =====================================================
         * PAGINATION
         * =====================================================
         */

        $rows = $query
            ->orderBy([
                'elog.id' =>
                    SORT_DESC,

                'elog_set.set_number' =>
                    SORT_ASC,

                'catch_data.id' =>
                    SORT_ASC,
            ])
            ->limit($pageSize)
            ->offset($offset)
            ->all(Yii::$app->db);


        /*
         * =====================================================
         * HELPERS
         * =====================================================
         */

        $normalizeBoat = static function (
            $value
        ): string {

            return strtoupper(
                trim(
                    (string) $value
                )
            );
        };


        /*
         * fishing_area must be returned as a STRING.
         *
         * Valid examples:
         *
         * EEZ
         * High Seas
         *
         * 0 is considered an invalid/placeholder value.
         */
        $normalizeFisheryType =
            static function (
                $value
            ): ?string {

                if ($value === null) {
                    return null;
                }


                $value = trim(
                    (string) $value
                );


                if (
                    $value === ''
                    ||
                    $value === '0'
                ) {
                    return null;
                }


                return $value;
            };


        /*
         * =====================================================
         * CACHES
         * =====================================================
         *
         * Prevent repeated DB calls for the same boat/request.
         */

        $departureRequestCache = [];

        $crewCountCache = [];

        $districtCache = [];

        $districtNameCache = [];


        /*
         * =====================================================
         * FINAL DATA
         * =====================================================
         */

        $data = [];


        foreach ($rows as $row) {

            /*
             * ---------------------------------------------
             * BASIC VALUES
             * ---------------------------------------------
             */

            $vesselId = trim(
                (string) (
                    $row[
                        'vessel_id'
                    ] ?? ''
                )
            );


            $skipperId = trim(
                (string) (
                    $row[
                        'skipper_id'
                    ] ?? ''
                )
            );


            $departureHarbourName = trim(
                (string) (
                    $row[
                        'departure_harbour_name'
                    ]
                    ?? ''
                )
            );


            $arrivalHarbourName = trim(
                (string) (
                    $row[
                        'arrival_harbour_name'
                    ]
                    ?? ''
                )
            );


            $fishTypeName = trim(
                (string) (
                    $row[
                        'fish_type_name'
                    ]
                    ?? ''
                )
            );


            $fishVariantName = trim(
                (string) (
                    $row[
                        'fish_variant_name'
                    ]
                    ?? ''
                )
            );


            $speciesName =
                $fishVariantName !== ''
                    ? $fishVariantName
                    : $fishTypeName;


            $fishTypeId = (
                isset(
                    $row[
                        'fish_type_id'
                    ]
                )
                &&
                $row[
                    'fish_type_id'
                ] !== ''
            )
                ? (int) $row[
                    'fish_type_id'
                ]
                : null;


            $fishVariantId = (
                isset(
                    $row[
                        'fish_variant_id'
                    ]
                )
                &&
                $row[
                    'fish_variant_id'
                ] !== ''
            )
                ? (int) $row[
                    'fish_variant_id'
                ]
                : null;


            /*
             * ---------------------------------------------
             * GPS
             * ---------------------------------------------
             */

            $latitude =
                $this
                    ->convertDegreeMinuteCoordinate(
                        $row[
                            'start_gps_n'
                        ] ?? null,

                        $row[
                            'start_gps_direction'
                        ] ?? null
                    );


            $longitude =
                $this
                    ->convertDegreeMinuteCoordinate(
                        $row[
                            'start_gps_e'
                        ] ?? null,

                        'E'
                    );


            /*
             * ---------------------------------------------
             * DEFAULT EXTRA VALUES
             * ---------------------------------------------
             */

            $departureRequestId = null;

            $fisheryType = null;

            $fishermanCount = null;

            $districtId = null;

            $districtName = null;


            /*
             * =================================================
             * DEPARTURE REQUEST
             * =================================================
             */

            $boatKey =
                $normalizeBoat(
                    $vesselId
                );


            $departureDate =
                substr(
                    (string) (
                        $row[
                            'departure_date'
                        ] ?? ''
                    ),
                    0,
                    10
                );


            $requestKey =
                $boatKey .
                '|' .
                $departureDate;


            if (
                $boatKey !== ''
                &&
                preg_match(
                    '/^\d{4}-\d{2}-\d{2}$/',
                    $departureDate
                )
            ) {

                /*
                 * Only query once for same boat/date.
                 */
                if (
                    !array_key_exists(
                        $requestKey,
                        $departureRequestCache
                    )
                ) {

                    try {

                        /*
                         * IMPORTANT:
                         *
                         * Match:
                         *
                         * elog.vessel_id
                         *   =
                         * departure_requests.boat_no
                         *
                         * elog.departure_date
                         *   =
                         * DATE(
                         *   departure_requests.action_date
                         * )
                         *
                         *
                         * If multiple records exist,
                         * prefer one with a meaningful
                         * fishing_area such as:
                         *
                         * EEZ
                         * High Seas
                         *
                         * instead of:
                         *
                         * 0
                         */

                        $departureRequestCache[
                            $requestKey
                        ] =
                            Yii::$app->db
                                ->createCommand(
                                    "
                                    SELECT
                                        dr.id,
                                        dr.boat_no,
                                        dr.action_date,
                                        dr.fishing_area

                                    FROM
                                        departure_requests dr

                                    WHERE
                                        UPPER(
                                            TRIM(
                                                dr.boat_no
                                            )
                                        )
                                        =
                                        :boat_no

                                        AND

                                        DATE(
                                            dr.action_date
                                        )
                                        =
                                        :departure_date

                                    ORDER BY

                                        CASE

                                            WHEN
                                                dr.fishing_area
                                                IS NOT NULL

                                                AND

                                                TRIM(
                                                    CAST(
                                                        dr.fishing_area
                                                        AS CHAR
                                                    )
                                                ) <> ''

                                                AND

                                                TRIM(
                                                    CAST(
                                                        dr.fishing_area
                                                        AS CHAR
                                                    )
                                                ) <> '0'

                                            THEN 0

                                            ELSE 1

                                        END ASC,

                                        dr.id DESC

                                    LIMIT 1
                                    ",
                                    [
                                        ':boat_no' =>
                                            $boatKey,

                                        ':departure_date' =>
                                            $departureDate,
                                    ]
                                )
                                ->queryOne();

                    } catch (\Throwable $e) {

                        $departureRequestCache[
                            $requestKey
                        ] = false;


                        Yii::error(
                            [
                                'stage' =>
                                    'departure-request',

                                'vesselId' =>
                                    $vesselId,

                                'departureDate' =>
                                    $departureDate,

                                'message' =>
                                    $e->getMessage(),
                            ],
                            'production-data-api'
                        );


                        if ($debug) {

                            $debugErrors[] = [
                                'stage' =>
                                    'departure-request',

                                'vesselId' =>
                                    $vesselId,

                                'departureDate' =>
                                    $departureDate,

                                'error' =>
                                    $e->getMessage(),
                            ];
                        }
                    }
                }


                $departureRequest =
                    $departureRequestCache[
                        $requestKey
                    ];


                if (
                    is_array(
                        $departureRequest
                    )
                    &&
                    !empty(
                        $departureRequest
                    )
                ) {

                    /*
                     * Request ID
                     */
                    $departureRequestId =
                        isset(
                            $departureRequest[
                                'id'
                            ]
                        )
                            ? (int)
                                $departureRequest[
                                    'id'
                                ]
                            : null;


                    /*
                     * Fishing area:
                     *
                     * EEZ
                     * High Seas
                     */
                    $fisheryType =
                        $normalizeFisheryType(
                            $departureRequest[
                                'fishing_area'
                            ] ?? null
                        );
                }
            }


            /*
             * =================================================
             * CREW COUNT
             * =================================================
             */

            if (
                $departureRequestId
                !== null
            ) {

                if (
                    !array_key_exists(
                        $departureRequestId,
                        $crewCountCache
                    )
                ) {

                    try {

                        $crewCountCache[
                            $departureRequestId
                        ] =
                            (int)
                            Yii::$app->db
                                ->createCommand(
                                    '
                                    SELECT
                                        COUNT(*)

                                    FROM
                                        deparure_request_crew

                                    WHERE
                                        request_id =
                                        :request_id
                                    ',
                                    [
                                        ':request_id' =>
                                            $departureRequestId,
                                    ]
                                )
                                ->queryScalar();

                    } catch (\Throwable $e) {

                        $crewCountCache[
                            $departureRequestId
                        ] = null;


                        Yii::error(
                            [
                                'stage' =>
                                    'crew-count',

                                'requestId' =>
                                    $departureRequestId,

                                'message' =>
                                    $e->getMessage(),
                            ],
                            'production-data-api'
                        );


                        if ($debug) {

                            $debugErrors[] = [
                                'stage' =>
                                    'crew-count',

                                'requestId' =>
                                    $departureRequestId,

                                'error' =>
                                    $e->getMessage(),
                            ];
                        }
                    }
                }


                $fishermanCount =
                    $crewCountCache[
                        $departureRequestId
                    ];
            }


            /*
             * =================================================
             * DISTRICT
             * =================================================
             *
             * vessel_id
             *      =
             * boat_numbers.boat_number
             *
             * status = 101
             *
             * boat_numbers.id
             *      =
             * fisherman_registered_boat.boat_number_id
             *
             * districtId
             *      =
             * fisherman_registered_boat.district
             */

            if ($boatKey !== '') {

                if (
                    !array_key_exists(
                        $boatKey,
                        $districtCache
                    )
                ) {

                    try {

                        $districtValue =
                            Yii::$app->db
                                ->createCommand(
                                    "
                                    SELECT
                                        frb.district

                                    FROM
                                        boat_numbers bn

                                    INNER JOIN
                                        fisherman_registered_boat frb

                                        ON
                                            frb.boat_number_id
                                            =
                                            bn.id

                                    WHERE
                                        UPPER(
                                            TRIM(
                                                bn.boat_number
                                            )
                                        )
                                        =
                                        :boat_number

                                        AND

                                        bn.status = 101

                                        AND

                                        frb.district
                                        IS NOT NULL

                                    ORDER BY
                                        bn.id DESC

                                    LIMIT 1
                                    ",
                                    [
                                        ':boat_number' =>
                                            $boatKey,
                                    ]
                                )
                                ->queryScalar();


                        $districtCache[
                            $boatKey
                        ] = (
                            $districtValue
                                !== false
                            &&
                            $districtValue
                                !== null
                            &&
                            $districtValue
                                !== ''
                        )
                            ? (int)
                                $districtValue
                            : null;

                    } catch (\Throwable $e) {

                        $districtCache[
                            $boatKey
                        ] = null;


                        Yii::error(
                            [
                                'stage' =>
                                    'boat-district',

                                'vesselId' =>
                                    $vesselId,

                                'message' =>
                                    $e->getMessage(),
                            ],
                            'production-data-api'
                        );


                        if ($debug) {

                            $debugErrors[] = [
                                'stage' =>
                                    'boat-district',

                                'vesselId' =>
                                    $vesselId,

                                'error' =>
                                    $e->getMessage(),
                            ];
                        }
                    }
                }


                $districtId =
                    $districtCache[
                        $boatKey
                    ];
            }


            /*
             * =================================================
             * DISTRICT NAME
             * =================================================
             */

            if (
                $districtId !== null
            ) {

                if (
                    !array_key_exists(
                        $districtId,
                        $districtNameCache
                    )
                ) {

                    try {

                        /*
                         * Use SELECT * because the exact
                         * district name column casing/name
                         * has not been confirmed.
                         */

                        $districtRow =
                            Yii::$app->db
                                ->createCommand(
                                    '
                                    SELECT
                                        *

                                    FROM
                                        m_fi_district

                                    WHERE
                                        id =
                                        :district_id

                                    LIMIT 1
                                    ',
                                    [
                                        ':district_id' =>
                                            $districtId,
                                    ]
                                )
                                ->queryOne();


                        $districtNameCache[
                            $districtId
                        ] = null;


                        if (
                            is_array(
                                $districtRow
                            )
                        ) {

                            /*
                             * Create lower-case
                             * column map.
                             */
                            $columnMap = [];


                            foreach (
                                array_keys(
                                    $districtRow
                                )
                                as $columnName
                            ) {

                                $columnMap[
                                    strtolower(
                                        (string)
                                        $columnName
                                    )
                                ] =
                                    $columnName;
                            }


                            /*
                             * Try possible column names.
                             */
                            foreach (
                                [
                                    'name',
                                    'district_name',
                                    'districtname',
                                    'district',
                                ]
                                as $candidate
                            ) {

                                if (
                                    !isset(
                                        $columnMap[
                                            $candidate
                                        ]
                                    )
                                ) {
                                    continue;
                                }


                                $actualColumn =
                                    $columnMap[
                                        $candidate
                                    ];


                                $value = trim(
                                    (string) (
                                        $districtRow[
                                            $actualColumn
                                        ] ?? ''
                                    )
                                );


                                if (
                                    $value !== ''
                                ) {

                                    $districtNameCache[
                                        $districtId
                                    ] =
                                        $value;

                                    break;
                                }
                            }
                        }

                    } catch (\Throwable $e) {

                        $districtNameCache[
                            $districtId
                        ] = null;


                        Yii::error(
                            [
                                'stage' =>
                                    'district-name',

                                'districtId' =>
                                    $districtId,

                                'message' =>
                                    $e->getMessage(),
                            ],
                            'production-data-api'
                        );


                        if ($debug) {

                            $debugErrors[] = [
                                'stage' =>
                                    'district-name',

                                'districtId' =>
                                    $districtId,

                                'error' =>
                                    $e->getMessage(),
                            ];
                        }
                    }
                }


                $districtName =
                    $districtNameCache[
                        $districtId
                    ];
            }


            /*
             * =================================================
             * API OBJECT
             * =================================================
             */

            $data[] = [

                'tripId' => (
                    isset(
                        $row[
                            'trip_id'
                        ]
                    )
                    &&
                    $row[
                        'trip_id'
                    ] !== ''
                )
                    ? (int)
                        $row[
                            'trip_id'
                        ]
                    : null,


                'vesselId' =>
                    $vesselId !== ''
                        ? $vesselId
                        : null,


                'skipperId' =>
                    $skipperId !== ''
                        ? $skipperId
                        : null,


                'departureHarbor' =>
                    $departureHarbourName
                    !== ''
                        ? $departureHarbourName
                        : null,


                'startDate' =>
                    $row[
                        'departure_date'
                    ] ?? null,


                'arrivalHarbor' =>
                    $arrivalHarbourName
                    !== ''
                        ? $arrivalHarbourName
                        : null,


                'endDate' =>
                    $row[
                        'arrival_date'
                    ] ?? null,


                'fishName' =>
                    $speciesName !== ''
                        ? $speciesName
                        : null,


                'gearType' =>
                    $row[
                        'gear_type'
                    ] ?? null,


                /*
                 * departure_requests.fishing_area
                 *
                 * Examples:
                 *
                 * EEZ
                 * High Seas
                 */
                'fisheryType' =>
                    $fisheryType,


                /*
                 * Number of crew rows
                 */
                'fishermanCount' =>
                    $fishermanCount,


                /*
                 * departure_requests.id
                 */
                'requestId' =>
                    $departureRequestId,


                /*
                 * Existing E-Log created date.
                 */
                'requestDate' =>
                    $row[
                        'request_date'
                    ] ?? null,


                /*
                 * fisherman_registered_boat.district
                 */
                'districtId' =>
                    $districtId,


                /*
                 * m_fi_district district name
                 */
                'districtName' =>
                    $districtName,


                /*
                 * Landing harbour
                 */
                'harbourId' => (
                    isset(
                        $row[
                            'arrival_harbour_id'
                        ]
                    )
                    &&
                    $row[
                        'arrival_harbour_id'
                    ] !== ''
                )
                    ? (int)
                        $row[
                            'arrival_harbour_id'
                        ]
                    : null,


                'harbourName' =>
                    $arrivalHarbourName
                    !== ''
                        ? $arrivalHarbourName
                        : null,


                /*
                 * Catch row ID
                 */
                'samplingDataId' => (
                    isset(
                        $row[
                            'catch_id'
                        ]
                    )
                    &&
                    $row[
                        'catch_id'
                    ] !== ''
                )
                    ? (int)
                        $row[
                            'catch_id'
                        ]
                    : null,


                'boatNumber' =>
                    $vesselId !== ''
                        ? $vesselId
                        : null,


                'speciesId' =>
                    $fishVariantId
                    ?? $fishTypeId,


                'speciesType' =>
                    $fishTypeName !== ''
                        ? $fishTypeName
                        : null,


                'speciesName' =>
                    $speciesName !== ''
                        ? $speciesName
                        : null,


                'productionQuantityNumbers' =>
                    isset(
                        $row[
                            'production_quantity_numbers'
                        ]
                    )
                        ? (int)
                            $row[
                                'production_quantity_numbers'
                            ]
                        : 0,


                'productionQuantityKg' =>
                    isset(
                        $row[
                            'production_quantity_kg'
                        ]
                    )
                        ? (float)
                            $row[
                                'production_quantity_kg'
                            ]
                        : 0.0,


                /*
                 * Price source not yet supplied.
                 */
                'pricePerKg' =>
                    null,


                'catchLatitude' =>
                    $latitude,


                'catchLongitude' =>
                    $longitude,


                'departureSiteType' =>
                    'HARBOUR',
            ];
        }


        /*
         * =====================================================
         * RESPONSE
         * =====================================================
         */

        $response = [

            'success' =>
                true,

            'count' =>
                count(
                    $data
                ),

            'total' =>
                $total,

            'page' =>
                $page,

            'pageSize' =>
                $pageSize,

            'data' =>
                $data,
        ];


        /*
         * Debug data only when:
         *
         * ?debug=1
         */
        if ($debug) {

            $response[
                'debugErrors'
            ] =
                $debugErrors;
        }


        return $response;

    } catch (\Throwable $e) {

        Yii::error(
            [
                'stage' =>
                    'MAIN-PRODUCTION-QUERY',

                'message' =>
                    $e->getMessage(),

                'file' =>
                    $e->getFile(),

                'line' =>
                    $e->getLine(),

                'trace' =>
                    $e->getTraceAsString(),
            ],
            'production-data-api'
        );


        Yii::$app->response
            ->statusCode = 500;


        if (
            isset($debug)
            &&
            $debug
        ) {

            return [

                'success' =>
                    false,

                'stage' =>
                    'MAIN-PRODUCTION-QUERY',

                'error' =>
                    $e->getMessage(),

                'file' =>
                    $e->getFile(),

                'line' =>
                    $e->getLine(),

                'message' =>
                    'Unable to retrieve production data.',
            ];
        }


        return [

            'success' =>
                false,

            'message' =>
                'Unable to retrieve production data.',
        ];
    }
}

private function getFirstModelValue(
    $model,
    array $attributes,
    $default = null
) {
    if ($model === null) {
        return $default;
    }

    foreach ($attributes as $attribute) {
        try {
            if (
                $model instanceof \yii\db\BaseActiveRecord
                && $model->hasAttribute($attribute)
            ) {
                $value = $model->getAttribute(
                    $attribute
                );

                if ($value !== null && $value !== '') {
                    return $value;
                }

                continue;
            }

            if (isset($model->$attribute)) {
                $value = $model->$attribute;

                if ($value !== null && $value !== '') {
                    return $value;
                }
            }
        } catch (\Throwable $e) {
            Yii::warning(
                'Unable to read model attribute '
                . $attribute
                . ': '
                . $e->getMessage(),
                'api'
            );
        }
    }

    return $default;
}

private function convertDegreeMinuteCoordinate(
    $value,
    $direction = null
) {
    if ($value === null || $value === '') {
        return null;
    }

    $coordinate = trim((string) $value);

    if ($coordinate === '') {
        return null;
    }

    $isNegative =
        strpos($coordinate, '-') === 0;

    $coordinate = ltrim(
        $coordinate,
        '+-'
    );

    $parts = explode(
        '.',
        $coordinate,
        2
    );

    $degrees = (int) ($parts[0] ?? 0);

    $minuteText = $parts[1] ?? '0';

    $minuteText = str_pad(
        substr($minuteText, 0, 2),
        2,
        '0'
    );

    $minutes = (int) $minuteText;

    if ($minutes >= 60) {
        return null;
    }

    $decimal =
        $degrees + ($minutes / 60);

    $direction = strtoupper(
        trim((string) $direction)
    );

    if (
        $isNegative
        || $direction === 'S'
        || $direction === 'W'
    ) {
        $decimal *= -1;
    }

    return round($decimal, 6);
}


public function actionGetBoatDepartureById()
{
    Yii::$app->response->format =
        \yii\web\Response::FORMAT_JSON;

    $requestId = trim(
        (string) Yii::$app->request->get('id', '')
    );

    if ($requestId === '') {
        Yii::$app->response->statusCode = 400;

        return [
            'success' => false,
            'status' => 400,
            'message' => 'Bad request',
            'data' => (object) [],
            'errors' => 'Enter a departure request ID as the id parameter.',
        ];
    }

    try {
        $boat = (new \yii\db\Query())
            ->from('departure_requests')
            ->where([
                'id' => $requestId,
            ])
            ->one();

        if (empty($boat)) {
            Yii::$app->response->statusCode = 404;

            $this->saveApiRequestLog(
                null,
                null,
                null,
                404,
                'No departure record found for request ID: '
                    . $requestId
            );

            return [
                'success' => false,
                'status' => 404,
                'message' => 'No departure record found for this ID.',
                'data' => (object) [],
                'errors' => null,
            ];
        }

        $boatNumber = $boat['boat_no'] ?? null;

        /*
         * Get the boat registration information.
         */
        $boatInfo = null;

        if (!empty($boatNumber)) {
            $boatInfo = (new \yii\db\Query())
                ->from([
                    'bn' => 'boat_numbers',
                ])
                ->select([
                    'bn.*',
                    'district.name AS district_name',
                    'division.name AS division_name',
                ])
                ->leftJoin(
                    ['district' => 'm_fi_district'],
                    'district.id = bn.fisheries_district'
                )
                ->leftJoin(
                    ['division' => 'm_division'],
                    'division.id = bn.fisheries_division'
                )
                ->where([
                    'bn.boat_number' => $boatNumber,
                ])
                ->one();
        }

        /*
         * Get boat licence information.
         */
        $boatLicense = null;

        if (!empty($boatInfo['id'])) {
            $boatLicense = (new \yii\db\Query())
                ->from(
                    'fisherman_registerd_boat_license'
                )
                ->where([
                    'boat_number_id' => $boatInfo['id'],
                ])
                ->one();
        }

        /*
         * Get crew members for this departure request.
         */
        $crewRows = (new \yii\db\Query())
            ->select([
                'name',
                'nic',
                'mobile_number AS phone',
            ])
            ->from('deparure_request_crew')
            ->where([
                'request_id' => $boat['id'],
            ])
            ->all();

        $crew = [];

        foreach ($crewRows as $crewMember) {
            $crew[] = [
                'name' =>
                    $crewMember['name'] ?? null,

                'nic' =>
                    $crewMember['nic'] ?? null,

                'phone' =>
                    $crewMember['phone'] ?? null,
            ];
        }

        /*
         * Get skipper status/name and registered UID.
         */
        $skipperNic = trim(
            (string) (
                $boat['skipper_nic']
                ?? ''
            )
        );

        $skipper = null;
        $skipperProfile = null;

        if ($skipperNic !== '') {
            $skipper = (new \yii\db\Query())
                ->select([
                    'nic',
                    'skipper_name',
                    'status',
                ])
                ->from('dep_skipper')
                ->where([
                    'nic' => $skipperNic,
                ])
                ->one();

            $skipperProfile = (new \yii\db\Query())
                ->select([
                    'nic' => 'pf.nic',
                    'skipper_uid' =>
                        'skipper.skipper_uid',
                ])
                ->from([
                    'skipper' => 'skipper',
                ])
                ->innerJoin(
                    ['pf' => 'profile_fisherman'],
                    'pf.id = skipper.fisherman_id'
                )
                ->where([
                    'pf.nic' => $skipperNic,
                ])
                ->one();
        }

        $boatData = [
            /*
             * departure_requests information
             */
            'id' =>
                $boat['id'] ?? null,

            'boat_no' =>
                $boat['boat_no'] ?? null,

            'boat_name' =>
                $boat['boat_name'] ?? null,

            'owner' =>
                $boat['owner'] ?? null,

            'contact_no' =>
                $boat['contact_no'] ?? null,

            'email' =>
                $boat['email'] ?? null,

            'skipper' =>
                $boat['skipper'] ?? null,

            'skipper_no' =>
                $boat['skipper_no'] ?? null,

            'skipper_nic' =>
                $boat['skipper_nic'] ?? null,

            'district' =>
                $boat['district'] ?? null,

            'harbor' =>
                $boat['harbor'] ?? null,

            'fishing_area' =>
                $boat['fishing_area'] ?? null,

            'length_longline' =>
                $boat['length_longline'] ?? null,

            'length_gillnet' =>
                $boat['length_gillnet'] ?? null,

            'length_ringnet' =>
                $boat['length_ringnet'] ?? null,

            'longline_hooks' =>
                $boat['longline_hooks'] ?? null,

            'mesh_gillnet' =>
                $boat['mesh_gillnet'] ?? null,

            'mesh_ringnet' =>
                $boat['mesh_ringnet'] ?? null,

            'national_license_no' =>
                $boat['national_license_no']
                ?? null,

            'hs_license_no' =>
                $boat['hs_license_no'] ?? null,

            'vms' =>
                $boat['vms'] ?? null,

            'agree' =>
                $boat['agree'] ?? null,

            'action_date' =>
                $boat['action_date'] ?? null,

            'approve' =>
                $boat['approve'] ?? null,

            'remarks' =>
                $boat['remarks'] ?? null,

            'water_bottle' =>
                $boat['water_bot'] ?? null,

            'mcs' =>
                $boat['mcs'] ?? null,

            'frequency' =>
                $boat['frequency'] ?? null,

            'vms_code' =>
                $boat['vms_code'] ?? null,

            'manual' =>
                $boat['manual'] ?? null,

            'arrivalPort' =>
                $boat['arrivalPort'] ?? null,

            'arrivalDate' =>
                $boat['arrivalDate'] ?? null,

            'arrTime' =>
                $boat['arrTime'] ?? null,

            /*
             * Related information
             */
            'crew' => $crew,

            'skipper_uid' =>
                $skipperProfile['skipper_uid']
                ?? null,

            'skipper_name' =>
                $skipper['skipper_name']
                ?? null,

            'skipper_status' =>
                $skipper['status']
                ?? null,

            'call_sign_no' =>
                $boatLicense['call_sign_no']
                ?? null,

            'imo_no' =>
                $boatLicense['imo_no']
                ?? null,

            'district_info' =>
                !empty(
                    $boatInfo['district_name']
                )
                    ? [
                        'name' =>
                            $boatInfo[
                                'district_name'
                            ],
                    ]
                    : null,

            'division_info' =>
                !empty(
                    $boatInfo['division_name']
                )
                    ? [
                        'name' =>
                            $boatInfo[
                                'division_name'
                            ],
                    ]
                    : null,
        ];

        Yii::$app->response->statusCode = 200;

        $this->saveApiRequestLog(
            null,
            null,
            null,
            200,
            'Boat departure fetched successfully for request ID: '
                . $requestId
        );

        return [
            'success' => true,
            'status' => 200,
            'message' =>
                'Boat departure fetched successfully',
            'data' => $boatData,
            'errors' => null,
        ];
    } catch (\Throwable $e) {
        Yii::error(
            [
                'endpoint' =>
                    'get-boat-departure-by-id',

                'request_id' =>
                    $requestId,

                'message' =>
                    $e->getMessage(),

                'file' =>
                    $e->getFile(),

                'line' =>
                    $e->getLine(),
            ],
            'api'
        );

        Yii::$app->response->statusCode = 500;

        $this->saveApiRequestLog(
            null,
            null,
            null,
            500,
            'Server issue fetching departure for request ID: '
                . $requestId
        );

        return [
            'success' => false,
            'status' => 500,
            'message' =>
                'Server issue fetching departure',
            'data' => (object) [],
            'errors' =>
                'An internal server error occurred.',
        ];
    }
}

public function actionBoatDataByHarbour()
{
    Yii::$app->response->format =
        \yii\web\Response::FORMAT_JSON;

    $harbor = trim(
        (string) Yii::$app->request->get('harbor', '')
    );

    $date = trim(
        (string) Yii::$app->request->get('date', '')
    );

    if ($harbor === '') {
        Yii::$app->response->statusCode = 400;

        return [
            'success' => false,
            'status_code' => 400,
            'message' => 'The harbor parameter is required.',
            'harbor' => null,
            'data' => null,
        ];
    }

    // Validate date format if provided (expects YYYY-MM-DD)
    if ($date !== '' && !\DateTime::createFromFormat('Y-m-d', $date)) {
        Yii::$app->response->statusCode = 400;

        return [
            'success' => false,
            'status_code' => 400,
            'message' => 'The date parameter must be in YYYY-MM-DD format.',
            'harbor' => $harbor,
            'data' => null,
        ];
    }

    try {
        $query = \backend\models\DepartureRequests::find()
            ->where([
                'harbor' => $harbor,
            ]);

        if ($date !== '') {
            $startOfDay = $date . ' 00:00:00';
            $endOfDay = $date . ' 23:59:59.999999';

            $query->andWhere([
                'between',
                'action_date',
                $startOfDay,
                $endOfDay,
            ]);
        }

        $departures = $query
            ->orderBy([
                'req_date_time' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->asArray()
            ->all();

        if (empty($departures)) {
            Yii::$app->response->statusCode = 404;

            return [
                'success' => false,
                'status_code' => 404,
                'message' =>
                    'No departure records were found for the given harbor'
                    . ($date !== '' ? ' and date.' : '.'),
                'harbor' => $harbor,
                'data' => null,
            ];
        }

        /*
         * Get all request IDs for loading crew members
         * using one database query.
         */
        $requestIds = array_column(
            $departures,
            'id'
        );

        $crewRows = DeparureRequestCrew::find()
            ->select([
                'request_id',
                'name',
                'nic',
                'mobile_number',
            ])
            ->where([
                'request_id' => $requestIds,
            ])
            ->asArray()
            ->all();

        
        $crewByRequest = [];

        foreach ($crewRows as $crewMember) {
            $requestId =
                $crewMember['request_id'];

            $crewByRequest[$requestId][] = [
                'name' =>
                    $crewMember['name'] ?? null,

                'nic' =>
                    $crewMember['nic'] ?? null,

                'phone' =>
                    $crewMember['mobile_number'] ?? null,
            ];
        }

        $data = [];

        foreach ($departures as $departure) {
            $requestId = $departure['id'] ?? null;

            $data[] = [
                'boat_number' =>
                    $departure['boat_no'] ?? null,
                'owner_name' =>
                    $departure['owner'] ?? null,
                'owner_phone' =>
                    $departure['contact_no'] ?? null,
                'harbor' =>
                    $departure['harbor'] ?? null,
                'skipper_name' =>
                    $departure['skipper'] ?? null,
                'skipper_no' =>
                    $departure['skipper_no'] ?? null,
                'skipper_nic' =>
                    $departure['skipper_nic'] ?? null,
                'action_date' =>
                    $departure['action_date'] ?? null,
                'crew' =>
                    $crewByRequest[$requestId]
                    ?? [],
            ];
        }

        Yii::$app->response->statusCode = 200;

        return [
            'success' => true,
            'status_code' => 200,
            'message' =>
                'Boat departure information fetched successfully.',
            'harbor' => $harbor,
            'date' => $date !== '' ? $date : null,
            'data' => $data,
        ];
    } catch (\Throwable $e) {
        Yii::error(
            [
                'endpoint' =>
                    'owner-nic-by-harbor',
                'harbor' => $harbor,
                'date' => $date,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ],
            'api'
        );

        Yii::$app->response->statusCode = 500;

        return [
            'success' => false,
            'status_code' => 500,
            'message' =>
                'A server issue occurred while fetching boat departure information.',
            'harbor' => $harbor,
            'data' => null,
        ];
    }
}
}