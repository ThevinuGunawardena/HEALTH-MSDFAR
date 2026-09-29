<?php

namespace backend\controllers;

use backend\models\CatchDataFishcatch;
use backend\models\CatchDataFishcatchSearch;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CatchDataFishcatchController implements the CRUD actions for CatchDataFishcatch model.
 */
class CatchDataFishcatchController extends Controller
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
     * Lists all CatchDataFishcatch models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CatchDataFishcatchSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CatchDataFishcatch model.
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
     * Creates a new CatchDataFishcatch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
{
    $model = new CatchDataFishcatch();

    if ($this->request->isPost) {
        if ($model->load($this->request->post()) && $model->save()) {

            // 🔥 redirect back to parent view
            return $this->redirect([
                'catch-data-request/view',
                'id' => $model->catchdata_req_id
            ]);
        }
    } else {
        $model->loadDefaultValues();
    }

    return $this->render('create', [
        'model' => $model,
    ]);
}

    /**
     * Updates an existing CatchDataFishcatch model.
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
     * Deletes an existing CatchDataFishcatch model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
{
    $model = $this->findModel($id);

    // 🔥 get parent ID BEFORE delete
    $requestId = $model->catchdata_req_id;

    $model->delete();

    // 🔥 redirect back to parent view
    return $this->redirect(['catch-data-request/view', 'id' => $requestId]);
}

    /**
     * Finds the CatchDataFishcatch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CatchDataFishcatch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CatchDataFishcatch::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionCreateAjax()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $data = json_decode(Yii::$app->request->getRawBody(), true);

    $model = new CatchDataFishcatch();
    $model->attributes = $data;

    if ($model->save()) {
        return ['success' => true];
    }

    return ['success' => false, 'errors' => $model->errors];
}
}
