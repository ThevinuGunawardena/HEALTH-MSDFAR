<?php

namespace backend\controllers;
use backend\config\Constant;
use backend\models\BoatNumbers;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\HighseasLicense;
use backend\models\MFiDistrict;
use backend\models\NationalLicense;
use backend\models\ProfileFisherman;
use backend\models\ProgressItems;
use backend\models\DepartureBoats;
use backend\models\ProgressItemsSearch;
use backend\models\Skipper;
use backend\models\User;
use DateTime;
use kartik\mpdf\Pdf;
use yii;
use yii\db\Expression;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ProgressItemsController implements the CRUD actions for ProgressItems model.
 */
class ProgressItemsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all ProgressItems models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ProgressItemsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProgressItems model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProgressItems model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ProgressItems();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProgressItems model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProgressItems model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProgressItems model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ProgressItems the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProgressItems::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

//   public static function getStacs(
//     $fromDate = null,
//     $toDate = null,
//     $districtId = null,
//     $divisionId = null
// ) {
//     /*
//      * Replace these with the actual active values
//      * stored in each table.
//      */
//     $fishermanActiveStatus = Constant::Active;
//     $boatNumberActiveStatus = Constant::Active;
//     $boatRegistrationActiveStatus = Constant::Active;
//     $highSeasActiveStatus = Constant::Active;
//     $nationalLicenseActiveStatus = Constant::Active;

//     $fromDateTime = null;
//     $toDateTime = null;

//     if (!empty($fromDate) && strtotime($fromDate) !== false) {
//         $fromDateTime = date(
//             'Y-m-d 00:00:00',
//             strtotime($fromDate)
//         );
//     }

//     if (!empty($toDate) && strtotime($toDate) !== false) {
//         $toDateTime = date(
//             'Y-m-d 00:00:00',
//             strtotime($toDate . ' +1 day')
//         );
//     }

//    /*
//  * 1. Fisherman registration
//  *
//  * Counts all records from profile_fisherman.
//  * District, division, and date filters are applied
//  * when filter values are selected.
//  */
// $fishermanQuery = ProfileFisherman::find()
//     ->alias('pf');

// /*
//  * District filter.
//  */
// if (!empty($districtId)) {
//     $fishermanQuery->andWhere([
//         'pf.district' => $districtId,
//     ]);
// }

// /*
//  * Division filter.
//  */
// if (!empty($divisionId)) {
//     $fishermanQuery->andWhere([
//         'pf.division' => $divisionId,
//     ]);
// }

// /*
//  * From-date filter.
//  */
// if ($fromDateTime !== null) {
//     $fishermanQuery->andWhere([
//         '>=',
//         'pf.created',
//         $fromDateTime,
//     ]);
// }

// /*
//  * To-date filter.
//  */
// if ($toDateTime !== null) {
//     $fishermanQuery->andWhere([
//         '<',
//         'pf.created',
//         $toDateTime,
//     ]);
// }

// $fishermanRegistrationCount =
//     (int) $fishermanQuery->count();
//     /*
//      * 2. Boat number issuing
//      */
//     $boatNumberQuery = BoatNumbers::find()
//         ->alias('bn')
//         ->where([
//             'bn.status' => $boatNumberActiveStatus,
//         ]);

//     if (!empty($districtId)) {
//         $boatNumberQuery->andWhere([
//             'bn.fisheries_district' => $districtId,
//         ]);
//     }

//     if (!empty($divisionId)) {
//         $boatNumberQuery->andWhere([
//             'bn.fisheries_division' => $divisionId,
//         ]);
//     }

//     if ($fromDateTime !== null) {
//         $boatNumberQuery->andWhere([
//             '>=',
//             'bn.created',
//             $fromDateTime,
//         ]);
//     }

//     if ($toDateTime !== null) {
//         $boatNumberQuery->andWhere([
//             '<',
//             'bn.created',
//             $toDateTime,
//         ]);
//     }

//     $boatNumberIssuingCount =
//         (int) $boatNumberQuery->count();


// /*
//  * 3 and 4. Boat registration and renewal counts by boat type
//  *
//  * fisherman_registerd_boat_license.boat_number_id = boat_numbers.id
//  * boat_numbers.boat_type = m_boat_types.id
//  */

