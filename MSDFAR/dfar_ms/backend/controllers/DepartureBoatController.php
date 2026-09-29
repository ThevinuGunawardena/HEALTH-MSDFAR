<?php

namespace backend\controllers;

use backend\components\RecordLookupRateLimit;
use backend\components\SecurityHelper;
use backend\models\DepatureBoatPayment;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\DepartureBoatActivity;
use backend\models\DepartureBoats;
use backend\models\DepartureBoatsSearch;
use backend\models\Files;
use backend\models\BluetrakerReports;
use backend\models\MApprovalWorkflow;
use backend\models\ProfileFisherman;
use backend\services\CommonService;
use backend\services\Util;
use Yii;
use yii\helpers\FileHelper;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;
use yii\web\ForbiddenHttpException;
/**
 * DepartureBoatController implements the CRUD actions for DepartureBoats model.
 */
class DepartureBoatController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'except' => [],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $existingVerbActions =
            $behaviors['verbs']['actions'] ?? [];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => array_merge(
                $existingVerbActions,
                [
                    'index' => ['GET'],
                    'view' => ['GET'],
                    'update' => ['GET', 'POST'],
                    'update-owner' => ['GET', 'POST'],
                    'update-payment' => ['GET', 'POST'],
                    'add-boat-comment' => ['GET', 'POST'],
                    'update-vms-status' => ['POST'],
                    'delete' => ['POST'],
                ]
            ),
        ];

        /*
         * Direct record lookups and record mutations.
         * Every action below accepts a model-bound DepartureBoats token.
         */
        $protectedRecordActions = [
            'view',
            'update',
            'update-owner',
            'update-payment',
            'add-boat-comment',
            'update-vms-status',
            'delete',
        ];

        $behaviors['departureBoatRecordBurstLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $protectedRecordActions,
            'bucketName' => 'departure-boat-record-burst',
            'limit' => 30,
            'window' => 60,
        ];

        $behaviors['departureBoatRecordSustainedLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => $protectedRecordActions,
            'bucketName' => 'departure-boat-record-sustained',
            'limit' => 200,
            'window' => 900,
        ];

        /*
         * Higher interactive limit for the list/search page.
         */
        $behaviors['departureBoatPageBurstLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => ['index'],
            'bucketName' => 'departure-boat-page-burst',
            'limit' => 120,
            'window' => 60,
        ];

        $behaviors['departureBoatPageSustainedLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => ['index'],
            'bucketName' => 'departure-boat-page-sustained',
            'limit' => 600,
            'window' => 900,
        ];

        return $behaviors;
    }


    /**
     * Lists all DepartureBoats models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DepartureBoatsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DepartureBoats model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(string $token): string
    {
        $model = $this->findModelByToken($token);
        $boatId = (int) $model->id;

        $activityQuery = DepartureBoatActivity::find()
            ->where([
                'boat_no' => $model->boat->boat_number,
            ])
            ->orderBy([
                'date_time' => SORT_DESC,
            ]);

        $activity = new ActiveDataProvider([
            'query' => $activityQuery,
        ]);

        $paymentQuery = DepatureBoatPayment::find()
            ->where([
                'boat_reg_id' => $boatId,
            ])
            ->orderBy([
                'to_date' => SORT_DESC,
            ]);

        $payments = new ActiveDataProvider([
            'query' => $paymentQuery,
        ]);

        $previousUnpaidPaymentData =
            $this->getPreviousUnpaidMonths($boatId);

        $blueTrakerQuery = BluetrakerReports::find()
            ->alias('bt')
            ->where([
                'bt.VesselName' => $model->boat->boat_number,
            ])
            ->andWhere([
                '<>',
                'bt.Event',
                0,
            ])
            ->orderBy([
                'bt.id' => SORT_DESC,
            ])
            ->limit(10);

        $blueTrakerEvents = new ActiveDataProvider([
            'query' => $blueTrakerQuery,
            'pagination' => false,
        ]);

        return $this->render(
            'view',
            [
                'model' => $model,
                'activity' => $activity,
                'payments' => $payments,
                'blueTrakerEvents' => $blueTrakerEvents,
                'previousUnpaidPaymentData' =>
                    $previousUnpaidPaymentData,
                'token' => $token,
            ]
        );
    }

    /**
     * Finds the DepartureBoats model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return DepartureBoats the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): DepartureBoats
    {
        $model = DepartureBoats::findOne([
            'id' => (int) $id,
        ]);

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'The requested page does not exist.'
            )
        );
    }

    private function createDepartureBoatToken(
        DepartureBoats $model
    ): string {
        return SecurityHelper::encryptId(
            DepartureBoats::class,
            (int) $model->id
        );
    }

    protected function findModelByToken(
        string $token
    ): DepartureBoats {
        $id = SecurityHelper::decryptId(
            trim($token),
            DepartureBoats::class
        );

        return $this->findModel(
            (int) $id
        );
    }

    /**
     * Updates an existing DepartureBoats model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(string $token)
{
    CommonService::validatePermission(
        $this,
        'DepartureBoatController-update'
    );

    if (!Util::adminPermission()) {
        throw new UnauthorizedHttpException(
            Yii::t(
                'app',
                'You do not have permission to run this operation.'
            )
        );
    }

    if (!UserTypeUtil::hasType(Constant::HARBOUR_OFFICER)) {
        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'The requested page does not exist.'
            )
        );
    }

    /*
     * ==========================================================
     * LOAD MODEL USING SECURE TOKEN
     * ==========================================================
     */

    $model = $this->findModelByToken(
        $token
    );

    /*
     * Keep original database values before POST load.
     */
    $currentStatus =
        (string) ($model->status ?? '');

    $currentCompulsoryService =
        (int) $model->compulsory_service;

    $existingDepCancelDate =
        $model->dep_cancel_date;

    $existingToDate =
        $model->to_date;

    $existingCompulsoryServiceFromDate =
        $model->compulsory_service_from_date;

    $existingCompulsoryServiceToDate =
        $model->compulsory_service_to_date;

    /*
     * IMPORTANT:
     *
     * DO NOT reset all model attributes to NULL here.
     *
     * The previous code cleared values such as harbor,
     * approved_by, timestamp and other database fields.
     *
     * This could cause model validation/save() to fail.
     */

    if (
        $this->request->isPost
        && $model->load(
            $this->request->post()
        )
    ) {
        $updatedStatus =
            trim(
                (string) $model->status
            );

        /*
         * ======================================================
         * VMS ROLE RULES
         * ======================================================
         */

        if (
            Yii::$app->user->can(
                'DepartureBoatController-VMS'
            )
        ) {
            /*
             * Determine whether this boat was actually in
             * the pure "Compulsory Service Pending" state.
             *
             * Your View determines this using
             * compulsory_service = 0 rather than storing
             * "Compulsory Service Pending" in status.
             */
            $wasCompulsoryServicePending =
                $currentCompulsoryService === 0
                && (
                    $currentStatus === ''
                    || $currentStatus ===
                        'Departure Allowed'
                );

            /*
             * Departure Allowed can only be applied by VMS
             * when the boat was in Compulsory Service Pending.
             */
            if (
                $updatedStatus ===
                    'Departure Allowed'
                && !$wasCompulsoryServicePending
            ) {
                Yii::$app->session->setFlash(
                    'error',
                    'VMS cannot allow departure because this boat is not currently in Compulsory Service Pending status.'
                );

                return $this->redirect([
                    'update',
                    'token' => $token,
                ]);
            }

            /*
             * --------------------------------------------------
             * COMPULSORY SERVICE PENDING
             * --------------------------------------------------
             *
             * This is represented by compulsory_service = 0.
             *
             * Keep the existing main boat status.
             */
            if (
                $updatedStatus ===
                    'Compulsory Service Pending'
            ) {
                $model->status =
                    $currentStatus;

                $model->compulsory_service =
                    0;

                $model->compulsory_service_from_date =
                    null;

                $model->compulsory_service_to_date =
                    null;
            }

            /*
             * --------------------------------------------------
             * COMPULSORY SERVICE DONE
             * --------------------------------------------------
             */
            elseif (
                $updatedStatus ===
                    'Compulsory Service Done'
            ) {
                $model->status =
                    $currentStatus;

                $model->compulsory_service =
                    1;

                $model->compulsory_service_from_date =
                    null;

                $model->compulsory_service_to_date =
                    null;
            }

            /*
             * --------------------------------------------------
             * TEMPORARY SERVICE ALLOW
             * --------------------------------------------------
             */
            elseif (
                $updatedStatus ===
                    'Temporary Service Allow'
            ) {
                /*
                 * Only boats with compulsory service pending
                 * can receive temporary service permission.
                 */
                if (
                    $currentCompulsoryService !== 0
                ) {
                    Yii::$app->session->setFlash(
                        'error',
                        'Temporary Service Allow can only be applied when compulsory service is pending.'
                    );

                    return $this->redirect([
                        'update',
                        'token' => $token,
                    ]);
                }

                /*
                 * Do not replace the actual boat status with
                 * "Temporary Service Allow".
                 */
                $model->status =
                    $currentStatus;

                /*
                 * Form fields are temporarily used for the
                 * compulsory-service valid period.
                 */
                $model->compulsory_service_from_date =
                    $model->dep_cancel_date;

                $model->compulsory_service_to_date =
                    $model->to_date;

                /*
                 * Temporarily allow service.
                 */
                $model->compulsory_service =
                    1;

                /*
                 * Restore normal cancellation dates.
                 */
                $model->dep_cancel_date =
                    $existingDepCancelDate;

                $model->to_date =
                    $existingToDate;
            }

            /*
             * --------------------------------------------------
             * DEPARTURE ALLOWED
             * --------------------------------------------------
             */
            elseif (
                $updatedStatus ===
                    'Departure Allowed'
            ) {
                $model->status =
                    'Departure Allowed';

                /*
                 * The compulsory service restriction has now
                 * been cleared.
                 */
                $model->compulsory_service =
                    1;

                $model->compulsory_service_from_date =
                    null;

                $model->compulsory_service_to_date =
                    null;
            }

            /*
             * --------------------------------------------------
             * OTHER VMS STATUS
             * --------------------------------------------------
             */
            else {
                $model->status =
                    $updatedStatus;
            }
        }

        /*
         * ======================================================
         * INVESTIGATION / OPERATION / OTHER AUTHORIZED ROLES
         * ======================================================
         */

        else {
            if (
                $updatedStatus ===
                    'Violation Detected'
            ) {
                $model->status =
                    'Violation Detected';

                $model->compulsory_service =
                    0;
            } elseif (
                $updatedStatus ===
                    'Departure Allowed'
            ) {
                $model->status =
                    'Departure Allowed';
            } else {
                /*
                 * Preserve other valid status selections
                 * provided by the role-specific dropdown.
                 */
                $model->status =
                    $updatedStatus;
            }
        }

        /*
         * ======================================================
         * COMMON UPDATE VALUES
         * ======================================================
         */

        if (
            $model->harbor === null
            || trim(
                (string) $model->harbor
            ) === ''
        ) {
            $model->harbor =
                '-';
        }

        $model->dep_cancelled_by =
            (string) Yii::$app
                ->user
                ->identity
                ->id;

        /*
         * ======================================================
         * ACTIVITY DESCRIPTION
         * ======================================================
         */

        if (
            $updatedStatus ===
                'Compulsory Service Pending'
        ) {
            $activityStatus =
                'Compulsory Service Pending';
        } elseif (
            $updatedStatus ===
                'Compulsory Service Done'
        ) {
            $activityStatus =
                'Compulsory Service Done';
        } elseif (
            $updatedStatus ===
                'Temporary Service Allow'
        ) {
            $activityStatus =
                'Temporary Service Allow';
        } else {
            $activityStatus =
                $updatedStatus;
        }

        /*
         * ======================================================
         * SAVE
         * ======================================================
         */

        if ($model->save()) {
            $this->addBoatActivityLog(
                $model,
                $activityStatus
            );

            Yii::$app->session->setFlash(
                'success',
                'Boat status updated successfully.'
            );

            return $this->redirect([
                'view',
                'token' => $token,
            ]);
        }

        /*
         * ======================================================
         * SAVE FAILED
         * ======================================================
         *
         * Previously this failure was silent and simply
         * displayed the same Update page again.
         */

        Yii::error(
            [
                'message' =>
                    'Departure Boat status update failed.',

                'boatId' =>
                    (int) $model->id,

                'submittedStatus' =>
                    $updatedStatus,

                'currentStatus' =>
                    $currentStatus,

                'errors' =>
                    $model->getErrors(),
            ],
            __METHOD__
        );

        Yii::$app->session->setFlash(
            'error',
            'Boat status could not be updated. Please check the fields below.'
        );
    }

    /*
     * ==========================================================
     * RENDER UPDATE
     * ==========================================================
     */

    return $this->render(
        'update',
        [
            'model' =>
                $model,

            'token' =>
                $token,
        ]
    );
}
    public function actionUpdateOwner(string $token)
    {
        CommonService::validateEditPermission();

        if (!UserTypeUtil::hasType(Constant::HARBOUR_OFFICER)) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested page does not exist.'
                )
            );
        }

        $boat = $this->findModelByToken($token);

        $model = ProfileFisherman::findOne(
            (int) $boat->fisherman_id
        );

        if ($model === null) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested page does not exist.'
                )
            );
        }

        if (
            $this->request->isPost
            && $model->load($this->request->post())
            && $model->save(false, ['email', 'mobile'])
        ) {
            return $this->redirect([
                'view',
                'token' => $token,
            ]);
        }

        return $this->render(
            'updateOwner',
            [
                'model' => $model,
                'token' => $token,
            ]
        );
    }

   private function addBoatActivityLog(DepartureBoats $model, $activityStatus)
{
    $boatActivity = new DepartureBoatActivity();
    $boatActivity->boat_no = $model->boat->boat_number;
    $boatActivity->activity = $activityStatus; // use explicit activity
    $boatActivity->description = $model->remarks . " - " . $model->offence;
    $boatActivity->date_time = date("Y-m-d H:i:s");
    $boatActivity->user_name = Yii::$app->user->identity->id . "";
    $boatActivity->to_date = empty($model->to_date) ? "-" : $model->to_date;

    $boatActivity->validate();
    $boatActivity->save();
}

    private function addBoatActivityLogAuto(DepartureBoats $model)
    {
        $boatActivity = new DepartureBoatActivity();
        $boatActivity->boat_no = $model->boat->boat_number;
        $boatActivity->activity = $model->status;
        $boatActivity->description = $model->remarks . " - " . $model->offence;
        $boatActivity->date_time = date("Y-m-d H:i:s");
        $boatActivity->user_name = "AUTO";
        $boatActivity->to_date = empty($model->to_date) ? "-" : $model->to_date;

        $boatActivity->validate();
//        print_r($boatActivity->getErrors());exit();
        $boatActivity->save();


    }

    /**
     * Deletes an existing DepartureBoats model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(string $token): Response
    {
        if (!Util::adminPermission()) {
            throw new UnauthorizedHttpException(
                Yii::t(
                    'app',
                    'You do not have permission to perform this operation.'
                )
            );
        }

        $model = $this->findModelByToken($token);
        $model->delete();

        return $this->redirect([
            'index',
        ]);
    }
    public function actionUpdatePayment(string $token)
    {
        $roleString = (string) (
            Yii::$app->user->identity->type ?? ''
        );

        $roles = array_map(
            'trim',
            explode(',', $roleString)
        );

        $mainRole = $roles[0] ?? null;
        $secondaryRole = $roles[1] ?? null;

        $isMainPaymentOfficer =
            $mainRole == Constant::VMS_PAYMENT_OFFICER_MAIN;

        $isSecondaryPaymentOfficer =
            $secondaryRole == Constant::VMS_PAYMENT_OFFICER_SECONDARY;

        $hasAdminPaymentPermission =
            Util::adminPermission()
            && Yii::$app->user->can(
                'DepartureBoatController-payment'
            );

        if (
            !$isMainPaymentOfficer
            && !$isSecondaryPaymentOfficer
            && !$hasAdminPaymentPermission
        ) {
            throw new UnauthorizedHttpException(
                Yii::t(
                    'app',
                    'You do not have permission to perform this operation.'
                )
            );
        }

        $boat = $this->findModelByToken($token);
        $boatId = (int) $boat->id;

        $paymentLog = new DepatureBoatPayment();
        $paymentLog->boat_reg_id = $boatId;
        $paymentLog->added_by =
            (int) Yii::$app->user->identity->id;

        $previousUnpaidPaymentData =
            $this->getPreviousUnpaidPaymentData($boatId);

        $hasPreviousUnpaidMonths =
            (int) (
                $previousUnpaidPaymentData['count'] ?? 0
            ) > 0;

        if (
            Yii::$app->request->isPost
            && $paymentLog->load(
                Yii::$app->request->post()
            )
        ) {
            $paymentLog->boat_reg_id = $boatId;
            $paymentLog->added_by =
                (int) Yii::$app->user->identity->id;

            if (!empty($paymentLog->from_month)) {
                $paymentLog->from_month =
                    substr(
                        (string) $paymentLog->from_month,
                        0,
                        7
                    ) . '-01';
            }

            $amount = (int) $paymentLog->amount;

            if (
                $amount < 6000
                || $amount % 6000 !== 0
            ) {
                $paymentLog->addError(
                    'amount',
                    'Amount must be a multiple of 6,000.'
                );
            } elseif (!empty($paymentLog->from_month)) {
                $monthCount =
                    (int) ($amount / 6000);

                try {
                    $fromDate = new \DateTimeImmutable(
                        $paymentLog->from_month
                    );

                    $toDate = $fromDate->modify(
                        '+' . ($monthCount - 1) . ' months'
                    );

                    $paymentLog->to_date =
                        $toDate->format('Y-m-01');
                } catch (\Throwable $exception) {
                    $paymentLog->addError(
                        'from_month',
                        'Please select a valid From Month.'
                    );
                }
            }

            $paymentLog->fileUpload =
                UploadedFile::getInstance(
                    $paymentLog,
                    'fileUpload'
                );

            $paymentLog->affidavitFileUpload =
                UploadedFile::getInstance(
                    $paymentLog,
                    'affidavitFileUpload'
                );

            $isValid =
                $paymentLog->validate(null, false);

            if (
                $hasPreviousUnpaidMonths
                && !$paymentLog->affidavitFileUpload
            ) {
                $paymentLog->addError(
                    'affidavitFileUpload',
                    'Please upload the required affidavit.'
                );

                $isValid = false;
            }

            if ($isValid) {
                $uploadPath =
                    Constant::$FILE_UPLOAD_PATH . 'payment/';

                FileHelper::createDirectory($uploadPath);

                if ($paymentLog->fileUpload) {
                    $paymentFileName =
                        $boatId
                        . '-departure-payment-'
                        . date('YmdHis')
                        . '-'
                        . Yii::$app->security
                            ->generateRandomString(5)
                        . '.'
                        . strtolower(
                            $paymentLog->fileUpload->extension
                        );

                    if (
                        $paymentLog->fileUpload->saveAs(
                            $uploadPath . $paymentFileName
                        )
                    ) {
                        $paymentLog->file =
                            $paymentFileName;
                    } else {
                        $paymentLog->addError(
                            'fileUpload',
                            'The payment file could not be uploaded.'
                        );
                    }
                }

                if ($paymentLog->affidavitFileUpload) {
                    $affidavitFileName =
                        $boatId
                        . '-payment-affidavit-'
                        . date('YmdHis')
                        . '-'
                        . Yii::$app->security
                            ->generateRandomString(5)
                        . '.'
                        . strtolower(
                            $paymentLog
                                ->affidavitFileUpload
                                ->extension
                        );

                    if (
                        $paymentLog
                            ->affidavitFileUpload
                            ->saveAs(
                                $uploadPath
                                . $affidavitFileName
                            )
                    ) {
                        $paymentLog->affidavit_file =
                            $affidavitFileName;
                        $paymentLog->Affidavit_status = 1;
                    } else {
                        $paymentLog->addError(
                            'affidavitFileUpload',
                            'The affidavit file could not be uploaded.'
                        );
                    }
                }

                if (
                    !$paymentLog->hasErrors()
                    && $paymentLog->save(false)
                ) {
                    Yii::$app->session->setFlash(
                        'success',
                        'Payment was added successfully.'
                    );

                    return $this->redirect([
                        'view',
                        'token' => $token,
                    ]);
                }
            }

            Yii::error(
                [
                    'message' =>
                        'Departure payment submission failed.',
                    'boat_reg_id' => $boatId,
                    'errors' => $paymentLog->getErrors(),
                ],
                __METHOD__
            );
        }

        return $this->render(
            'payment',
            [
                'model' => $paymentLog,
                'previousUnpaidPaymentData' =>
                    $previousUnpaidPaymentData,
                'hasPreviousUnpaidMonths' =>
                    $hasPreviousUnpaidMonths,
                'token' => $token,
            ]
        );
    }
    public function actionAddBoatComment(string $token)
    {
        CommonService::validateEditPermission();

        $request = Yii::$app->request;
        $model = $this->findModelByToken($token);
        $boatId = (int) $model->id;
        $boatNumber = (string) (
            $model->boat->boat_number ?? ''
        );

        if ($request->isGet) {
            return $this->render(
                'addBoatComment',
                [
                    'boatNumber' => $boatNumber,
                    'token' => $token,
                ]
            );
        }

        $comment = trim(
            (string) $request->post('comment', '')
        );

        if ($comment === '') {
            Yii::$app->session->setFlash(
                'error',
                'Please enter a comment.'
            );

            return $this->redirect([
                'add-boat-comment',
                'token' => $token,
            ]);
        }

        $boatActivity = new DepartureBoatActivity();
        $boatActivity->boat_no = $boatNumber;
        $boatActivity->activity = 'comment';
        $boatActivity->description = $comment;
        $boatActivity->date_time = date('Y-m-d H:i:s');
        $boatActivity->to_date =
            empty($model->to_date)
                ? '-'
                : $model->to_date;
        $boatActivity->user_name =
            (string) Yii::$app->user->identity->id;

        if (!$boatActivity->save()) {
            Yii::$app->session->setFlash(
                'error',
                'Failed to save comment.'
            );

            return $this->redirect([
                'view',
                'token' => $token,
            ]);
        }

        $uploadedFiles =
            UploadedFile::getInstancesByName(
                'support_doc'
            );

        if (!empty($uploadedFiles)) {
            $process = 'BOAT_COMMENT';

            $workflow = MApprovalWorkflow::find()
                ->where([
                    'type' => $process,
                ])
                ->one();

            $uploadPath =
                Constant::$FILE_UPLOAD_PATH . 'files/';

            FileHelper::createDirectory($uploadPath);

            $allowedExtensions = [
                'pdf',
                'jpg',
                'jpeg',
                'png',
                'doc',
                'docx',
            ];

            foreach ($uploadedFiles as $file) {
                $extension = strtolower(
                    (string) $file->extension
                );

                if (!in_array(
                    $extension,
                    $allowedExtensions,
                    true
                )) {
                    Yii::warning(
                        [
                            'message' =>
                                'Rejected unsupported boat comment attachment.',
                            'boatId' => $boatId,
                            'extension' => $extension,
                        ],
                        __METHOD__
                    );
                    continue;
                }

                if ((int) $file->size > 10 * 1024 * 1024) {
                    Yii::warning(
                        [
                            'message' =>
                                'Rejected oversized boat comment attachment.',
                            'boatId' => $boatId,
                            'size' => (int) $file->size,
                        ],
                        __METHOD__
                    );
                    continue;
                }

                $fileType = -999;

                $fileName =
                    $process
                    . '_'
                    . $boatId
                    . '_'
                    . $fileType
                    . '_'
                    . date('YmdHis')
                    . '_'
                    . random_int(100, 999)
                    . '.'
                    . $extension;

                if (!$file->saveAs($uploadPath . $fileName)) {
                    continue;
                }

                $fileModel = new Files();
                $fileModel->type = $workflow->id ?? null;
                $fileModel->process_id = $boatActivity->id;
                $fileModel->status = 1;
                $fileModel->file_type = $fileType;
                $fileModel->file_name = $fileName;
                $fileModel->save(false);
            }
        }

        Yii::$app->session->setFlash(
            'success',
            'Comment and files saved successfully.'
        );

        return $this->redirect([
            'view',
            'token' => $token,
        ]);
    }

    public
    static function markeAsAllowed()
    {
        $expiredDepartureCancelled = DepartureBoats::find()
            ->where(['and',
                ['!=', 'to_date', ''],
                ['<', 'to_date', date('Y-m-d')],
                ['!=', 'status', 'Departure Allowed'],
            ])
            ->all();

        foreach ($expiredDepartureCancelled as $item) {
            $item->status = "Departure Allowed";
            if ($item->save(false)) {
                $boatActivity = new DepartureBoatActivity();
                $boatActivity->boat_no = $item->boat->boat_number;
                $boatActivity->activity = $item->status;
                $boatActivity->description = $item->remarks . " - " . $item->offence;
                $boatActivity->date_time = date("Y-m-d H:i:s");
                $boatActivity->user_name = "AUTO";
                $boatActivity->to_date = empty($item->to_date) ? "-" : $item->to_date;

                $boatActivity->validate();
//        print_r($boatActivity->getErrors());exit();
                $boatActivity->save();

            } else {

            }
        }

    }



    public static function markeAsServicepending()
{
    $today = date('Y-m-d');

    $expiredCompulsoryServiceAllow = DepartureBoats::find()
        ->where([
            'and',
            ['not', ['compulsory_service_to_date' => null]],
            ['<=', 'compulsory_service_to_date', $today],
            ['<>', 'compulsory_service', 0],
        ])
        ->all();

    foreach ($expiredCompulsoryServiceAllow as $item) {
        $expiredToDate = $item->compulsory_service_to_date;

        $item->compulsory_service = 0;
        $item->compulsory_service_from_date = null;
        $item->compulsory_service_to_date = null;

        if ($item->save(false)) {
            $boatActivity = new DepartureBoatActivity();
            $boatActivity->boat_no = $item->boat->boat_number;
            $boatActivity->activity = 'Compulsory Service Temporary Allow expired.';
            $boatActivity->description = 'Compulsory Service Temporary Allow expired.';
            $boatActivity->date_time = date('Y-m-d H:i:s');
            $boatActivity->user_name = 'AUTO';
            $boatActivity->to_date = $expiredToDate ?: '-';

            $boatActivity->save(false);
        }
    }
}

private function getPreviousUnpaidMonths($boatRegId)
{
    $paymentRecords = DepatureBoatPayment::find()
        ->where(['boat_reg_id' => $boatRegId])
        ->andWhere(['not', ['to_date' => null]])
        ->orderBy(['to_date' => SORT_ASC])
        ->all();

    if (empty($paymentRecords)) {
        return [
            'count' => 0,
            'months' => [],
            'hasPaymentHistory' => false,
        ];
    }

    // First payment's applicable-until month
    $firstPaymentToDate = (new \DateTimeImmutable(
        $paymentRecords[0]->to_date
    ))->modify('first day of this month');

    // Start calculating from the next month after the first payment
    $startMonth = $firstPaymentToDate->modify('+1 month');

    // Exclude the current month
    $lastPreviousMonth = (new \DateTimeImmutable('first day of this month'))
        ->modify('-1 month');

    if ($startMonth > $lastPreviousMonth) {
        return [
            'count' => 0,
            'months' => [],
            'hasPaymentHistory' => true,
        ];
    }

    $paidMonths = [];

    foreach ($paymentRecords as $payment) {
        $paymentTo = (new \DateTimeImmutable($payment->to_date))
            ->modify('first day of this month');

        /*
         * New payment records use from_month.
         * For old records where from_month is empty,
         * derive it using amount / 6000.
         */
        if (!empty($payment->from_month)) {
            $paymentFrom = (new \DateTimeImmutable($payment->from_month))
                ->modify('first day of this month');
        } else {
            $monthCount = (int) ($payment->amount / 6000);

            if ($monthCount < 1) {
                $monthCount = 1;
            }

            $paymentFrom = $paymentTo->modify(
                '-' . ($monthCount - 1) . ' month'
            );
        }

        // Mark every covered payment month as paid
        for (
            $month = $paymentFrom;
            $month <= $paymentTo;
            $month = $month->modify('+1 month')
        ) {
            $paidMonths[$month->format('Y-m')] = true;
        }
    }

    $unpaidMonths = [];

    // Add every missing month to the unpaid list
    for (
        $month = $startMonth;
        $month <= $lastPreviousMonth;
        $month = $month->modify('+1 month')
    ) {
        $monthKey = $month->format('Y-m');

        if (!isset($paidMonths[$monthKey])) {
            $unpaidMonths[] = $monthKey;
        }
    }

    return [
        'count' => count($unpaidMonths),
        'months' => $unpaidMonths,
        'hasPaymentHistory' => true,
    ];
}

