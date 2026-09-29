<?php

namespace backend\controllers;

use backend\models\AuthItem;
use backend\models\AuthItemChild;
use backend\models\AuthItemSearch;
use Yii;
use yii\filters\VerbFilter;
use backend\components\Controller;
use backend\services\CommonService;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class AuthItemController extends Controller
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
     * Lists all AuthItem models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this, "admin");

        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $searchModel = new AuthItemSearch();
        $dataProviderPermisson = $searchModel->searchPermissions($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderPermisson' => $dataProviderPermisson,
        ]);
    }

    /**
     * Displays a single AuthItem model.
     * @param string $name Name
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($name)
    {
        return $this->render('view', [
            'model' => $this->findModel($name),
        ]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new AuthItem();
        $model->type = 1;
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['update-permission', 'name' => $model->name]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $name Name
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnauthorizedHttpException
     */
    public function actionUpdate($name)
    {
        $model = $this->findModel($name);
        if ($model->type == 0 || $model->type == 1) {
            throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));

        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'name' => $model->name]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdatePermission($name)
    {
        $permissionList = AuthItem::find()->where(['!=', 'type', 0])->andWhere(['!=', 'type', 1])->andWhere(['!=', 'type', 999])->orderBy(["name" => SORT_ASC])->all();
        $assignements = AuthItemChild::find()->where(["parent" => $name])->all();
        $childString = "";
        foreach ($assignements as $assignment) {
            $childString .= $assignment->child . ",";
        }
        if ($this->request->isPost) {
            $permissions = $this->request->post("permisions");
            AuthItemChild::deleteAll(["parent" => $name]);
            foreach ($permissions as $permission) {
                $insert = new AuthItemChild();
                $insert->parent = $name;
                $insert->child = $permission;
                $insert->validate();
//                print_r($insert->getErrors());
                $insert->save();


            }
//            print_r($name);
//            exit();
            return $this->redirect(['index']);
        }
        return $this->render('updatePermission', [
            'name' => $name,
            'permissionList' => $permissionList,
            'childString' => $childString,
        ]);
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $name Name
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($name)
    {

        $model = $this->findModel($name);
        if ($model->type == 0 || $model->type == 999) {
            throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));

        }
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $name Name
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($name)
    {
        if (($model = AuthItem::findOne(['name' => $name])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