// /*
//  * Get type-wise counts.
//  */
// $getBoatCountsByType = function (?int $renew = null) use (
//     $boatRegistrationActiveStatus,
//     $districtId,
//     $divisionId,
//     $fromDateTime,
//     $toDateTime
// ) {
//     $countQuery = (new \yii\db\Query())
//         ->select([
//             'boat_type_id' => 'bn.boat_type',
//             'total_count' => new \yii\db\Expression(
//                 'COUNT(br.nid)'
//             ),
//         ])
//         ->from([
//             'br' => FishermanRegisterdBoatLicense::tableName(),
//         ])
//         ->leftJoin(
//             ['bn' => 'boat_numbers'],
//             'bn.id = br.boat_number_id'
//         )
//         ->where([
//             'br.status' => 101,
//         ]);

//     if ($renew !== null) {
//         $countQuery->andWhere([
//             'br.renew' => $renew,
//         ]);
//     }

//     if (!empty($districtId)) {
//         $countQuery->andWhere([
//             'br.district' => $districtId,
//         ]);
//     }

//     if (!empty($divisionId)) {
//         $countQuery->andWhere([
//             'br.division' => $divisionId,
//         ]);
//     }

//     if ($fromDateTime !== null) {
//         $countQuery->andWhere([
//             '>=',
//             'br.created',
//             $fromDateTime,
//         ]);
//     }

//     if ($toDateTime !== null) {
//         $countQuery->andWhere([
//             '<',
//             'br.created',
//             $toDateTime,
//         ]);
//     }

//     $countQuery->groupBy([
//         'bn.boat_type',
//     ]);

//     return (new \yii\db\Query())
//         ->select([
//             'boat_type_id' => 'bt.id',
//             'boat_type_code' => 'bt.code',
//             'boat_type_description' => 'bt.description',
//             'total_count' => new \yii\db\Expression(
//                 'COALESCE(bc.total_count, 0)'
//             ),
//         ])
//         ->from([
//             'bt' => 'm_boat_types',
//         ])
//         ->leftJoin(
//             ['bc' => $countQuery],
//             'bc.boat_type_id = bt.id'
//         )
//         ->where([
//             'bt.status' => 1,
//         ])
//         ->orderBy([
//             'bt.id' => SORT_ASC,
//         ])
//         ->all();
// };


// /*
//  * Get the overall total directly from
//  * fisherman_registerd_boat_license.
//  *
//  * This prevents records from being excluded because of
//  * boat type relationships.
//  */
// $getBoatTotalCount = function (?int $renew = null) use (
//     $boatRegistrationActiveStatus,
//     $districtId,
//     $divisionId,
//     $fromDateTime,
//     $toDateTime
// ) {
//     $query = FishermanRegisterdBoatLicense::find()
//         ->alias('br')
//         ->where([
//             'br.status' => $boatRegistrationActiveStatus,
//         ]);

//     /*
//      * Apply the renewal filter only when requested.
//      * null = all registrations; 1 = renewals only.
//      */
//     if ($renew !== null) {
//         $query->andWhere([
//             'br.renew' => $renew,
//         ]);
//     }

//     /*
//      * District filter.
//      */
//     if (!empty($districtId)) {
//         $query->andWhere([
//             'br.district' => $districtId,
//         ]);
//     }

//     /*
//      * Division filter.
//      */
//     if (!empty($divisionId)) {
//         $query->andWhere([
//             'br.division' => $divisionId,
//         ]);
//     }

//     /*
//      * From-date filter.
//      */
//     if ($fromDateTime !== null) {
//         $query->andWhere([
//             '>=',
//             'br.created',
//             $fromDateTime,
//         ]);
//     }

//     /*
//      * To-date filter.
//      */
//     if ($toDateTime !== null) {
//         $query->andWhere([
//             '<',
//             'br.created',
//             $toDateTime,
//         ]);
//     }

//     return (int) $query->count();
// };


// /*
//  * All active boat registrations by type.
//  * Includes both original registrations and renewals.
//  */
// $boatRegistrationByType = $getBoatCountsByType(null);

// /*
//  * renew = 1: Boat registration renewals by type.
//  */
// $boatRegistrationRenewByType = $getBoatCountsByType(1);


// /*
//  * Overall boat registration total.
//  * Includes every status 101 record matching the filters.
//  */
// $boatRegistrationCount = $getBoatTotalCount(null);

// $boatRegistrationRenewCount = $getBoatTotalCount(1);
//         /*
//         * 5. High Seas licence
//         */
//         $highSeasLicenseQuery = HighseasLicense::find()
//             ->alias('hs')
//             ->where([
//                 'hs.status' => $highSeasActiveStatus,
//             ]);

//         if (!empty($districtId)) {
//             $highSeasLicenseQuery->andWhere([
//                 'hs.district' => $districtId,
//             ]);
//         }

