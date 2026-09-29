<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\DepartureBoats;
use backend\models\Files;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\MApprovalWorkflow;
use backend\models\MFiDistrict;
use backend\models\MFishTypes;
use backend\models\MGearTypes;
use backend\models\MLandingSite;
use backend\models\MRequeredDocuments;
use backend\models\NationalLicense;
use backend\models\NationalLicenseSearch;
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
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use backend\components\Controller;
use backend\components\SecurityHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
use yii\web\ServerErrorHttpException;

/**
 * NationalLicenseController implements the CRUD actions for NationalLicense model.
 */
class NationalLicenseController extends Controller
{
    public $processType = "NATIONAL_LICENSE";

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'except' => [],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all NationalLicense models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this, "NationalLicenseController-list");
//        $this->markeAsExpired();
        $searchModel = new NationalLicenseSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NationalLicense model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnauthorizedHttpException
     */
    public function actionView($token)
{
    CommonService::validatePermission(
        $this,
        'NationalLicenseController-office-view'
    );

    $id = SecurityHelper::decryptId(
        (string) $token,
        \backend\models\NationalLicense::class
    );

    $model = $this->findModel($id);
    $validated = false;

    $workflow = MApprovalWorkflow::find()
        ->where([
            'type' => $this->processType,
        ])
        ->one();

    $fileCount = Files::find()
        ->where([
            'type' => $workflow->id,
            'process_id' => $id,
        ])
        ->andWhere([
            '!=',
            'file_type',
            -999,
        ])
        ->count();

    $requiredDocumentCount = MRequeredDocuments::find()
        ->where([
            'type' => $workflow->id,
            'status' => 1,
        ])
        ->count();

    if ($model->validate()) {
        $validated = true;
    }

    $approvalFlow = CommonService::getApprovalProcess(
        $model,
        $this->processType,
        $id,
        false
    );

    if ($this->request->isPost && Util::editPermission()) {
        $model = CommonService::markApprovalStage(
            $approvalFlow,
            $model,
            $id,
            $this->processType
        );

        if (
            $model->license_number === null
            || $model->license_number === ''
        ) {
            $boatType =
                $model
                    ->boatRegistration
                    ->boatNumber
                    ->boatType
                    ->code;

            $uid = Constant::$NATIONAL_LICENSE_FORMAT;

            $uid = str_replace(
                '{year}',
                date('y'),
                $uid
            );

            $uid = str_replace(
                '{boatType}',
                $boatType,
                $uid
            );

            $uid = str_replace(
                '{number}',
                sprintf('%05d', $model->id),
                $uid
            );

            $uid = str_replace(
                '{district_code}',
                $model->fisheriesDistrict->code,
                $uid
            );

            $model->license_number = $uid;
        }

        if ($model->save()) {
            return $this->redirect([
                '/national-license/view',
                'token' => SecurityHelper::encryptId(
                    \backend\models\NationalLicense::class,
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
                'type' => $this->processType,
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
        'process' => $this->processType,
        'validated' => $validated,
        'files' => $files,
    ]);
}

    /**
     * Creates a new NationalLicense model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     * @throws UnauthorizedHttpException
     */
 public function actionCreate(
    string $boat,
    ?string $mainGear = null
) {
    CommonService::validatePermission(
        $this,
        'NationalLicenseController-create'
    );

    /*
     * The token contains fisherman_registerd_boat.id.
     */
    $boatId = SecurityHelper::decryptId(
        $boat,
        FishermanRegisterdBoatLicense::class
    );

    /*
     * This model uses nid as its primary key, so explicitly
     * search using the id column.
     */
    $boatRegistration = FishermanRegisterdBoatLicense::find()
        ->where([
            'id' => $boatId,
        ])
        ->orderBy([
            'nid' => SORT_DESC,
        ])
        ->one();

    if ($boatRegistration === null) {
        Yii::warning([
            'message' => 'Boat registration was not found.',
            'boatId' => $boatId,
        ], 'national-license-create');

        throw new NotFoundHttpException(
            Yii::t('app', 'Boat registration was not found.')
        );
    }

    /*
     * Load the related departure boat record.
     */
    $boatReg = DepartureBoats::findOne($boatId);

    if ($boatReg === null) {
        Yii::warning([
            'message' => 'Departure boat record was not found.',
            'boatId' => $boatId,
            'boatLicenseNid' => $boatRegistration->nid,
        ], 'national-license-create');

        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'Departure boat record was not found.'
            )
        );
    }

    if ($boatReg->status !== 'Departure Allowed') {
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

    /*
     * Decrypt and validate the optional main gear token.
     */
    $mainGearId = null;

    if ($mainGear !== null && trim($mainGear) !== '') {
        $mainGearId = SecurityHelper::decryptId(
            $mainGear,
            MGearTypes::class
        );

        if (MGearTypes::findOne($mainGearId) === null) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'Main gear type was not found.'
                )
            );
        }
    }

    $readOnly = [];
    $errors = [];

    $model = new NationalLicense();
    $model->loadDefaultValues();

    $gearTypes = ArrayHelper::map(
        MGearTypes::find()
            ->asArray()
            ->all(),
        'id',
        'description'
    );

    $districtList = ArrayHelper::map(
        MFiDistrict::find()
            ->where([
                'status' => 1,
            ])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    $landingSite = ArrayHelper::map(
        MLandingSite::find()
            ->where([
                'status' => 1,
            ])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    /*
     * Resolve the fisherman.
     */
    if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
        $resolvedFishermanId = (int) (
            Yii::$app->user->identity->profile_id ?? 0
        );

        if ($resolvedFishermanId <= 0) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'Fisherman profile was not found.'
                )
            );
        }

        $fishermanProfile = ProfileFisherman::findOne(
            $resolvedFishermanId
        );

        if ($fishermanProfile === null) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'Fisherman profile was not found.'
                )
            );
        }

        $readOnly['fisherman_id'] = true;
    } else {
        $resolvedFishermanId = (int) (
            $boatRegistration->fisherman_id ?? 0
        );

        if ($resolvedFishermanId <= 0) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'Boat owner was not found.'
                )
            );
        }
    }

    /*
     * Set trusted values from the boat registration.
     */
    $model->boat_registration_id = $boatId;
    $model->fisherman_id = $resolvedFishermanId;
    $model->fisheries_district =
        $boatRegistration->district;
    $model->division =
        $boatRegistration->division;
    $model->landing_site =
        $boatRegistration->landing_site;

    $readOnly['boat_registration_id'] = true;

    if ($mainGearId !== null) {
        $model->main_gear_type = $mainGearId;
    }

    /*
     * Load the division gears before processing POST.
     *
     * The checkbox IDs come from getDivisionGears(), not directly
     * from the MGearTypes list.
     */
    $divisionGearData = $this->getDivisionGears(
        $model
    );

    $allowedDivisionGearIds = [];

    foreach ((array) $divisionGearData as $divisionGearItem) {
        $divisionGearId = (int) (
            $divisionGearItem['id'] ?? 0
        );

        if ($divisionGearId > 0) {
            $allowedDivisionGearIds[] =
                $divisionGearId;
        }
    }

    $allowedDivisionGearIds = array_values(
        array_unique($allowedDivisionGearIds)
    );

    if ($this->request->isPost) {
        /*
         * Fishermen may submit this form without the office-only
         * edit permission.
         */
        if (
            !UserTypeUtil::hasType(Constant::FISHERMAN)
            && !Util::editPermission()
        ) {
            throw new \yii\web\ForbiddenHttpException(
                Yii::t(
                    'app',
                    'You are not allowed to perform this action.'
                )
            );
        }

        $model->load(
            $this->request->post()
        );

        /*
         * Reapply trusted fields after load() to prevent request
         * manipulation.
         */
        $model->boat_registration_id = $boatId;
        $model->fisherman_id = $resolvedFishermanId;
        $model->fisheries_district =
            $boatRegistration->district;
        $model->division =
            $boatRegistration->division;
        $model->landing_site =
            $boatRegistration->landing_site;

        if ($mainGearId !== null) {
            $model->main_gear_type = $mainGearId;
        }

        /*
         * Read the custom divisionGears[] checkboxes.
         */
        $selectedGearTypes = $this->request->post(
            'divisionGears',
            []
        );

        if (!is_array($selectedGearTypes)) {
            $selectedGearTypes = [];
        }

        /*
         * Fallback for a hidden division_gear_types model field,
         * if it exists in the form.
         */
        if (empty($selectedGearTypes)) {
            $nationalLicensePost =
                $this->request->post(
                    $model->formName(),
                    []
                );

            $postedDivisionGearTypes =
                $nationalLicensePost[
                    'division_gear_types'
                ] ?? '';

            if (
                is_string($postedDivisionGearTypes)
                && trim($postedDivisionGearTypes) !== ''
            ) {
                $selectedGearTypes = explode(
                    ',',
                    $postedDivisionGearTypes
                );
            }
        }

        /*
         * Convert submitted IDs to positive integers.
         */
        $selectedGearTypes = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'intval',
                        $selectedGearTypes
                    ),
                    static function (
                        int $gearId
                    ): bool {
                        return $gearId > 0;
                    }
                )
            )
        );

        /*
         * Only allow IDs that were generated for this boat.
         */
        $validGearTypes = array_values(
            array_intersect(
                $selectedGearTypes,
                $allowedDivisionGearIds
            )
        );

        $model->division_gear_types = implode(
            ',',
            $validGearTypes
        );

        if (empty($validGearTypes)) {
            $model->addError(
                'division_gear_types',
                Yii::t(
                    'app',
                    'Please select at least one gear type.'
                )
            );
        }

        /*
         * Update engine information when applicable.
         */
        $boatRegistrationSaved = true;

        $boatType = $boatRegistration
            ->boatNumber
            ?->boat_type;

        if (
            $boatType !== null
            && (int) $boatType !== 1
        ) {
            $boatRegistration->load(
                $this->request->post()
            );

            if (
                $boatRegistration->validate([
                    'engine_type',
                    'engine_serial_number',
                ])
            ) {
                $boatRegistrationSaved =
                    $boatRegistration->save(
                        false,
                        [
                            'engine_type',
                            'engine_serial_number',
                        ]
                    );
            } else {
                $boatRegistrationSaved = false;

                $errors = array_merge(
                    $errors,
                    $boatRegistration->getErrors()
                );
            }
        }

        $model->approval_stage =
            (string) Constant::FI;
        $model->status = 1;

        /*
         * The created column cannot be null.
         */
        if (empty($model->created)) {
            $model->created = date(
                'Y-m-d H:i:s'
            );
        }

        $modelSaved = false;

        if (
            $boatRegistrationSaved
            && !$model->hasErrors()
        ) {
            $modelSaved = $model->save();
        }

        if ($modelSaved) {
            CommonService::addApprovalLog(
                $this->processType,
                'Submitted',
                '',
                $model->id
            );

            Yii::info([
                'message' =>
                    'National licence application submitted.',
                'nationalLicenseId' => $model->id,
                'fishermanId' =>
                    $model->fisherman_id,
                'boatRegistrationId' =>
                    $model->boat_registration_id,
                'divisionGearTypes' =>
                    $model->division_gear_types,
            ], 'national-license-create');

            Yii::$app->session->setFlash(
                'success',
                Yii::t(
                    'app',
                    'National License application submitted successfully.'
                )
            );

            if (
                UserTypeUtil::hasType(
                    Constant::FISHERMAN
                )
            ) {
                return $this->redirect([
                    '/fisherman/profile',
                ]);
            }

            return $this->redirect([
                '/national-license/view',
                'token' => SecurityHelper::encryptId(
                    NationalLicense::class,
                    $model->id
                ),
            ]);
        }

        $errors = array_merge(
            $errors,
            $model->getErrors()
        );

        Yii::warning([
            'message' =>
                'National licence creation failed.',
            'boatId' => $boatId,
            'boatLicenseNid' =>
                $boatRegistration->nid,
            'allowedDivisionGearIds' =>
                $allowedDivisionGearIds,
            'selectedGearTypes' =>
                $selectedGearTypes,
            'validGearTypes' =>
                $validGearTypes,
            'divisionGearTypesValue' =>
                $model->division_gear_types,
            'errors' => $errors,
        ], 'national-license-create');

        /*
         * Regenerate checkbox data so submitted gears remain
         * selected after a failed submission.
         */
        $divisionGearData = $this->getDivisionGears(
            $model
        );
    }

    if ($model->division_gear_types === null) {
        $model->division_gear_types = '';
    }

    return $this->render('create', [
        'model' => $model,
        'gearTypes' => $gearTypes,
        'divisionGearData' => $divisionGearData,
        'districtList' => $districtList,
        'landingSite' => $landingSite,
        'readOnly' => $readOnly,
        'errors' => $errors,
        'boatRegistration' => $boatRegistration,
    ]);
}
  /**
     * @throws BadRequestHttpException
     * @throws UnauthorizedHttpException
     */
    public function actionRenew(string $token)
{
    CommonService::validatePermission(
        $this,
        'NationalLicenseController-renew'
    );

    $id = SecurityHelper::decryptId(
        $token,
        NationalLicense::class
    );

    $readOnly = [];
    $errors = [];

    /*
     * Load the original National License.
     */
    $originalModel = NationalLicense::findOne($id);

    if ($originalModel === null) {
        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'National license was not found.'
            )
        );
    }

    /*
     * Fisherman users may renew only their own licence.
     */
    if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
        $loggedInProfileId = (int) (
            Yii::$app->user->identity->profile_id ?? 0
        );

        if (
            $loggedInProfileId <= 0
            || (int) $originalModel->fisherman_id
                !== $loggedInProfileId
        ) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'National license was not found.'
                )
            );
        }

        $readOnly['fisherman_id'] = true;
    }

    /*
     * Load the related Departure Boats record.
     */
    $boatReg = DepartureBoats::findOne(
        $originalModel->boat_registration_id
    );

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

    /*
     * Load the latest registered boat licence row.
     *
     * FishermanRegisterdBoatLicense uses nid as its primary key,
     * while NationalLicense.boat_registration_id stores its id field.
     */
    $boatRegistration = FishermanRegisterdBoatLicense::find()
        ->where([
            'id' => $originalModel->boat_registration_id,
        ])
        ->orderBy([
            'nid' => SORT_DESC,
        ])
        ->one();

    if ($boatRegistration === null) {
        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'Boat registration was not found.'
            )
        );
    }

    /*
     * Check for a later renewal request.
     *
     * Use the record ID because pending renewals may not have an
     * expiry date yet.
     */
    $hasOngoingRenewal = NationalLicense::find()
        ->where([
            'fisherman_id' =>
                $originalModel->fisherman_id,
            'boat_registration_id' =>
                $originalModel->boat_registration_id,
            'renew' => 1,
        ])
        ->andWhere([
            '>',
            'id',
            $originalModel->id,
        ])
        ->exists();

    if ($hasOngoingRenewal) {
        throw new BadRequestHttpException(
            Yii::t(
                'app',
                'A renewal request already exists for this license.'
            )
        );
    }

    /*
     * Create a new ActiveRecord and copy the old licence values.
     */
    $model = new NationalLicense();

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
    $model->created = date('Y-m-d H:i:s');
    $model->status = 1;
    $model->approval_stage =
        (string) Constant::FI;

    $gearTypes = ArrayHelper::map(
        MGearTypes::find()
            ->asArray()
            ->all(),
        'id',
        'description'
    );

    $districtList = ArrayHelper::map(
        MFiDistrict::find()
            ->where([
                'status' => 1,
            ])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    $landingSite = ArrayHelper::map(
        MLandingSite::find()
            ->where([
                'status' => 1,
            ])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    /*
     * Generate the division gear list before POST handling.
     *
     * Checkbox IDs are generated by getDivisionGears(), so they
     * must be validated against this list rather than $gearTypes.
     */
    $divisionGearData = $this->getDivisionGears(
        $model
    );

    $allowedDivisionGearIds = [];

    foreach ((array) $divisionGearData as $divisionGearItem) {
        $divisionGearId = (int) (
            $divisionGearItem['id'] ?? 0
        );

        if ($divisionGearId > 0) {
            $allowedDivisionGearIds[] =
                $divisionGearId;
        }
    }

    $allowedDivisionGearIds = array_values(
        array_unique($allowedDivisionGearIds)
    );

    if ($this->request->isPost) {
        /*
         * Fisherman users can submit their own renewal.
         * Other users must have edit permission.
         */
        if (
            !UserTypeUtil::hasType(Constant::FISHERMAN)
            && !Util::editPermission()
        ) {
            throw new \yii\web\ForbiddenHttpException(
                Yii::t(
                    'app',
                    'You are not allowed to perform this action.'
                )
            );
        }

        $model->load(
            $this->request->post()
        );

        /*
         * Preserve the posted landing site for FI users.
         */
        $postedLandingSite = $model->landing_site;

        /*
         * Reapply trusted values to prevent ownership and workflow
         * values from being changed in the submitted request.
         */
        $model->id = null;
        $model->setIsNewRecord(true);

        $model->fisherman_id =
            $originalModel->fisherman_id;

        $model->boat_registration_id =
            $originalModel->boat_registration_id;

        $model->fisheries_district =
            $originalModel->fisheries_district;

        $model->division =
            $originalModel->division;

        $model->landing_site =
            $originalModel->landing_site;

        /*
         * Allow an FI user to select a landing site from the form.
         */
        if (
            UserTypeUtil::hasType(Constant::FI)
            && $postedLandingSite !== null
            && $postedLandingSite !== ''
        ) {
            $model->landing_site =
                $postedLandingSite;
        }

        $model->renew = 1;
        $model->license_number = null;
        $model->approved_time = null;
        $model->expire_date = null;
        $model->created = date('Y-m-d H:i:s');
        $model->approval_stage =
            (string) Constant::FI;
        $model->status = 1;

        /*
         * Process divisionGears[] checkbox values.
         */
        $selectedGearTypes = $this->request->post(
            'divisionGears',
            []
        );

        if (!is_array($selectedGearTypes)) {
            $selectedGearTypes = [];
        }

        $selectedGearTypes = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'intval',
                        $selectedGearTypes
                    ),
                    static function (
                        int $gearId
                    ): bool {
                        return $gearId > 0;
                    }
                )
            )
        );

        /*
         * Accept only IDs displayed by getDivisionGears().
         */
        $validGearTypes = array_values(
            array_intersect(
                $selectedGearTypes,
                $allowedDivisionGearIds
            )
        );

        $model->division_gear_types = implode(
            ',',
            $validGearTypes
        );

        if (empty($validGearTypes)) {
            $model->addError(
                'division_gear_types',
                Yii::t(
                    'app',
                    'Please select at least one gear type.'
                )
            );
        }

        /*
         * Update engine information when applicable.
         */
        $boatRegistrationSaved = true;

        $boatType = $boatRegistration
            ->boatNumber
            ?->boat_type;

        if (
            $boatType !== null
            && (int) $boatType !== 1
        ) {
            $boatRegistrationPost = $this->request->post(
                $boatRegistration->formName(),
                []
            );

            if (is_array($boatRegistrationPost)) {
                if (
                    array_key_exists(
                        'engine_type',
                        $boatRegistrationPost
                    )
                ) {
                    $boatRegistration->engine_type =
                        $boatRegistrationPost[
                            'engine_type'
                        ];
                }

                if (
                    array_key_exists(
                        'engine_serial_number',
                        $boatRegistrationPost
                    )
                ) {
                    $boatRegistration->engine_serial_number =
                        $boatRegistrationPost[
                            'engine_serial_number'
                        ];
                }
            }

            if (
                $boatRegistration->validate([
                    'engine_type',
                    'engine_serial_number',
                ])
            ) {
                $boatRegistrationSaved =
                    $boatRegistration->save(
                        false,
                        [
                            'engine_type',
                            'engine_serial_number',
                        ]
                    );
            } else {
                $boatRegistrationSaved = false;

                $errors = array_merge(
                    $errors,
                    $boatRegistration->getErrors()
                );
            }
        }

        /*
         * Do not call save() when custom validation has already
         * added an error.
         */
        $modelSaved = false;

        if (
            $boatRegistrationSaved
            && !$model->hasErrors()
        ) {
            $modelSaved = $model->save();
        }

        if ($modelSaved) {
            CommonService::addApprovalLog(
                $this->processType,
                'Submitted',
                '',
                $model->id
            );

            Yii::$app->session->setFlash(
                'success',
                Yii::t(
                    'app',
                    'National License renewal submitted successfully.'
                )
            );

            if (
                UserTypeUtil::hasType(
                    Constant::FISHERMAN
                )
            ) {
                return $this->redirect([
                    '/fisherman/profile',
                ]);
            }

            return $this->redirect([
                '/national-license/view',
                'token' => SecurityHelper::encryptId(
                    NationalLicense::class,
                    $model->id
                ),
            ]);
        }

        $errors = array_merge(
            $errors,
            $model->getErrors()
        );

        Yii::warning([
            'message' =>
                'National licence renewal failed.',
            'originalNationalLicenseId' =>
                $originalModel->id,
            'boatRegistrationId' =>
                $originalModel->boat_registration_id,
            'boatLicenseNid' =>
                $boatRegistration->nid,
            'allowedDivisionGearIds' =>
                $allowedDivisionGearIds,
            'selectedGearTypes' =>
                $selectedGearTypes,
            'validGearTypes' =>
                $validGearTypes,
            'divisionGearTypesValue' =>
                $model->division_gear_types,
            'errors' => $errors,
        ], 'national-license-renew');

        /*
         * Regenerate gear data so selected checkboxes remain
         * checked after validation fails.
         */
        $divisionGearData = $this->getDivisionGears(
            $model
        );
    }

    if ($model->division_gear_types === null) {
        $model->division_gear_types = '';
    }

    return $this->render('create', [
        'model' => $model,
        'gearTypes' => $gearTypes,
        'divisionGearData' => $divisionGearData,
        'districtList' => $districtList,
        'landingSite' => $landingSite,
        'readOnly' => $readOnly,
        'errors' => $errors,
        'boatRegistration' => $boatRegistration,
    ]);
}

    /**
     * Updates an existing NationalLicense model.
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
        'NationalLicenseController-update'
    );

    $id = SecurityHelper::decryptId(
        $token,
        NationalLicense::class
    );

    $model = $this->findModel($id);
    $readOnly = [];
    $errors = [];

    $gearTypes = ArrayHelper::map(
        MGearTypes::find()
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

    $landingSite = ArrayHelper::map(
        MLandingSite::find()
            ->where(['status' => 1])
            ->asArray()
            ->all(),
        'id',
        'name'
    );

    /*
     * NationalLicense.boat_registration_id contains
     * fisherman_registerd_boat.id.
     *
     * FishermanRegisterdBoatLicense uses nid as its primary key,
     * so search explicitly using the id column.
     */
    $boatRegistration = FishermanRegisterdBoatLicense::find()
        ->where([
            'id' => $model->boat_registration_id,
        ])
        ->orderBy([
            'nid' => SORT_DESC,
        ])
        ->one();

    if ($boatRegistration === null) {
        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'Boat registration was not found.'
            )
        );
    }

    /*
     * Generate the available division gears before POST handling.
     * These IDs may be different from MGearTypes IDs.
     */
    $divisionGearData = $this->getDivisionGears(
        $model
    );

    $allowedDivisionGearIds = [];

    foreach ((array) $divisionGearData as $divisionGearItem) {
        $divisionGearId = (int) (
            $divisionGearItem['id'] ?? 0
        );

        if ($divisionGearId > 0) {
            $allowedDivisionGearIds[] =
                $divisionGearId;
        }
    }

    $allowedDivisionGearIds = array_values(
        array_unique($allowedDivisionGearIds)
    );

    if ($this->request->isPost) {
        if (!Util::editPermission()) {
            throw new \yii\web\ForbiddenHttpException(
                Yii::t(
                    'app',
                    'You are not allowed to perform this action.'
                )
            );
        }

        $nationalLicensePost = $this->request->post(
            $model->formName(),
            []
        );

        if (
            is_array($nationalLicensePost)
            && array_key_exists(
                'landing_site',
                $nationalLicensePost
            )
        ) {
            $model->landing_site =
                $nationalLicensePost['landing_site'];
        }

        /*
         * Read and validate selected division gear IDs.
         */
        $selectedGearTypes = $this->request->post(
            'divisionGears',
            []
        );

        if (!is_array($selectedGearTypes)) {
            $selectedGearTypes = [];
        }

        $selectedGearTypes = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'intval',
                        $selectedGearTypes
                    ),
                    static function (
                        int $gearId
                    ): bool {
                        return $gearId > 0;
                    }
                )
            )
        );

        $validGearTypes = array_values(
            array_intersect(
                $selectedGearTypes,
                $allowedDivisionGearIds
            )
        );

        $model->division_gear_types = implode(
            ',',
            $validGearTypes
        );

        if (empty($validGearTypes)) {
            $model->addError(
                'division_gear_types',
                Yii::t(
                    'app',
                    'Please select at least one gear type.'
                )
            );
        }

        /*
         * Update engine details for applicable boat types.
         */
        $boatRegistrationSaved = true;

        if (
            $boatRegistration->boatNumber !== null
            && (int) $boatRegistration
                ->boatNumber
                ->boat_type !== 1
        ) {
            $boatRegistrationPost = $this->request->post(
                $boatRegistration->formName(),
                []
            );

            if (is_array($boatRegistrationPost)) {
                if (
                    array_key_exists(
                        'engine_type',
                        $boatRegistrationPost
                    )
                ) {
                    $boatRegistration->engine_type =
                        $boatRegistrationPost[
                            'engine_type'
                        ];
                }

                if (
                    array_key_exists(
                        'engine_serial_number',
                        $boatRegistrationPost
                    )
                ) {
                    $boatRegistration->engine_serial_number =
                        $boatRegistrationPost[
                            'engine_serial_number'
                        ];
                }
            }

            if (
                $boatRegistration->validate([
                    'engine_type',
                    'engine_serial_number',
                ])
            ) {
                $boatRegistrationSaved =
                    $boatRegistration->save(
                        false,
                        [
                            'engine_type',
                            'engine_serial_number',
                        ]
                    );
            } else {
                $boatRegistrationSaved = false;

                $errors = array_merge(
                    $errors,
                    $boatRegistration->getErrors()
                );
            }
        }

        $modelSaved = false;

        if (
            $boatRegistrationSaved
            && !$model->hasErrors()
        ) {
            $modelSaved = $model->save();
        }

        if ($modelSaved) {
            Yii::$app->session->setFlash(
                'success',
                Yii::t(
                    'app',
                    'National License updated successfully.'
                )
            );

            return $this->redirect([
                '/national-license/view',
                'token' => SecurityHelper::encryptId(
                    NationalLicense::class,
                    $model->id
                ),
            ]);
        }

        $errors = array_merge(
            $errors,
            $model->getErrors()
        );

        Yii::warning([
            'message' =>
                'National licence update failed.',
            'nationalLicenseId' => $model->id,
            'boatRegistrationId' =>
                $model->boat_registration_id,
            'boatLicenseNid' =>
                $boatRegistration->nid,
            'allowedDivisionGearIds' =>
                $allowedDivisionGearIds,
            'selectedGearTypes' =>
                $selectedGearTypes,
            'validGearTypes' =>
                $validGearTypes,
            'errors' => $errors,
        ], 'national-license-update');

        /*
         * Regenerate checkbox data so selected items remain checked
         * after validation fails.
         */
        $divisionGearData = $this->getDivisionGears(
            $model
        );
    }

    return $this->render('update', [
        'model' => $model,
        'divisionGearData' => $divisionGearData,
        'gearTypes' => $gearTypes,
        'districtList' => $districtList,
        'landingSite' => $landingSite,
        'readOnly' => $readOnly,
        'boatRegistration' => $boatRegistration,
        'errors' => $errors,
    ]);
}
    /**
     * Deletes an existing NationalLicense model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));

//        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the NationalLicense model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return NationalLicense the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NationalLicense::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    /**
     * @throws UnauthorizedHttpException
     */
    public function actionPayment($id)
    {
        CommonService::validatePermission($this, "NationalLicenseController-payment");

        $boat = NationalLicense::findOne($id);
        $paymentLog = new PaymentLog();
        $paymentLog->type = $this->processType;
        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();

        $paymentType = SubPaymentTypes::find()->where(['payment_type_id' => $workflow->payment_type])->asArray()->all();
        foreach ($paymentType as $item) {
            if (trim($item["Description"]) == trim($boat->boatNumber->boatCategory->code)) {
                $paymentLog->amount = $item["Amount"];
                break;
            } else {

            }
        }
        $paymentLog->amount = 0;
        $paymentLog->process_id = $id;
        $paymentLog->status = 1;
        if ($this->request->isPost && Util::editPermission()) {
            if ($paymentLog->load($this->request->post())) {
                $file = UploadedFile::getInstance($paymentLog, 'file');
                if (isset($file)) {
                    $fileName = $boat->id . $this->processType . '-payment.' . $file->extension;
                    $file->saveAs(Constant::$FILE_UPLOAD_PATH.'payment/' . $fileName);

                    $paymentLog->file = $fileName;

                }
                if ($paymentLog->save()) {
                    if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
                        CommonService::addApprovalLog($this->processType, "Paid", "Marked as Paid, Payment Approved", $boat->id);
                        CommonService::markAsPaid($boat, $this->processType);

                    } else {
                        CommonService::addApprovalLog($this->processType, "Paid", "Marked as Paid", $boat->id);
                        $boat->status = 100;
                    }
                    $boat->save();
                }

                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('../payment/create', [
            'model' => $paymentLog,

        ]);

    }


    public function actionPaymentApprove($id)
    {
        throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));

        $model = NationalLicense::findOne($id);
        $modelLast = NationalLicense::find()->where(["IS NOT", "expire_date", null])->andWhere(["fisherman_id" => $model->fisherman_id])->orderBy(["id" => SORT_DESC])->one();
        $model->expire_date = $modelLast->expire_date;
        CommonService::markAsPaid($model, $this->processType);
        $model->save();
        CommonService::addApprovalLog($this->processType, $model->approval_stage, "Payment Approved", $model->id);
        echo json_encode(true);
        exit();


    }

    public function actionLicenseView(string $token): string
{
    CommonService::validatePermission(
        $this,
        'NationalLicenseController-license-view'
    );

    $id = SecurityHelper::decryptId(
        $token,
        NationalLicense::class
    );

    $model = NationalLicense::findOne($id);

    if ($model === null) {
        throw new NotFoundHttpException(
            Yii::t('app', 'National license was not found.')
        );
    }

    return $this->render('licenseView', [
        'model' => $model,
    ]);
}

    /**
     * @throws MpdfException
     * @throws CrossReferenceException
     * @throws InvalidConfigException
     * @throws PdfParserException
     * @throws UnauthorizedHttpException
     * @throws PdfTypeException
     */
   public function actionLicenseDownload(string $token): string
{
    CommonService::validatePermission(
        $this,
        'NationalLicenseController-license-download'
    );

    $id = SecurityHelper::decryptId(
        $token,
        NationalLicense::class
    );

    $model = NationalLicense::findOne($id);

    if ($model === null) {
        throw new NotFoundHttpException(
            Yii::t('app', 'National licence record was not found.')
        );
    }

    /*
     * Fisherman users may download only their own licence.
     */
    if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
        $loggedInProfileId = (int) (
            Yii::$app->user->identity->profile_id ?? 0
        );

        if (
            $loggedInProfileId <= 0
            || (int) $model->fisherman_id !== $loggedInProfileId
        ) {
            throw new NotFoundHttpException(
                Yii::t('app', 'National licence record was not found.')
            );
        }
    }

    $officer = CommonService::getApprovedOfficer(
        $model->id,
        'NATIONAL_LICENSE'
    );

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
                'Failed to read ' . $imageName . ': ' . $filePath,
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
            $signatureData = file_get_contents(
                $signaturePath
            );

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
                'Officer signature was not found or is unreadable: '
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
            'hasOfficerSignature' => $hasOfficerSignature,
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
        'filename' => 'EEZ-' . $safeLicenseNumber . '.pdf',
    ]);

    $mpdf = $pdf->getApi();
    $mpdf->showImageErrors = false;

    $mpdf->imageVars['nationalLogo'] =
        $nationalLogoData;

    $mpdf->imageVars['boatLicense'] =
        $boatLicenseData;

    if (
        $hasOfficerSignature
        && is_string($signatureData)
    ) {
        $mpdf->imageVars['officerSignature'] =
            $signatureData;
    }

    return $pdf->render();
}
    /**
     * @return void
     */

    public static function markeAsExpired()
    {
        $expiredLicense = NationalLicense::find()->where(["<=", "expire_date", date("Y-m-d")])->andWhere(["status" => Constant::Active])->all();
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

        $userType = Yii::$app->user->identity->type;
        if (UserTypeUtil::hasType(Constant::FI)) {
            $where = ["division" => Yii::$app->session->get("officer_division")];
            $wherePending = ["division" => Yii::$app->session->get("officer_division"), 'approval_stage' => Constant::FI];

        }
        if (UserTypeUtil::hasType(Constant::AD)) {
            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
            $wherePending = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::AD];

        }
        if (UserTypeUtil::hasType(Constant::DFI)) {
            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
            $wherePending = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DFI];

        }
        if ($userType == Constant::DO) {
            $where = ["fisheries_district" => Yii::$app->session->get("officer_district")];
            $wherePending = ["fisheries_district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DO];

        }
        $countPending = NationalLicense::find()->where(['status' => Constant::Pending])->andWhere($wherePending)->count();
        $countActive = NationalLicense::find()->where(['status' => Constant::Active])->andWhere($where)->count();
        $countFinalApproval = NationalLicense::find()->where(['status' => Constant::FinalApprovalPending])->andWhere($where)->count();
        $countExpired = NationalLicense::find()->where(['status' => Constant::Expired])->andWhere($where)->count();

        return [
            "countPending" => $countPending,
            "countActive" => $countActive,
            "countFinalApproval" => $countFinalApproval,
            "countExpired" => $countExpired,
        ];

    }

    /**
     * @param NationalLicense $model
     * @return array
     */
    public static function getDivisionGears(NationalLicense $model): array
{
    $toArray = static function ($value): array {
        if ($value === null || trim((string) $value) === '') {
            return [];
        }

        return array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(',', (string) $value)
                ),
                static fn($item) => $item !== ''
            )
        );
    };

    $selected = array_map(
        'strval',
        $toArray($model->division_gear_types)
    );

    $divisionGears = CommonService::getDivisionGearTypesArray(
        $model->main_gear_type,
        $model->division
    );

    $divisionGearData = [];

    foreach ($divisionGears as $count => $divisionGear) {
        $divisionGearData[$count] = [
            'id' => $divisionGear->id,
            'selected' => in_array(
                (string) $divisionGear->id,
                $selected,
                true
            ) ? 'checked' : '',
            'name' => $divisionGear->name,
            'subGear' => $divisionGear->subGear->description ?? '',
            'extra' => str_replace(
                ['"', '{', '}'],
                ' ',
                (string) ($divisionGear->extra ?? '')
            ),
        ];

        /*
         * Fishing-time durations
         */
        $times = [];

        foreach ($toArray($divisionGear->fishing_time_durations) as $time) {
            if (isset(Constant::$FishingDuration[$time])) {
                $times[] = Constant::$FishingDuration[$time];
            }
        }

        $divisionGearData[$count]['times'] = implode(', ', $times);

        /*
         * Fish species
         */
        $fishSpeciesIds = $toArray($divisionGear->fish_species);
        $fish = [];

        if (!empty($fishSpeciesIds)) {
            $fishTypes = MFishTypes::find()
                ->select(['name'])
                ->where(['id' => $fishSpeciesIds])
                ->asArray()
                ->all();

            foreach ($fishTypes as $fishType) {
                if (!empty($fishType['name'])) {
                    $fish[] = $fishType['name'];
                }
            }
        }

        $divisionGearData[$count]['fishTypes'] = implode(', ', $fish);
    }

    return $divisionGearData;
}
}
