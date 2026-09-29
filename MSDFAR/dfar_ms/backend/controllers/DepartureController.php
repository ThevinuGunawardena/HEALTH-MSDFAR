<?php

namespace backend\controllers;

use backend\components\RecordLookupRateLimit;
use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumbers;
use backend\models\DepartureBoats;
use backend\models\DepartureRequests;
use backend\models\DepartureRequestsSearch;
use backend\models\DepartureSkipper;
use backend\models\DeparureRequestCrew;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
use backend\models\ProfileFisherman;
use backend\models\ProfileOfficer;
use backend\models\Skipper;
use backend\models\SkipperRenew;
use backend\services\CommonService;
use backend\models\DepatureBoatPayment;
use backend\services\Util;
use Exception;
use kartik\mpdf\Pdf;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * DepartureController implements the CRUD actions for DepartureRequests model.
 */
class DepartureController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'except' => [
                'submitted',
                'create',
                'boatdetails',
                'getskipper',
                'getdepartureboatdetails',
            ],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $existingVerbActions =
            $behaviors['verbs']['actions'] ?? [];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => array_merge(
                $existingVerbActions,
                [
                    'delete' => ['POST'],
                ]
            ),
        ];

        /*
         * Protected Departure Request record routes.
         * These actions accept model-bound SecurityHelper tokens.
         */
        $protectedRecordActions = [
            'view',
            'submitted',
            'update',
            'license-download',
            'delete',
        ];

        $behaviors['departureRecordBurstLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $protectedRecordActions,
            'bucketName' => 'departure-record-burst',
            'limit' => 30,
            'window' => 60,
        ];

        $behaviors['departureRecordSustainedLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $protectedRecordActions,
            'bucketName' => 'departure-record-sustained',
            'limit' => 200,
            'window' => 900,
        ];

        /*
         * Public Departure form submission.
         * Keep this separate from record lookups to reduce abuse without
         * making normal AJAX form lookups too restrictive.
         */
        $behaviors['departureCreateBurstLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => ['create'],
            'bucketName' => 'departure-create-burst',
            'limit' => 20,
            'window' => 60,
        ];

        $behaviors['departureCreateSustainedLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => ['create'],
            'bucketName' => 'departure-create-sustained',
            'limit' => 100,
            'window' => 900,
        ];

        /*
         * Public boat / skipper lookup endpoints used by the create form.
         */
        $publicLookupActions = [
            'boatdetails',
            'getskipper',
            'getdepartureboatdetails',
        ];

        $behaviors['departureLookupBurstLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $publicLookupActions,
            'bucketName' => 'departure-lookup-burst',
            'limit' => 120,
            'window' => 60,
        ];

        $behaviors['departureLookupSustainedLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $publicLookupActions,
            'bucketName' => 'departure-lookup-sustained',
            'limit' => 600,
            'window' => 900,
        ];

        return $behaviors;
    }

    /**
     * Lists all DepartureRequests models.
     *
     * @return string
     */
    public function actionIndex($allIsland = 0)
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $searchModel = new DepartureRequestsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $allIsland);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DepartureRequests model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(string $token)
    {
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(
                Yii::t('app', 'The requested page does not exist.')
            );
        }

        $model = $this->findModelByToken($token);

        $crews = DeparureRequestCrew::find()
            ->where(['request_id' => $model->id])
            ->all();

        if (
            $this->request->isPost
            && $model->load($this->request->post())
            && Util::editPermission()
        ) {
            if (!UserTypeUtil::hasType(Constant::HARBOUR_OFFICER)) {
                throw new NotFoundHttpException(
                    Yii::t('app', 'The requested page does not exist.')
                );
            }

            if (
                (string) $model->harbor !==
                (string) Yii::$app->session->get('officer_harbour')
            ) {
                throw new UnauthorizedHttpException(
                    Yii::t('app', 'Unauthorized')
                );
            }

            $model->action_date = date('Y-m-d H:i');

            $officer = ProfileOfficer::findOne(
                Yii::$app->user->identity->profile_id
            );

            if ($officer === null) {
                throw new NotFoundHttpException(
                    Yii::t('app', 'Officer profile was not found.')
                );
            }

            $model->user =
                $officer->first_name
                . ' '
                . $officer->last_name
                . ' ('
                . Yii::$app->user->identity->nic
                . ')';

            if ($model->save()) {
                if ($model->approve === 'A') {
                    if ($this->sendemail((int) $model->id)) {
                        Yii::$app->session->setFlash(
                            'success',
                            'Email with PDF attachment sent successfully.'
                        );
                    } else {
                        Yii::$app->session->setFlash(
                            'error',
                            'Failed to send email.'
                        );
                    }
                }

                return $this->redirect([
                    'view',
                    'token' => $token,
                ]);
            }
        }

        return $this->render('view', [
            'model' => $model,
            'crews' => $crews,
            'token' => $token,
        ]);
    }

    /**
     * Displays a single DepartureRequests model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSubmitted(string $token): string
    {
        $model = $this->findModelByToken($token);

        $crews = DeparureRequestCrew::find()
            ->where(['request_id' => $model->id])
            ->all();

        return $this->render('submitted', [
            'model' => $model,
            'crews' => $crews,
            'token' => $token,
        ]);
    }

    /**
     * Creates a new DepartureRequests model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
   public function actionCreate()
{
    $model = new DepartureRequests();

    if ($this->request->isPost) {
        if ($model->load($this->request->post())) {
            $model->approve = 'P';
            $model->req_date_time = date('Y-m-d H:i');

            $transaction = Yii::$app->db->beginTransaction();

            $error = false;
            $hasTakenAsSkipper = false;
            $emptyCrewName = false;
            $emptyCrewMobile = false;
            $emptyCrewNic = false;
            $hasAtLeastOneCrew = false;
            $hasAtLeastOneEqu = false;
            $emptyHighseas = false;

            if ($model->fishing_area == 2 && empty($model->hs_license_no)) {
                $error = true;
                $emptyHighseas = true;
            }

            /*
             * Check whether at least one complete fishing-gear
             * combination has been entered.
             */
            if (
                (!empty($model->length_longline) && !empty($model->longline_hooks))
                || (!empty($model->length_ringnet) && !empty($model->mesh_ringnet))
                || (!empty($model->length_gillnet) && !empty($model->mesh_gillnet))
            ) {
                if (
                    !empty($model->length_longline)
                    && empty($model->longline_hooks)
                ) {
                    $hasAtLeastOneEqu = false;
                } elseif (
                    empty($model->length_longline)
                    && !empty($model->longline_hooks)
                ) {
                    $hasAtLeastOneEqu = false;
                } elseif (
                    !empty($model->length_ringnet)
                    && empty($model->mesh_ringnet)
                ) {
                    $hasAtLeastOneEqu = false;
                } elseif (
                    empty($model->length_ringnet)
                    && !empty($model->mesh_ringnet)
                ) {
                    $hasAtLeastOneEqu = false;
                } elseif (
                    !empty($model->length_gillnet)
                    && empty($model->mesh_gillnet)
                ) {
                    $hasAtLeastOneEqu = false;
                } elseif (
                    empty($model->length_gillnet)
                    && !empty($model->mesh_gillnet)
                ) {
                    $hasAtLeastOneEqu = false;
                } else {
                    $hasAtLeastOneEqu = true;
                }
            }

            if (
                $hasAtLeastOneEqu
                && (
                    !empty($model->national_license_no)
                    || !empty($model->hs_license_no)
                )
                && $model->agree == 1
                && $model->save()
            ) {
                $crewMembersNIC = (array) $this->request->post(
                    'crewNIC',
                    []
                );

                $crewNames = (array) $this->request->post(
                    'crewName',
                    []
                );

                $crewMobiles = (array) $this->request->post(
                    'crewMobile',
                    []
                );

                /*
                 * Use the largest array length to prevent undefined
                 * index errors when one field is missing.
                 */
                $crewRowCount = max(
                    count($crewMembersNIC),
                    count($crewNames),
                    count($crewMobiles)
                );

                for ($i = 0; $i < $crewRowCount; $i++) {
                    $crewNic = trim(
                        (string) ($crewMembersNIC[$i] ?? '')
                    );

                    $crewName = trim(
                        (string) ($crewNames[$i] ?? '')
                    );

                    $crewMobile = trim(
                        (string) ($crewMobiles[$i] ?? '')
                    );

                    /*
                     * Ignore a completely empty crew row.
                     */
                    if (
                        $crewNic === ''
                        && $crewName === ''
                        && $crewMobile === ''
                    ) {
                        continue;
                    }

                    /*
                     * If any field is entered, all three fields
                     * must be completed.
                     */
                    if ($crewNic === '') {
                        $emptyCrewNic = true;
                        $error = true;
                        break;
                    }

                    if ($crewName === '') {
                        $emptyCrewName = true;
                        $error = true;
                        break;
                    }

                    if ($crewMobile === '') {
                        $emptyCrewMobile = true;
                        $error = true;
                        break;
                    }

                    /*
                     * Skipper cannot be entered as a crew member.
                     */
                    if (
                        $crewNic === trim(
                            (string) $model->skipper_nic
                        )
                    ) {
                        $hasTakenAsSkipper = true;
                        $error = true;
                        break;
                    }

                    $crew = new DeparureRequestCrew();
                    $crew->request_id = $model->id;
                    $crew->nic = $crewNic;
                    $crew->name = $crewName;
                    $crew->mobile_number = $crewMobile;

                    if (!$crew->save()) {
                        $error = true;

                        $crewErrors = $crew->getFirstErrors();

                        Yii::$app->session->setFlash(
                            'error',
                            !empty($crewErrors)
                                ? implode(' ', $crewErrors)
                                : 'Unable to save crew member details.'
                        );

                        break;
                    }

                    $hasAtLeastOneCrew = true;
                }
            } else {
                $error = true;

                if ($model->agree == 0) {
                    Yii::$app->session->setFlash(
                        'error',
                        'Please accept the terms and conditions.'
                    );
                }

                /*
                 * Show this error only when both license
                 * numbers are empty.
                 */
                if (
                    empty($model->national_license_no)
                    && empty($model->hs_license_no)
                ) {
                    Yii::$app->session->setFlash(
                        'error',
                        'Please enter the High Seas or National License number.'
                    );
                }

                if (!$hasAtLeastOneEqu) {
                    Yii::$app->session->setFlash(
                        'error',
                        'අරං යන ආම්පන්න විස්තරය හරියට පුරවා ඇතිදැයි පරීක්ෂා කරන්න. | 
எடுத்துச் செல்லும் வலை தொடர்பான விபரங்கள் சரியாக நிரப்பப்பட்டுள்ளனவா என்பதை சரிபார்க்கவும். | 
Check whether the details of the fishing gears have been filled out correctly.'
                    );
                }
            }

            if (!$error && !$hasAtLeastOneCrew) {
                $error = true;

                Yii::$app->session->setFlash(
                    'error',
                    'අවම වශයෙන් එක් ගැනියෙකුගේ හෝ විස්තර පුරවන්න. | Please enter at least one crew member. | குறைந்தது ஒரு குழு உறுப்பினரையாவது உள்ளீடு செய்யவும்.'
                );
            }

            if ($hasTakenAsSkipper) {
                $error = true;

                Yii::$app->session->setFlash(
                    'error',
                    'නියමුවාව ගැනියෙකු ලෙස ද ඇතුළත් කළ නොහැක. | You cannot use the skipper as a crew member. | படகோட்டியை ஒரு குழு உறுப்பினராக உள்ளீடு செய்ய முடியாது.'
                );
            }

            if ($emptyCrewNic) {
                $error = true;

                Yii::$app->session->setFlash(
                    'error',
                    'Please enter the NIC number for all crew members.'
                );
            }

            if ($emptyCrewName) {
                $error = true;

                Yii::$app->session->setFlash(
                    'error',
                    'Please enter the name of all crew members.'
                );
            }

            if ($emptyCrewMobile) {
                $error = true;

                Yii::$app->session->setFlash(
                    'error',
                    'Please enter the mobile number of all crew members.'
                );
            }

            if ($emptyHighseas) {
                $error = true;

                Yii::$app->session->setFlash(
                    'error',
                    'ඔබට High Seas පිටත්වීම සඳහා අයදුම් කිරීමට වලංගු High Seas බලපත්‍රයක් නොමැත. | You do not have a valid High Seas license to apply for High Seas departure. | High Seas புறப்பாட்டிற்கு விண்ணப்பிக்க உங்களிடம் செல்லுபடியாகும் High Seas உரிமம் இல்லை.'
                );
            }

            if (!$error) {
                $transaction->commit();

                Yii::$app->session->setFlash(
                    'success',
                    'Departure request has been sent successfully | ගමන්වාර ඉල්ලීමේ අයදුම්පත සාර්ථකව යොමුකරන ලදී | புறப்பாடு கோரிக்கை வெற்றிகரமாக அனுப்பப்பட்டுள்ளது.'
                );

                $recordToken = $this->createDepartureToken($model);

                if (!Yii::$app->user->isGuest) {
                    return $this->redirect([
                        'view',
                        'token' => $recordToken,
                    ]);
                }

                return $this->redirect([
                    'submitted',
                    'token' => $recordToken,
                ]);
            }

            $transaction->rollBack();
        }
    } else {
        $model->loadDefaultValues();
    }

    return $this->render('create', [
        'model' => $model,
    ]);
}
    /**
     * Updates an existing DepartureRequests model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(string $token)
    {
        CommonService::validateEditPermission();
        if (Yii::$app->user->isGuest) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        if (!UserTypeUtil::hasType(Constant::HARBOUR_OFFICER)) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        CommonService::validatePermission($this, "DepartureController-update");
        $model = $this->findModelByToken($token);
        if ($model->harbor !=
            Yii::$app->session->get("officer_harbour")) {
            throw new UnauthorizedHttpException(Yii::t('app', 'Unauthorized'));
        }


        if ($model->approve == "A") {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $crews = DeparureRequestCrew::find()->where(["request_id" => $model->id])->all();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->approve = "P";
                $model->req_date_time = date("Y-m-d H:i");
                $transaction = Yii::$app->db->beginTransaction();
                $model->validate();
                $error = false;
                $hasTakenAsSkipper = false;
                $emptyCrewName = false;
                $hasAtLeastOneCrew = false;
                $hasAtLeastOneEqu = false;
                $emptyHighseas = false;
                if ($model->fishing_area == 2 && empty($model->hs_license_no)) {
                    $error = true;
                    $emptyHighseas = true;
                }

                if (
                    (!empty($model->length_longline) && !empty($model->longline_hooks))
                    || (!empty($model->length_ringnet) && !empty($model->mesh_ringnet))
                    || (!empty($model->length_gillnet) && !empty($model->mesh_gillnet))
                ) {
                    if (!empty($model->length_longline) && empty($model->longline_hooks)) {
                        $hasAtLeastOneEqu = false;

                    } elseif (empty($model->length_longline) && !empty($model->longline_hooks)) {
                        $hasAtLeastOneEqu = false;
                    } elseif (!empty($model->length_ringnet) && empty($model->mesh_ringnet)) {
                        $hasAtLeastOneEqu = false;
                    } elseif (empty($model->length_ringnet) && !empty($model->mesh_ringnet)) {
                        $hasAtLeastOneEqu = false;
                    } elseif (!empty($model->length_gillnet) && empty($model->mesh_gillnet)) {
                        $hasAtLeastOneEqu = false;
                    } elseif (empty($model->length_gillnet) && !empty($model->mesh_gillnet)) {
                        $hasAtLeastOneEqu = false;
                    } else {
                        $hasAtLeastOneEqu = true;
                    }
                }
                if ($hasAtLeastOneEqu && $model->agree == 1 && $model->save()) {
                    foreach ($crews as $crew) {
                        $crew->delete();
                    }
                    $crewMembersNIC = $this->request->post("crewNIC");
                    $crewName = $this->request->post("crewName");

                    for ($i = 0; $i < sizeof($crewMembersNIC); $i++) {
                        if ($crewMembersNIC[$i] != "") {
                            if (!empty($crewMembersNIC[$i])) {
                                $hasAtLeastOneCrew = true;
                            }
                            $crew = new DeparureRequestCrew();
                            $crew->request_id = $model->id;
                            $crew->nic = $crewMembersNIC[$i];
                            $crew->name = $crewName[$i];
                            if (empty($crewName[$i])) {

                                $emptyCrewName = true;
                                $error = true;
                                break;

                            }
                            if ($crewMembersNIC[$i] == $model->skipper_nic) {
                                $hasTakenAsSkipper = true;
                                $error = true;
                                break;
                            }
                            if (!$crew->save()) {
                                $error = true;
                                break;
                            }
                        }
                    }
                } else {
                    $error = true;
                    if ($model->agree == 0) {
                        Yii::$app->session->setFlash('error', 'Please accept the terms and conditions.');
                    }
                    if (!$hasAtLeastOneEqu) {
                        Yii::$app->session->setFlash('error', 'අරං යන ආම්පන්න විස්තරය හරියට පුරවා ඇතිදැයි පරීක්ෂා කරන්න. | Please check whether the details of the fishing gears have been filled out correctly | எடுத்துச் செல்லும் வலை தொடர்பான விபரங்கள் சரியாக நிரப்பப்பட்டுள்ளனவா என்பதை சரிபார்க்கவும்.');

                    }
                }
                if ($error == false && $hasAtLeastOneCrew == false) {
                    $error = true;
                    Yii::$app->session->setFlash('error', 'අවම වශයෙන් එක් ගැනියෙකුගේ හෝ විස්තර පුරවන්න. | Please enter at least one crew member. | குறைந்தது ஒரு குழு உறுப்பினரையாவது உள்ளீடு செய்யவும்.');
                }
                if ($error == true && $hasTakenAsSkipper == true) {
                    $error = true;
                    Yii::$app->session->setFlash('error', 'නියමුවාව ගැනියෙකු ලෙස ද ඇතුළත් කළ නොහැක. | You cannot use the skipper as a crew member. | படகோட்டியை ஒரு குழு உறுப்பினராக உள்ளீடு செய்ய முடியாது.');
                }
                if ($error == true && $emptyCrewName == true) {
                    $error = true;
                    Yii::$app->session->setFlash('error', 'Please fill the all crew members names');
                }
                if ($error == true && $emptyHighseas == true) {
                    $error = true;
                    Yii::$app->session->setFlash('error', 'ඔබට High Seas පිටත්වීම සඳහා අයදුම් කිරීමට වලංගු High Seas බලපත්‍රයක් නොමැත. | You do not have a valid High Seas license to apply for High Seas departure. | High Seas புறப்பாட்டிற்கு விண்ணப்பிக்க உங்களிடம் செல்லுபடியாகும் High Seas உரிமம் இல்லை.');
                }
                if (!$error) {
                    $transaction->commit();
                    if (!Yii::$app->user->isGuest) {
                        return $this->redirect([
                            'view',
                            'token' => $token,
                        ]);
                    }

                    return $this->redirect([
                        'submitted',
                        'token' => $token,
                    ]);
                } else {
                    $transaction->rollback();
                }

            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('update', [
            'model' => $model,
            'crews' => $crews,
            'token' => $token,
        ]);
    }

    /**
     * Deletes an existing DepartureRequests model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(string $token): Response
    {
        /*
         * Deletion remains intentionally disabled, but validate the
         * model-bound token so a numeric ID route cannot be reintroduced.
         */
        $this->findModelByToken($token);

        return $this->redirect(['index']);
    }

    public function actionBoatdetails($id)
    {
        $id = strtoupper(trim((string) $id));

        if (
            $id === ''
            || strlen($id) > 50
            || preg_match('/[\x00-\x1F\x7F]/', $id)
        ) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'status' => 'Invalid boat number.',
                'alters' => '',
                'ownerName' => '',
                'ownerMobile' => '',
                'ownerEmail' => '',
                'nationalLicence' => '',
                'highseasLicence' => '',
            ];
        }

         $returnData = [
        'status' => '',
        'alters' => '',
        'ownerName' => '',
        'ownerMobile' => '',
        'ownerEmail' => '',
        'nationalLicence' => '',
        'highseasLicence' => '',
    ];
        $boatNumber = BoatNumbers::find()->where(["boat_number" => $id, 'status' => Constant::Active])->one();
        if ($boatNumber == "") {
            $returnData["status"] = "Invalid boat number, please check the boat number again | වැරදි යාත්‍රා අංකයකි, කරැණාකර නැවත පරීක්ෂා කර බලන්න. | தவறான படகு எண், தயவுசெய்து படகு எண்ணை மீண்டும் சரிபார்க்கவும்.";
            echo json_encode($returnData);
            exit();

        }

        $boatReg = DepartureBoats::find()->where(["boat_number_id" => $boatNumber->id])->one();

        if ($boatReg === null || $boatReg->status === null || $boatReg->status !== "Departure Allowed" ) {
            $returnData["status"] = "Departure is not allowed for this boat | මෙම යාත්‍රාවට ගමන්වාර ලබාගැනීමට අවසර නැත | இந்தப் படகுக்கு புறப்பாடு அனுமதி இல்லை.";
            echo json_encode($returnData);
            exit();
        }

       if ((int)$boatReg->compulsory_service !== 1) {
            $returnData["status"] = "Departure is not allowed for this boat due to VMS Service Pending  | VMS උපාංගයේ වාර්ෂික නඩත්තු කටයුතු සිදු නොකිරීම නිසා මෙම යාත්‍රාවට ගමන්වාර ලබාගැනීමට අවසර නැත | VMS உபகரணத்துக்கான வருடாந்த பராமரிப்பு சேவை நிலுவையில் உள்ளதால், இந்த  மீன்பிடிப் படகுக்கான புறப்படுகைகள் இடைநிறுத்தப்பட்டுள்ளன.";
            echo json_encode($returnData);
            exit();
        }
        if (empty($boatReg->latestBoatRegExpire) || strtotime($boatReg->latestBoatRegExpire) < time()) {
            $returnData["status"] = "ඔබගේ යාත්‍රාවේ ලියාපදිංචිය කල් ඉකුත් වී ඇත. | You do not have a valid boat registration license. | உங்களிடம் செல்லுபடியாகும் படகு பதிவு உரிமம் இல்லை.";
            echo json_encode($returnData);
            exit();
        }
    $boatNumber = BoatNumbers::find()
    ->where([
        'boat_number' => $id,
        'status' => Constant::Active,
    ])
    ->one();

