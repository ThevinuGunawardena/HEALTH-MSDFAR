<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\AuthAssignment;
use backend\models\BoatNumberCancelRequests;
use backend\models\BoatNumbers;
use backend\models\Files;
use backend\models\Fisherman;
use backend\models\FishermanRegisterdBoat;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\HighseasLicense;
use backend\models\IdPrintQueue;
use backend\models\MApprovalWorkflow;
use backend\models\MFiDistrict;
use backend\models\MDivision;
use backend\models\NationalLicense;
use backend\models\ProfileFisherman;
use backend\models\ProfileFishermanSearch;
use backend\models\ProfileOfficer;
use backend\models\ProfileYard;
use backend\models\Skipper;
use backend\models\ProfileFishermanRenew;
use backend\models\User;
use backend\services\CommonService;
use backend\services\Util;
use common\components\WebUser;
use Exception;
use kartik\mpdf\Pdf;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
use backend\components\SecurityHelper;
use backend\components\RecordLookupRateLimit;
use backend\components\BaminiToUnicode;
use backend\components\SinhalaFontConverter;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;

class FishermanController extends Controller
{
    public $processType = "FISHERMAN-REG";

    /**
     * @throws UnauthorizedHttpException
     */
  public function behaviors(): array
{
    $behaviors = parent::behaviors();

    /*
     * Preserve any HTTP method rules inherited from the parent controller.
     */
    $existingVerbActions =
        $behaviors['verbs']['actions'] ?? [];

    $behaviors['verbs'] = [
        'class' => VerbFilter::class,
        'actions' => array_merge(
            $existingVerbActions,
            [
                'get-personal-details' => ['POST'],
                'backfill-localized-details' => ['POST'],
            ]
        ),
    ];

    /*
     * Burst protection:
     * Maximum 30 sensitive record requests per minute.
     */
    $behaviors['recordLookupBurstLimit'] = [
        'class' => RecordLookupRateLimit::class,
        'only' => [
            'index',
            'view',
            'profile',
            'update',
            'license-view',
            'renew-license-view',
            'license-download',
            'renew-license-download',
            'renew',
        ],
        'bucketName' => 'fisherman-burst',
        'limit' => 30,
        'window' => 60,
    ];

    /*
     * Sustained protection:
     * Maximum 200 sensitive record requests per 15 minutes.
     */
    $behaviors['recordLookupSustainedLimit'] = [
        'class' => RecordLookupRateLimit::class,
        'only' => [
            'view',
            'profile',
            'update',
            'license-view',
            'renew-license-view',
            'license-download',
            'renew-license-download',
            'renew',
        ],
        'bucketName' => 'fisherman-sustained',
        'limit' => 200,
        'window' => 900,
    ];

    return $behaviors;
}
    public function actionIndex()
    {
        CommonService::validatePermission($this, "fisherman-list");

        $searchModel = new ProfileFishermanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     */
public function actionView($token)
{
    CommonService::validatePermission(
        $this,
        'fisherman-profile-office-view'
    );

    $id = SecurityHelper::decryptId(
        (string) $token,
        \backend\models\ProfileFisherman::class
    );

    $error = false;

    $model = $this->findModel($id);

    $renewModel = null;
    $isRenewal = false;

    /*
     * By default, the approval workflow belongs to the original
     * fisherman registration.
     */
    $approvalModel = $model;
    $approvalId = $model->id;
    $processType = $this->processType;

    /*
     * When a renewal request exists, use ProfileFishermanRenew
     * for the approval workflow.
     */
    if (
        (int) $model->renew === 1
        && !empty($model->renew_id)
    ) {
        $renewModel = ProfileFishermanRenew::find()
            ->where([
                'id' => $model->renew_id,
                'fisherman_id' => (string) $model->id,
            ])
            ->one();

        if ($renewModel !== null) {
            $isRenewal = true;

            $approvalModel = $renewModel;
            $approvalId = $renewModel->id;
            $processType = 'FISHERMAN_LICENSE_RENEW';
        }
    }

    /*
     * Get the current approval process details.
     */
    $approvalFlow = CommonService::getApprovalProcess(
        $approvalModel,
        $processType,
        $approvalId,
        false
    );

    if (
        $this->request->isPost
        && Util::editPermission()
    ) {
        $approvalDecision = strtolower(
            trim(
                (string) $this->request->post(
                    'status-approval',
                    ''
                )
            )
        );

        if (
            !in_array(
                $approvalDecision,
                ['approve', 'reject'],
                true
            )
        ) {
            $approvalModel->addError(
                'approval_stage',
                'Invalid approval action.'
            );

            $error = true;
        } elseif ($approvalModel->validate()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                /*
                 * markApprovalStage() updates the approval stage according
                 * to the current logged-in user's approval level.
                 */
                $updatedApprovalModel = CommonService::markApprovalStage(
                    $approvalFlow,
                    $approvalModel,
                    $approvalId,
                    $processType
                );

                if ($updatedApprovalModel === null) {
                    throw new \RuntimeException(
                        'Unable to update the approval stage.'
                    );
                }

                $approvalModel = $updatedApprovalModel;

                /*
                 * Determine whether all approval stages are finished.
                 */
                $isApprovalCompleted = strcasecmp(
                    trim(
                        (string) $approvalModel->approval_stage
                    ),
                    'Completed'
                ) === 0;

                /*
                 * For an approval action:
                 *
                 * Intermediate approval:
                 *     status = Pending
                 *
                 * Final approval:
                 *     status = Active (101)
                 *
                 * Do not overwrite the status for a rejection because
                 * markApprovalStage() should set the rejected status.
                 */
                if ($approvalDecision === 'approve') {
                    if ($approvalModel->hasAttribute('status')) {
                        $approvalModel->status = $isApprovalCompleted
                            ? Constant::Active
                            : Constant::Pending;
                    }

                    /*
                     * Final-stage actions must only run after the complete
                     * approval workflow has finished.
                     */
                    if ($isApprovalCompleted) {
                        /*
                         * Set the approved time only after final approval.
                         */
                        if (
                            $approvalModel->hasAttribute('approved_time')
                            && empty($approvalModel->approved_time)
                        ) {
                            $approvalModel->approved_time = date(
                                'Y-m-d H:i:s'
                            );
                        }

                        /*
                         * For renewal requests, calculate the expiry date
                         * only after final approval.
                         */
                        if (
                            $isRenewal
                            && $approvalModel->hasAttribute('expire_date')
                            && !empty($approvalModel->approved_time)
                        ) {
                            $approvalModel->expire_date = date(
                                'Y-m-d',
                                strtotime(
                                    $approvalModel->approved_time
                                    . ' +5 years -1 day'
                                )
                            );
                        }

                        /*
                         * Generate the fisherman UID only for the original
                         * registration and only after final approval.
                         */
                        if (
                            !$isRenewal
                            && empty($approvalModel->fisherman_uid)
                        ) {
                            if ($approvalModel->district0 === null) {
                                throw new \RuntimeException(
                                    'Unable to generate fisherman UID because '
                                    . 'the district information is unavailable.'
                                );
                            }

                            $uid = Constant::$FISHERMAN_NUMBER_FORMAT;

                            $uid = str_replace(
                                '{number}',
                                sprintf(
                                    '%05d',
                                    (int) $approvalModel->id
                                ),
                                $uid
                            );

                            $uid = str_replace(
                                '{district_code}',
                                $approvalModel->district0->code,
                                $uid
                            );

                            $approvalModel->fisherman_uid = $uid;
                        }
                    }
                }

                if (!$approvalModel->save()) {
                    throw new \RuntimeException(
                        'Unable to save the fisherman approval details. '
                        . json_encode(
                            $approvalModel->getFirstErrors()
                        )
                    );
                }

                /*
                 * When a renewal is fully approved, update the original
                 * fisherman profile where required.
                 */
                if (
                    $isRenewal
                    && $approvalDecision === 'approve'
                    && $isApprovalCompleted
                ) {
                    /*
                     * Keep this block only if the original fisherman profile
                     * must be marked as active after renewal approval.
                     */
                    if ($model->hasAttribute('status')) {
                        $model->status = Constant::Active;
                    }

                    if ($model->hasAttribute('renew')) {
                        $model->renew = 0;
                    }

                    if (!$model->save(false)) {
                        throw new \RuntimeException(
                            'Unable to update the original fisherman profile.'
                        );
                    }
                }

                $transaction->commit();

                if ($approvalDecision === 'reject') {
                    Yii::$app->session->setFlash(
                        'success',
                        'The fisherman request was rejected successfully.'
                    );
                } elseif ($isApprovalCompleted) {
                    Yii::$app->session->setFlash(
                        'success',
                        'The fisherman approval process was completed successfully.'
                    );
                } else {
                    Yii::$app->session->setFlash(
                        'success',
                        'The approval was submitted successfully. '
                        . 'The request is waiting for the next approval stage.'
                    );
                }

                return $this->redirect(['index']);
            } catch (\Throwable $e) {
                if ($transaction->isActive) {
                    $transaction->rollBack();
                }

                Yii::error([
                    'message' => $e->getMessage(),
                    'exceptionClass' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'profileId' => $model->id,
                    'approvalId' => $approvalId,
                    'processType' => $processType,
                    'approvalDecision' => $approvalDecision,
                ], 'fisherman-approval');

                Yii::$app->session->setFlash(
                    'error',
                    $e->getMessage()
                );

                $approvalModel->addError(
                    'approval_stage',
                    $e->getMessage()
                );

                $error = true;
            }
        } else {
            $error = true;
        }
    }

    /*
     * Reload the approval process so the view receives the most recent
     * history and button permissions.
     */
    $approvalFlow = CommonService::getApprovalProcess(
        $approvalModel,
        $processType,
        $approvalId,
        false
    );

    $workflow = MApprovalWorkflow::find()
        ->where([
            'type' => $processType,
        ])
        ->one();

    $files = [];

    if ($workflow !== null) {
        $files = Files::find()
            ->where([
                'type' => $workflow->id,
                'process_id' => $approvalId,
            ])
            ->all();
    }

    return $this->render('view', [
    'model' => $model,
    'renewModel' => $renewModel,
    'approvalModel' => $approvalModel,

    'isRenewal' => $isRenewal,
    'process' => $processType,
    'processId' => $approvalId,

    'approvalHistory' => $approvalFlow['approvalHistory'],
    'showRejectBtn' => $approvalFlow['showRejectBtn'],
    'showApproveBtn' => $approvalFlow['showApproveBtn'],

    'files' => $files,
    'error' => $error,

    // Add this.
    'token' => $token,
]);
}

    /**
     * @throws UnauthorizedHttpException
     */
    public function actionProfileEdit()
    {
        CommonService::validatePermission($this, "fisherman-profile-edit");

        $districtList = ArrayHelper::map(MFiDistrict::find()->where(["status" => 1])->asArray()->all(), 'id', "name");
        $years = array_combine(range(date("Y"), 1910), range(date("Y"), 1910));
        $user = User::findOne(["id" => Yii::$app->user->identity->id, "type" => 1]);

        $update = true;
        $model = ProfileFisherman::findOne($user->profile_id);
        $profileId = $user->profile_id;
        if ($model == null) {
            $model = new ProfileFisherman();
            $profileId = 0;
            $update = false;
        }
        if ($model->nic == null || $model->nic == "") {
            $model->nic = Yii::$app->user->identity->nic;
        }
        if ($this->request->isPost && Util::editPermission()) {
            $profile_image = $model->profile_image;
            $signature = $model->signature;
            if ($model->load($this->request->post())) {
                $model->status = 1;
                $model->approval_stage = "" . Constant::FI;
                if ($model->save()) {
                    if ($profileId == 0) {
                        $user->profile_id = $model->id;
                        $user->save();
                    }
                    $file = UploadedFile::getInstance($model, 'profile_image');
                    if (isset($file)) {
                        $fileName = $model->id . '_profileImage.' . $file->extension;
                        $file->saveAs(Constant::$FILE_UPLOAD_PATH . 'fisherman/' . $fileName);

                        $model->profile_image = $fileName;
                    } else {
                        $model->profile_image = $profile_image;
                    }
                    $fileSignature = UploadedFile::getInstance($model, 'signature');
                    if (isset($fileSignature)) {
                        $fileName = $model->id . '_signature.' . $fileSignature->extension;
                        $fileSignature->saveAs(Constant::$FILE_UPLOAD_PATH.'fisherman/' . $fileName);

                        $model->signature = $fileName;

                    } else {
                        $model->signature = $signature;
                    }

                    $model->save();
                    CommonService::addApprovalLog($this->processType, $update ? "Update = Submitted" : "Create = Submitted", "Submitted", $model->id);
                    if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        return $this->goHome();
                    }
                }
            }
        }

//        $model->nic = $user->nic;

