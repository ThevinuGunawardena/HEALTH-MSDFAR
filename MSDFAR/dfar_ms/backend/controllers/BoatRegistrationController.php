<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\components\RecordLookupRateLimit;
use backend\components\SecurityHelper;
use backend\models\BoatNumbers;
use backend\models\Files;
use backend\models\FishermanRegisterdBoat;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\FishermanRegisterdBoatSearch;
use backend\models\MApprovalWorkflow;
use backend\models\MeaBoatRegistration;
use backend\models\MRequeredDocuments;
use backend\models\PaymentLog;
use backend\models\ProfileFisherman;
use backend\models\SubPaymentTypes;
use backend\services\CommonService;
use backend\services\Util;
use kartik\mpdf\Pdf;
use Mpdf\MpdfException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * BoatRegistrationController implements the CRUD actions for FishermanRegisterdBoat model.
 */
class BoatRegistrationController extends Controller
{
    public $processType = "BOAT_REGISTER";
    public $processTypeRenew = "BOAT_REGISTER_ReNEW";

    /**
     * @inheritDoc
     */
    public function behaviors(): array
{
    $behaviors = parent::behaviors();

    /*
     * Allow authenticated users only.
     */
    $behaviors['access'] = [
        'class' => AccessControl::class,
        'except' => [],
        'rules' => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ];

    /*
     * Preserve any inherited HTTP method rules.
     */
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

    $protectedActions = [
        'view',
        'create',
        'renew',
        'update',
        'update-callsign',
        'payment',
        'license-view',
        'license-view-first',
        'license-download',
        'first-license-download',
    ];

    /*
     * Burst protection:
     * Maximum 30 requests per 60 seconds.
     */
    $behaviors['recordLookupBurstLimit'] = [
        'class' => RecordLookupRateLimit::class,
        'only' => $protectedActions,
        'bucketName' => 'boat-registration-burst',
        'limit' => 30,
        'window' => 60,
    ];

    /*
     * Sustained protection:
     * Maximum 200 requests per 15 minutes.
     */
    $behaviors['recordLookupSustainedLimit'] = [
        'class' => RecordLookupRateLimit::class,
        'only' => $protectedActions,
        'bucketName' => 'boat-registration-sustained',
        'limit' => 200,
        'window' => 900,
    ];

    return $behaviors;
}
    /**
     * Lists all FishermanRegisterdBoat models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this, "BoatRegistrationController-list");

        $searchModel = new FishermanRegisterdBoatSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FishermanRegisterdBoat model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnauthorizedHttpException
     */
    public function actionView(string $token)
    {
        CommonService::validatePermission(
            $this,
            'BoatRegistrationController-office-view'
        );

        $nid = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        $model = $this->findLicenseModel($nid);
        $this->enforceLicenseOwnership($model);

        $process = (int) $model->renew === 1
            ? $this->processTypeRenew
            : $this->processType;

        $workflow = MApprovalWorkflow::findOne([
            'type' => $process,
        ]);

        if ($workflow === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Approval workflow was not found.')
            );
        }

        $requiredFileCount = Files::find()
            ->where([
                'type' => $workflow->id,
                'process_id' => $model->nid,
            ])
            ->andWhere(['!=', 'file_type', -999])
            ->count();

        $requiredDocumentCount = MRequeredDocuments::find()
            ->where([
                'type' => $workflow->id,
                'status' => 1,
            ])
            ->count();

        $validated = $model->validate();

