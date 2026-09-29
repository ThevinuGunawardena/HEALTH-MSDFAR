<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\models\FishermanRegisterdBoatLicenseSearch;
use backend\models\FishermanRegisterdBoatRenew;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\components\Controller;


/**
 * BoatRegistrationRenewController implements the CRUD actions for FishermanRegisterdBoatRenew model.
 */
class BoatRegistrationRenewController extends Controller
{
    public $processType = "BOAT_REGISTER_ReNEW";

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
     * Lists all FishermanRegisterdBoatRenew models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FishermanRegisterdBoatLicenseSearch();
        $dataProvider = $searchModel->searchLicense($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
//
//    /**
//     * Displays a single FishermanRegisterdBoatRenew model.
//     * @param int $id ID
//     * @return string
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionView($id)
//    {
//        CommonService::validatePermission($this, "BoatRegistrationController-office-view");
//
//        $validated = false;
//        $model = $this->findModel($id);
//        $validated = false;
//        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
//        $files = Files::find()->where(["type" => $workflow->id, "process_id" => $id])->andWhere(["!=", "file_type", -999])->count();
//        $requred_documents = MRequeredDocuments::find()->where(['type' => $workflow->id, "status" => 1])->count();
//
////        if ($model->validate() && $files >= $requred_documents) {
//        if ($model->validate()) {
//            $validated = true;
//        }
////        print_r( $model->getErrors()        );exit();
//        $approvalFlow = CommonService::getApprovalProcess($model, $this->processType, $id, false);
//        if ($this->request->isPost && Util::editPermission()) {
//            $model = CommonService::markApprovalStage($approvalFlow, $model, $id, $this->processType);
//            if ($model->approval_stage == "Completed") {
//
//            }
//            if ($model->save()) {
//                if ($model->approval_stage == "Completed") {
//                    $modelBoat = FishermanRegisterdBoat::find()->where(["boat_number_id" => $model->boat_number_id])->one();
//                    $data = $model->attributes;
//                    $modelBoat->setAttributes($data);
//                    $modelBoat->save();
//                }
//                return $this->redirect(['view', 'id' => $model->id]);
//            }
//        }
//        $paymentHistory = [];
//        if ($model->status == 100 || $model->status == 101) {
//            $paymentHistory = PaymentLog::find()->where(["type" => $this->processType, "process_id" => $id])->all();
//        }
//
//        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
//        $files = Files::find()->where(["type" => $workflow->id, "process_id" => $id])->all();
//        return $this->render('view', [
//            'model' => $model,
//            'approvalHistory' => $approvalFlow['approvalHistory'],
//            'showRejectBtn' => $approvalFlow['showRejectBtn'],
//            'showApproveBtn' => $approvalFlow['showApproveBtn'],
//            'paymentHistory' => $paymentHistory,
//            'process' => $this->processType,
//            'validated' => $validated,
//            'files' => $files,
//
//        ]);
//    }
//
//    /**
//     * Creates a new FishermanRegisterdBoatRenew model.
//     * If creation is successful, the browser will be redirected to the 'view' page.
//     * @return string|Response
//     */
////    public function actionCreate()
////    {
////        $model = new FishermanRegisterdBoatRenew();
////
////        if ($this->request->isPost) {
////            if ($model->load($this->request->post()) && $model->save()) {
////                return $this->redirect(['view', 'id' => $model->id]);
////            }
////        } else {
////            $model->loadDefaultValues();
////        }
////
////        return $this->render('create', [
////            'model' => $model,
////        ]);
////    }
//
//    /**
//     * Updates an existing FishermanRegisterdBoatRenew model.
//     * If update is successful, the browser will be redirected to the 'view' page.
//     * @param int $id ID
//     * @return string|Response
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionUpdate($id)
//    {
//        CommonService::validatePermission($this, "BoatRegistrationController-update");
//
//        $model = $this->findModel($id);
//        $model->communication_equipment = explode(',', $model->communication_equipment);//converting to array...
//        $model->fishing_equipment = explode(',', $model->fishing_equipment);//converting to array...
//        $model->navigation_equipment = explode(',', $model->navigation_equipment);//converting to array...
//        $latestReport = MeaBoatRegistration::find()->where(["boat_reg_number" => $id])->orderBy(['id' => SORT_DESC])->one();
//
//        if (empty($model->mea_report) && !empty($latestReport))
//            $model->mea_report = $latestReport->mea_certificate_number;
//        if ($this->request->isPost && $model->load($this->request->post()) && Util::editPermission()) {
//
//            if ($model->communication_equipment !== '') {
//                $model->communication_equipment = implode(',', $model->communication_equipment);
//            }
//            if ($model->fishing_equipment !== '') {
//                $model->fishing_equipment = implode(',', $model->fishing_equipment);
//            }
//            if ($model->navigation_equipment !== '') {
//                $model->navigation_equipment = implode(',', $model->navigation_equipment);
//            }
////            print_r($model);exit();
//            if ($model->save()) {
//
//                return $this->redirect(['view', 'id' => $model->id]);
//            }
//
//        }
//
//        return $this->render('update', [
//            'model' => $model,
//        ]);
//    }
//
//    /**
//     * Deletes an existing FishermanRegisterdBoatRenew model.
//     * If deletion is successful, the browser will be redirected to the 'index' page.
//     * @param int $id ID
//     * @return Response
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }
//
//    /**
//     * Finds the FishermanRegisterdBoatRenew model based on its primary key value.
//     * If the model is not found, a 404 HTTP exception will be thrown.
//     * @param int $id ID
//     * @return FishermanRegisterdBoatRenew the loaded model
//     * @throws NotFoundHttpException if the model cannot be found
//     */
//    protected function findModel($id)
//    {
//        if (($model = FishermanRegisterdBoatRenew::findOne(['id' => $id])) !== null) {
//            return $model;
//        }
//
//        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
//    }

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
        $countPending = FishermanRegisterdBoatRenew::find()->where(['status' => Constant::Pending])->andWhere($where)->count();
        $countActive = FishermanRegisterdBoatRenew::find()->where(['status' => Constant::Active])->andWhere($where)->count();
        $countFinalApproval = FishermanRegisterdBoatRenew::find()->where(['status' => Constant::FinalApprovalPending])->andWhere($where)->count();
        $countExpired = FishermanRegisterdBoatRenew::find()->where(['status' => Constant::Expired])->andWhere($where)->count();

        return [
            "countPending" => $countPending,
            "countActive" => $countActive,
            "countFinalApproval" => $countFinalApproval,
            "countExpired" => $countExpired,
        ];

    }
}
