<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Applicationexportchank;
use backend\models\ApplicationexportchankSearch;
use backend\models\ExportChankConsignment;
use backend\models\ExportCompany;
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

class ApplicationexportchankController extends Controller
{
    public $processType = "ExportChank";

    /**
     * Lists all Applicationexportchank models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity->type == Constant::EXPORT_COMPANY && Util::editPermission()) {
            if (($model = ExportCompany::findOne(['id' => Yii::$app->user->identity->profile_id])) !== null) {
                CommonService::validateLicenseType($model, $this->processType);
            }
        }
        $searchModel = new ApplicationexportchankSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Applicationexportchank model.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $consignments = ExportChankConsignment::find()->where(['application_id' => $model->id])->all();

        if (empty($consignments)) {
            // If no records exist, initialize with a new model
            $consignments = [new ExportChankConsignment()];
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
     * Creates a new Applicationexportchank model.
     * If creation is successful, the browser will be redirected to the 'report' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Applicationexportchank();
        $model->company = UserTypeUtil::hasType(Constant::EXPORT_COMPANY) ? Yii::$app->user->identity->profile_id : "";
        $consignments = [new ExportChankConsignment()];
        if ($model->load(Yii::$app->request->post())) {
            $company = ExportCompany::findOne($model->company);
            $model->full_name = $company->company_name;
            $model->address = $company->address;
//            $model->business_reg_number=$company->br;
            // Handle export countries
            if (isset(Yii::$app->request->post('Applicationexportchank')['export_countries'])) {
                $exportCountries = Yii::$app->request->post('Applicationexportchank')['export_countries'];
                $model->export_countries = implode(',', $exportCountries);
            } else {
                $model->export_countries = null;
            }

            $model->approval_stage = "17";
            $model->status = 1;

            if ($model->save()) {
                $post = Yii::$app->request->post('ExportChankConsignment');
                $species = $post['commetial_name'] ?? [];

                foreach ($species as $index => $value) {
                    if (!empty($value)) { // Only save rows with at least species filled
                        $newModel = new ExportChankConsignment();
                        $newModel->application_id = $model->id;
                        $newModel->commetial_name = $value;
                        $newModel->size = $post['size'][$index] ?? null;
                        $newModel->total_weight = $post['total_weight'][$index] ?? null;
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
     * Updates an existing Applicationexportchank model.
     * If update is successful, the browser will be redirected to the 'report' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $consignments = ExportChankConsignment::find()->where(['application_id' => $model->id])->all();

        if (empty($consignments)) {
            // If no records exist, initialize with a new model
            $consignments = [new ExportChankConsignment()];
        }
        if ($model->approval_stage == "Completed" || $model->status == 99) {
            throw new MethodNotAllowedHttpException('This application is already approved. Editing is not allowed.');
        }

        if ($model->load(Yii::$app->request->post())) {
            // Handle export countries
            if (isset(Yii::$app->request->post('Applicationexportchank')['export_countries'])) {
                $exportCountries = Yii::$app->request->post('Applicationexportchank')['export_countries'];
                $model->export_countries = implode(',', $exportCountries);
            } else {
                $model->export_countries = null;
            }

            if ($model->save()) {
                $post = Yii::$app->request->post('ExportChankConsignment');
                $species = $post['commetial_name'] ?? [];
                $ids = $post['id'] ?? [];

                // Delete all existing records for this application_id
                ExportChankConsignment::deleteAll(['application_id' => $model->id]);

                // Save new records
                foreach ($species as $index => $value) {
                    if (!empty($value)) { // Only save rows with at least species filled
                        $newModel = new ExportChankConsignment();
                        $newModel->id = isset($ids[$index]) ? $ids[$index] : null; // Preserve ID if updating
                        $newModel->application_id = $model->id;
                        $newModel->commetial_name = $value;
                        $newModel->size = $post['size'][$index] ?? null;
                        $newModel->total_weight = $post['total_weight'][$index] ?? null;
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
     * Deletes an existing Applicationexportchank model.
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
     * Finds the Applicationexportchank model based on its primary key value.
     * @param int $id ID
     * @return Applicationexportchank the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Applicationexportchank::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Displays the report for a single Applicationexportchank model.
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
        $consignments = ExportChankConsignment::find()->where(['application_id' => $id])->all();
        if (empty($consignments)) {
            // If no records exist, initialize with a new model
            $consignments = [new ExportChankConsignment()];
        }
        return $this->render('licenseView', ['model' => $model, 'consignments' => $consignments]);

    }

   public function actionLicenseDownload($id): string
{
    // CommonService::validatePermission(
    //     $this,
    //     'ExportChankController-license-download'
    // );

    $model = $this->findModel($id);

    $consignments = ExportChankConsignment::find()
        ->where([
            'application_id' => $id,
        ])
        ->all();

    /*
     * isPdf=true makes the view use local file:/// paths instead
     * of authenticated /files/... browser URLs.
     */
    $content = $this->renderPartial(
        'license',
        [
            'model' => $model,
            'consignments' => $consignments,
            'isPdf' => true,
        ]
    );

    $pdf = new Pdf([
        'mode' => Pdf::MODE_CORE,
        'defaultFontSize' => 10,
        'format' => Pdf::FORMAT_A4,
        'orientation' => Pdf::ORIENT_PORTRAIT,
        'destination' => Pdf::DEST_BROWSER,
        'content' => $content,
        'filename' => 'EXPORT-CHANK-'
            . (string) $model->id
            . '.pdf',
        'marginLeft' => 10,
        'marginTop' => 10,
        'marginRight' => 10,
        'marginBottom' => 10,
    ]);

    $mpdf = $pdf->getApi();

    /*
     * Set to true temporarily when diagnosing image failures.
     */
    $mpdf->showImageErrors = false;

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