        $approvalFlow = CommonService::getApprovalProcess(
            $model,
            $process,
            $model->nid,
            false
        );

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t('app', 'You are not allowed to perform this action.')
                );
            }

            $model = CommonService::markApprovalStage(
                $approvalFlow,
                $model,
                $model->nid,
                $process
            );

            if (
                $model->approval_stage === 'Completed'
                && (int) $model->renew === 0
            ) {
                $model->date_of_first_registration = date('Y-m-d');
            }

            if ($model->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    Yii::t('app', 'Boat registration updated successfully.')
                );

                return $this->redirect([
                    '/boat-registration/view',
                    'token' => SecurityHelper::encryptId(
                        FishermanRegisterdBoatLicense::class,
                        $model->nid
                    ),
                ]);
            }
        }

        $paymentHistory = [];

        if (in_array((int) $model->status, [100, 101], true)) {
            $paymentHistory = PaymentLog::find()
                ->where([
                    'type' => $process,
                    'process_id' => $model->nid,
                ])
                ->all();
        }

        $files = Files::find()
            ->where([
                'type' => $workflow->id,
                'process_id' => $model->nid,
            ])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'approvalHistory' => $approvalFlow['approvalHistory'],
            'showRejectBtn' => $approvalFlow['showRejectBtn'],
            'showApproveBtn' => $approvalFlow['showApproveBtn'],
            'paymentHistory' => $paymentHistory,
            'process' => $process,
            'validated' => $validated,
            'files' => $files,
            'requiredFileCount' => $requiredFileCount,
            'requiredDocumentCount' => $requiredDocumentCount,
        ]);
    }

    /**
     * Creates a new FishermanRegisterdBoat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     */
    public function actionCreate(string $boat)
    {
        CommonService::validatePermission(
            $this,
            'BoatRegistrationController-create'
        );

        $boatNumberId = SecurityHelper::decryptId(
            $boat,
            BoatNumbers::class
        );

        $boatDetails = BoatNumbers::findOne($boatNumberId);

        if ($boatDetails === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Boat number was not found.')
            );
        }

        $loggedInProfileId = (int) (
            Yii::$app->user->identity->profile_id ?? 0
        );

        if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
            $fishermanId = $loggedInProfileId;

            if (
                $fishermanId <= 0
                || (int) ($boatDetails->owner ?? 0) !== $fishermanId
            ) {
                throw new NotFoundHttpException(
                    Yii::t('app', 'Boat number was not found.')
                );
            }
        } else {
            $fishermanId = (int) ($boatDetails->owner ?? 0);
        }

        $fishermanProfile = ProfileFisherman::findOne($fishermanId);

        if ($fishermanProfile === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Boat owner profile was not found.')
            );
        }

        if (
            FishermanRegisterdBoat::find()
                ->where(['boat_number_id' => $boatNumberId])
                ->exists()
        ) {
            throw new BadRequestHttpException(
                Yii::t('app', 'A boat registration already exists for this boat.')
            );
        }

        $model = new FishermanRegisterdBoat();
        $modelLicense = new FishermanRegisterdBoatLicense();

        $model->boat_number_id = $boatNumberId;
        $model->fisherman_id = $fishermanProfile->id;

        $modelLicense->boat_number_id = $boatNumberId;
        $modelLicense->fisherman_id = $fishermanProfile->id;
        $modelLicense->district = $fishermanProfile->district;
        $modelLicense->division = $fishermanProfile->division;
        $modelLicense->landing_site = $fishermanProfile->landing_site;

        if ($this->request->isPost) {
            if (
                !UserTypeUtil::hasType(Constant::FISHERMAN)
                && !Util::editPermission()
            ) {
                throw new ForbiddenHttpException(
                    Yii::t('app', 'You are not allowed to perform this action.')
                );
            }

            if ($modelLicense->load($this->request->post())) {
                $model->boat_number_id = $boatNumberId;
                $model->fisherman_id = $fishermanProfile->id;

                $modelLicense->boat_number_id = $boatNumberId;
                $modelLicense->fisherman_id = $fishermanProfile->id;
                $modelLicense->district = $fishermanProfile->district;
                $modelLicense->division = $fishermanProfile->division;
                $modelLicense->landing_site = $fishermanProfile->landing_site;

                $this->normalizeBoatLicenseMultiValueFields($modelLicense);

                $modelLicense->status = 1;
                $modelLicense->approval_stage = (string) Constant::FI;

                if (
                    $modelLicense->hasAttribute('created')
                    && empty($modelLicense->created)
                ) {
                    $modelLicense->created = date('Y-m-d H:i:s');
                }

                $transaction = Yii::$app->db->beginTransaction();

                try {
                    if (!$model->save()) {
                        $modelLicense->addErrors($model->getErrors());
                        $transaction->rollBack();
                    } else {
                        $modelLicense->id = $model->id;

                        if (!$modelLicense->save()) {
                            $transaction->rollBack();
                        } else {
                            CommonService::addApprovalLog(
                                $this->processType,
                                'Submitted',
                                'Submitted',
                                $modelLicense->nid
                            );

                            $transaction->commit();

                            Yii::$app->session->setFlash(
                                'success',
                                Yii::t('app', 'Boat registration submitted successfully.')
                            );

                            if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
                                return $this->goHome();
                            }

                            return $this->redirect([
                                '/boat-registration/view',
                                'token' => SecurityHelper::encryptId(
                                    FishermanRegisterdBoatLicense::class,
                                    $modelLicense->nid
                                ),
                            ]);
                        }
                    }
                } catch (\Throwable $exception) {
                    if ($transaction->isActive) {
                        $transaction->rollBack();
                    }

                    Yii::error([
                        'message' => 'Boat registration creation failed.',
                        'boatNumberId' => $boatNumberId,
                        'exception' => $exception->getMessage(),
                    ], 'boat-registration-create');

                    throw $exception;
                }
            }
        } else {
            $model->loadDefaultValues();
            $modelLicense->loadDefaultValues();

            $model->boat_number_id = $boatNumberId;
            $model->fisherman_id = $fishermanProfile->id;

            $modelLicense->boat_number_id = $boatNumberId;
            $modelLicense->fisherman_id = $fishermanProfile->id;
            $modelLicense->district = $fishermanProfile->district;
            $modelLicense->division = $fishermanProfile->division;
            $modelLicense->landing_site = $fishermanProfile->landing_site;
        }

        return $this->render('create', [
            'model' => $modelLicense,
            'boatDetails' => $boatDetails,
        ]);
    }

    /**
     * Creates a new FishermanRegisterdBoat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     */
    public function actionRenew(string $token)
    {
        CommonService::validatePermission(
            $this,
            'BoatRegistrationController-renew'
        );

        $originalNid = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        $originalModel = $this->findLicenseModel($originalNid);
        $this->enforceLicenseOwnership($originalModel);

        $boatDetails = BoatNumbers::findOne(
            $originalModel->boat_number_id
        );

        if ($boatDetails === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Boat number was not found.')
            );
        }

        $hasOngoingRenewal = FishermanRegisterdBoatLicense::find()
            ->where([
                'id' => $originalModel->id,
                'renew' => 1,
            ])
            ->andWhere(['>', 'nid', $originalModel->nid])
            ->exists();

        if ($hasOngoingRenewal) {
            throw new BadRequestHttpException(
                Yii::t('app', 'A renewal request already exists for this boat registration.')
            );
        }

        $model = new FishermanRegisterdBoatLicense();
        $model->setAttributes($originalModel->getAttributes(), false);
        $model->nid = null;
        $model->setIsNewRecord(true);
        $model->renew = 1;
        $model->transered_license = 0;
        $model->approved_time = null;
        $model->expire_date = null;
        $model->created = date('Y-m-d H:i:s');
        $model->approval_stage = (string) Constant::FI;
        $model->status = 1;

        if ($this->request->isPost) {
            if (
                !UserTypeUtil::hasType(Constant::FISHERMAN)
                && !Util::editPermission()
            ) {
                throw new ForbiddenHttpException(
                    Yii::t('app', 'You are not allowed to perform this action.')
                );
            }

            $model->load($this->request->post());

            $model->nid = null;
            $model->setIsNewRecord(true);
            $model->id = $originalModel->id;
            $model->boat_number_id = $originalModel->boat_number_id;
            $model->fisherman_id = $originalModel->fisherman_id;
            $model->district = $originalModel->district;
            $model->division = $originalModel->division;
            $model->renew = 1;
            $model->transered_license = 0;
            $model->approved_time = null;
            $model->expire_date = null;
            $model->created = date('Y-m-d H:i:s');
            $model->approval_stage = (string) Constant::FI;
            $model->status = 1;

            $this->normalizeBoatLicenseMultiValueFields($model);

            CommonService::logValidateErrors($model);

            if ($model->save()) {
                CommonService::addApprovalLog(
                    $this->processTypeRenew,
                    'Submitted',
                    'Renew request',
                    $model->nid
                );

                Yii::$app->session->setFlash(
                    'success',
                    Yii::t('app', 'Boat registration renewal submitted successfully.')
                );

                if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
                    return $this->goHome();
                }

                return $this->redirect([
                    '/boat-registration/view',
                    'token' => SecurityHelper::encryptId(
                        FishermanRegisterdBoatLicense::class,
                        $model->nid
                    ),
                ]);
            }
        }

        $this->prepareBoatLicenseMultiValueFieldsForForm($model);

        return $this->render('create', [
            'model' => $model,
            'boatDetails' => $boatDetails,
        ]);
    }

    /**
     * Updates an existing FishermanRegisterdBoat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnauthorizedHttpException
     */
    public function actionUpdate(string $token)
    {
        CommonService::validatePermission(
            $this,
            'BoatRegistrationController-update'
        );

        $nid = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        $model = $this->findLicenseModel($nid);
        $this->enforceLicenseOwnership($model);

        $trustedValues = [
            'nid' => $model->nid,
            'id' => $model->id,
            'boat_number_id' => $model->boat_number_id,
            'fisherman_id' => $model->fisherman_id,
        ];

        $this->prepareBoatLicenseMultiValueFieldsForForm($model);

        $latestReport = MeaBoatRegistration::find()
            ->where(['boat_reg_number' => $model->boat_number_id])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if (empty($model->mea_report) && $latestReport !== null) {
            $model->mea_report = $latestReport->mea_certificate_number;
        }

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t('app', 'You are not allowed to perform this action.')
                );
            }

            if ($model->load($this->request->post())) {
                $model->nid = $trustedValues['nid'];
                $model->id = $trustedValues['id'];
                $model->boat_number_id = $trustedValues['boat_number_id'];
                $model->fisherman_id = $trustedValues['fisherman_id'];

                $this->normalizeBoatLicenseMultiValueFields($model);

                if ($model->save()) {
                    Yii::$app->session->setFlash(
                        'success',
                        Yii::t('app', 'Boat registration updated successfully.')
                    );

                    return $this->redirect([
                        '/boat-registration/view',
                        'token' => SecurityHelper::encryptId(
                            FishermanRegisterdBoatLicense::class,
                            $model->nid
                        ),
                    ]);
                }

                $this->prepareBoatLicenseMultiValueFieldsForForm($model);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    public function actionUpdateCallsign(string $token)
    {
        CommonService::validatePermission(
            $this,
            'BoatRegistrationController-update'
        );

        $nid = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        $model = $this->findLicenseModel($nid);

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t('app', 'You are not allowed to perform this action.')
                );
            }

            if (
                $model->load($this->request->post())
                && UserTypeUtil::hasType(Constant::CALL_SIGN)
                && $model->save(false)
            ) {
                return $this->redirect([
                    '/boat-registration/view',
                    'token' => SecurityHelper::encryptId(
                        FishermanRegisterdBoatLicense::class,
                        $model->nid
                    ),
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FishermanRegisterdBoat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(string $token)
    {
        SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        return $this->redirect(['index']);
    }

    /**
     * Finds the FishermanRegisterdBoat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return FishermanRegisterdBoat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findModel($id)
    {
        if (($model = FishermanRegisterdBoat::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Finds the FishermanRegisterdBoat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return FishermanRegisterdBoat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findLicenseModel($id)
    {
        if (($model = FishermanRegisterdBoatLicense::findOne(['nid' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    /**
     * @throws UnauthorizedHttpException
     */
    public function actionPayment(string $token)
    {
        CommonService::validatePermission(
            $this,
            'BoatRegistrationController-payment'
        );

        $nid = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        $boatLicense = $this->findLicenseModel($nid);
        $this->enforceLicenseOwnership($boatLicense);

        $process = (int) $boatLicense->renew === 1
            ? $this->processTypeRenew
            : $this->processType;

        $workflow = MApprovalWorkflow::findOne([
            'type' => $process,
        ]);

        if ($workflow === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Approval workflow was not found.')
            );
        }

        $paymentLog = new PaymentLog();
        $paymentLog->type = $process;
        $paymentLog->process_id = $boatLicense->nid;
        $paymentLog->status = 1;

        $paymentTypes = SubPaymentTypes::find()
            ->where(['payment_type_id' => $workflow->payment_type])
            ->asArray()
            ->all();

        $boatCategoryCode = trim((string) (
            $boatLicense->boatNumber->boatCategory->code ?? ''
        ));

        foreach ($paymentTypes as $item) {
            if (
                trim((string) ($item['Description'] ?? ''))
                === $boatCategoryCode
            ) {
                $paymentLog->amount = $item['Amount'];
                break;
            }
        }

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t('app', 'You are not allowed to perform this action.')
                );
            }

            if ($paymentLog->load($this->request->post())) {
                $paymentLog->type = $process;
                $paymentLog->process_id = $boatLicense->nid;
                $paymentLog->status = 1;

                $file = UploadedFile::getInstance($paymentLog, 'file');

                if ($file !== null) {
                    $fileName = $boatLicense->nid
                        . $process
                        . '-payment.'
                        . strtolower($file->extension);

                    $file->saveAs(
                        rtrim(Constant::$FILE_UPLOAD_PATH, '/\\')
                        . DIRECTORY_SEPARATOR
                        . 'payment'
                        . DIRECTORY_SEPARATOR
                        . $fileName
                    );

                    $paymentLog->file = $fileName;
                }

                if ($paymentLog->save()) {
                    if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
                        CommonService::addApprovalLog(
                            $process,
                            'Paid',
                            'Marked as Paid, Payment Approved',
                            $boatLicense->nid
                        );

                        CommonService::markAsPaid(
                            $boatLicense,
                            $process
                        );
                    } else {
                        CommonService::addApprovalLog(
                            $process,
                            'Paid',
                            'Marked as Paid',
                            $boatLicense->nid
                        );

                        $boatLicense->status = 100;
                    }

                    $boatLicense->save(false);

                    return $this->redirect([
                        '/boat-registration/view',
                        'token' => SecurityHelper::encryptId(
                            FishermanRegisterdBoatLicense::class,
                            $boatLicense->nid
                        ),
                    ]);
                }
            }
        }

        return $this->render('../payment/create', [
            'model' => $paymentLog,
        ]);
    }


    public
    function actionPaymentApprove($id)
    {
//        $model = FishermanRegisterdBoat::findOne($id);
//        CommonService::markAsPaid($model, $this->processType);
//        $model->save();
//        CommonService::addApprovalLog($this->processType, $model->approval_stage, "Payment Approved", $model->id);
//        echo json_encode(true);
//        exit();

    }

    /**
     * @throws UnauthorizedHttpException
     */
    /**
 * Display the normal boat licence.
 *
 * @throws \yii\web\UnauthorizedHttpException
 * @throws \yii\web\NotFoundHttpException
 */
    public function actionLicenseView(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'BoatNumbersController-license-view'
        );

        $nid = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        return $this->renderBoatLicenseView($nid, false);
    }