private function getMonthsBetween($fromDate, $toDate)
{
    $months = [];

    $fromMonth = (new \DateTimeImmutable($fromDate))
        ->modify('first day of this month');

    $toMonth = (new \DateTimeImmutable($toDate))
        ->modify('first day of this month');

    for (
        $month = $fromMonth;
        $month <= $toMonth;
        $month = $month->modify('+1 month')
    ) {
        $months[] = $month->format('Y-m');
    }

    return $months;
}
    public function actionUpdateVmsStatus(
        string $token
    ): Response {
        if (!Yii::$app->user->can('DepartureBoatController-VMS')) {
            throw new UnauthorizedHttpException(
                Yii::t(
                    'app',
                    'You do not have permission to perform this operation.'
                )
            );
        }

        $model = $this->findModelByToken($token);
        $postedVmsStatus =
            Yii::$app->request->post('VMS');

        if (!in_array(
            $postedVmsStatus,
            ['0', '1', 0, 1],
            true
        )) {
            Yii::$app->session->setFlash(
                'error',
                'Invalid VMS status value.'
            );

            return $this->redirect([
                'view',
                'token' => $token,
            ]);
        }

        $model->VMS = (int) $postedVmsStatus;

        if ($model->save(false, ['VMS'])) {
            Yii::$app->session->setFlash(
                'success',
                $model->VMS === 1
                    ? 'VMS status activated successfully.'
                    : 'VMS status deactivated successfully.'
            );
        } else {
            Yii::$app->session->setFlash(
                'error',
                'VMS status could not be updated.'
            );
        }

        return $this->redirect([
            'view',
            'token' => $token,
        ]);
    }

