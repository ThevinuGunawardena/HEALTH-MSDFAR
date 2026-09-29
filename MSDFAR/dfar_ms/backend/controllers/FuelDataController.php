<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\services\CommonService;
use backend\models\MApprovalWorkflow;
use backend\services\Util;
use backend\models\FuelData;
use backend\models\FuelDataSearch;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * FuelDataController implements the CRUD actions for FuelData model.
 */
class FuelDataController extends Controller
{
     public $processType = "FUEL_DATA";
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
     * Lists all FuelData models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FuelDataSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FuelData model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
{
    $model = $this->findModel($id);

    $process = $this->processType;

    $approvalFlow = CommonService::getApprovalProcess($model, $process, $id, false);

    $validated = true;

    $workflow = MApprovalWorkflow::find()
        ->where(["type" => $process])
        ->one();

    // ✅ POST ACTION
    if ($this->request->isPost && Util::editPermission()) {

        $model = CommonService::markApprovalStage(
            $approvalFlow,
            $model,
            $id,
            $process
        );

        // ❌ DO NOT TOUCH expire_date here

        CommonService::logValidateErrors($model);

        if ($model->save()) {
            if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
    }

    

    return $this->render('view', [
        'model' => $model,
        'approvalHistory' => $approvalFlow['approvalHistory'],
        'showRejectBtn' => $approvalFlow['showRejectBtn'],
        'showApproveBtn' => $approvalFlow['showApproveBtn'],
        'process' => $process,
        'validated' => $validated,
    ]);
}
    /**
     * Creates a new FuelData model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
{
    $model = new FuelData();

    // ✅ QUERY
    $rows = (new Query())
        ->select([
            'frbl.id',
            'bn.boat_number',
            'frbl.engine_horsepower',
            'pf.nic',
            'pf.first_name',
            'pf.permanent_address',
            'd.name AS district_name',
            'div.name AS division_name'
        ])
        ->from('boat_numbers bn')
        ->innerJoin('fisherman_registerd_boat_license frbl', 'frbl.boat_number_id = bn.id')
        ->innerJoin('profile_fisherman pf', 'pf.id = frbl.fisherman_id')
        ->leftJoin('m_fi_district d', 'd.id = pf.district')
        ->leftJoin('m_division div', 'div.id = pf.division')
        ->where(['frbl.status' => 101])
        ->all();

    // ✅ DROPDOWN DATA
    $boatList = ArrayHelper::map($rows, 'id', 'boat_number');

    // ✅ JS DATA
    $boatData = [];
    foreach ($rows as $row) {
        $boatData[$row['id']] = [
            'boat_number' => $row['boat_number'],
            'nic' => $row['nic'],
            'name' => $row['first_name'],
            'address' => $row['permanent_address'],
            'district' => $row['district_name'],
            'division' => $row['division_name'],
            'capacity' => $row['engine_horsepower'],
        ];
    }

    $boatJson = json_encode($boatData);

    // ✅ DEFAULT VALUES (set before save)
    $model->approval_stage = "" . Constant::FI;
    $model->created = date('Y-m-d H:i:s'); // ✅ FIXED
    $model->status = 1;

    // ✅ SAVE
    if ($this->request->isPost) {
        if ($model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            // 🔥 DEBUG
            print_r($model->errors);
            exit;
        }
    } else {
        $model->loadDefaultValues();
    }

    return $this->render('create', [
        'model' => $model,
        'boatList' => $boatList,
        'boatJson' => $boatJson,
    ]);
}
    /**
     * Updates an existing FuelData model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $rows = (new Query())
    ->select([
        'bn.id',
        'bn.boat_number',
        'frbl.engine_horsepower',
        'pf.nic',
        'pf.first_name',
        'pf.permanent_address',

        'd.name AS district_name',     // ✅ FIX
        'div.name AS division_name'    // ✅ FIX
    ])
    ->from('boat_numbers bn')

    ->innerJoin('fisherman_registerd_boat_license frbl', 'frbl.boat_number_id = bn.id')
    ->innerJoin('profile_fisherman pf', 'pf.id = frbl.fisherman_id')

    // 🔥 NEW JOINS
    ->leftJoin('m_fi_district d', 'd.id = pf.district')
    ->leftJoin('m_division div', 'div.id = pf.division')

    ->where(['frbl.status' => 101])
    ->groupBy('bn.id')
    ->all();

    // ✅ DROPDOWN DATA
    $boatList = ArrayHelper::map($rows, 'id', 'boat_number');

    // ✅ JS DATA
    $boatData = [];
    foreach ($rows as $row) {
        $boatData[$row['id']] = [
            'boat_number' => $row['boat_number'],
            'nic' => $row['nic'],
            'name' => $row['first_name'],
            'address' => $row['permanent_address'],
            'district' => $row['district_name'], 
            'division' => $row['division_name'],
            'capacity' => $row['engine_horsepower'],
        ];
    }

    $boatJson = json_encode($boatData);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
              'boatList' => $boatList,
        'boatJson' => $boatJson,
        ]);
    }

    /**
     * Deletes an existing FuelData model.
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
     * Finds the FuelData model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return FuelData the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FuelData::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    public function actionGetBranches($bankCode)
{
    $branches = \backend\models\BankBranches::find()
        ->where(['bank_code' => $bankCode])
        ->asArray()
        ->all();

    return json_encode(\yii\helpers\ArrayHelper::map($branches, 'id', 'branch_name'));
}
}