/**
 * Display the first boat licence.
 *
 * @throws \yii\web\UnauthorizedHttpException
 * @throws \yii\web\NotFoundHttpException
 */
    public function actionLicenseViewFirst(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'BoatNumbersController-license-view'
        );

        $nid = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        return $this->renderBoatLicenseView($nid, true);
    }

/**
 * Prepare common data for normal and first licence browser views.
 *
 * @throws \yii\web\NotFoundHttpException
 */
private function renderBoatLicenseView($id, bool $first): string
{
    $model = $this->findLicenseModel($id);

    if ($model === null) {
        throw new \yii\web\NotFoundHttpException(
            'Boat licence record was not found.'
        );
    }

    $workflowType = (int) $model->renew === 1
        ? 'BOAT_REGISTER_ReNEW'
        : 'BOAT_REGISTER';

    $officer = CommonService::getApprovedOfficer(
        $model->nid,
        $workflowType
    );

    if (empty($officer)) {
        $officer = CommonService::getApprovedOfficer(
            $model->nid,
            'BOAT_REGISTER'
        );
    }

    if ($officer instanceof \yii\db\ActiveRecord) {
        $officer = $officer->toArray();
    }

    if (!is_array($officer)) {
        $officer = [];
    }

    $officerSignatureFilename = basename(
        (string) ($officer['signature'] ?? '')
    );

    $officerSignaturePath = $officerSignatureFilename !== ''
        ? '/var/mountpoint/uploads/officer/signature/'
            . $officerSignatureFilename
        : '';

    $hasOfficerSignature =
        $officerSignaturePath !== ''
        && is_file($officerSignaturePath)
        && is_readable($officerSignaturePath);

    $fisherman = $model->fisherman;

    $fishermanSignatureFilename = basename(
        (string) ($fisherman?->signature ?? '')
    );

    $fishermanSignaturePath = $fishermanSignatureFilename !== ''
        ? '/var/mountpoint/uploads/fisherman/'
            . $fishermanSignatureFilename
        : '';

    $hasFishermanSignature =
        $fishermanSignaturePath !== ''
        && is_file($fishermanSignaturePath)
        && is_readable($fishermanSignaturePath);

    return $this->render(
        'licenseView',
        [
            'model' => $model,
            'first' => $first,
            'isPdf' => false,
            'officer' => $officer,
            'hasOfficerSignature' => $hasOfficerSignature,
            'hasFishermanSignature' => $hasFishermanSignature,
        ]
    );
}
    public
    function actionViewMea($mea)
    {

        $mea = MeaBoatRegistration::find()->where(["mea_certificate_number" => urldecode($mea)])->one();
        return $this->redirect(['mea-boat-registration/view', 'id' => $mea->id]);

    }

    /**
     * @throws CrossReferenceException
     * @throws MpdfException
     * @throws InvalidConfigException
     * @throws PdfParserException
     * @throws UnauthorizedHttpException
     * @throws PdfTypeException
     */
   /**
 * Download renewed/normal boat licence PDF.
 */
    public function actionLicenseDownload(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'BoatRegistrationController-license-download'
        );

        $nid = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        return $this->renderBoatLicensePdf($nid, false);
    }

