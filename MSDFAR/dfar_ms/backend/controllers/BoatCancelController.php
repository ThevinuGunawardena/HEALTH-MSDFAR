<?php

namespace backend\controllers;

use backend\components\Controller;
use backend\components\RecordLookupRateLimit;
use backend\components\SecurityHelper;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumberCancelRequests;
use backend\models\BoatNumberCancelRequestsSearch;
use backend\models\BoatNumbers;
use backend\models\Files;
use backend\models\FishermanRegisterdBoat;
use backend\models\HighseasLicense;
use backend\models\MApprovalWorkflow;
use backend\models\NationalLicense;
use backend\services\CommonService;
use backend\services\Util;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Secure CRUD controller for boat cancellation requests.
 *
 * Token rules:
 * - actionCreate() receives a BoatNumbers::class token.
 * - view/update/delete/file-upload use a BoatNumberCancelRequests::class token.
 */
class BoatCancelController extends Controller
{
    public $processType = 'BOAT_CANCEL';

   public function behaviors(): array
{
    return array_merge(
        parent::behaviors(),
        [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

            /*
             * Short burst protection.
             */
            'recordLookupBurstLimit' => [
                'class' =>
                    RecordLookupRateLimit::class,

                'only' => [
                    'view',
                    'create',
                    'update',
                    'delete',
                ],

                'limit' => 30,
                'window' => 60,
                'bucketName' =>
                    'boat-cancel-burst',
            ],

            /*
             * Longer sustained protection.
             */
            'recordLookupSustainedLimit' => [
                'class' =>
                    RecordLookupRateLimit::class,

                'only' => [
                    'view',
                    'create',
                    'update',
                    'delete',
                ],

                'limit' => 200,
                'window' => 900,
                'bucketName' =>
                    'boat-cancel-sustained',
            ],

            /*
             * Index/search may require a higher limit.
             */
            'indexRateLimit' => [
                'class' =>
                    RecordLookupRateLimit::class,

                'only' => [
                    'index',
                ],

                'limit' => 120,
                'window' => 60,
                'bucketName' =>
                    'boat-cancel-index',
            ],
        ]
    );
}