        return $this->render('profileEdit', [
            'model' => $model,
            'districtList' => $districtList,
            'years' => $years,
        ]);
    }

    /**
     * @throws UnauthorizedHttpException
     * @throws NotFoundHttpException
     */
    public function actionProfile(?string $token = null)
{
    CommonService::validatePermission(
        $this,
        'fisherman-profile-view'
    );

    /*
     * Fisherman users may only open their own profile.
     * Do not accept a token to switch to another profile.
     */
    if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
        $fishermanId = (int) (
            Yii::$app->user->identity->profile_id ?? 0
        );
    } else {
        /*
         * Office/admin users must use an encrypted token.
         */
        if ($token === null || trim($token) === '') {
            throw new NotFoundHttpException(
                'Fisherman was not found.'
            );
        }

        $fishermanId = SecurityHelper::decryptId(
            $token,
            \backend\models\Fisherman::class
        );
    }

    if ($fishermanId <= 0) {
        throw new NotFoundHttpException(
            'Fisherman was not found.'
        );
    }

    $fisherman = Fisherman::findOne($fishermanId);

    if ($fisherman === null) {
        /*
         * Keep this redirect only when a logged-in fisherman
         * has not completed their own profile.
         */
        if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
            return $this->redirect([
                '/fisherman/profile-edit',
            ]);
        }

        throw new NotFoundHttpException(
            'Fisherman was not found.'
        );
    }

    $myBoatNumbers = BoatNumbers::find()
        ->where([
            'owner' => $fisherman->id,
        ])
        ->all();

    $myBoats = FishermanRegisterdBoat::find()
        ->where([
            'fisherman_id' => $fisherman->id,
        ])
        ->all();

    $myBoatsIds = [];

    foreach ($myBoats as $myBoat) {
        $myBoatsIds[] = $myBoat['id'];
    }

    $myBoatsLicenseList =
        FishermanRegisterdBoatLicense::find()
            ->where([
                'in',
                'id',
                $myBoatsIds,
            ])
            ->andWhere([
                'fisherman_id' => $fisherman->id,
            ])
            ->all();

    $mySkipperLicence = Skipper::find()
        ->where([
            'fisherman_id' => $fisherman->id,
        ])
        ->all();

    $myFishermanLicense = ProfileFisherman::find()
        ->where([
            'id' => $fisherman->id,
        ])
        ->all();

    $myFishermanRenewalLicenses =
        ProfileFishermanRenew::find()
            ->where([
                'fisherman_id' =>
                    (string) $fisherman->id,
            ])
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->all();

    $myNationalLicence = NationalLicense::find()
        ->where([
            'fisherman_id' => $fisherman->id,
        ])
        ->orderBy([
            'boat_registration_id' => SORT_ASC,
        ])
        ->all();

    $myYardLicence = ProfileYard::find()
        ->where([
            'owner' => $fisherman->id,
        ])
        ->all();

    $myHighseasLicence = HighseasLicense::find()
        ->where([
            'fisherman_id' => $fisherman->id,
        ])
        ->orderBy([
            'boat_registration_id' => SORT_ASC,
        ])
        ->all();

    $myBoatsLicenseMap = ArrayHelper::index(
        $myBoatsLicenseList,
        null,
        'id'
    );

    $result = ArrayHelper::index(
        $myNationalLicence,
        null,
        [
            static function ($element) {
                return $element['boat_registration_id'];
            },
            'status',
        ]
    );

    $boatNumbers = [];

    foreach ($myBoatNumbers as $myBoatNumber) {
        $boatNumbers[] = $myBoatNumber['id'];
    }

    $myBoatCancelRequests =
        BoatNumberCancelRequests::find()
            ->where([
                'in',
                'boat_number_id',
                $boatNumbers,
            ])
            ->all();

    return $this->render('profile', [
        'fisherMan' =>
            $fisherman,

        'myBoatNumbers' =>
            $myBoatNumbers,

        'myBoats' =>
            $myBoats,

        'mySkipperLicence' =>
            $mySkipperLicence,

        'myFishermanLicense' =>
            $myFishermanLicense,

        'myFishermanRenewalLicenses' =>
            $myFishermanRenewalLicenses,

        'myNationalLicence' =>
            $myNationalLicence,

        'myYardLicence' =>
            $myYardLicence,

        'myHighseasLicence' =>
            $myHighseasLicence,

        'myBoatCancelRequests' =>
            $myBoatCancelRequests,

        'myBoatsLicenseMap' =>
            $myBoatsLicenseMap,
    ]);
}


    public function actionFishermanRegisterRequests()
    {
        $searchModel = new ProfileFishermanSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('fishermanRegisterRequests', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = ProfileFisherman::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * @throws UnauthorizedHttpException
     */
    public function actionCreate()
{
    CommonService::validatePermission(
        $this,
        'fisherman-profile-create'
    );

    $model = new ProfileFisherman();

    $districtList = ArrayHelper::map(
        MFiDistrict::find()
            ->where(['status' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    $years = array_combine(
        range((int) date('Y'), 1910),
        range((int) date('Y'), 1910)
    );

    if (
        $this->request->isPost
        && $model->load($this->request->post())
        && Util::editPermission()
    ) {
        $profileImageFile = UploadedFile::getInstance(
            $model,
            'profile_image'
        );

        $signatureFile = UploadedFile::getInstance(
            $model,
            'signature'
        );

        $model->status = Constant::Pending;
        $model->approval_stage = (string) Constant::FI;

        /*
         * Uploaded images are validated separately by
         * stageFishermanImage(). Validate all other model fields here.
         */
        $validationAttributes = array_values(
            array_diff(
                $model->activeAttributes(),
                ['profile_image', 'signature']
            )
        );

        if ($model->validate($validationAttributes)) {
            $uploadDirectory = rtrim(
                Constant::$FILE_UPLOAD_PATH,
                '/\\'
            ) . DIRECTORY_SEPARATOR
                . 'fisherman'
                . DIRECTORY_SEPARATOR;

            $stagedProfileImage = null;
            $stagedSignature = null;
            $newFiles = [];
            $transaction = null;

            try {
                if ($profileImageFile !== null) {
                    $stagedProfileImage = $this->stageFishermanImage(
                        $profileImageFile,
                        $uploadDirectory,
                        'Profile image'
                    );
                }

                if ($signatureFile !== null) {
                    $stagedSignature = $this->stageFishermanImage(
                        $signatureFile,
                        $uploadDirectory,
                        'Signature'
                    );
                }

                $transaction = Yii::$app->db->beginTransaction();

                if (!$model->save(false)) {
                    throw new \RuntimeException(
                        'Unable to create the fisherman profile.'
                    );
                }

                $fileUpdates = [];

                if ($stagedProfileImage !== null) {
                    $profileImageName = $this->generateFishermanImageName(
                        (int) $model->id,
                        'profileImage',
                        $stagedProfileImage['extension']
                    );

                    $profileImagePath = $uploadDirectory
                        . $profileImageName;

                    $this->moveStagedFishermanImage(
                        $stagedProfileImage['temporaryPath'],
                        $profileImagePath
                    );

                    $newFiles[] = $profileImagePath;
                    $fileUpdates['profile_image'] = $profileImageName;
                }

                if ($stagedSignature !== null) {
                    $signatureName = $this->generateFishermanImageName(
                        (int) $model->id,
                        'signature',
                        $stagedSignature['extension']
                    );

                    $signaturePath = $uploadDirectory
                        . $signatureName;

                    $this->moveStagedFishermanImage(
                        $stagedSignature['temporaryPath'],
                        $signaturePath
                    );

                    $newFiles[] = $signaturePath;
                    $fileUpdates['signature'] = $signatureName;
                }

                if (!empty($fileUpdates)) {
                    $model->updateAttributes($fileUpdates);
                }

                CommonService::addApprovalLog(
                    $this->processType,
                    'Submitted',
                    'Submitted',
                    $model->id
                );

                $transaction->commit();

                Yii::$app->session->setFlash(
                    'success',
                    'Fisherman profile created successfully.'
                );

                if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
                    return $this->redirect([
                    '/fisherman/view',
                    'token' => SecurityHelper::encryptId(
                        \backend\models\ProfileFisherman::class,
                        $model->id
                    ),
                ]);
                }

                return $this->goHome();
            } catch (\Throwable $e) {
                if ($transaction !== null && $transaction->isActive) {
                    $transaction->rollBack();
                }

                $this->removeFishermanFiles($newFiles);

                /*
                 * A rolled-back INSERT leaves the ActiveRecord instance
                 * marked as saved. Reset it before rendering the create form.
                 */
                if (!$model->getIsNewRecord()) {
                    $model->setIsNewRecord(true);
                    $model->setOldAttributes(null);

                    if ($model->hasAttribute('id')) {
                        $model->id = null;
                    }
                }

                Yii::error([
                    'message' => $e->getMessage(),
                    'exceptionClass' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ], 'fisherman-create');

                $model->addError(
                    'profile_image',
                    $e->getMessage()
                );

                Yii::$app->session->setFlash(
                    'error',
                    $e->getMessage()
                );
            } finally {
                $this->removeFishermanFiles([
                    $stagedProfileImage['temporaryPath'] ?? null,
                    $stagedSignature['temporaryPath'] ?? null,
                ]);
            }
        }
    }

    return $this->render('create', [
        'model' => $model,
        'districtList' => $districtList,
        'years' => $years,
    ]);
}


    /**
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     */
 public function actionUpdate($token)
{
    CommonService::validatePermission(
        $this,
        'fisherman-profile-update'
    );

     $id = SecurityHelper::decryptId(
        (string) $token,
        \backend\models\ProfileFisherman::class
    );

    $model = $this->findModel($id);

    $oldDistrict = $model->district;
    $oldDivision = $model->division;
    $oldProfileImage = $model->profile_image;
    $oldSignature = $model->signature;

    $districtList = ArrayHelper::map(
        MFiDistrict::find()
            ->where(['status' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    $getDivisionList =
        static function ($districtId): array {
            if (empty($districtId)) {
                return [];
            }

            return ArrayHelper::map(
                MDivision::find()
                    ->where([
                        'district_id' =>
                            $districtId,

                        'status' =>
                            1,
                    ])
                    ->orderBy([
                        'name' =>
                            SORT_ASC,
                    ])
                    ->asArray()
                    ->all(),
                'id',
                'name'
            );
        };

    $divisionList =
        $getDivisionList(
            $model->district
        );

    $yearRange = range(
        (int) date('Y'),
        1910
    );

    $years = array_combine(
        $yearRange,
        $yearRange
    );

    if (
        $this->request->isPost &&
        $model->load(
            $this->request->post()
        ) &&
        Util::editPermission()
    ) {
        $divisionList =
            $getDivisionList(
                $model->district
            );

        $profileImageFile =
            UploadedFile::getInstance(
                $model,
                'profile_image'
            );

        $signatureFile =
            UploadedFile::getInstance(
                $model,
                'signature'
            );

        /*
         * Keep the current filenames until a
         * replacement is successfully prepared.
         */
        $model->profile_image =
            $oldProfileImage;

        $model->signature =
            $oldSignature;

        if (
            $model->status !=
            Constant::Active
        ) {
            $model->status =
                Constant::Active;

            $model->approval_stage =
                (string) Constant::FI;
        }

        $validationAttributes =
            array_values(
                array_diff(
                    $model->activeAttributes(),
                    [
                        'profile_image',
                        'signature',
                    ]
                )
            );

        if (
            $model->validate(
                $validationAttributes
            )
        ) {
            $uploadDirectory =
                rtrim(
                    Constant::$FILE_UPLOAD_PATH,
                    '/\\'
                )
                . DIRECTORY_SEPARATOR
                . 'fisherman'
                . DIRECTORY_SEPARATOR;

            $stagedProfileImage = null;
            $stagedSignature = null;

            /*
             * Holds final and backup paths so
             * existing files can be restored if
             * anything fails.
             */
            $fileReplacements = [];

            $transaction = null;

            try {
                if (
                    $profileImageFile !== null
                ) {
                    $stagedProfileImage =
                        $this->stageFishermanImage(
                            $profileImageFile,
                            $uploadDirectory,
                            'Profile image'
                        );
                }

                if (
                    $signatureFile !== null
                ) {
                    $stagedSignature =
                        $this->stageFishermanImage(
                            $signatureFile,
                            $uploadDirectory,
                            'Signature'
                        );
                }

                $transaction =
                    Yii::$app->db
                        ->beginTransaction();

                if (
                    $stagedProfileImage !== null
                ) {
                    $profileImageName =
                        $this
                            ->generateFishermanImageName(
                                (int) $model->id,
                                'profileImage',
                                $stagedProfileImage[
                                    'extension'
                                ]
                            );

                    $profileImagePath =
                        $uploadDirectory
                        . $profileImageName;

                    $profileImageBackup =
                        $this
                            ->replaceStagedFishermanImage(
                                $stagedProfileImage[
                                    'temporaryPath'
                                ],
                                $profileImagePath
                            );

                    $fileReplacements[] = [
                        'finalPath' =>
                            $profileImagePath,

                        'backupPath' =>
                            $profileImageBackup,
                    ];

                    $model->profile_image =
                        $profileImageName;
                }

                if (
                    $stagedSignature !== null
                ) {
                    $signatureName =
                        $this
                            ->generateFishermanImageName(
                                (int) $model->id,
                                'signature',
                                $stagedSignature[
                                    'extension'
                                ]
                            );

                    $signaturePath =
                        $uploadDirectory
                        . $signatureName;

                    $signatureBackup =
                        $this
                            ->replaceStagedFishermanImage(
                                $stagedSignature[
                                    'temporaryPath'
                                ],
                                $signaturePath
                            );

                    $fileReplacements[] = [
                        'finalPath' =>
                            $signaturePath,

                        'backupPath' =>
                            $signatureBackup,
                    ];

                    $model->signature =
                        $signatureName;
                }

                if (!$model->save(false)) {
                    throw new \RuntimeException(
                        'Unable to update the fisherman profile.'
                    );
                }

                if (
                    (string) $oldDistrict !==
                        (string) $model->district ||
                    (string) $oldDivision !==
                        (string) $model->division
                ) {
                    ProfileFishermanRenew::updateAll(
                        [
                            'district' =>
                                $model->district,

                            'division' =>
                                $model->division,
                        ],
                        [
                            'fisherman_id' =>
                                (string) $model->id,
                        ]
                    );
                }

                CommonService::addApprovalLog(
                    $this->processType,
                    'Update - Submitted',
                    'Submitted',
                    $model->id
                );

                $transaction->commit();

                /*
                 * Database update succeeded.
                 * The old-file backups are no
                 * longer required.
                 */
                $this
                    ->completeFishermanImageReplacements(
                        $fileReplacements
                    );

                /*
                 * When the extension changed, delete
                 * the old file with the old extension.
                 *
                 * When the name is unchanged, this
                 * method does nothing.
                 */
                if (
                    $stagedProfileImage !== null
                ) {
                    $this
                        ->deleteOldFishermanImage(
                            $uploadDirectory,
                            $oldProfileImage,
                            $model->profile_image
                        );
                }

                if (
                    $stagedSignature !== null
                ) {
                    $this
                        ->deleteOldFishermanImage(
                            $uploadDirectory,
                            $oldSignature,
                            $model->signature
                        );
                }

                Yii::$app->session->setFlash(
                    'success',
                    'Fisherman profile updated successfully.'
                );

                if (
                    !UserTypeUtil::hasType(
                        Constant::FISHERMAN
                    )
                ) {
                    return $this->redirect([
                    '/fisherman/view',
                    'token' => SecurityHelper::encryptId(
                        \backend\models\ProfileFisherman::class,
                        $model->id
                    ),
                ]);
                }

                return $this->goHome();
            } catch (\Throwable $e) {
                if (
                    $transaction !== null &&
                    $transaction->isActive
                ) {
                    $transaction->rollBack();
                }

                /*
                 * Remove newly written images and
                 * restore the previous images.
                 */
                $this
                    ->rollbackFishermanImageReplacements(
                        $fileReplacements
                    );

                $model->profile_image =
                    $oldProfileImage;

                $model->signature =
                    $oldSignature;

                Yii::error([
                    'message' =>
                        $e->getMessage(),

                    'exceptionClass' =>
                        get_class($e),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),

                    'fishermanId' =>
                        $model->id,

                    'trace' =>
                        $e->getTraceAsString(),
                ], 'fisherman-update');

                $model->addError(
                    'profile_image',
                    $e->getMessage()
                );

                Yii::$app->session->setFlash(
                    'error',
                    $e->getMessage()
                );
            } finally {
                /*
                 * These files will normally no longer
                 * exist after rename(), but this cleans
                 * up any remaining temporary files.
                 */
                $this->removeFishermanFiles([
                    $stagedProfileImage[
                        'temporaryPath'
                    ] ?? null,

                    $stagedSignature[
                        'temporaryPath'
                    ] ?? null,
                ]);
            }
        }
    }

    return $this->render('update', [
        'model' =>
            $model,

        'districtList' =>
            $districtList,

        'divisionList' =>
            $divisionList,

        'years' =>
            $years,

        'token' => 
            $token,

    ]);
}

    /**
     * @throws NotFoundHttpException
     * @throws UnauthorizedHttpException
     */
   public function actionUpdateNativeData(
    string $token,
    ?string $skipperToken = null
) {
    CommonService::validatePermission(
        $this,
        'fisherman-profile-update'
    );

    /*
     * Decrypt the fisherman token and ensure that it was
     * generated specifically for ProfileFisherman.
     */
    $fishermanId = SecurityHelper::decryptId(
        $token,
        ProfileFisherman::class
    );

    $model = $this->findModel(
        (int) $fishermanId
    );

    /*
     * Tokenization does not replace authorization.
     * A logged-in fisherman can update only their own profile.
     */
    if (
        UserTypeUtil::hasType(
            Constant::FISHERMAN
        )
    ) {
        $loggedInProfileId = (int) (
            Yii::$app->user->identity->profile_id
            ?? 0
        );

        if (
            $loggedInProfileId <= 0
            || $loggedInProfileId !==
                (int) $model->id
        ) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested fisherman profile was not found.'
                )
            );
        }
    }

    /*
     * Validate the optional skipper token.
     *
     * The skipper must belong to the fisherman being updated.
     */
    $skipperModel = null;

    if (
        $skipperToken !== null
        && trim($skipperToken) !== ''
    ) {
        $skipperId =
            SecurityHelper::decryptId(
                $skipperToken,
                Skipper::class
            );

        $skipperModel = Skipper::findOne([
            'id' => (int) $skipperId,
        ]);

        if (
            $skipperModel === null
            || (int) $skipperModel->fisherman_id
                !== (int) $model->id
        ) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested skipper record was not found.'
                )
            );
        }
    }

    /*
     * Only these fields may be changed from this screen.
     *
     * This prevents mass assignment of status, approval_stage,
     * district, user IDs, and other protected attributes.
     */
    $allowedAttributes = [
        'name_sinhala',
        'address_sinhala',
        'name_tamil',
        'address_tamil',
    ];

    /*
     * Remove any attribute that does not exist in the current
     * ProfileFisherman table/model.
     */
    $allowedAttributes = array_values(
        array_filter(
            $allowedAttributes,
            static fn (
                string $attribute
            ): bool => $model->hasAttribute(
                $attribute
            )
        )
    );

    if (empty($allowedAttributes)) {
        Yii::error(
            'No native-language attributes exist on ProfileFisherman.',
            __METHOD__
        );

        throw new ServerErrorHttpException(
            Yii::t(
                'app',
                'The native-language fields are not configured.'
            )
        );
    }

    if ($this->request->isPost) {
        $submittedData = (array) $this->request->post(
            $model->formName(),
            []
        );

        /*
         * Assign only the four permitted fields.
         *
         * Tamil data is kept exactly as entered because the
         * existing database requires Bamini/Avvaiyar encoding.
         */
        foreach (
            $allowedAttributes as $attribute
        ) {
            if (
                array_key_exists(
                    $attribute,
                    $submittedData
                )
            ) {
                $model->setAttribute(
                    $attribute,
                    $submittedData[$attribute]
                );
            }
        }

        /*
         * Validate only the native-language fields.
         */
        if (
            $model->validate(
                $allowedAttributes
            )
        ) {
            /*
             * Update only the permitted columns.
             */
            if (
                !$model->save(
                    false,
                    $allowedAttributes
                )
            ) {
                Yii::error(
                    [
                        'message' =>
                            'Unable to update native fisherman data.',

                        'fishermanId' =>
                            (int) $model->id,

                        'errors' =>
                            $model->getErrors(),
                    ],
                    __METHOD__
                );

                throw new ServerErrorHttpException(
                    Yii::t(
                        'app',
                        'The profile could not be updated.'
                    )
                );
            }

            Yii::$app->session->setFlash(
                'success',
                Yii::t(
                    'app',
                    'Profile updated successfully.'
                )
            );

            /*
             * Return to the tokenized Skipper licence page
             * when this screen was opened from Skipper.
             */
            if ($skipperModel !== null) {
                return $this->redirect([
                    '/skipper/license-view',

                    'token' =>
                        SecurityHelper::encryptId(
                            Skipper::class,
                            (int) $skipperModel->id
                        ),
                ]);
            }

            /*
             * Otherwise return to the fisherman licence.
             */
            return $this->redirect([
                '/fisherman/license-view',

                'token' =>
                    SecurityHelper::encryptId(
                        ProfileFisherman::class,
                        (int) $model->id
                    ),
            ]);
        }
    }

    return $this->render(
        'updateNative',
        [
            'model' => $model,
            'token' => $token,
            'skipperToken' =>
                $skipperToken,
        ]
    );
}

    /**
     * @throws UnauthorizedHttpException
     */