if ($boatNumber === null) {
    $returnData['status'] =
        'Boat number was not found or is inactive.';

    echo json_encode($returnData);
    exit();
}

$boatReg = DepartureBoats::find()
    ->where([
        'boat_number_id' => $boatNumber->id,
    ])
    ->one();

if ($boatReg === null) {
    $returnData['status'] =
        'Departure boat registration was not found.';

    echo json_encode($returnData);
    exit();
}

/*
 * Apply the following VMS payment restrictions only when:
 * 1. boat_type is 1
 * 2. DepartureBoats.VMS is 1
 */
// if (
//     (int) $boatNumber->boat_type === 1
//     && (int) $boatReg->VMS === 1
// ) {
//     /*
//      * Check whether the boat has an applicable payment
//      * within the last six months.
//      */
//     if (!$this->hasPaymentWithinLastSixMonths($boatReg->id)) {
//         $returnData['status'] =
//             'පසුගිය මාස හය තුළ අදාළ VMS ගෙවීමක් නොමැති බැවින් යාත්‍රාව පිටත් වීමට අවසර නැත. | ' .
//             'Departure is not allowed because there is no applicable VMS payment within the last six months. | ' .
//             'கடந்த ஆறு மாதங்களுக்குள் உரிய VMS கட்டணம் செலுத்தப்படாததால், புறப்பட அனுமதி இல்லை.';

