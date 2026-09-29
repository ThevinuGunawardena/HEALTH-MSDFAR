<?php

namespace backend\controllers;

use backend\components\Controller;
use backend\components\RecordLookupRateLimit;
use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ScientificData;
use backend\models\ScientificDataSearch;
use backend\models\ScientificEnumerationRequest;
use backend\models\ScientificEnumerationRequestSearch;
use backend\models\ScientificSamplingBoatGearData;
use backend\models\ScientificSamplingCatchData;
use backend\models\ScientificSamplingData;
use backend\models\ScientificSamplingLengthDetails;
use backend\models\ScientificSamplingOperationCost;
use backend\services\CommonService;
use backend\services\ScientificService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * ScientificController implements Scientific Data actions.
 *
 * Persistent records are addressed with model-bound SecurityHelper tokens.
 * Unsaved wizard crafts use client-generated opaque craft keys because no
 * database record exists yet for them.
 */
class ScientificController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
{
    return array_merge(
        parent::behaviors(),
        [
            /*
             * =====================================================
             * HTTP METHOD RESTRICTIONS
             * =====================================================
             */
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => [
                        'GET',
                        'POST',
                    ],

                    'view' => [
                        'GET',
                    ],

                    'create' => [
                        'GET',
                        'POST',
                    ],

                    'addsamplecraft' => [
                        'GET',
                    ],

                    'catchdata' => [
                        'GET',
                    ],

                    'addlengthweight' => [
                        'GET',
                    ],

                    'addoperationcost' => [
                        'GET',
                    ],

                    'viewboatgeaers' => [
                        'GET',
                    ],

                    'viewlengthweight' => [
                        'GET',
                    ],

                    'viewcatch' => [
                        'GET',
                    ],

                    'viewoperationcost' => [
                        'GET',
                    ],

                    'approve' => [
                        'POST',
                    ],

                    'reject' => [
                        'POST',
                    ],

                    'get-report' => [
                        'GET',
                    ],

                    'update' => [
                        'GET',
                        'POST',
                    ],

                    'delete' => [
                        'POST',
                    ],
                ],
            ],

            /*
             * =====================================================
             * NORMAL SCIENTIFIC PAGE RATE LIMIT
             *
             * 120 requests / minute
             * =====================================================
             */
            'scientificPageRateLimit' => [
                'class' =>
                    RecordLookupRateLimit::class,

                'only' => [
                    'index',
                    'create',
                    'addsamplecraft',
                    'catchdata',
                    'addlengthweight',
                    'addoperationcost',
                ],

                'limit' =>
                    120,

                'window' =>
                    60,

                'bucketName' =>
                    'scientific-page-short',
            ],

            /*
             * =====================================================
             * NORMAL SCIENTIFIC PAGE LONG WINDOW
             *
             * 600 requests / 15 minutes
             * =====================================================
             */
            'scientificPageLongRateLimit' => [
                'class' =>
                    RecordLookupRateLimit::class,

                'only' => [
                    'index',
                    'create',
                    'addsamplecraft',
                    'catchdata',
                    'addlengthweight',
                    'addoperationcost',
                ],

                'limit' =>
                    600,

                'window' =>
                    900,

                'bucketName' =>
                    'scientific-page-long',
            ],

            /*
             * =====================================================
             * PROTECTED RECORD LOOKUP RATE LIMIT
             *
             * 30 requests / minute
             * =====================================================
             */
            'scientificRecordRateLimit' => [
                'class' =>
                    RecordLookupRateLimit::class,

                'only' => [
                    'view',
                    'viewboatgeaers',
                    'viewlengthweight',
                    'viewcatch',
                    'viewoperationcost',
                    'get-report',
                    'approve',
                    'reject',
                    'update',
                    'delete',
                ],

                'limit' =>
                    30,

                'window' =>
                    60,

                'bucketName' =>
                    'scientific-record-short',
            ],

