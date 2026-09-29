<?php

namespace backend\controllers;

use backend\components\Controller;
use backend\components\RecordLookupRateLimit;
use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumberOwnersLog;
use backend\models\BoatNumbers;
use backend\models\BoatNumbersSearch;
use backend\models\Files;
use backend\models\FishermanRegisterdBoat;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\MApprovalWorkflow;
use backend\models\MBoatTypes;
use backend\models\MFiDistrict;
use backend\models\MOngoingNumber;
use backend\models\MRequeredDocuments;
use backend\models\PaymentLog;
use backend\models\ProfileFisherman;
use backend\models\ProfileOfficer;
use backend\models\ProfileYard;
use backend\models\SubPaymentTypes;
use backend\services\CommonService;
use backend\services\Util;
use kartik\mpdf\Pdf;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * BoatNumbersController implements the CRUD actions for BoatNumbers model.
 */
class BoatNumbersController extends Controller
{
    public $processType = "BOAT_NUMBER";

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
                    'payment-approve' => ['POST'],
                ]
            ),
        ];

        $recordActions = [
            'index',
            'view',
            'create',
            'update',
            'delete',
            'payment',
            'license-view',
            'license-download',
            'add-boat-number-manual',
        ];

        $behaviors['boatNumberBurstLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $recordActions,
            'bucketName' => 'boat-number-burst',
            'limit' => 30,
            'window' => 60,
        ];

        $behaviors['boatNumberSustainedLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $recordActions,
            'bucketName' => 'boat-number-sustained',
            'limit' => 200,
            'window' => 900,
        ];

        $lookupActions = [
            'boat-search',
            'boat-global-search',
            'search',
            'boatdetails',
        ];

        $behaviors['boatNumberLookupLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $lookupActions,
            'bucketName' => 'boat-number-lookups',
            'limit' => 120,
            'window' => 60,
        ];

        return $behaviors;
    }

    /**
     * Lists all BoatNumbers models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this, "BoatNumbersController-list");

        $searchModel = new BoatNumbersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BoatNumbers model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(string $token)
    {
        CommonService::validatePermission(
            $this,
            'BoatNumbersController-office-view'
        );

        $id = SecurityHelper::decryptId(
            $token,
            BoatNumbers::class
        );

        $model = $this->findModel($id);
        $this->enforceBoatOwnership($model);

        $workflow = MApprovalWorkflow::findOne([
            'type' => $this->processType,
        ]);

        if ($workflow === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Approval workflow was not found.')
            );
        }

        $activeRecords = [];

        if ((int) $model->status === (int) Constant::Active) {
            $activeRecords = BoatNumberOwnersLog::find()
                ->where([
                    'boat_number' => $model->boat_number,
                ])
                ->asArray()
                ->all();
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
                        'You are not allowed to update this record.'
                    )
                );
            }

            $ongoingNumber = null;

            $model = CommonService::markApprovalStage(
                $approvalFlow,
                $model,
                $model->id,
                $this->processType
            );

            if (
                trim((string) $model->boat_number) === ''
                && in_array(
                    (string) $model->approval_stage,
                    ['Approved', 'Completed'],
                    true
                )
            ) {
                $ongoingNumber = MOngoingNumber::findOne([
                    'type' => $model->boat_type,
                ]);

                if (
                    $ongoingNumber === null
                    || $ongoingNumber->number === null
                    || $ongoingNumber->number === ''
                ) {
                    throw new NotFoundHttpException(
                        Yii::t(
                            'app',
                            'Ongoing number was not found for this category. Please contact the IT department.'
                        )
                    );
                }

                $districtCode = (string) (
                    $model->owner0?->district0?->code
                    ?? ''
                );

                $boatTypeCode = (string) (
                    $model->boatType?->code
                    ?? ''
                );

                if ($districtCode === '' || $boatTypeCode === '') {
                    throw new BadRequestHttpException(
                        Yii::t(
                            'app',
                            'Boat owner district or boat category is incomplete.'
                        )
                    );
                }

                if ((int) $ongoingNumber->number === 9999) {
                    $letter = (string) $ongoingNumber->letter;
                    $finalLetter = ++$letter;
                    $finalNumber = sprintf('%04d', 0);
                } else {
                    $finalLetter = (string) $ongoingNumber->letter;
                    $finalNumber = sprintf(
                        '%04d',
                        (int) $ongoingNumber->number + 1
                    );
                }

                $model->boat_number =
                    $boatTypeCode
                    . $finalLetter
                    . $finalNumber
                    . $districtCode;
            }

            if ($model->save()) {
                if ($ongoingNumber !== null) {
                    $ongoingNumber->letter = $finalLetter;
                    $ongoingNumber->number = $finalNumber;
                    $ongoingNumber->save(false);
                }

                return $this->redirect([
                    'view',
                    'token' => SecurityHelper::encryptId(
                        BoatNumbers::class,
                        $model->id
                    ),
                ]);
            }
        }

        $paymentHistory = [];

        if (
            in_array(
                (int) $model->status,
                [
                    (int) Constant::PaymentPending,
                    (int) Constant::Active,
                ],
                true
            )
        ) {
            $paymentHistory = PaymentLog::find()
                ->where([
                    'type' => $this->processType,
                    'process_id' => $model->id,
                ])
                ->orderBy(['id' => SORT_DESC])
                ->all();
        }

        $files = Files::find()
            ->where([
                'type' => $workflow->id,
                'process_id' => $model->id,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'token' => $token,
            'approvalHistory' =>
                $approvalFlow['approvalHistory'],
            'showRejectBtn' =>
                $approvalFlow['showRejectBtn'],
            'showApproveBtn' =>
                $approvalFlow['showApproveBtn'],
            'paymentHistory' => $paymentHistory,
            'process' => $this->processType,
            'files' => $files,
            'validated' => $validated,
            'activeRecords' => $activeRecords,
        ]);
    }

    /**
     * Creates a new BoatNumbers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
   public function actionCreate()
{
    CommonService::validatePermission(
        $this,
        'BoatNumbersController-create'
    );

    $model = new BoatNumbers();

    $profileId = (int) (
        Yii::$app->user->identity->profile_id
        ?? 0
    );

    $officerProfile = ProfileOfficer::findOne(
        $profileId
    );

    $fishermanList = ArrayHelper::map(
        ProfileFisherman::find()
            ->where([
                'status' => Constant::Active,
            ])
            ->orderBy([
                'first_name' => SORT_ASC,
                'last_name' => SORT_ASC,
            ])
            ->asArray()
            ->all(),
        'id',
        static function (array $row): string {
            return trim(
                (string) ($row['first_name'] ?? '')
                . ' '
                . (string) ($row['last_name'] ?? '')
            );
        }
    );

    $yardList = ArrayHelper::map(
        ProfileYard::find()
            ->orderBy([
                'name' => SORT_ASC,
            ])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    $districtList = ArrayHelper::map(
        MFiDistrict::find()
            ->where([
                'status' => 1,
            ])
            ->orderBy([
                'name' => SORT_ASC,
            ])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    $boatTypeArray = ArrayHelper::map(
        MBoatTypes::find()
            ->orderBy([
                'code' => SORT_ASC,
            ])
            ->asArray()
            ->all(),
        'id',
        'code'
    );

    if ($officerProfile !== null) {
        $model->fisheries_district =
            $officerProfile->district;
    }

    if ($this->request->isPost) {
        if (!Util::editPermission()) {
            throw new ForbiddenHttpException(
                Yii::t(
                    'app',
                    'You are not allowed to create boat numbers.'
                )
            );
        }

        if (
            $model->load(
                $this->request->post()
            )
        ) {
            /*
             * Reload the owner from the database.
             * Do not trust related data from submitted input.
             */
            $owner = ProfileFisherman::findOne(
                (int) $model->owner
            );

            if ($owner === null) {
                $model->addError(
                    'owner',
                    Yii::t(
                        'app',
                        'The selected boat owner was not found.'
                    )
                );
            } elseif (
                empty($owner->district)
                || empty($owner->division)
            ) {
                $model->addError(
                    'owner',
                    Yii::t(
                        'app',
                        'The selected owner does not have a valid district and division.'
                    )
                );
            } else {
                /*
                 * Set protected values from the database.
                 */
                $model->fisheries_district =
                    (int) $owner->district;

                $model->fisheries_division =
                    (int) $owner->division;

                if (
                    UserTypeUtil::hasType(
                        Constant::ADMIN
                    )
                ) {
                    $model->status =
                        Constant::Active;

                    $model->approval_stage =
                        'Completed';
                } else {
                    $model->status =
                        Constant::Pending;

                    $model->approval_stage =
                        (string) Constant::DO;
                }

                if ($model->save()) {
                    if (
                        !UserTypeUtil::hasType(
                            Constant::ADMIN
                        )
                    ) {
                        CommonService::addApprovalLog(
                            $this->processType,
                            'Submitted',
                            (string) $model->remarks,
                            $model->id
                        );
                    }

                    /*
                     * The token is created only after the
                     * record has been saved and has an ID.
                     */
                    $newToken =
                        SecurityHelper::encryptId(
                            BoatNumbers::class,
                            (int) $model->id
                        );

                    return $this->redirect([
                        '/boat-numbers/view',
                        'token' => $newToken,
                    ]);
                }
            }
        }
    } else {
        $model->loadDefaultValues();

        if ($officerProfile !== null) {
            $model->fisheries_district =
                $officerProfile->district;
        }
    }

    /*
     * Do not pass $token here.
     * No token exists before record creation.
     */
    return $this->render(
        'create',
        [
            'model' => $model,
            'fishermanList' =>
                $fishermanList,
            'yardList' =>
                $yardList,
            'districtList' =>
                $districtList,
            'boatTypeArray' =>
                $boatTypeArray,
        ]
    );
}

    /**
     * Updates an existing BoatNumbers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(string $token)
    {
        CommonService::validatePermission(
            $this,
            'BoatNumbersController-update'
        );

        $id = SecurityHelper::decryptId(
            $token,
            BoatNumbers::class
        );

        $model = $this->findModel($id);
        $this->enforceBoatOwnership($model);

        $yardList = ArrayHelper::map(
            ProfileYard::find()
                ->orderBy(['name' => SORT_ASC])
                ->all(),
            'id',
            'name'
        );

        $districtList = ArrayHelper::map(
            MFiDistrict::find()
                ->where(['status' => 1])
                ->orderBy(['name' => SORT_ASC])
                ->asArray()
                ->all(),
            'id',
            'name'
        );

        $boatTypeArray = ArrayHelper::map(
            MBoatTypes::find()
                ->orderBy(['code' => SORT_ASC])
                ->asArray()
                ->all(),
            'id',
            'code'
        );

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t(
                        'app',
                        'You are not allowed to update this record.'
                    )
                );
            }

            if (
                $model->load($this->request->post())
                && $model->save()
            ) {
                return $this->redirect([
                    'view',
                    'token' => SecurityHelper::encryptId(
                        BoatNumbers::class,
                        $model->id
                    ),
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'token' => $token,
            'yardList' => $yardList,
            'districtList' => $districtList,
            'boatTypeArray' => $boatTypeArray,
        ]);
    }

    /**
     * Deletes an existing BoatNumbers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(string $token): Response
    {
        SecurityHelper::decryptId(
            $token,
            BoatNumbers::class
        );

        throw new ForbiddenHttpException(
            Yii::t(
                'app',
                'Deleting boat-number records is disabled.'
            )
        );
    }

    /**
     * Finds the BoatNumbers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return BoatNumbers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): BoatNumbers
    {
        $model = BoatNumbers::findOne(['id' => $id]);

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'The requested boat-number record was not found.'
            )
        );
    }
    public function actionPayment(string $token)
    {
        CommonService::validatePermission(
            $this,
            'BoatNumbersController-payment'
        );

        $id = SecurityHelper::decryptId(
            $token,
            BoatNumbers::class
        );

        $boat = $this->findModel($id);
        $this->enforceBoatOwnership($boat);

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
        $paymentLog->process_id = $boat->id;
        $paymentLog->status = 1;

        $paymentTypes = SubPaymentTypes::find()
            ->where([
                'payment_type_id' => $workflow->payment_type,
            ])
            ->asArray()
            ->all();

        foreach ($paymentTypes as $item) {
            if (
                (string) ($item['Description'] ?? '')
                === (string) ($boat->boatType?->code ?? '')
            ) {
                $paymentLog->amount = $item['Amount'] ?? null;
                break;
            }
        }

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t(
                        'app',
                        'You are not allowed to update this payment.'
                    )
                );
            }

            if ($paymentLog->load($this->request->post())) {
                $paymentLog->type = $this->processType;
                $paymentLog->process_id = $boat->id;
                $paymentLog->status = 1;

                $savedPaymentPath = null;
                $file = UploadedFile::getInstance(
                    $paymentLog,
                    'file'
                );

                if ($file !== null) {
                    $extension = strtolower(
                        (string) $file->extension
                    );

                    $allowedExtensions = [
                        'jpg',
                        'jpeg',
                        'png',
                        'pdf',
                    ];

                    if (!in_array(
                        $extension,
                        $allowedExtensions,
                        true
                    )) {
                        $paymentLog->addError(
                            'file',
                            Yii::t(
                                'app',
                                'Only JPG, JPEG, PNG and PDF payment files are allowed.'
                            )
                        );
                    } else {
                        $paymentDirectory =
                            rtrim(
                                Constant::$FILE_UPLOAD_PATH,
                                '/\\'
                            )
                            . DIRECTORY_SEPARATOR
                            . 'payment'
                            . DIRECTORY_SEPARATOR;

                        if (
                            !is_dir($paymentDirectory)
                            || !is_writable($paymentDirectory)
                        ) {
                            throw new ServerErrorHttpException(
                                Yii::t(
                                    'app',
                                    'The payment upload directory is unavailable.'
                                )
                            );
                        }

                        $fileName =
                            'BOAT_NUMBER_PAYMENT_'
                            . $boat->id
                            . '_'
                            . date('YmdHis')
                            . '_'
                            . Yii::$app->security
                                ->generateRandomString(12)
                            . '.'
                            . $extension;

                        $savedPaymentPath =
                            $paymentDirectory . $fileName;

                        if (!$file->saveAs($savedPaymentPath)) {
                            $paymentLog->addError(
                                'file',
                                Yii::t(
                                    'app',
                                    'The payment file could not be saved.'
                                )
                            );
                        } else {
                            $paymentLog->file = $fileName;
                        }
                    }
                }

                if (
                    !$paymentLog->hasErrors()
                    && $paymentLog->save()
                ) {
                    if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
                        CommonService::addApprovalLog(
                            $this->processType,
                            'Paid',
                            'Marked as Paid, Payment Approved',
                            $boat->id
                        );

                        CommonService::markAsPaid(
                            $boat,
                            $this->processType
                        );
                    } else {
                        CommonService::addApprovalLog(
                            $this->processType,
                            'Paid',
                            'Marked as Paid',
                            $boat->id
                        );

                        $boat->status =
                            Constant::PaymentPending;
                    }

                    $boat->save(false);

                    return $this->redirect([
                        'view',
                        'token' => SecurityHelper::encryptId(
                            BoatNumbers::class,
                            $boat->id
                        ),
                    ]);
                }

                if (
                    $savedPaymentPath !== null
                    && is_file($savedPaymentPath)
                    && $paymentLog->getIsNewRecord()
                ) {
                    @unlink($savedPaymentPath);
                }
            }
        }

        return $this->render('../payment/create', [
            'model' => $paymentLog,
        ]);
    }
    public function actionPaymentApprove(
        string $token = ''
    ): Response {
        throw new ForbiddenHttpException(
            Yii::t(
                'app',
                'You do not have permission to run this operation.'
            )
        );
    }
    public function actionLicenseView(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'BoatNumbersController-license-view'
        );

        $id = SecurityHelper::decryptId(
            $token,
            BoatNumbers::class
        );

        $model = $this->findModel($id);
        $this->enforceBoatOwnership($model);

        return $this->render('licenseView', [
            'model' => $model,
            'token' => $token,
        ]);
    }
    public function actionLicenseDownload(
        string $token
    ): string {
        CommonService::validatePermission(
            $this,
            'BoatNumbersController-license-download'
        );

        $id = SecurityHelper::decryptId(
            $token,
            BoatNumbers::class
        );

        $model = $this->findModel($id);
        $this->enforceBoatOwnership($model);

        $officer = CommonService::getApprovedOfficer(
            $model->id,
            $this->processType
        );

        if ($officer instanceof \yii\db\ActiveRecord) {
            $officer = $officer->toArray();
        }

        if (!is_array($officer)) {
            $officer = [];
        }

        $uploadRoot = rtrim(
            Constant::$FILE_UPLOAD_PATH,
            '/\\'
        );

        $nationalLogoPath =
            $uploadRoot
            . DIRECTORY_SEPARATOR
            . 'static'
            . DIRECTORY_SEPARATOR
            . 'national_Logo2.jpg';

        $boatLicensePath =
            $uploadRoot
            . DIRECTORY_SEPARATOR
            . 'static'
            . DIRECTORY_SEPARATOR
            . 'boatlicense_1.jpg';

        $readRequiredImage = static function (
            string $filePath,
            string $imageName
        ): string {
            if (
                !is_file($filePath)
                || !is_readable($filePath)
            ) {
                throw new ServerErrorHttpException(
                    $imageName . ' was not found or is not readable.'
                );
            }

            $imageData = file_get_contents($filePath);

            if ($imageData === false || $imageData === '') {
                throw new ServerErrorHttpException(
                    'Unable to read ' . $imageName . '.'
                );
            }

            return $imageData;
        };

        $nationalLogoData = $readRequiredImage(
            $nationalLogoPath,
            'National logo'
        );

        $boatLicenseData = $readRequiredImage(
            $boatLicensePath,
            'Boat licence header'
        );

        $signatureFilename = basename(
            (string) ($officer['signature'] ?? '')
        );

        $signatureData = null;
        $hasOfficerSignature = false;

        if ($signatureFilename !== '') {
            $signaturePath =
                $uploadRoot
                . DIRECTORY_SEPARATOR
                . 'officer'
                . DIRECTORY_SEPARATOR
                . 'signature'
                . DIRECTORY_SEPARATOR
                . $signatureFilename;

            if (
                is_file($signaturePath)
                && is_readable($signaturePath)
            ) {
                $loadedSignature = file_get_contents(
                    $signaturePath
                );

                if (
                    $loadedSignature !== false
                    && $loadedSignature !== ''
                ) {
                    $signatureData = $loadedSignature;
                    $hasOfficerSignature = true;
                }
            } else {
                Yii::warning(
                    [
                        'message' =>
                            'Officer signature is missing or unreadable.',
                        'filename' => $signatureFilename,
                    ],
                    __METHOD__
                );
            }
        }

        $content = $this->renderPartial(
            'license',
            [
                'model' => $model,
                'token' => $token,
                'validationToken' => $token,
                'officer' => $officer,
                'isPdf' => true,
                'hasOfficerSignature' =>
                    $hasOfficerSignature,
            ]
        );

        $boatNumber = trim(
            (string) ($model->boat_number ?? '')
        );

        if ($boatNumber === '') {
            $boatNumber = 'record';
        }

        $safeBoatNumber = preg_replace(
            '/[^A-Za-z0-9_-]+/',
            '_',
            $boatNumber
        );

        if ($safeBoatNumber === null || $safeBoatNumber === '') {
            $safeBoatNumber = 'record';
        }

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'filename' =>
                'Boat-Number-' . $safeBoatNumber . '.pdf',
            'content' => $content,
        ]);

        $mpdf = $pdf->getApi();
        $mpdf->showImageErrors = false;
        $mpdf->imageVars['nationalLogo'] =
            $nationalLogoData;
        $mpdf->imageVars['boatLicense'] =
            $boatLicenseData;

        if (
            $hasOfficerSignature
            && $signatureData !== null
        ) {
            $mpdf->imageVars['officerSignature'] =
                $signatureData;
        }

        return $pdf->render();
    }
    public function actionBoatSearch(
        $q = null,
        $id = null
    ): array {
        Yii::$app->response->format =
            Response::FORMAT_JSON;

        $profileId = (int) (
            Yii::$app->user->identity->profile_id
            ?? 0
        );

        $officerProfile = ProfileOfficer::findOne(
            $profileId
        );

        if ($officerProfile === null) {
            throw new ForbiddenHttpException(
                Yii::t('app', 'Officer profile was not found.')
            );
        }

        $out = ['results' => []];
        $queryText = trim((string) $q);

        if ($queryText !== '') {
            $rows = (new Query())
                ->select(['id', 'boat_number AS text'])
                ->from('boat_numbers')
                ->where(['like', 'boat_number', $queryText])
                ->andWhere([
                    'fisheries_district' =>
                        $officerProfile->district,
                ])
                ->andWhere(['status' => Constant::Active])
                ->limit(20)
                ->all();

            foreach ($rows as &$row) {
                $row['id'] = (int) $row['id'];
                $row['text'] = (string) $row['text'];
                $row['token'] = SecurityHelper::encryptId(
                    BoatNumbers::class,
                    $row['id']
                );
            }
            unset($row);

            $out['results'] = array_values($rows);
        } elseif ((int) $id > 0) {
            $model = BoatNumbers::findOne((int) $id);

            if (
                $model !== null
                && (int) $model->fisheries_district
                    === (int) $officerProfile->district
            ) {
                $out['results'] = [[
                    'id' => (int) $model->id,
                    'text' => (string) $model->boat_number,
                    'token' => SecurityHelper::encryptId(
                        BoatNumbers::class,
                        $model->id
                    ),
                ]];
            }
        }

        return $out;
    }
    public function actionBoatGlobalSearch(
        $q = null,
        $id = null
    ): array {
        Yii::$app->response->format =
            Response::FORMAT_JSON;

        $out = ['results' => []];
        $queryText = trim((string) $q);

        if ($queryText !== '') {
            $rows = (new Query())
                ->select(['id', 'boat_number AS text'])
                ->from('boat_numbers')
                ->where(['like', 'boat_number', $queryText])
                ->andWhere(['status' => Constant::Active])
                ->limit(20)
                ->all();

            foreach ($rows as &$row) {
                $row['id'] = (int) $row['id'];
                $row['text'] = (string) $row['text'];
                $row['token'] = SecurityHelper::encryptId(
                    BoatNumbers::class,
                    $row['id']
                );
            }
            unset($row);

            $out['results'] = array_values($rows);
        } elseif ((int) $id > 0) {
            $model = BoatNumbers::findOne((int) $id);

            if ($model !== null) {
                $out['results'] = [[
                    'id' => (int) $model->id,
                    'text' => (string) $model->boat_number,
                    'token' => SecurityHelper::encryptId(
                        BoatNumbers::class,
                        $model->id
                    ),
                ]];
            }
        }

        return $out;
    }
    public function actionSearch(
        $q = null,
        $id = null
    ): array {
        Yii::$app->response->format =
            Response::FORMAT_JSON;

        $out = ['results' => []];
        $queryText = trim((string) $q);

        if ($queryText !== '') {
            $rows = (new Query())
                ->select(['id', 'boat_number AS text'])
                ->from('boat_numbers')
                ->where(['like', 'boat_number', $queryText])
                ->andWhere(['!=', 'status', Constant::Transferred])
                ->limit(20)
                ->all();

            foreach ($rows as &$row) {
                $row['id'] = (int) $row['id'];
                $row['text'] = (string) $row['text'];
                $row['token'] = SecurityHelper::encryptId(
                    BoatNumbers::class,
                    $row['id']
                );
            }
            unset($row);

            $out['results'] = array_values($rows);
        } elseif ((int) $id > 0) {
            $model = BoatNumbers::findOne((int) $id);

            if ($model !== null) {
                $out['results'] = [[
                    'id' => (int) $model->id,
                    'text' => (string) $model->boat_number,
                    'token' => SecurityHelper::encryptId(
                        BoatNumbers::class,
                        $model->id
                    ),
                ]];
            }
        }

        return $out;
    }

    public
    static function getStacs()
    {
        $where = [];
        $wherePending = [];
        if (UserTypeUtil::hasType(Constant::FI)) {
            $wherePending = ["division" => Yii::$app->session->get("officer_division"), 'approval_stage' => Constant::FI];
            $where = ["fisheries_division" => Yii::$app->session->get("officer_division")];
        }
        if (UserTypeUtil::hasType(Constant::AD)) {
            $wherePending = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::AD];
            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DO)) {
            $wherePending = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DO];
            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DM)) {
            $wherePending = ['approval_stage' => Constant::DM];
//            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DG)) {
            $wherePending = ['approval_stage' => Constant::DG];
//            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::ADMIN)) {
            $wherePending = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::ADMIN];
            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DFI)) {
            $wherePending = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DFI];
            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
            $whereActive = ["district" => Yii::$app->session->get("officer_district")];
        }
        $countPending = BoatNumbers::find()->where(['status' => Constant::Pending])->andWhere($wherePending)->count();
        $countActive = BoatNumbers::find()->where(['status' => Constant::Active])->andWhere($where)->count();
        $countFinalApproval = BoatNumbers::find()->where(['status' => Constant::FinalApprovalPending])->andWhere($where)->count();
        $countExpired = BoatNumbers::find()->where(['status' => Constant::Expired])->andWhere($where)->count();

        return [
            "countPending" => $countPending,
            "countActive" => $countActive,
            "countFinalApproval" => $countFinalApproval,
            "countExpired" => $countExpired,
        ];

    }
    public function actionBoatdetails(
        string $token
    ): array {
        Yii::$app->response->format =
            Response::FORMAT_JSON;

        CommonService::validatePermission(
            $this,
            'BoatNumbersController-office-view'
        );

        $id = SecurityHelper::decryptId(
            $token,
            BoatNumbers::class
        );

        $model = $this->findModel($id);
        $this->enforceBoatOwnership($model);

        $owner = $model->owner0;

        if ($owner === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Boat owner was not found.')
            );
        }

        $profileImage = basename(
            (string) ($owner->profile_image ?? '')
        );

        $imageUrl = $profileImage === ''
            ? ''
            : rtrim(
                (string) Yii::getAlias('@web'),
                '/'
            )
            . '/uploads/fisherman/'
            . rawurlencode($profileImage);

        return [
            'name' => trim(
                (string) ($owner->first_name ?? '')
                . ' '
                . (string) ($owner->last_name ?? '')
            ),
            'nic' => (string) ($owner->nic ?? ''),
            'imageUrl' => $imageUrl,
            'image' => $imageUrl === ''
                ? ''
                : Html::img(
                    $imageUrl,
                    [
                        'alt' => Yii::t(
                            'app',
                            'Boat owner profile image'
                        ),
                    ]
                ),
            'district' => (string) (
                $owner->district0?->name
                ?? ''
            ),
            'division' => (string) (
                $owner->division0?->name
                ?? ''
            ),
        ];
    }


    private function enforceBoatOwnership(
        BoatNumbers $model
    ): void {
        if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
            return;
        }

        $profileId = (int) (
            Yii::$app->user->identity->profile_id
            ?? 0
        );

        if (
            $profileId <= 0
            || (int) $model->owner !== $profileId
        ) {
            /*
             * Return 404 instead of revealing that another
             * fisherman's boat-number record exists.
             */
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested boat-number record was not found.'
                )
            );
        }
    }

    public function actionAddBoatNumberManual()
{
    if (!UserTypeUtil::hasType(Constant::ADMIN)) {
        throw new \yii\web\ForbiddenHttpException(
            'You are not allowed to access this page.'
        );
    }

    $model = new BoatNumbers();

    $fishermanList = ArrayHelper::map(
        ProfileFisherman::find()
            ->where([
                'status' => Constant::Active,
            ])
            ->orderBy([
                'first_name' => SORT_ASC,
                'last_name' => SORT_ASC,
            ])
            ->asArray()
            ->all(),
        'id',
        static function (array $row): string {
            return trim(
                (string) ($row['first_name'] ?? '')
                . ' '
                . (string) ($row['last_name'] ?? '')
            );
        }
    );

    $yardList = ArrayHelper::map(
        ProfileYard::find()
            ->orderBy([
                'name' => SORT_ASC,
            ])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    $districtList = ArrayHelper::map(
        MFiDistrict::find()
            ->where([
                'status' => 1,
            ])
            ->orderBy([
                'name' => SORT_ASC,
            ])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    $boatTypeArray = ArrayHelper::map(
        MBoatTypes::find()
            ->orderBy([
                'code' => SORT_ASC,
            ])
            ->asArray()
            ->all(),
        'id',
        'code'
    );

    $renderData = static function () use (
        $model,
        $fishermanList,
        $yardList,
        $districtList,
        $boatTypeArray
    ): array {
        return [
            'model' => $model,
            'fishermanList' => $fishermanList,
            'yardList' => $yardList,
            'districtList' => $districtList,
            'boatTypeArray' => $boatTypeArray,
        ];
    };

    if (Yii::$app->request->isPost) {
        if (!Util::editPermission()) {
            throw new ForbiddenHttpException(
                Yii::t(
                    'app',
                    'You are not allowed to add boat numbers.'
                )
            );
        }

        if (!$model->load(
            Yii::$app->request->post()
        )) {
            return $this->render(
                'addBoatNumberManual',
                $renderData()
            );
        }
        $model->boat_number = trim(
            (string) $model->boat_number
        );

        if ($model->boat_number === '') {
            $model->addError(
                'boat_number',
                'Boat number cannot be blank.'
            );

            return $this->render(
                'addBoatNumberManual',
                $renderData()
            );
        }

        /*
         * Case-insensitive duplicate check after trimming.
         */
        $existingBoatNumber = BoatNumbers::find()
            ->where(
                'LOWER(TRIM([[boat_number]])) = :boatNumber',
                [
                    ':boatNumber' => mb_strtolower(
                        $model->boat_number,
                        'UTF-8'
                    ),
                ]
            )
            ->exists();

        if ($existingBoatNumber) {
            $model->addError(
                'boat_number',
                'This boat number already exists.'
            );

            Yii::$app->session->setFlash(
                'error',
                'This boat number already exists.'
            );

            return $this->render(
                'addBoatNumberManual',
                $renderData()
            );
        }

        $profileFisherman = ProfileFisherman::findOne(
            $model->owner
        );

        if ($profileFisherman === null) {
            $model->addError(
                'owner',
                'The selected boat owner was not found.'
            );

            return $this->render(
                'addBoatNumberManual',
                $renderData()
            );
        }

        if (
            empty($profileFisherman->district)
            || empty($profileFisherman->division)
        ) {
            $model->addError(
                'owner',
                'The selected owner does not have a valid '
                . 'district and division.'
            );

            return $this->render(
                'addBoatNumberManual',
                $renderData()
            );
        }

        $model->fisheries_district =
            $profileFisherman->district;

        $model->fisheries_division =
            $profileFisherman->division;

        /*
         * Default values for manually added boat numbers.
         */
        $model->hull_number = 0;
        $model->status = Constant::Active;
        $model->approval_stage = 'Completed';
        $model->created = date('Y-m-d H:i:s');
        $model->approved_time = $model->created;

        $model->expire_date = date(
            'Y-m-d',
            strtotime(
                $model->approved_time
                . ' +1 year -1 day'
            )
        );

        /*
         * Validate BoatNumbers before starting the transaction.
         */
        if (!$model->validate()) {
            Yii::warning(
                [
                    'step' => 'boat_number_validation',
                    'errors' => $model->getErrors(),
                    'attributes' => $model->attributes,
                ],
                __METHOD__
            );

            Yii::$app->session->setFlash(
                'error',
                'Please correct the highlighted form errors.'
            );

            return $this->render(
                'addBoatNumberManual',
                $renderData()
            );
        }

        $transaction =
            Yii::$app->db->beginTransaction();

        $step = 'saving boat number';

        try {
            /*
             * Save BoatNumbers.
             */
            if (!$model->save(false)) {
                throw new \RuntimeException(
                    'Boat number could not be saved.'
                );
            }

            /*
             * Save fisherman_registerd_boat.
             */
            $step = 'saving registered boat';

            $registeredBoat =
                new FishermanRegisterdBoat();

            $registeredBoat->boat_number_id =
                $model->id;

            $registeredBoat->fisherman_id =
                $model->owner;

            $registeredBoat->status =
                'Departure Allowed';

            if (!$registeredBoat->validate()) {
                throw new \RuntimeException(
                    'Registered boat validation failed: '
                    . \yii\helpers\Json::encode(
                        $registeredBoat->getErrors()
                    )
                );
            }

            if (!$registeredBoat->save(false)) {
                throw new \RuntimeException(
                    'Registered boat could not be saved.'
                );
            }

            /*
             * Save fisherman_registerd_boat_license.
             */
            $step =
                'saving boat registration licence';

            $boatRegistrationLicense =
                new FishermanRegisterdBoatLicense();

            /*
             * The current data model uses the registered boat ID
             * as the licence record ID.
             */
            $boatRegistrationLicense->id =
                $registeredBoat->id;

            $boatRegistrationLicense->boat_number_id =
                $registeredBoat->boat_number_id;

            $boatRegistrationLicense->fisherman_id =
                $registeredBoat->fisherman_id;

            $boatRegistrationLicense->district =
                $model->fisheries_district;

            $boatRegistrationLicense->division =
                $model->fisheries_division;

            $boatRegistrationLicense->renew = 0;

            $boatRegistrationLicense->transered_license =
                0;

            $boatRegistrationLicense->status =
                $model->status;

            $boatRegistrationLicense->approval_stage =
                $model->approval_stage;

            $boatRegistrationLicense->created =
                $model->created;

            $boatRegistrationLicense->approved_time =
                $model->approved_time;

            $boatRegistrationLicense->expire_date =
                $model->expire_date;

            /*
             * These fields are not applicable when only a manual
             * boat number is being created. Store them as NULL.
             */
            $boatRegistrationLicense->landing_site =
                null;

            $boatRegistrationLicense
                ->date_of_construction = null;

            $boatRegistrationLicense
                ->communication_equipment = null;

            $boatRegistrationLicense
                ->fishing_equipment = null;

            $boatRegistrationLicense
                ->navigation_equipment = null;

            $boatRegistrationLicense
                ->engine_serial_number = null;

            $boatRegistrationLicense->engine_make =
                null;

            $boatRegistrationLicense->engine_type =
                null;

            $boatRegistrationLicense->fuel_type =
                null;

            /*
             * Skip the normal licence validation rules because those
             * rules require fields that are intentionally not applicable
             * to manual boat-number creation.
             *
             * Database columns must allow NULL.
             */
            if (!$boatRegistrationLicense->save(false)) {
                throw new \RuntimeException(
                    'Boat registration licence '
                    . 'could not be saved.'
                );
            }

            $transaction->commit();

            Yii::$app->session->setFlash(
                'success',
                'Boat number added successfully.'
            );

            return $this->redirect([
                'index',
            ]);
        } catch (\Throwable $exception) {
            if ($transaction->isActive) {
                $transaction->rollBack();
            }

            Yii::error(
                [
                    'step' => $step,
                    'exception' =>
                        get_class($exception),
                    'message' =>
                        $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' =>
                        $exception->getTraceAsString(),
                    'boatNumberErrors' =>
                        $model->getErrors(),
                ],
                __METHOD__
            );

            $errorMessage =
                'Failed while '
                . $step
                . ': '
                . $exception->getMessage();

            /*
             * A DB integrity exception here usually means one of the
             * listed columns is still defined as NOT NULL.
             */
            if (
                $exception instanceof
                    \yii\db\IntegrityException
            ) {
                $errorMessage .=
                    ' Check whether the manual licence columns '
                    . 'allow NULL in the database.';
            }

            $model->addError(
                'boat_number',
                $errorMessage
            );

            Yii::$app->session->setFlash(
                'error',
                $errorMessage
            );
        }
    }

    return $this->render(
        'addBoatNumberManual',
        $renderData()
    );
}
}