public function actionLicenseView($token)
{
    CommonService::validatePermission(
        $this,
        'license-view'
    );

    $id = SecurityHelper::decryptId(
        (string) $token,
        \backend\models\ProfileFisherman::class
    );

    $model = ProfileFisherman::findOne($id);

    if ($model === null) {
        throw new \yii\web\NotFoundHttpException(
            'Fisherman record was not found.'
        );
    }

    return $this->render('licenseView', [
        'model' => $model,
        'renewModel' => null,
        'pdf' => false,
    ]);
}

public function actionRenewLicenseView($id)
{
    CommonService::validatePermission($this, 'license-view');

    // $id = profile_fisherman_renew.id
    $renewModel = ProfileFishermanRenew::findOne($id);

    if ($renewModel === null) {
        throw new \yii\web\NotFoundHttpException('Fisherman renewal record not found.');
    }

    // Get personal/profile details from profile_fisherman
    $model = ProfileFisherman::findOne($renewModel->fisherman_id);

    if ($model === null) {
        throw new \yii\web\NotFoundHttpException('Original fisherman record not found.');
    }

    return $this->render('licenseView', [
        'model' => $model,
        'renewModel' => $renewModel,
        'pdf' => false,
    ]);
}

    /**
     * @throws CrossReferenceException
     * @throws MpdfException
     * @throws InvalidConfigException
     * @throws PdfParserException
     * @throws UnauthorizedHttpException
     * @throws PdfTypeException
     */
   public function actionLicenseDownload($token): string
{
    CommonService::validatePermission(
        $this,
        'fisherman-license-download'
    );

    $id = SecurityHelper::decryptId(
        (string) $token,
        \backend\models\ProfileFisherman::class
    );

    $model = ProfileFisherman::findOne($id);

    if ($model === null) {
        throw new NotFoundHttpException(
            'Fisherman record was not found.'
        );
    }

    $isCompleted =
        $model->approval_stage === 'Completed'
        && UserTypeUtil::hasType(Constant::PRINT);

    $headingImageData = null;
    $profileImageData = null;
    $hasProfileImage = false;

    if (!$isCompleted) {
        $headingImagePath =
            '/var/mountpoint/uploads/static/DFAR_heading.jpg';

        if (!is_file($headingImagePath)) {
            Yii::error(
                'DFAR heading image not found: '
                . $headingImagePath,
                __METHOD__
            );

            throw new ServerErrorHttpException(
                'DFAR heading image was not found.'
            );
        }

        if (!is_readable($headingImagePath)) {
            Yii::error(
                'DFAR heading image is not readable: '
                . $headingImagePath,
                __METHOD__
            );

            throw new ServerErrorHttpException(
                'DFAR heading image cannot be read.'
            );
        }

        $headingImageData = file_get_contents(
            $headingImagePath
        );

        if (
            $headingImageData === false
            || $headingImageData === ''
        ) {
            throw new ServerErrorHttpException(
                'Unable to load the DFAR heading image.'
            );
        }

        $profileFilename = basename(
            (string) ($model->profile_image ?? '')
        );

        if ($profileFilename !== '') {
            $profileImagePath =
                '/var/mountpoint/uploads/fisherman/'
                . $profileFilename;

            if (
                is_file($profileImagePath)
                && is_readable($profileImagePath)
            ) {
                $profileImageData = file_get_contents(
                    $profileImagePath
                );

                if (
                    $profileImageData !== false
                    && $profileImageData !== ''
                ) {
                    $hasProfileImage = true;
                } else {
                    $profileImageData = null;

                    Yii::warning(
                        'Profile image could not be read: '
                        . $profileImagePath,
                        __METHOD__
                    );
                }
            } else {
                Yii::warning(
                    'Profile image not found or unreadable: '
                    . $profileImagePath,
                    __METHOD__
                );
            }
        }
    }

    if ($isCompleted) {
        $content = $this->renderPartial(
            'license',
            [
                'model' => $model,
                'renewModel' => null,
                'isPdf' => true,
            ]
        );
    } else {
        $content = $this->renderPartial(
            'license-temp',
            [
                'model' => $model,
                'renewModel' => null,
                'isPdf' => true,
                'hasProfileImage' => $hasProfileImage,
            ]
        );
    }

    $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8,
        'defaultFontSize' => 10,

        'format' => $isCompleted
            ? [264, 167]
            : Pdf::FORMAT_A4,

        'orientation' => Pdf::ORIENT_PORTRAIT,
        'destination' => Pdf::DEST_BROWSER,
        'content' => $content,

        'filename' => $isCompleted
            ? 'Fisherman-Licence-' . $model->id . '.pdf'
            : 'Temporary-Fisherman-Licence-'
                . $model->id
                . '.pdf',

        'marginLeft' => $isCompleted ? 0 : 10,
        'marginTop' => $isCompleted ? 0 : 10,
        'marginRight' => $isCompleted ? 0 : 10,
        'marginBottom' => $isCompleted ? 0 : 10,

        'cssInline' => '
            @font-face {
                font-family: "DLSarala";
                src: url("https://msdfar.com/DLSarala.ttf")
                    format("truetype");
            }
        ',
    ]);

    $mpdf = $pdf->getApi();

    $mpdf->showImageErrors = false;

    if (!$isCompleted) {
        $mpdf->imageVars['dfarHeading'] =
            $headingImageData;

        if (
            $hasProfileImage
            && $profileImageData !== null
        ) {
            $mpdf->imageVars['profileImage'] =
                $profileImageData;
        }
    }

    return $pdf->render();
}
    public function actionSearch($q = null, $id = null)
    {
        $officerProfile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query
                ->from('profile_fisherman')
                ->where(['like', 'fisherman_uid', $q])
                ->orWhere(['like', 'nic', $q])
                ->andWhere(["status" => Constant::Active])
                ->andWhere(["district" => $officerProfile->district])
                ->limit(20);


            $command = $query->createCommand();
            $data = $command->queryAll();
            $dataFormated = [];
            foreach ($data as $datum) {
                $dataFormated[] = [
                    "id" => $datum['id'],
                    "text" => "ID-" . $datum['fisherman_uid'] . ", NIC:" . $datum['nic']
                ];
            }
//print_r($dataFormated);exit();
            $out['results'] = array_values($dataFormated);
        }
        return $out;
    }   

    public function actionSearchGlobal($q = null, $id = null)
    {
        $officerProfile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query
                ->from('profile_fisherman')
                ->where(['like', 'fisherman_uid', $q])
                ->orWhere(['like', 'nic', $q])
                ->andWhere(["status" => Constant::Active])
                ->limit(20);


            $command = $query->createCommand();
            $data = $command->queryAll();
            $dataFormated = [];
            foreach ($data as $datum) {
                $dataFormated[] = [
                    "id" => $datum['id'],
                    "text" => "ID-" . $datum['fisherman_uid'] . ", NIC:" . $datum['nic']
                ];
            }
//print_r($dataFormated);exit();
            $out['results'] = array_values($dataFormated);
        }
        return $out;
    }

    public function actionSearchGlobalAll($q = null, $id = null)
    {
        $officerProfile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query
                ->from('profile_fisherman')
                ->where(['like', 'fisherman_uid', $q])
                ->orWhere(['like', 'nic', $q])
                ->limit(20);


            $command = $query->createCommand();
            $data = $command->queryAll();
            $dataFormated = [];
            foreach ($data as $datum) {
                $dataFormated[] = [
                    "id" => $datum['id'],
                    "text" => "ID-" . $datum['fisherman_uid'] . ", NIC:" . $datum['nic']
                ];
            }
//print_r($dataFormated);exit();
            $out['results'] = array_values($dataFormated);
        }
        return $out;
    }

    public static function getStacs()
    {
        $where = [];
        $wherePending = [];
        if (UserTypeUtil::hasType(Constant::FI)) {
            $wherePending = ["division" => Yii::$app->session->get("officer_division"), 'approval_stage' => Constant::FI];
//            $where = ["division" => Yii::$app->session->get("officer_division")];
            $whereActive = ["division" => Yii::$app->session->get("officer_division")];
        }
        if (UserTypeUtil::hasType(Constant::AD)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::AD];
//            $where = ["district" => Yii::$app->session->get("officer_district")];
            $whereActive = ["district" => Yii::$app->session->get("officer_district")];
        }

        if (UserTypeUtil::hasType(Constant::DFI)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DFI];