/**
 * Download first boat licence PDF.
 */
    public function actionFirstLicenseDownload(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'BoatRegistrationController-license-download'
        );

        $nid = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        return $this->renderBoatLicensePdf($nid, true);
    }

/**
 * Generate the normal or first boat licence PDF.
 *
 * @throws \yii\web\NotFoundHttpException
 */
private function renderBoatLicensePdf(
    $id,
    bool $first
): string {
    /*
     * Secondary safeguard only. The main fix is removing Base64
     * images from license.php and licenseFirst.php.
     */
    ini_set('pcre.backtrack_limit', '5000000');

    $model = $this->findLicenseModel($id);

    if ($model === null) {
        throw new \yii\web\NotFoundHttpException(
            'Boat licence record was not found.'
        );
    }

    $workflowType = (int) $model->renew === 1
        ? 'BOAT_REGISTER_ReNEW'
        : 'BOAT_REGISTER';

    $officer = CommonService::getApprovedOfficer(
        $model->nid,
        $workflowType
    );

    if (empty($officer) && $workflowType !== 'BOAT_REGISTER') {
        $officer = CommonService::getApprovedOfficer(
            $model->nid,
            'BOAT_REGISTER'
        );
    }

    if ($officer instanceof \yii\db\ActiveRecord) {
        $officer = $officer->toArray();
    }

    if (!is_array($officer)) {
        $officer = [];
    }

    $officerSignatureFilename = basename(
        (string) ($officer['signature'] ?? '')
    );

    $officerSignaturePath =
        $officerSignatureFilename !== ''
            ? '/var/mountpoint/uploads/officer/signature/'
                . $officerSignatureFilename
            : '';

    $hasOfficerSignature =
        $officerSignaturePath !== ''
        && is_file($officerSignaturePath)
        && is_readable($officerSignaturePath);

    $fisherman = $model->fisherman;

    $fishermanSignatureFilename = basename(
        (string) ($fisherman?->signature ?? '')
    );

    $fishermanSignaturePath =
        $fishermanSignatureFilename !== ''
            ? '/var/mountpoint/uploads/fisherman/'
                . $fishermanSignatureFilename
            : '';

    $hasFishermanSignature =
        $fishermanSignaturePath !== ''
        && is_file($fishermanSignaturePath)
        && is_readable($fishermanSignaturePath);

    $viewFile = $first
        ? 'licenseFirst'
        : 'license';

    $content = $this->renderPartial(
        $viewFile,
        [
            'model' => $model,
            'first' => $first,
            'isPdf' => true,
            'pdf' => true,
            'officer' => $officer,
            'hasOfficerSignature' =>
                $hasOfficerSignature,
            'hasFishermanSignature' =>
                $hasFishermanSignature,
            'imageSrc' =>
                $this->createBoatLicenseImageResolver(true),
        ]
    );

    $boatNumber = trim(
        (string) (
            $model->boatNumber->boat_number
            ?? $model->nid
        )
    );

    $safeBoatNumber = preg_replace(
        '/[^A-Za-z0-9_-]+/',
        '_',
        $boatNumber
    );

    if ($safeBoatNumber === null || $safeBoatNumber === '') {
        $safeBoatNumber = 'boat-license-' . $model->nid;
    }

    $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8,
        'format' => Pdf::FORMAT_A4,
        'orientation' => Pdf::ORIENT_PORTRAIT,
        'destination' => Pdf::DEST_BROWSER,
        'filename' => $safeBoatNumber . '.pdf',
        'content' => $content,
        'marginLeft' => 10,
        'marginTop' => 10,
        'marginRight' => 10,
        'marginBottom' => 10,
    ]);

    $mpdf = $pdf->getApi();

    /*
     * Keep true while testing. Set to false after confirming
     * that every image exists and is readable.
     */
    $mpdf->showImageErrors = true;

    return $pdf->render();
}

