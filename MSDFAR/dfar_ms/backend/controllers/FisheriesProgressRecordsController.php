<?php

namespace backend\controllers;

use backend\models\FisheriesProgressRecords;
use backend\models\FisheriesProgressRecordsSearch;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FisheriesProgressRecordsController implements the CRUD actions for FisheriesProgressRecords model.
 */
class FisheriesProgressRecordsController extends Controller
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
     * Lists all FisheriesProgressRecords models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FisheriesProgressRecordsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FisheriesProgressRecords model.
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
    ;

    // Log the fetched records for debugging purposes
    Yii::debug($records, 'fisheries-progress-records');

    // Render the view and pass the models
    return $this->render('view', [
        'model' => $model,
        'newRecord' => $newRecord,  // Pass the new record model for form
    ]);
}

    /**
     * Creates a new FisheriesProgressRecords model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new FisheriesProgressRecords();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing FisheriesProgressRecords model.
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
     * Deletes an existing FisheriesProgressRecords model.
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
     * Finds the FisheriesProgressRecords model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return FisheriesProgressRecords the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FisheriesProgressRecords::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
