<?php

namespace backend\controllers;
use Yii;

use backend\models\FisheriesProgress;
use backend\models\FisheriesProgressRecords;
use backend\models\FisheriesProgressSearch;
use backend\models\ProfileOfficer;
use backend\models\User;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FisheriesProgressController implements the CRUD actions for FisheriesProgress model.
 */
class FisheriesProgressController extends Controller
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
     * Lists all FisheriesProgress models.
     *
     * @return string
     */
  public function actionIndex()
{
    $searchModel = new FisheriesProgressSearch();
    $dataProvider = $searchModel->search($this->request->queryParams);

    // Get the first day of the current and previous months
    $currentMonth = date('Y-m-01'); // Current month first day
    $previousMonth = date('Y-m-01', strtotime("first day of last month")); // Previous month first day

    // Fetch the records for the current and previous months
    $currentMonthRecords = FisheriesProgress::find()->where(['month' => $currentMonth])->all();
    $previousMonthRecords = FisheriesProgress::find()->where(['month' => $previousMonth])->all();

    // Count the number of records for the current and previous months
    $currentMonthRecordCount = count($currentMonthRecords);
    $previousMonthRecordCount = count($previousMonthRecords);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'currentMonthRecords' => $currentMonthRecords,
        'previousMonthRecords' => $previousMonthRecords,
        'currentMonthRecordCount' => $currentMonthRecordCount,
        'previousMonthRecordCount' => $previousMonthRecordCount,
    ]);
}







    /**
     * Displays a single FisheriesProgress model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
   public function actionView($id)
{
    // Find the FisheriesProgress model
    $model = $this->findModel($id);

    // Create a new FisheriesProgressRecords model for form submission
    $newRecord = new FisheriesProgressRecords();

    // Handle form submission and save new record
    if ($newRecord->load(Yii::$app->request->post()) && $newRecord->save()) {
        return $this->redirect(['view', 'id' => $id]);  // Redirect back to the view page after saving
    }

    // Fetch the related FisheriesProgressRecords for this FisheriesProgress record
    $records = FisheriesProgressRecords::find()->where(['fisheries_progress_id' => $model->id])->all();

    // Group records by type
    $groupedRecords = [];
    foreach ($records as $record) {
        $groupedRecords[$record->type][] = $record; // Group by 'type'
    }

    // Render the view and pass the models
    return $this->render('view', [
        'model' => $model,
        'newRecord' => $newRecord,  // Pass the new record model for form
        'groupedRecords' => $groupedRecords, // Pass the grouped records for summary
    ]);
}



    /**
     * Creates a new FisheriesProgress model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
{
    // Get the first day of the current, previous, and next months
    $currentMonth = date('Y-m-01'); // Current month first day
    $previousMonth = date('Y-m-01', strtotime("first day of last month")); // Previous month first day
    $nextMonth = date('Y-m-01', strtotime("first day of next month")); // Next month first day

    // Check if records exist for the current and previous months
    $currentMonthRecordExists = FisheriesProgress::find()->where(['month' => $currentMonth])->exists();
    $previousMonthRecordExists = FisheriesProgress::find()->where(['month' => $previousMonth])->exists();

    // Create current month and previous month records if they do not exist
    if (!$currentMonthRecordExists) {
        $modelCurrentMonth = new FisheriesProgress();
        $modelCurrentMonth->loadDefaultValues();
        $modelCurrentMonth->month = $currentMonth;
        $modelCurrentMonth->officer = Yii::$app->user->id;
        $modelCurrentMonth->timestamp = date('Y-m-d H:i:s');

        // Fetch officer's district
        $user = User::findOne($modelCurrentMonth->officer);
        if ($user && $user->profile_id) {
            $officerProfile = ProfileOfficer::findOne($user->profile_id);
            if ($officerProfile) {
                $modelCurrentMonth->district = $officerProfile->district;
            }
        }

        // Save the current month record
        $modelCurrentMonth->save();
    }

    if (!$previousMonthRecordExists) {
        $modelPreviousMonth = new FisheriesProgress();
        $modelPreviousMonth->loadDefaultValues();
        $modelPreviousMonth->month = $previousMonth;
        $modelPreviousMonth->officer = Yii::$app->user->id;
        $modelPreviousMonth->timestamp = date('Y-m-d H:i:s');

        // Fetch officer's district
        $user = User::findOne($modelPreviousMonth->officer);
        if ($user && $user->profile_id) {
            $officerProfile = ProfileOfficer::findOne($user->profile_id);
            if ($officerProfile) {
                $modelPreviousMonth->district = $officerProfile->district;
            }
        }

        // Save the previous month record
        $modelPreviousMonth->save();
    }

    // If either current or previous month record exists, only create the next month record
    if ($currentMonthRecordExists || $previousMonthRecordExists) {
        $modelNextMonth = new FisheriesProgress();
        $modelNextMonth->loadDefaultValues();
        $modelNextMonth->month = $nextMonth;
        $modelNextMonth->officer = Yii::$app->user->id;
        $modelNextMonth->timestamp = date('Y-m-d H:i:s');

        // Fetch officer's district
        $user = User::findOne($modelNextMonth->officer);
        if ($user && $user->profile_id) {
            $officerProfile = ProfileOfficer::findOne($user->profile_id);
            if ($officerProfile) {
                $modelNextMonth->district = $officerProfile->district;
            }
        }

        // Save the next month record
        $modelNextMonth->save();
    }

    // Return a response to indicate successful creation
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return ['status' => 'success'];
}



    /**
     * Updates an existing FisheriesProgress model.
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
     * Deletes an existing FisheriesProgress model.
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
     * Finds the FisheriesProgress model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return FisheriesProgress the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FisheriesProgress::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
