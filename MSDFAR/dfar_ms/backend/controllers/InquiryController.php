<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\Files;
use backend\models\Inquiry;
use backend\models\InquirySearch;
use backend\models\MFiDistrict;
use Exception;
use Yii;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * InquiryController implements the CRUD actions for Inquiry model.
 */
class InquiryController extends Controller
{
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
     * Lists all Inquiry models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new InquirySearch();

        // Fetch counts based on Inquiry_Status while excluding deleted inquiries (status = 0)
        $counts = [
            'countOpen' => Inquiry::find()
                ->where(['Inquiry_Status' => 'Open'])
                ->andWhere(['!=', 'status', 0])  // Exclude deleted inquiries
                ->count(),
            'countInProgress' => Inquiry::find()
                ->where(['Inquiry_Status' => 'In Progress'])
                ->andWhere(['!=', 'status', 0])  // Exclude deleted inquiries
                ->count(),
            'countCompleted' => Inquiry::find()
                ->where(['Inquiry_Status' => 'Completed'])
                ->andWhere(['!=', 'status', 0])  // Exclude deleted inquiries
                ->count(),
            // Add count for completed inquiries from the 1st of current month
            'countCompletedLastMonth' => Inquiry::find()
                ->where(['Inquiry_Status' => 'Completed'])
                ->andWhere(['>=', 'completion_date', new Expression('DATE_FORMAT(CURDATE(), "%Y-%m-01")')])
                ->andWhere(['!=', 'status', 0])  // Exclude deleted inquiries
                ->count(),
        ];

