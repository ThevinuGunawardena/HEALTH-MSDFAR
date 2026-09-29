<?php

namespace backend\controllers;
use backend\services\CommonService;
use Yii;
use backend\models\ApiKeyPermissions;
use backend\models\ApiKeyPermissionsSearch;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ApiKeyPermissionsController implements the CRUD actions for ApiKeyPermissions model.
 */
class ApiKeyPermissionsController extends Controller
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
     * Lists all ApiKeyPermissions models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this, "admin");

        $searchModel = new ApiKeyPermissionsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ApiKeyPermissions model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        CommonService::validatePermission($this, "admin");
 
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ApiKeyPermissions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
{
    CommonService::validatePermission($this, "admin");

    $model = new ApiKeyPermissions();
    $model->status = 1;

    if (Yii::$app->request->isPost) {

        $post = Yii::$app->request->post('ApiKeyPermissions');

        $apiKeyId = $post['api_key_id'] ?? null;
        $status = $post['status'] ?? 1;
        $apiEndpointIds = $post['api_endpoint_ids'] ?? [];

        if (empty($apiKeyId)) {
            Yii::$app->session->setFlash('error', 'Please select API key.');

            return $this->render('create', [
                'model' => $model,
            ]);
        }

        if (empty($apiEndpointIds)) {
            Yii::$app->session->setFlash('error', 'Please select at least one allowed API.');

            return $this->render('create', [
                'model' => $model,
            ]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($apiEndpointIds as $apiEndpointId) {

                $apiEndpointId = (int)$apiEndpointId;

                $existingPermission = ApiKeyPermissions::find()
                    ->where([
                        'api_key_id' => $apiKeyId,
                        'api_endpoint_id' => $apiEndpointId,
                    ])
                    ->one();

                if ($existingPermission) {
                    $existingPermission->status = (int)$status;
                    $existingPermission->updated_at = date('Y-m-d H:i:s');

                    if (!$existingPermission->save(false)) {
                        throw new \Exception('Failed to update permission.');
                    }
                } else {
                    $permission = new ApiKeyPermissions();
                    $permission->api_key_id = (int)$apiKeyId;
                    $permission->api_endpoint_id = $apiEndpointId;
                    $permission->status = (int)$status;
                    $permission->created_at = date('Y-m-d H:i:s');

                    if (!$permission->save(false)) {
                        throw new \Exception('Failed to save permission.');
                    }
                }
            }

            $transaction->commit();

            Yii::$app->session->setFlash('success', 'API permissions saved successfully.');

            return $this->redirect(['index']);

        } catch (\Throwable $e) {
            $transaction->rollBack();

            Yii::$app->session->setFlash('error', $e->getMessage());
        }
    }

    return $this->render('create', [
        'model' => $model,
    ]);
}
    /**
     * Updates an existing ApiKeyPermissions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        CommonService::validatePermission($this, "admin");

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ApiKeyPermissions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        CommonService::validatePermission($this, "admin");

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ApiKeyPermissions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ApiKeyPermissions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        CommonService::validatePermission($this, "admin");

        if (($model = ApiKeyPermissions::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