//            $where = ["district" => Yii::$app->session->get("officer_district")];
            $whereActive = ["district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DO)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DO];
//            $where = ["district" => Yii::$app->session->get("officer_district")];
            $whereActive = ["district" => Yii::$app->session->get("officer_district")];
        }
        $countPending = ProfileFisherman::find()->where(['status' => Constant::Pending])->andWhere($wherePending)->count();
        $countActive = ProfileFisherman::find()->where(['status' => Constant::Active])->andWhere($where)->count();
        $countFinalApproval = ProfileFisherman::find()->where(['status' => Constant::FinalApprovalPending])->andWhere($where)->count();
        $countExpired = ProfileFisherman::find()->where(['status' => Constant::Expired])->andWhere($where)->count();

        return [
            "countPending" => $countPending,
            "countActive" => $countActive,
            "countFinalApproval" => $countFinalApproval,
            "countExpired" => $countExpired,
        ];

    }

    public function actionViewFisherman($fishermanId)
    {
        if (!Util::editPermission()) {
            throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));
        }
        /** @var WebUser $webUser */
        $webUser = Yii::$app->getUser();
        $user = $this->getUser($fishermanId);
        $mainIdentityId = $webUser->getMainIdentityId();

        if ($mainIdentityId != $user->id) {
            /** @var User $user */
//            $user = User::findOne($fishermanId);

            $webUser->login($user, $duration = 0);
            $webUser->setMainIdentityId($mainIdentityId);

            CommonService::addImpersonateLog($fishermanId, $mainIdentityId, "Stared");
        }



        // return $this->redirect(['/site/index']);
        return $this->redirect(['/fisherman-re-correction/index', 'mainId' => $mainIdentityId, 'fishermanId' => $fishermanId]);
        // return $this->redirect(['/kpi/index']);
    }

    /**
     * Finds user by [[username]]
     *
     * @return \common\models\User|null
     */
    protected function getUser($id)
    {
        $user = \common\models\User::findByProfileId($id);
//        print_r($user);exit();
        if ($user == null) {
            $user = new \common\models\User();
            $user->nic = "impersonate_" . $id;
            $user->email = $user->nic . "@hynetz.com";
            $user->type = Constant::FISHERMAN;
            $user->status = 10;
            $user->profile_id = $id;
            $user->setPassword($user->email);
            $user->generateAuthKey();
            $user->generateEmailVerificationToken();

            if ($user->save()) {
                $auth = new AuthAssignment();
                $auth->user_id = $user->id;
                $auth->item_name = "FISHERMAN";
                $auth->save();
            }
        }
//        print_r($user);exit();

        return $user;
    }

    public function actionPrinted($id)
    {
        if (Util::editPermission()) {

            $model = $this->findModel($id);
            $model->printed = 1;
            if ($model->save(false)) {
                return $this->redirect(['license-view', 'id' => $model->id]);
            }
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

   public function actionFishermanPrint()
{
    CommonService::validatePermission($this, "fisherman-list");

    $searchModel = new ProfileFishermanSearch();
    $params = $this->request->queryParams;

    // Force status to Active
    $params['ProfileFishermanSearch']['status'] = Constant::Active;

    $dataProvider = $searchModel->search($params);


    return $this->render('fishermanPrint', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}
public function actionAddprintqueue($id)
{
    // Check how many items are already in the queue for the current user
    $existingQueueCount = IdPrintQueue::find()
        ->where(['user_id' => Yii::$app->user->identity->id, 'status'=> 1])
        ->count(); // Get the count of items already in the queue

    // If there are already 8 items in the queue, show an error
    if ($existingQueueCount >= 8) {
        Yii::$app->session->setFlash('error', 'You cannot add more than 8 print jobs to the queue.');
        return $this->redirect(['fisherman-print']); // Redirect to the desired page
    }

    // Create a new instance of the IdPrintQueue model
    $IdPrintQueueModel = new IdPrintQueue();

    // Assign values to the model attributes
    $type = 1;  // Assuming '1' is the type of print queue you want to set
    $user_id = Yii::$app->user->identity->id;  // Get the current logged-in user's ID
    $print_id = $id;  // Get the print job ID passed to the function
    $status = 1;  // Assuming '1' indicates the status is active or queued

    // Assign the attributes to the model
    $IdPrintQueueModel->user_id = $user_id;
    $IdPrintQueueModel->print_id = $print_id;
    $IdPrintQueueModel->type = $type;
    $IdPrintQueueModel->status = $status;

    // Save the model to the database
    if ($IdPrintQueueModel->save()) {
        Yii::$app->session->setFlash('success', 'The print job has been added to the queue.');
    } else {
        Yii::$app->session->setFlash('error', 'There was an error adding the print job to the queue.');
    }

    // Redirect back to the previous page or the desired page
    return $this->redirect(['fisherman-print']);  // Adjust the redirect URL as necessary
}

        public function actionCancelprintqueue($id)
    {
        // Find the print queue record by its ID
        $queueItem = IdPrintQueue::findOne($id);

        if ($queueItem && $queueItem->user_id == Yii::$app->user->identity->id) {
            // Delete or update the record as canceled
            $queueItem->status = 0;  // Assuming 0 means canceled (you can define your own status values)
            $queueItem->save();

            Yii::$app->session->setFlash('success', 'Print job canceled successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Invalid print job or you do not have permission to cancel this job.');
        }

        // Redirect after cancellation, you can adjust this based on your requirements
        return $this->redirect(['fisherman-print']);  // Redirect to the list or another page
    }

   public function actionLicensePrintTemplate(): string
{
    [$fishermen, $renewalByFishermanId] =
        $this->loadQueuedFishermenForPrint();

    return $this->render(
        'licensePrintTemplate',
        [
            'fishermen' => $fishermen,
            'renewalByFishermanId' => $renewalByFishermanId,
            'pdf' => false,
            'imageSrc' => $this->createPrintImageResolver(false),
        ]
    );
}

public function actionLicensePrintTemplateBack(): string
{
    [$fishermen, $renewalByFishermanId] =
        $this->loadQueuedFishermenForPrint();

    return $this->render(
        'licensePrintTemplateBack',
        [
            'fishermen' => $fishermen,
            'renewalByFishermanId' => $renewalByFishermanId,
            'pdf' => false,
            'imageSrc' => $this->createPrintImageResolver(false),
        ]
    );
}

public function actionSendtoprint(): string
{
    /*
     * This is only a safety margin.
     *
     * The important fix is that createPrintImageResolver(true)
     * now returns short file:/// paths instead of very large Base64
     * strings. This keeps the HTML well below the previous PCRE limit.
     */
    ini_set('pcre.backtrack_limit', '5000000');
    ini_set('pcre.recursion_limit', '1000000');

    [$fishermen, $renewalByFishermanId, $fishermanIds] =
        $this->loadQueuedFishermenForPrint();

    if (empty($fishermen)) {
        throw new BadRequestHttpException(
            'There are no fishermen in the print queue.'
        );
    }

    $tempDir = Yii::getAlias('@runtime/mpdf');

    if (
        !is_dir($tempDir)
        && !mkdir($tempDir, 0775, true)
        && !is_dir($tempDir)
    ) {
        throw new \RuntimeException(
            'Unable to create the mPDF temporary directory.'
        );
    }

    /*
     * These font files must exist in backend/web:
     * - NotoSansTamil.ttf
     * - DLSarala.ttf
     */
    $fontDir = Yii::getAlias('@backend/web');

    $defaultConfig = (new ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',

        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 10,
        'margin_bottom' => 10,

        'tempDir' => $tempDir,

        'fontDir' => array_merge(
            $fontDirs,
            [$fontDir]
        ),

        'fontdata' => $fontData + [
            'notosanstamil' => [
                'R' => 'NotoSansTamil.ttf',
                'useOTL' => 0xFF,
                'useKashida' => 75,
            ],
            'dlsarala' => [
                'R' => 'DLSarala.ttf',
            ],
        ],

        'default_font' => 'notosanstamil',
        'autoScriptToLang' => true,
        'autoLangToFont' => true,
    ]);

    /*
     * Keep true while testing. Change to false after confirming
     * that all images render correctly.
     */
    $mpdf->showImageErrors = true;

    $fontCss = '
        .sinhala-text {
            font-family: dlsarala !important;
            font-size: 30px;
            font-weight: bold;
            color: lightyellow;
            line-height: 1.2;
        }

        .tamil-text {
            font-family: notosanstamil !important;
            font-size: 24px;
            font-weight: bold;
            color: lightyellow;
            line-height: 1.3;
        }
    ';

    $mpdf->WriteHTML(
        $fontCss,
        HTMLParserMode::HEADER_CSS
    );

    /*
     * In PDF mode, this resolver returns local file:/// URLs.
     * It does not embed images as Base64, so the generated HTML
     * stays small and does not exceed pcre.backtrack_limit.
     */
    $imageSrc = $this->createPrintImageResolver(true);

    $frontHtml = $this->renderPartial(
        'licensePrintTemplate',
        [
            'fishermen' => $fishermen,
            'renewalByFishermanId' => $renewalByFishermanId,
            'pdf' => true,
            'imageSrc' => $imageSrc,
        ]
    );

    $mpdf->WriteHTML(
        $frontHtml,
        HTMLParserMode::HTML_BODY
    );

    $mpdf->AddPage();

    $backHtml = $this->renderPartial(
        'licensePrintTemplateBack',
        [
            'fishermen' => $fishermen,
            'renewalByFishermanId' => $renewalByFishermanId,
            'pdf' => true,
            'imageSrc' => $imageSrc,
        ]
    );

    $mpdf->WriteHTML(
        $backHtml,
        HTMLParserMode::HTML_BODY
    );

    /*
     * Mark records as printed only after both PDF pages
     * have been rendered successfully.
     */
    if (!empty($fishermanIds)) {
        ProfileFisherman::updateAll(
            [
                'printed' => 2,
                'printed_date' => date('Y-m-d'),
            ],
            [
                'id' => $fishermanIds,
            ]
        );
    }

    return $mpdf->Output(
        'license.pdf',
        Destination::INLINE
    );
}

/**
 * Load up to eight fishermen from the current user's print queue,
 * together with the latest renewal record for each fisherman.
 *
 * @return array{
 *     0: array,
 *     1: array<string, ProfileFishermanRenew>,
 *     2: array<int>
 * }
 */
private function loadQueuedFishermenForPrint(): array
{
    $userId = (int) Yii::$app->user->id;

    $fishermen = ProfileFisherman::find()
        ->innerJoin(
            'id_print_queue ipq',
            'ipq.print_id = profile_fisherman.id'
        )
        ->where([
            'ipq.user_id' => $userId,
            'ipq.status' => 1,
        ])
        ->orderBy([
            'ipq.id' => SORT_ASC,
        ])
        ->limit(8)
        ->all();

    $fishermanIds = array_values(
        array_unique(
            array_filter(
                array_map(
                    static fn($model) =>
                        isset($model->id)
                            ? (int) $model->id
                            : null,
                    $fishermen
                )
            )
        )
    );

    $renewalRecords = !empty($fishermanIds)
        ? ProfileFishermanRenew::find()
            ->where([
                'fisherman_id' => $fishermanIds,
            ])
            ->orderBy([
                'fisherman_id' => SORT_ASC,
                'id' => SORT_DESC,
            ])
            ->all()
        : [];

    $renewalByFishermanId = [];

    foreach ($renewalRecords as $renewalRecord) {
        $key = (string) $renewalRecord->fisherman_id;

        /*
         * Because the query is ordered by ID descending,
         * the first record for each fisherman is the latest.
         */
        if (!isset($renewalByFishermanId[$key])) {
            $renewalByFishermanId[$key] = $renewalRecord;
        }
    }

    return [
        $fishermen,
        $renewalByFishermanId,
        $fishermanIds,
    ];
}


private function createPrintImageResolver(bool $isPdf): \Closure
{
    return static function (string $relativePath) use ($isPdf): string {
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

        $uploadRoot = realpath('/var/mountpoint/uploads');

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
            || !str_starts_with(
                $realPath,
                $uploadRoot . DIRECTORY_SEPARATOR
            )
        ) {
            Yii::warning(
                'PDF image is missing or unreadable: '
                . $requestedPath,
                __METHOD__
            );

            return '';
        }

        $extension = strtolower(
            pathinfo($realPath, PATHINFO_EXTENSION)
        );

        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
        ];

        if (!in_array($extension, $allowedExtensions, true)) {
            Yii::warning(
                'Unsupported PDF image extension: '
                . $realPath,
                __METHOD__
            );

            return '';
        }

        /*
         * Encode special characters but preserve directory separators.
         * For a Linux absolute path, this produces:
         * file:///var/mountpoint/uploads/...
         */
        $normalizedPath = str_replace(
            DIRECTORY_SEPARATOR,
            '/',
            $realPath
        );

        $encodedPath = str_replace(
            '%2F',
            '/',
            rawurlencode($normalizedPath)
        );

        return 'file://' . $encodedPath;
    };
}

    public function actionPrivacyPolicy(){

    return $this->render('privacyPolicy', [
    ]);

    }


    public function actionDataView()
{
    $searchModel = new ProfileFishermanSearch();
    $params = Yii::$app->request->queryParams;

    $model = null;
    $boatdetails = null;

    if (isset($params['ProfileFishermanSearch']['id'])) {
        $id = $params['ProfileFishermanSearch']['id'];

        // Get fisherman
        $model = ProfileFisherman::findOne($id);
        $skipper = Skipper::find()
                ->where(['fisherman_id' => $id])
                ->one();

        $isSkipper = $skipper !== null;


        // Get boat details using fisherman_id
       $subQuery = FishermanRegisterdBoatLicense::find()
    ->select(['boat_number_id', 'MAX(approved_time) as max_date'])
    ->where(['fisherman_id' => $id])
    ->groupBy('boat_number_id');

$boatdetails = FishermanRegisterdBoatLicense::find()
    ->alias('f')
    ->innerJoin(['latest' => $subQuery],
        'f.boat_number_id = latest.boat_number_id AND f.approved_time = latest.max_date'
    )
    ->where(['f.fisherman_id' => $id])
    ->with('boatNumber') // for boat number relation
    ->all();
    }

    return $this->render('dataView', [
        'searchModel' => $searchModel,
        'model' => $model,
        'boatdetails' => $boatdetails,
    ]);
}