    /**
     * Lists cancellation requests.
     */
    public function actionIndex(): string
    {
        $this->ensureAuthenticated();

        $searchModel = new BoatNumberCancelRequestsSearch();
        $dataProvider = $searchModel->search(
            $this->request->queryParams
        );

        /*
         * Fishermen may only list cancellation requests belonging to
         * their own BoatNumbers record.
         */
        if (UserTypeUtil::hasType(Constant::FISHERMAN)) {
            $profileId = $this->getLoggedInProfileId();

            if ($profileId <= 0) {
                $dataProvider->getQuery()->andWhere('0=1');
            } else {
                $dataProvider->getQuery()
                    ->joinWith(['boatNumber'])
                    ->andWhere([
                        BoatNumbers::tableName() . '.owner' =>
                            $profileId,
                    ]);
            }
        }

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays and processes approval for a cancellation request.
     *
     * @return string|Response
     */
    public function actionView(string $token)
    {
        $this->ensureAuthenticated();

        $model = $this->findCancellationByToken($token);
        $this->enforceCancellationOwnership($model);

        $requestId = (int) $model->id;

        $approvalFlow = CommonService::getApprovalProcess(
            $model,
            $this->processType,
            $requestId,
            false
        );

        if ($this->request->isPost) {
            if (
                !Util::editPermission()
                || empty($approvalFlow['showApproveBtn'])
            ) {
                throw new ForbiddenHttpException(
                    Yii::t(
                        'app',
                        'You are not allowed to approve or reject this request.'
                    )
                );
            }

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $model = CommonService::
                    markApprovalStageForBoatCancellationAndTransfer(
                        $approvalFlow,
                        $model,
                        $requestId,
                        $this->processType
                    );

                if (!$model->save()) {
                    throw new ServerErrorHttpException(
                        Yii::t(
                            'app',
                            'The cancellation request could not be updated.'
                        )
                    );
                }

                $this->cancelReleasedLicenses($model);

                $transaction->commit();

                Yii::$app->session->setFlash(
                    'success',
                    Yii::t(
                        'app',
                        'The cancellation request was updated successfully.'
                    )
                );

                $newToken = SecurityHelper::encryptId(
                    BoatNumberCancelRequests::class,
                    (int) $model->id
                );

                return $this->redirect([
                    '/boat-cancel/view',
                    'token' => $newToken,
                ]);
            } catch (\Throwable $exception) {
                if ($transaction->isActive) {
                    $transaction->rollBack();
                }

                Yii::error(
                    [
                        'message' => $exception->getMessage(),
                        'requestId' => $requestId,
                        'exception' => get_class($exception),
                    ],
                    'boat-cancel-approval'
                );

                throw $exception;
            }
        }

        $workflow = MApprovalWorkflow::find()
            ->where([
                'type' => $this->processType,
            ])
            ->one();

        if ($workflow === null) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'Boat cancellation workflow was not found.'
                )
            );
        }

        $files = Files::find()
            ->where([
                'type' => $workflow->id,
                'process_id' => $requestId,
            ])
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->all();

        return $this->render(
            'view',
            [
                'model' => $model,
                'approvalHistory' =>
                    $approvalFlow['approvalHistory'] ?? [],
                'showRejectBtn' => (bool) (
                    $approvalFlow['showRejectBtn'] ?? false
                ),
                'showApproveBtn' => (bool) (
                    $approvalFlow['showApproveBtn'] ?? false
                ),
                'process' => $this->processType,
                'files' => $files,
                'token' => $token,
            ]
        );
    }

    /**
     * Creates a cancellation request from a BoatNumbers token.
     *
     * @return string|Response
     */
    public function actionCreate(string $token)
    {
        $this->ensureAuthenticated();

        $boatNumber = $this->findBoatNumberByToken($token);
        $this->enforceBoatOwnership($boatNumber);

        if (
            (int) $boatNumber->status
            === (int) Constant::Cancelled
        ) {
            throw new BadRequestHttpException(
                Yii::t(
                    'app',
                    'This boat has already been cancelled.'
                )
            );
        }

        $model = new BoatNumberCancelRequests();
        $model->boat_number_id = (int) $boatNumber->id;

        if ($this->request->isPost) {
            if (!Util::editPermission()) {
                throw new ForbiddenHttpException(
                    Yii::t(
                        'app',
                        'You are not allowed to submit a boat cancellation request.'
                    )
                );
            }

            if ($model->load($this->request->post())) {
                /*
                 * Do not trust protected workflow fields from POST.
                 */
                $model->boat_number_id = (int) $boatNumber->id;
                $model->status = Constant::Pending;
                $model->approval_stage = (string) Constant::FI;

                $transaction = Yii::$app->db->beginTransaction();

                try {
                    if (!$model->save()) {
                        throw new ServerErrorHttpException(
                            Yii::t(
                                'app',
                                'The boat cancellation request could not be saved.'
                            )
                        );
                    }

                    CommonService::addApprovalLog(
                        $this->processType,
                        'Submitted',
                        'Submitted',
                        (int) $model->id
                    );

                    $transaction->commit();

                    Yii::$app->session->setFlash(
                        'success',
                        Yii::t(
                            'app',
                            'Boat cancellation request submitted successfully.'
                        )
                    );

                    $requestToken = SecurityHelper::encryptId(
                        BoatNumberCancelRequests::class,
                        (int) $model->id
                    );

                    return $this->redirect([
                        '/boat-cancel/view',
                        'token' => $requestToken,
                    ]);
                } catch (\Throwable $exception) {
                    if ($transaction->isActive) {
                        $transaction->rollBack();
                    }

                    Yii::error(
                        [
                            'message' => $exception->getMessage(),
                            'boatNumberId' => (int) $boatNumber->id,
                            'errors' => $model->getErrors(),
                            'exception' => get_class($exception),
                        ],
                        'boat-cancel-create'
                    );

                    if (
                        !$exception
                        instanceof ServerErrorHttpException
                    ) {
                        throw $exception;
                    }

                    $model->addError(
                        'boat_number_id',
                        Yii::t(
                            'app',
                            'The request could not be saved. Please check the form and try again.'
                        )
                    );
                }
            }
        } else {
            $model->loadDefaultValues();
            $model->boat_number_id = (int) $boatNumber->id;
        }

        return $this->render(
            'create',
            [
                'model' => $model,
                'boatNumber' => $boatNumber,
                'boatToken' => $token,
            ]
        );
    }

    /**
     * Updates a cancellation request using a model-bound token.
     *
     * @return string|Response
     */
    public function actionUpdate(string $token)
    {
        $this->ensureAuthenticated();

        $model = $this->findCancellationByToken($token);
        $this->enforceCancellationOwnership($model);

        if (!Util::editPermission()) {
            throw new ForbiddenHttpException(
                Yii::t(
                    'app',
                    'You are not allowed to update this request.'
                )
            );
        }

        $originalBoatNumberId = (int) $model->boat_number_id;
        $originalStatus = $model->status;
        $originalApprovalStage = $model->approval_stage;

        if (
            $this->request->isPost
            && $model->load($this->request->post())
        ) {
            /*
             * Restore protected fields after mass assignment.
             */
            $model->boat_number_id = $originalBoatNumberId;
            $model->status = $originalStatus;
            $model->approval_stage = $originalApprovalStage;

            if ($model->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    Yii::t(
                        'app',
                        'Boat cancellation request updated successfully.'
                    )
                );

                $newToken = SecurityHelper::encryptId(
                    BoatNumberCancelRequests::class,
                    (int) $model->id
                );

                return $this->redirect([
                    '/boat-cancel/view',
                    'token' => $newToken,
                ]);
            }
        }

        return $this->render(
            'update',
            [
                'model' => $model,
                'token' => $token,
            ]
        );
    }

    /**
     * Deletes a pending request. This action is deliberately restricted
     * to administrators and is POST-only.
     */
    public function actionDelete(string $token): Response
    {
        $this->ensureAuthenticated();

        if (!UserTypeUtil::hasType(Constant::ADMIN)) {
            throw new ForbiddenHttpException(
                Yii::t(
                    'app',
                    'Only an administrator may delete a cancellation request.'
                )
            );
        }

        $model = $this->findCancellationByToken($token);

        if (
            (int) $model->status
            !== (int) Constant::Pending
        ) {
            throw new ForbiddenHttpException(
                Yii::t(
                    'app',
                    'Only a pending cancellation request may be deleted.'
                )
            );
        }

        if ($model->delete() === false) {
            throw new ServerErrorHttpException(
                Yii::t(
                    'app',
                    'The cancellation request could not be deleted.'
                )
            );
        }

        Yii::$app->session->setFlash(
            'success',
            Yii::t(
                'app',
                'Boat cancellation request deleted successfully.'
            )
        );

        return $this->redirect([
            '/boat-cancel/index',
        ]);
    }

    private function findCancellationByToken(
        string $token
    ): BoatNumberCancelRequests {
        $requestId = SecurityHelper::decryptId(
            trim($token),
            BoatNumberCancelRequests::class
        );

        $model = BoatNumberCancelRequests::findOne([
            'id' => (int) $requestId,
        ]);

        if ($model === null) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested boat cancellation record was not found.'
                )
            );
        }

        return $model;
    }

    private function findBoatNumberByToken(
        string $token
    ): BoatNumbers {
        $boatNumberId = SecurityHelper::decryptId(
            trim($token),
            BoatNumbers::class
        );

        $model = BoatNumbers::findOne([
            'id' => (int) $boatNumberId,
        ]);

        if ($model === null) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested boat number was not found.'
                )
            );
        }

        return $model;
    }

    private function enforceCancellationOwnership(
        BoatNumberCancelRequests $model
    ): void {
        if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
            return;
        }

        $boatNumber = $model->boatNumber;

        if (!$boatNumber instanceof BoatNumbers) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested boat cancellation record was not found.'
                )
            );
        }

        $this->enforceBoatOwnership($boatNumber);
    }

    private function enforceBoatOwnership(
        BoatNumbers $boatNumber
    ): void {
        if (!UserTypeUtil::hasType(Constant::FISHERMAN)) {
            return;
        }

        $profileId = $this->getLoggedInProfileId();
        $ownerId = (int) ($boatNumber->owner ?? 0);

        if (
            $profileId <= 0
            || $ownerId !== $profileId
        ) {
            /*
             * Return 404 rather than revealing that another user's
             * record exists.
             */
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested boat number was not found.'
                )
            );
        }
    }

    private function getLoggedInProfileId(): int
    {
        return (int) (
            Yii::$app->user->identity->profile_id
            ?? 0
        );
    }

    private function ensureAuthenticated(): void
    {
        if (Yii::$app->user->isGuest) {
            throw new UnauthorizedHttpException(
                Yii::t(
                    'app',
                    'Authentication is required.'
                )
            );
        }
    }

    /**
     * Cancels the boat number, registered boat and released licences.
     * The caller must execute this method inside a database transaction.
     */
    private function cancelReleasedLicenses(
        BoatNumberCancelRequests $model
    ): void {
        if (
            (int) $model->status
            !== (int) Constant::RequestCompleted
        ) {
            return;
        }

        $boatNumberId = (int) $model->boat_number_id;

        if ($boatNumberId <= 0) {
            throw new ServerErrorHttpException(
                Yii::t(
                    'app',
                    'The cancellation request has no valid boat number.'
                )
            );
        }

        BoatNumbers::updateAll(
            [
                'status' => Constant::Cancelled,
            ],
            [
                'id' => $boatNumberId,
            ]
        );

        $registeredBoatIds = FishermanRegisterdBoat::find()
            ->select(['id'])
            ->where([
                'boat_number_id' => $boatNumberId,
            ])
            ->column();

        FishermanRegisterdBoat::updateAll(
            [
                'status' => Constant::Cancelled,
            ],
            [
                'boat_number_id' => $boatNumberId,
            ]
        );

        if (empty($registeredBoatIds)) {
            return;
        }

        NationalLicense::updateAll(
            [
                'status' => Constant::Cancelled,
            ],
            [
                'boat_registration_id' => $registeredBoatIds,
            ]
        );

        HighseasLicense::updateAll(
            [
                'status' => Constant::Cancelled,
            ],
            [
                'boat_registration_id' => $registeredBoatIds,
            ]
        );
    }
}
