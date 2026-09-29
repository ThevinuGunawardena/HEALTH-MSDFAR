<?php

namespace backend\controllers;

use backend\models\NaklaexportApplication;
use backend\models\NaklaexportApplicationSearch;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NaklaexportApplicationController implements the CRUD actions for NaklaexportApplication model.
 */
class NaklaexportApplicationController extends Controller
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
     * Lists all NaklaexportApplication models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new NaklaexportApplicationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NaklaexportApplication model.
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
     * Creates a new NaklaexportApplication model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new NaklaexportApplication();

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
     * Updates an existing NaklaexportApplication model.
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
     * Deletes an existing NaklaexportApplication model.
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
     * Finds the NaklaexportApplication model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $business_reg_number Business Reg Number
     * @return NaklaexportApplication the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($business_reg_number)
    {
        if (($model = NaklaexportApplication::findOne(['business_reg_number' => $business_reg_number])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