public function actionRenew($id)
{
    if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
        CommonService::validatePermission($this, 'FishermanController-renew');
    }

    $model = Fisherman::findOne($id);

    if ($model === null) {
        throw new \yii\web\NotFoundHttpException('Fisherman record not found.');
    }

    // Original profile_fisherman primary key
    $fishermanId = $model->id;

    $districtList = ArrayHelper::map(
        MFiDistrict::find()
            ->where(['status' => 1])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    if ($this->request->isPost && Util::editPermission()) {

        // Copy existing fisherman details to renewal record
      $data = $model->attributes;

        unset(
            $data['id'],
            $data['approved_time'],
            $data['expire_date'],
            $data['renew'],
            $data['renew_id']
        );

        $modelRenew = new ProfileFishermanRenew();
        $modelRenew->setAttributes($data);

        // Keep the same UID as the original fisherman record
        $modelRenew->fisherman_uid = $model->fisherman_uid;

        $modelRenew->fisherman_id = (string) $fishermanId;
        $modelRenew->renew = 1;
        $modelRenew->status = Constant::Pending;
        $modelRenew->created = date('Y-m-d H:i');
        $modelRenew->approval_stage = (string) Constant::FI;

        $modelRenew->approved_time = null;
        $modelRenew->expire_date = null;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if (!$modelRenew->save()) {
                $transaction->rollBack();

                print_r($modelRenew->getErrors());
                exit;
            }

            /*
             * $modelRenew->id is now the new AUTO_INCREMENT ID
             * of profile_fisherman_renew.
             */
            $updatedRows = Fisherman::updateAll(
                [
                    'renew' => 1,
                    'renew_id' => $modelRenew->id,
                ],
                [
                    'id' => $fishermanId,
                ]
            );

            if ($updatedRows !== 1) {
                throw new \RuntimeException(
                    'Unable to update the original fisherman renewal status.'
                );
            }

            CommonService::addApprovalLog(
                'FISHERMAN_LICENSE_RENEW',
                'Submitted',
                'Renew request',
                $modelRenew->id
            );

            $transaction->commit();

            if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
                return $this->redirect(['view', 'id' => $modelRenew->id]);
            }

            return $this->goHome();

        } catch (\Throwable $e) {
            if ($transaction->isActive) {
                $transaction->rollBack();
            }

            throw $e;
        }
    }

    return $this->render('create', [
        'model' => $model,
        'districtList' => $districtList,
        'renew' => true,
    ]);
}

public static function markeAsExpired()
{
    $today = date('Y-m-d');

    // Expire main fisherman licenses
    Fisherman::updateAll(
        [
            'status' => Constant::Expired,
        ],
        [
            'and',
            ['status' => Constant::Active],
            ['not', ['expire_date' => null]],
            ['<=', 'expire_date', $today],
        ]
    );

    // Expire fisherman renewal licenses
    ProfileFishermanRenew::updateAll(
        [
            'status' => Constant::Expired,
        ],
        [
            'and',
            ['status' => Constant::Active],
            ['not', ['expire_date' => null]],
            ['<=', 'expire_date', $today],
        ]
    );
}

/**
 * Retrieves personal details from the DRP API using an NIC number.
 *
 * @return array
 */
/**
 * Retrieves personal details from the DRP API using an NIC number.
 *
 * @return array
 */
public function actionGetPersonalDetails()
{
    Yii::$app->response->format = Response::FORMAT_JSON;

    /*
     * Normalize NIC:
     * - Remove spaces
     * - Convert V/X to uppercase
     */
    $nic = strtoupper(
        preg_replace(
            '/\s+/',
            '',
            trim(
                (string) Yii::$app->request->post(
                    'nic',
                    ''
                )
            )
        )
    );

    if ($nic === '') {
        return [
            'success' => false,
            'message' => 'NIC is required.',
        ];
    }

    /*
     * Supported NIC formats:
     * Old NIC: 9 digits followed by V or X
     * New NIC: 12 digits
     */
    if (!preg_match('/^(?:\d{9}[VX]|\d{12})$/', $nic)) {
        return [
            'success' => false,
            'message' =>
                'Please enter a valid 10-character or 12-digit NIC number.',
        ];
    }

    try {
        /*
         * Get cached, refreshed or newly generated access token.
         */
        $tokenResult = $this->getPersonalApiAccessToken();

        if (
            !is_array($tokenResult) ||
            empty($tokenResult['success'])
        ) {
            Yii::error([
                'message' =>
                    'Unable to obtain DRP access token.',
                'tokenResult' => $tokenResult,
            ], 'personal-details-api');

            return [
                'success' => false,
                'message' =>
                    $tokenResult['message']
                    ?? 'Unable to authenticate with the personal details service.',
            ];
        }

        $accessToken = trim(
            (string) (
                $tokenResult['accessToken']
                ?? ''
            )
        );

        if ($accessToken === '') {
            Yii::error([
                'message' =>
                    'DRP authentication succeeded, but the access token was empty.',
            ], 'personal-details-api');

            return [
                'success' => false,
                'message' =>
                    'The personal details service did not provide an access token.',
            ];
        }

        /*
         * Call the DRP personal-details API.
         */
        $apiResult = $this->callPersonalDetailsApi(
            $nic,
            $accessToken
        );

        if (
            !is_array($apiResult) ||
            empty($apiResult['success'])
        ) {
            Yii::error([
                'message' =>
                    'Unable to call DRP personal-details API.',
                'nic' => $nic,
                'apiResult' => $apiResult,
            ], 'personal-details-api');

            return [
                'success' => false,
                'message' =>
                    $apiResult['message']
                    ?? 'Unable to retrieve personal details.',
            ];
        }

        /*
         * If the access token is rejected,
         * obtain a new token and retry once.
         */
        if ($this->isAccessTokenUnauthorized($apiResult)) {
            Yii::warning([
                'message' =>
                    'DRP access token was rejected. Retrying with a new token.',
                'nic' => $nic,
                'httpCode' =>
                    $apiResult['httpCode'] ?? 0,
                'requestUuid' =>
                    $apiResult['requestUuid'] ?? '',
            ], 'personal-details-api');

            $tokenResult = $this->getPersonalApiAccessToken(
                true
            );

            if (
                !is_array($tokenResult) ||
                empty($tokenResult['success'])
            ) {
                return [
                    'success' => false,
                    'message' =>
                        $tokenResult['message']
                        ?? 'Unable to renew the personal API access token.',
                ];
            }

            $newAccessToken = trim(
                (string) (
                    $tokenResult['accessToken']
                    ?? ''
                )
            );

            if ($newAccessToken === '') {
                return [
                    'success' => false,
                    'message' =>
                        'The personal details service did not provide a renewed access token.',
                ];
            }

            $apiResult = $this->callPersonalDetailsApi(
                $nic,
                $newAccessToken
            );

            if (
                !is_array($apiResult) ||
                empty($apiResult['success'])
            ) {
                return [
                    'success' => false,
                    'message' =>
                        $apiResult['message']
                        ?? 'Unable to retrieve personal details after renewing the access token.',
                ];
            }

            /*
             * Prevent unlimited retry attempts.
             */
            if ($this->isAccessTokenUnauthorized($apiResult)) {
                Yii::error([
                    'message' =>
                        'DRP rejected the renewed access token.',
                    'nic' => $nic,
                    'httpCode' =>
                        $apiResult['httpCode'] ?? 0,
                    'requestUuid' =>
                        $apiResult['requestUuid'] ?? '',
                    'responseData' =>
                        $apiResult['data'] ?? [],
                ], 'personal-details-api');

                return [
                    'success' => false,
                    'message' =>
                        'The personal details service rejected the renewed access token.',
                ];
            }
        }

        $httpCode = (int) (
            $apiResult['httpCode']
            ?? 0
        );

        $responseData = is_array(
            $apiResult['data'] ?? null
        )
            ? $apiResult['data']
            : [];

        /*
         * Handle invalid NIC, subscription and API errors.
         */
        if ($httpCode < 200 || $httpCode >= 300) {
            $apiMessage = trim(
                (string) (
                    $responseData['message']
                    ?? $responseData['error']
                    ?? ''
                )
            );

            $apiReference = trim(
                (string) (
                    $responseData['value']
                    ?? $apiResult['requestUuid']
                    ?? ''
                )
            );

            Yii::error([
                'message' =>
                    'DRP personal-details request failed.',
                'nic' => $nic,
                'httpCode' => $httpCode,
                'apiCode' =>
                    $responseData['code'] ?? '',
                'apiMessage' => $apiMessage,
                'apiReference' => $apiReference,
                'requestUuid' =>
                    $apiResult['requestUuid'] ?? '',
            ], 'personal-details-api');

            if ($apiMessage === '') {
                if ($httpCode === 404) {
                    $apiMessage =
                        'No personal details were found for this NIC.';
                } elseif ($httpCode >= 500) {
                    $apiMessage =
                        'The personal details service is temporarily unavailable.';
                } else {
                    $apiMessage =
                        'The personal details request was rejected.';
                }
            }

            return [
                'success' => false,
                'message' => $apiMessage,
                'reference' => $apiReference,
            ];
        }

        /*
         * Find the actual person record if the DRP response is wrapped.
         */
        $personData = $this->findPersonData(
            $responseData
        );

        if (empty($personData)) {
            Yii::error([
                'message' =>
                    'Person record was not found in the DRP response.',
                'nic' => $nic,
                'httpCode' => $httpCode,
                'requestUuid' =>
                    $apiResult['requestUuid'] ?? '',
                'topLevelResponseKeys' =>
                    array_keys($responseData),
            ], 'personal-details-api');

            return [
                'success' => false,
                'message' =>
                    'No personal details were found for this NIC.',
            ];
        }

        /*
         * Clean normal text values.
         */
        $cleanValue = static function ($value): string {
            return trim((string) $value);
        };

        /*
         * Clean one address line.
         *
         * DRP address lines may already end with commas or periods.
         * These characters are removed before joining the lines.
         */
        $cleanAddressLine = static function ($value): string {
            $value = trim((string) $value);

            if ($value === '') {
                return '';
            }

            /*
             * Remove one or more trailing:
             * - spaces
             * - commas
             * - periods
             * - semicolons
             * - colons
             */
            $value = preg_replace(
                '/[\s,.;:]+$/u',
                '',
                $value
            );

            return trim((string) $value);
        };

        /*
         * Build one combined address.
         *
         * Result:
         * 425/17/3, මහවත්ත, කැන්දලියද්දපාළුව, ගනේමුල්ල.
         */
        $buildAddress = static function (
            array $addressParts
        ) use (
            $cleanAddressLine
        ): string {
            $cleanParts = [];

            foreach ($addressParts as $part) {
                $part = $cleanAddressLine($part);

                if ($part !== '') {
                    $cleanParts[] = $part;
                }
            }

            if (empty($cleanParts)) {
                return '';
            }

            return implode(', ', $cleanParts) . '.';
        };

        /*
         * Clean English address lines.
         */
        $addressLine1English = $cleanAddressLine(
            $personData['addressLine1English']
            ?? ''
        );

        $addressLine2English = $cleanAddressLine(
            $personData['addressLine2English']
            ?? ''
        );

        $addressLine3English = $cleanAddressLine(
            $personData['addressLine3English']
            ?? ''
        );

        $addressLine4English = $cleanAddressLine(
            $personData['addressLine4English']
            ?? ''
        );

        /*
         * Clean Sinhala address lines.
         */
        $addressLine1Sinhala = $cleanAddressLine(
            $personData['addressLine1Sinhala']
            ?? ''
        );

        $addressLine2Sinhala = $cleanAddressLine(
            $personData['addressLine2Sinhala']
            ?? ''
        );

        $addressLine3Sinhala = $cleanAddressLine(
            $personData['addressLine3Sinhala']
            ?? ''
        );

        $addressLine4Sinhala = $cleanAddressLine(
            $personData['addressLine4Sinhala']
            ?? ''
        );

        /*
         * Clean Tamil address lines.
         */
        $addressLine1Tamil = $cleanAddressLine(
            $personData['addressLine1Tamil']
            ?? ''
        );

        $addressLine2Tamil = $cleanAddressLine(
            $personData['addressLine2Tamil']
            ?? ''
        );

        $addressLine3Tamil = $cleanAddressLine(
            $personData['addressLine3Tamil']
            ?? ''
        );

        $addressLine4Tamil = $cleanAddressLine(
            $personData['addressLine4Tamil']
            ?? ''
        );

        /*
         * Build combined addresses.
         */
        $addressEnglish = $buildAddress([
            $addressLine1English,
            $addressLine2English,
            $addressLine3English,
            $addressLine4English,
        ]);

        $addressSinhala = $buildAddress([
            $addressLine1Sinhala,
            $addressLine2Sinhala,
            $addressLine3Sinhala,
            $addressLine4Sinhala,
        ]);

        $addressTamil = $buildAddress([
            $addressLine1Tamil,
            $addressLine2Tamil,
            $addressLine3Tamil,
            $addressLine4Tamil,
        ]);

        /*
         * Select the best available NIC value returned by DRP.
         */
        $returnedNic = '';

        $possibleNicValues = [
            $personData['latestIDCardNo'] ?? '',
            $personData['idCardNumber'] ?? '',
            $personData['previousIDCardNo'] ?? '',
            $nic,
        ];

        foreach ($possibleNicValues as $possibleNic) {
            $possibleNic = $cleanValue($possibleNic);

            if ($possibleNic !== '') {
                $returnedNic = $possibleNic;
                break;
            }
        }

        return [
            'success' => true,
            'message' =>
                'Personal details loaded successfully.',

            'data' => [
                /*
                 * NIC details.
                 */
                'nic' => $returnedNic,

                'idCardNumber' => $cleanValue(
                    $personData['idCardNumber']
                    ?? ''
                ),

                'latestIDCardNo' => $cleanValue(
                    $personData['latestIDCardNo']
                    ?? ''
                ),

                'previousIDCardNo' => $cleanValue(
                    $personData['previousIDCardNo']
                    ?? ''
                ),

                'prePrintedNumber' => $cleanValue(
                    $personData['prePrintedNumber']
                    ?? ''
                ),

                'nicStatus' => $cleanValue(
                    $personData['nicStatus']
                    ?? ''
                ),

                'issueDate' => $cleanValue(
                    $personData['issueDate']
                    ?? ''
                ),

                /*
                 * Names.
                 */
                'fullNameEnglish' => $cleanValue(
                    $personData['fullNameEnglish']
                    ?? ''
                ),

                'fullNameSinhala' => $cleanValue(
                    $personData['fullNameSinhala']
                    ?? ''
                ),

                'fullNameTamil' => $cleanValue(
                    $personData['fullNameTamil']
                    ?? ''
                ),

                'otherNamesEnglish' => $cleanValue(
                    $personData['otherNamesEnglish']
                    ?? ''
                ),

                'otherNamesSinhala' => $cleanValue(
                    $personData['otherNamesSinhala']
                    ?? ''
                ),

                'otherNamesTamil' => $cleanValue(
                    $personData['otherNamesTamil']
                    ?? ''
                ),

                /*
                 * Basic personal details.
                 */
                'dateOfBirth' => $cleanValue(
                    $personData['dateOfBirth']
                    ?? ''
                ),

                'gender' => $cleanValue(
                    $personData['gender']
                    ?? ''
                ),

                'mobileNumber' => $cleanValue(
                    $personData['contactPhoneNo']
                    ?? ''
                ),

                /*
                 * Combined addresses.
                 *
                 * "address" remains the English combined address
                 * for compatibility with the existing JavaScript.
                 */
                'address' => $addressEnglish,
                'addressEnglish' => $addressEnglish,
                'addressSinhala' => $addressSinhala,
                'addressTamil' => $addressTamil,

                /*
                 * Clean English address lines.
                 */
                'addressLine1English' =>
                    $addressLine1English,

                'addressLine2English' =>
                    $addressLine2English,

                'addressLine3English' =>
                    $addressLine3English,

                'addressLine4English' =>
                    $addressLine4English,

                /*
                 * Clean Sinhala address lines.
                 */
                'addressLine1Sinhala' =>
                    $addressLine1Sinhala,

                'addressLine2Sinhala' =>
                    $addressLine2Sinhala,

                'addressLine3Sinhala' =>
                    $addressLine3Sinhala,

                'addressLine4Sinhala' =>
                    $addressLine4Sinhala,

                /*
                 * Clean Tamil address lines.
                 */
                'addressLine1Tamil' =>
                    $addressLine1Tamil,

                'addressLine2Tamil' =>
                    $addressLine2Tamil,

                'addressLine3Tamil' =>
                    $addressLine3Tamil,

                'addressLine4Tamil' =>
                    $addressLine4Tamil,

                /*
                 * Place of birth.
                 */
                'placeOfBirthEnglish' => $cleanValue(
                    $personData['placeOfBirthEnglish']
                    ?? ''
                ),

                'placeOfBirthSinhala' => $cleanValue(
                    $personData['placeOfBirthSinhala']
                    ?? ''
                ),

                'placeOfBirthTamil' => $cleanValue(
                    $personData['placeOfBirthTamil']
                    ?? ''
                ),

                /*
                 * Profession.
                 */
                'professionEnglish' => $cleanValue(
                    $personData['professionEnglish']
                    ?? ''
                ),

                'professionSinhala' => $cleanValue(
                    $personData['professionSinhala']
                    ?? ''
                ),

                'professionTamil' => $cleanValue(
                    $personData['professionTamil']
                    ?? ''
                ),

                /*
                 * DRP images are Base64 strings.
                 */
                'imageRecordAvailable' =>
                    $personData['imageRecordAvailable']
                    ?? null,

                'photoImage' =>
                    $personData['photoImage']
                    ?? '',

                'frontImage' =>
                    $personData['frontImage']
                    ?? '',

                'backImage' =>
                    $personData['backImage']
                    ?? '',
            ],

            'requestUuid' =>
                $apiResult['requestUuid']
                ?? '',
        ];
    } catch (\Throwable $e) {
        Yii::error([
            'message' => $e->getMessage(),
            'exceptionClass' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'nic' => $nic,
            'trace' => $e->getTraceAsString(),
        ], 'personal-details-api');

        return [
            'success' => false,
            'message' =>
                'An error occurred while retrieving personal details.',
        ];
    }
}


