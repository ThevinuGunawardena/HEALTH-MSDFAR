<?php

namespace backend\controllers;

use backend\components\RecordLookupRateLimit;
use backend\components\SecurityHelper;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Files;
use backend\models\MApprovalWorkflow;
use backend\models\MEducatinalQualification;
use backend\models\MFiDistrict;
use backend\models\MRequeredDocuments;
use backend\models\MTraningInstitutes;
use backend\models\PaymentLog;
use backend\models\ProfileFisherman;
use backend\models\ProfileOfficer;
use backend\models\Skipper;
use backend\models\SkipperRenew;
use backend\models\SkipperRenewSearch;
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
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * SkipperRenewController implements the CRUD actions for SkipperRenew model.
 */
class SkipperRenewController extends Controller
{
    public $processType = "SKIPPER_LICENCE_RENEW";

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
        $countPending = SkipperRenew::find()->where(['status' => Constant::Pending])->andWhere($wherePending)->count();
        $countActive = SkipperRenew::find()->where(['status' => Constant::Active])->andWhere($where)->count();
        $countFinalApproval = SkipperRenew::find()->where(['status' => Constant::FinalApprovalPending])->andWhere($where)->count();
        $countExpired = SkipperRenew::find()->where(['status' => Constant::Expired])->andWhere($where)->count();

