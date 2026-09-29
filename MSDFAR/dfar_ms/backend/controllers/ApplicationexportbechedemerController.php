<?php
namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Applicationexportbechedemer;
use backend\models\ApplicationexportbechedemerSearch;
use backend\models\ExportBecheDemerConsignment;
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

class ApplicationexportbechedemerController extends Controller
{
    public $processType = "ExportBedchamber";
    /**
     * Lists all Applicationexportbechedemer models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity->type == Constant::EXPORT_COMPANY && Util::editPermission()) {
            if (($model = ExportCompany::findOne(['id' => Yii::$app->user->identity->profile_id])) !== null) {
                CommonService::validateLicenseType($model, $this->processType);
            }
        }
        $searchModel = new ApplicationexportbechedemerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Applicationexportbechedemer model.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $consignments = ExportBecheDemerConsignment::find()->where(['application_id' => $model->id])->all();

        if (empty($consignments)) {
            // If no records exist, initialize with a new model
            $consignments = [new ExportBecheDemerConsignment()];
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
     * Creates a new Applicationexportbechedemer model.
     * If creation is successful, the browser will be redirected to the 'report' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Applicationexportbechedemer();
        $model->company = UserTypeUtil::hasType(Constant::EXPORT_COMPANY) ? Yii::$app->user->identity->profile_id : "";
        $consignments = [new ExportBecheDemerConsignment()];
        if ($model->load(Yii::$app->request->post())) {
            $company = ExportCompany::findOne($model->company);
            $model->full_name = $company->company_name;
//            $model->address = $company->address;
//            $model->business_reg_number = $company->br;
            // Handle export countries
            if (isset(Yii::$app->request->post('Applicationexportbechedemer')['export_countries'])) {
                $exportCountries = Yii::$app->request->post('Applicationexportbechedemer')['export_countries'];
                $model->export_countries = implode(',', $exportCountries);
            } else {
                $model->export_countries = '';
            }

            $model->approval_stage = "17";
            $model->status = 1;
            $model->fax_number = 0;

            if ($model->save()) {
                $post = Yii::$app->request->post('ExportBecheDemerConsignment');
                $species = $post['commetial_name'] ?? [];

                foreach ($species as $index => $value) {
                    if (!empty($value)) { // Only save rows with at least species filled
                        $newModel = new ExportBecheDemerConsignment();
                        $newModel->application_id = $model->id;
                        $newModel->commetial_name = $value;
                        $newModel->quantity_per_unit = $post['quantity_per_unit'][$index] ?? null;
                        $newModel->total_weight = $post['total_weight'][$index] ?? null;
                        $newModel->total_number = $post['total_number'][$index] ?? null;
                        $newModel->supply_area = $post['supply_area'][$index] ?? null;
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
     * Updates an existing Applicationexportbechedemer model.
     * If update is successful, the browser will be redirected to the 'report' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $consignments = ExportBecheDemerConsignment::find()->where(['application_id' => $model->id])->all();

        if (empty($consignments)) {
            // If no records exist, initialize with a new model
            $consignments = [new ExportBecheDemerConsignment()];
        }
        if ($model->approval_stage == "Completed" || $model->status == 99) {
            throw new MethodNotAllowedHttpException('This application is already approved. Editing is not allowed.');
        }
        $model->export_countries = explode(',', $model->export_countries);//converting to array...
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            // Handle export countries
            if (isset(Yii::$app->request->post('Applicationexportbechedemer')['export_countries'])) {
                $exportCountries = Yii::$app->request->post('Applicationexportbechedemer')['export_countries'];
                $model->export_countries = implode(',', $exportCountries);
            } else {
                $model->export_countries = '';
            }


            if ($model->save()) {
                $post = Yii::$app->request->post('ExportBecheDemerConsignment');
                $species = $post['commetial_name'] ?? [];
                $ids = $post['id'] ?? [];

                // Delete all existing records for this application_id
                ExportBecheDemerConsignment::deleteAll(['application_id' => $model->id]);

                // Save new records
                foreach ($species as $index => $value) {
                    if (!empty($value)) { // Only save rows with at least species filled
                        $newModel = new ExportBecheDemerConsignment();
                        $newModel->id = isset($ids[$index]) ? $ids[$index] : null; // Preserve ID if updating
                        $newModel->application_id = $model->id;
                        $newModel->commetial_name = $value;
                        $newModel->quantity_per_unit = $post['quantity_per_unit'][$index] ?? null;
                        $newModel->total_weight = $post['total_weight'][$index] ?? null;
                        $newModel->total_number = $post['total_number'][$index] ?? null;
                        $newModel->supply_area = $post['supply_area'][$index] ?? null;


                        if (!$newModel->save()) {
                            throw new Exception('Failed to save row ' . ($index + 1));
                        }
                    }
                }
                $transaction->commit();

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
     * Deletes an existing Applicationexportbechedemer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
//        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Applicationexportbechedemer model based on its primary key value.
     * @param int $id ID
     * @return Applicationexportbechedemer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Applicationexportbechedemer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Displays the report for a single Applicationexportbechedemer model.
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
    /*
     * Keep or replace this permission key with the key
     * used by this controller.
     */
    // CommonService::validatePermission(
    //     $this,
    //     'ExportBecheDemerController-license-view'
    // );