            /*
             * =====================================================
             * PROTECTED RECORD LONG WINDOW
             *
             * 200 requests / 15 minutes
             * =====================================================
             */
            'scientificRecordLongRateLimit' => [
                'class' =>
                    RecordLookupRateLimit::class,

                'only' => [
                    'view',
                    'viewboatgeaers',
                    'viewlengthweight',
                    'viewcatch',
                    'viewoperationcost',
                    'get-report',
                    'approve',
                    'reject',
                    'update',
                    'delete',
                ],

                'limit' =>
                    200,

                'window' =>
                    900,

                'bucketName' =>
                    'scientific-record-long',
            ],
        ]
    );
}

    /**
     * Lists scientific records and handles the embedded enumeration request.
     */
    public function actionIndex()
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-list'
        );

        $searchModelEmu = new ScientificEnumerationRequestSearch();
        $dataProviderEmu = $searchModelEmu->search(
            $this->request->queryParams
        );

        $scientificEnumeration = new ScientificEnumerationRequest();

        if ($this->request->isPost) {
            CommonService::validatePermission(
                $this,
                'ScientificController-create'
            );

            $postData = $this->request->post();
            $enumerationPost = $postData['ScientificEnumerationRequest']
                ?? null;

            if (is_array($enumerationPost)) {
                $otherReason = trim(
                    (string) ($enumerationPost['other-reason'] ?? '')
                );

                if ($scientificEnumeration->load($postData)) {
                    /* Never trust ownership/workflow fields from POST. */
                    $scientificEnumeration->user =
                        (int) Yii::$app->user->id;
                    $scientificEnumeration->status = 1;
                    $scientificEnumeration->date = date('Y-m-d H:i:s');

                    if (
                        strtolower(
                            trim(
                                (string) $scientificEnumeration->can_continue
                            )
                        ) === 'yes'
                    ) {
                        $scientificEnumeration->reson = '';
                    } elseif (
                        trim((string) $scientificEnumeration->reson)
                        === 'Other'
                    ) {
                        if ($otherReason === '') {
                            $scientificEnumeration->addError(
                                'reson',
                                Yii::t(
                                    'app',
                                    'Please provide the other reason.'
                                )
                            );
                        } else {
                            $scientificEnumeration->reson = $otherReason;
                        }
                    }

                    if (
                        !$scientificEnumeration->hasErrors()
                        && $scientificEnumeration->save()
                    ) {
                        Yii::info(
                            [
                                'message' =>
                                    'Scientific enumeration request saved.',
                                'recordId' =>
                                    (int) $scientificEnumeration->id,
                                'userId' =>
                                    (int) Yii::$app->user->id,
                            ],
                            'scientific'
                        );

                        return $this->redirect(['index']);
                    }
                }
            }
        }

        $searchModel = new ScientificDataSearch();
        $dataProvider = $searchModel->search(
            $this->request->queryParams
        );

        $fiveMinutesAgo = date(
            'Y-m-d H:i:s',
            time() - (5 * 60)
        );

        $scientificEnumerationData =
            ScientificEnumerationRequest::find()
                ->where([
                    'user' => (int) Yii::$app->user->id,
                ])
                ->andWhere([
                    '>',
                    'date',
                    $fiveMinutesAgo,
                ])
                ->orderBy([
                    'id' => SORT_DESC,
                ])
                ->one();

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'searchModelEmu' => $searchModelEmu,
                'dataProviderEmu' => $dataProviderEmu,
                'sientificEnumeration' =>
                    $scientificEnumeration,
                'sientificEnumerationData' =>
                    $scientificEnumerationData,
            ]
        );
    }

    /**
     * Displays one ScientificData record.
     */
    public function actionView(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-view'
        );

        return $this->render(
            'view',
            [
                'model' => $this->findModelByToken($token),
                'token' => $token,
            ]
        );
    }

    /**
     * Displays Boat & Gear data for one persisted sampling craft.
     */
    public function actionViewboatgeaers(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-view'
        );

        $samplingModel = $this->findSamplingModelByToken($token);
        $model = ScientificSamplingBoatGearData::findOne([
            'sampling_data_id' => (int) $samplingModel->id,
        ]);

        return $this->render(
            'samplecraftview',
            [
                'model' => $model,
                'boat' => (string) ($samplingModel->boat_number ?? ''),
                'token' => $token,
                'parentToken' => $this->createParentScientificToken(
                    $samplingModel
                ),
            ]
        );
    }

    /**
     * Displays Length & Weight data for one persisted sampling craft.
     */
    public function actionViewlengthweight(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-view'
        );

        $samplingModel = $this->findSamplingModelByToken($token);
        $model = ScientificSamplingLengthDetails::findAll([
            'sampling_data_id' => (int) $samplingModel->id,
        ]);

        return $this->render(
            'lengthweightview',
            [
                'model' => $model,
                'boat' => (string) ($samplingModel->boat_number ?? ''),
                'token' => $token,
                'parentToken' => $this->createParentScientificToken(
                    $samplingModel
                ),
            ]
        );
    }

    /**
     * Displays Catch data for one persisted sampling craft.
     */
    public function actionViewcatch(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-view'
        );

        $samplingModel = $this->findSamplingModelByToken($token);
        $model = ScientificSamplingCatchData::findAll([
            'craft_id' => (int) $samplingModel->id,
        ]);

        return $this->render(
            'catchtview',
            [
                'model' => $model,
                'boat' => (string) ($samplingModel->boat_number ?? ''),
                'token' => $token,
                'parentToken' => $this->createParentScientificToken(
                    $samplingModel
                ),
            ]
        );
    }

    /**
     * Displays Economic/Operation Cost data for one persisted sampling craft.
     */
    public function actionViewoperationcost(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-view'
        );

        $samplingModel = $this->findSamplingModelByToken($token);
        $model = ScientificSamplingOperationCost::findOne([
            'sampling_data_id' => (int) $samplingModel->id,
        ]);

        return $this->render(
            'viewoperationcost',
            [
                'model' => $model,
                'boat' => (string) ($samplingModel->boat_number ?? ''),
                'token' => $token,
                'parentToken' => $this->createParentScientificToken(
                    $samplingModel
                ),
            ]
        );
    }

    /**
     * Creates a scientific record through ScientificService.
     */
    public function actionCreate()
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-create'
        );

        if (!$this->request->isPost) {
            return $this->render('scientific');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $scientificService = new ScientificService();
        $data = $this->request->post();

        if (!is_array($data)) {
            throw new BadRequestHttpException(
                Yii::t('app', 'Invalid scientific submission.')
            );
        }

        $fleetData = $data['fleet'] ?? [];
        $craftData = $data['craft'] ?? [];

        if (!is_array($fleetData) || !is_array($craftData)) {
            throw new BadRequestHttpException(
                Yii::t('app', 'Invalid scientific submission structure.')
            );
        }

        $scientificService->validateData($data);
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $scientificId = (int) $scientificService
                ->saveScientificData($transaction, $data);

            if ($scientificId <= 0) {
                throw new ServerErrorHttpException(
                    Yii::t(
                        'app',
                        'The scientific record could not be created.'
                    )
                );
            }

            $scientificService->saveFleetData(
                $transaction,
                $scientificId,
                $fleetData
            );

            $scientificService->saveSamplingCrafts(
                $transaction,
                $scientificId,
                $data
            );

            $transaction->commit();

            $recordToken = SecurityHelper::encryptId(
                ScientificData::class,
                $scientificId
            );

            Yii::info(
                [
                    'message' => 'Scientific record created.',
                    'recordId' => $scientificId,
                    'userId' => (int) Yii::$app->user->id,
                ],
                'scientific'
            );

            return [
                'success' => true,
                'message' => '',
                'token' => $recordToken,
                'viewUrl' => Url::to([
                    '/scientific/view',
                    'token' => $recordToken,
                ]),
            ];
        } catch (\Throwable $exception) {
            if ($transaction->isActive) {
                $transaction->rollBack();
            }

            Yii::error(
                [
                    'message' => 'Scientific record creation failed.',
                    'exception' => get_class($exception),
                    'error' => $exception->getMessage(),
                    'userId' => (int) Yii::$app->user->id,
                ],
                'scientific'
            );

            throw $exception;
        }
    }

    /**
     * Wizard pages do not have a database ID yet. The craft key is an opaque,
     * client-generated key used only to select a local draft craft.
     */
    public function actionAddsamplecraft(?string $craft = null): string
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-create'
        );
        $this->validateClientCraftKey($craft);

        return $this->render('samplecraft');
    }

    public function actionCatchdata(?string $craft = null): string
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-create'
        );
        $this->validateClientCraftKey($craft);

        return $this->render('catchdata');
    }

    public function actionAddlengthweight(?string $craft = null): string
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-create'
        );
        $this->validateClientCraftKey($craft);

        return $this->render('addlengthweight');
    }

    public function actionAddoperationcost(?string $craft = null): string
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-create'
        );
        $this->validateClientCraftKey($craft);

        return $this->render('addoperationcost');
    }

    /** Updating ScientificData remains intentionally disabled. */
    public function actionUpdate(string $token)
    {
        $this->findModelByToken($token);

        throw new UnauthorizedHttpException(
            Yii::t(
                'app',
                'You do not have permission to run this operation.'
            )
        );
    }

    /** Approves one scientific record. */
    public function actionApprove(string $token): Response
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-view'
        );
        $this->requireManagementRole();

        $model = $this->findModelByToken($token);
        $model->approval_stage = 'Approved';
        $model->inspected_by = (int) Yii::$app->user->id;

        if (!$model->save()) {
            Yii::error(
                [
                    'message' => 'Scientific approval failed.',
                    'recordId' => (int) $model->id,
                    'errors' => $model->getErrors(),
                ],
                'scientific'
            );

            throw new ServerErrorHttpException(
                Yii::t(
                    'app',
                    'The scientific record could not be approved.'
                )
            );
        }

        return $this->redirect([
            '/scientific/view',
            'token' => $token,
        ]);
    }

    /** Rejects one scientific record. */
    public function actionReject(string $token): Response
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-view'
        );
        $this->requireManagementRole();

        $model = $this->findModelByToken($token);
        $model->approval_stage = 'Rejected';
        $model->inspected_by = (int) Yii::$app->user->id;

        if (!$model->save()) {
            Yii::error(
                [
                    'message' => 'Scientific rejection failed.',
                    'recordId' => (int) $model->id,
                    'errors' => $model->getErrors(),
                ],
                'scientific'
            );

            throw new ServerErrorHttpException(
                Yii::t(
                    'app',
                    'The scientific record could not be rejected.'
                )
            );
        }

        return $this->redirect([
            '/scientific/view',
            'token' => $token,
        ]);
    }

    /** Deleting ScientificData remains intentionally disabled. */
    public function actionDelete(string $token)
    {
        $this->findModelByToken($token);

        throw new UnauthorizedHttpException(
            Yii::t(
                'app',
                'You do not have permission to run this operation.'
            )
        );
    }

    /**
     * Returns report data for exactly one ScientificData record.
     */
    public function actionGetReport(string $token): array
    {
        CommonService::validatePermission(
            $this,
            'ScientificController-view'
        );

        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = SecurityHelper::decryptId(
            trim($token),
            ScientificData::class
        );

        $model = ScientificData::find()
            ->where([
                'id' => (int) $id,
            ])
            ->with([
                'district0',
                'division0',
                'landingSite',
                'scientificFleetDatas',
                'scientificFleetDatas.boatType',
                'scientificFleetDatas.gearType',
                'scientificFleetDatas.subCategory',
                'scientificSamplingDatas',
                'scientificSamplingDatas.scientificSamplingBoatGearDatas',
                'scientificSamplingDatas.scientificSamplingBoatGearDatas.departureDistrict',
                'scientificSamplingDatas.scientificSamplingBoatGearDatas.departureDivision',
                'scientificSamplingDatas.scientificSamplingBoatGearDatas.depaturePort',
                'scientificSamplingDatas.scientificSamplingBoatGearDatas.fisheyType',
                'scientificSamplingDatas.scientificSamplingBoatGearDatas.subCategory',
                'scientificSamplingDatas.scientificSamplingLengthDetails',
                'scientificSamplingDatas.scientificSamplingLengthDetails.specie0',
                'scientificSamplingDatas.scientificSamplingBoatGearDatas.scientificSamplingGearDatas',
                'scientificSamplingDatas.scientificSamplingBoatGearDatas.scientificSamplingGearDatas.targetSpecies',
                'scientificSamplingDatas.scientificSamplingBoatGearDatas.scientificSamplingGearDatas.gear0',
            ])
            ->asArray()
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested scientific record was not found.'
                )
            );
        }

        $model['district'] =
            (string) ($model['district0']['name'] ?? '');
        $model['division'] =
            (string) ($model['division0']['name'] ?? '');
        $model['landing_site'] =
            (string) ($model['landingSite']['name'] ?? '');

        /* Preserve the original response shape: list containing one record. */
        return [$model];
    }

    /** Finds ScientificData using a model-bound encrypted token. */
    protected function findModelByToken(string $token): ScientificData
    {
        $id = SecurityHelper::decryptId(
            trim($token),
            ScientificData::class
        );

        return $this->findModel((int) $id);
    }

    /** Finds one sampling record using a model-bound encrypted token. */
    protected function findSamplingModelByToken(
        string $token
    ): ScientificSamplingData {
        $id = SecurityHelper::decryptId(
            trim($token),
            ScientificSamplingData::class
        );

        $model = ScientificSamplingData::findOne([
            'id' => (int) $id,
        ]);

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'The requested scientific sampling record was not found.'
            )
        );
    }

    /** Internal numeric lookup after token validation. */
    protected function findModel(int $id): ScientificData
    {
        $model = ScientificData::findOne([
            'id' => $id,
        ]);

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'The requested scientific record was not found.'
            )
        );
    }

    /**
     * Create a parent ScientificData token from a persisted sampling record.
     */
    private function createParentScientificToken(
        ScientificSamplingData $samplingModel
    ): string {
        $scientificId = (int) ($samplingModel->scientific_id ?? 0);

        if ($scientificId <= 0) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The parent scientific record was not found.'
                )
            );
        }

        /* Confirm the parent exists before creating the navigation token. */
        $this->findModel($scientificId);

        return SecurityHelper::encryptId(
            ScientificData::class,
            $scientificId
        );
    }

    /**
     * Unsaved craft identifiers are client-only opaque keys, not DB tokens.
     */
    private function validateClientCraftKey(?string $craft): void
    {
        $craft = trim((string) $craft);

        if (!preg_match('/^c_[a-f0-9]{32}$/', $craft)) {
            throw new NotFoundHttpException(
                Yii::t('app', 'The requested draft craft was not found.')
            );
        }
    }

    /** Approval/rejection authorization must also be enforced server-side. */
    private function requireManagementRole(): void
    {
        if (!UserTypeUtil::hasType(Constant::MANAGEMENT)) {
            throw new ForbiddenHttpException(
                Yii::t(
                    'app',
                    'You do not have permission to perform this operation.'
                )
            );
        }
    }
}
