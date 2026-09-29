<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\models\Files;
use backend\models\FilesSearch;
use backend\models\MApprovalWorkflow;
use backend\models\MRequeredDocuments;
use backend\services\Util;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\components\Controller;
use backend\components\SecurityHelper;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\Skipper;
use backend\models\SkipperRenew;
use backend\models\BoatNumbers;
use backend\models\BoatNumberCancelRequests;
/**
 * FileController implements the CRUD actions for Files model.
 */
class FileController extends Controller
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
     * Lists all Files models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FilesSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Files model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Files model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionUpload(
    $id = null,
    $process = null,
    $token = null
) {
    $process = trim((string) $process);
    $token = trim((string) $token);

    if ($process === '') {
        throw new BadRequestHttpException(
            Yii::t(
                'app',
                'Upload process is required.'
            )
        );
    }

    /*
     * Resolve and validate the parent record.
     *
     * For tokenized processes, the returned ID is the
     * decrypted database record ID.
     */
    $resolvedParent = $this->resolveUploadParent(
        $process,
        $id,
        $token
    );

    $recordId = (int) (
        $resolvedParent['id'] ?? 0
    );

    $isTokenizedProcess = (bool) (
        $resolvedParent['tokenized'] ?? false
    );

    /*
     * Use the token returned by the resolver when available.
     */
    $token = trim(
        (string) (
            $resolvedParent['token']
            ?? $token
        )
    );

    if ($recordId <= 0) {
        throw new BadRequestHttpException(
            Yii::t(
                'app',
                'A valid parent record is required.'
            )
        );
    }

    /*
     * Tokenized modules must retain the encrypted token
     * throughout upload, redirect, delete and Go Back actions.
     */
    if (
        $isTokenizedProcess
        && $token === ''
    ) {
        throw new BadRequestHttpException(
            Yii::t(
                'app',
                'A valid record token is required.'
            )
        );
    }

    /*
     * Load the workflow for the requested process.
     */
    $workflow = MApprovalWorkflow::find()
        ->where([
            'type' => $process,
        ])
        ->one();

    if ($workflow === null) {
        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'Approval workflow was not found.'
            )
        );
    }

    $model = new Files();

    $files = Files::find()
        ->where([
            'type' => $workflow->id,
            'process_id' => $recordId,
        ])
        ->orderBy([
            'id' => SORT_DESC,
        ])
        ->all();

    /*
     * Assign trusted server-side values.
     */
    $model->type = (string) $workflow->id;
    $model->process_id = $recordId;
    $model->status = 1;

    $requiredDocuments = MRequeredDocuments::find()
        ->where([
            'type' => $workflow->id,
            'status' => 1,
        ])
        ->all();

    $documents = ArrayHelper::map(
        $requiredDocuments,
        'id',
        'discription'
    );

    $documents['-999'] = Yii::t(
        'app',
        'Other'
    );

    if ($this->request->isPost) {
        if (!Util::editPermission()) {
            throw new ForbiddenHttpException(
                Yii::t(
                    'app',
                    'You are not allowed to upload files.'
                )
            );
        }

        if ($model->load($this->request->post())) {
            /*
             * Never trust these protected attributes
             * from the submitted form.
             */
            $model->type =
                (string) $workflow->id;

            $model->process_id =
                $recordId;

            $model->status = 1;

            $file = UploadedFile::getInstance(
                $model,
                'file_name'
            );

            if ($file === null) {
                $model->addError(
                    'file_name',
                    Yii::t(
                        'app',
                        'Please select a file to upload.'
                    )
                );
            } else {
                /*
                 * Build a safe server-generated filename.
                 */
                $safeProcess = preg_replace(
                    '/[^A-Za-z0-9_-]+/',
                    '_',
                    $process
                );

                if (
                    $safeProcess === null
                    || $safeProcess === ''
                ) {
                    $safeProcess = 'FILE';
                }

                $extension = strtolower(
                    (string) $file->extension
                );

                if ($extension === '') {
                    $model->addError(
                        'file_name',
                        Yii::t(
                            'app',
                            'The selected file has no valid extension.'
                        )
                    );
                } else {
                    $randomPart =
                        Yii::$app->security
                            ->generateRandomString(12);

                    $fileName =
                        $safeProcess
                        . '_'
                        . $recordId
                        . '_'
                        . (int) $model->file_type
                        . '_'
                        . date('YmdHis')
                        . '_'
                        . $randomPart
                        . '.'
                        . $extension;

                    $uploadDirectory =
                        rtrim(
                            Constant::$FILE_UPLOAD_PATH,
                            '/\\'
                        )
                        . DIRECTORY_SEPARATOR
                        . 'files'
                        . DIRECTORY_SEPARATOR;

                    if (
                        !is_dir($uploadDirectory)
                        || !is_writable($uploadDirectory)
                    ) {
                        Yii::error(
                            [
                                'message' =>
                                    'File upload directory is unavailable.',
                                'directory' =>
                                    $uploadDirectory,
                                'process' =>
                                    $process,
                                'processId' =>
                                    $recordId,
                            ],
                            'file-upload'
                        );

                        throw new ServerErrorHttpException(
                            Yii::t(
                                'app',
                                'The upload directory is unavailable.'
                            )
                        );
                    }

                    $savedFilePath =
                        $uploadDirectory
                        . $fileName;

                    $savedToDisk = $file->saveAs(
                        $savedFilePath
                    );

                    if (!$savedToDisk) {
                        $model->addError(
                            'file_name',
                            Yii::t(
                                'app',
                                'The file could not be saved.'
                            )
                        );
                    } else {
                        $model->file_name =
                            $fileName;

                        if ($model->save()) {
                            Yii::$app->session
                                ->setFlash(
                                    'success',
                                    Yii::t(
                                        'app',
                                        'File uploaded successfully.'
                                    )
                                );

                            /*
                             * Always preserve the token for
                             * tokenized modules.
                             */
                            $redirectRoute = [
                                '/file/upload',
                                'process' => $process,
                            ];

                            if ($isTokenizedProcess) {
                                $redirectRoute['token'] =
                                    $token;
                            } else {
                                $redirectRoute['id'] =
                                    $recordId;
                            }

                            return $this->redirect(
                                $redirectRoute
                            );
                        }

                        /*
                         * Remove the physical file if the
                         * database record cannot be saved.
                         */
                        if (is_file($savedFilePath)) {
                            @unlink($savedFilePath);
                        }
                    }
                }
            }
        }
    } else {
        $model->loadDefaultValues();

        /*
         * loadDefaultValues() may replace values,
         * so restore trusted attributes.
         */
        $model->type =
            (string) $workflow->id;

        $model->process_id =
            $recordId;

        $model->status = 1;
    }

    return $this->render(
        'create',
        [
            'model' =>
                $model,

            'documentArray' =>
                $documents,

            'files' =>
                $files,

            'process' =>
                $process,

            'recordId' =>
                $recordId,

            /*
             * Required for Go Back, form submission
             * and file deletion.
             */
            'token' =>
                $token,

            'isTokenizedProcess' =>
                $isTokenizedProcess,
        ]
    );
}
    /**
     * Updates an existing Files model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save() && Util::editPermission()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'uploadedFiles' => $model,
        ]);
    }

    /**
     * Deletes an existing Files model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(
    $fileId,
    $process,
    $id = null,
    $token = null
) {
    if (!Util::editPermission()) {
        throw new ForbiddenHttpException(
            Yii::t(
                'app',
                'You are not allowed to delete this file.'
            )
        );
    }

    $process = trim((string) $process);

    if ($process === '') {
        throw new BadRequestHttpException(
            Yii::t(
                'app',
                'Upload process is required.'
            )
        );
    }

    /*
     * Resolve and validate the parent record.
     */
    $resolvedParent = $this->resolveUploadParent(
        $process,
        $id,
        $token
    );

    $parentId =
        (int) $resolvedParent['id'];

    $isTokenizedProcess =
        (bool) $resolvedParent['tokenized'];

    /*
     * Validate the workflow as well.
     *
     * This prevents deleting a file through another
     * workflow that happens to use the same process ID.
     */
    $workflow = MApprovalWorkflow::find()
        ->where([
            'type' => $process,
        ])
        ->one();

    if ($workflow === null) {
        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'Approval workflow was not found.'
            )
        );
    }

    $fileId = (int) $fileId;

    if ($fileId <= 0) {
        throw new BadRequestHttpException(
            Yii::t(
                'app',
                'A valid file ID is required.'
            )
        );
    }

    $fileModel = $this->findModel($fileId);

    /*
     * Prevent cross-record and cross-workflow deletion.
     */
    if (
        (int) $fileModel->process_id
            !== $parentId
        || (int) $fileModel->type
            !== (int) $workflow->id
    ) {
        throw new NotFoundHttpException(
            Yii::t(
                'app',
                'The requested file was not found.'
            )
        );
    }

    if ($fileModel->delete() === false) {
        throw new ServerErrorHttpException(
            Yii::t(
                'app',
                'The file record could not be deleted.'
            )
        );
    }

    Yii::$app->session->setFlash(
        'success',
        Yii::t(
            'app',
            'File deleted successfully.'
        )
    );

    /*
     * Redirect tokenized processes using the token.
     */
    if ($isTokenizedProcess) {
        return $this->redirect([
            'upload',
            'token' => $token,
            'process' => $process,
        ]);
    }

    /*
     * Existing modules use the numeric parent ID.
     */
    return $this->redirect([
        'upload',
        'id' => $parentId,
        'process' => $process,
    ]);
}

    /**
     * Finds the Files model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Files the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Files::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    private function resolveUploadParent(
    string $process,
    $id = null,
    $token = null
): array {
    $process = trim($process);
    $token = trim((string) $token);

    if ($process === '') {
        throw new BadRequestHttpException(
            Yii::t(
                'app',
                'A valid process is required.'
            )
        );
    }

    /*
     * Configuration for every process that uses
     * an encrypted model-bound token.
     */
    $tokenizedProcesses = [
        /*
         * High Seas licence and renewal.
         */
        'HIGHSEAS_LICENSE' => [
            'modelClass' =>
                HighseasLicense::class,
            'primaryKey' => 'id',
            'tokenMessage' =>
                'A valid High Seas License token is required.',
            'notFoundMessage' =>
                'High Seas License was not found.',
        ],

        'HIGHSEAS_LICENSE_RENEW' => [
            'modelClass' =>
                HighseasLicense::class,
            'primaryKey' => 'id',
            'tokenMessage' =>
                'A valid High Seas License token is required.',
            'notFoundMessage' =>
                'High Seas License renewal was not found.',
        ],

        /*
         * National licence.
         */
        'NATIONAL_LICENSE' => [
            'modelClass' =>
                NationalLicense::class,
            'primaryKey' => 'id',
            'tokenMessage' =>
                'A valid National License token is required.',
            'notFoundMessage' =>
                'National License was not found.',
        ],

        /*
         * First Boat Registration and Boat
         * Registration Renewal.
         *
         * The encrypted value is:
         * fisherman_registerd_boat_license.nid
         */
        'BOAT_REGISTER' => [
            'modelClass' =>
                FishermanRegisterdBoatLicense::class,
            'primaryKey' => 'nid',
            'tokenMessage' =>
                'A valid Boat Registration token is required.',
            'notFoundMessage' =>
                'Boat Registration was not found.',
        ],

        'BOAT_REGISTER_ReNEW' => [
            'modelClass' =>
                FishermanRegisterdBoatLicense::class,
            'primaryKey' => 'nid',
            'tokenMessage' =>
                'A valid Boat Registration token is required.',
            'notFoundMessage' =>
                'Boat Registration renewal was not found.',
        ],

        /*
         * First Skipper licence.
         *
         * The encrypted value is skipper.id.
         */
        'SKIPPER_LICENCE' => [
            'modelClass' =>
                Skipper::class,
            'primaryKey' => 'id',
            'tokenMessage' =>
                'A valid Skipper License token is required.',
            'notFoundMessage' =>
                'Skipper License was not found.',
        ],

        /*
         * Skipper licence renewal.
         *
         * The encrypted value is skipper_renew.id.
         */
        'SKIPPER_LICENCE_RENEW' => [
            'modelClass' =>
                SkipperRenew::class,
            'primaryKey' => 'id',
            'tokenMessage' =>
                'A valid Skipper License renewal token is required.',
            'notFoundMessage' =>
                'Skipper License renewal was not found.',
        ],

        'BOAT_NUMBER' => [
            'modelClass' =>
                BoatNumbers::class,

            'primaryKey' =>
                'id',

            'tokenMessage' =>
                'A valid Boat Number token is required.',

            'notFoundMessage' =>
                'Boat Number record was not found.',
        ],


        'BOAT_CANCEL' => [
            'modelClass' =>
                BoatNumberCancelRequests::class,

            'primaryKey' =>
                'id',

            'tokenMessage' =>
                'A valid Boat Number token is required.',

            'notFoundMessage' =>
                'Boat Number record was not found.',
        ],
    ];

    /*
     * Resolve tokenized processes before validating
     * the numeric ID.
     *
     * Tokenized upload URLs intentionally do not
     * include an id parameter.
     */
    if (isset($tokenizedProcesses[$process])) {
        $configuration =
            $tokenizedProcesses[$process];

        if ($token === '') {
            throw new BadRequestHttpException(
                Yii::t(
                    'app',
                    $configuration['tokenMessage']
                )
            );
        }

        $modelClass =
            $configuration['modelClass'];

        $primaryKey =
            $configuration['primaryKey'];

        /*
         * SecurityHelper validates that the encrypted
         * token belongs to the expected model class.
         */
        $parentId = SecurityHelper::decryptId(
            $token,
            $modelClass
        );

        $parentId = (int) $parentId;

        if ($parentId <= 0) {
            throw new BadRequestHttpException(
                Yii::t(
                    'app',
                    $configuration['tokenMessage']
                )
            );
        }

        /*
         * Confirm that the decrypted record still exists.
         */
        $parentExists = $modelClass::find()
            ->where([
                $primaryKey => $parentId,
            ])
            ->exists();

        if (!$parentExists) {
            throw new NotFoundHttpException(
                Yii::t(
                    'app',
                    $configuration['notFoundMessage']
                )
            );
        }

        return [
            'id' => $parentId,
            'tokenized' => true,
            'token' => $token,
            'process' => $process,
            'modelClass' => $modelClass,
        ];
    }

    /*
     * Existing non-tokenized processes continue
     * using the numeric ID.
     */
    $parentId = (int) $id;

    if ($parentId <= 0) {
        throw new BadRequestHttpException(
            Yii::t(
                'app',
                'A valid record ID is required.'
            )
        );
    }

    return [
        'id' => $parentId,
        'tokenized' => false,
        'token' => null,
        'process' => $process,
        'modelClass' => null,
    ];
}
}
