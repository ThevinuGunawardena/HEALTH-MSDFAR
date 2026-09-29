<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\components\RecordLookupRateLimit;
use backend\components\SecurityHelper;
use backend\models\FishermanRegisterdBoat;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\FishermanRegisterdBoatLicenseSearch;
use backend\models\BoatNumbers;
use backend\models\ProfileFisherman;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\components\Controller;
use backend\services\Util;

use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * BoatRegistrationLicensesController implements the CRUD actions for FishermanRegisterdBoatLicense model.
 */
class BoatRegistrationLicensesController extends Controller
{
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
        'index',
        'license',
        'update',
        'delete',
        'data-view',
    ];

    /*
     * Burst protection:
     * Maximum 30 requests per 60 seconds.
     */
    $behaviors['recordLookupBurstLimit'] = [
        'class' => RecordLookupRateLimit::class,
        'only' => $protectedActions,
        'bucketName' =>
            'boat-registration-license-list-burst',
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
        'bucketName' =>
            'boat-registration-license-list-sustained',
        'limit' => 200,
        'window' => 900,
    ];

    return $behaviors;
}

    /**
     * Lists all FishermanRegisterdBoatLicense models.
     *
     * @return string
     */
    public function actionIndex(string $token)
    {
        $boatRegistrationId = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoat::class
        );

        $boatRegistration = FishermanRegisterdBoat::findOne(
            $boatRegistrationId
        );

        if ($boatRegistration === null) {
            throw new NotFoundHttpException(
                Yii::t('app', 'Boat registration was not found.')
            );
        }

        if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
            $loggedInProfileId = (int) (
                Yii::$app->user->identity->profile_id ?? 0
            );

            if (
                $loggedInProfileId <= 0
                || (int) $boatRegistration->fisherman_id
                    !== $loggedInProfileId
            ) {
                throw new NotFoundHttpException(
                    Yii::t('app', 'Boat registration was not found.')
                );
            }
        }

        $searchModel = new FishermanRegisterdBoatLicenseSearch();
        $dataProvider = $searchModel->search(
            $this->request->queryParams,
            $boatRegistrationId
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'licenseList' => false,
            'boatToken' => $token,
        ]);
    }

    /**
     * Lists all FishermanRegisterdBoatLicense models.
     *
     * @return string
     */
    public function actionLicense()
    {
        $searchModel = new FishermanRegisterdBoatLicenseSearch();
        $dataProvider = $searchModel->searchLicense($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'licenseList' => true
        ]);
    }

    /**
     * Displays a single FishermanRegisterdBoatLicense model.
     * @param int $nid Nid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
//    public function actionView($nid)
//    {
//        return $this->render('view', [
//            'model' => $this->findModel($nid),
//        ]);
//    }

    /**
     * Finds the FishermanRegisterdBoatLicense model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $nid Nid
     * @return FishermanRegisterdBoatLicense the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($nid)
    {
        if (($model = FishermanRegisterdBoatLicense::findOne(['nid' => $nid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Creates a new FishermanRegisterdBoatLicense model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
//    public function actionCreate()
//    {
//        $model = new FishermanRegisterdBoatLicense();
//
//        if ($this->request->isPost) {
//            if ($model->load($this->request->post()) && $model->save()) {
//                return $this->redirect(['view', 'nid' => $model->nid]);
//            }
//        } else {
//            $model->loadDefaultValues();
//        }
//
//        return $this->render('create', [
//            'model' => $model,
//        ]);
//    }

    /**
     * Updates an existing FishermanRegisterdBoatLicense model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $nid Nid
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(string $token)
    {
        $nid = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        $model = $this->findModel($nid);

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t('app', 'You are not allowed to perform this action.')
                );
            }

            if (
                $model->load($this->request->post())
                && $model->save()
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
     * Deletes an existing FishermanRegisterdBoatLicense model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $nid Nid
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(string $token)
    {
        $nid = SecurityHelper::decryptId(
            $token,
            FishermanRegisterdBoatLicense::class
        );

        $model = $this->findModel($nid);

        return $this->redirect([
            '/boat-registration/view',
            'token' => SecurityHelper::encryptId(
                FishermanRegisterdBoatLicense::class,
                $model->nid
            ),
        ]);
    }

    public static function getStacs()
    {
        $where = [];
        if (UserTypeUtil::hasType(Constant::FI)) {
            $where = ["division" => Yii::$app->session->get("officer_division")];
        }
        if (UserTypeUtil::hasType(Constant::DO)) {
            $where = ["district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::AD)) {
            $where = ["district" => Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DFI)) {
            $where = ["district" => Yii::$app->session->get("officer_district")];
        }
        $countPending = FishermanRegisterdBoatLicense::find()->where(['status' => Constant::Pending])->andWhere($where)->count();
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

public function actionDataView()
{
    $searchModel = new FishermanRegisterdBoatLicenseSearch();

    $boatNumberInput = Yii::$app->request->get('boat_number');

    $models = [];
    $boatsMap = [];
    $fishermenMap = [];
    $nationalLicenseMap = [];
    $highseasLicenseMap = [];

    if (!empty($boatNumberInput)) {

        // ================= STEP 1: GET BOATS =================
        $boatNumbers = \backend\models\BoatNumbers::find()
            ->where(['boat_number' => trim($boatNumberInput)])
            ->all();

        if (!empty($boatNumbers)) {

            $boatsMap = \yii\helpers\ArrayHelper::index($boatNumbers, 'id');
            $boatIds = array_keys($boatsMap);

            // ================= STEP 2: OWNER (ONLY FROM boat_numbers) =================
            $models = [];

            foreach ($boatNumbers as $boat) {
                if (!empty($boat->owner)) {
                    $models[] = (object)[
                        'boat_number_id' => $boat->id,
                        'fisherman_id' => $boat->owner
                    ];
                }
            }

            // ================= STEP 3: LOAD LICENSES (UNCHANGED) =================
            foreach ($boatIds as $boatId) {

                $nationalLicenseMap[$boatId] = [];
                $highseasLicenseMap[$boatId] = [];

                $regBoats = \backend\models\FishermanRegisterdBoat::find()
                    ->where(['boat_number_id' => $boatId])
                    ->all();

                foreach ($regBoats as $regBoat) {

                    $national = \backend\models\NationalLicense::find()
                        ->where(['boat_registration_id' => $regBoat->id])
                        ->all();

                    $highseas = \backend\models\HighseasLicense::find()
                        ->where(['boat_registration_id' => $regBoat->id])
                        ->all();

                    $nationalLicenseMap[$boatId] = array_merge($nationalLicenseMap[$boatId], $national);
                    $highseasLicenseMap[$boatId] = array_merge($highseasLicenseMap[$boatId], $highseas);
                }
            }

            // ================= STEP 4: LOAD FISHERMEN =================
            $ownerIds = array_filter(array_column($boatNumbers, 'owner'));

            $fishermenMap = \backend\models\ProfileFisherman::find()
                ->where(['id' => $ownerIds])
                ->indexBy('id')
                ->all();
        }
    }

    return $this->render('dataView', [
        'searchModel' => $searchModel,
        'models' => $models,
        'boatsMap' => $boatsMap,
        'fishermenMap' => $fishermenMap,
        'nationalLicenseMap' => $nationalLicenseMap,
        'highseasLicenseMap' => $highseasLicenseMap,
    ]);
}
}