//         if (!empty($divisionId)) {
//             $highSeasLicenseQuery->andWhere([
//                 'hs.division' => $divisionId,
//             ]);
//         }

//         if ($fromDateTime !== null) {
//             $highSeasLicenseQuery->andWhere([
//                 '>=',
//                 'hs.created',
//                 $fromDateTime,
//             ]);
//         }

//         if ($toDateTime !== null) {
//             $highSeasLicenseQuery->andWhere([
//                 '<',
//                 'hs.created',
//                 $toDateTime,
//             ]);
//         }

//         $highSeasLicenseIssuingCount =
//             (int) $highSeasLicenseQuery->count();


//     /*
//      * 6. National licence
//      */
//     /*
//  * National licence issuing count by boat type
//  *
//  * nl.boat_registration_id = db.id
//  * db.boat_number_id = bn.id
//  * bn.boat_type = bt.id
//  */
// $nationalLicenseCountQuery = (new \yii\db\Query())
//     ->select([
//         'boat_type_id' => 'bn.boat_type',
//         'total_count' => new \yii\db\Expression(
//             'COUNT(DISTINCT nl.id)'
//         ),
//     ])
//     ->from([
//         'nl' => NationalLicense::tableName(),
//     ])
//     ->innerJoin(
//         ['db' => DepartureBoats::tableName()],
//         'db.id = nl.boat_registration_id'
//     )
//     ->innerJoin(
//         ['bn' => 'boat_numbers'],
//         'bn.id = db.boat_number_id'
//     )
//     ->where([
//         'nl.status' => $nationalLicenseActiveStatus,
//     ]);

// /*
//  * District filter
//  */
// if (!empty($districtId)) {
//     $nationalLicenseCountQuery->andWhere([
//         'nl.fisheries_district' => $districtId,
//     ]);
// }

// /*
//  * Division filter
//  */
// if (!empty($divisionId)) {
//     $nationalLicenseCountQuery->andWhere([
//         'nl.division' => $divisionId,
//     ]);
// }

// /*
//  * From-date filter
//  */
// if ($fromDateTime !== null) {
//     $nationalLicenseCountQuery->andWhere([
//         '>=',
//         'nl.created',
//         $fromDateTime,
//     ]);
// }

// /*
//  * To-date filter
//  */
// if ($toDateTime !== null) {
//     $nationalLicenseCountQuery->andWhere([
//         '<',
//         'nl.created',
//         $toDateTime,
//     ]);
// }

// /*
//  * Group national licence records by boat type.
//  */
// $nationalLicenseCountQuery->groupBy([
//     'bn.boat_type',
// ]);

// /*
//  * Get all active boat types.
//  *
//  * Boat types without national licences will display 0.
//  */
// $nationalLicenseByType = (new \yii\db\Query())
//     ->select([
//         'boat_type_id' => 'bt.id',
//         'boat_type_code' => 'bt.code',
//         'boat_type_description' => 'bt.description',
//         'total_count' => new \yii\db\Expression(
//             'COALESCE(nlc.total_count, 0)'
//         ),
//     ])
//     ->from([
//         'bt' => 'm_boat_types',
//     ])
//     ->leftJoin(
//         ['nlc' => $nationalLicenseCountQuery],
//         'nlc.boat_type_id = bt.id'
//     )
//     ->where([
//         'bt.status' => 1,
//     ])
//     ->orderBy([
//         'bt.id' => SORT_ASC,
//     ])
//     ->all();

// /*
//  * Calculate total national licence count.
//  */
// $nationalLicenseIssuingCount = 0;

// foreach ($nationalLicenseByType as $boatType) {
//     $nationalLicenseIssuingCount +=
//         (int) $boatType['total_count'];
// }

//     $totalCount =
//         $fishermanRegistrationCount +
//         $boatNumberIssuingCount +
//         $boatRegistrationCount +
//         $highSeasLicenseIssuingCount +
//         $nationalLicenseIssuingCount;

//     return [
//         'fishermanRegistration' =>
//             $fishermanRegistrationCount,
            

//         'boatNumberIssuing' =>
//             $boatNumberIssuingCount,

//         'boatRegistration' => $boatRegistrationCount,

//     'boatRegistrationRenew' =>
//         $boatRegistrationRenewCount,

//     'boatRegistrationByType' =>
//         $boatRegistrationByType,

//     'boatRegistrationRenewByType' =>
//         $boatRegistrationRenewByType,

//         'highSeasLicenseIssuing' =>
//             $highSeasLicenseIssuingCount,

//        'nationalLicenseIssuing' =>
//         $nationalLicenseIssuingCount,

