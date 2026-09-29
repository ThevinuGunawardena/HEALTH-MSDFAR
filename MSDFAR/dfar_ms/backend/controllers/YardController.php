<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\models\PaymentLog;
use backend\models\ProfileFisherman;
use backend\models\ProfileYard;
use backend\models\ProfileYardSearch;
use backend\services\CommonService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * YardController implements the CRUD actions for ProfileYard model.
 */
class YardController extends Controller
{
    public $processType = "YARD_REGISTER";

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
     * Lists all ProfileYard models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this,"test-app");

        $searchModel = new ProfileYardSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProfileYard model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    { $model = $this->findModel($id);

        $approvalFlow = CommonService::getApprovalProcess($model, $this->processType, $id, false);
        if ($this->request->isPost) {
            $model = CommonService::markApprovalStage($approvalFlow, $model, $id, $this->processType);
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }
        $paymentHistory = [];
        if ($model->status == 100 || $model->status == 101) {
            $paymentHistory = PaymentLog::find()->where(["type" => $this->processType, "process_id" => $id])->all();
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'approvalHistory' => $approvalFlow['approvalHistory'],
            'showRejectBtn' => $approvalFlow['showRejectBtn'],
            'showApproveBtn' => $approvalFlow['showApproveBtn'],
            'paymentHistory' => $paymentHistory,
        ]);
    }

    /**
     * Creates a new ProfileYard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new ProfileYard();
        $fishermanList =ArrayHelper::map( ProfileFisherman::find()->where(['status'=>100])->asArray()->all(),"id","first_name");

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) ) {
                $model->status = 1;
                $model->approval_stage = "" . Constant::FI;
                if ($model->save()) {
                    CommonService::addApprovalLog($this->processType, "Submitted", "Submitted", $model->id);
                    return $this->redirect(['view', 'id' => $model->id]);

                }
            }
        } else {
            $model->loadDefaultValues();
        }
        $paymentHistory = [];
        if ($model->status == 100 || $model->status == 101) {
            $paymentHistory = PaymentLog::find()->where(["type" => $this->processType, "process_id" => $id])->all();
        }
        return $this->render('create', [
            'model' => $model,
            'fishermanList' => $fishermanList,
            'districtList' => CommonService::getFIDistrictArray(),
        ]);
    }

    /**
     * Updates an existing ProfileYard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProfileYard model.
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
     * Finds the ProfileYard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ProfileYard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProfileYard::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionList($type = 0)
    {
        $data = [];
        if ($type != 0) {
            $data = ProfileYard::find()->where(['status' => Constant::Active])->asArray()->all();
        }

        echo json_encode($data);
        exit();
    }


    public function actionPayment($id, $ref)
    {
        $boat = ProfileYard::findOne($id);
        $paymentLog = new PaymentLog();
        $paymentLog->type = $this->processType;
        $paymentLog->amount = 0;
        $paymentLog->process_id = $id;
        $paymentLog->ref = $ref;
        $paymentLog->file = $ref;
        $paymentLog->status = 1;
        if ($paymentLog->save()) {
            $boat->status = 100;
            $boat->save();
            CommonService::addApprovalLog($this->processType, "Paid", $ref, $boat->id);

            echo json_encode(true);
            exit();
        }
        echo json_encode(false);

    }


    public function actionPaymentApprove($id)
    {
        $model = ProfileYard::findOne($id);
        CommonService::markAsPaid($model,$this->processType);
        $model->save();
        CommonService::addApprovalLog($this->processType, $model->approval_stage, "Payment Approved", $model->id);
        echo json_encode(true);
        exit();



    }
}