/**
 * Gets a valid access token.
 *
 * Normal flow:
 * 1. Use cached access token or token in params.php.
 * 2. When forceNewToken is true, try refresh token.
 * 3. If refresh token is invalid/expired, generate a new token pair.
 */
private function getPersonalApiAccessToken($forceNewToken = false)
{
    $accessTokenCacheKey = 'personal_api_access_token';
    $refreshTokenCacheKey = 'personal_api_refresh_token';

    $cache = Yii::$app->has('cache') ? Yii::$app->cache : null;

    /*
     * Use the current access token unless a renewed token is required.
     */
    if (!$forceNewToken) {
        $accessToken = $cache ? $cache->get($accessTokenCacheKey) : null;

        if (empty($accessToken)) {
            $accessToken = Yii::$app->params['personalApiBearerToken'] ?? '';
        }

        $accessToken = $this->removeBearerPrefix($accessToken);

        if ($accessToken !== '') {
            return [
                'success' => true,
                'accessToken' => $accessToken,
            ];
        }
    }

    /*
     * Try to refresh the access token first.
     */
    $refreshToken = $cache ? $cache->get($refreshTokenCacheKey) : null;

    if (empty($refreshToken)) {
        $refreshToken = Yii::$app->params['personalApiRefreshToken'] ?? '';
    }

    $refreshToken = $this->removeBearerPrefix($refreshToken);

    if ($refreshToken !== '') {
        $refreshResult = $this->refreshPersonalApiAccessToken($refreshToken);

        if ($refreshResult['success']) {
            if ($cache) {
                $cache->set(
                    $accessTokenCacheKey,
                    $refreshResult['accessToken'],
                    max(60, (int)$refreshResult['expiresIn'] - 60)
                );
            }

            return [
                'success' => true,
                'accessToken' => $refreshResult['accessToken'],
            ];
        }

        /*
         * Do not try token generation for temporary connection errors
         * or unexpected refresh-token service errors.
         */
        if (!$this->isRefreshTokenExpiredOrInvalid($refreshResult)) {
            return [
                'success' => false,
                'message' => $refreshResult['message'],
            ];
        }
    }

    /*
     * Refresh token is unavailable, invalid, or expired.
     * Generate a new access-token / refresh-token pair.
     */
    $tokenPairResult = $this->generatePersonalApiTokenPair();

    if (!$tokenPairResult['success']) {
        return [
            'success' => false,
            'message' => $tokenPairResult['message'],
        ];
    }

    if ($cache) {
        $cache->set(
            $accessTokenCacheKey,
            $tokenPairResult['accessToken'],
            max(60, (int)$tokenPairResult['expiresIn'] - 60)
        );

        $cache->set(
            $refreshTokenCacheKey,
            $tokenPairResult['refreshToken'],
            max(60, (int)$tokenPairResult['refreshExpiresIn'] - 60)
        );
    }

    return [
        'success' => true,
        'accessToken' => $tokenPairResult['accessToken'],
    ];
}


/**
 * Calls the DRP NIC Personal Details API.
 */
private function callPersonalDetailsApi($nic, $token)
{
    $baseUrl = rtrim(
        Yii::$app->params['personalApiBaseUrl'] ?? '',
        '/'
    );

    if ($baseUrl === '') {
        return [
            'success' => false,
            'message' => 'Personal API base URL is not configured.',
        ];
    }

    $apiUrl = $baseUrl
        . '/request-handler-service/org/request';

    $subscriptionId = (int) (
        Yii::$app->params['subscriptionId'] ?? 0
    );

    if ($subscriptionId <= 0) {
        return [
            'success' => false,
            'message' => 'Valid live subscription ID is not configured.',
        ];
    }

    $nic = strtoupper(
        preg_replace('/\s+/', '', trim((string)$nic))
    );

    if (
        !preg_match('/^(?:\d{9}[VX]|\d{12})$/i', $nic)
    ) {
        return [
            'success' => false,
            'message' => 'Invalid NIC format.',
        ];
    }

    $requestUuid = $this->generateUuid();

    $requestBody = [
        'subscriptionId' => $subscriptionId,
        'keys' => [
            'nic' => $nic,
        ],
        'consent' => true,
    ];

    $jsonBody = json_encode(
        $requestBody,
        JSON_UNESCAPED_SLASHES
    );

    if ($jsonBody === false) {
        return [
            'success' => false,
            'message' => 'Unable to prepare the personal-details request.',
        ];
    }

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $jsonBody,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 45,

        /*
         * This is the live environment.
         * SSL verification must remain enabled.
         */
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,

        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'uuid: ' . $requestUuid,
            'Authorization: Bearer '
                . $this->removeBearerPrefix($token),
        ],
    ]);

    $response = curl_exec($ch);

    $httpCode = (int) curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    $curlErrorNumber = curl_errno($ch);
    $curlError = curl_error($ch);

    curl_close($ch);

    if ($response === false) {
        Yii::error([
            'message' =>
                'Personal Details API connection failed.',
            'requestUuid' => $requestUuid,
            'curlErrorNumber' => $curlErrorNumber,
            'curlError' => $curlError,
            'apiUrl' => $apiUrl,
        ], 'personal-details-api');

        return [
            'success' => false,
            'message' =>
                'Unable to connect to the personal details service.',
            'httpCode' => 0,
            'data' => [],
            'requestUuid' => $requestUuid,
        ];
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        Yii::error([
            'message' =>
                'Invalid JSON response from Personal Details API.',
            'requestUuid' => $requestUuid,
            'httpCode' => $httpCode,
            'jsonError' => json_last_error_msg(),
        ], 'personal-details-api');

        return [
            'success' => false,
            'message' =>
                'Invalid response received from the personal details service.',
            'httpCode' => $httpCode,
            'data' => [],
            'requestUuid' => $requestUuid,
        ];
    }

    if (!is_array($data)) {
        $data = [];
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        Yii::error([
            'message' =>
                'Personal Details API returned an error.',
            'requestUuid' => $requestUuid,
            'httpCode' => $httpCode,
            'apiCode' => $data['code'] ?? '',
            'apiMessage' => $data['message'] ?? '',
            'apiReference' => $data['value'] ?? '',
        ], 'personal-details-api');
    }

    return [
        /*
         * Connection succeeded even when DRP returned
         * a non-2xx business/API response.
         */
        'success' => true,
        'httpCode' => $httpCode,
        'data' => $data,
        'requestUuid' => $requestUuid,
    ];
}

/**
 * Calls the DRP Refresh Token API.
 */
private function refreshPersonalApiAccessToken($refreshToken)
{
    $refreshApiUrl = Yii::$app->params['personalApiRefreshTokenUrl'] ?? '';

    $refreshToken = $this->removeBearerPrefix($refreshToken);

    if (empty($refreshApiUrl) || empty($refreshToken)) {
        return [
            'success' => false,
            'message' => 'Refresh token API URL or refresh token is not configured.',
            'httpCode' => 0,
            'data' => [],
        ];
    }

    $requestUuid = $this->generateUuid();

    $requestBody = [
        'refreshToken' => $refreshToken,
    ];

    $ch = curl_init();

    curl_setopt_array($ch, [
    CURLOPT_URL => $refreshApiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($requestBody),
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 30,

    // Temporary: disable SSL verification
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,

    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'uuid: ' . $requestUuid,
    ],
]);

    $response = curl_exec($ch);

    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    curl_close($ch);

    if ($response === false) {
        Yii::error([
            'message' => 'Refresh Token API connection failed.',
            'requestUuid' => $requestUuid,
            'curlError' => $curlError,
        ], 'personal-details-api');

        return [
            'success' => false,
            'message' => 'Unable to connect to the refresh-token service. Error: ' . $curlError,
            'httpCode' => 0,
            'data' => [],
        ];
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'message' => 'Invalid response received from the refresh-token service.',
            'httpCode' => $httpCode,
            'data' => [],
        ];
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        Yii::error([
            'message' => 'Refresh Token API returned an error.',
            'requestUuid' => $requestUuid,
            'httpCode' => $httpCode,
            'apiCode' => $data['code'] ?? '',
            'apiMessage' => $data['message'] ?? '',
        ], 'personal-details-api');

        return [
            'success' => false,
            'message' => $data['message']
                ?? $data['error']
                ?? 'Unable to refresh the personal API access token.',
            'httpCode' => $httpCode,
            'data' => is_array($data) ? $data : [],
        ];
    }

    $newAccessToken = $data['accessToken'] ?? '';
    $expiresIn = (int)($data['expiresIn'] ?? 1800);

    if (empty($newAccessToken)) {
        return [
            'success' => false,
            'message' => 'The refresh-token API did not return a new access token.',
            'httpCode' => $httpCode,
            'data' => $data,
        ];
    }

    return [
        'success' => true,
        'accessToken' => $this->removeBearerPrefix($newAccessToken),
        'expiresIn' => $expiresIn,
    ];
}


/**
 * Calls the DRP Generate Access Token API.
 */
private function generatePersonalApiTokenPair()
{
    $tokenApiUrl = Yii::$app->params['personalApiTokenUrl'] ?? '';

    $username = Yii::$app->params['personalApiUsername'] ?? '';
    $password = Yii::$app->params['personalApiPassword'] ?? '';
    $clientId = Yii::$app->params['personalApiClientId'] ?? '';
    $clientSecret = Yii::$app->params['personalApiClientSecret'] ?? '';

    if (
        empty($tokenApiUrl) ||
        empty($username) ||
        empty($password) ||
        empty($clientId) ||
        empty($clientSecret)
    ) {
        return [
            'success' => false,
            'message' => 'Personal API authentication credentials are not configured.',
        ];
    }

    $requestUuid = $this->generateUuid();

    $requestBody = [
        'username' => $username,
        'password' => $password,
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
    ];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $tokenApiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestBody),
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 30,

        // Sandbox / UAT only. Enable SSL verification in production.
       CURLOPT_SSL_VERIFYPEER => false,
       CURLOPT_SSL_VERIFYHOST => 0,

        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'uuid: ' . $requestUuid,
        ],
    ]);

    $response = curl_exec($ch);

    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    curl_close($ch);

    if ($response === false) {
        Yii::error([
            'message' => 'Generate Token API connection failed.',
            'requestUuid' => $requestUuid,
            'curlError' => $curlError,
        ], 'personal-details-api');

        return [
            'success' => false,
            'message' => 'Unable to connect to the token generation service.',
        ];
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'message' => 'Invalid response received from the token generation service.',
        ];
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        Yii::error([
            'message' => 'Generate Token API returned an error.',
            'requestUuid' => $requestUuid,
            'httpCode' => $httpCode,
            'apiCode' => $data['code'] ?? '',
            'apiMessage' => $data['message'] ?? '',
        ], 'personal-details-api');

        return [
            'success' => false,
            'message' => $data['message']
                ?? $data['error']
                ?? 'Unable to generate a new personal API token pair.',
        ];
    }

    $accessToken = $data['accessToken'] ?? '';
    $refreshToken = $data['refreshToken'] ?? '';

    if (empty($accessToken) || empty($refreshToken)) {
        return [
            'success' => false,
            'message' => 'Generate Token API did not return both accessToken and refreshToken.',
        ];
    }

    return [
        'success' => true,
        'accessToken' => $this->removeBearerPrefix($accessToken),
        'expiresIn' => (int)($data['expiresIn'] ?? 1800),
        'refreshToken' => $this->removeBearerPrefix($refreshToken),
        'refreshExpiresIn' => (int)($data['refreshExpiresIn'] ?? 9000),
    ];
}


/**
 * Checks whether the Personal Details API rejected the access token.
 */