        return [
            "countPending" => $countPending,
            "countActive" => $countActive,
            "countFinalApproval" => $countFinalApproval,
            "countExpired" => $countExpired,
        ];

    }

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
            'payment',
            'license-view',
            'license-download',
            'printed',
            'delete',
        ];

        $behaviors['recordLookupBurstLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $protectedActions,
            'bucketName' => 'skipper-renew-burst',
            'limit' => 30,
            'window' => 60,
        ];

        $behaviors['recordLookupSustainedLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $protectedActions,
            'bucketName' => 'skipper-renew-sustained',
            'limit' => 200,
            'window' => 900,
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

        $searchModel = new SkipperRenewSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
            SkipperRenew::class
        );

        $model = $this->findModel($id);
        $this->enforceSkipperRenewOwnership($model);

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

            if ($model->save()) {
                if ($model->approval_stage === 'Completed') {
                    $originalSkipper = Skipper::find()
                        ->where(['skipper_uid' => $model->skipper_uid])
                        ->one();

                    if ($originalSkipper !== null) {
                        $originalCreated = $originalSkipper->created;
                        $data = $model->attributes;
                        unset($data['id']);
                        $originalSkipper->setAttributes($data, false);
                        $originalSkipper->created = $originalCreated;

                        if ($originalSkipper->hasAttribute('renew_id')) {
                            $originalSkipper->renew_id = $model->id;
                        }

                        $originalSkipper->save(false);
                    }
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
     * Finds the Skipper model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Skipper the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): SkipperRenew
    {
        $model = SkipperRenew::findOne(['id' => $id]);

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested record was not found.')
        );
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
            SkipperRenew::class
        );

        $model = $this->findModel($id);
        $this->enforceSkipperRenewOwnership($model);

        $trustedValues = [
            'id' => $model->id,
            'fisherman_id' => $model->fisherman_id,
            'skipper_uid' => $model->skipper_uid,
            'renew' => $model->renew,
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
                $model->renew = $trustedValues['renew'];

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
                        '/skipper-renew/view',
                        'token' => SecurityHelper::encryptId(
                            SkipperRenew::class,
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
            SkipperRenew::class
        );

        throw new UnauthorizedHttpException(
            Yii::t(
                'app',
                'You do not have permission to run this operation.'
            )
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
            SkipperRenew::class
        );

        $skipperRenew = $this->findModel($id);
        $this->enforceSkipperRenewOwnership($skipperRenew);

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
        $paymentLog->process_id = $skipperRenew->id;
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
                $paymentLog->process_id = $skipperRenew->id;
                $paymentLog->status = 1;

                $file = UploadedFile::getInstance(
                    $paymentLog,
                    'file'
                );

                if ($file !== null) {
                    $fileName = $skipperRenew->id
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
                            $skipperRenew->id
                        );
                        CommonService::markAsPaid(
                            $skipperRenew,
                            $this->processType
                        );

                        if ($skipperRenew->approval_stage === 'Completed') {
                            $originalSkipper = Skipper::find()
                                ->where([
                                    'skipper_uid' =>
                                        $skipperRenew->skipper_uid,
                                ])
                                ->one();

                            if ($originalSkipper !== null) {
                                $originalCreated = $originalSkipper->created;
                                $data = $skipperRenew->attributes;
                                unset($data['id']);
                                $originalSkipper->setAttributes($data, false);
                                $originalSkipper->created = $originalCreated;

                                if ($originalSkipper->hasAttribute('renew_id')) {
                                    $originalSkipper->renew_id =
                                        $skipperRenew->id;
                                }

                                $originalSkipper->save(false);
                            }
                        }
                    } else {
                        CommonService::addApprovalLog(
                            $this->processType,
                            'Paid',
                            'Marked as Paid',
                            $skipperRenew->id
                        );
                        $skipperRenew->status = 100;
                    }

                    $skipperRenew->save(false);

                    return $this->redirect([
                        '/skipper-renew/view',
                        'token' => SecurityHelper::encryptId(
                            SkipperRenew::class,
                            $skipperRenew->id
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
     * @throws UnauthorizedHttpException
     */
    public function actionLicenseView(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'SkipperController-license-view'
        );

        $id = SecurityHelper::decryptId(
            $token,
            SkipperRenew::class
        );

        $model = $this->findModel($id);
        $this->enforceSkipperRenewOwnership($model);
        $officer = $this->getRenewApprovedOfficer($model);

        return $this->render(
            'licenseView',
            [
                'model' => $model,
                'pdf' => false,
                'officer' => $officer,
                'imageSrc' =>
                    $this->createSkipperImageResolver(false),
            ]
        );
    }
    /**
     * @throws CrossReferenceException
     * @throws MpdfException
     * @throws InvalidConfigException
     * @throws PdfParserException
     * @throws UnauthorizedHttpException
     * @throws PdfTypeException
     */
    public function actionLicenseDownload(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'SkipperController-license-download'
        );

        $id = SecurityHelper::decryptId(
            $token,
            SkipperRenew::class
        );

        $model = $this->findModel($id);
        $this->enforceSkipperRenewOwnership($model);

        $isCompletedPrintable =
            $model->approval_stage === 'Completed'
            && UserTypeUtil::hasType(Constant::PRINT);

        $viewFile = $isCompletedPrintable
            ? 'license'
            : 'license-temp';

        $officer = $this->getRenewApprovedOfficer($model);
        $imageSrc = $this->createSkipperImageResolver(true);

        $content = $this->renderPartial(
            $viewFile,
            [
                'model' => $model,
                'pdf' => true,
                'officer' => $officer,
                'imageSrc' => $imageSrc,
            ]
        );

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => $isCompletedPrintable
                ? [264, 167]
                : Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'marginLeft' => $isCompletedPrintable ? 0 : 10,
            'marginTop' => $isCompletedPrintable ? 0 : 10,
            'marginRight' => $isCompletedPrintable ? 0 : 10,
            'marginBottom' => $isCompletedPrintable ? 0 : 10,
            'filename' =>
                'skipper-renew-license-'
                . $model->id
                . '.pdf',
        ]);

        $mpdf = $pdf->getApi();
        $mpdf->showImageErrors = false;

        return $pdf->render();
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
            SkipperRenew::class
        );

        $model = $this->findModel($id);
        $this->enforceSkipperRenewOwnership($model);
        $model->printed = 1;

        if (!$model->save(false, ['printed'])) {
            throw new \yii\web\ServerErrorHttpException(
                Yii::t('app', 'The print status could not be updated.')
            );
        }

        return $this->redirect([
            '/skipper-renew/license-view',
            'token' => SecurityHelper::encryptId(
                SkipperRenew::class,
                $model->id
            ),
        ]);
    }


    /**
 * Returns the correct image source for browser or PDF rendering.
 *
 * Browser:
 * /files/static/example.jpg
 *
 * PDF:
 * file:///var/mountpoint/uploads/static/example.jpg
 */
    private function enforceSkipperRenewOwnership(
        SkipperRenew $model
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

    private function getRenewApprovedOfficer(
        SkipperRenew $model
    ): array {
        $officer = CommonService::getApprovedOfficer(
            $model->id,
            'SKIPPER_LICENCE_RENEW'
        );

        if (empty($officer)) {
            $officer = CommonService::getApprovedOfficer(
                $model->id,
                'SKIPPER_LICENCE'
            );
        }

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
                    'skipper-renew-pdf-image'
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
                            'Skipper renewal PDF image is missing or unreadable.',
                        'path' => $requestedPath,
                    ],
                    'skipper-renew-pdf-image'
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
}