//         echo json_encode($returnData);
//         exit();
//     }

//     /*
//      * From 31 December 2026, also block boats with
//      * unsettled previous VMS payments.
//      */
//     $timeZone = new \DateTimeZone('Asia/Colombo');

//     $today = new \DateTimeImmutable('now', $timeZone);

//     $previousPaymentDeadline = new \DateTimeImmutable(
//         '2026-12-31 00:00:00',
//         $timeZone
//     );

//     if ($today >= $previousPaymentDeadline) {
//         $previousUnpaidPaymentData = $this->getPreviousUnpaidMonths(
//             $boatReg->id
//         );

//         if ((int) $previousUnpaidPaymentData['count'] > 0) {
//             $unpaidMonths = implode(
//                 ', ',
//                 $previousUnpaidPaymentData['months']
//             );

//             $returnData['status'] =
//                 'පෙර මාසවල VMS හිඟ ගෙවීම් පියවා නොමැති බැවින් යාත්‍රාව පිටත් වීමට අවසර නැත. | ' .
//                 'Departure is not allowed because previous VMS payments have not been settled. ' .
//                 'Unpaid months: ' . $unpaidMonths . ' | ' .
//                 'கடந்த மாதங்களுக்கான VMS கட்டணங்கள் செலுத்தப்படாததால், புறப்பட அனுமதி இல்லை.';