    $model = Applicationexportbechedemer::findOne($id);

    if ($model === null) {
        throw new NotFoundHttpException(
            'Export Beche-de-mer application was not found.'
        );
    }

    $consignments = ExportBecheDemerConsignment::find()
        ->where([
            'application_id' => $model->id,
        ])
        ->all();

    $officer = $this->getExportLicenseApprovedOfficer(
        $model
    );

    return $this->render(
        'licenseView',
        [
            'model' => $model,
            'consignments' => $consignments,
            'officer' => $officer,
            'imageSrc' =>
                $this->createExportLicenseImageResolver(
                    false
                ),
        ]
    );
}

public function actionLicenseDownload($id): string
{
    /*
     * Keep or replace this permission key with the key
     * used by this controller.
     */
    // CommonService::validatePermission(
    //     $this,
    //     'ExportBecheDemerController-license-download'
    // );

    $model = Applicationexportbechedemer::findOne($id);

    if ($model === null) {
        throw new NotFoundHttpException(
            'Export Beche-de-mer application was not found.'
        );
    }

    $consignments = ExportBecheDemerConsignment::find()
        ->where([
            'application_id' => $model->id,
        ])
        ->all();

    /*
     * An empty array is sufficient. The view will display
     * the no-records message instead of a blank row.
     */
    $officer = $this->getExportLicenseApprovedOfficer(
        $model
    );

    $content = $this->renderPartial(
        'license',
        [
            'model' => $model,
            'consignments' => $consignments,
            'isPdf' => true,
            'pdf' => true,
            'officer' => $officer,
            'imageSrc' =>
                $this->createExportLicenseImageResolver(
                    true
                ),
        ]
    );

    $safeId = preg_replace(
        '/[^A-Za-z0-9_-]+/',
        '_',
        (string) $model->id
    );

    $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8,
        'format' => Pdf::FORMAT_A4,
        'orientation' => Pdf::ORIENT_PORTRAIT,
        'destination' => Pdf::DEST_BROWSER,
        'filename' =>
            'Beche_Demer_Export_Licence_'
            . $safeId
            . '.pdf',
        'content' => $content,
        'marginLeft' => 10,
        'marginTop' => 10,
        'marginRight' => 10,
        'marginBottom' => 10,
    ]);

    $mpdf = $pdf->getApi();

    /*
     * Change this to true temporarily only when debugging
     * a missing or unsupported image.
     */
    $mpdf->showImageErrors = false;

    return $pdf->render();
}

/**
 * @return array<string, mixed>
 */
private function getExportLicenseApprovedOfficer(
    Applicationexportbechedemer $model
): array {
    $officer = CommonService::getApprovedOfficer(
        $model->id,
        'ExportBedchamber'
    );

    if ($officer instanceof \yii\db\ActiveRecord) {
        $officer = $officer->toArray();
    }

    return is_array($officer)
        ? $officer
        : [];
}

private function createExportLicenseImageResolver(
    bool $isPdf
): \Closure {
    return static function (
        string $relativePath
    ) use ($isPdf): string {
        $relativePath = trim(
            str_replace('\\', '/', $relativePath),
            '/'
        );

        if (
            $relativePath === ''
            || strpos($relativePath, '..') !== false
            || strpos($relativePath, "\0") !== false
        ) {
            return '';
        }

        /*
         * Browser view uses the authenticated /files route.
         */
        if (!$isPdf) {
            $encodedPath = implode(
                '/',
                array_map(
                    'rawurlencode',
                    explode('/', $relativePath)
                )
            );

            return rtrim(
                Constant::$FILE_VIEW_PATH,
                '/'
            ) . '/' . $encodedPath;
        }

        /*
         * PDF rendering reads the image directly from disk.
         */
        $uploadRoot = realpath(
            '/var/mountpoint/uploads'
        );

        if ($uploadRoot === false) {
            Yii::error(
                'Upload root was not found: '
                . '/var/mountpoint/uploads',
                __METHOD__
            );

            return '';
        }

        $requestedPath = $uploadRoot
            . DIRECTORY_SEPARATOR
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $relativePath
            );

        $realPath = realpath($requestedPath);

        if (
            $realPath === false
            || !is_file($realPath)
            || !is_readable($realPath)
        ) {
            Yii::warning(
                'Export licence image is missing or unreadable: '
                . $requestedPath,
                __METHOD__
            );

            return '';
        }

        $allowedPrefix = $uploadRoot
            . DIRECTORY_SEPARATOR;

        if (strpos($realPath, $allowedPrefix) !== 0) {
            Yii::warning(
                'Blocked image outside the uploads directory: '
                . $realPath,
                __METHOD__
            );

            return '';
        }

        return 'file:///'
            . ltrim(
                str_replace('\\', '/', $realPath),
                '/'
            );
    };
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
        $paymentLog->amount = $application->charges;
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
                return $this->redirect(['view',
                    'id' => $model->id]);
            }
        }
        return $this->redirect(['view', 'id' => $application]);
    }
}