//     'nationalLicenseByType' =>
//         $nationalLicenseByType,
//         'total' =>
//             $totalCount,
//     ];
// }


public static function getStacs(
    $fromDate = null,
    $toDate = null,
    $districtId = null,
    $divisionId = null
) {
    /*
     * Temporary dashboard data taken from:
     * Boat_License_fishermen_2026_07_20.xlsx
     *
     * Only the district filter is applied.
     * Date and division filters are intentionally ignored.
     */

    $boatTypeCodes = [
        'IMUL',
        'OFRP',
        'MTRB',
        'IDAY',
        'NTRB',
        'NBSB',
    ];

    /*
     * District IDs must match the IDs sent by the
     * dashboard district dropdown.
     */
    $dashboardData = [
        4 => [
            'district_name' => 'BATTICALOA',
            'fishermen' => 6041,
            'registrations' => [
                463,
                2109,
                10,
                9,
                4082,
                53,
            ],
            'renewals' => [
                436,
                1850,
                10,
                6,
                3788,
                60,
            ],
            'high_seas' => 0,
            'national_licences' => [
                409,
                1560,
                10,
                6,
                3494,
                67,
            ],
        ],

        6 => [
            'district_name' => 'CHILAW',
            'fishermen' => 4947,
            'registrations' => [
                425,
                1816,
                66,
                1,
                1321,
                20,
            ],
            'renewals' => [
                377,
                1652,
                51,
                0,
                965,
                10,
            ],
            'high_seas' => 332,
            'national_licences' => [
                331,
                1490,
                34,
                0,
                611,
                0,
            ],
        ],

        5 => [
            'district_name' => 'COLOMBO',
            'fishermen' => 866,
            'registrations' => [
                19,
                348,
                2,
                12,
                178,
                39,
            ],
            'renewals' => [
                12,
                344,
                2,
                8,
                171,
                38,
            ],
            'high_seas' => 12,
            'national_licences' => [
                12,
                342,
                2,
                8,
                171,
                37,
            ],
        ],

        7 => [
            'district_name' => 'GALLE',
            'fishermen' => 6977,
            'registrations' => [
                1185,
                633,
                240,
                55,
                275,
                54,
            ],
            'renewals' => [
                1001,
                601,
                233,
                45,
                235,
                52,
            ],
            'high_seas' => 877,
            'national_licences' => [
                986,
                571,
                227,
                38,
                199,
                52,
            ],
        ],

        8 => [
            'district_name' => 'JAFFNA',
            'fishermen' => 6737,
            'registrations' => [
                83,
                6167,
                1147,
                447,
                1705,
                77,
            ],
            'renewals' => [
                61,
                5801,
                1084,
                417,
                1480,
                71,
            ],
            'high_seas' => 2,
            'national_licences' => [
                61,
                5463,
                1021,
                388,
                1257,
                67,
            ],
        ],

        11 => [
            'district_name' => 'KALMUNAI',
            'fishermen' => 2227,
            'registrations' => [
                240,
                1119,
                182,
                37,
                1112,
                190,
            ],
            'renewals' => [
                225,
                1001,
                157,
                33,
                925,
                147,
            ],
            'high_seas' => 2,
            'national_licences' => [
                210,
                899,
                132,
                29,
                738,
                102,
            ],
        ],

        10 => [
            'district_name' => 'KALUTHARA',
            'fishermen' => 3539,
            'registrations' => [
                409,
                485,
                0,
                8,
                311,
                47,
            ],
            'renewals' => [
                380,
                466,
                0,
                8,
                281,
                45,
            ],
            'high_seas' => 268,
            'national_licences' => [
                369,
                447,
                0,
                8,
                252,
                47,
            ],
        ],

        9 => [
            'district_name' => 'KILINOCHCHI',
            'fishermen' => 1866,
            'registrations' => [
                1,
                1241,
                184,
                0,
                250,
                0,
            ],
            'renewals' => [
                0,
                1140,
                172,
                0,
                240,
                0,
            ],
            'high_seas' => 0,
            'national_licences' => [
                0,
                1050,
                160,
                0,
                230,
                0,
            ],
        ],

        13 => [
            'district_name' => 'MANNAR',
            'fishermen' => 2207,
            'registrations' => [
                25,
                3621,
                726,
                190,
                551,
                15,
            ],
            'renewals' => [
                11,
                3198,
                682,
                157,
                465,
                13,
            ],
            'high_seas' => 0,
            'national_licences' => [
                11,
                2775,
                638,
                127,
                380,
                11,
            ],
        ],

        14 => [
            'district_name' => 'MATARA',
            'fishermen' => 8478,
            'registrations' => [
                1246,
                904,
                291,
                178,
                481,
                3,
            ],
            'renewals' => [
                1190,
                840,
                249,
                175,
                404,
                3,
            ],
            'high_seas' => 817,
            'national_licences' => [
                1145,
                783,
                207,
                173,
                329,
                3,
            ],
        ],

        12 => [
            'district_name' => 'MULLEITHIVU',
            'fishermen' => 149,
            'registrations' => [
                2,
                1730,
                3,
                0,
                455,
                48,
            ],
            'renewals' => [
                2,
                1621,
                2,
                0,
                262,
                35,
            ],
            'high_seas' => 0,
            'national_licences' => [
                2,
                1513,
                1,
                0,
                72,
                23,
            ],
        ],

        15 => [
            'district_name' => 'NEGOMBO',
            'fishermen' => 4093,
            'registrations' => [
                501,
                1902,
                6,
                86,
                1797,
                47,
            ],
            'renewals' => [
                470,
                1771,
                6,
                84,
                1488,
                44,
            ],
            'high_seas' => 151,
            'national_licences' => [
                449,
                1652,
                6,
                82,
                1174,
                42,
            ],
        ],

        16 => [
            'district_name' => 'PUTTALAM',
            'fishermen' => 1922,
            'registrations' => [
                59,
                3343,
                274,
                1,
                1471,
                216,
            ],
            'renewals' => [
                27,
                2398,
                258,
                0,
                1208,
                110,
            ],
            'high_seas' => 26,
            'national_licences' => [
                25,
                2534,
                244,
                0,
                947,
                7,
            ],
        ],

        19 => [
            'district_name' => 'TANGALLE',
            'fishermen' => 5751,
            'registrations' => [
                741,
                1030,
                136,
                57,
                648,
                83,
            ],
            'renewals' => [
                691,
                930,
                125,
                54,
                544,
                78,
            ],
            'high_seas' => 586,
            'national_licences' => [
                646,
                929,
                114,
                51,
                436,
                75,
            ],
        ],

        17 => [
            'district_name' => 'TRINCOMALEE',
            'fishermen' => 8080,
            'registrations' => [
                147,
                3543,
                75,
                11,
                2437,
                163,
            ],
            'renewals' => [
                145,
                3451,
                73,
                8,
                2928,
                159,
            ],
            'high_seas' => 14,
            'national_licences' => [
                145,
                3434,
                71,
                8,
                3422,
                159,
            ],
        ],
    ];

    /*
     * Keep district rows in database-ID order.
     * The IDs are intentionally non-sequential.
     */
    ksort($dashboardData, SORT_NUMERIC);

    /*
     * Apply only the district filter.
     *
     * Empty, null, or 0 means all districts.
     */

    $selectedDistrictId = (int) ($districtId ?? 0);

    if ($selectedDistrictId > 0) {
        if (
            array_key_exists(
                $selectedDistrictId,
                $dashboardData
            )
        ) {
            $selectedRows = [
                $selectedDistrictId =>
                    $dashboardData[$selectedDistrictId],
            ];
        } else {
            /*
             * An unknown district ID returns zero values.
             */
            $selectedRows = [];
        }
    } else {
        /*
         * No district selected: use all districts.
         */
        $selectedRows = $dashboardData;
    }

    /*
     * Initialise totals.
     */
    $fishermanRegistrationCount = 0;
    $boatRegistrationCount = 0;
    $boatRegistrationRenewCount = 0;
    $highSeasLicenseIssuingCount = 0;
    $nationalLicenseIssuingCount = 0;

    $registrationTypeTotals = array_fill(
        0,
        count($boatTypeCodes),
        0
    );

    $renewalTypeTotals = array_fill(
        0,
        count($boatTypeCodes),
        0
    );

    $nationalLicenseTypeTotals = array_fill(
        0,
        count($boatTypeCodes),
        0
    );

    $districtStats = [];
    $boatLicenseDistrictTypeStats = [];

    foreach ($selectedRows as $id => $row) {
        $districtRegistrationTotal =
            array_sum($row['registrations']);

        $districtRenewalTotal =
            array_sum($row['renewals']);

        $districtNationalLicenseTotal =
            array_sum($row['national_licences']);

        $fishermanRegistrationCount +=
            (int) $row['fishermen'];

        $boatRegistrationCount +=
            $districtRegistrationTotal;

        $boatRegistrationRenewCount +=
            $districtRenewalTotal;

        $highSeasLicenseIssuingCount +=
            (int) $row['high_seas'];

        $nationalLicenseIssuingCount +=
            $districtNationalLicenseTotal;

        /*
         * Add boat-type totals.
         */
        foreach ($boatTypeCodes as $index => $boatTypeCode) {
            $registrationCount =
                (int) ($row['registrations'][$index] ?? 0);

            $renewalCount =
                (int) ($row['renewals'][$index] ?? 0);

            $nationalLicenseCount =
                (int) (
                    $row['national_licences'][$index]
                    ?? 0
                );

            $registrationTypeTotals[$index] +=
                $registrationCount;

            $renewalTypeTotals[$index] +=
                $renewalCount;

            $nationalLicenseTypeTotals[$index] +=
                $nationalLicenseCount;

            /*
             * Existing district/boat-type chart data.
             */
            $boatLicenseDistrictTypeStats[] = [
                'district_id' => $id,
                'district_name' =>
                    $row['district_name'],
                'boat_type_name' =>
                    $boatTypeCode,
                'count' =>
                    $registrationCount,
            ];
        }

        $districtStats[] = [
            'district_id' => $id,
            'district_name' =>
                $row['district_name'],

            'fishermanRegistration' =>
                (int) $row['fishermen'],

            'boatRegistration' =>
                $districtRegistrationTotal,

            'boatRegistrationRenew' =>
                $districtRenewalTotal,

            'highSeasLicenseIssuing' =>
                (int) $row['high_seas'],

            'nationalLicenseIssuing' =>
                $districtNationalLicenseTotal,
        ];
    }

    /*
     * Convert type totals into the structure
     * expected by the existing view.
     */
    $boatRegistrationByType = [];
    $boatRegistrationRenewByType = [];
    $nationalLicenseByType = [];

    foreach ($boatTypeCodes as $index => $boatTypeCode) {
        $boatRegistrationByType[] = [
            'boat_type_id' => $index + 1,
            'boat_type_code' => $boatTypeCode,
            'boat_type_description' =>
                $boatTypeCode,
            'total_count' =>
                $registrationTypeTotals[$index],
        ];

        $boatRegistrationRenewByType[] = [
            'boat_type_id' => $index + 1,
            'boat_type_code' => $boatTypeCode,
            'boat_type_description' =>
                $boatTypeCode,
            'total_count' =>
                $renewalTypeTotals[$index],
        ];

        $nationalLicenseByType[] = [
            'boat_type_id' => $index + 1,
            'boat_type_code' => $boatTypeCode,
            'boat_type_description' =>
                $boatTypeCode,
            'total_count' =>
                $nationalLicenseTypeTotals[$index],
        ];
    }

    /*
     * Overall dashboard total.
     */
    $totalCount =
        $fishermanRegistrationCount +
        $boatRegistrationCount +
        $boatRegistrationRenewCount +
        $highSeasLicenseIssuingCount +
        $nationalLicenseIssuingCount;

    return [
        'fishermanRegistration' =>
            $fishermanRegistrationCount,

        /*
         * No boat-number issuing value is available
         * in the supplied Excel file.
         */
        'boatNumberIssuing' => 0,

        'boatRegistration' =>
            $boatRegistrationCount,

        'boatRegistrationRenew' =>
            $boatRegistrationRenewCount,

        'boatRegistrationByType' =>
            $boatRegistrationByType,

        'boatRegistrationRenewByType' =>
            $boatRegistrationRenewByType,

        'highSeasLicenseIssuing' =>
            $highSeasLicenseIssuingCount,

        'nationalLicenseIssuing' =>
            $nationalLicenseIssuingCount,

        'nationalLicenseByType' =>
            $nationalLicenseByType,

        'districtStats' =>
            $districtStats,

        'boatLicenseDistrictTypeStats' =>
            $boatLicenseDistrictTypeStats,

        'total' =>
            $totalCount,
    ];
}

