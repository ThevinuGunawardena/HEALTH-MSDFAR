<?php

namespace backend\controllers;
use backend\services\CommonService;
use Yii;
use backend\models\ApiKeys;
use backend\models\ApiKeysSearch;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/**
 * ApiKeysController implements the CRUD actions for ApiKeys model.
 */
class ApiKeysController extends Controller
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
     * Lists all ApiKeys models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this, "admin");
        $searchModel = new ApiKeysSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ApiKeys model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        CommonService::validatePermission($this, "admin");

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ApiKeys model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
{
    CommonService::validatePermission($this, "admin");

    $model = new ApiKeys();

    if ($model->load(Yii::$app->request->post())) {

        // Generate plain API key
        $plainApiKey = Yii::$app->security->generateRandomString(64);

        // Store only hashed API key in DB
        $model->api_key_hash = Yii::$app->security->generatePasswordHash($plainApiKey);

        if ($model->save()) {
            Yii::$app->session->setFlash(
                'success',
                'API key generated successfully. Copy this key now. It will not be shown again: ' . $plainApiKey
            );

            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    return $this->render('create', [
        'model' => $model,
    ]);
}

    /**
     * Updates an existing ApiKeys model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        CommonService::validatePermission($this, "admin");

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ApiKeys model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        CommonService::validatePermission($this, "admin");

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ApiKeys model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ApiKeys the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        CommonService::validatePermission($this, "admin");

        if (($model = ApiKeys::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
