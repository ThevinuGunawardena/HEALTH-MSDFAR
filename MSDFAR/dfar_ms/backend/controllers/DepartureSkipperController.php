<?php

namespace backend\controllers;

use app\models\DepartureSkipperC;
use backend\components\Controller;
use backend\components\RecordLookupRateLimit;
use backend\components\SecurityHelper;
use backend\models\DepartureActivitySkipper;
use backend\models\DepartureSkipper;
use backend\models\DepartureSkipperSearch;
use backend\services\CommonService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * DepartureSkipperController implements the CRUD actions for DepartureSkipper.
 *
 * Public record navigation uses model-bound SecurityHelper tokens instead of
 * exposing NIC values in URLs.
 */
class DepartureSkipperController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
                'create' => ['GET', 'POST'],
                'update' => ['GET', 'POST'],
                'update-crew' => ['GET', 'POST'],
                'delete' => ['POST'],
            ],
        ];

        /*
         * Index/search page: generous limit because pagination/search can
         * legitimately create several requests.
         */
        $behaviors['departureSkipperIndexRateLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => ['index'],
            'limit' => 120,
            'window' => 60,
            'bucketName' => 'departure-skipper-index-short',
        ];

        $behaviors['departureSkipperIndexLongRateLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => ['index'],
            'limit' => 600,
            'window' => 900,
            'bucketName' => 'departure-skipper-index-long',
        ];

        /*
         * Token-protected record lookup.
         */
        $behaviors['departureSkipperRecordRateLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => ['view'],
            'limit' => 30,
            'window' => 60,
            'bucketName' => 'departure-skipper-record-short',
        ];

        $behaviors['departureSkipperRecordLongRateLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => ['view'],
            'limit' => 200,
            'window' => 900,
            'bucketName' => 'departure-skipper-record-long',
        ];

        /*
         * Create/update endpoints are more sensitive because they change data.
         * Your RecordLookupRateLimit class applies to both GET and POST here;
         * VerbFilter above controls the allowed HTTP methods.
         */
        $behaviors['departureSkipperWriteRateLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => [
                'create',
                'update',
                'update-crew',
                'delete',
            ],
            'limit' => 20,
            'window' => 60,
            'bucketName' => 'departure-skipper-write-short',
        ];

        $behaviors['departureSkipperWriteLongRateLimit'] = [
            'class' => RecordLookupRateLimit::class,
            'only' => [
                'create',
                'update',
                'update-crew',
                'delete',
            ],
            'limit' => 100,
            'window' => 900,
            'bucketName' => 'departure-skipper-write-long',
        ];

        return $behaviors;
    }

    /**
     * Lists all DepartureSkipper models.
     */
    public function actionIndex(): string
    {
        $searchModel = new DepartureSkipperSearch();
        $dataProvider = $searchModel->search(
            $this->request->queryParams
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DepartureSkipper model using a secure token.
     */
    public function actionView(string $token): string
    {
        $model = $this->findModelByToken($token);

        $activityQuery = DepartureActivitySkipper::find()
            ->where([
                'nic' => $model->nic,
            ])
            ->orderBy([
                'date_time' => SORT_DESC,
            ]);

        $activity = new ActiveDataProvider([
            'query' => $activityQuery,
        ]);

        return $this->render('view', [
            'model' => $model,
            'activity' => $activity,
            'token' => $token,
        ]);
    }

    /**
     * Creates a new DepartureSkipper/Crew Member record.
     */
    public function actionCreate()
    {
        CommonService::validateEditPermission();
        CommonService::validatePermission(
            $this,
            'DepartureSkipperController-insert'
        );

        $model = new DepartureSkipperC();

        if ($this->request->isPost) {
            if (
                $model->load($this->request->post())
                && $model->save()
            ) {
                /*
                 * The listing/view controller is based on DepartureSkipper.
                 * Resolve that persisted row, then create a model-bound token.
                 */
                $viewModel = DepartureSkipper::findOne([
                    'nic' => $model->nic,
                ]);

                if ($viewModel === null) {
                    throw new NotFoundHttpException(
                        Yii::t(
                            'app',
                            'The saved skipper record could not be loaded.'
                        )
                    );
                }

                $token = $this->createToken($viewModel);

                Yii::$app->session->setFlash(
                    'success',
                    'Skipper saved'
                );

                return $this->redirect([
                    'view',
                    'token' => $token,
                ]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates a skipper or crew-member record using the same secure token that
     * identifies the corresponding DepartureSkipper row.
     */
    public function actionUpdate(string $token)
    {
        CommonService::validatePermission(
            $this,
            'DepartureSkipperController-update'
        );

        $departureSkipper = $this->findModelByToken($token);
        $model = $departureSkipper;

        if ($departureSkipper->crew_type === 'Crew Member') {
            $model = $this->findModelCrewByNic(
                (string) $departureSkipper->nic
            );
        }

        if (
            $this->request->isPost
            && $model->load($this->request->post())
        ) {
            /*
             * NIC is used to relate the DepartureSkipper and crew model.
             * Keep the persisted identity stable during tokenized updates.
             */
            $model->nic = $departureSkipper->nic;
            $model->approved_by =
                (string) Yii::$app->user->identity->id;
            $model->dep_cancel_allow_by =
                (string) Yii::$app->user->identity->id;
            $model->timestamp = date('Y-m-d H:i:s');

            if ($model->save()) {
                $this->addSkipperActivityLog($model);

                Yii::$app->session->setFlash(
                    'success',
                    'Skipper updated'
                );

                return $this->redirect([
                    'view',
                    'token' => $token,
                ]);
            }

            Yii::error([
                'message' => 'Departure skipper update failed.',
                'recordId' => (int) $departureSkipper->id,
                'errors' => $model->getErrors(),
            ], __METHOD__);
        }

        return $this->render('update', [
            'model' => $model,
            'token' => $token,
            'formAction' => 'update',
        ]);
    }

    /**
     * Explicit crew update route, retained for compatibility.
     */
    public function actionUpdateCrew(string $token)
    {
        CommonService::validatePermission(
            $this,
            'DepartureSkipperController-update'
        );

        $departureSkipper = $this->findModelByToken($token);

        $model = $this->findModelCrewByNic(
            (string) $departureSkipper->nic
        );

        if (
            $this->request->isPost
            && $model->load($this->request->post())
        ) {
            $model->nic = $departureSkipper->nic;
            $model->approved_by =
                (string) Yii::$app->user->identity->id;
            $model->dep_cancel_allow_by =
                (string) Yii::$app->user->identity->id;
            $model->timestamp = date('Y-m-d H:i:s');

            if ($model->save()) {
                $this->addSkipperActivityLog($model);

                Yii::$app->session->setFlash(
                    'success',
                    'Crew member updated'
                );

                return $this->redirect([
                    'view',
                    'token' => $token,
                ]);
            }

            Yii::error([
                'message' => 'Departure crew update failed.',
                'recordId' => (int) $departureSkipper->id,
                'errors' => $model->getErrors(),
            ], __METHOD__);
        }

        return $this->render('update', [
            'model' => $model,
            'token' => $token,
            'formAction' => 'update-crew',
        ]);
    }

    /**
     * Delete remains disabled as in the original controller, but the endpoint
     * no longer accepts a NIC value in the URL.
     */
    public function actionDelete(string $token): Response
    {
        /*
         * Validate the token/record even though deletion is currently disabled.
         * This prevents a meaningless token parameter from bypassing lookup
         * validation if deletion is enabled later.
         */
        $this->findModelByToken($token);

        return $this->redirect(['index']);
    }

    /**
     * Creates a model-bound token for a DepartureSkipper record.
     */
    private function createToken(DepartureSkipper $model): string
    {
        return SecurityHelper::encryptId(
            DepartureSkipper::class,
            (int) $model->id
        );
    }

    /**
     * Loads a DepartureSkipper using a model-bound token.
     */
    protected function findModelByToken(string $token): DepartureSkipper
    {
        $token = trim($token);

        if ($token === '') {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested page does not exist.'
                )
            );
        }

        try {
            $id = SecurityHelper::decryptId(
                $token,
                DepartureSkipper::class
            );
        } catch (\Throwable $exception) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    'The requested page does not exist.'
                )
            );
        }

        $model = DepartureSkipper::findOne([
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

    /**
     * Internal lookup only. NIC is no longer accepted from public routes.
     */
    protected function findModelCrewByNic(string $nic): DepartureSkipperC
    {
        $model = DepartureSkipperC::findOne([
            'nic' => $nic,
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

    private function addSkipperActivityLog($model): void
    {
        $skipperActivity = new DepartureActivitySkipper();
        $skipperActivity->skipper_id = $model->skipper_id;
        $skipperActivity->nic = $model->nic;
        $skipperActivity->served_vessel =
            $model->served_vessel ?? 'NA';
        $skipperActivity->dep_date =
            $model->dep_date ?? null;
        $skipperActivity->dep_id = $model->dep_id;
        $skipperActivity->activity = $model->status;
        $skipperActivity->description =
            ($model->remarks ?? '')
            . ' - '
            . ($model->offence_reason ?? '');
        $skipperActivity->date_time = date('Y-m-d H:i');
        $skipperActivity->user_name =
            (string) Yii::$app->user->identity->id;
        $skipperActivity->to_date = $model->to_date;

        if (!$skipperActivity->save()) {
            Yii::error([
                'message' => 'Departure skipper activity log failed.',
                'errors' => $skipperActivity->getErrors(),
            ], __METHOD__);
        }
    }
}