public static function getFishermanDistrictStats(
    $fromDate = null,
    $toDate = null
) {
    $query = ProfileFisherman::find()
        ->alias('pf')
        ->select([
            'district_id' => 'pf.district',
            'fisherman_count' => new Expression('COUNT(*)'),
        ])
        ->where([
            'pf.status' => Constant::Active,
        ]);

    if (!empty($fromDate) && strtotime($fromDate) !== false) {
        $query->andWhere([
            '>=',
            'pf.created',
            date('Y-m-d 00:00:00', strtotime($fromDate)),
        ]);
    }

    if (!empty($toDate) && strtotime($toDate) !== false) {
        $query->andWhere([
            '<',
            'pf.created',
            date(
                'Y-m-d 00:00:00',
                strtotime($toDate . ' +1 day')
            ),
        ]);
    }

    $countRows = $query
        ->groupBy(['pf.district'])
        ->asArray()
        ->all();

    $countMap = [];

    foreach ($countRows as $row) {
        $countMap[(int) $row['district_id']] =
            (int) $row['fisherman_count'];
    }

    $districts = MFiDistrict::find()
        ->select([
            'id',
            'name',
        ])
        ->orderBy([
            'name' => SORT_ASC,
        ])
        ->asArray()
        ->all();

    $districtStats = [];

    foreach ($districts as $district) {
        $districtId = (int) $district['id'];

        $districtStats[] = [
            'id' => $districtId,
            'name' => $district['name'],
            'count' => $countMap[$districtId] ?? 0,
        ];
    }

    usort(
        $districtStats,
        static function ($first, $second) {
            return $second['count'] <=> $first['count'];
        }
    );

    return $districtStats;
}