//             echo json_encode($returnData);
//             exit();
//         }
//     }
// }
 //            if ((empty($boatReg->latestNationalExpire) && empty($boatReg->latestHighseasExpire)) || (strtotime
    //            ($boatReg->latestNationalExpire) < time() && strtotime($boatReg->latestHighseasExpire) < time())) {
    //        $returnData["status"] = "ඔබට සක්‍රීය Boat EEZ හෝ High Seas බලපත්‍රයක් නොමැත. | You do not have an active Boat EEZ or High Seas license. | உங்களிடம் செயலில் உள்ள Boat EEZ அல்லது High Seas உரிமம் இல்லை.";
    //        echo json_encode($returnData);
    //        exit();
    //    }
    //     $returnData['alters'] = "";
        $expireDateBR = $boatReg->latestBoatRegExpire;
//
        if (!empty($expireDateBR)) {

            $daysLeft = floor((strtotime($expireDateBR) - time()) / 86400);
            $formattedDate = date('Y-m-d', strtotime($expireDateBR));

            if ($daysLeft <= 30) {
                $returnData['alters'] = "<br> Boat Registration License expiring on $formattedDate (in $daysLeft days). | බෝට්ටු ලියාපදිංචි බලපත්‍රය $formattedDate දිනෙන් (දින $daysLeft ක් තුළ) කල් ඉකුත් වේ. | படகு பதிவு உரிமம் $formattedDate அன்று (இன்னும் $daysLeft நாட்களில்) காலாவதியாகும். ";
            }
        }
        $expireDate = $boatReg->latestNationalExpire;
