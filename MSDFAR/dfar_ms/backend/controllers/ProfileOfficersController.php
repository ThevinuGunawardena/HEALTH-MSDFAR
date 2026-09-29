<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ExamTraining;
use backend\models\LanguageQualification;
use backend\models\ProfileOfficer;
use backend\models\ProfileOfficers;
use backend\models\ProfileOfficersSearch;
use backend\models\Promotion;
use backend\models\Salary;
use backend\models\SignupForm;
use backend\models\User;
use backend\services\CommonService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ProfileOfficersController extends Controller
{
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

    public function actionIndex()
    {
        CommonService::validatePermission($this, "ProfileOfficersController-list");
        CommonService::validateEditPermission();
        $searchModel = new ProfileOfficersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        CommonService::validateLoginUser($this);
        if (!UserTypeUtil::hasType(Constant::ADMINISTRATION)) {

            $id = Yii::$app->user->identity->profile_id;
        }
        $model = $this->findModel($id);
        $examTrainingDataProvider = new ActiveDataProvider([
            'query' => ExamTraining::find()->where(['profile_officer_id' => $id]),
        ]);
        $languageQualificationDataProvider = new ActiveDataProvider([
            'query' => LanguageQualification::find()->where(['profile_officer_id' => $id]),
        ]);
        $promotionDataProvider = new ActiveDataProvider([
            'query' => Promotion::find()->where(['profile_officer_id' => $id]),
        ]);
        $salaryDataProvider = new ActiveDataProvider([
            'query' => Salary::find()->where(['profile_officer_id' => $id]),
        ]);
        $msdfarEnabled = false;
        $user = User::find()->where(['profile_id' => $id])->one();
        if (!empty($user)) {
            $status = $user->status;
            if ($status == 10) {
                $msdfarEnabled = true;
            }
        }


        return $this->render('view', [
            'model' => $model,
            'examTrainingDataProvider' => $examTrainingDataProvider,
            'languageQualificationDataProvider' => $languageQualificationDataProvider,
            'promotionDataProvider' => $promotionDataProvider,
            'salaryDataProvider' => $salaryDataProvider,
            'msdfarEnabled' => $msdfarEnabled,
        ]);
    }

    public function actionCreate()
    {
        CommonService::validatePermission($this, "ProfileOfficersController-create");
        CommonService::validateEditPermission();
        $model = new ProfileOfficers();
        $appointmentLetterData = "";
        $dfarAppointmentLetterData = "";
        if ($model->load(Yii::$app->request->post())) {
            $model->status = 1;
            $model->force_reset_pw = 1;

            $appointmentLetter = UploadedFile::getInstance($model, 'appointment_letter');
            if (!empty($appointmentLetter)) {
                $fileName = $model->id . '_appointment_letter.' . $appointmentLetter->extension;
                $appointmentLetter->saveAs($this->ensureUploadDir('officer/appointment_letter/') . $fileName);
                $model->appointment_letter = $fileName;
            } else {
                $model->appointment_letter = $appointmentLetterData;
            }

            $dfarAppointmentLetter = UploadedFile::getInstance($model, 'dfar_appointment_letter');
            if (!empty($dfarAppointmentLetter)) {
                $fileName = $model->id . 'dfar_appointment_letter.' . $dfarAppointmentLetter->extension;
                $dfarAppointmentLetter->saveAs($this->ensureUploadDir('officer/dfar_appointment_letter/') . $fileName);
                $model->dfar_appointment_letter = $fileName;
            } else {
                $model->dfar_appointment_letter = $dfarAppointmentLetterData;
            }

            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        if (!UserTypeUtil::hasType(Constant::ADMINISTRATION)) {
            $id = Yii::$app->user->identity->profile_id;
        }
        $model = $this->findModel($id);
        $appointmentLetterData = $model->appointment_letter;
        $dfarAppointmentLetterData = $model->dfar_appointment_letter;
        $signature = $model->signature;
        $agreement = $model->agreement;
        $profile_image = $model->profile_image;
        $it_result_sheet = $model->it_result_sheet;
        $cetificate = $model->cetificate;
        if ($model->load(Yii::$app->request->post())) {
            $appointmentLetter = UploadedFile::getInstance($model, 'appointment_letter');
            if (!empty($appointmentLetter)) {
                $fileName = $model->id . '_appointment_letter.' . $appointmentLetter->extension;
                $appointmentLetter->saveAs($this->ensureUploadDir('officer/appointment_letter/') . $fileName);
                $model->appointment_letter = $fileName;
            } else {
                $model->appointment_letter = $appointmentLetterData;
            }

            $dfarAppointmentLetter = UploadedFile::getInstance($model, 'dfar_appointment_letter');
            if (!empty($dfarAppointmentLetter)) {
                $fileName = $model->id . '_dfar_appointment_letter.' . $dfarAppointmentLetter->extension;
                $dfarAppointmentLetter->saveAs($this->ensureUploadDir('officer/dfar_appointment_letter/') . $fileName);
                $model->dfar_appointment_letter = $fileName;
            } else {
                $model->dfar_appointment_letter = $dfarAppointmentLetterData;
            }

            $fileSignature = UploadedFile::getInstance($model, 'signature');
            if (isset($fileSignature) && !empty($fileSignature)) {
                $fileName = $model->id . '_signature.' . $fileSignature->extension;
                $fileSignature->saveAs($this->ensureUploadDir('officer/signature/') . $fileName);
                $model->signature = $fileName;
            } else {
                $model->signature = $signature;
            }
            $fileAgreement = UploadedFile::getInstance($model, 'agreement');
            if (isset($fileAgreement) && !empty($fileAgreement)) {
                $fileName = $model->id . '_agreement.' . $fileAgreement->extension;
                $fileAgreement->saveAs($this->ensureUploadDir('officer/agreement/') . $fileName);
                $model->agreement = $fileName;
            } else {
                $model->agreement = $agreement;
            }
            $fileprofile_image = UploadedFile::getInstance($model, 'profile_image');
            if (isset($fileprofile_image) && !empty($fileprofile_image)) {
                $fileName = $model->id . '_profile_image.' . $fileprofile_image->extension;
                $fileprofile_image->saveAs($this->ensureUploadDir('officer/profile/') . $fileName);
                $model->profile_image = $fileName;
            } else {
                $model->profile_image = $profile_image;
            }
            $fileit_result_sheet = UploadedFile::getInstance($model, 'it_result_sheet');
            if (isset($fileit_result_sheet) && !empty($fileit_result_sheet)) {
                $fileName = $model->id . '_it_result_sheet.' . $fileit_result_sheet->extension;
                $fileit_result_sheet->saveAs($this->ensureUploadDir('officer/cetificate/') . $fileName);
                $model->it_result_sheet = $fileName;
            } else {
                $model->it_result_sheet = $it_result_sheet;
            }
            $filecetificate = UploadedFile::getInstance($model, 'cetificate');
            if (isset($filecetificate) && !empty($filecetificate)) {
                $fileName = $model->id . '_cetificate.' . $filecetificate->extension;
                $filecetificate->saveAs($this->ensureUploadDir('officer/cetificate/') . $fileName);
                $model->cetificate = $fileName;
            } else {
                $model->cetificate = $cetificate;
            }
            if ($model->save()) {
                Yii::$app->session->set('officer_district', $model->district);
                Yii::$app->session->set('officer_division', $model->division);
                Yii::$app->session->set('officer_harbour', $model->harbour);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//        return $this->redirect(['index']);
//    }

    public function actionEnableMsdfar($id)
    {
        CommonService::validatePermission($this, "ProfileOfficersController-enable-dfar");
        CommonService::validateEditPermission();

        $userProfile = $this->findModel($id);

        $user = User::find()->where(['profile_id' => $id])->one();
        if (!empty($user)) {
            $user->status = 10;
            $user->save();
            Yii::$app->session->setFlash('success', 'MSDFAR is enabled for this user');
            return $this->redirect(['view', 'id' => $id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        $model = new SignupForm();
        $model->nic = $userProfile->nic;
        $model->email = empty($userProfile->personal_email) ? $userProfile->nic . '@dfar.com' : $userProfile->personal_email;
        $model->password = "abc@1234";
        $model->type = 0;
        $model->user_permission = 1;
        $model->user_role = "OFFICER";
        if ($user = $model->signupOfficers()) {
            $user->profile_id = $userProfile->id;
            $user->save();
            $transaction->commit();
            Yii::$app->session->setFlash('success', 'MSDFAR is enabled for this user');
            return $this->redirect(['view', 'id' => $id]);

        } else {
            $transaction->rollBack();
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Ensures an officer upload sub-directory exists under the configured
     * upload path and returns its absolute path (with trailing slash).
     *
     * Several file fields (signature, agreement, profile image, certificates)
     * previously called saveAs() directly; on a fresh install those folders
     * do not exist yet, so move_uploaded_file() failed with
     * "No such file or directory". Creating the folder on demand fixes that
     * for every upload type.
     *
     * @param string $relative e.g. 'officer/signature/'
     * @return string absolute directory path ending in a slash
     */
    protected function ensureUploadDir($relative)
    {
        $dir = Constant::$FILE_UPLOAD_PATH . $relative;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    protected function findModel($id)
    {
        if (UserTypeUtil::hasType(Constant::ADMINISTRATION)) {
            if (($model = ProfileOfficers::findOne($id)) !== null) {
                return $model;
            }
        } else {
            if (($model = ProfileOfficer::findOne($id)) !== null) {
                return $model;
            }
        }
        throw new NotFoundHttpException('The requested profile officer does not exist.');
    }
     public function actionPrivacyPolicy(){

    return $this->render('privacyPolicy', [
    ]);

    }
}