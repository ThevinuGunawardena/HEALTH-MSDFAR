<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\AuthAssignment;
use backend\models\AuthItem;
use backend\models\SignupForm;
use backend\models\User;
use backend\models\UserSearch;
use backend\services\CommonService;
use backend\services\Util;
use common\components\WebUser;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'except' => [],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        CommonService::validatePermission($this, "admin");

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionImpersonate($id)
    {
        if (!UserTypeUtil::hasType(Constant::ADMIN)) {
            throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));
        }
        /** @var WebUser $webUser */
        $webUser = Yii::$app->getUser();

        $mainIdentityId = $webUser->getMainIdentityId();

        if ($mainIdentityId != $id) {
            /** @var User $user */
//            $user = User::findOne($fishermanId);

            $webUser->login($this->getUser($id), $duration = 0);
            $webUser->setMainIdentityId($mainIdentityId);

            CommonService::addImpersonateLog($id, $mainIdentityId, "Stared");
        }

        return $this->redirect(['/site/index']);
    }

    protected function getUser($id)
    {
        $user = \common\models\User::findById($id);
//        print_r($user);exit();

//        print_r($user);exit();

        return $user;
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionCompanyIndex()
    {
        CommonService::validatePermission($this, "admin");

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->companySearch($this->request->queryParams);

        return $this->render('companyindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndexFisherman()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->searchFisherman($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        CommonService::validatePermission($this, "admin");
        $transaction = Yii::$app->db->beginTransaction();
        $model = new SignupForm();
        if (Util::editPermission() && $model->load(Yii::$app->request->post()) && $user = $model->signupOfficers()) {
            $authItem = AuthItem::findOne($model->user_role);
            if (isset($authItem)) {
                $auth = new AuthAssignment();
                $auth->user_id = $user->id;
                $auth->item_name = $authItem->name;
                if ($auth->save()) {
                    $transaction->commit();
                    return $this->redirect("index");
                }
            } else {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'This user type is not available');

            }
        } else {
            $transaction->rollBack();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionCreatecompanyuser()
    {
        CommonService::validatePermission($this, "admin");
        $transaction = Yii::$app->db->beginTransaction();
        $model = new SignupForm();
        $model->type = Constant::EXPORT_COMPANY;
        $model->user_role = "SPECIAL_LICENSE";
        $model->user_permission = 1;
        if (Util::editPermission() && $model->load(Yii::$app->request->post()) && $user = $model->signupOfficers()) {
            $authItem = AuthItem::findOne($model->user_role);
            if (isset($authItem)) {
                $auth = new AuthAssignment();
                $auth->user_id = $user->id;
                $auth->item_name = $authItem->name;
                if ($auth->save()) {
                    $transaction->commit();
                    return $this->redirect("company-index");
                }
            } else {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'This user type is not available');

            }
        } else {
            $transaction->rollBack();
        }

        return $this->render('companyusercreate', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPasswordReset($id)
    {
        CommonService::validatePermission($this, "admin");
        $modelData = $this->findModel($id);
        if ($modelData->type == Constant::ADMIN) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $model = new SignupForm();
        if (Util::editPermission() && $this->request->isPost && $model->load($this->request->post()) && $model->resetPw($id)) {
            return $this->redirect(['view', 'id' => $modelData->id]);
        }

        return $this->render('pwreset', [
            'model' => $model,
            'modelData' => $modelData,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->type == Constant::ADMIN) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        $signUpModel = new SignupForm();
        $signUpModel->nic = $model->nic;
        $signUpModel->email = $model->email;
        $signUpModel->user_permission = $model->user_permission;
        $AUTHiTEMS = AuthItem::find()->select(["name"])->where(['type' => 0])->orWhere(['type' => 1])->asArray()->all();
        $items = [];
        foreach ($AUTHiTEMS as $AUTHiTEM) {
            $items[] = $AUTHiTEM['name'];
        }

        $auth = AuthAssignment::find()->where(['user_id' => $id])->andWhere(['in', 'item_name', $items])->one();

//        print_r($AUTHiTEMS);exit();
        $signUpModel->user_role = $auth->item_name ?? "";

        $types = explode(',', $model->type); // E.g., "option1,option2,option3" -> ['option1', 'option2', 'option3']
        $signUpModel->type = $types[0]; // Assign first element, e.g., 'option1'
        if (sizeof($types) > 1) { // Corrected condition
            array_shift($types); // Remove first element, leaving ['option2', 'option3']
            $signUpModel->secondary = implode(',', $types); // Join as string, e.g., "option2,option3"
//            $items = array_combine($types, $types); // Create key-value pairs for dropdown
        }
//        $signUpModel->type = $model->type;
        if (Util::editPermission() && $this->request->isPost && $signUpModel->load($this->request->post())) {

            $model->nic = $signUpModel->nic;
            $model->email = $signUpModel->email;
            $model->user_permission = $signUpModel->user_permission;
            $model->type = $signUpModel->type;
            if ($signUpModel->secondary) {
                $model->type = $signUpModel->type . ',' . implode(',', $signUpModel->secondary);
            } else {
                $model->type = $signUpModel->type;
            }

//            print_r($model->type);exit();
            $auth = AuthAssignment::find()->where(['user_id' => $id])->andWhere(['in', 'item_name', $items])->one();
            if (isset($auth)) {
                $auth->item_name = $signUpModel->user_role;
            } else {
                $auth = new AuthAssignment();
                $auth->user_id = $id;
                $auth->item_name = $signUpModel->user_role;

            }
            $auth->validate();

//            $auth->save();
            if ($model->save() && $auth->save()) {

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $signUpModel,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
                CommonService::validatePermission($this, "admin");

        if (Util::editPermission()) {
            $model = $this->findModel($id);
            $model->status = 9;
            if ($model->type == Constant::ADMIN) {
                throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
            }
            $model->save();
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
