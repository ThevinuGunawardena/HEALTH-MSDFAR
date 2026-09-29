<?php

namespace app\controllers;

use app\models\reexportregistration;
use app\models\reexportregistrationSearch;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReexportregistrationSearchController implements the CRUD actions for reexportregistration model.
 */
class ReexportregistrationSearchController extends Controller
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
     * Lists all reexportregistration models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new reexportregistrationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single reexportregistration model.
     * @param int $business_reg_number Business Reg Number
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($business_reg_number)
    {
        return $this->render('view', [
            'model' => $this->findModel($business_reg_number),
        ]);
    }

    /**
     * Creates a new reexportregistration model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new reexportregistration();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'business_reg_number' => $model->business_reg_number]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing reexportregistration model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $business_reg_number Business Reg Number
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($business_reg_number)
    {
        $model = $this->findModel($business_reg_number);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'business_reg_number' => $model->business_reg_number]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing reexportregistration model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $business_reg_number Business Reg Number
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($business_reg_number)
    {
        $this->findModel($business_reg_number)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the reexportregistration model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $business_reg_number Business Reg Number
     * @return reexportregistration the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($business_reg_number)
    {
        if (($model = reexportregistration::findOne(['business_reg_number' => $business_reg_number])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