/**
 * Resolves protected browser image URLs and local PDF image paths.
 */
private function createBoatLicenseImageResolver(
    bool $isPdf
): \Closure {
    return static function (
        string $relativePath
    ) use ($isPdf): string {
        $relativePath = trim(
            str_replace('\\', '/', $relativePath),
            '/'
        );

        if (
            $relativePath === ''
            || str_contains($relativePath, '..')
            || str_contains($relativePath, "\0")
        ) {
            return '';
        }

        if (!$isPdf) {
            $encodedPath = implode(
                '/',
                array_map(
                    'rawurlencode',
                    explode('/', $relativePath)
                )
            );

            return rtrim(
                Constant::$FILE_VIEW_PATH,
                '/'
            ) . '/' . $encodedPath;
        }

        $uploadRoot = realpath(
            '/var/mountpoint/uploads'
        );

        if ($uploadRoot === false) {
            Yii::error(
                'Upload root does not exist: '
                . '/var/mountpoint/uploads',
                __METHOD__
            );

            return '';
        }

        $requestedPath = $uploadRoot
            . DIRECTORY_SEPARATOR
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $relativePath
            );

        $realPath = realpath($requestedPath);

        if (
            $realPath === false
            || !is_file($realPath)
            || !is_readable($realPath)
        ) {
            Yii::warning(
                'Boat licence image is missing or unreadable: '
                . $requestedPath,
                __METHOD__
            );

            return '';
        }

        $allowedPrefix = $uploadRoot
            . DIRECTORY_SEPARATOR;

        if (!str_starts_with($realPath, $allowedPrefix)) {
            Yii::warning(
                'Blocked boat licence image path: '
                . $realPath,
                __METHOD__
            );

            return '';
        }

        return 'file:///' . ltrim(
            str_replace('\\', '/', $realPath),
            '/'
        );
    };
}

    private function enforceLicenseOwnership(
        FishermanRegisterdBoatLicense $model
    ): void {
        if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
            return;
        }

        $loggedInProfileId = (int) (
            Yii::$app->user->identity->profile_id ?? 0
        );

        if (
            $loggedInProfileId <= 0
            || (int) $model->fisherman_id !== $loggedInProfileId
        ) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Boat registration was not found.')
            );
        }
    }

    private function normalizeBoatLicenseMultiValueFields(
        FishermanRegisterdBoatLicense $model
    ): void {
        foreach (
            [
                'communication_equipment',
                'fishing_equipment',
                'navigation_equipment',
            ] as $attribute
        ) {
            $value = $model->{$attribute};

            if (is_array($value)) {
                $value = array_values(
                    array_filter(
                        array_map('strval', $value),
                        static fn (string $item): bool => $item !== ''
                    )
                );

                $model->{$attribute} = implode(',', $value);
            } elseif ($value === null) {
                $model->{$attribute} = '';
            }
        }
    }

    private function prepareBoatLicenseMultiValueFieldsForForm(
        FishermanRegisterdBoatLicense $model
    ): void {
        foreach (
            [
                'communication_equipment',
                'fishing_equipment',
                'navigation_equipment',
            ] as $attribute
        ) {
            $value = $model->{$attribute};

            if (is_array($value)) {
                continue;
            }

            $model->{$attribute} = $value === null || $value === ''
                ? []
                : explode(',', (string) $value);
        }
    }


    public
    static function markeAsExpired()
    {
        $expiredLicense = FishermanRegisterdBoatLicense::find()->where(["<=", "expire_date", date("Y-m-d")])->andWhere(["status" => Constant::Active])->all();

        foreach ($expiredLicense as $item) {
            $item->status = Constant::Expired;
            $item->save(false);
        }

    }

    public
    static function getStacs()
    {
        $where = [];
        $wherePending = [];

        if (UserTypeUtil::hasType(Constant::FI)) {
            $wherePending = ["division" => Yii::$app->session->get("officer_division"), 'approval_stage' => Constant::FI];
            $where = ["division" => Yii::$app->session->get("officer_division")];
        }
        if (UserTypeUtil::hasType(Constant::DO)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DO];
            $where = ["district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::AD)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::AD];
            $where = ["district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DFI)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DFI];
            $where = ["district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DM)) {
            $wherePending = ['approval_stage' => Constant::DM];
        }
        if (UserTypeUtil::hasType(Constant::DG)) {
            $wherePending = ['approval_stage' => Constant::DG];
        }
        $countPending = FishermanRegisterdBoatLicense::find()->where(['status' => Constant::Pending])->andWhere($wherePending)->count();
        $countActive = FishermanRegisterdBoatLicense::find()->where(['status' => Constant::Active])->andWhere($where)->count();
        $countFinalApproval = FishermanRegisterdBoatLicense::find()->where(['status' => Constant::FinalApprovalPending])->andWhere($where)->count();
        $countExpired = FishermanRegisterdBoatLicense::find()->where(['status' => Constant::Expired])->andWhere($where)->count();

        return [
            "countPending" => $countPending,
            "countActive" => $countActive,
            "countFinalApproval" => $countFinalApproval,
            "countExpired" => $countExpired,
        ];

    }

    public
    function actionSearchGlobal($q = null, $id = null)
    {
//        $officerProfile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select(["fisherman_registerd_boat.id", "boat_numbers.boat_number"])
                ->from('fisherman_registerd_boat')
                ->innerJoin("boat_numbers", "boat_numbers.id=fisherman_registerd_boat.boat_number_id")
                ->where(['like', 'boat_numbers.boat_number', $q])
                ->andWhere(['not in', 'boat_numbers.status', [400, 500,404]])
                ->limit(20);


            $command = $query->createCommand();
            $data = $command->queryAll();
            $dataFormated = [];
            foreach ($data as $datum) {
                $dataFormated[] = [
                    "id" => $datum['id'],
                    "text" => $datum['boat_number']
                ];
            }
//print_r($dataFormated);exit();
            $out['results'] = array_values($dataFormated);
        }
        return $out;
    }


}