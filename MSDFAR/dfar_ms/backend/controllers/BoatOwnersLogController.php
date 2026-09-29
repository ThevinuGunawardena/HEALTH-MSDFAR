<?php

namespace backend\controllers;

use backend\models\BoatNumberOwnersLog;
use backend\models\BoatNumberOwnersLogSearch;
use backend\models\BoatNumbers;
use backend\models\FishermanRegisterdBoatLicense;
use Yii;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * BoatOwnersLogController implements the CRUD actions for BoatNumberOwnersLog model.
 */
class BoatOwnersLogController extends Controller
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
     * Lists all BoatNumberOwnersLog models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BoatNumberOwnersLogSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BoatNumberOwnersLog model.
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
     * Creates a new BoatNumberOwnersLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate($boatNo)
    {
        $model = new BoatNumberOwnersLog();
        $activeRecords = BoatNumberOwnersLog::find()->where(['boat_number' => $boatNo])->asArray()->all();
        $boatNumber = BoatNumbers::find()->where(['boat_number' => $boatNo])->one();
        $boatNumberLength = $boatNumber->length;
        $boatDesignLength = "";
        if ($boatNumber->boatDesign != null) {
            $boatDesignLength = $boatNumber->boatDesign->length ?? "";
        }
        $boatRegistration = FishermanRegisterdBoatLicense::find()->where(["boat_number_id" => $boatNumber->id])->all();
        $boatRegAvailable = false;
        $boatRegDate = null;
        if ($boatRegistration != null) {
            $boatRegAvailable = true;
            $boatRegDate = $boatRegistration[0]->date_of_first_registration;

        }
        $model->boat_number = $boatNo;
        $model->added_by = Yii::$app->user->identity->id;
        if ($this->request->isPost) {
            if ($boatRegAvailable == true) {
                $firstRegDate = $this->request->post("BoatNumberOwnersLog")["first_reg_date"];
                if ($firstRegDate != null) {
                    foreach ($boatRegistration as $item) {
                        $item->date_of_first_registration = $firstRegDate;
                        $item->save(false);
                    }
                }
            }


            $boatlength = $this->request->post("BoatNumberOwnersLog")["boat_length"];
            if ($boatlength != null || $boatlength != 0) {
                $boatNumber->length = $boatlength;
                $boatNumber->save(false);
            } else {
                $boatNumber->length = null;
                $boatNumber->save(false);
            }
            if ($model->load($this->request->post())) {
                if ($model->name != "" && $model->nic != "" && $model->from_date != "" && $model->boat_number != "")
                    $model->save();
                return $this->redirect(['create', 'boatNo' => $boatNo]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'activeRecords' => $activeRecords,
            'boatRegAvailable' => $boatRegAvailable,
            'boatRegDate' => $boatRegDate,
            'boatDesignLength' => $boatDesignLength,
            'boatNumberLength' => $boatNumberLength,
        ]);
    }

    /**
     * Updates an existing BoatNumberOwnersLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BoatNumberOwnersLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BoatNumberOwnersLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return BoatNumberOwnersLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BoatNumberOwnersLog::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