//
        if (!empty($expireDate)) {

            $daysLeft = floor((strtotime($expireDate) - time()) / 86400);
            $formattedDate = date('Y-m-d', strtotime($expireDate));

            if ($daysLeft <= 30) {
                $returnData['alters'] = "<br>National License (EEZ) expiring on $formattedDate (in $daysLeft days). | ජාතික බලපත්‍රය (EEZ) $formattedDate දිනෙන් (දින $daysLeft ක් තුළ) කල් ඉකුත් වේ. | தேசிய உரிமம் (EEZ) $formattedDate அன்று (இன்னும் $daysLeft நாட்களில்) காலாவதியாகும். ";
            }
        }
        $expireDateHS = $boatReg->getLatestHighseasExpire();

        if (!empty($expireDateHS)) {

            $daysLeft = floor((strtotime($expireDateHS) - time()) / 86400);
            $formattedDate = date('Y-m-d', strtotime($expireDateHS));

            if ($daysLeft <= 30) {
                $returnData['alters'] = $returnData['alters'] . "<br> High Seas License expiring on $formattedDate (in $daysLeft days). | High Seas බලපත්‍රය $formattedDate දිනෙන් (දින $daysLeft ක් තුළ) කල් ඉකුත් වේ. | High Seas உரிமம் $formattedDate அன்று (இன்னும் $daysLeft நாட்களில்) காலாவதியாகும்.";
            }
        }
        $nationalLicence = NationalLicense::find()->where(['boat_registration_id' => $boatReg->id, "status" =>
            Constant::Active])->orderBy(['expire_date' => SORT_DESC])
            ->one();
        $highseasLicence = HighseasLicense::find()->where(['boat_registration_id' => $boatReg->id, "status" => Constant::Active])->orderBy(['expire_date' => SORT_DESC])->one();
        $owner = ProfileFisherman::findOne($boatReg->fisherman_id);

        $returnData['status'] = "";
        $returnData['ownerName'] = $owner->preferred_name_for_id;
        $returnData['ownerMobile'] = $owner->mobile;
        $returnData['ownerEmail'] = $owner->email;
        $returnData['nationalLicence'] = $nationalLicence->license_number ?? "";
        $returnData['highseasLicence'] = $highseasLicence->license_number ?? "";

        echo json_encode($returnData);
        exit();

    }

    private function hasCurrentMonthPayment($boatRegId)
{
    $currentMonth = date('Y-m-01');

    return DepatureBoatPayment::find()
        ->where(['boat_reg_id' => $boatRegId])
        ->andWhere(['<=', 'from_month', $currentMonth])
        ->andWhere(['>=', 'to_date', $currentMonth])
        ->exists();
}

