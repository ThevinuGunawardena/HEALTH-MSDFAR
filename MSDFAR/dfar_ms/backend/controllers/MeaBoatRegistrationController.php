<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\models\BoatNumbers;
use backend\models\BoatNumbersSearch;
use backend\models\Files;
use backend\models\MApprovalWorkflow;
use backend\models\MeaBoatRegistration;
use backend\models\MeaBoatRegistrationSearch;
use backend\models\MRequeredDocuments;
use backend\models\PaymentLog;
use backend\services\CommonService;
use backend\services\Util;
use Yii;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * MeaBoatRegistrationController implements the CRUD actions for MeaBoatRegistration model.
 */
class MeaBoatRegistrationController extends Controller
{
    public $processType = "MEA_BOAT_REG";

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
     * Lists all MeaBoatRegistration models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BoatNumbersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexMea($boat_number)
    {
        $searchModel = new MeaBoatRegistrationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams,$boat_number);

        return $this->render('history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MeaBoatRegistration model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $validated = false;
        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
        $files = Files::find()->where(["type" => $workflow->id, "process_id" => $id])->andWhere(["!=", "file_type", -999])->count();
        $requred_documents = MRequeredDocuments::find()->where(['type' => $workflow->id, "status" => 1])->count();
//        if ($model->validate() && $files >= $requred_documents) {
        if ($model->validate()) {
            $validated = true;
        }
        $approvalFlow = CommonService::getApprovalProcess($model, $this->processType, $id, false);

        if ($this->request->isPost && Util::editPermission()) {
            $model = CommonService::markApprovalStage($approvalFlow, $model, $id, $this->processType);

//            if ($model->license_number == null || $model->license_number == "") {
//                $boatType = $model->boatRegistration->boatNumber->boatType->code;
//                $uid = Constant::$NATIONAL_LICENSE_FORMAT;
//                $uid = str_replace("{year}", date("y"), $uid);
//                $uid = str_replace("{boatType}", $boatType, $uid);
//                $uid = str_replace("{number}", sprintf("%05d", $model->id), $uid);
//                $uid = str_replace("{district_code}", $model->fisheriesDistrict->code, $uid);
//                $model->license_number = $uid;
//            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $paymentHistory = [];
        if ($model->status == 100 || $model->status == 101) {
            $paymentHistory = PaymentLog::find()->where(["type" => $this->processType, "process_id" => $id])->all();
        }
        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
        $files = Files::find()->where(["type" => $workflow->id, "process_id" => $id])->all();
        return $this->render('view', [
            'model' => $model,
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
     * Creates a new MeaBoatRegistration model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate($boatNumber)
    {
        $renew = false;

        $model = new MeaBoatRegistration();
        $model->renew = false;

        $latestReport = MeaBoatRegistration::find()->where(["boat_reg_number" => $boatNumber])->orderBy(['id' => SORT_DESC])->one();
        $boat = BoatNumbers::findOne($boatNumber);
        $model->boat_reg_number = $boat->id;
        if (!empty($latestReport)) {
            $model = $latestReport;
            $renew = true;
            $model->renew = true;

            unset(
                $model->id
//                $model->engine_type,
//                $model->fuel_type,
//                $model->number_of_cylinders,
//                $model->horse_power_of_engine,
//                $model->engine_model,
//                $model->engine_number,
//                $model->propeller_diameter_pitch_no_of_blades,
//                $model->gear_ratio,
//                $model->steering_gear_type,
//                $model->engine_model_number,
//                $model->place_of_inspectio,
//                $model->inspection_date,
//                $model->date_of_repairs_approved_modifications_hull,
//                $model->date_of_repairs_approved_modifications_machinery,
//                $model->running_trail_carried_out_on,
//                $model->maneuverability,
//                $model->machinery_operation,
//                $model->declaration,
//                $model->next_inspected_date,
//                $model->number_of_professional_competent_crew,
//                $model->name_of_coxswain_certificate_no,
//                $model->name_of_engine_driver_certificate_no,
//                $model->other_information,
//                $model->value_of_boat_hull,
//                $model->value_of_boat_engine,
//                $model->name_of_engine_driver_certificate_no,
//                $model->compass,
//                $model->radio,
//                $model->gps,
//                $model->radar,
//                $model->ais,
//                $model->winch,
//                $model->vms,
//                $model->gillnets,
//                $model->longlines,
//                $model->other,
//                $model->condition_hull_internal,
//                $model->condition_hull_external,
//                $model->condition_hull_sheathing,
//                $model->condition_decks,
//                $model->condition_steering_gear,
//                $model->condition_cargo_compartment,
//                $model->condition_anchor_cables,
//                $model->condition_framework_timbers_internals,
//                $model->condition_navigation_lights,
//                $model->condition_machinery,
//                $model->condition_rudder,
//                $model->equipped_compass,
//                $model->equipped_bailers,
//                $model->equipped_life_saving_appliances,
//                $model->equipped_first_aid_equipment,
//                $model->equipped_fire_extingulshers_type,
//                $model->equipped_navigation_equipment,
//                $model->equipped_bilge_pump,
//                $model->equipped_gps_available,
//                $model->equipped_vms_installation,
//                $model->equipped_emergency_repairs,
//                $model->other,
//                $model->total
            );
        }
        if ($this->request->isPost) {
            if ($renew) {
                $model->isNewRecord = true;
                $model->renew = 1;
            } else {
                $model->renew = 0;
            }
            $model->mea_certificate_number = "Dump";
            $model->created = date("Y-m-d H:i:s");
            $model->created_by = Yii::$app->user->identity->id;
            $model->approval_stage = "" . Constant::MEA;
            $model->load($this->request->post());
            $model->validate();
//print_r($model->getErrors());exit();
            if ($model->load($this->request->post()) && $model->save()) {
                $model->status = 1;
                $uid = Constant::$MEA_FORMAT;
                $uid = str_replace("{year}", date("Y"), $uid);
                $uid = str_replace("{boat}", $boat->boat_number, $uid);
                $uid = str_replace("{id}", sprintf("%05d", $model->id), $uid);
                $uid = str_replace("{district_code}", $boat->fisheriesDistrict->code, $uid);
                $model->mea_certificate_number = $uid . "";
                if ($model->save()) {

                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }


        return $this->render('create', [
            'model' => $model,
            'boat' => $boat,
            'renew' => $renew,
        ]);
    }

    /**
     * Updates an existing MeaBoatRegistration model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $boat = BoatNumbers::findOne($model->boat_reg_number);
        $model->boat_reg_number = $boat->id;
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'boat' => $boat,
            'renew' => $model->renew == 1,
        ]);
    }

    /**
     * Deletes an existing MeaBoatRegistration model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MeaBoatRegistration model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return MeaBoatRegistration the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MeaBoatRegistration::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
