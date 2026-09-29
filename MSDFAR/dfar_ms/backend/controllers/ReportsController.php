<?php

namespace backend\controllers;

use app\models\IotcReport;
use backend\config\Constant;
use backend\models\DepartureBoats;
use backend\models\DepartureSkipper;
use backend\models\HighseasLicense;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\MDivision;
use backend\models\MFiDistrict;
use backend\models\MFishTypes;
use backend\models\MGearTypes;
use backend\models\MLandingSite;
use backend\models\NationalLicense;
use backend\models\NationalLicenseSearch;
use backend\models\ProfileFishermanSearch;
use backend\models\ProfileOfficer;
use backend\models\ReportImulNumberIssue;
use backend\models\ReportScientificCatch2;
use backend\models\ReportScientificCraft;
use backend\models\ReportScientificCraftGearWise;
use backend\models\ReportScientificFleet;
use backend\models\ReportScientificLw;
use backend\models\ReportWebsite;
use backend\models\ScientificDataSearch;
use backend\models\ScientificSamplingDataSearch;
use backend\models\SkipperSearch;
use backend\models\User;
use backend\services\CommonService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * OfficerController implements the CRUD actions for ProfileOfficer model.
 */
class ReportsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all ProfileOfficer models.
     *
     * @return string
     */
    public function actionFishermenRegistration($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $searchModel = new ProfileFishermanSearch();
        $dataProvider = $searchModel->fishermanReport($from, $to, $district);

        return $this->render('fisherman', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district
        ]);
    }

    /**
     * Lists all ProfileOfficer models.
     *
     * @return string
     */
    public function actionFishermenRegistrationPrinted($from = null, $to = null, $district = "All", $status = null)
    {
        CommonService::validatePermission($this, "Reports-view");

        $searchModel = new ProfileFishermanSearch();
        $dataProvider = $searchModel->idPrintedReport($from, $to, $district, $status);

        return $this->render('fishermanPrinted', [
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
            'status' => $status,
        ]);
    }
     public function actionFishermenRegistrationNotPrinted()
    {

        CommonService::validatePermission($this, "Reports-view");
        $officerProfile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);
        $division = $officerProfile->division ;
        $searchModel = new ProfileFishermanSearch();
        $dataProvider = $searchModel->idNotPrintedReport();

        return $this->render('fishermanNotPrinted', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            
        ]);
    }

    public function actionSkippers($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $searchModel = new SkipperSearch();
        $dataProvider = $searchModel->skipperReport($from, $to, $district);


        return $this->render('skipper', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionNatinalLicense($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $searchModel = new NationalLicenseSearch();
        $dataProvider = $searchModel->licenseReport($from, $to, $district);


        return $this->render('nationalLicense', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionScientific1($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $searchModel = new ScientificDataSearch();
        $dataProvider = $searchModel->report1($from, $to, $district);


        return $this->render('scientific1', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionScientificSamplingData($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $searchModel = new ScientificSamplingDataSearch();
        $dataProvider = $searchModel->report1($from, $to, $district);


        return $this->render('scientificSamplingData', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionScientific2($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = ReportScientificCatch2::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if ($district != "All") {
            $query->andWhere(["district" => $district]);
        }
        if ($from != null && $to != null) {
            $query->andWhere(['between', 'start_time', $from, $to]);
        }
        return $this->render('scientific2', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionScientific3($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = ReportScientificLw::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if ($district != "All") {
            $query->andWhere(["district" => $district]);
        }
        if ($from != null && $to != null) {
            $query->andWhere(['between', 'start_time', $from, $to]);
        }
        return $this->render('scientific3', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionScientific4($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = ReportScientificFleet::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if ($district != "All") {
            $query->andWhere(["district" => $district]);
        }
        if ($from != null && $to != null) {
            $query->andWhere(['between', 'start_time', $from, $to]);
        }
        return $this->render('scientific4', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionScientificSamplingCraft($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = ReportScientificCraft::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if ($district != "All") {
            $query->andWhere(["district" => $district]);
        }
        if ($from != null && $to != null) {
            $query->andWhere(['between', 'start_time', $from, $to]);
        }
        return $this->render('scientifisamplingCreaft', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionScientificSamplingCraftGear($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = ReportScientificCraftGearWise::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if ($district != "All") {
            $query->andWhere(["district" => $district]);
        }
        if ($from != null && $to != null) {
            $query->andWhere(['between', 'start_time', $from, $to]);
        }
        return $this->render('scientifisamplingCreaftGear', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionFishList($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = MFishTypes::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('fishList', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionDistrictList($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = MFiDistrict::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('districtList', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionDivisionList($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = MDivision::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('divisionList', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionLandingSiteList($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = MLandingSite::find()->where(['status' => 1]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('landingSiteList', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionGearTypesList($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = MGearTypes::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('gearTypesList', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

public function actionBoatRegistration($from = null, $to = null, $district = "All", $boat_type = null, $reg_type = null, $status = null)
{
    CommonService::validatePermission($this, "Reports-view");

    $query = FishermanRegisterdBoatLicense::find()
        ->alias('fbrl')
        ->leftJoin('boat_numbers bn', 'bn.id = fbrl.boat_number_id');

    if ($district != "All") {
        $query->andWhere(['fbrl.district' => $district]);
    }

    if ($from != null && $to != null) {
        $query->andWhere(['between', 'fbrl.created', $from, $to]);
    }

    if (!empty($boat_type)) {
        $query->andWhere(['bn.boat_type' => $boat_type]);
    }

    if ($reg_type !== null && $reg_type !== '') {
        $query->andWhere(['fbrl.renew' => $reg_type]);
    }

    if ($status !== null && $status !== '') {
        $query->andWhere(['fbrl.status' => $status]);
    }

    $dataProvider = new ActiveDataProvider([
        'query' => $query,
    ]);

    return $this->render('boatRegistration', [
        'dataProvider' => $dataProvider,
        'from' => $from,
        'to' => $to,
        'district' => $district,
        'boat_type' => $boat_type,
        'reg_type' => $reg_type,
        'status' => $status,
    ]);
}

    public function actionHighseasLicense($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = HighseasLicense::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if ($district != "All") {
            $query->andWhere(["district" => $district]);
        }
        if ($from != null && $to != null) {
            $query->andWhere(['between', 'created', $from, $to]);
        }
        return $this->render('highseasLicense', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }
    

    public function actionNationalLicense($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = NationalLicense::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        if ($district != "All") {
            $query->andWhere(["district" => $district]);
        }
        if ($from != null && $to != null) {
            $query->andWhere(['between', 'created', $from, $to]);
        }
        return $this->render('nationalLicense', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionImulNumberIssue($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = ReportImulNumberIssue::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if ($from != null && $to != null) {
            $query->andWhere(['between', 'registration_number_issue_date', $from, $to]);
        }
        return $this->render('imulBoatNumber', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionIotc($from = null, $to = null, $district = "All")
    {
        CommonService::validatePermission($this, "Reports-view");

        $query = IotcReport::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if ($from != null && $to != null) {
            $query->andWhere(['between', 'approved_time', $from, $to]);
        }
        return $this->render('iotc', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'from' => $from,
            'to' => $to,
            'district' => $district,
        ]);
    }

    public function actionWebsite($boat = null, $nic = null)
    {

        $query = ReportWebsite::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => [
                'boat_number' => SORT_ASC,
            ]
            ]
        ]);

        if ($boat != null) {
            $query->andWhere(['like', 'boat_number', $boat]);
        }
        if ($nic != null) {
            $query->andWhere(['like', 'nic', $nic]);
        }
        return $this->renderPartial('reportwebsite', [
//            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'boat' => $boat,
            'nic' => $nic,
        ]);
    }

    public function actionDepartureStatus($boat = null, $status = null)
    {
        $query = DepartureBoats::find()
            ->alias('dboat')
            ->joinWith(['boat boat'])
            ->where(['boat.status' => 101])
            ->andWhere(['is not', 'dboat.status', null])
            ->andWhere(['!=', 'dboat.status', ''])
            ->andWhere([
                    'or',
                    ['!=', 'dboat.status', 'Departure Allowed'],
                    ['dboat.compulsory_service' => 0]
                ]);

        if ($boat) {
            $query->andWhere(['like', 'boat.boat_number', $boat]);
        }

        if ($status) {
            $query->andWhere(['dboat.status' => $status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->renderPartial('reportwebsiteDeparture', [
            'dataProvider' => $dataProvider,
            'boat' => $boat,
            'status' => $status,
        ]);
    }

    public function actionDepartureSkipperStatus($nic = null)
    {
        $query = DepartureSkipper::find()
            ->andWhere(['is not', 'status', null])
            ->andWhere(['!=', 'status', ''])
            ->andWhere(['!=', 'status', 'Departure Allowed']);

        if ($nic) {
            $query->andWhere(['like', 'nic', $nic]);
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->renderPartial('reportwebsiteDepartureSkipper', [
            'dataProvider' => $dataProvider,
            'nic' => $nic,
        ]);
    }

    /**
     * Displays a single ProfileOfficer model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        CommonService::validatePermission($this, "Reports-view");

        CommonService::validatePermission($this, "admin");
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the ProfileOfficer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ProfileOfficer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        CommonService::validatePermission($this, "Reports-view");

        if (($model = ProfileOfficer::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Creates a new ProfileOfficer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        CommonService::validatePermission($this, "Reports-view");

//        CommonService::validatePermission($this, "test-app");

        $model = new ProfileOfficer();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $user = User::findOne(["id" => Yii::$app->user->identity->id]);
                $profileId = $user->profile_id;
                $model->status = 1;
                if ($model->save()) {
                    $fileSignature = UploadedFile::getInstance($model, 'signature');

                    if (isset($fileSignature)) {
                        $fileName = $model->id . '_signature.' . $fileSignature->extension;
                        $fileSignature->saveAs(Constant::$FILE_UPLOAD_PATH.'fisherman/' . $fileName);

                        $model->signature = $fileName;
                    }
                }
                if ($model->save()) {
                    if ($profileId == 0) {
                        $user->profile_id = $model->id;
                        $user->save();
                    }
                    return $this->goHome();

                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProfileOfficer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        CommonService::validatePermission($this, "Reports-view");

//        CommonService::validatePermission($this, "test-app");

        $model = $this->findModel($id);
        $signature = $model->signature;
        if ($this->request->isPost && $model->load($this->request->post())) {
            $fileSignature = UploadedFile::getInstance($model, 'signature');
            if (isset($fileSignature) && !empty($fileSignature)) {
//                print_r($fileSignature);exit();

                $fileName = $model->id . '_signature.' . $fileSignature->extension;
                $fileSignature->saveAs(Constant::$FILE_UPLOAD_PATH.'officer/signature/' . $fileName);

                $model->signature = $fileName;

            } else {
                $model->signature = $signature;
            }
            if ($model->save()) {
                return $this->goHome();

            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProfileOfficer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        CommonService::validatePermission($this, "Reports-view");

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
}
