<?php

namespace backend\controllers;

use backend\models\ScientificEnumerationRequest;
use backend\services\CommonService;
use Yii;
use yii\data\ActiveDataProvider;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;

/**
 * ScientificEnumerationController implements the CRUD actions for ScientificEnumerationRequest model.
 */
class ScientificEnumerationController extends Controller
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
     * Lists all ScientificEnumerationRequest models.
     *
     * @return string
     * @throws UnauthorizedHttpException
     */
    public function actionIndex()
    {
        throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));

        $dataProvider = new ActiveDataProvider([
            'query' => ScientificEnumerationRequest::find(),
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            */
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ScientificEnumerationRequest model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnauthorizedHttpException
     */
    public function actionView($id)
    {
        throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ScientificEnumerationRequest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     * @throws UnauthorizedHttpException
     */
    public function actionCreate()
    {
        CommonService::validatePermission($this,"ScientificController");

        $model = new ScientificEnumerationRequest();

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
     * Updates an existing ScientificEnumerationRequest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnauthorizedHttpException
     */
    public function actionUpdate($id)
    {
        throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ScientificEnumerationRequest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnauthorizedHttpException
     */
    public function actionDelete($id)
    {
        throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ScientificEnumerationRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ScientificEnumerationRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScientificEnumerationRequest::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