private function getPreviousUnpaidMonths($boatRegId)
{
    $paymentRecords = DepatureBoatPayment::find()
        ->where(['boat_reg_id' => $boatRegId])
        ->andWhere(['not', ['from_month' => null]])
        ->andWhere(['not', ['to_date' => null]])
        ->orderBy(['to_date' => SORT_ASC])
        ->all();

    if (empty($paymentRecords)) {
        return [
            'count' => 0,
            'months' => [],
        ];
    }

    // Start from the month after the first payment's applicable-until month
    $firstPaymentToMonth = (new \DateTimeImmutable(
        $paymentRecords[0]->to_date
    ))->modify('first day of next month');

    // Only previous months; exclude the current month
    $lastPreviousMonth = (new \DateTimeImmutable('first day of this month'))
        ->modify('-1 month');

    if ($firstPaymentToMonth > $lastPreviousMonth) {
        return [
            'count' => 0,
            'months' => [],
        ];
    }

    // Store all paid months from all payment records
    $paidMonths = [];

    foreach ($paymentRecords as $payment) {
        $paymentFrom = (new \DateTimeImmutable($payment->from_month))
            ->modify('first day of this month');

        $paymentTo = (new \DateTimeImmutable($payment->to_date))
            ->modify('first day of this month');

        for (
            $month = $paymentFrom;
            $month <= $paymentTo;
            $month = $month->modify('+1 month')
        ) {
            $paidMonths[$month->format('Y-m')] = true;
        }
    }

    // Identify months with no payment coverage
    $unpaidMonths = [];

    for (
        $month = $firstPaymentToMonth;
        $month <= $lastPreviousMonth;
        $month = $month->modify('+1 month')
    ) {
        $monthKey = $month->format('Y-m');

        if (!isset($paidMonths[$monthKey])) {
            $unpaidMonths[] = $monthKey;
        }
    }

    return [
        'count' => count($unpaidMonths),
        'months' => $unpaidMonths,
    ];
}

    function getNicVariants($nic)
    {
        $nic = strtoupper(trim($nic));
        $list = [];

        // Always include original
        $list[] = $nic;

        // If 12-digit NIC
        if (preg_match('/^\d{12}$/', $nic)) {

            // Convert to old NIC
            $old = substr($nic, 2);
            $old = substr_replace($old, '', 5, 1); // remove 6th char

            $list[] = $old . 'V'; // with V
            $list[] = $old;       // without V
        }

        // If old NIC (with or without V)
        if (preg_match('/^\d{9}V?$/i', $nic)) {

            $base = rtrim($nic, 'V'); // remove V if exists

            // ensure both formats
            $list[] = $base;
            $list[] = $base . 'V';
            // remove V if exists
            $oldNic = strtoupper(rtrim($base, 'V'));

            if (preg_match('/^\d{9}$/', $oldNic)) {

                // insert 19 at start


                // insert 0 at 6th position (after year + part of serial)
                $new = substr($oldNic, 0, 5) . '0' . substr($oldNic, 5);
                $new = '19' . $new;
                $list[] = $new;
            }


        }

        // Remove duplicates
        return array_values(array_unique($list));
    }

    public function actionGetskipper($id)
    {
        $id = strtoupper(trim((string) $id));

        $returnData = [
            'status' => '',
            'id' => '',
            'name' => '',
            'nic' => '',
        ];

        if (
            $id === ''
            || strlen($id) > 20
            || !preg_match('/^[0-9A-Z]+$/', $id)
        ) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $returnData['status'] = 'Invalid NIC number.';
            return $returnData;
        }

        // Normalize NIC
        $nic = $id;
        if (!preg_match('/^\d{12}$/', $nic)) { // not 12 digits
            if (stripos($nic, 'v') === false) { // no 'v' or 'V'
                $nic .= 'V';
            }
        }

        $profileFisherman = ProfileFisherman::find()->where(['nic' => $nic])->one();
        $nicList = $this->getNicVariants($id);

        $departureSkippers = DepartureSkipper::find()
            ->where(['nic' => $nicList])
            ->all();

        // Check if any record is NOT allowed
        $blocked = false;
        $departureSkipper = null;

        foreach ($departureSkippers as $ds) {
            if ($ds->status != "Departure Allowed" || $ds->status == "") {
                $blocked = true;
                break;
            }
            $departureSkipper = $ds; // keep a valid one
        }
        if ($blocked) {
            $returnData["status"] = "Departure is not allowed for this departureSkipper | මෙම නියමුවාට ගමන්වාර පිටත්වීමට අවසර නැත | இந்தப் படகோட்டிக்கு புறப்பாடு அனுமதி இல்லை.";
            echo json_encode($returnData);
            exit();
        }

        if (!empty($profileFisherman)) {
            $skipperProfile = Skipper::find()->where(['fisherman_id' => $profileFisherman->id])->one();

            if (!empty($skipperProfile)) {
                if (empty($departureSkipper)) {
                    $departureSkipper = new DepartureSkipper();
                    $departureSkipper->status = 'Departure Allowed';
                }
                if ($skipperProfile->status == Constant::Pending || $skipperProfile->status ==
                    Constant::PaymentPending) {
                    $departureSkipper->skipper_id = "SKPREQ-" . $skipperProfile->id;
                } elseif ($skipperProfile->status == Constant::Expired) {
                    $skipperProfileRenew = SkipperRenew::find()->where(['skipper_uid' =>
                        $skipperProfile->skipper_uid])->andWhere(['!=', 'status', Constant::Completed])->one();
                    if (!empty($skipperProfileRenew)) {
                        $departureSkipper->skipper_id = "SKPREQ-" . $skipperProfileRenew->id;
                    }

                } else {
                    $departureSkipper->skipper_id =
                        $profileFisherman->fisherman_uid;
                }

                $departureSkipper->skipper_name =
                    $profileFisherman->preferred_name_for_id;

                $departureSkipper->nic = $id;
            }
        }
