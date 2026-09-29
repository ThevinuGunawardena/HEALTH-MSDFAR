<?php

namespace backend\controllers;

use backend\models\BoatDesign;
use backend\models\BoatDesignSearch;
use backend\services\CommonService;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * BoatDesignController implements the CRUD actions for BoatDesign model.
 */
class BoatDesignController extends Controller
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
     * Lists all BoatDesign models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this,"test-app");

        $searchModel = new BoatDesignSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BoatDesign model.
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
     * Creates a new BoatDesign model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        CommonService::validatePermission($this, "test-app");

        $model = new BoatDesign();
        $model->status = 1;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'yardsList' => CommonService::getYardsArray(),
            'boatCategories' => CommonService::getBoatCategoriesArray(),
        ]);
    }

    /**
     * Updates an existing BoatDesign model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        CommonService::validatePermission($this, "test-app");

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'yardsList' => CommonService::getYardsArray(),
            'boatCategories' => CommonService::getBoatCategoriesArray(),
        ]);
    }

    /**
     * Deletes an existing BoatDesign model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {

        return $this->redirect(['index']);
    }

    /**
     * Finds the BoatDesign model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return BoatDesign the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BoatDesign::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionListByYard($yard = 0)
    {
        $data = [];
        if ($yard != 0)
            $data = BoatDesign::find()->where(["yard" => $yard])->asArray()->all();
        echo json_encode($data);
        exit();
    }
}
