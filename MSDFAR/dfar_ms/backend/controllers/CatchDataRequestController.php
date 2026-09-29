<?php

namespace backend\controllers;

use backend\models\CatchDataRequest;
use backend\models\CatchDataRequestSearch;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use backend\models\BoatNumbers;
use backend\models\MHarbours;
use backend\models\MMainGearTypes;
use backend\models\ProfileOfficer;
use backend\models\CatchDataFishcatch;
use backend\models\CatchDataFishcatchSearch;
use backend\models\CatchDataPurchaseDetails;

use yii\data\ActiveDataProvider;

/**
 * CatchDataRequestController implements the CRUD actions for CatchDataRequest model.
 */
class CatchDataRequestController extends Controller
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
     * Lists all CatchDataRequest models.
     *
     * @return string
     */
  public function actionIndex()
{
    $searchModel = new CatchDataRequestSearch();

    // ✅ THIS IS THE KEY FIX
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $profileId = Yii::$app->user->identity->profile_id;
    $userType = Yii::$app->user->identity->type;

    // Get harbour
    $officerProfile = ProfileOfficer::findOne($profileId);
    $harbourName = $officerProfile ? $officerProfile->harbour : null;

    $harbourId = MHarbours::find()
        ->select('id')
        ->where(['Name' => $harbourName])
        ->scalar();

    // ✅ Apply additional filter AFTER search
    if ($userType != 226 && !empty($harbourId)) {
        $dataProvider->query->andWhere(['unloading_harbour' => $harbourId]);
    }

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}

    /**
     * Displays a single CatchDataRequest model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
{
    $model = $this->findModel($id);

    $fishcatchsearchModel = new CatchDataFishcatchSearch();
    $fishcatchsearchModeldataProvider = $fishcatchsearchModel->search(Yii::$app->request->queryParams);

    $fishcatchsearchModeldataProvider->query->andWhere([
        'catchdata_req_id' => $id
    ]);

    $totalLog = $fishcatchsearchModeldataProvider->query->sum('weight_of_fish_log');
    $totalActual = $fishcatchsearchModeldataProvider->query->sum('weight_of_fish_act');

    $boat_data = BoatNumbers::find()
        ->where(['id' => $model->boat_registration_id])
        ->one();

    // ✅ ADD THESE
    $catchdata = \backend\models\CatchDataFishcatch::find()
        ->where(['catchdata_req_id' => $id])
        ->all();

    $purcheseDeatils = \backend\models\CatchDataPurchaseDetails::find()
        ->with(['user.officerProfile'])
        ->where(['catch_data_request_id' => $id])
        ->all();

    return $this->render('view', [
        'model' => $model,
        'boat_number' => $boat_data ? $boat_data->boat_number : null,
        'fishcatchsearchModeldataProvider' => $fishcatchsearchModeldataProvider,
        'totalLog' => $totalLog,
        'totalActual' => $totalActual,
        'fishcatchsearchModel' => $fishcatchsearchModel,

        // ✅ PASS THESE
        'catchdata' => $catchdata,
        'purcheseDeatils' => $purcheseDeatils,
    ]);
}

    /**
     * Creates a new CatchDataRequest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
        public function actionCreate()
    {
        $model = new CatchDataRequest();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                // ✅ Set current date & time
                $model->created_at = date('Y-m-d H:i:s');

                // ✅ Set logged-in user ID
                $model->created_by = Yii::$app->user->id;

                if ($model->save()) {
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
     * Updates an existing CatchDataRequest model.
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
     * Deletes an existing CatchDataRequest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CatchDataRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CatchDataRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CatchDataRequest::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionExporterView()
    {
     return $this->render('exporter-view', [
        'data' => []
        ]);
    }

 public function actionGetLogsheetData($logBNo, $logSNo)
{
    $data = \backend\models\CatchDataFishcatch::find()
        ->select([
            'fish_type',
            'SUM(remain_fish_count) AS total_fish',
            'SUM(remain_weight) AS total_weight'
        ])
        ->innerJoin(
            'catch_data_request',
            'catch_data_request.id = catch_data_fishcatch.catchdata_req_id'
        )
        ->where([
            'catch_data_request.log_book_no' => $logBNo,
            'catch_data_request.log_book_page_no' => $logSNo
        ])
        ->groupBy('fish_type')
        ->asArray()
        ->all();

        $request = \backend\models\CatchDataRequest::find()
        ->where([
            'log_book_no' => $logBNo,
            'log_book_page_no' => $logSNo
        ])
        ->one();

        $boatNumber = null;

        if ($request) {
            $boat = \backend\models\BoatNumbers::findOne($request->boat_registration_id);
            $boatNumber = $boat->boat_number ?? 'N/A';
        }

        $purchaseData = [];

         if ($request) {
        // 🔥 Load purchase details
        $purchaseData = \backend\models\CatchDataPurchaseDetails::find()
           ->where([
            'catch_data_request_id' => $request->id,
            'exporter_uid' => Yii::$app->user->id 
        ])
            ->all();


    }

    return $this->render('exporter-view', [
        'data' => $data,
        'purchaseData' => $purchaseData,
        'landing_date' => $request->landing_date ?? null,
        'boatNumber' => $boatNumber,
    ]);
}
    public function actionQualityOfficerView()
        {
        return $this->render('quality-officer-view', [
            'data' => []
            ]);
        }
  

public function actionGetLogsheetOfficerData($logBNo, $logSNo)
{
    $logbookdata = \backend\models\CatchDataRequest::find()
    ->where([
        'log_book_no' => $logBNo,
        'log_book_page_no' => $logSNo
    ])
    ->one();

$catchdata = [];

if ($logbookdata) {
    $catchdata = \backend\models\CatchDataFishcatch::find()
        ->where(['catchdata_req_id' => $logbookdata->id])
        ->all();
}

$purcheseDeatils = [];
if ($logbookdata) {
$purcheseDeatils = \backend\models\CatchDataPurchaseDetails::find()
    ->with(['user.officerProfile'])
    ->where(['catch_data_request_id' => $logbookdata->id])
    ->all();
}


    return $this->render('quality-officer-view', [
        'logbookdata' => $logbookdata,
        'catchdata' => $catchdata,
        'purcheseDeatils' => $purcheseDeatils,
    ]);
}

}