//        $departureSkipper->save(false);

        if ($departureSkipper == "" || empty($departureSkipper)) {

            $returnData['status'] = "";
            $returnData['name'] = "";
            $returnData['nic'] = "";
            echo json_encode($returnData);
            exit();
        }


        $returnData['status'] = "";
        $returnData['id'] = $departureSkipper->skipper_id;
        $returnData['name'] = $departureSkipper->skipper_name;
        $returnData['nic'] = $departureSkipper->nic;

        echo json_encode($returnData);
        exit();

    }
    public function actionGetdepartureboatdetails($main, $role)
    {
        $main = trim((string) $main);
        $role = trim((string) $role);

        if (
            $main === ''
            || $role === ''
            || strlen($main) > 100
            || strlen($role) > 100
        ) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return Constant::getSubTypesByMainAndAllowed(
            $main,
            $role
        );
    }

    /**
     * Create a non-guessable, model-bound Departure Request token.
     */
    private function createDepartureToken(
        DepartureRequests $model
    ): string {
        return SecurityHelper::encryptId(
            DepartureRequests::class,
            (int) $model->id
        );
    }

    /**
     * Resolve a Departure Request from a model-bound token.
     */
    protected function findModelByToken(
        string $token
    ): DepartureRequests {
        $id = SecurityHelper::decryptId(
            trim($token),
            DepartureRequests::class
        );

        return $this->findModel((int) $id);
    }

    /**
     * Finds the DepartureRequests model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return DepartureRequests the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): DepartureRequests
    {
        $model = DepartureRequests::findOne([
            'id' => (int) $id,
        ]);

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'The requested page does not exist.'
            )
        );
    }


   public function sendemail($id): bool
{
    try {
        $model = DepartureRequests::findOne($id);

        if ($model === null) {
            throw new \RuntimeException(
                'Departure request was not found. ID: ' . $id
            );
        }

        $crew = DeparureRequestCrew::findAll([
            'request_id' => $model->id,
        ]);

        /*
         * Physical image paths on the Apache/Yii server.
         */
        $nationalLogoPath = '/var/mountpoint/uploads/static/national_Logo2.jpg';
        $boatLicensePath  = '/var/mountpoint/uploads/static/boatlicense_1.jpg';
        $bottlesPath      = '/var/mountpoint/uploads/static/bottles.png';

        $imagePaths = [
            'National logo' => $nationalLogoPath,
            'Boat license'  => $boatLicensePath,
            'Bottles image' => $bottlesPath,
        ];

        /*
         * Check that every image exists and PHP can read it.
         */
        foreach ($imagePaths as $imageName => $imagePath) {
            if (!is_file($imagePath)) {
                throw new \RuntimeException(
                    $imageName . ' was not found: ' . $imagePath
                );
            }

            if (!is_readable($imagePath)) {
                throw new \RuntimeException(
                    $imageName . ' is not readable: ' . $imagePath
                );
            }
        }

        /*
         * The PDF view must use:
         * src="var:nationalLogo"
         * src="var:boatLicense"
         * src="var:bottlesImage"
         */
        $content = $this->renderPartial(
            'departureApprovalPdf',
            [
                'row'  => $model,
                'crew' => $crew,
            ]
        );

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'defaultFontSize' => 45,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_STRING,
            'content' => $content,
        ]);

        /*
         * Get the underlying mPDF instance.
         */
        $mpdf = $pdf->getApi();

        /*
         * Enable temporarily to identify image errors.
         * Change to false after confirming everything works.
         */
        $mpdf->showImageErrors = true;

        /*
         * Register the raw image data before rendering.
         */
        $mpdf->imageVars['nationalLogo'] =
            file_get_contents($nationalLogoPath);

        $mpdf->imageVars['boatLicense'] =
            file_get_contents($boatLicensePath);

        $mpdf->imageVars['bottlesImage'] =
            file_get_contents($bottlesPath);

        /*
         * Render only after imageVars have been registered.
         */
        $pdfContent = $pdf->render();

        $sendEmail = Yii::$app->mailer
            ->compose()
            ->setFrom('departure1@fisheriesdept.gov.lk')
            ->setTo($model->email)
            ->setSubject(
                'Departure approval for '
                . $model->boat_no
                . ' - Issue date: '
                . date('Y-m-d H:i:s')
            )
            ->setTextBody('Please find the PDF report attached.')
            ->setHtmlBody(
                '<b>Please find the PDF report attached.</b>'
            )
            ->attachContent(
                $pdfContent,
                [
                    'fileName' =>
                        'Departure_approval-'
                        . $model->boat_no
                        . '.pdf',
                    'contentType' => 'application/pdf',
                ]
            )
            ->send();

        return $sendEmail === true;

    } catch (\Throwable $e) {
        Yii::error(
            'Error while generating or emailing departure PDF: '
            . $e->getMessage()
            . ' at '
            . $e->getFile()
            . ':'
            . $e->getLine(),
            __METHOD__
        );

        Yii::$app->session->setFlash(
            'error',
            'Unable to send the PDF: ' . $e->getMessage()
        );

        return false;
    }
}

    public function actionLicenseDownload(string $token): string
{
    if (!UserTypeUtil::hasType(Constant::HARBOUR_OFFICER)) {
        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'The requested page does not exist.'
            )
        );
    }

    $model = $this->findModelByToken($token);

    if ($model === null || $model->approve !== 'A') {
        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'The requested page does not exist.'
            )
        );
    }

    $crew = DeparureRequestCrew::findAll([
    'request_id' => (int) $model->id,
]);

    /*
     * Local image paths used by mPDF.
     */
    $imagePaths = [
        'nationalLogo' =>
            '/var/mountpoint/uploads/static/national_Logo2.jpg',

        'boatLicense' =>
            '/var/mountpoint/uploads/static/boatlicense_1.jpg',

        'bottlesImage' =>
            '/var/mountpoint/uploads/static/bottles.png',
    ];

    $imageData = [];

    foreach ($imagePaths as $imageKey => $imagePath) {
        if (
            !is_file($imagePath)
            || !is_readable($imagePath)
        ) {
            Yii::error(
                'PDF image missing or unreadable: '
                . $imagePath,
                __METHOD__
            );

            throw new \yii\web\ServerErrorHttpException(
                'A required PDF image could not be loaded.'
            );
        }

        $loadedImage = file_get_contents($imagePath);

        if (
            $loadedImage === false
            || $loadedImage === ''
        ) {
            Yii::error(
                'Unable to read PDF image: '
                . $imagePath,
                __METHOD__
            );

            throw new \yii\web\ServerErrorHttpException(
                'A required PDF image could not be read.'
            );
        }

        $imageData[$imageKey] = $loadedImage;
    }

    $content = $this->renderPartial(
        'departureApprovalPdf',
        [
            'row' => $model,
            'crew' => $crew,
        ]
    );

    /*
     * Remove characters that are unsafe in filenames.
     */
    $boatNumber = preg_replace(
        '/[^A-Za-z0-9_-]/',
        '_',
        (string) $model->boat_no
    );

    $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8,
        'format' => Pdf::FORMAT_A4,
        'orientation' => Pdf::ORIENT_PORTRAIT,
        'destination' => Pdf::DEST_BROWSER,

        'filename' =>
            'Departure_approval-'
            . $boatNumber
            . '.pdf',

        'content' => $content,

        'marginLeft' => 10,
        'marginTop' => 10,
        'marginRight' => 10,
        'marginBottom' => 10,
    ]);

    /*
     * Register images used by:
     *
     * var:nationalLogo
     * var:boatLicense
     * var:bottlesImage
     */
    $mpdf = $pdf->getApi();

    $mpdf->imageVars['nationalLogo'] =
        $imageData['nationalLogo'];

    $mpdf->imageVars['boatLicense'] =
        $imageData['boatLicense'];

    $mpdf->imageVars['bottlesImage'] =
        $imageData['bottlesImage'];

    /*
     * Set true temporarily when debugging image errors.
     */
    $mpdf->showImageErrors = false;

    return $pdf->render();
}




//     private function hasPaymentWithinLastSixMonths($boatRegId): bool
// {
//     // Current month + previous 5 months = 6 months total
//     $rangeStart = date('Y-m-01', strtotime('-5 months'));

//     // Last day of the current month
//     $rangeEnd = date('Y-m-t');

//     return DepatureBoatPayment::find()
//         ->where(['boat_reg_id' => $boatRegId])

//         // Payment starts before the six-month range ends
//         ->andWhere(['<=', 'from_month', $rangeEnd])

//         // Payment ends after the six-month range starts
//         ->andWhere(['>=', 'to_date', $rangeStart])

//         ->exists();
// }

private function hasPaymentWithinLastSixMonths($boatRegId): bool
{
    // Previous 6 completed months, excluding the current month
    $rangeStart = date('Y-m-01', strtotime('first day of -6 months'));
    $rangeEnd = date('Y-m-t', strtotime('last day of previous month'));

    return DepatureBoatPayment::find()
        ->where(['boat_reg_id' => $boatRegId])
        ->andWhere(['<=', 'from_month', $rangeEnd])
        ->andWhere(['>=', 'to_date', $rangeStart])
        ->exists();
}
}