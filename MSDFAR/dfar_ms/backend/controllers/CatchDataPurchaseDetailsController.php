<?php

namespace backend\controllers;

use backend\models\CatchDataPurchaseDetails;
use backend\models\CatchDataPurchaseDetailsSearch;
use backend\components\Controller;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CatchDataPurchaseDetailsController implements the CRUD actions for CatchDataPurchaseDetails model.
 */
class CatchDataPurchaseDetailsController extends Controller
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
     * Lists all CatchDataPurchaseDetails models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CatchDataPurchaseDetailsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CatchDataPurchaseDetails model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CatchDataPurchaseDetails model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
   public function actionCreate()
{
    $model = new CatchDataPurchaseDetails();

    if ($this->request->isPost && $model->load($this->request->post())) {

        $transaction = Yii::$app->db->beginTransaction();

        try {

            $model->exporter_uid = Yii::$app->user->id;

            $logBNo = Yii::$app->request->post('logBNo');
            $logSNo = Yii::$app->request->post('logSNo');

            $request = \backend\models\CatchDataRequest::find()
                ->where(['log_book_no' => trim($logBNo)])
                ->andFilterWhere(['log_book_page_no' => trim($logSNo)])
                ->one();

            if (!$request) {
                throw new \Exception('Log sheet not found');
            }

            $model->catch_data_request_id = $request->id;

            // 🔥 IMPORTANT: check fish FIRST
            $fishcatch = \backend\models\CatchDataFishcatch::find()
                ->where([
                    'catchdata_req_id' => $model->catch_data_request_id,
                    'fish_type' => $model->fish_type
                ])
                ->one();

            if (!$fishcatch) {
                throw new \Exception('Fish type not available in log sheet');
            }

            // 🔥 VALIDATE STOCK BEFORE SAVE
            if ($fishcatch->remain_fish_count < $model->No_of_Fish ||
                $fishcatch->remain_weight < $model->Weight_of_Fish) {
                throw new \Exception('Not enough remaining fish');
            }

            // ✅ deduct
            $fishcatch->remain_fish_count -= (float)$model->No_of_Fish;
            $fishcatch->remain_weight -= (float)$model->Weight_of_Fish;

            if (!$fishcatch->save()) {
                throw new \Exception('Fishcatch update failed');
            }

            // ✅ NOW SAVE purchase (after validation)
            if (!$model->save()) {
                throw new \Exception('Purchase save failed');
            }

            $transaction->commit();

            return $this->redirect([
                'catch-data-request/get-logsheet-data',
                'logBNo' => $logBNo,
                'logSNo' => $logSNo
            ]);

        } catch (\Exception $e) {

            $transaction->rollBack();

            Yii::$app->session->setFlash('error', $e->getMessage());

            return $this->redirect(Yii::$app->request->referrer);
        }
    }
}
    /**
     * Updates an existing CatchDataPurchaseDetails model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
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
     * Deletes an existing CatchDataPurchaseDetails model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
   public function actionDelete($id)
{
    $transaction = Yii::$app->db->beginTransaction();

    try {
        $model = $this->findModel($id);

        $requestId = $model->catch_data_request_id;

        $fishcatch = \backend\models\CatchDataFishcatch::find()
            ->where([
                'catchdata_req_id' => $requestId,
                'fish_type' => $model->fish_type
            ])
            ->one();

        if ($fishcatch) {
            $fishcatch->remain_fish_count += (float)$model->No_of_Fish;
            $fishcatch->remain_weight += (float)$model->Weight_of_Fish;

            if (!$fishcatch->save()) {
                throw new \Exception('Restore failed');
            }
        }

        $model->delete();

        $transaction->commit();

        $request = \backend\models\CatchDataRequest::findOne($requestId);

        return $this->redirect([
            'catch-data-request/get-logsheet-data',
            'logBNo' => $request->log_book_no,
            'logSNo' => $request->log_book_page_no
        ]);

    } catch (\Exception $e) {

        $transaction->rollBack();

        Yii::$app->session->setFlash('error', $e->getMessage());

        return $this->redirect(Yii::$app->request->referrer);
    }
}
    /**
     * Finds the CatchDataPurchaseDetails model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CatchDataPurchaseDetails the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CatchDataPurchaseDetails::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    
}