private function getBoatLicensesByDistrict(
    $boatRegistrationActiveStatus,
    $districtId = null,
    $divisionId = null,
    $fromDateTime = null,
    $toDateTime = null
): array {
    $query = FishermanRegisterdBoatLicense::find()
        ->alias('br')
        ->select([
            'district_id' => 'br.district',
            'district_name' => new \yii\db\Expression(
                "COALESCE(d.name, 'Unknown District')"
            ),
            'total' => new \yii\db\Expression(
                'COUNT(DISTINCT br.id)'
            ),
        ])
        ->leftJoin(
            ['d' => '{{%m_fi_district}}'],
            'd.id = br.district'
        )
        ->where([
            'br.status' => $boatRegistrationActiveStatus,
        ]);

    if (!empty($districtId)) {
        $query->andWhere([
            'br.district' => $districtId,
        ]);
    }

    if (!empty($divisionId)) {
        $query->andWhere([
            'br.division' => $divisionId,
        ]);
    }

    if ($fromDateTime !== null) {
        $query->andWhere([
            '>=',
            'br.created',
            $fromDateTime,
        ]);
    }

    if ($toDateTime !== null) {
        $query->andWhere([
            '<',
            'br.created',
            $toDateTime,
        ]);
    }

    return $query
        ->groupBy([
            'br.district',
            'd.name',
        ])
        ->orderBy([
            'total' => SORT_DESC,
        ])
        ->asArray()
        ->all();
}

