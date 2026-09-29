<?php

namespace backend\controllers;

use backend\models\ExportCompany;
use backend\models\ExportCompanySearch;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\User;
use Yii;
use backend\services\CommonService;

use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * ExportCompanyController implements the CRUD actions for ExportCompany model.
 */
class ExportCompanyController extends Controller
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
     * Lists all ExportCompany models.
     *
     * @return string
     */
    public function actionIndex()
    {
       if (
    !UserTypeUtil::hasType(Constant::ADMIN) &&
    !UserTypeUtil::hasType(Constant::MANAGEMENT)
) {
    throw new UnauthorizedHttpException(
        Yii::t('app', "You don't have permission to run this operation.")
    );
}
        $searchModel = new ExportCompanySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExportCompany model.
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
     * Finds the ExportCompany model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return ExportCompany the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findModel($id)
    {
        if (($model = ExportCompany::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Creates a new ExportCompany model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
                CommonService::validatePermission($this, "admin");


        $model = new ExportCompany();
        $model->district = 1;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExportCompany model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($user)
    {
                CommonService::validatePermission($this, "admin");

        $user = User::findOne($user);
        if ($user->profile_id == 0) {
            $model = new ExportCompany();

        } else {
            $model = $this->findModel($user->profile_id);
            $model->appication_types = explode(',', $model->appication_types);
        }
        $model->district = 1;

        if ($this->request->isPost && $model->load($this->request->post())) {
            if ($model->appication_types !== '') {
                $model->appication_types = implode(',', $model->appication_types);
            }
//            if ($user->profile_id == 0) {
//                $model->created = date("Y-m-d H:i");
//            }
//            $model->updated = date("Y-m-d H:i");
            $model->save();
            if ($user->profile_id == 0) {
                $user->profile_id = $model->id;
                $user->save();
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }


        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ExportCompany model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public
    function actionDelete($id)
    {
                CommonService::validatePermission($this, "admin");

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public static function getExportCompanyArray()
    {
        return ArrayHelper::map(ExportCompany::find()->where(["status" => 1])->orderBy("company_name")->asArray()->all(), 'id', function ($item) {
            return $item['company_name'] . ' [ BR:' . $item['br'] . ' ]';
        });
    }
}
