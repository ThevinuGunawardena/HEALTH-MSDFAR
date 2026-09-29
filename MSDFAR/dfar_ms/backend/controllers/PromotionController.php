<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\models\Promotion;
use backend\services\CommonService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class PromotionController extends Controller
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
//            'query' => Promotion::find()->where(['profile_officer_id' => $profile_officer_id]),
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
        CommonService::validatePermission($this, "PromotionController-create");
        CommonService::validateEditPermission();
        CommonService::validateCurrentUserAuthForMethod($profile_officer_id);

        $model = new Promotion(['profile_officer_id' => $profile_officer_id]);
        $promotionLetterData = "";
        if ($model->load(Yii::$app->request->post())) {
            $promotionLetter = UploadedFile::getInstance($model, 'promotion_letter');
            if (!empty($promotionLetter)) {
                $uploadDir = Constant::$FILE_UPLOAD_PATH . 'officer/promotion_letter/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = $model->id . '_promotion_letter.' . $promotionLetter->extension;
                $promotionLetter->saveAs($uploadDir . $fileName);
                $model->promotion_letter = $fileName;
            } else {
                $model->promotion_letter = $promotionLetterData;
            }
        }
        if ($model->save()) {
            return $this->redirect(['profile-officers/view', 'id' => $profile_officer_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id, $profile_officer_id)
    {
        CommonService::validatePermission($this, "PromotionController-update");
        CommonService::validateEditPermission();
        CommonService::validateCurrentUserAuthForMethod($profile_officer_id);

        $model = $this->findModel($id);
        $promotionLetterData = $model->promotion_letter;
        if ($model->load(Yii::$app->request->post())) {
            $promotionLetter = UploadedFile::getInstance($model, 'promotion_letter');
            if (!empty($promotionLetter)) {
                $uploadDir = Constant::$FILE_UPLOAD_PATH . 'officer/promotion_letter/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = $model->id . '_promotion_letter.' . $promotionLetter->extension;
                $promotionLetter->saveAs($uploadDir . $fileName);
                $model->promotion_letter = $fileName;
            } else {
                $model->promotion_letter = $promotionLetterData;
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
        CommonService::validatePermission($this, "PromotionController-delete");
        CommonService::validateEditPermission();
        CommonService::validateCurrentUserAuthForMethod($profile_officer_id);

        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['profile-officers/view', 'id' => $profile_officer_id]);
    }

    protected function findModel($id)
    {
        if (($model = Promotion::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested promotion does not exist.');
    }
}