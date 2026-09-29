<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ProfileOfficer;
use backend\models\ProfileOfficers;
use backend\models\ProfileOfficerSearch;
use backend\models\SignupForm;
use backend\models\User;
use backend\services\CommonService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

use backend\models\AuthenticatedPasswordChangeForm;
use yii\web\UnauthorizedHttpException;

/**
 * OfficerController implements the CRUD actions for ProfileOfficer model.
 */
class OfficerController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'except' => [],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ProfileOfficer models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this, "admin");

        $searchModel = new ProfileOfficerSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProfileOfficer model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
//        CommonService::validatePermission($this, "admin");
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ProfileOfficer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
//        CommonService::validatePermission($this, "test-app");

        $model = new ProfileOfficers();
        $model->force_reset_pw = 1;
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $user = User::findOne(["id" => Yii::$app->user->identity->id]);
                $profileId = $user->profile_id;
                $model->status = 1;
                if ($model->save()) {

                    $fileSignature = UploadedFile::getInstance($model, 'signature');
                    if (isset($fileSignature)) {
                        $fileName = $model->id . '_signature.' . $fileSignature->extension;
                        $fileSignature->saveAs(Constant::$FILE_UPLOAD_PATH . 'officer/signature/' . $fileName);

                        $model->signature = $fileName;
                    }
                    $fileagreement = UploadedFile::getInstance($model, 'agreement');
                    if (isset($fileagreement)) {
                        $fileName = $model->id . '_agreement.' . $fileagreement->extension;
                        $fileagreement->saveAs(Constant::$FILE_UPLOAD_PATH . 'officer/agreement/' . $fileName);

                        $model->agreement = $fileName;
                    }
                    $fileprofile_image = UploadedFile::getInstance($model, 'profile_image');
                    if (isset($fileprofile_image)) {
                        $fileName = $model->id . '_profile_image.' . $fileprofile_image->extension;
                        $fileprofile_image->saveAs(Constant::$FILE_UPLOAD_PATH . 'officer/profile/' . $fileName);

                        $model->profile_image = $fileName;
                    }
                    $fileit_result_sheet = UploadedFile::getInstance($model, 'it_result_sheet');
                    if (isset($fileit_result_sheet)) {
                        $fileName = $model->id . '_it_result_sheet.' . $fileit_result_sheet->extension;
                        $fileit_result_sheet->saveAs(Constant::$FILE_UPLOAD_PATH . 'officer/cetificate/' . $fileName);

                        $model->it_result_sheet = $fileName;
                    }
                    $filecetificate = UploadedFile::getInstance($model, 'cetificate');
                    if (isset($filecetificate)) {
                        $fileName = $model->id . '_cetificate.' . $filecetificate->extension;
                        $filecetificate->saveAs(Constant::$FILE_UPLOAD_PATH . 'officer/cetificate/' . $fileName);

                        $model->cetificate = $fileName;
                    }
                }
                if ($model->save()) {
                    if ($profileId == 0) {
                        $user->profile_id = $model->id;
                        $user->save();
                    }
                    return $this->goHome();

                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProfileOfficer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id = 0)
    {
//        CommonService::validatePermission($this, "test-app");
        if (UserTypeUtil::hasType(Constant::ADMIN)) {
            $id = $id;
        } else {
            $id = Yii::$app->user->identity->profile_id;
        }
        $model = $this->findModel($id);
        $signature = $model->signature;
        $agreement = $model->agreement;
        $profile_image = $model->profile_image;
        $it_result_sheet = $model->it_result_sheet;
        $cetificate = $model->cetificate;
        if ($this->request->isPost && $model->load($this->request->post())) {

            $fileSignature = UploadedFile::getInstance($model, 'signature');
            if (isset($fileSignature) && !empty($fileSignature)) {
                $fileName = $model->id . '_signature.' . $fileSignature->extension;
                $fileSignature->saveAs(Constant::$FILE_UPLOAD_PATH . 'officer/signature/' . $fileName);
                $model->signature = $fileName;
            } else {
                $model->signature = $signature;
            }
            $fileAgreement = UploadedFile::getInstance($model, 'agreement');
            if (isset($fileAgreement) && !empty($fileAgreement)) {
                $fileName = $model->id . '_agreement.' . $fileAgreement->extension;
                $fileAgreement->saveAs(Constant::$FILE_UPLOAD_PATH . 'officer/agreement/' . $fileName);
                $model->agreement = $fileName;
            } else {
                $model->agreement = $agreement;
            }
            $fileprofile_image = UploadedFile::getInstance($model, 'profile_image');
            if (isset($fileprofile_image) && !empty($fileprofile_image)) {
                $fileName = $model->id . '_profile_image.' . $fileprofile_image->extension;
                $fileprofile_image->saveAs(Constant::$FILE_UPLOAD_PATH . 'officer/profile/' . $fileName);
                $model->profile_image = $fileName;
            } else {
                $model->profile_image = $profile_image;
            }
            $fileit_result_sheet = UploadedFile::getInstance($model, 'it_result_sheet');
            if (isset($fileit_result_sheet) && !empty($fileit_result_sheet)) {
                $fileName = $model->id . '_it_result_sheet.' . $fileit_result_sheet->extension;
                $fileit_result_sheet->saveAs(Constant::$FILE_UPLOAD_PATH . 'officer/cetificate/' . $fileName);
                $model->it_result_sheet = $fileName;
            } else {
                $model->it_result_sheet = $it_result_sheet;
            }
            $filecetificate = UploadedFile::getInstance($model, 'cetificate');
            if (isset($filecetificate) && !empty($filecetificate)) {
                $fileName = $model->id . '_cetificate.' . $filecetificate->extension;
                $filecetificate->saveAs(Constant::$FILE_UPLOAD_PATH . 'officer/cetificate/' . $fileName);
                $model->cetificate = $fileName;
            } else {
                $model->cetificate = $cetificate;
            }
            if ($model->save()) {
                Yii::$app->session->set('officer_district', $model->district);
                Yii::$app->session->set('officer_division', $model->division);
                Yii::$app->session->set('officer_harbour', $model->harbour);
                return $this->redirect(["officer/view", "id" => $model->id]);

            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ProfileOfficer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProfileOfficer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ProfileOfficer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProfileOfficers::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.s' . $id));
    }


public function actionPasswordUpdate()
{
    if (Yii::$app->user->isGuest) {
        throw new \yii\web\UnauthorizedHttpException(
            'Authentication is required.'
        );
    }

    $modelData = User::findOne(Yii::$app->user->id);

    if ($modelData === null) {
        throw new \yii\web\NotFoundHttpException(
            'User account was not found.'
        );
    }

    $model = new AuthenticatedPasswordChangeForm();

    if (
        $this->request->isPost &&
        $model->load($this->request->post()) &&
        $model->changePassword()
    ) {
        $profile = ProfileOfficers::findOne(
            Yii::$app->user->identity->profile_id
        );

        if ($profile !== null) {
            $profile->force_reset_pw = 0;

            if (!$profile->save(false, ['force_reset_pw'])) {
                Yii::$app->session->setFlash(
                    'error',
                    'Unable to update the password reset status.'
                );

                return $this->refresh();
            }
        }

        Yii::$app->user->logout(true);

        return $this->redirect(['site/login']);
    }

    return $this->render('pwreset', [
        'model' => $model,
        'modelData' => $modelData,
    ]);
}
}
