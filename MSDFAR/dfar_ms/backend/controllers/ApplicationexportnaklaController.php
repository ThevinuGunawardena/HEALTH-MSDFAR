<?php
namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Applicationexportnakla;
use backend\models\ApplicationexportnaklaSearch;
use backend\models\ExportCompany;
use backend\models\Files;
use backend\models\MApprovalWorkflow;
use backend\models\MRequeredDocuments;
use backend\models\PaymentLog;
use backend\services\CommonService;
use backend\services\Util;
use kartik\mpdf\Pdf;
use Yii;
use backend\components\Controller;

use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ApplicationexportnaklaController extends Controller
{
    public $processType = "ExportNakla";
    /**
     * Lists all Applicationexportnakla models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity->type == Constant::EXPORT_COMPANY && Util::editPermission()) {
            if (($model = ExportCompany::findOne(['id' => Yii::$app->user->identity->profile_id])) !== null) {
                CommonService::validateLicenseType($model, $this->processType);
            }
        }
        $searchModel = new ApplicationexportnaklaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Applicationexportnakla model.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $validated = false;
        $workflow = MApprovalWorkflow::find()->where(["type" => $this->processType])->one();
        $files = Files::find()->where(["type" => $workflow->id, "process_id" => $id])->andWhere(["!=", "file_type", -999])->count();
        $requred_documents = MRequeredDocuments::find()->where(['type' => $workflow->id, "status" => 1])->count();
        $model->validate();
//        print_r($model->getErrors());exit();
        if ($model->validate() && $files >= $requred_documents) {
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
     * Creates a new Applicationexportnakla model.
     * If creation is successful, the browser will be redirected to the 'report' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Applicationexportnakla();
        $model->company = UserTypeUtil::hasType(Constant::EXPORT_COMPANY) ? Yii::$app->user->identity->profile_id : "";

        if ($model->load(Yii::$app->request->post())) {
            // Handle export countries
            if ($model->export_countries) {
                $model->export_countries = implode(',', $model->export_countries);
            } else {
                $model->export_countries = '';
            }
            $company = ExportCompany::findOne($model->company);
            $model->full_name = $company->company_name;
            $model->permanent_address = $company->address;
            $model->business_reg_number = $company->br;

            $model->approval_stage = "17";
            $model->status = 1;
            $model->company = Yii::$app->user->identity->profile_id;

            $model->fax_number = 0;
            $model->previouspermit_exported_quantity_pieces = 0;
            $model->export_quantity_pieces = 0;
            $model->export_quantity_pieces = 0;

            if ($model->save()) {
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
        ]);
    }

    /**
     * Updates an existing Applicationexportnakla model.
     * If update is successful, the browser will be redirected to the 'report' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->approval_stage == "Completed") {
            throw new MethodNotAllowedHttpException('This application is already approved. Editing is not allowed.');
        }
        if ($model->load(Yii::$app->request->post())) {
            // Handle export countries
            if ($model->export_countries) {
                $model->export_countries = implode(',', $model->export_countries);
            } else {
                $model->export_countries = '';
            }

            // Handle file upload
            $uploadedFile = UploadedFile::getInstance($model, 'supporting_document');
            if ($uploadedFile) {
                $uploadDir = Constant::$FILE_UPLOAD_PATH . 'application/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filePath = $uploadDir . '/' . $uploadedFile->baseName . '.' . $uploadedFile->extension;
                if ($uploadedFile->saveAs($filePath)) {
                    $model->supporting_document = 'uploads/' . $uploadedFile->baseName . '.' . $uploadedFile->extension;
                }
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::error('Model save failed: ' . json_encode($model->errors), __METHOD__);
                Yii::$app->session->setFlash('error', 'Failed to save the model: ' . json_encode($model->errors));
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Applicationexportnakla model.
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
     * Finds the Applicationexportnakla model based on its primary key value.
     * @param int $id ID
     * @return Applicationexportnakla the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Applicationexportnakla::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Displays the report for a single Applicationexportnakla model.
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

        return $this->render('licenseView', ['model' => $model]);

    }

    public function actionLicenseDownload($id): string
    {
//        CommonService::validatePermission($this, "BoatNumbersController-license-download");

        $model = $this->findModel($id);
        $content = $this->renderPartial('license', ['model' => $model]);

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
}