private function getBoatLicensesByBoatType(
    $boatRegistrationActiveStatus,
    $districtId = null,
    $divisionId = null,
    $fromDateTime = null,
    $toDateTime = null
): array {
    $query = FishermanRegisterdBoatLicense::find()
        ->alias('br')
        ->select([
            'boat_type_id' => 'bn.boat_type',
            'boat_type_name' => new \yii\db\Expression(
                "COALESCE(bt.code, 'Unknown Boat Type')"
            ),
            'total' => new \yii\db\Expression(
                'COUNT(DISTINCT br.id)'
            ),
        ])
        ->innerJoin(
            ['bn' => BoatNumbers::tableName()],
            'bn.id = br.boat_number_id'
        )
        ->leftJoin(
            ['bt' => '{{%m_boat_types}}'],
            'bt.id = bn.boat_type'
        )
        ->where([
            'br.status' => $boatRegistrationActiveStatus,
        ]);

    if (!empty($districtId)) {
        $query->andWhere([
            'br.district' => $districtId,
        ]);
    }

    if (!empty($divisionId)) {
        $query->andWhere([
            'br.division' => $divisionId,
        ]);
    }

    if ($fromDateTime !== null) {
        $query->andWhere([
            '>=',
            'br.created',
            $fromDateTime,
        ]);
    }

    if ($toDateTime !== null) {
        $query->andWhere([
            '<',
            'br.created',
            $toDateTime,
        ]);
    }

    return $query
        ->groupBy([
            'bn.boat_type',
            'bt.code',
        ])
        ->orderBy([
            'total' => SORT_DESC,
        ])
        ->asArray()
        ->all();
}

