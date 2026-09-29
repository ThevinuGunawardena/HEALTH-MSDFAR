<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\models\BoatNumbers;
use backend\models\Files;
use backend\models\FishermanRegisterdBoat;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\FishermanRegisterdBoatLicenseSearch;
use backend\models\HighseasLicense;
use backend\models\MApprovalWorkflow;
use backend\models\MRequeredDocuments;
use backend\models\NationalLicense;
use backend\models\PaymentLog;
use backend\models\SpecialAltereration;
use backend\services\CommonService;
use backend\services\Util;
use Yii;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * BoatRegistrationController implements the CRUD actions for FishermanRegisterdBoat model.
 */
class BoatRegistrationSpecialController extends Controller
{
    public $processType = "BOAT_REGISTER";

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
     * Lists all FishermanRegisterdBoat models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this, "Special-BoatRegistrationController-list");

        $searchModel = new FishermanRegisterdBoatLicenseSearch();
        $dataProvider = $searchModel->searchLicense($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FishermanRegisterdBoat model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnauthorizedHttpException
     */
    public function actionView($id)
    {
        CommonService::validatePermission($this, "Special-BoatRegistrationController-office-view");

        $validated = false;
        $model = $this->findModel($id);
        $validated = false;
        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
        $files = Files::find()->where(["type" => $workflow->id, "process_id" => $id])->andWhere(["!=", "file_type", -999])->count();
        $requred_documents = MRequeredDocuments::find()->where(['type' => $workflow->id, "status" => 1])->count();

        if ($model->validate() && $files >= $requred_documents) {
            $validated = true;
        }
//        print_r( $model->getErrors()        );exit();
        $approvalFlow = CommonService::getApprovalProcess($model, $this->processType, $id, false);

        $paymentHistory = [];
        if ($model->status == 100 || $model->status == 101) {
            $paymentHistory = PaymentLog::find()->where(["type" => $this->processType, "process_id" => $id])->all();
        }

        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
        $files = Files::find()->where(["type" => $workflow->id, "process_id" => $id])->all();
        return $this->render('view', [
            'model' => $model,
            'approvalHistory' => $approvalFlow['approvalHistory'],
            'showRejectBtn' => $approvalFlow['showRejectBtn'],
            'showApproveBtn' => $approvalFlow['showApproveBtn'],
            'paymentHistory' => $paymentHistory,
            'process' => $this->processType,
            'validated' => $validated,
            'files' => $files,

        ]);
    }

    /**
     * Finds the FishermanRegisterdBoat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return FishermanRegisterdBoat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FishermanRegisterdBoatLicense::findOne(['nid' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Finds the FishermanRegisterdBoat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return FishermanRegisterdBoat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelReg($id)
    {
        if (($model = FishermanRegisterdBoat::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Updates an existing FishermanRegisterdBoat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     * @throws UnauthorizedHttpException
     */
    public function actionUpdate($id)
    {
        CommonService::validatePermission($this, "Special-BoatRegistrationController-update");
        $model = $this->findModel($id);
        $modelReg = $this->findModelReg($model->id);
        $specialAlter = new SpecialAltereration();
        $description = "Boat owner:" . $model->fisherman_id . " District:" . $model->district . " Division:" . $model->division . " landing:" . $model->landing_site;
        $boatValidated = false;
        $error = "";
        if ($this->request->isPost && $model->load($this->request->post()) && Util::editPermission()) {
            $transaction = Yii::$app->db->beginTransaction();
            $modelReg->fisherman_id = $model->fisherman_id;
            $modelReg->fisherman_id = $model->fisherman_id;

            $boatNumber = BoatNumbers::findOne($model->boat_number_id);
            $boatNumber->owner = $model->fisherman_id;
            $boatNumber->fisheries_district = $model->district;
            $boatNumber->fisheries_division = $model->division;
            $specialAlter->userId = Yii::$app->user->identity->getId();
            $specialAlter->title = "Boat registration";
            $specialAlter->desciption = $description;
            $file = UploadedFile::getInstance($specialAlter, 'file');
            if (isset($file)) {
                $fileName = "ALTER" . "_" . $id . "_" . date("Ymd-His") . "." . $file->extension;

//                    if ( $model->file_type==-999){
//                        $fileName = $process . "_" . $id . "_" . $model->file_type."_".date("Ymd-His") .".". $file->extension;
//
//                    }
                $file->saveAs(Constant::$FILE_UPLOAD_PATH.'files/alter/' . $fileName);
                $specialAlter->file = $fileName;
//                $model->save();
            }


            if ($model->validate() && $specialAlter->validate()) {
                $nationalLicences = NationalLicense::find()->where(["boat_registration_id" => $modelReg->id])->all();
                foreach ($nationalLicences as $nationalLicence) {
                    $description = $description . " --- National licence: id" . $nationalLicence->id . " District:" . $nationalLicence->fisheries_district . " Division:" . $nationalLicence->division;
                    $nationalLicence->fisheries_district = $model->district;
                    $nationalLicence->division = $model->division;
                    if ($nationalLicence->save(false)) {
                        $description = $description . " UPDATED";
                    } else {
                        $nationalLicence->validate();
                        $error .= "nationalLicence " . $nationalLicence->id . "<br>";
                        $error .= json_encode($nationalLicence->getErrors());
                        $transaction->rollBack();
                    }
                    $description = $description . " ---- ";
                }

                $highseasLicences = HighseasLicense::find()->where(["boat_registration_id" => $modelReg->id])->all();
                foreach ($highseasLicences as $highseasLicence) {
                    $description = $description . " --- Highseas licence: id" . $highseasLicence->id . " District:" . $highseasLicence->district . " Division:" . $highseasLicence->division;
                    $highseasLicence->district = $model->district;
                    $highseasLicence->division = $model->division;
                    if ($highseasLicence->save(false)) {
                        $description = $description . " UPDATED";
                    } else {
                        $highseasLicence->validate();
                        $error .= "HIGHSEAS " . $highseasLicence->id . "<br>";
                        $error .= json_encode($highseasLicence->getErrors());
                        $transaction->rollBack();
                    }
//                    $newPerson = serialize($highseasLicence->getErrors());

                    $description = $description . " ---- ";
                }
            }

            $specialAlter->desciption = $description . " ->>>> new Boat owner:" . $model->fisherman_id . " District:" . $model->district . " Division:" . $model->division . " landing:" . $model->landing_site;
            if (!$specialAlter->validate() || !$modelReg->validate() || !$boatNumber->validate() || !$model->validate()) {
                if (!$specialAlter->validate()) {
                    $error .= "specialAlter  <br>";
                    $error .= json_encode($specialAlter->getErrors());
                }
                if (!$modelReg->validate()) {
                    $error .= "modelReg  <br>";
                    $error .= json_encode($modelReg->getErrors());
                }
                if (!$boatNumber->validate()) {
                    $error .= "boatNumber  <br>";
                    $error .= json_encode($boatNumber->getErrors());
                }
                if (!$model->validate()) {
                    $error .= "boat reg license <br>";
                    $error .= json_encode($model->getErrors());
                }
                $transaction->rollBack();

            }

            if ($error == "" && $modelReg->save(false) && $boatNumber->save(false) && $model->save(false) && $specialAlter->save(false)) {
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->nid]);
            }
            $transaction->rollBack();
//            exit();
        }

        return $this->render('update', [
            'boatValidated' => $boatValidated,
            'model' => $model,
            'specialAlter' => $specialAlter,
            'error' => $error,
        ]);
    }


}
