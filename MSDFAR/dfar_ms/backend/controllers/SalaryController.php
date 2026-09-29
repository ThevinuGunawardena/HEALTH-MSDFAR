<?php

namespace backend\controllers;

use backend\models\Salary;
use backend\services\CommonService;
use backend\services\Util;
use Yii;
use yii\filters\AccessControl;
use backend\components\Controller;

use yii\web\NotFoundHttpException;

class SalaryController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

//    public function actionIndex($profile_officer_id)
//    {
//        CommonService::validatePermission($this, "HighseasLicenseController-list");
//        $dataProvider = new ActiveDataProvider([
//            'query' => Salary::find()->where(['profile_officer_id' => $profile_officer_id]),
//        ]);
//
//        return $this->render('index', [
//            'dataProvider' => $dataProvider,
//            'profile_officer_id' => $profile_officer_id,
//        ]);
//    }

//    public function actionView($id)
//    {
//        $model = $this->findModel($id);
//        return $this->render('view', [
//            'model' => $model,
//        ]);
//    }

    public function actionCreate($profile_officer_id)
    {
        CommonService::validatePermission($this, "SalaryController-create");
        CommonService::validateEditPermission();
        CommonService::validateCurrentUserAuthForMethod($profile_officer_id);

        $model = new Salary(['profile_officer_id' => $profile_officer_id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['profile-officers/view', 'id' => $profile_officer_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id, $profile_officer_id)
    {
        CommonService::validatePermission($this, "SalaryController-update");
        CommonService::validateEditPermission();
        CommonService::validateCurrentUserAuthForMethod($profile_officer_id);

        $model = $this->findModel($id);

        if (Util::editPermission() && $model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['profile-officers/view', 'id' => $profile_officer_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id, $profile_officer_id)
    {
        CommonService::validatePermission($this, "SalaryController-delete");
        CommonService::validateEditPermission();
        CommonService::validateCurrentUserAuthForMethod($profile_officer_id);

        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['profile-officers/view', 'id' => $profile_officer_id]);

    }

    protected function findModel($id)
    {
        if (($model = Salary::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested salary does not exist.');
    }
}