public static function getBoatLicenseDistrictBoatTypeStats(
    $fromDate = null,
    $toDate = null,
    $districtId = null,
    $divisionId = null
): array {
    $fromDateTime = null;
    $toDateTime = null;

    if (!empty($fromDate) && strtotime($fromDate) !== false) {
        $fromDateTime = date(
            'Y-m-d 00:00:00',
            strtotime($fromDate)
        );
    }

    if (!empty($toDate) && strtotime($toDate) !== false) {
        $toDateTime = date(
            'Y-m-d 00:00:00',
            strtotime($toDate . ' +1 day')
        );
    }

    $query = FishermanRegisterdBoatLicense::find()
        ->alias('br')
        ->select([
            'district_id' => 'br.district',

            'district_name' => new Expression(
                "COALESCE(
                    NULLIF(TRIM(d.name), ''),
                    CONCAT('District ', br.district)
                )"
            ),

            'boat_type_id' => 'bn.boat_type',

            'boat_type_name' => new Expression(
                "COALESCE(
                    NULLIF(TRIM(bt.code), ''),
                    'Unknown Boat Type'
                )"
            ),

            'count' => new Expression(
                'COUNT(DISTINCT br.id)'
            ),
        ])

        /*
         * Keep unmatched licence rows in the result as
         * "Unknown Boat Type" instead of dropping them.
         */
        ->leftJoin(
            ['bn' => BoatNumbers::tableName()],
            'bn.id = br.boat_number_id'
        )

        ->leftJoin(
            ['bt' => '{{%m_boat_types}}'],
            'bt.id = bn.boat_type'
        )

        ->leftJoin(
            ['d' => MFiDistrict::tableName()],
            'd.id = br.district'
        )

        /*
         * The chart displays active first-time registrations.
         * Remove br.renew => 0 when renewals should be included.
         */
        ->where([
            'br.status' => Constant::Active,
            'br.renew' => 0,
        ]);

    if (!empty($districtId)) {
        $query->andWhere([
            'br.district' => $districtId,
        ]);
    }

    if (!empty($divisionId)) {
        $query->andWhere([
            'br.division' => $divisionId,
        ]);
    }

    if ($fromDateTime !== null) {
        $query->andWhere([
            '>=',
            'br.created',
            $fromDateTime,
        ]);
    }

    if ($toDateTime !== null) {
        $query->andWhere([
            '<',
            'br.created',
            $toDateTime,
        ]);
    }

    return $query
        ->groupBy([
            'br.district',
            'd.name',
            'bn.boat_type',
            'bt.code',
        ])
        ->orderBy([
            'd.name' => SORT_ASC,
            'bt.code' => SORT_ASC,
        ])
        ->asArray()
        ->all();
}



public static function getDepartureCancelledBoatDistrictStats(
    $fromDate = null,
    $toDate = null,
    $districtId = null,
    $divisionId = null
): array {
    $fromDateTime = null;
    $toDateTime = null;

    if (
        !empty($fromDate) &&
        strtotime($fromDate) !== false
    ) {
        $fromDateTime = date(
            'Y-m-d 00:00:00',
            strtotime($fromDate)
        );
    }

    if (
        !empty($toDate) &&
        strtotime($toDate) !== false
    ) {
        $toDateTime = date(
            'Y-m-d 00:00:00',
            strtotime($toDate . ' +1 day')
        );
    }

    $query = DepartureBoats::find()
        ->alias('db')
        ->select([
            'district_id' => 'db.district',

            'district_name' => new Expression(
                "COALESCE(
                    NULLIF(TRIM(d.name), ''),
                    CONCAT('District ', db.district)
                )"
            ),

            /*
             * Count each cancelled boat only once.
             */
            'cancelled_boat_count' => new Expression(
                'COUNT(DISTINCT db.boat_number_id)'
            ),
        ])
        ->leftJoin(
            ['d' => MFiDistrict::tableName()],
            'd.id = db.district'
        )
        ->where([
            'or',

            /*
             * Compulsory service is pending.
             */
            [
                'db.compulsory_service' => 0,
            ],

            /*
             * Departure is not allowed because another
             * status has been assigned.
             */
            [
                'and',
                ['not', ['db.status' => null]],
                ['<>', 'db.status', ''],
                ['<>', 'db.status', 'Departure Allowed'],
            ],
        ]);

    if (!empty($districtId)) {
        $query->andWhere([
            'db.district' => $districtId,
        ]);
    }

    if (!empty($divisionId)) {
        $query->andWhere([
            'db.division' => $divisionId,
        ]);
    }

    if ($fromDateTime !== null) {
        $query->andWhere([
            '>=',
            'db.created',
            $fromDateTime,
        ]);
    }

    if ($toDateTime !== null) {
        $query->andWhere([
            '<',
            'db.created',
            $toDateTime,
        ]);
    }

    return $query
        ->groupBy([
            'db.district',
            'd.name',
        ])
        ->orderBy([
            'cancelled_boat_count' => SORT_DESC,
        ])
        ->asArray()
        ->all();
}
}