private function getPreviousUnpaidPaymentData($boatRegId)
{
    $lastPaidToDate = DepatureBoatPayment::find()
        ->where([
            'boat_reg_id' => $boatRegId,
        ])
        ->andWhere([
            'not',
            ['to_date' => null],
        ])
        ->max('to_date');

    /*
     * No previous payment means no previous payment gap.
     */
    if (empty($lastPaidToDate)) {
        return [
            'count' => 0,
            'last_paid_month' => null,
        ];
    }

    try {
        $lastPaidMonth = new \DateTimeImmutable(
            substr($lastPaidToDate, 0, 7) . '-01'
        );

        $firstUnpaidMonth =
            $lastPaidMonth->modify('+1 month');

        $previousMonth = new \DateTimeImmutable(
            'first day of previous month'
        );

        if ($firstUnpaidMonth > $previousMonth) {
            return [
                'count' => 0,
                'last_paid_month' =>
                    $lastPaidMonth->format('Y-m'),
            ];
        }

        $unpaidCount =
            (($previousMonth->format('Y') -
                $firstUnpaidMonth->format('Y')) * 12) +
            ($previousMonth->format('n') -
                $firstUnpaidMonth->format('n')) +
            1;

        return [
            'count' => max(0, $unpaidCount),
            'last_paid_month' =>
                $lastPaidMonth->format('Y-m'),
        ];
    } catch (\Throwable $exception) {
        Yii::error([
            'message' =>
                'Unable to calculate unpaid payment months.',
            'boat_reg_id' => $boatRegId,
            'last_paid_to_date' => $lastPaidToDate,
            'exception' => $exception->getMessage(),
        ], __METHOD__);

        return [
            'count' => 0,
            'last_paid_month' => null,
        ];
    }
}
}