<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\models\ExamTraining;
use backend\services\CommonService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ExamTrainingController extends Controller
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
//        $dataProvider = new ActiveDataProvider([
//            'query' => ExamTraining::find()->where(['profile_officer_id' => $profile_officer_id]),
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
        CommonService::validatePermission($this, "ExamTrainingController-create");
        CommonService::validateEditPermission();
        CommonService::validateCurrentUserAuthForMethod($profile_officer_id);

        $model = new ExamTraining(['profile_officer_id' => $profile_officer_id]);
        $resultsCertificateData = $model->results_certificate;
        if ($model->load(Yii::$app->request->post())) {
            $resultsCertificate = UploadedFile::getInstance($model, 'results_certificate');
            if (!empty($resultsCertificate)) {
                $uploadDir = Constant::$FILE_UPLOAD_PATH . 'officer/exam_training/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = $model->id . '_results_certificate.' . $resultsCertificate->extension;
                $resultsCertificate->saveAs($uploadDir . $fileName);
                $model->results_certificate = $fileName;
            } else {
                $model->results_certificate = $resultsCertificateData;
            }
            if ($model->save()) {
                return $this->redirect(['profile-officers/view', 'id' => $profile_officer_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id, $profile_officer_id)
    {
        CommonService::validatePermission($this, "ExamTrainingController-update");
        CommonService::validateEditPermission();
        CommonService::validateCurrentUserAuthForMethod($profile_officer_id);

        $model = $this->findModel($id);
        $resultsCertificateData = $model->results_certificate;
        if ($model->load(Yii::$app->request->post())) {
            $resultsCertificate = UploadedFile::getInstance($model, 'results_certificate');
            if (!empty($resultsCertificate)) {
                $uploadDir = Constant::$FILE_UPLOAD_PATH . 'officer/exam_training/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = $model->id . '_results_certificate.' . $resultsCertificate->extension;
                $resultsCertificate->saveAs($uploadDir . $fileName);
                $model->results_certificate = $fileName;
            } else {
                $model->results_certificate = $resultsCertificateData;
            }
            if ($model->save()) {
                return $this->redirect(['profile-officers/view', 'id' => $profile_officer_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id, $profile_officer_id)
    {
        CommonService::validatePermission($this, "ExamTrainingController-delete");
        CommonService::validateEditPermission();
        CommonService::validateCurrentUserAuthForMethod($profile_officer_id);

        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['profile-officers/view', 'id' => $profile_officer_id]);
    }

    protected function findModel($id)
    {
        if (($model = ExamTraining::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested exam/training does not exist.');
    }
}