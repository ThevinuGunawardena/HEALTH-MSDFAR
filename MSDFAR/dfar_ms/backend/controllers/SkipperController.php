<?php

namespace backend\controllers;

use backend\components\RecordLookupRateLimit;
use backend\components\SecurityHelper;
use yii\web\ForbiddenHttpException;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Files;
use backend\models\MApprovalWorkflow;
use backend\models\MEducatinalQualification;
use backend\models\MFiDistrict;
use backend\models\MDivision;
use backend\models\MRequeredDocuments;
use backend\models\MTraningInstitutes;
use backend\models\PaymentLog;
use backend\models\ProfileFisherman;
use backend\models\ProfileOfficer;
use backend\models\Skipper;
use backend\models\SkipperRenew;
use backend\models\SkipperSearch;
use backend\models\SkipperTranings;
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
use yii\helpers\ArrayHelper;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
use yii\helpers\Html;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Mpdf\HTMLParserMode;
/**
 * SkipperController implements the CRUD actions for Skipper model.
 */
class SkipperController extends Controller
{
    public $processType = "SKIPPER_LICENCE";

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

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

        $existingVerbActions =
            $behaviors['verbs']['actions'] ?? [];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => array_merge(
                $existingVerbActions,
                [
                    'delete' => ['POST'],
                    'printed' => ['POST'],
                ]
            ),
        ];

        $protectedActions = [
            'view',
            'create',
            'update',
            'renew',
            'payment',
            'license-view',
            'license-download',
            'printed',
            'delete',
        ];

        $behaviors['recordLookupBurstLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $protectedActions,
            'bucketName' => 'skipper-burst',
            'limit' => 30,
            'window' => 60,
        ];

        $behaviors['recordLookupSustainedLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $protectedActions,
            'bucketName' => 'skipper-sustained',
            'limit' => 200,
            'window' => 900,
        ];


        $behaviors['trainingProgramSearchLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => [
                'load-programs',
            ],
            'bucketName' =>
                'skipper-training-program-search',
            'limit' => 120,
            'window' => 60,
        ];

        return $behaviors;
    }

    /**
     * Lists all Skipper models.
     *
     * @return string
     * @throws UnauthorizedHttpException
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this, "SkipperController-list");
        $this->markeAsExpired();

        $searchModel = new SkipperSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function markeAsExpired()
    {
        $expiredLicense = Skipper::find()->where(["<=", "expire_date", date("Y-m-d")])->all();
        foreach ($expiredLicense as $item) {
            $item->status = Constant::Expired;
            $item->save();
        }
    }
    /**
     * Displays a single Skipper model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnauthorizedHttpException
     */
    public function actionView(string $token)
    {
        CommonService::validatePermission(
            $this,
            'SkipperController-office-view'
        );

        $id = SecurityHelper::decryptId(
            $token,
            Skipper::class
        );

        $model = $this->findModel($id);
        $this->enforceSkipperOwnership($model);

        $workflow = MApprovalWorkflow::findOne([
            'type' => $this->processType,
        ]);

        if ($workflow === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Approval workflow was not found.')
            );
        }

        $uploadedRequiredFileCount = Files::find()
            ->where([
                'type' => $workflow->id,
                'process_id' => $model->id,
            ])
            ->andWhere(['!=', 'file_type', -999])
            ->count();

        $requiredDocumentCount = MRequeredDocuments::find()
            ->where([
                'type' => $workflow->id,
                'status' => 1,
            ])
            ->count();

        $validated =
            $model->validate()
            && $uploadedRequiredFileCount >= $requiredDocumentCount;

        $tranings = SkipperTranings::find()
            ->where(['skipper_id' => $model->id])
            ->all();

        $approvalFlow = CommonService::getApprovalProcess(
            $model,
            $this->processType,
            $model->id,
            false
        );

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t(
                        'app',
                        'You are not allowed to perform this action.'
                    )
                );
            }

            $model = CommonService::markApprovalStage(
                $approvalFlow,
                $model,
                $model->id,
                $this->processType
            );

            if (empty($model->skipper_uid)) {
                $uid = Constant::$SKIPPER_NUMBER_FORMAT;
                $uid = str_replace(
                    '{number}',
                    sprintf('%05d', $model->id),
                    $uid
                );
                $uid = str_replace(
                    '{district_code}',
                    (string) ($model->fisheriesDistrict->code ?? ''),
                    $uid
                );
                $model->skipper_uid = $uid;
            }

            if ($model->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    Yii::t('app', 'Skipper licence updated successfully.')
                );

                return $this->redirect([
                    '/skipper/view',
                    'token' => SecurityHelper::encryptId(
                        Skipper::class,
                        $model->id
                    ),
                ]);
            }
        }

        $paymentHistory = [];

        if (in_array((int) $model->status, [100, 101], true)) {
            $paymentHistory = PaymentLog::find()
                ->where([
                    'type' => $this->processType,
                    'process_id' => $model->id,
                ])
                ->all();
        }

        $files = Files::find()
            ->where([
                'type' => $workflow->id,
                'process_id' => $model->id,
            ])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'tranings' => $tranings,
            'approvalHistory' => $approvalFlow['approvalHistory'],
            'showRejectBtn' => $approvalFlow['showRejectBtn'],
            'showApproveBtn' => $approvalFlow['showApproveBtn'],
            'paymentHistory' => $paymentHistory,
            'process' => $this->processType,
            'validated' => $validated,
            'files' => $files,
        ]);
    }

    /**
     * Creates a new Skipper model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     * @throws InvalidConfigException
     * @throws UnauthorizedHttpException
     */
    public function actionCreate()
    {
        CommonService::validatePermission($this, "SkipperController-create");


        $model = new Skipper();
        $skipperTranings = new SkipperTranings();
        $fishermanList = ArrayHelper::map(ProfileFisherman::find()->where(['status' => Constant::Active])->asArray()->all(), "id", "first_name");
        $educationQualification = ArrayHelper::map(MEducatinalQualification::find()->where(['status' => 1])->asArray()->all(), "id", "description");
        $districtList = ArrayHelper::map(MFiDistrict::find()->where(["status" => 1])->asArray()->all(), 'id', "name");
        $instituteList = ArrayHelper::map(MTraningInstitutes::find()->where(["status" => 1])->asArray()->all(), 'id', "name");

        if ($this->request->isPost) {
            $tranings = $this->request->post("Tranings");
            $model->approval_stage = "" . Constant::DO;

            if ($model->load($this->request->post()) && Util::editPermission() && $model->save()) {
                for ($i = 0; $i < sizeof($tranings['institute']); $i++) {
                    $skipperTranings = new SkipperTranings();
                    $skipperTranings->institute = $tranings['institute'][$i];
                    $skipperTranings->program_name = $tranings['program_name'][$i];
                    $skipperTranings->training_period = $tranings['training_period'][$i];
                    $skipperTranings->date_certified = $tranings['date_certified'][$i];
                    $skipperTranings->skipper_id = $model->id;
                    $skipperTranings->save();
                }
                CommonService::addApprovalLog($this->processType, "Submitted", "Submitted", $model->id);

                return $this->redirect([
                    '/skipper/view',
                    'token' => SecurityHelper::encryptId(
                        Skipper::class,
                        $model->id
                    ),
                ]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'fishermanList' => $fishermanList,
            'educationQualification' => $educationQualification,
            'districtList' => $districtList,
            'skipperTranings' => $skipperTranings,
            'instituteList' => $instituteList,
            'renew' => false,
        ]);
    }

    /**
     * Updates an existing Skipper model.
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
            'SkipperController-update'
        );

        $id = SecurityHelper::decryptId(
            $token,
            Skipper::class
        );

        $model = $this->findModel($id);
        $this->enforceSkipperOwnership($model);

        $trustedValues = [
            'id' => $model->id,
            'fisherman_id' => $model->fisherman_id,
            'skipper_uid' => $model->skipper_uid,
        ];

        $skipperTranings = new SkipperTranings();
        $fishermanList = ArrayHelper::map(
            ProfileFisherman::find()
                ->where(['status' => Constant::Active])
                ->asArray()
                ->all(),
            'id',
            'first_name'
        );
        $educationQualification = ArrayHelper::map(
            MEducatinalQualification::find()
                ->where(['status' => 1])
                ->asArray()
                ->all(),
            'id',
            'description'
        );
        $districtList = ArrayHelper::map(
            MFiDistrict::find()
                ->where(['status' => 1])
                ->asArray()
                ->all(),
            'id',
            'name'
        );
        $instituteList = ArrayHelper::map(
            MTraningInstitutes::find()
                ->where(['status' => 1])
                ->asArray()
                ->all(),
            'id',
            'name'
        );

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t(
                        'app',
                        'You are not allowed to perform this action.'
                    )
                );
            }

            if ($model->load($this->request->post())) {
                $model->id = $trustedValues['id'];
                $model->fisherman_id = $trustedValues['fisherman_id'];
                $model->skipper_uid = $trustedValues['skipper_uid'];

                if ($model->save()) {
                    $tranings = (array) $this->request->post(
                        'Tranings',
                        []
                    );
                    $institutes = (array) ($tranings['institute'] ?? []);

                    foreach ($institutes as $index => $instituteId) {
                        if (empty($instituteId)) {
                            continue;
                        }

                        $training = new SkipperTranings();
                        $training->institute = $instituteId;
                        $training->program_name =
                            $tranings['program_name'][$index] ?? '';
                        $training->training_period =
                            $tranings['training_period'][$index] ?? '';
                        $training->date_certified =
                            $tranings['date_certified'][$index] ?? null;
                        $training->skipper_id = $model->id;
                        $training->save();
                    }

                    CommonService::addApprovalLog(
                        $this->processType,
                        'Submitted',
                        'Submitted',
                        $model->id
                    );

                    return $this->redirect([
                        '/skipper/view',
                        'token' => SecurityHelper::encryptId(
                            Skipper::class,
                            $model->id
                        ),
                    ]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'fishermanList' => $fishermanList,
            'educationQualification' => $educationQualification,
            'districtList' => $districtList,
            'skipperTranings' => $skipperTranings,
            'instituteList' => $instituteList,
            'renew' => false,
        ]);
    }
    public function actionRenew(
    string $token
) {
    if (
        !UserTypeUtil::hasType(
            Constant::FISHERMAN
        )
    ) {
        CommonService::validatePermission(
            $this,
            'SkipperController-renew'
        );
    }

    /*
     * This is the original Skipper record token.
     */
    $originalId =
        SecurityHelper::decryptId(
            $token,
            Skipper::class
        );

    $originalModel =
        $this->findModel($originalId);

    $this->enforceSkipperOwnership(
        $originalModel
    );  

        $model = new SkipperRenew();
        $model->setAttributes(
            $originalModel->getAttributes(),
            false
        );
        $model->id = null;
        $model->setIsNewRecord(true);
        $model->renew = 1;
        $model->status = 1;
        $model->created = date('Y-m-d H:i:s');
        $model->approved_time = null;
        $model->approval_stage = (string) Constant::DO;

        if ($model->hasAttribute('renew_id')) {
            $model->renew_id = $originalModel->id;
        }

        $skipperTranings = new SkipperTranings();
        $fishermanList = ArrayHelper::map(
            ProfileFisherman::find()
                ->where(['status' => Constant::Active])
                ->asArray()
                ->all(),
            'id',
            'first_name'
        );
        $educationQualification = ArrayHelper::map(
            MEducatinalQualification::find()
                ->where(['status' => 1])
                ->asArray()
                ->all(),
            'id',
            'description'
        );
        $districtList = ArrayHelper::map(
            MFiDistrict::find()
                ->where(['status' => 1])
                ->asArray()
                ->all(),
            'id',
            'name'
        );
        $instituteList = ArrayHelper::map(
            MTraningInstitutes::find()
                ->where(['status' => 1])
                ->asArray()
                ->all(),
            'id',
            'name'
        );

        if ($this->request->isPost) {
            if (
                !UserTypeUtil::hasType(Constant::FISHERMAN)
                && !Util::editPermission()
            ) {
                throw new ForbiddenHttpException(
                    Yii::t(
                        'app',
                        'You are not allowed to perform this action.'
                    )
                );
            }

            $model->load($this->request->post());

            $model->id = null;
            $model->setIsNewRecord(true);
            $model->fisherman_id = $originalModel->fisherman_id;
            $model->skipper_uid = $originalModel->skipper_uid;
            $model->renew = 1;
            $model->status = 1;
            $model->created = date('Y-m-d H:i:s');
            $model->approved_time = null;
            $model->approval_stage = (string) Constant::DO;

            if ($model->hasAttribute('renew_id')) {
                $model->renew_id = $originalModel->id;
            }

            if ($model->save()) {
                CommonService::addApprovalLog(
                    'SKIPPER_LICENCE_RENEW',
                    'Submitted',
                    'Renew request',
                    $model->id
                );

                Yii::$app->session->setFlash(
                    'success',
                    Yii::t(
                        'app',
                        'Skipper licence renewal submitted successfully.'
                    )
                );

                if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
                    return $this->goHome();
                }

                return $this->redirect([
                    '/skipper-renew/view',
                    'token' => SecurityHelper::encryptId(
                        SkipperRenew::class,
                        $model->id
                    ),
                ]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'fishermanList' => $fishermanList,
            'educationQualification' => $educationQualification,
            'districtList' => $districtList,
            'skipperTranings' => $skipperTranings,
            'instituteList' => $instituteList,
            'renew' => true,
        ]);
    }

    /**
     * Deletes an existing Skipper model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnauthorizedHttpException
     */
    public function actionDelete(string $token)
    {
        SecurityHelper::decryptId(
            $token,
            Skipper::class
        );

        throw new UnauthorizedHttpException(
            Yii::t(
                'app',
                'You do not have permission to run this operation.'
            )
        );
    }

    /**
     * Finds the Skipper model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Skipper the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Skipper
    {
        $model = Skipper::findOne(['id' => $id]);

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested record was not found.')
        );
    }
    public function actionPayment(string $token)
    {
        CommonService::validatePermission(
            $this,
            'SkipperController-payment'
        );

        $id = SecurityHelper::decryptId(
            $token,
            Skipper::class
        );

        $skipper = $this->findModel($id);
        $this->enforceSkipperOwnership($skipper);

        $workflow = MApprovalWorkflow::findOne([
            'type' => $this->processType,
        ]);

        if ($workflow === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Approval workflow was not found.')
            );
        }

        $paymentLog = new PaymentLog();
        $paymentLog->type = $this->processType;
        $paymentLog->process_id = $skipper->id;
        $paymentLog->status = 1;

        $paymentTypes = SubPaymentTypes::find()
            ->where(['payment_type_id' => $workflow->payment_type])
            ->asArray()
            ->all();

        foreach ($paymentTypes as $item) {
            if (($item['Code'] ?? '') === 'SKL') {
                $paymentLog->amount = $item['Amount'] ?? null;
                break;
            }
        }

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t(
                        'app',
                        'You are not allowed to perform this action.'
                    )
                );
            }

            if ($paymentLog->load($this->request->post())) {
                $paymentLog->type = $this->processType;
                $paymentLog->process_id = $skipper->id;
                $paymentLog->status = 1;

                $file = UploadedFile::getInstance(
                    $paymentLog,
                    'file'
                );

                if ($file !== null) {
                    $fileName = $skipper->id
                        . $this->processType
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
                            $this->processType,
                            'Paid',
                            'Marked as Paid, Payment Approved',
                            $skipper->id
                        );
                        CommonService::markAsPaid(
                            $skipper,
                            $this->processType
                        );
                    } else {
                        CommonService::addApprovalLog(
                            $this->processType,
                            'Paid',
                            'Marked as Paid',
                            $skipper->id
                        );
                        $skipper->status = 100;
                    }

                    $skipper->save(false);

                    return $this->redirect([
                        '/skipper/view',
                        'token' => SecurityHelper::encryptId(
                            Skipper::class,
                            $skipper->id
                        ),
                    ]);
                }
            }
        }

        return $this->render('../payment/create', [
            'model' => $paymentLog,
        ]);
    }
    public function actionPaymentApprove()
    {
        throw new UnauthorizedHttpException(
            Yii::t(
                'app',
                'You do not have permission to run this operation.'
            )
        );
    }


    /**
     * Display a Skipper licence.
     *
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     */
    public function actionLicenseView(string $token): string
{
    CommonService::validatePermission(
        $this,
        'SkipperController-license-view'
    );

    $skipperId = SecurityHelper::decryptId(
        $token,
        Skipper::class
    );

    $model = $this->findModel(
        (int) $skipperId
    );

    $this->enforceSkipperOwnership(
        $model
    );

    $officer = $this->getSkipperApprovedOfficer(
        $model
    );

    /*
     * Generate the Fisherman token used by the
     * native-language data update page.
     */
    $fishermanToken = null;

    if (
        !empty($model->fisherman_id)
    ) {
        $fisherman = ProfileFisherman::findOne(
            (int) $model->fisherman_id
        );

        if ($fisherman !== null) {
            $fishermanToken =
                SecurityHelper::encryptId(
                    ProfileFisherman::class,
                    (int) $fisherman->id
                );
        }
    }

    return $this->render(
        'licenseView',
        [
            'model' => $model,
            'pdf' => false,
            'officer' => $officer,
            'imageSrc' =>
                $this->createSkipperImageResolver(
                    false
                ),

            /*
             * Used by the native-language update link.
             */
            'fishermanToken' =>
                $fishermanToken,

            /*
             * Reuse the validated current Skipper token
             * to return to this licence after saving.
             */
            'skipperToken' =>
                $token,
        ]
    );
}

    /**
     * Download a Skipper licence PDF.
     *
     * @throws CrossReferenceException
     * @throws MpdfException
     * @throws InvalidConfigException
     * @throws PdfParserException
     * @throws UnauthorizedHttpException
     * @throws PdfTypeException
     * @throws NotFoundHttpException
     */


    public function actionLicenseDownload(string $token): string
{
    CommonService::validatePermission(
        $this,
        'SkipperController-license-download'
    );

    ini_set('pcre.backtrack_limit', '5000000');
    ini_set('pcre.recursion_limit', '1000000');

    $id = SecurityHelper::decryptId(
        $token,
        Skipper::class
    );

    $model = $this->findModel($id);

    $this->enforceSkipperOwnership($model);

    $isCompletedPrintable =
        $model->approval_stage === 'Completed'
        && UserTypeUtil::hasType(Constant::PRINT);

    $viewFile = $isCompletedPrintable
        ? 'license'
        : 'license-temp';

    $officer = $this->getSkipperApprovedOfficer(
        $model
    );

    /*
     * PDF mode should return local file:/// image paths.
     */
    $imageSrc = $this->createSkipperImageResolver(
        true
    );

    $content = $this->renderPartial(
        $viewFile,
        [
            'model' => $model,
            'pdf' => true,
            'officer' => $officer,
            'imageSrc' => $imageSrc,
        ]
    );

    /*
     * Create the PDF filename.
     */
    $fishermanUid = $model->fisherman
        ? $model->fisherman->fisherman_uid
        : null;

    $skipperNumber = trim(
        (string) (
            $model->skipper_uid
            ?: $fishermanUid
            ?: $model->id
        )
    );

    $safeFilename = preg_replace(
        '/[^A-Za-z0-9_-]+/',
        '_',
        $skipperNumber
    );

    if (
        $safeFilename === null
        || $safeFilename === ''
    ) {
        $safeFilename = (string) $model->id;
    }

    $filename =
        'Skipper-License-'
        . $safeFilename
        . '.pdf';

    /*
     * Create the mPDF temporary directory.
     */
    $tempDir = Yii::getAlias(
        '@runtime/mpdf'
    );

    if (
        !is_dir($tempDir)
        && !mkdir(
            $tempDir,
            0775,
            true
        )
        && !is_dir($tempDir)
    ) {
        throw new \RuntimeException(
            'Unable to create the mPDF temporary directory.'
        );
    }

    if (!is_writable($tempDir)) {
        throw new \RuntimeException(
            'The mPDF temporary directory is not writable: '
            . $tempDir
        );
    }

    /*
     * These files must exist inside backend/web:
     *
     * - NotoSansTamil.ttf
     * - DLSarala.ttf
     */
    $fontDir = Yii::getAlias(
        '@backend/web'
    );

    $requiredFontFiles = [
        'NotoSansTamil.ttf',
        'DLSarala.ttf',
    ];

    foreach ($requiredFontFiles as $fontFile) {
        $fontPath =
            $fontDir
            . DIRECTORY_SEPARATOR
            . $fontFile;

        if (
            !is_file($fontPath)
            || !is_readable($fontPath)
        ) {
            throw new \RuntimeException(
                'PDF font file is missing or unreadable: '
                . $fontPath
            );
        }
    }

    /*
     * Load the default mPDF font configuration.
     */
    $defaultConfig = (
        new ConfigVariables()
    )->getDefaults();

    $fontDirs =
        $defaultConfig['fontDir'] ?? [];

    $defaultFontConfig = (
        new FontVariables()
    )->getDefaults();

    $fontData =
        $defaultFontConfig['fontdata'] ?? [];

    /*
     * Create mPDF using the same font configuration
     * as the working send-to-print action.
     */
    $mpdf = new Mpdf([
        'mode' => 'utf-8',

        'format' => $isCompletedPrintable
            ? [264, 167]
            : 'A4',

        'orientation' => 'P',

        'margin_left' =>
            $isCompletedPrintable ? 0 : 10,

        'margin_right' =>
            $isCompletedPrintable ? 0 : 10,

        'margin_top' =>
            $isCompletedPrintable ? 0 : 10,

        'margin_bottom' =>
            $isCompletedPrintable ? 0 : 10,

        'tempDir' => $tempDir,

        'fontDir' => array_merge(
            $fontDirs,
            [
                $fontDir,
            ]
        ),

        'fontdata' => $fontData + [
            /*
             * Tamil font.
             */
            'notosanstamil' => [
                'R' => 'NotoSansTamil.ttf',
                'B' => 'NotoSansTamil.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75,
            ],

            /*
             * Sinhala font.
             */
            'dlsarala' => [
                'R' => 'DLSarala.ttf',
                'B' => 'DLSarala.ttf',
            ],
        ],

        'default_font' => 'notosanstamil',

        'autoScriptToLang' => true,
        'autoLangToFont' => true,
    ]);

    /*
     * Keep true during testing.
     * Change to false after confirming all images.
     */
    $mpdf->showImageErrors = true;

    /*
     * Register the same font classes used in the
     * working print PDF.
     */
    $fontCss = '
        .sinhala-text {
            font-family: dlsarala !important;
            font-weight: bold;
            line-height: 1.2;
        }

        .tamil-text {
            font-family: notosanstamil !important;
            font-weight: bold;
            line-height: 1.3;
        }
    ';

    $mpdf->WriteHTML(
        $fontCss,
        HTMLParserMode::HEADER_CSS
    );

    $mpdf->SetTitle(
        'Skipper License - '
        . $skipperNumber
    );

    $mpdf->SetAuthor(
        Yii::$app->name
    );

    $mpdf->WriteHTML(
        $content,
        HTMLParserMode::HTML_BODY
    );

    return $mpdf->Output(
        $filename,
        Destination::INLINE
    );
}

    private function enforceSkipperOwnership(
        Skipper $model
    ): void {
        if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
            return;
        }

        $profileId = (int) (
            Yii::$app->user->identity->profile_id ?? 0
        );

        if (
            $profileId <= 0
            || (int) $model->fisherman_id !== $profileId
        ) {
            throw new NotFoundHttpException(
                Yii::t('app', 'The requested record was not found.')
            );
        }
    }


    private function getSkipperApprovedOfficer(
        Skipper $model
    ): array {
        $officer = CommonService::getApprovedOfficer(
            $model->id,
            'SKIPPER_LICENCE'
        );

        if ($officer instanceof \yii\db\ActiveRecord) {
            $officer = $officer->toArray();
        }

        return is_array($officer) ? $officer : [];
    }
    private function createSkipperImageResolver(
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

            $configuredUploadRoot = rtrim(
                (string) Constant::$FILE_UPLOAD_PATH,
                '/\\'
            );

            $uploadRoot = realpath($configuredUploadRoot);

            if ($uploadRoot === false) {
                Yii::error(
                    [
                        'message' => 'Upload root does not exist.',
                        'path' => $configuredUploadRoot,
                    ],
                    'skipper-pdf-image'
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
                    [
                        'message' =>
                            'Skipper PDF image is missing or unreadable.',
                        'path' => $requestedPath,
                    ],
                    'skipper-pdf-image'
                );

                return '';
            }

            $normalizedRoot = rtrim(
                str_replace('\\', '/', $uploadRoot),
                '/'
            );
            $normalizedRealPath = str_replace(
                '\\',
                '/',
                $realPath
            );

            if (
                $normalizedRealPath !== $normalizedRoot
                && !str_starts_with(
                    $normalizedRealPath,
                    $normalizedRoot . '/'
                )
            ) {
                return '';
            }

            return 'file:///'
                . ltrim($normalizedRealPath, '/');
        };
    }


    /**
     * Return active training programs for a selected institute.
     */
    /**
 * Load training programs for the selected institute.
 */