private function isAccessTokenUnauthorized($apiResult)
{
    $httpCode = (int)($apiResult['httpCode'] ?? 0);
    $data = $apiResult['data'] ?? [];

    $apiCode = strtoupper(trim((string)($data['code'] ?? '')));
    $message = strtolower(trim((string)($data['message'] ?? '')));

    return $httpCode === 401
        || $apiCode === '401 UNAUTHORIZED'
        || strpos($message, 'invalid access token') !== false;
}


/**
 * Checks whether the refresh token is expired or invalid.
 */
private function isRefreshTokenExpiredOrInvalid($refreshResult)
{
    $httpCode = (int)($refreshResult['httpCode'] ?? 0);
    $data = $refreshResult['data'] ?? [];

    $apiCode = strtoupper(trim((string)($data['code'] ?? '')));
    $message = strtolower(trim((string)($data['message'] ?? '')));

    return $httpCode === 401
        || (
            $httpCode === 400
            && (
                $apiCode === 'AUTH-400'
                || strpos($message, 'invalid refresh token') !== false
                || strpos($message, 'invalid_grant') !== false
            )
        )
        || strpos($message, 'invalid refresh token') !== false
        || strpos($message, 'invalid_grant') !== false;
}


/**
 * Finds the actual personal-details record in a nested API response.
 */
private function findPersonData($value)
{
    /*
     * Some APIs wrap a JSON object inside a string.
     */
    if (is_string($value)) {
        $decodedValue = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $this->findPersonData($decodedValue);
        }

        return [];
    }

    if (!is_array($value)) {
        return [];
    }

    /*
     * These fields identify the actual person record.
     */
    if (
        array_key_exists('idCardNumber', $value)
        || array_key_exists('fullNameEnglish', $value)
        || array_key_exists('photoImage', $value)
        || array_key_exists('frontImage', $value)
    ) {
        return $value;
    }

    foreach ($value as $item) {
        $personData = $this->findPersonData($item);

        if (!empty($personData)) {
            return $personData;
        }
    }

    return [];
}


/**
 * Removes a possible "Bearer " prefix from token values.
 */
private function removeBearerPrefix($token)
{
    return preg_replace('/^Bearer\s+/i', '', trim((string)$token));
}


/**
 * Generates a UUID v4 for the mandatory DRP uuid header.
 */
private function generateUuid()
{
    $data = random_bytes(16);

    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    return vsprintf(
        '%s%s-%s-%s-%s-%s%s%s',
        str_split(bin2hex($data), 4)
    );
}

public function actionBackfillLocalizedDetails()
{
     set_time_limit(900);
    ignore_user_abort(true);

  
    Yii::$app->response->format =
        Response::FORMAT_JSON;

    CommonService::validatePermission(
        $this,
        'fisherman-profile-update'
    );

    if (
        Yii::$app->user->isGuest ||
        !Util::editPermission()
    ) {
        throw new UnauthorizedHttpException(
            'You are not authorized to run this update.'
        );
    }

    $request = Yii::$app->request;

    /*
     * Required range supplied from the browser console.
     */
    $startId = (int) $request->post(
        'startId',
        0
    );

    $endId = (int) $request->post(
        'endId',
        0
    );

    /*
     * afterId controls the position inside the range.
     *
     * First request:
     * afterId = startId - 1
     *
     * Next requests:
     * afterId = nextAfterId returned by the previous request
     */
    $afterId = (int) $request->post(
        'afterId',
        $startId - 1
    );

    if ($startId <= 0) {
        return [
            'success' => false,
            'message' =>
                'A valid start ID is required.',
        ];
    }

    if ($endId <= 0) {
        return [
            'success' => false,
            'message' =>
                'A valid end ID is required.',
        ];
    }

    if ($endId < $startId) {
        return [
            'success' => false,
            'message' =>
                'The end ID must be greater than or equal to the start ID.',
        ];
    }

    /*
     * Do not allow the cursor to start below the range.
     */
    if ($afterId < ($startId - 1)) {
        $afterId = $startId - 1;
    }

    /*
     * The requested range is already complete.
     */
    if ($afterId >= $endId) {
        return [
            'success' => true,
            'message' =>
                'The requested ID range has been completed.',
            'startId' => $startId,
            'endId' => $endId,
            'processed' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'failed' => 0,
            'nextAfterId' => $afterId,
            'hasMore' => false,
            'errors' => [],
        ];
    }

    /*
     * Always process a maximum of five eligible records.
     */
        $limit = min(
            10,
            max(
                1,
                (int) $request->post(
                    'limit',
                    10
                )
            )
        );
    $requestsPerMinute = min(
        60,
        max(
            1,
            (int) $request->post(
                'requestsPerMinute',
                10
            )
        )
    );

    $dryRun = filter_var(
        $request->post(
            'dryRun',
            true
        ),
        FILTER_VALIDATE_BOOLEAN
    );

    $primaryKey =
        ProfileFisherman::primaryKey()[0]
        ?? 'id';

    /*
     * Select only records inside the requested ID range
     * that still have at least one missing localized field.
     */
    $query = $this->buildLocalizedBackfillRangeQuery(
        $primaryKey,
        $startId,
        $endId,
        $afterId
    );

    $models = (clone $query)
        ->limit($limit)
        ->all();

    if (empty($models)) {
        return [
            'success' => true,
            'message' =>
                'No records in this ID range require Sinhala or Tamil updates.',
            'startId' => $startId,
            'endId' => $endId,
            'dryRun' => $dryRun,
            'processed' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'failed' => 0,
            'nextAfterId' => $afterId,
            'hasMore' => false,
            'errors' => [],
        ];
    }

    /*
     * Obtain one DRP token for this five-record batch.
     */
    $tokenResult =
        $this->getPersonalApiAccessToken();

    if (
        !is_array($tokenResult) ||
        empty($tokenResult['success']) ||
        empty($tokenResult['accessToken'])
    ) {
        return [
            'success' => false,
            'message' =>
                $tokenResult['message']
                ?? 'Unable to obtain a DRP access token.',
        ];
    }

    $accessToken = (string)
        $tokenResult['accessToken'];

    $requestIntervalMicroseconds =
        (int) ceil(
            60000000 /
            $requestsPerMinute
        );

    $lastRequestAt = 0.0;

    $processed = 0;
    $updated = 0;
    $unchanged = 0;
    $failed = 0;

    $lastProcessedId = $afterId;

    $errors = [];

    foreach ($models as $model) {
        $recordId = (int)
            $model->getPrimaryKey();

        $lastProcessedId = $recordId;
        $processed++;

        $originalNic = trim(
            (string) $model->nic
        );

        /*
         * Normalize formatted NIC values.
         *
         * 123456789-V becomes 123456789V
         * 2000-1234-5678 becomes 200012345678
         */
        $nic = strtoupper(
            (string) preg_replace(
                '/[^0-9VX]/i',
                '',
                $originalNic
            )
        );

        /*
         * Old NIC: 9 digits followed by V or X.
         * New NIC: 12 digits.
         */
        if (
            !preg_match(
                '/^(?:\d{9}[VX]|\d{12})$/',
                $nic
            )
        ) {
            $failed++;

            $errors[] = [
                'id' => $recordId,
                'nic' => $originalNic,
                'message' =>
                    'Invalid NIC format.',
            ];

            Yii::warning([
                'message' =>
                    'Backfill skipped because the NIC format is invalid.',
                'recordId' => $recordId,
                'nic' => $originalNic,
            ], 'personal-details-backfill');

            /*
             * Skip the invalid NIC and continue with
             * the next record in the five-record batch.
             */
            continue;
        }

        $personResult =
            $this->fetchDrpPersonForBackfill(
                $nic,
                $accessToken,
                $lastRequestAt,
                $requestIntervalMicroseconds
            );

        if (empty($personResult['success'])) {
            $failed++;

            $errors[] = [
                'id' => $recordId,

                'message' =>
                    $personResult['message']
                    ?? 'DRP request failed.',

                'requestUuid' =>
                    $personResult['requestUuid']
                    ?? '',
            ];

            Yii::error([
                'message' =>
                    'Unable to backfill localized fisherman details.',

                'recordId' => $recordId,

                'requestUuid' =>
                    $personResult['requestUuid']
                    ?? '',

                'apiMessage' =>
                    $personResult['message']
                    ?? '',
            ], 'personal-details-backfill');

            continue;
        }

        $personData =
            $personResult['data'];

        $nameSinhala = trim(
            (string) (
                $personData['fullNameSinhala']
                ?? ''
            )
        );

        $nameTamil = trim(
            (string) (
                $personData['fullNameTamil']
                ?? ''
            )
        );

        $addressSinhala =
            $this->buildDrpLocalizedAddress(
                $personData,
                'Sinhala'
            );

        $addressTamil =
            $this->buildDrpLocalizedAddress(
                $personData,
                'Tamil'
            );

        /*
         * Only these four fields can be updated.
         */
        $updates = [];

        /*
         * Existing values are never overwritten.
         */
        if (
            $this->isEmptyLocalizedValue(
                $model->name_sinhala
            ) &&
            $nameSinhala !== ''
        ) {
            $updates['name_sinhala'] =
                $nameSinhala;
        }

        if (
            $this->isEmptyLocalizedValue(
                $model->address_sinhala
            ) &&
            $addressSinhala !== ''
        ) {
            $updates['address_sinhala'] =
                $addressSinhala;
        }

        if (
            $this->isEmptyLocalizedValue(
                $model->name_tamil
            ) &&
            $nameTamil !== ''
        ) {
            $updates['name_tamil'] =
                $nameTamil;
        }

        if (
            $this->isEmptyLocalizedValue(
                $model->address_tamil
            ) &&
            $addressTamil !== ''
        ) {
            $updates['address_tamil'] =
                $addressTamil;
        }

        if (empty($updates)) {
            $unchanged++;
            continue;
        }

        if (!$dryRun) {
            try {
                /*
                 * Updates only the fields in $updates.
                 */
                $model->updateAttributes(
                    $updates
                );
            } catch (\Throwable $e) {
                $failed++;

                $errors[] = [
                    'id' => $recordId,

                    'message' =>
                        'Database update failed: '
                        . $e->getMessage(),
                ];

                Yii::error([
                    'message' =>
                        $e->getMessage(),

                    'recordId' =>
                        $recordId,

                    'updatedFields' =>
                        array_keys($updates),
                ], 'personal-details-backfill');

                continue;
            }
        }

        $updated++;

        Yii::info([
            'message' =>
                $dryRun
                    ? 'Localized fields found during dry run.'
                    : 'Localized fisherman fields updated.',

            'recordId' =>
                $recordId,

            'updatedFields' =>
                array_keys($updates),

            'requestUuid' =>
                $personResult['requestUuid']
                ?? '',

            'dryRun' =>
                $dryRun,

            'startId' =>
                $startId,

            'endId' =>
                $endId,
        ], 'personal-details-backfill');
    }

    /*
     * Check whether eligible records remain after the
     * last processed ID but still inside the requested range.
     */
    $hasMore =
        $this->buildLocalizedBackfillRangeQuery(
            $primaryKey,
            $startId,
            $endId,
            $lastProcessedId
        )->exists();

    return [
        'success' => true,

        'message' =>
            $dryRun
                ? 'Dry run completed. No database changes were made.'
                : 'Five-record localized-details batch completed.',

        'startId' =>
            $startId,

        'endId' =>
            $endId,

        'dryRun' =>
            $dryRun,

        'requestsPerMinute' =>
            $requestsPerMinute,

        'processed' =>
            $processed,

        'updated' =>
            $updated,

        'unchanged' =>
            $unchanged,

        'failed' =>
            $failed,

        /*
         * Pass this value as afterId for the next batch.
         */
        'nextAfterId' =>
            $lastProcessedId,

        'hasMore' =>
            $hasMore,

        'errors' =>
            $errors,
    ];
}

/**
 * Builds the query for records with missing localized fields.
 */
private function buildLocalizedBackfillQuery(
    string $primaryKey,
    int $afterId
) {
    return ProfileFisherman::find()
        ->andWhere(['>', $primaryKey, $afterId])
        ->andWhere(['not', ['nic' => null]])
        ->andWhere(['<>', 'nic', ''])
        ->andWhere([
            'or',
            ['name_sinhala' => null],
            ['name_sinhala' => ''],
            ['address_sinhala' => null],
            ['address_sinhala' => ''],
            ['name_tamil' => null],
            ['name_tamil' => ''],
            ['address_tamil' => null],
            ['address_tamil' => ''],
        ])
        ->orderBy([$primaryKey => SORT_ASC]);
}

private function fetchDrpPersonForBackfill(
    string $nic,
    string &$accessToken,
    float &$lastRequestAt,
    int $requestIntervalMicroseconds
): array {
    $maximumAttempts = 4;
    $lastRequestUuid = '';

    for ($attempt = 1; $attempt <= $maximumAttempts; $attempt++) {
        $this->waitForDrpRateLimit(
            $lastRequestAt,
            $requestIntervalMicroseconds
        );

        $apiResult = $this->callPersonalDetailsApi(
            $nic,
            $accessToken
        );

        $lastRequestUuid = is_array($apiResult)
            ? (string) ($apiResult['requestUuid'] ?? '')
            : '';

        if (
            !is_array($apiResult)
            || empty($apiResult['success'])
        ) {
            if ($attempt < $maximumAttempts) {
                sleep(min(15, 2 ** $attempt));
                continue;
            }

            return [
                'success' => false,
                'message' => is_array($apiResult)
                    ? ($apiResult['message']
                        ?? 'Unable to connect to DRP.')
                    : 'Invalid DRP API result.',
                'requestUuid' => $lastRequestUuid,
            ];
        }

        if ($this->isAccessTokenUnauthorized($apiResult)) {
            $tokenResult = $this->getPersonalApiAccessToken(true);

            if (
                !is_array($tokenResult)
                || empty($tokenResult['success'])
                || empty($tokenResult['accessToken'])
            ) {
                return [
                    'success' => false,
                    'message' => is_array($tokenResult)
                        ? ($tokenResult['message']
                            ?? 'Unable to renew the DRP access token.')
                        : 'Unable to renew the DRP access token.',
                    'requestUuid' => $lastRequestUuid,
                ];
            }

            $accessToken = (string) $tokenResult['accessToken'];
            continue;
        }

        $httpCode = (int) ($apiResult['httpCode'] ?? 0);

        $responseData = is_array($apiResult['data'] ?? null)
            ? $apiResult['data']
            : [];

        if ($httpCode === 429 && $attempt < $maximumAttempts) {
            sleep(min(60, 10 * $attempt));
            continue;
        }

        if ($httpCode >= 500 && $attempt < $maximumAttempts) {
            sleep(min(30, 2 ** $attempt));
            continue;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            return [
                'success' => false,
                'message' => $responseData['message']
                    ?? $responseData['error']
                    ?? 'DRP rejected the request.',
                'requestUuid' => $lastRequestUuid,
            ];
        }

        $personData = $this->findPersonData($responseData);

        if (empty($personData)) {
            return [
                'success' => false,
                'message' =>
                    'No DRP personal details were found.',
                'requestUuid' => $lastRequestUuid,
            ];
        }

        return [
            'success' => true,
            'data' => $personData,
            'requestUuid' => $lastRequestUuid,
        ];
    }

    return [
        'success' => false,
        'message' =>
            'DRP request failed after multiple attempts.',
        'requestUuid' => $lastRequestUuid,
    ];
}

