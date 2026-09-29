<?php

namespace backend\controllers;

use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\DepartureBoats;
use backend\models\DistrictGearTypes;
use backend\models\Files;
use backend\models\FishermanRegisterdBoat;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\HighseasLicense;
use backend\models\HighseasLicenseSearch;
use backend\models\MApprovalWorkflow;
use backend\models\MGearTypes;
use backend\models\MFishTypes;
use backend\models\MRequeredDocuments;
use backend\models\PaymentLog;
use backend\models\ProfileFisherman;
use backend\models\ProfileOfficer;
use backend\models\Skipper;
use backend\models\SubPaymentTypes;
use backend\services\CommonService;
use backend\services\Util;
use kartik\mpdf\Pdf;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\components\Controller;

use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * HighseasLicenseController implements the CRUD actions for HighseasLicense model.
 */
class HighseasLicenseController extends Controller
{
    public $processType = "HIGHSEAS_LICENSE";
    public $processTypeRenew = "HIGHSEAS_LICENSE_RENEW";

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

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

        return $behaviors;
    }

    public function actionIndex()
    {
        CommonService::validatePermission($this, "HighseasLicenseController-list");
//        $this->markeAsExpired();
        $searchModel = new HighseasLicenseSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HighseasLicense model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(string $token)
    {
        CommonService::validatePermission(
            $this,
            'HighseasLicenseController-office-view'
        );

        $id = SecurityHelper::decryptId(
            $token,
            HighseasLicense::class
        );

        $model = $this->findModel($id);
        $this->assertLicenseOwnership($model);

        $process = (int) $model->renew === 1
            ? $this->processTypeRenew
            : $this->processType;

        $approvalFlow = CommonService::getApprovalProcess(
            $model,
            $process,
            $id,
            false
        );

        $workflow = MApprovalWorkflow::findOne([
            'type' => $process,
        ]);

        if ($workflow === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Approval workflow was not found.')
            );
        }

        $uploadedFileCount = Files::find()
            ->where([
                'type' => $workflow->id,
                'process_id' => $id,
            ])
            ->andWhere(['!=', 'file_type', -999])
            ->count();

        $requiredDocumentCount = MRequeredDocuments::find()
            ->where([
                'type' => $workflow->id,
                'status' => 1,
            ])
            ->count();

        $validated = $model->validate()
            && $uploadedFileCount >= $requiredDocumentCount;

        if ($this->request->isPost && Util::editPermission()) {
            $model = CommonService::markApprovalStage(
                $approvalFlow,
                $model,
                $id,
                $process
            );

            if (empty($model->license_number)) {
                $uid = Constant::$HIGHSEAS_LICENSE_FORMAT;
                $uid = str_replace('{year}', date('y'), $uid);
                $uid = str_replace(
                    '{number}',
                    sprintf('%05d', $model->id),
                    $uid
                );
                $uid = str_replace(
                    '{district_code}',
                    $model->district0->code,
                    $uid
                );
                $model->license_number = $uid;
            }

            CommonService::logValidateErrors($model);

            if ($model->save()) {
                return $this->redirect([
                    '/highseas-license/view',
                    'token' => SecurityHelper::encryptId(
                        HighseasLicense::class,
                        $model->id
                    ),
                ]);
            }
        }

        $paymentHistory = [];

        if (
            (int) $model->status === 100
            || (int) $model->status === 101
        ) {
            $paymentHistory = PaymentLog::find()
                ->where([
                    'type' => $process,
                    'process_id' => $id,
                ])
                ->all();
        }

        $files = Files::find()
            ->where([
                'type' => $workflow->id,
                'process_id' => $id,
            ])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'approvalHistory' =>
                $approvalFlow['approvalHistory'],
            'showRejectBtn' =>
                $approvalFlow['showRejectBtn'],
            'showApproveBtn' =>
                $approvalFlow['showApproveBtn'],
            'paymentHistory' => $paymentHistory,
            'process' => $process,
            'validated' => $validated,
            'files' => $files,
        ]);
    }

    public function actionCreate(
        string $boat,
        ?string $mainGear = null
    ) {
        CommonService::validatePermission(
            $this,
            'HighseasLicenseController-create'
        );

        /*
         * The boat token represents fisherman_registerd_boat.id.
         */
        $boatId = SecurityHelper::decryptId(
            $boat,
            FishermanRegisterdBoat::class
        );

        $registeredBoat = FishermanRegisterdBoat::findOne($boatId);

        if ($registeredBoat === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Registered boat was not found.')
            );
        }

        $boatRegistration = $this->findLatestBoatRegistration(
            $boatId
        );

        $this->validateDepartureBoat($boatId);

        $mainGearId = null;

        if ($mainGear !== null && trim($mainGear) !== '') {
            $mainGearId = SecurityHelper::decryptId(
                $mainGear,
                MGearTypes::class
            );

            if (MGearTypes::findOne($mainGearId) === null) {
                throw new NotFoundHttpException(
                    Yii::t('app', 'Main gear type was not found.')
                );
            }
        }

        $resolvedFishermanId = (int) (
            $registeredBoat->fisherman_id ?? 0
        );

        if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
            $loggedInProfileId = (int) (
                Yii::$app->user->identity->profile_id ?? 0
            );

            if (
                $loggedInProfileId <= 0
                || $resolvedFishermanId !== $loggedInProfileId
            ) {
                throw new NotFoundHttpException(
                    Yii::t('app', 'Registered boat was not found.')
                );
            }

            if (ProfileFisherman::findOne($loggedInProfileId) === null) {
                throw new NotFoundHttpException(
                    Yii::t('app', 'Fisherman profile was not found.')
                );
            }

            $resolvedFishermanId = $loggedInProfileId;
        }

        if ($resolvedFishermanId <= 0) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Boat owner was not found.')
            );
        }

        $model = new HighseasLicense();
        $model->loadDefaultValues();
        $model->fisherman_id = $resolvedFishermanId;
        $model->boat_registration_id = $boatId;
        $model->district = $boatRegistration->district;
        $model->division = $boatRegistration->division;
        $model->renew = 0;

        if ($mainGearId !== null) {
            $model->main_gear_type = $mainGearId;
        }

        if ($model->fishing_gear_type === null) {
            $model->fishing_gear_type = '';
        }

        $skipperList = ArrayHelper::map(
            Skipper::find()
                ->where(['status' => Constant::Active])
                ->all(),
            'id',
            'skipper_uid'
        );

        $fishermanList = [
            $resolvedFishermanId =>
                (string) ($model->fisherman->first_name ?? '')
                . ' '
                . (string) ($model->fisherman->last_name ?? ''),
        ];

        $boatNumberList = [
            $boatId =>
                (string) ($registeredBoat->boatNumber->boat_number ?? ''),
        ];

        $allowedGearIds = $this->getAllowedDivisionGearIds($model);
        $errors = [];

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

            /* Reapply trusted server-side values. */
            $model->fisherman_id = $resolvedFishermanId;
            $model->boat_registration_id = $boatId;
            $model->district = $boatRegistration->district;
            $model->division = $boatRegistration->division;
            $model->renew = 0;
            $model->approval_stage = (string) Constant::FI;
            $model->status = 1;

            if ($mainGearId !== null) {
                $model->main_gear_type = $mainGearId;
            }

            $selectedGearIds = $this->filterAllowedIds(
                $this->normalizeIdList(
                    $this->request->post('divisionGears', [])
                ),
                $allowedGearIds
            );

            $model->fishing_gear_type = implode(
                ',',
                $selectedGearIds
            );

            if (empty($selectedGearIds)) {
                $model->addError(
                    'fishing_gear_type',
                    Yii::t(
                        'app',
                        'Please select at least one gear type.'
                    )
                );
            }

            $model->unloading_sites = implode(
                ',',
                $this->normalizeIdList($model->unloading_sites)
            );

            $this->setCreatedValue($model);

            if (!$model->hasErrors() && $model->save()) {
                CommonService::addApprovalLog(
                    $this->processType,
                    'Submitted',
                    'Submitted',
                    $model->id
                );

                Yii::$app->session->setFlash(
                    'success',
                    Yii::t(
                        'app',
                        'High Seas License application submitted successfully.'
                    )
                );

                if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
                    return $this->goHome();
                }

                return $this->redirect([
                    '/highseas-license/view',
                    'token' => SecurityHelper::encryptId(
                        HighseasLicense::class,
                        $model->id
                    ),
                ]);
            }

            $errors = $model->getErrors();
            CommonService::logValidateErrors($model);
        }

        $model->unloading_sites = $this->normalizeIdList(
            $model->unloading_sites
        );

        return $this->render('create', [
            'model' => $model,
            'skipperList' => $skipperList,
            'fishermanList' => $fishermanList,
            'boatNumberList' => $boatNumberList,
            'errors' => $errors,
        ]);
    }

    public function actionRenew(
        string $token,
        ?string $mainGear = null
    ) {
        CommonService::validatePermission(
            $this,
            'HighseasLicenseController-renew'
        );

        $id = SecurityHelper::decryptId(
            $token,
            HighseasLicense::class
        );

        $originalModel = $this->findModel($id);
        $this->assertLicenseOwnership($originalModel);
        $this->validateDepartureBoat(
            (int) $originalModel->boat_registration_id
        );

        $hasLaterRenewal = HighseasLicense::find()
            ->where([
                'fisherman_id' => $originalModel->fisherman_id,
                'boat_registration_id' =>
                    $originalModel->boat_registration_id,
                'renew' => 1,
            ])
            ->andWhere(['>', 'id', $originalModel->id])
            ->exists();

        if ($hasLaterRenewal) {
            throw new BadRequestHttpException(
                Yii::t(
                    'app',
                    'A renewal request already exists for this license.'
                )
            );
        }

        $model = new HighseasLicense();
        $model->setAttributes(
            $originalModel->getAttributes(),
            false
        );
        $model->id = null;
        $model->setIsNewRecord(true);
        $model->renew = 1;
        $model->license_number = null;
        $model->approved_time = null;
        $model->expire_date = null;
        $model->approval_stage = (string) Constant::FI;
        $model->status = 1;
        $this->setCreatedValue($model, true);

        if ($mainGear !== null && trim($mainGear) !== '') {
            $mainGearId = SecurityHelper::decryptId(
                $mainGear,
                MGearTypes::class
            );

            if (MGearTypes::findOne($mainGearId) === null) {
                throw new NotFoundHttpException(
                    Yii::t('app', 'Main gear type was not found.')
                );
            }

            $model->main_gear_type = $mainGearId;
        }

        $skipperList = ArrayHelper::map(
            Skipper::find()
                ->where(['status' => Constant::Active])
                ->all(),
            'id',
            'skipper_uid'
        );

        $allowedGearIds = $this->getAllowedDivisionGearIds($model);
        $errors = [];

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
            $model->boat_registration_id =
                $originalModel->boat_registration_id;
            $model->district = $originalModel->district;
            $model->division = $originalModel->division;
            $model->renew = 1;
            $model->license_number = null;
            $model->approved_time = null;
            $model->expire_date = null;
            $model->approval_stage = (string) Constant::FI;
            $model->status = 1;
            $this->setCreatedValue($model, true);

            $selectedGearIds = $this->filterAllowedIds(
                $this->normalizeIdList(
                    $this->request->post('divisionGears', [])
                ),
                $allowedGearIds
            );

            $model->fishing_gear_type = implode(
                ',',
                $selectedGearIds
            );

            if (empty($selectedGearIds)) {
                $model->addError(
                    'fishing_gear_type',
                    Yii::t(
                        'app',
                        'Please select at least one gear type.'
                    )
                );
            }

            $model->unloading_sites = implode(
                ',',
                $this->normalizeIdList($model->unloading_sites)
            );

            if (!$model->hasErrors() && $model->save()) {
                CommonService::addApprovalLog(
                    $this->processTypeRenew,
                    'Submitted',
                    '',
                    $model->id
                );

                Yii::$app->session->setFlash(
                    'success',
                    Yii::t(
                        'app',
                        'High Seas License renewal submitted successfully.'
                    )
                );

                if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
                    return $this->goHome();
                }

                return $this->redirect([
                    '/highseas-license/view',
                    'token' => SecurityHelper::encryptId(
                        HighseasLicense::class,
                        $model->id
                    ),
                ]);
            }

            $errors = $model->getErrors();
            CommonService::logValidateErrors($model);
        }

        $model->unloading_sites = $this->normalizeIdList(
            $model->unloading_sites
        );

        return $this->render('create', [
            'model' => $model,
            'skipperList' => $skipperList,
            'errors' => $errors,
        ]);
    }

    public function actionUpdate(string $token)
    {
        CommonService::validatePermission(
            $this,
            'HighseasLicenseController-update'
        );

        $id = SecurityHelper::decryptId(
            $token,
            HighseasLicense::class
        );

        $model = $this->findModel($id);
        $this->assertLicenseOwnership($model);

        $skipperList = ArrayHelper::map(
            Skipper::find()
                ->where(['status' => Constant::Active])
                ->all(),
            'id',
            'skipper_uid'
        );

        $allowedGearIds = $this->getAllowedDivisionGearIds($model);
        $errors = [];

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t(
                        'app',
                        'You are not allowed to perform this action.'
                    )
                );
            }

            $trustedFishermanId = $model->fisherman_id;
            $trustedBoatId = $model->boat_registration_id;
            $trustedDistrict = $model->district;
            $trustedDivision = $model->division;

            $model->load($this->request->post());

            $model->fisherman_id = $trustedFishermanId;
            $model->boat_registration_id = $trustedBoatId;
            $model->district = $trustedDistrict;
            $model->division = $trustedDivision;

            $selectedGearIds = $this->filterAllowedIds(
                $this->normalizeIdList(
                    $this->request->post('divisionGears', [])
                ),
                $allowedGearIds
            );

            $model->fishing_gear_type = implode(
                ',',
                $selectedGearIds
            );

            if (empty($selectedGearIds)) {
                $model->addError(
                    'fishing_gear_type',
                    Yii::t(
                        'app',
                        'Please select at least one gear type.'
                    )
                );
            }

            $model->unloading_sites = implode(
                ',',
                $this->normalizeIdList($model->unloading_sites)
            );

            CommonService::logValidateErrors($model);

            if (!$model->hasErrors() && $model->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    Yii::t(
                        'app',
                        'High Seas License updated successfully.'
                    )
                );

                return $this->redirect([
                    '/highseas-license/view',
                    'token' => SecurityHelper::encryptId(
                        HighseasLicense::class,
                        $model->id
                    ),
                ]);
            }

            $errors = $model->getErrors();
        }

        $model->unloading_sites = $this->normalizeIdList(
            $model->unloading_sites
        );
        $model->renew = (bool) $model->renew;

        return $this->render('update', [
            'model' => $model,
            'skipperList' => $skipperList,
            'errors' => $errors,
        ]);
    }

    public function actionDelete(string $token): Response
    {
        CommonService::validatePermission(
            $this,
            'HighseasLicenseController-delete'
        );

        $id = SecurityHelper::decryptId(
            $token,
            HighseasLicense::class
        );

        $this->findModel($id);

        /* Deletion remains disabled intentionally. */
        return $this->redirect(['/highseas-license/index']);
    }

    private function assertLicenseOwnership(
        HighseasLicense $model
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
                Yii::t('app', 'High Seas License was not found.')
            );
        }
    }

    private function findLatestBoatRegistration(
        int $boatId
    ): FishermanRegisterdBoatLicense {
        $boatRegistration = FishermanRegisterdBoatLicense::find()
            ->where(['id' => $boatId])
            ->orderBy(['nid' => SORT_DESC])
            ->one();

        if ($boatRegistration === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Boat registration was not found.')
            );
        }

        return $boatRegistration;
    }

    private function validateDepartureBoat(
        int $boatId
    ): DepartureBoats {
        $boatReg = DepartureBoats::findOne($boatId);

        if (
            $boatReg === null
            || $boatReg->status !== 'Departure Allowed'
        ) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'Departure is not allowed for this boat | මෙම යාත්‍රාවට ගමන්වාර ලබාගැනීමට අවසර නැත | இந்தப் படகுக்கு புறப்பாடு அனுமதி இல்லை.'
                )
            );
        }

        if (
            empty($boatReg->latestBoatRegExpire)
            || strtotime($boatReg->latestBoatRegExpire) < time()
        ) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'ඔබගේ යාත්‍රාවේ ලියාපදිංචිය කල් ඉකුත් වී ඇත. | You do not have a valid boat registration license. | உங்களிடம் செல்லுபடியாகும் படகு பதிவு உரிமம் இல்லை.'
                )
            );
        }

        return $boatReg;
    }

    private function normalizeIdList(mixed $value): array
    {
        if (is_string($value)) {
            $value = trim($value) === ''
                ? []
                : explode(',', $value);
        }

        if (!is_array($value)) {
            return [];
        }

        return array_values(
            array_unique(
                array_filter(
                    array_map('intval', $value),
                    static fn (int $id): bool => $id > 0
                )
            )
        );
    }

    private function getAllowedDivisionGearIds(
        HighseasLicense $model
    ): array {
        $allowedIds = [];

        foreach (self::getDivisionGears($model) as $item) {
            $id = (int) ($item['id'] ?? 0);

            if ($id > 0) {
                $allowedIds[] = $id;
            }
        }

        return array_values(array_unique($allowedIds));
    }

    private function filterAllowedIds(
        array $submittedIds,
        array $allowedIds
    ): array {
        return array_values(
            array_intersect($submittedIds, $allowedIds)
        );
    }

    private function setCreatedValue(
        HighseasLicense $model,
        bool $force = false
    ): void {
        if (
            $model->hasAttribute('created')
            && ($force || empty($model->created))
        ) {
            $model->created = date('Y-m-d H:i:s');
        }
    }

    protected function findModel($id)
    {
        if (($model = HighseasLicense::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    public function actionPayment(string $token)
    {
        CommonService::validatePermission(
            $this,
            'HighseasLicenseController-payment'
        );

        $id = SecurityHelper::decryptId(
            $token,
            HighseasLicense::class
        );

        $boat = $this->findModel($id);
        $this->assertLicenseOwnership($boat);

        $process = (int) $boat->renew === 1
            ? $this->processTypeRenew
            : $this->processType;

        $paymentLog = new PaymentLog();
        $paymentLog->type = $process;

        $workflow = MApprovalWorkflow::findOne([
            'type' => $process,
        ]);

        if ($workflow === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Payment workflow was not found.')
            );
        }

        $paymentTypes = SubPaymentTypes::find()
            ->where([
                'payment_type_id' => $workflow->payment_type,
            ])
            ->asArray()
            ->all();

        $boatLength = $boat->boatRegistration->boatNumber->length;

        if ($boatLength === null || (float) $boatLength === 0.0) {
            $boatLength =
                $boat->boatRegistration
                    ->boatNumber
                    ->boatDesign
                    ->length ?? 0;
        }

        foreach ($paymentTypes as $item) {
            $range = explode('-', (string) $item['Code']);

            if (count($range) !== 2) {
                continue;
            }

            $minimum = (float) $range[0];
            $maximum = (float) $range[1];

            if (
                (float) $boatLength > $minimum
                && (float) $boatLength <= $maximum
            ) {
                $paymentLog->amount = $item['Amount'];
                break;
            }
        }

        $paymentLog->process_id = $id;
        $paymentLog->status = 1;

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

            if ($paymentLog->load($this->request->post())) {
                $file = UploadedFile::getInstance(
                    $paymentLog,
                    'file'
                );

                if ($file !== null) {
                    $fileName =
                        $boat->id
                        . $process
                        . '-payment.'
                        . $file->extension;

                    $file->saveAs(
                        Constant::$FILE_UPLOAD_PATH
                        . 'payment/'
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
                            $boat->id
                        );
                        CommonService::markAsPaid($boat, $process);
                    } else {
                        CommonService::addApprovalLog(
                            $process,
                            'Paid',
                            'Marked as Paid',
                            $boat->id
                        );
                        $boat->status = 100;
                    }

                    $boat->save();
                }

                return $this->redirect([
                    '/highseas-license/view',
                    'token' => SecurityHelper::encryptId(
                        HighseasLicense::class,
                        $id
                    ),
                ]);
            }
        }

        return $this->render('../payment/create', [
            'model' => $paymentLog,
        ]);
    }

    public function actionPaymentApprove(
        ?string $token = null
    ) {
        throw new UnauthorizedHttpException(
            Yii::t(
                'app',
                'You dont have permission to run this operation.'
            )
        );
    }

    public function actionLicenseView(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'HighseasLicenseController-license-view'
        );

        $id = SecurityHelper::decryptId(
            $token,
            HighseasLicense::class
        );

        $model = $this->findModel($id);
        $this->assertLicenseOwnership($model);

        return $this->render('licenseView', [
            'model' => $model,
        ]);
    }

    public function actionLicenseDownload(string $token): string
    {
        CommonService::validatePermission(
            $this,
            'HighseasLicenseController-license-download'
        );

        $id = SecurityHelper::decryptId(
            $token,
            HighseasLicense::class
        );

        $model = $this->findModel($id);
        $this->assertLicenseOwnership($model);

        $workflowType = (int) $model->renew === 1
            ? 'HIGHSEAS_LICENSE_RENEW'
            : 'HIGHSEAS_LICENSE';

        $officer = CommonService::getApprovedOfficer(
            $model->id,
            $workflowType
        );

        if (
            empty($officer)
            && $workflowType === 'HIGHSEAS_LICENSE_RENEW'
        ) {
            $officer = CommonService::getApprovedOfficer(
                $model->id,
                'HIGHSEAS_LICENSE'
            );
        }

        if ($officer instanceof \yii\db\ActiveRecord) {
            $officer = $officer->toArray();
        }

        if (!is_array($officer)) {
            $officer = [];
        }

        $nationalLogoPath =
            '/var/mountpoint/uploads/static/national_Logo2.jpg';

        $boatLicensePath =
            '/var/mountpoint/uploads/static/boatlicense_1.jpg';

        $readRequiredImage = static function (
            string $filePath,
            string $imageName
        ): string {
            if (!is_file($filePath)) {
                Yii::error(
                    $imageName . ' was not found: ' . $filePath,
                    __METHOD__
                );

                throw new ServerErrorHttpException(
                    $imageName . ' was not found.'
                );
            }

            if (!is_readable($filePath)) {
                Yii::error(
                    $imageName . ' is not readable: ' . $filePath,
                    __METHOD__
                );

                throw new ServerErrorHttpException(
                    $imageName . ' cannot be read.'
                );
            }

            $imageData = file_get_contents($filePath);

            if ($imageData === false || $imageData === '') {
                Yii::error(
                    'Unable to read '
                    . $imageName
                    . ': '
                    . $filePath,
                    __METHOD__
                );

                throw new ServerErrorHttpException(
                    'Unable to load ' . $imageName . '.'
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
            'Boat licence image'
        );

        $signatureFilename = basename(
            (string) ($officer['signature'] ?? '')
        );

        $signatureData = null;
        $hasOfficerSignature = false;

        if ($signatureFilename !== '') {
            $signaturePath =
                '/var/mountpoint/uploads/officer/signature/'
                . $signatureFilename;

            if (
                is_file($signaturePath)
                && is_readable($signaturePath)
            ) {
                $signatureData = file_get_contents($signaturePath);

                if (
                    $signatureData !== false
                    && $signatureData !== ''
                ) {
                    $hasOfficerSignature = true;
                } else {
                    Yii::warning(
                        'Officer signature could not be read: '
                        . $signaturePath,
                        __METHOD__
                    );
                }
            } else {
                Yii::warning(
                    'Officer signature was not found or unreadable: '
                    . $signaturePath,
                    __METHOD__
                );
            }
        }

        $content = $this->renderPartial(
            'license',
            [
                'model' => $model,
                'officer' => $officer,
                'hasOfficerSignature' =>
                    $hasOfficerSignature,
                'isPdf' => true,
            ]
        );

        $licenseNumber = trim(
            (string) ($model->license_number ?? '')
        );

        if ($licenseNumber === '') {
            $licenseNumber = (string) $model->id;
        }

        $safeLicenseNumber = preg_replace(
            '/[^A-Za-z0-9_-]+/',
            '_',
            $licenseNumber
        );

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'defaultFontSize' => 45,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'filename' =>
                'HIGHSEAS-'
                . $safeLicenseNumber
                . '.pdf',
            'marginLeft' => 10,
            'marginTop' => 5,
            'marginRight' => 10,
            'marginBottom' => 5,
        ]);

        $mpdf = $pdf->getApi();
        $mpdf->showImageErrors = false;
        $mpdf->imageVars['nationalLogo'] = $nationalLogoData;
        $mpdf->imageVars['boatLicense'] = $boatLicenseData;

        if (
            $hasOfficerSignature
            && is_string($signatureData)
        ) {
            $mpdf->imageVars['officerSignature'] =
                $signatureData;
        }

        return $pdf->render();
    }

    public static function markeAsExpired()
    {
        $expiredLicense = HighseasLicense::find()->where(["<=", "expire_date", date("Y-m-d")])->andWhere(["status" => Constant::Active])->all();
        foreach ($expiredLicense as $item) {
            $item->status = Constant::Expired;
            if ($item->save(false)) {
            } else {
            }
        }
    }


    public static function getStacs()
    {
        $where = [];
        $wherePending = [];
        if (UserTypeUtil::hasType(Constant::FI)) {
            $wherePending = ["division" => Yii::$app->session->get("officer_division"), 'approval_stage' => Constant::FI];

            $where = ["division" => Yii::$app->session->get("officer_division")];
        }
        if (UserTypeUtil::hasType(Constant::AD)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::AD];

            $where = ["district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DFI)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DFI];

            $where = ["district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DO)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DO];

            $where = ["district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DM)) {
            $wherePending = ['approval_stage' => Constant::DM];

        }
        if (UserTypeUtil::hasType(Constant::DG)) {
            $wherePending = ['approval_stage' => Constant::DG];

        }
        $countPending = HighseasLicense::find()->where(['status' => Constant::Pending])->andWhere($wherePending)->count();
        $countActive = HighseasLicense::find()->where(['status' => Constant::Active])->andWhere($where)->count();
        $countFinalApproval = HighseasLicense::find()->where(['status' => Constant::FinalApprovalPending])->andWhere($where)->count();
        $countExpired = HighseasLicense::find()->where(['status' => Constant::Expired])->andWhere($where)->count();

        return [
            "countPending" => $countPending,
            "countActive" => $countActive,
            "countFinalApproval" => $countFinalApproval,
            "countExpired" => $countExpired,
        ];

    }

    public static function getDivisionGears(HighseasLicense $model): array
    {
        $divisionGearData = [];
        $selected = [];
        if (isset($model->fishing_gear_type) && $model->fishing_gear_type != null) {
//            $model->fishing_gear_type = implode(',', $model->fishing_gear_type);
            $selected = explode(',', $model->fishing_gear_type);//converting to array...
        }
        $divisionGears = CommonService::getDivisionGearTypesArray($model->main_gear_type, $model->division);
        $count = 0;
        foreach ($divisionGears as $divisionGear) {
            $divisionGearData[$count]["id"] = $divisionGear->id;
            $divisionGearData[$count]["selected"] = in_array($divisionGear->id, $selected) ? "checked" : "";
            $divisionGearData[$count]["name"] = $divisionGear->name;
            $divisionGearData[$count]["subGear"] = $divisionGear->subGear->description;
            $divisionGearData[$count]["extra"] = str_replace("\"", " ", str_replace("{", " ", str_replace("}", " ", $divisionGear->extra)));
            $times = [];

            foreach (explode(",", $divisionGear->fishing_time_durations) as $time) {
                $times[] = Constant::$FishingDuration[$time];
            }
            $divisionGearData[$count]["times"] = implode(", ", $times);
            $fishTypes = MFishTypes::find()->select("name")->where(["IN", "id", explode(",", $divisionGear->fish_species)])->asArray()->all();
            $fish = [];
            foreach ($fishTypes as $fishType) {
                $fish[] = $fishType["name"];
            }
            $divisionGearData[$count]["fishTypes"] = implode(", ", $fish);
            $count++;
        }

        return $divisionGearData;
    }
}