        // Modify the search query to exclude inquiries marked as "deleted" (status = 0)
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query->andWhere(['!=', 'status', 0]);  // Exclude deleted inquiries in the data provider

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'counts' => $counts,  // Pass the counts to the view
        ]);
    }



    /**
     * Displays a single Inquiry model.
     * @param int $Inquiry_ID Inquiry ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        // Use the findModel method to find the inquiry model by ID
        $model = $this->findModel($id);

        // If the model is null, throw a NotFoundHttpException
        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        // Pass the model to the view
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Inquiry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */


    public function actionCreate()
    {
        $model = new Inquiry();
        $fileModel = new Files();

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Set automatic fields
                $model->ip_address = Yii::$app->request->userIP;
                $model->Inquiry_Status = 'Open';
                $model->Submission_Date = date('Y-m-d H:i:s');
                $model->status = 1; // Set active status

                if ($model->validate()) {
                    // Handle file upload
                    $model->uploadedFile = UploadedFile::getInstance($model, 'uploadedFile');
                    $hasFile = false;
                    $fileName = null;

                    if ($model->save(false)) {
                        if ($model->uploadedFile !== null) {
                            // Create upload directory if it doesn't exist
                            $uploadPath = Yii::getAlias('@backend/web/uploads/files/inquiry/');
                            if (!is_dir($uploadPath)) {
                                FileHelper::createDirectory($uploadPath, 0777, true);
                            }

                            // Generate unique filename
                            $fileName = 'INQUIRY_' . $model->Inquiry_ID . '_' . time() . '.' . $model->uploadedFile->extension;

                            // Save file
                            if ($model->uploadedFile->saveAs($uploadPath . $fileName)) {
                                // Save file record
                                $fileModel->type = 'INQUIRY';
                                $fileModel->file_type = 1;
                                $fileModel->file_name = $fileName;
                                $fileModel->process_id = $model->Inquiry_ID;
                                $fileModel->status = 1;

                                if (!$fileModel->save()) {
                                    throw new Exception('Failed to save file record');
                                }
                                $hasFile = true;
                            } else {
                                throw new Exception('Failed to save uploaded file');
                            }
                        }

                        // Prepare email body with optional file information
                        $emailBody = '<p>Dear ' . Html::encode($model->Name) . ',</p>'
                            . '<p>Thank you for your inquiry. Here are the details:</p>'
                            . '<p><strong>Inquiry Type:</strong> ' . Html::encode($model->Inquiry_Type) . '</p>'
                            . '<p><strong>Status:</strong> ' . Html::encode($model->Inquiry_Status) . '</p>'
                            . '<p><strong>Description:</strong><br>' . nl2br(Html::encode($model->Description)) . '</p>';

                        // Add file information if a file was uploaded
                        // if ($hasFile) {
                        //     $emailBody .= '<p><strong>Attached Document:</strong> ' . Html::encode($fileName) . '</p>';
                        // }

                        $emailBody .= '<p>We will get back to you soon.</p>'
                            . '<p>Best regards,</p>'
                            . '<p>Information Technology Division </p>'
                            . '<p>Department of Fisheries and Aquatic Resources</p>';

                        // Send confirmation email
                        if (
                            !Yii::$app->mailer->compose()
                                ->setFrom('IT@fisheriesdept.gov.lk')
                                ->setTo($model->Email)
                                ->setSubject('Submit Inquiry: ' . $model->Inquiry_Type)
                                ->setHtmlBody($emailBody)
                                ->send()
                        ) {
                            throw new Exception('Failed to send confirmation email');
                        }

                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 'Inquiry submitted successfully and confirmation email sent.');
                    }
                } else {
                    throw new Exception('Validation failed: ' . print_r($model->errors, true));
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Inquiry model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $Inquiry_ID Inquiry ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($Inquiry_ID)
{
    $model = $this->findModel($Inquiry_ID);

    // Store old status before update
    $oldStatus = $model->Inquiry_Status;

    if ($model->load(Yii::$app->request->post())) {

        // Set completion date if completed
        if ($model->Inquiry_Status === 'Completed') {
            $model->completion_date = date('Y-m-d H:i:s');
        }

        if ($model->save()) {

            // Send email only if status changed
            if ($oldStatus != $model->Inquiry_Status) {

                $sent = Yii::$app->mailer->compose()
                    ->setFrom(['elog.dfar@gmail.com' => 'DFAR IT Division'])
                    ->setTo($model->Email)
                    ->setSubject('Inquiry Status Updated: ' . $model->Inquiry_Type)
                    ->setHtmlBody(
                        '<p>Dear ' . Html::encode($model->Name) . ',</p>' .
                        '<p>Your inquiry status has been updated.</p>' .

                        '<p><strong>Inquiry Type:</strong> ' . Html::encode($model->Inquiry_Type) . '</p>' .
                        '<p><strong>Status:</strong> ' . Html::encode($model->Inquiry_Status) . '</p>' .
                        '<p><strong>Description:</strong><br>' . nl2br(Html::encode($model->Description)) . '</p>' .
                        '<p><strong>Remarks:</strong><br>' . nl2br(Html::encode($model->remarks)) . '</p>' .

                        '<p>Best regards,</p>' .
                        '<p>Information Technology Division</p>' .
                        '<p>Department of Fisheries and Aquatic Resources</p>'
                    )
                    ->send();

                if ($sent) {
                    Yii::$app->session->setFlash('success', 'Inquiry updated and email sent.');
                } else {
                    Yii::$app->session->setFlash('error', 'Inquiry updated but email failed.');
                }

            } else {
                Yii::$app->session->setFlash('success', 'Inquiry updated.');
            }

            return $this->redirect(['view', 'id' => $model->Inquiry_ID]);
        }
    }

    return $this->render('update', [
        'model' => $model,
    ]);
}
    /**
     * Marks an existing Inquiry model as deleted by updating its Status to 0.
     * If the status update is successful, the browser will be redirected to the 'index' page.
     * @param int $Inquiry_ID Inquiry ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($Inquiry_ID)
    {
        $model = $this->findModel($Inquiry_ID);

        // Set 'status' to 0 for soft delete
        $model->status = 0;

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'The inquiry has been marked as deleted.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to mark the inquiry as deleted.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Inquiry model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $Inquiry_ID Inquiry ID
     * @return Inquiry the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($Inquiry_ID)
    {
        if (($model = Inquiry::findOne(['Inquiry_ID' => $Inquiry_ID])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Get statistics (Stacs) for Inquiry model based on user type and approval stage.
     * @return array
     */
    public static function getStacs()
    {
        $wherePending = [];
        $whereActive = [];

        // Determine the query conditions based on user type
        if (UserTypeUtil::hasType(Constant::ITU)) {
            $wherePending = ['status' => 1];  // Pending status for ITU
            $whereActive = ["office" => Yii::$app->session->get("officer_division")];
        } elseif (UserTypeUtil::hasType(Constant::AD)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::AD];
            $whereActive = ["district" => Yii::$app->session->get("officer_district")];
        } elseif (UserTypeUtil::hasType(Constant::DFI)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DFI];
            $whereActive = ["district" => Yii::$app->session->get("officer_district")];
        } elseif (UserTypeUtil::hasType(Constant::DO)) {
            $wherePending = ["district" => Yii::$app->session->get("officer_district"), 'approval_stage' => Constant::DO];
            $whereActive = ["district" => Yii::$app->session->get("officer_district")];
        }

        // Count inquiries based on their status and conditions
        $countOpen = Inquiry::find()
            ->where(['Inquiry_Status' => 'Open'])
            ->andWhere(['!=', 'status', 0])  // Exclude deleted inquiries
            ->count();
        $countInProgress = Inquiry::find()
            ->where(['Inquiry_Status' => 'In Progress'])
            ->andWhere(['!=', 'status', 0])  // Exclude deleted inquiries
            ->count();
        $countCompleted = Inquiry::find()
            ->where(['Inquiry_Status' => 'Completed'])
            ->andWhere(['!=', 'status', 0])  // Exclude deleted inquiries
            ->count();

        $countCompletedLastMonth = Inquiry::find()
            ->where(['Inquiry_Status' => 'Completed'])
            ->andWhere(['>=', 'completion_date', new Expression('DATE_FORMAT(CURDATE(), "%Y-%m-01")')])
            ->andWhere(['!=', 'status', 0])  // Exclude deleted inquiries
            ->count();



        return [
            "countOpen" => $countOpen,
            "countInProgress" => $countInProgress,
            "countCompleted" => $countCompleted,
            "countCompletedLastMonth" => $countCompletedLastMonth,
        ];
    }


    public function actionDashboard()
    {
        if (Yii::$app->request->isAjax) {
            // Fetch the correct counts of open, in-progress, and completed inquiries
            $countOpen = Inquiry::find()->where(['Inquiry_Status' => 'Open'])->andWhere(['!=', 'status', 0])->count();
            $countInProgress = Inquiry::find()->where(['Inquiry_Status' => 'In Progress'])->andWhere(['!=', 'status', 0])->count();
            $countCompleted = Inquiry::find()->where(['Inquiry_Status' => 'Completed'])->andWhere(['!=', 'status', 0])->count();
            $countTotal = Inquiry::find()->andWhere(['!=', 'status', 0])->count(); // Total of all inquiries
            $countCompletedLastMonth = Inquiry::find()
                ->where(['Inquiry_Status' => 'Completed'])
                ->andWhere(['>=', 'completion_date', new Expression('DATE_FORMAT(CURDATE(), "%Y-%m-01")')])
                ->andWhere(['!=', 'status', 0])  // Exclude deleted inquiries
                ->count();

            // Return counts as JSON response for AJAX request
            return $this->asJson([
                'countOpen' => $countOpen,
                'countInProgress' => $countInProgress,
                'countCompleted' => $countCompleted,
                'countTotal' => $countTotal,
                'countCompletedLastMonth' => $countCompletedLastMonth,
            ]);
        }

        // If not an AJAX request, return the full page render
        $searchModel = new InquirySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['!=', 'status', 0]);

        // Calculate counts
        $countOpen = Inquiry::find()->where(['Inquiry_Status' => 'Open'])->andWhere(['!=', 'status', 0])->count();
        $countInProgress = Inquiry::find()->where(['Inquiry_Status' => 'In Progress'])->andWhere(['!=', 'status', 0])->count();
        $countCompleted = Inquiry::find()->where(['Inquiry_Status' => 'Completed'])->andWhere(['!=', 'status', 0])->count();
        $countTotal = Inquiry::find()->andWhere(['!=', 'status', 0])->count(); // Total of all inquiries
        $countCompletedLastMonth = Inquiry::find()
            ->where(['Inquiry_Status' => 'Completed'])
            ->andWhere(['>=', 'completion_date', new Expression('DATE_FORMAT(CURDATE(), "%Y-%m-01")')])
            ->andWhere(['!=', 'status', 0])  // Exclude deleted inquiries
            ->count();

        $counts = [
            'countOpen' => $countOpen,
            'countInProgress' => $countInProgress,
            'countCompleted' => $countCompleted,
            'countTotal' => $countTotal,
            'countCompletedLastMonth' => $countCompletedLastMonth,
        ];

        return $this->render('dashboard', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'counts' => $counts,
        ]);
    }

    /**
     * Downloads an inquiry attachment
     * @param int $id Inquiry ID
     * @return Response
     * @throws NotFoundHttpException if file not found
     */
    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        $file = Files::findOne(['process_id' => $id, 'type' => 'INQUIRY']);

        if (!$file) {
            throw new NotFoundHttpException('File not found.');
        }

        $filePath = Yii::getAlias('@backend/web/uploads/files/inquiry/') . $file->file_name;

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('File not found on server.');
        }

        return Yii::$app->response->sendFile($filePath, $file->file_name);
    }

    /**
     * Analytics action for Inquiry model.
     * Displays key metrics, monthly trends, and category distribution.
     * @return string
     */
    public function actionAnalytics()
    {
        // Get metrics data
        $metrics = [
            'resolutionRate' => $this->calculateResolutionRate(),
            'avgResponseTime' => $this->calculateAverageResponseTime(),
            'customerSatisfaction' => 4.6,
            'firstContactResolution' => 72
        ];

        // Get monthly trends data
        $monthlyData = $this->getMonthlyTrendsData();

        // Get category distribution data
        $categoryData = $this->getCategoryDistributionData();

        // Add this line
        $districtData = $this->getDistrictOfficeData();

        return $this->render('analytics', [
            'metrics' => $metrics,
            'monthlyData' => $monthlyData,
            'categoryData' => $categoryData,
            'districtData' => $districtData, // Add this line
        ]);
    }

    private function calculateResolutionRate()
    {
        $total = Inquiry::find()->count();
        $resolved = Inquiry::find()->where(['Inquiry_Status' => 'Completed'])->count();
        return $total > 0 ? round(($resolved / $total) * 100, 1) : 0;
    }

    private function calculateAverageResponseTime()
    {
        // Implement response time calculation
        return 1.7; // Placeholder
    }

    private function getMonthlyTrendsData()
    {
        $data = Inquiry::find()
            ->select(['MONTH(Submission_Date) as month', 'COUNT(*) as count'])
            ->groupBy('month')
            ->orderBy('month')
            ->asArray()
            ->all();

        return [
            'categories' => array_column($data, 'month'),
            'inquiries' => array_column($data, 'count'),
            'resolved' => [] // Add resolved data calculation
        ];
    }

    private function getCategoryDistributionData()
    {
        $data = Inquiry::find()
            ->select(['Inquiry_Type', 'COUNT(*) as count'])
            ->groupBy('Inquiry_Type')
            ->asArray()
            ->all();

        return array_map(function ($item) {
            return [
                'name' => $item['Inquiry_Type'],
                'y' => (int) $item['count']
            ];
        }, $data);
    }

    private function getDistrictOfficeData()
    {
        $data = Inquiry::find()
            ->select(['District as name', 'COUNT(*) as y'])
            ->where(['!=', 'District', 0]) // Exclude District 0
            ->groupBy('District')
            ->orderBy(['COUNT(*)' => SORT_DESC])
            ->limit(5)  // Get only top 5
            ->asArray()
            ->all();

        return array_map(function ($item) {
            $districtName = null;
            if ($item['name']) {
                $district = MFiDistrict::findOne($item['name']);
                $districtName = $district ? $district->name : $item['name'];
            }
            return [
                'name' => $districtName,
                'y' => (int) $item['y'],
            ];
        }, $data);
    }
}