private function waitForDrpRateLimit(
    float &$lastRequestAt,
    int $requestIntervalMicroseconds
): void {
    if ($lastRequestAt > 0) {
        $elapsedMicroseconds = (int) (
            (microtime(true) - $lastRequestAt) * 1000000
        );

        $remainingMicroseconds = $requestIntervalMicroseconds
            - $elapsedMicroseconds;

        if ($remainingMicroseconds > 0) {
            usleep($remainingMicroseconds);
        }
    }

    $lastRequestAt = microtime(true);
}

private function buildDrpLocalizedAddress(
    array $personData,
    string $language
): string {
    if (!in_array($language, ['Sinhala', 'Tamil'], true)) {
        return '';
    }

    $addressParts = [];

    for ($line = 1; $line <= 4; $line++) {
        $key = 'addressLine' . $line . $language;
        $value = trim((string) ($personData[$key] ?? ''));

        if ($value === '') {
            continue;
        }

        /*
         * DRP lines often already end in commas or periods.
         * Remove trailing punctuation before joining them.
         */
        $value = preg_replace('/[\s,.;:]+$/u', '', $value);
        $value = trim((string) $value);

        if ($value !== '') {
            $addressParts[] = $value;
        }
    }

    if (empty($addressParts)) {
        return '';
    }

    return implode(', ', $addressParts) . '.';
}

private function isEmptyLocalizedValue($value): bool
{
    return trim((string) $value) === '';
}

/**
 * Validates and stages a genuine JPEG or PNG image.
 *
 * It supports both manually selected files and DRP Base64 images
 * converted to File objects in the browser.
 *
 * @return array{temporaryPath:string,extension:string,mimeType:string}
 */
private function stageFishermanImage(
    UploadedFile $uploadedFile,
    string $uploadDirectory,
    string $fieldLabel
): array {
    if ($uploadedFile->error !== UPLOAD_ERR_OK) {
        throw new \RuntimeException(
            $fieldLabel . ' could not be uploaded.'
        );
    }

    $maximumFileSize = 5 * 1024 * 1024;
    $uploadedSize = (int) $uploadedFile->size;

    if ($uploadedSize <= 0) {
        throw new \RuntimeException(
            $fieldLabel . ' is empty.'
        );
    }

    if ($uploadedSize > $maximumFileSize) {
        throw new \RuntimeException(
            $fieldLabel . ' must not exceed 5 MB.'
        );
    }

    if (
        empty($uploadedFile->tempName)
        || !is_file($uploadedFile->tempName)
    ) {
        throw new \RuntimeException(
            'Unable to read the uploaded '
            . strtolower($fieldLabel)
            . '.'
        );
    }

    $imageInformation = @getimagesize(
        $uploadedFile->tempName
    );

    if ($imageInformation === false) {
        throw new \RuntimeException(
            $fieldLabel . ' is not a valid image.'
        );
    }

    $detectedMimeType = strtolower(
        trim((string) ($imageInformation['mime'] ?? ''))
    );

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    if (!isset($allowedMimeTypes[$detectedMimeType])) {
        throw new \RuntimeException(
            'Only genuine JPEG and PNG '
            . strtolower($fieldLabel)
            . ' files are allowed.'
        );
    }

    if (class_exists(\finfo::class)) {
        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
        $fileInfoMimeType = strtolower(
            trim((string) $fileInfo->file($uploadedFile->tempName))
        );

        /*
         * Enforce Fileinfo only when it recognizes one of the allowed
         * image types. Some systems return application/octet-stream.
         */
        if (
            isset($allowedMimeTypes[$fileInfoMimeType])
            && $fileInfoMimeType !== $detectedMimeType
        ) {
            throw new \RuntimeException(
                $fieldLabel . ' content type is inconsistent.'
            );
        }
    }

    $actualExtension = $allowedMimeTypes[$detectedMimeType];

    $providedExtension = strtolower(
        trim((string) $uploadedFile->extension)
    );

    if (
        $providedExtension !== ''
        && !in_array(
            $providedExtension,
            ['jpg', 'jpeg', 'png'],
            true
        )
    ) {
        throw new \RuntimeException(
            'Only JPEG and PNG '
            . strtolower($fieldLabel)
            . ' files are allowed.'
        );
    }

    if (
        in_array($providedExtension, ['jpg', 'jpeg'], true)
        && $actualExtension !== 'jpg'
    ) {
        throw new \RuntimeException(
            $fieldLabel . ' extension does not match its content.'
        );
    }

    if (
        $providedExtension === 'png'
        && $actualExtension !== 'png'
    ) {
        throw new \RuntimeException(
            $fieldLabel . ' extension does not match its content.'
        );
    }

    /*
     * The folder must already exist, as requested. This code does not
     * create it. tempnam() returns false when it is unavailable.
     */
    $temporaryPath = @tempnam(
        $uploadDirectory,
        'fisherman_upload_'
    );

    if ($temporaryPath === false) {
        throw new \RuntimeException(
            'Unable to stage the '
            . strtolower($fieldLabel)
            . '. Confirm the existing fisherman upload folder is writable.'
        );
    }

    $imageContents = file_get_contents(
        $uploadedFile->tempName
    );

    if ($imageContents === false || $imageContents === '') {
        @unlink($temporaryPath);

        throw new \RuntimeException(
            'Unable to read the uploaded '
            . strtolower($fieldLabel)
            . '.'
        );
    }

    $writtenBytes = file_put_contents(
        $temporaryPath,
        $imageContents,
        LOCK_EX
    );

    if (
        $writtenBytes === false
        || $writtenBytes !== strlen($imageContents)
    ) {
        @unlink($temporaryPath);

        throw new \RuntimeException(
            'Unable to stage the '
            . strtolower($fieldLabel)
            . '.'
        );
    }

    @chmod($temporaryPath, 0644);

    return [
        'temporaryPath' => $temporaryPath,
        'extension' => $actualExtension,
        'mimeType' => $detectedMimeType,
    ];
}

private function generateFishermanImageName(
    int $fishermanId,
    string $imageType,
    string $extension
): string {
    if ($fishermanId <= 0) {
        throw new \RuntimeException(
            'A valid fisherman ID is required.'
        );
    }

    if (!in_array($extension, ['jpg', 'png'], true)) {
        throw new \RuntimeException(
            'Invalid fisherman image extension.'
        );
    }

    $safeImageType = preg_replace(
        '/[^a-zA-Z0-9_-]/',
        '',
        $imageType
    );

    if ($safeImageType === '') {
        throw new \RuntimeException(
            'Invalid fisherman image type.'
        );
    }

   return $fishermanId
        . '_'
        . $safeImageType
        . '.'
        . $extension;
}

private function replaceStagedFishermanImage(
    string $temporaryPath,
    string $finalPath
): ?string {
    if (!is_file($temporaryPath)) {
        throw new \RuntimeException(
            'The staged image file was not found.'
        );
    }

    $backupPath = null;

    /*
     * Preserve the existing file before replacing it.
     */
    if (is_file($finalPath)) {
        try {
            $randomSuffix =
                bin2hex(
                    random_bytes(8)
                );
        } catch (\Throwable $e) {
            $randomSuffix =
                str_replace(
                    '.',
                    '',
                    uniqid('', true)
                );
        }

        $backupPath =
            $finalPath
            . '.backup_'
            . $randomSuffix;

        if (
            !@rename(
                $finalPath,
                $backupPath
            )
        ) {
            throw new \RuntimeException(
                'Unable to create a backup of the existing image.'
            );
        }
    }

    try {
        if (
            !@rename(
                $temporaryPath,
                $finalPath
            )
        ) {
            throw new \RuntimeException(
                'Unable to replace the existing image.'
            );
        }

        @chmod(
            $finalPath,
            0644
        );

        return $backupPath;
    } catch (\Throwable $e) {
        /*
         * Remove a partially created replacement.
         */
        if (is_file($finalPath)) {
            @unlink($finalPath);
        }

        /*
         * Restore the original image.
         */
        if (
            $backupPath !== null &&
            is_file($backupPath)
        ) {
            @rename(
                $backupPath,
                $finalPath
            );
        }

        throw $e;
    }
}
private function deleteOldFishermanImage(
    string $uploadDirectory,
    $oldFilename,
    $newFilename
): void {
    $oldFilename = basename(trim((string) $oldFilename));
    $newFilename = basename(trim((string) $newFilename));

    if (
        $oldFilename === ''
        || $oldFilename === $newFilename
    ) {
        return;
    }

    $oldPath = $uploadDirectory . $oldFilename;

    if (is_file($oldPath) && !@unlink($oldPath)) {
        Yii::warning([
            'message' =>
                'Unable to delete the previous fisherman image.',
            'path' => $oldPath,
        ], 'fisherman-image');
    }
}

/**
 * Deletes temporary or newly created files during cleanup.
 */
private function removeFishermanFiles(array $paths): void
{
    foreach ($paths as $path) {
        if (is_string($path) && is_file($path)) {
            @unlink($path);
        }
    }
}

private function buildLocalizedBackfillRangeQuery(
    string $primaryKey,
    int $startId,
    int $endId,
    int $afterId
) {
    return ProfileFisherman::find()
        ->andWhere([
            '>=',
            $primaryKey,
            $startId,
        ])
        ->andWhere([
            '<=',
            $primaryKey,
            $endId,
        ])
        ->andWhere([
            '>',
            $primaryKey,
            $afterId,
        ])
        ->andWhere([
            'not',
            ['nic' => null],
        ])
        ->andWhere([
            '<>',
            'nic',
            '',
        ])
        ->andWhere([
            'or',

            ['name_sinhala' => null],
            ['name_sinhala' => ''],

            ['address_sinhala' => null],
            ['address_sinhala' => ''],

            ['name_tamil' => null],
            ['name_tamil' => ''],

            ['address_tamil' => null],
            ['address_tamil' => ''],
        ])
        ->orderBy([
            $primaryKey => SORT_ASC,
        ]);
}


/**
 * Removes new files and restores the previous files
 * when the database operation fails.
 */
private function rollbackFishermanImageReplacements(
    array $replacements
): void {
    foreach (
        array_reverse($replacements)
        as $replacement
    ) {
        $finalPath = trim(
            (string) (
                $replacement['finalPath']
                ?? ''
            )
        );

        $backupPath = trim(
            (string) (
                $replacement['backupPath']
                ?? ''
            )
        );

        if (
            $finalPath !== '' &&
            is_file($finalPath)
        ) {
            if (!@unlink($finalPath)) {
                Yii::warning([
                    'message' =>
                        'Unable to remove a failed replacement image.',

                    'path' =>
                        $finalPath,
                ], 'fisherman-image');
            }
        }

        if (
            $backupPath !== '' &&
            is_file($backupPath)
        ) {
            if (
                !@rename(
                    $backupPath,
                    $finalPath
                )
            ) {
                Yii::error([
                    'message' =>
                        'Unable to restore the previous fisherman image.',

                    'backupPath' =>
                        $backupPath,

                    'finalPath' =>
                        $finalPath,
                ], 'fisherman-image');
            }
        }
    }
}

/**
 * Deletes temporary backups after the database
 * transaction has completed successfully.
 */
private function completeFishermanImageReplacements(
    array $replacements
): void {
    foreach ($replacements as $replacement) {
        $backupPath = trim(
            (string) (
                $replacement['backupPath']
                ?? ''
            )
        );

        if (
            $backupPath !== '' &&
            is_file($backupPath) &&
            !@unlink($backupPath)
        ) {
            Yii::warning([
                'message' =>
                    'Unable to delete a completed image backup.',

                'path' =>
                    $backupPath,
            ], 'fisherman-image');
        }
    }
}

/**
 * Move a staged fisherman image to its permanent location.
 *
 * @throws \RuntimeException
 */
private function moveStagedFishermanImage(
    string $temporaryPath,
    string $destinationPath
): void {
    if (
        !is_file($temporaryPath)
        || !is_readable($temporaryPath)
    ) {
        throw new \RuntimeException(
            'The staged image file does not exist or cannot be read.'
        );
    }

    $destinationDirectory = dirname($destinationPath);

    if (
        !is_dir($destinationDirectory)
        && !\yii\helpers\FileHelper::createDirectory(
            $destinationDirectory,
            0775,
            true
        )
    ) {
        throw new \RuntimeException(
            'Unable to create the fisherman image upload directory.'
        );
    }

    /*
     * Avoid unexpectedly replacing an existing file.
     */
    if (file_exists($destinationPath)) {
        throw new \RuntimeException(
            'A fisherman image with the same name already exists.'
        );
    }

    /*
     * rename() may fail when the temporary and permanent directories
     * are on different filesystems. In that case, use copy and delete.
     */
    if (!@rename($temporaryPath, $destinationPath)) {
        if (!@copy($temporaryPath, $destinationPath)) {
            throw new \RuntimeException(
                'Unable to move the uploaded fisherman image.'
            );
        }

        if (!@unlink($temporaryPath)) {
            @unlink($destinationPath);

            throw new \RuntimeException(
                'Unable to remove the staged fisherman image.'
            );
        }
    }

    @chmod($destinationPath, 0644);
}

public function actionTestLicenseToken()
{
    $originalId = 15;

    $token = SecurityHelper::encryptId(
        \backend\models\ProfileFisherman::class,
        $originalId
    );

    $decodedId = SecurityHelper::decryptId(
        $token,
        \backend\models\ProfileFisherman::class
    );

    return $this->asJson([
        'originalId' => $originalId,
        'decodedId' => $decodedId,
        'matched' => $originalId === $decodedId,
        'modelClass' =>
            \backend\models\ProfileFisherman::class,
    ]);
}
}


