<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumberOwnersLog;
use backend\models\BoatNumbers;
use backend\models\BoatNumberTransferRequest;
use backend\models\BoatNumberTransferRequestFromSearch;
use backend\models\Files;
use backend\models\FishermanRegisterdBoat;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\HighseasLicense;
use backend\models\MApprovalWorkflow;
use backend\models\MRequeredDocuments;
use backend\models\NationalLicense;
use backend\models\ProfileFisherman;
use backend\services\CommonService;
use backend\services\Util;
use Exception;
use Throwable;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * BoatTransferController implements the CRUD actions for BoatNumberTransferRequest model.
 */
class BoatTransferController extends Controller
{
    public $processType = "BOAT_TRANSFER";

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
     * Lists all BoatNumberTransferRequest models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this, "BoatTransferController-list");

        $searchModel = new BoatNumberTransferRequestFromSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BoatNumberTransferRequest model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        CommonService::validatePermission($this, "BoatTransferController-office-view");

        $model = $this->findModel($id);
        $boat = BoatNumbers::find()->where(["id" => $model->boat_number])->one();
        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
//        print_r($boat);exit();
        $approvalFlow = CommonService::getApprovalProcess($model, $this->processType, $id, $boat->boat_type != 1);
        $validated = false;
        $files = Files::find()->where(["type" => $workflow->id, "process_id" => $id])->andWhere(["!=", "file_type", -999])->count();
        $requred_documents = MRequeredDocuments::find()->where(['type' => $workflow->id, "status" => 1])->count();

        if ($model->validate() && $files >= $requred_documents) {
            $validated = true;
        }
        if ($this->request->isPost && Util::editPermission()) {

            $transaction = Yii::$app->db->beginTransaction();

            try {

                $model = CommonService::markApprovalStageForBoatCancellationAndTransfer(
                    $approvalFlow,
                    $model,
                    $id,
                    $this->processType
                );

                if ($model->status == Constant::RequestCompleted) {

                    $boat = BoatNumbers::find()
                        ->where([
                            "id" => $model->boat_number,
                            "status" => Constant::Active
                        ])
                        ->one();

                    $transferdBoat = $boat;

                    $boat->status = Constant::Transferred;

                    if (!$boat->save()) {
                        throw new Exception(json_encode($boat->errors));
                    }

                    $boat = BoatNumbers::find()
                        ->where(["id" => $model->boat_number])
                        ->one();

                    $transferdBoat->isNewRecord = true;
                    unset($transferdBoat->id);

                    $transferdBoat->owner = $model->new_owner;
                    $transferdBoat->fisheries_district = $model->newOwner->district;
                    $transferdBoat->fisheries_division = $model->newOwner->division;
                    $transferdBoat->status = Constant::Active;

                    if (!$transferdBoat->save(false)) {
                        throw new Exception('Transferred boat save failed');
                    }

                    $previousOwner = new BoatNumberOwnersLog();
                    $previousOwner->name = $boat->owner0->preferred_name_for_id ?? "-";
                    $previousOwner->nic = $boat->owner0->nic;
                    $previousOwner->boat_number = $boat->boat_number;
                    $previousOwner->from_date = $boat->approved_time;
                    $previousOwner->to_date = date("Y-m-d");
                    $previousOwner->added_by = Yii::$app->user->identity->id;
                    $previousOwner->status = Constant::Transferred;

                    if (!$previousOwner->save(false)) {
                        throw new Exception('Previous owner log save failed');
                    }

                    $regBoat = FishermanRegisterdBoat::find()
                        ->where([
                            'boat_number_id' => $boat->id,
                            "fisherman_id" => $boat->owner
                        ])
                        ->one();

                    $boatRegId = $regBoat->id;

                    $trnBoatReg = $regBoat;
                    $trnBoatReg->isNewRecord = true;
                    unset($trnBoatReg->id);

                    $trnBoatReg->boat_number_id = $transferdBoat->id;
                    $trnBoatReg->fisherman_id = $transferdBoat->owner;

                    if (!$trnBoatReg->save()) {
                        throw new Exception(json_encode($trnBoatReg->errors));
                    }

                    $boatLicenses = FishermanRegisterdBoatLicense::find()
                        ->where(["id" => $boatRegId])
                        ->andWhere([
                            "NOT IN",
                            "status",
                            [Constant::Cancelled, Constant::Transferred]
                        ])
                        ->all();

                    $licenseNo = sizeof($boatLicenses);
                    $count = 1;

                    foreach ($boatLicenses as $boatLicense) {

                        $transferdLicense = $boatLicense;

                        $boatLicense->status = Constant::Transferred;

                        if (!$boatLicense->save(false)) {
                            throw new Exception('Boat license transfer failed');
                        }

                        if ($count == $licenseNo) {

                            $transferdLicense->isNewRecord = true;
                            $transferdLicense->fisherman_id = $transferdBoat->owner;
                            $transferdLicense->boat_number_id = $transferdBoat->id;

                            unset($transferdLicense->nid);

                            $transferdLicense->district = $model->newOwner->district;
                            $transferdLicense->division = $model->newOwner->division;
                            $transferdLicense->id = $trnBoatReg->id;
                            $transferdLicense->status = Constant::Active;
                            $transferdLicense->renew = 0;
                            $transferdLicense->transered_license = 1;

                            if (!$transferdLicense->save(false)) {
                                throw new Exception('Transferred license save failed');
                            }
                        }

                        $count++;
                    }

                    foreach (NationalLicense::find()->where(['boat_registration_id' => $boatRegId])->all() as $nationalLicense) {
                        $nationalLicense->status = Constant::Cancelled;

                        if (!$nationalLicense->save(false)) {
                            throw new Exception('National license cancellation failed');
                        }
                    }

                    foreach (HighseasLicense::find()->where(['boat_registration_id' => $boatRegId])->all() as $highseasLicense) {
                        $highseasLicense->status = Constant::Cancelled;

                        if (!$highseasLicense->save(false)) {
                            throw new Exception('High seas license cancellation failed');
                        }
                    }
                }

                if (!$model->save()) {

                    Yii::error([
                        'message' => 'Boat transfer request save failed',
                        'errors' => $model->errors,
                        'attributes' => $model->attributes,
                        'dirtyAttributes' => $model->getDirtyAttributes(),
                    ]);

                    throw new Exception(json_encode($model->errors));
                }

                $transaction->commit();

                return $this->redirect(['view', 'id' => $id]);

            } catch (Throwable $e) {

                $transaction->rollBack();

                Yii::error([
                    'message' => 'Boat transfer transaction failed',
                    'error' => $e->getMessage(),
                ]);

                Yii::$app->session->setFlash('error', $e->getMessage());

                return $this->redirect(['view', 'id' => $id]);
            }
        }
        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
        $files = Files::find()->where(["type" => $workflow->id, "process_id" => $id])->all();
        return $this->render('view', [
            'model' => $model,
            'approvalHistory' => $approvalFlow['approvalHistory'],
            'showRejectBtn' => $approvalFlow['showRejectBtn'],
            'showApproveBtn' => $approvalFlow['showApproveBtn'],
            'process' => $this->processType,
            'validated' => $validated,
            'files' => $files,
        ]);
    }

    /**
     * Creates a new BoatNumberTransferRequest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        CommonService::validatePermission($this, "BoatTransferController-create");

        $model = new BoatNumberTransferRequest();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && Util::editPermission()) {
                $model->approval_stage = "" . Constant::DO;
                $model->status = 1;
//                print_r($model->validate());exit();
                if ($model->save()) {
                    CommonService::addApprovalLog($this->processType, "Submitted", "", $model->id);
                    return $this->redirect(['view', 'id' => $model->id]);
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
     * Updates an existing BoatNumberTransferRequest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        CommonService::validatePermission($this, "BoatTransferController-update");

        $model = $this->findModel($id);
        $boatData = ArrayHelper::map(BoatNumbers::find()->andWhere(['id' => $model->boat_number])->all(), 'id', 'boat_number');
        $ownerData = ArrayHelper::map(ProfileFisherman::find()->andWhere(['id' => $model->new_owner])->all(), 'id', 'fisherman_uid');
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'boatData' => $boatData,
            'ownerData' => $ownerData,
        ]);
    }

    /**
     * Deletes an existing BoatNumberTransferRequest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {

        return $this->redirect(['index']);
    }

    /**
     * Finds the BoatNumberTransferRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return BoatNumberTransferRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BoatNumberTransferRequest::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public static function getStacs()
    {
        $where = [];
        if (UserTypeUtil::hasType(Constant::FI)) {
//            $where =["fisheries_division"=>Yii::$app->session->get("officer_division")];
        }
        if (UserTypeUtil::hasType(Constant::DO)) {
//            $where =["fisheries_district"=>Yii::$app->session->get("officer_district")];
        }
        if (UserTypeUtil::hasType(Constant::DG)) {
            $where = ["approval_stage" => Constant::DG];
        }
        $countPending = BoatNumberTransferRequest::find()->where(['status' => Constant::Pending])->andWhere($where)->count();
        $countActive = BoatNumberTransferRequest::find()->where(['status' => Constant::Active])->andWhere($where)->count();
        $countFinalApproval = BoatNumberTransferRequest::find()->where(['status' => Constant::FinalApprovalPending])->andWhere($where)->count();
        $countExpired = BoatNumberTransferRequest::find()->where(['status' => Constant::Expired])->andWhere($where)->count();

        return [
            "countPending" => $countPending,
            "countActive" => $countActive,
            "countFinalApproval" => $countFinalApproval,
            "countExpired" => $countExpired,
        ];

    }


public function actionAddManTransfer()
{
    CommonService::validatePermission($this, "admin");

    $request = Yii::$app->request;

    if ($request->isPost) {

        $boatId = $request->post('boat_number');
        $newOwner = $request->post('new_owner');
        $transferDate = $request->post('transferDate');

        // print_r($transferDate);die();

        // ✅ FIX: handle null transfer date
        if (empty($transferDate)) {
            $transferDate = date('Y-m-d H:i:s');
        } else {
            $transferDate = date('Y-m-d H:i:s', strtotime($transferDate));
        }

        if (!$boatId || !$newOwner) {
            Yii::$app->session->setFlash('error', 'Please select both boat and owner');
            return $this->refresh();
        }

        $oldBoat = BoatNumbers::findOne($boatId);
        $fisherman = ProfileFisherman::findOne($newOwner);

        if (!$oldBoat || !$fisherman) {
            Yii::$app->session->setFlash('error', 'Invalid boat or fisherman');
            return $this->refresh();
        }

        if ($oldBoat->status == 500) {
            Yii::$app->session->setFlash('error', 'Boat already transferred');
            return $this->refresh();
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {

            // =====================================================
            // 🔁 DUPLICATE BOAT
            // =====================================================
            $newBoat = new BoatNumbers();
            $newBoat->attributes = $oldBoat->attributes;
            $newBoat->id = null;

            $newBoat->owner = $newOwner;
            $newBoat->status = 101;
            $newBoat->fisheries_district = $fisherman->district;
            $newBoat->fisheries_division = $fisherman->division;

            $newBoat->created = $transferDate;
            $newBoat->approved_time = $transferDate;
            $newBoat->expire_date = date('Y-m-d', strtotime($transferDate . ' +1 year'));

            if (!$newBoat->save(false)) {
                throw new Exception(json_encode($newBoat->errors));
            }

            // =====================================================
            // 🔴 UPDATE OLD BOAT
            // =====================================================
            $oldBoat->status = 500;
            $oldBoat->save(false);

            // =====================================================
            // 🔴 UPDATE OLD LICENSE STATUS → 500
            // =====================================================
            $oldLicensesAll = FishermanRegisterdBoatLicense::find()
                ->where(['boat_number_id' => $boatId])
                ->all();

            foreach ($oldLicensesAll as $oldLicense) {
                $oldLicense->status = 500;
                $oldLicense->save(false);
            }

            // =====================================================
            // 🔁 DUPLICATE fisherman_registerd_boat
            // =====================================================
            $oldRegBoats = FishermanRegisterdBoat::find()
                ->where(['boat_number_id' => $boatId])
                ->all();

            foreach ($oldRegBoats as $oldRegBoat) {

                $newRegBoat = new FishermanRegisterdBoat();
                $newRegBoat->attributes = $oldRegBoat->attributes;

                $newRegBoat->id = null;
                $newRegBoat->boat_number_id = $newBoat->id;
                $newRegBoat->fisherman_id = $newOwner;
                $newRegBoat->district = $fisherman->district;

                if (!$newRegBoat->save(false)) {
                    throw new Exception(json_encode($newRegBoat->errors));
                }

                // =====================================================
                // 🔁 DUPLICATE LICENSES
                // =====================================================
                $oldLicenses = FishermanRegisterdBoatLicense::find()
                    ->where([
                        'boat_number_id' => $boatId,
                        'id' => $oldRegBoat->id
                    ])
                    ->all();

                foreach ($oldLicenses as $oldLicense) {

                    $newLicense = new FishermanRegisterdBoatLicense();
                    $newLicense->attributes = $oldLicense->attributes;

                    $newLicense->id = $newRegBoat->id;
                    $newLicense->boat_number_id = $newBoat->id;

                    $newLicense->fisherman_id = $newOwner;
                    $newLicense->district = $fisherman->district;
                    $newLicense->division = $fisherman->division;

                    $newLicense->status = 101;
                    $newLicense->approval_stage = 'Completed';
                    $newLicense->created = $transferDate;
                    $newLicense->approved_time = $transferDate;
                    $newLicense->expire_date = date('Y-m-d', strtotime($transferDate . ' +1 year'));
                    $newLicense->renew = 0;
                    $newLicense->transered_license = 1;

                    if (!$newLicense->save(false)) {
                        throw new Exception(json_encode($newLicense->errors));
                    }
                }

                // =====================================================
                // 🔴 UPDATE national_license & highseas_license → 400
                // =====================================================
                $nationalLicenses = NationalLicense::find()
                    ->where(['boat_registration_id' => $oldRegBoat->id])
                    ->all();

                foreach ($nationalLicenses as $nl) {
                    $nl->status = 400;
                    $nl->save(false);
                }

                $highSeasLicenses = HighseasLicense::find()
                    ->where(['boat_registration_id' => $oldRegBoat->id])
                    ->all();

                foreach ($highSeasLicenses as $hl) {
                    $hl->status = 400;
                    $hl->save(false);
                }
            }

            $transaction->commit();

            Yii::$app->session->setFlash('success', 'Boat transferred successfully');

        } catch (Exception $e) {

            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->refresh();
    }

    return $this->render('addManTransfer');
}
}