public function actionLoadPrograms(
    int $institute_id = 0
): array {
    Yii::$app->response->format =
        Response::FORMAT_JSON;

    if ($institute_id <= 0) {
        return [
            'success' => false,
            'message' => 'Invalid institute ID.',
            'results' => [],
        ];
    }

    try {
        $db = Yii::$app->db;

        /*
         * Add your actual table name here if it is
         * different from these names.
         */
        $candidateTables = [
            'training_programs',
            'm_traning_institute_programs',
            'm_traning_programs',
            'training-programs',
        ];

        $tableName = null;
        $tableSchema = null;

        foreach ($candidateTables as $candidateTable) {
            $schema = $db->schema->getTableSchema(
                $candidateTable,
                true
            );

            if ($schema !== null) {
                $tableName = $candidateTable;
                $tableSchema = $schema;
                break;
            }
        }

        if ($tableName === null) {
            return [
                'success' => false,
                'message' =>
                    'Training program table was not found.',
                'checkedTables' => $candidateTables,
                'results' => [],
            ];
        }

        $columns = array_keys(
            $tableSchema->columns
        );

        $idColumn = null;

        foreach (
            [
                'id',
                'program_id',
                'training_program_id',
            ] as $column
        ) {
            if (in_array($column, $columns, true)) {
                $idColumn = $column;
                break;
            }
        }

        $instituteColumn = null;

        foreach (
            [
                'institute_id',
                'institute',
                'training_institute_id',
                'traning_institute_id',
            ] as $column
        ) {
            if (in_array($column, $columns, true)) {
                $instituteColumn = $column;
                break;
            }
        }

        $nameColumn = null;

        foreach (
            [
                'name',
                'program_name',
                'description',
                'title',
            ] as $column
        ) {
            if (in_array($column, $columns, true)) {
                $nameColumn = $column;
                break;
            }
        }

        if (
            $idColumn === null
            || $instituteColumn === null
            || $nameColumn === null
        ) {
            return [
                'success' => false,
                'message' =>
                    'Required columns were not detected.',
                'table' => $tableName,
                'availableColumns' => $columns,
                'detectedColumns' => [
                    'id' => $idColumn,
                    'institute' => $instituteColumn,
                    'name' => $nameColumn,
                ],
                'results' => [],
            ];
        }

        /*
         * Count rows before applying status filtering.
         */
        $baseQuery = (new Query())
            ->from($tableName)
            ->where([
                $instituteColumn => $institute_id,
            ]);

        $rawCount = (int) (
            clone $baseQuery
        )->count();

        /*
         * Find the status values stored in this table.
         */
        $statusValues = [];

        if (in_array('status', $columns, true)) {
            $statusValues = (new Query())
                ->select([
                    'status',
                    'total' => new \yii\db\Expression(
                        'COUNT(*)'
                    ),
                ])
                ->from($tableName)
                ->where([
                    $instituteColumn =>
                        $institute_id,
                ])
                ->groupBy(['status'])
                ->all();
        }

        $query = (new Query())
            ->select([
                'id' => $idColumn,
                'text' => $nameColumn,
            ])
            ->from($tableName)
            ->where([
                $instituteColumn =>
                    $institute_id,
            ]);

        /*
         * Master tables in this module generally use
         * status = 1, not Constant::Active.
         */
        if (in_array('status', $columns, true)) {
            $query->andWhere([
                'status' => 1,
            ]);
        }

        $results = $query
            ->orderBy([
                $nameColumn => SORT_ASC,
            ])
            ->all();

        return [
            'success' => true,
            'table' => $tableName,
            'selectedInstituteId' =>
                $institute_id,
            'detectedColumns' => [
                'id' => $idColumn,
                'institute' => $instituteColumn,
                'name' => $nameColumn,
            ],
            'rawCountBeforeStatusFilter' =>
                $rawCount,
            'statusValues' => $statusValues,
            'resultCount' => count($results),
            'results' => $results,
        ];
    } catch (\Throwable $exception) {
        Yii::error(
            [
                'message' =>
                    $exception->getMessage(),
                'instituteId' =>
                    $institute_id,
                'file' =>
                    $exception->getFile(),
                'line' =>
                    $exception->getLine(),
            ],
            'skipper-load-programs'
        );

        return [
            'success' => false,
            'message' =>
                $exception->getMessage(),
            'results' => [],
        ];
    }
}
    public static function getStacs()
    {

        $where = [];
        $wherePending = [];

        if (UserTypeUtil::hasType(Constant::FI)) {
            $wherePending = ["fisheries_division" => Yii::$app->session->get("officer_division"), 'approval_stage' => Constant::FI];
            $where = ["fisheries_division" => Yii::$app->session->get("officer_division")];
        }
        if (UserTypeUtil::hasType(Constant::DO)) {
            $wherePending = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DO];
            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::AD)) {
            $wherePending = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::AD];
            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DM)) {
            $wherePending = ['approval_stage' => Constant::DM];
        }
        if (UserTypeUtil::hasType(Constant::DFI)) {
            $wherePending = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DFI];
            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DG)) {
            $wherePending = ['approval_stage' => Constant::DG];
        }
        $countPending = Skipper::find()->where(['status' => Constant::Pending])->andWhere($wherePending)->count();
        $countActive = Skipper::find()->where(['status' => Constant::Active])->andWhere($where)->count();
        $countFinalApproval = Skipper::find()->where(['status' => Constant::FinalApprovalPending])->andWhere($where)->count();
        $countExpired = Skipper::find()->where(['status' => Constant::Expired])->andWhere($where)->count();

        return [
            "countPending" => $countPending,
            "countActive" => $countActive,
            "countFinalApproval" => $countFinalApproval,
            "countExpired" => $countExpired,
        ];

    }

    public
    function actionSearch($q = null, $id = null)
    {
        $officerProfile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select(["skipper.id", "skipper.skipper_uid", "profile_fisherman.nic", "profile_fisherman.fisherman_uid"])
                ->from('skipper')
                ->innerJoin("profile_fisherman", "profile_fisherman.id=skipper.fisherman_id")
                ->where(['like', 'skipper_uid', $q])
                ->orWhere(['like', 'profile_fisherman.nic', $q])
                ->andWhere(["skipper.status" => Constant::Active])
                ->limit(20);


            $command = $query->createCommand();
            $data = $command->queryAll();
            $dataFormated = [];
            foreach ($data as $datum) {
                $dataFormated[] = [
                    "id" => $datum['id'],
                    "text" => "ID-" . $datum['fisherman_uid'] . " - NIC: " . $datum['nic']
                ];
            }
//print_r($dataFormated);exit();
            $out['results'] = array_values($dataFormated);
        }
        return $out;
    }
    public function actionPrinted(string $token)
    {
        if (!Util::editPermission()) {
            throw new ForbiddenHttpException(
                Yii::t(
                    'app',
                    'You are not allowed to perform this action.'
                )
            );
        }

        $id = SecurityHelper::decryptId(
            $token,
            Skipper::class
        );

        $model = $this->findModel($id);
        $this->enforceSkipperOwnership($model);
        $model->printed = 1;

        if (!$model->save(false, ['printed'])) {
            throw new \yii\web\ServerErrorHttpException(
                Yii::t('app', 'The print status could not be updated.')
            );
        }

        return $this->redirect([
            '/skipper/license-view',
            'token' => SecurityHelper::encryptId(
                Skipper::class,
                $model->id
            ),
        ]);
    }

    public function actionAddskipper($fisherman_id = null)
{
    // CommonService::validatePermission($this, "SkipperController-create");
    if (!UserTypeUtil::hasType(Constant::ADMIN)) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to access this page.');
        }
    /*
     * If fisherman_id is passed, check whether skipper already exists.
     */
    if (!empty($fisherman_id)) {

        $existingSkipper = Skipper::find()
            ->where(['fisherman_id' => $fisherman_id])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($existingSkipper) {
            // Fisherman already has skipper record, load existing skipper data
            $model = $existingSkipper;
        } else {
            // Fisherman does not have skipper record, create new skipper form
            $model = new Skipper();
            $model->fisherman_id = $fisherman_id;
        }

    } else {
        $model = new Skipper();
    }

    $skipperTranings = new SkipperTranings();

    $fishermanList = ArrayHelper::map(
        ProfileFisherman::find()
            ->where(['status' => Constant::Active])
            ->asArray()
            ->all(),
        "id",
        "first_name"
    );

    $educationQualification = ArrayHelper::map(
        MEducatinalQualification::find()
            ->where(['status' => 1])
            ->asArray()
            ->all(),
        "id",
        "description"
    );

    $districtList = ArrayHelper::map(
        MFiDistrict::find()
            ->where(["status" => 1])
            ->asArray()
            ->all(),
        'id',
        "name"
    );

    $divisionList = [];

    if (!empty($model->fisheries_district)) {
        $divisionList = ArrayHelper::map(
            MDivision::find()
                ->where([
                    'district_id' => $model->fisheries_district,
                    'status' => 1
                ])
                ->asArray()
                ->all(),
            'id',
            'name'
        );
    }

    $instituteList = ArrayHelper::map(
        MTraningInstitutes::find()
            ->where(["status" => 1])
            ->asArray()
            ->all(),
        'id',
        "name"
    );

    /*
     * Load existing training data if skipper already exists.
     */
    $existingTrainings = [];

    if (!$model->isNewRecord) {
        $existingTrainings = SkipperTranings::find()
            ->where(['skipper_id' => $model->id])
            ->all();
    }

    if (Yii::$app->request->isPost) {

        $post = Yii::$app->request->post();

        if ($model->load($post)) {

            /*
             * Extra safety:
             * If submitted fisherman already has a skipper record,
             * use that record instead of creating duplicate.
             */
            if ($model->isNewRecord && !empty($model->fisherman_id)) {

                $existingSkipper = Skipper::find()
                    ->where(['fisherman_id' => $model->fisherman_id])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();

                if ($existingSkipper) {
                    $model = $existingSkipper;
                    $model->load($post);
                }
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {

                $currentDateTime = date('Y-m-d H:i:s');

                if (!empty($model->approved_time)) {
                    $approvedTime = date('Y-m-d H:i:s', strtotime($model->approved_time));
                } else {
                    $approvedTime = $currentDateTime;
                }

                $expireDate = date('Y-m-d H:i:s', strtotime($approvedTime . ' +5 years -1 day'));

                $model->status = Constant::Active;
                $model->approval_stage = 'Completed';

                if ($model->isNewRecord) {
                    $model->created = $currentDateTime;
                    $model->renew = 0;
                    $model->renew_id = null;
                    $model->printed = 0;
                }

                $model->approved_time = $approvedTime;
                $model->expire_date = $expireDate;

                if (!$model->save()) {
                    throw new \Exception('Skipper save failed: ' . json_encode($model->errors));
                }

                /*
                 * Generate skipper_uid only if empty.
                 */
                if (empty($model->skipper_uid)) {

                    $district = MFiDistrict::findOne($model->fisheries_district);

                    if (!$district) {
                        throw new \Exception('Invalid fisheries district selected.');
                    }

                    $districtCode = $district->code ?? '';

                    if (empty($districtCode)) {
                        throw new \Exception('Fisheries district code is missing.');
                    }

                    $model->skipper_uid = 'SK' . $model->id . $districtCode;

                    if (!$model->save(false, ['skipper_uid'])) {
                        throw new \Exception('Failed to update skipper UID.');
                    }
                }

                /*
                 * Re-save training details.
                 */
                SkipperTranings::deleteAll(['skipper_id' => $model->id]);

                $trainingRows = $post['TrainingDetails'] ?? [];

                foreach ($trainingRows as $trainingRow) {

                    if (
                        empty($trainingRow['institute']) ||
                        empty($trainingRow['program_name']) ||
                        empty($trainingRow['training_period']) ||
                        empty($trainingRow['date_certified']) ||
                        $trainingRow['program_name'] === 'undefined' ||
                        $trainingRow['training_period'] === 'undefined' ||
                        $trainingRow['date_certified'] === 'undefined'
                    ) {
                        continue;
                    }

                    $trainingModel = new SkipperTranings();

                    $trainingModel->skipper_id = $model->id;
                    $trainingModel->institute = $trainingRow['institute'];
                    $trainingModel->program_name = $trainingRow['program_name'];
                    $trainingModel->training_period = $trainingRow['training_period'];
                    $trainingModel->date_certified = $trainingRow['date_certified'];

                    if ($trainingModel->hasAttribute('status')) {
                        $trainingModel->status = Constant::Active;
                    }

                    if (!$trainingModel->save()) {
                        throw new \Exception('Training save failed: ' . json_encode($trainingModel->errors));
                    }
                }

                $transaction->commit();

                Yii::$app->session->setFlash('success', 'Skipper saved successfully.');

                return $this->redirect(['addskipper']);

            } catch (\Exception $e) {

                $transaction->rollBack();

                Yii::$app->session->setFlash(
                    'error',
                    'Failed to save skipper. Error: ' . $e->getMessage()
                );
            }
        }
    }

    return $this->render('addskipper', [
        'model' => $model,
        'fishermanList' => $fishermanList,
        'educationQualification' => $educationQualification,
        'districtList' => $districtList,
        'divisionList' => $divisionList,
        'skipperTranings' => $skipperTranings,
        'instituteList' => $instituteList,
        'existingTrainings' => $existingTrainings,
        'renew' => false,
    ]);
}
}