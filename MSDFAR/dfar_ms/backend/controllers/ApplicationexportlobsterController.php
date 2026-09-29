<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Applicationexportlobster;
use backend\models\ApplicationexportlobsterSearch;
use backend\models\ExportChankConsignment;
use backend\models\ExportCompany;
use backend\models\ExportLobsterTypes;
use backend\models\Files;
use backend\models\MApprovalWorkflow;
use backend\models\MRequeredDocuments;
use backend\models\PaymentLog;
use backend\services\CommonService;
use backend\services\Util;
use Exception;
use kartik\mpdf\Pdf;
use Yii;
use backend\components\Controller;

use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ApplicationexportlobsterController extends Controller
{

    public $processType = "ExportLobster";

    /**
     * Lists all Applicationexportlobster models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity->type == Constant::EXPORT_COMPANY && Util::editPermission()) {
            if (($model = ExportCompany::findOne(['id' => Yii::$app->user->identity->profile_id])) !== null) {
                CommonService::validateLicenseType($model, $this->processType);
            }
        }
        $searchModel = new ApplicationexportlobsterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Applicationexportlobster model.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $consignments = ExportLobsterTypes::find()->where(['application_id' => $model->id])->all();

        if (empty($consignments)) {
            // If no records exist, initialize with a new model
            $consignments = [new ExportLobsterTypes()];
        }
        $validated = false;
        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
        $files = Files::find()->where(["type" => $workflow->id, "process_id" => $id])->andWhere(["!=", "file_type", -999])->count();
        $requred_documents = MRequeredDocuments::find()->where(['type' => $workflow->id, "status" => 1])->count();
        $model->validate();
        if ($model->validate()) {
            $validated = true;
        }

        $approvalFlow = CommonService::getApprovalProcess($model, $this->processType, $id, false);
        if ($this->request->isPost && Util::editPermission()) {
            $model = CommonService::markApprovalStage($approvalFlow, $model, $id, $this->processType);

            if ($model->save()) {
                return $this->redirect(['view',
                    'id' => $model->id]);
            }
        }
        $paymentHistory = [];
        if ($model->status == 100 || $model->status == 101) {
            $paymentHistory = PaymentLog::find()->where(["type" => $this->processType, "process_id" => $id])->all();
        }
        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
        $files = Files::find()->where(["type" => $workflow->id, "process_id" => $id])->all();

        return $this->render('view', [
            'model' => $model,
            'consignments' => $consignments,
            'approvalHistory' => $approvalFlow['approvalHistory'],
            'showRejectBtn' => $approvalFlow['showRejectBtn'],
            'showApproveBtn' => $approvalFlow['showApproveBtn'],
            'process' => $this->processType,
            'validated' => $validated,
            'paymentHistory' => $paymentHistory,
            'files' => $files
        ]);
    }

    /**
     * Creates a new Applicationexportlobster model.
     * If creation is successful, the browser will be redirected to the 'report' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Applicationexportlobster();
        $model->company = UserTypeUtil::hasType(Constant::EXPORT_COMPANY) ? Yii::$app->user->identity->profile_id : "";
        $consignments = [new ExportLobsterTypes()];
        if ($model->load(Yii::$app->request->post())) {
            $company = ExportCompany::findOne($model->company);
            $model->full_name = $company->company_name;
            $model->address = $company->address;
            // Handle export countries
            if (isset($model->export_countries)) {
                $model->export_countries = implode(',', $model->export_countries);
            } else {
                $model->export_countries = '';
            }
            $model->approval_stage = "" . "17";
            $model->status = 1;
            $model->company = Yii::$app->user->identity->profile_id;

            if ($model->save()) {

                $post = Yii::$app->request->post('ExportLobsterTypes');
                $species = $post['lobster_species'] ?? [];

                foreach ($species as $index => $value) {
                    if (!empty($value)) { // Only save rows with at least species filled
                        $newModel = new ExportLobsterTypes();
                        $newModel->application_id = $model->id;
                        $newModel->lobster_species = $value;
                        $newModel->weight = $post['weight'][$index] ?? null;
                        $newModel->caught_from = $post['caught_from'][$index] ?? null;
                        $newModel->save();
                    }
                }

                CommonService::addApprovalLog($this->processType, "Submitted", "", $model->id);
                if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                return $this->goHome();

            } else {
                Yii::error('Model save failed: ' . json_encode($model->errors), __METHOD__);
                Yii::$app->session->setFlash('error', 'Failed to save the model: ' . json_encode($model->errors));
            }
        }

        return $this->render('create', [
            'model' => $model,
            'consignments' => $consignments,
        ]);
    }

    /**
     * Updates an existing Applicationexportlobster model.
     * If update is successful, the browser will be redirected to the 'report' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $consignments = ExportLobsterTypes::find()->where(['application_id' => $model->id])->all();

        if (empty($consignments)) {
            // If no records exist, initialize with a new model
            $consignments = [new ExportLobsterTypes()];
        }
        if ($model->approval_stage == "Completed") {
            throw new MethodNotAllowedHttpException('This application is already approved. Editing is not allowed.');
        }
        if ($model->load(Yii::$app->request->post())) {
            // Handle export countries
            if (isset($model->export_countries)) {
                $model->export_countries = implode(',', $model->export_countries);
            } else {
                $model->export_countries = '';
            }

            if ($model->save()) {
                $post = Yii::$app->request->post('ExportLobsterTypes');
                $species = $post['lobster_species'] ?? [];
                $ids = $post['id'] ?? [];

                // Delete all existing records for this application_id
                ExportChankConsignment::deleteAll(['application_id' => $model->id]);
                // Save new records
                foreach ($species as $index => $value) {
                    if (!empty($value)) { // Only save rows with at least species filled
                        $newModel = new ExportLobsterTypes();
                        $newModel->id = isset($ids[$index]) ? $ids[$index] : null; // Preserve ID if updating
                        $newModel->application_id = $model->id;
                        $newModel->lobster_species = $value;
                        $newModel->weight = $post['weight'][$index] ?? null;
                        $newModel->caught_from = $post['caught_from'][$index] ?? null;

                        if (!$newModel->save()) {
                            throw new Exception('Failed to save row ' . ($index + 1));
                        }
                    }
                }
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::error('Model save failed: ' . json_encode($model->errors), __METHOD__);
                Yii::$app->session->setFlash('error', 'Failed to save the model: ' . json_encode($model->errors));
            }
        }

        return $this->render('update', [
            'model' => $model,
            'consignments' => $consignments,
        ]);
    }

    /**
     * Deletes an existing Applicationexportlobster model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Applicationexportlobster model based on its primary key value.
     * @param int $id ID
     * @return Applicationexportlobster the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Applicationexportlobster::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Displays the report for a single Applicationexportlobster model.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionReport($id)
    {
        return $this->render('report', [
            'model' => $this->findModel($id),
        ]);
    }


    public function actionLicenseView($id): string
    {
//        CommonService::validatePermission($this, "BoatNumbersController-license-view");

        $model = $this->findModel($id);
        $consignments = ExportLobsterTypes::find()->where(['application_id' => $id])->all();
        if (empty($consignments)) {
            // If no records exist, initialize with a new model
            $consignments = [new ExportLobsterTypes()];
        }
        return $this->render('licenseView', ['model' => $model, 'consignments' => $consignments]);

    }

    public function actionLicenseDownload($id): string
    {
//        CommonService::validatePermission($this, "BoatNumbersController-license-download");

        $model = $this->findModel($id);
        $consignments = ExportLobsterTypes::find()->where(['application_id' => $id])->all();
        if (empty($consignments)) {
            // If no records exist, initialize with a new model
            $consignments = [new ExportLobsterTypes()];
        }
        $content = $this->renderPartial('license', ['model' => $model, 'consignments' => $consignments]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'defaultFontSize' => 45,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'options' => [
                'curlAllowUnsafeSslRequests' => true,
                'allow_url_fopen' => true,
            ],
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();

    }


    public function actionPayment($id)
    {
        CommonService::validatePermission($this, "Export-license-controller-payment");

        $application = $this->findModel($id);
        $paymentLog = new PaymentLog();
        $paymentLog->type = $this->processType;
        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
//        $paymentType = SubPaymentTypes::find()->where(['payment_type_id' => $workflow->payment_type])->asArray()->all();
//        foreach ($paymentType as $item) {
//            if ($item["Code"] == "SKL") {
//        $paymentLog->amount = $application->charges;
//                break;
//            }
//        }
        $paymentLog->process_id = $id;
        $paymentLog->status = 1;
        if ($this->request->isPost) {
            if ($paymentLog->load($this->request->post()) && Util::editPermission()) {
                $file = UploadedFile::getInstance($paymentLog, 'file');
                if (isset($file)) {
                    $fileName = $application->id . $this->processType . '-payment.' . $file->extension;
                    $file->saveAs(Constant::$FILE_UPLOAD_PATH . 'payment/' . $fileName);

                    $paymentLog->file = $fileName;

                }
                if ($paymentLog->save()) {
                    if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
                        CommonService::addApprovalLog($this->processType, "Paid", "Marked as Paid, Payment Approved", $application->id);
                        CommonService::markAsPaid($application, $this->processType);

                    } else {
                        CommonService::addApprovalLog($this->processType, "Paid", "Marked as Paid", $application->id);
                        $application->status = 100;
                    }
                    $application->save();
                }

                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('../payment/create', [
            'model' => $paymentLog,

        ]);

    }

    public function actionUpdateTnc($id)
    {
        $model = $this->findModel($id);
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                if ($model->save(false)) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }
        return $this->render('tncupdate', [
            'model' => $model,
        ]);
    }


    public function actionSubmit($application)
    {
        $model = $this->findModel($application);
        $approvalFlow = CommonService::getApprovalProcess($model, $this->processType, $application, false);

        if ($this->request->isPost && Util::editPermission()) {
            $model = CommonService::markApprovalStage($approvalFlow, $model, $application, $this->processType);

            if ($model->save()) {
                return $this->redirect([
                    'view',
                    'id' => $model->id
                ]);
            }
        }
        return $this->redirect(['view', 'id' => $application]);
    }
}