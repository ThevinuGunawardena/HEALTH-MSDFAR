<?php

namespace backend\controllers;


use backend\models\DistrictGearTypes;
use backend\models\DistrictGearTypesSearch;
use backend\models\ProfileOfficer;
use Yii;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * DistrictGearTypesController implements the CRUD actions for DistrictGearTypes model.
 */
class DistrictGearTypesController extends Controller
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
     * Lists all DistrictGearTypes models.
     *
     * @return string
     */
    public function actionIndex()
    {
//        CommonService::validatePermission($this,"FI");

        $searchModel = new DistrictGearTypesSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DistrictGearTypes model.
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
     * Creates a new DistrictGearTypes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new DistrictGearTypes();
        $profile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                if ($model->fishing_time_periods !== '') {
                    $model->fishing_time_periods = implode(',', $model->fishing_time_periods);
                }
                if ($model->fishing_time_durations !== '') {
                    $model->fishing_time_durations = implode(',', $model->fishing_time_durations);
                }
                if ($model->fish_species !== '') {
                    $model->fish_species = implode(',', $model->fish_species);

                }
                $model->extra =json_encode($this->request->post("extra") ?? "");
                $model->division = $profile->division;
                if ($model->save())
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
     * Updates an existing DistrictGearTypes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->fishing_time_periods = explode(',', $model->fishing_time_periods);
        $model->fishing_time_durations = explode(',', $model->fishing_time_durations);
        $model->fish_species = explode(',', $model->fish_species);
        if ($model->load($this->request->post())) {
            if ($model->fishing_time_periods !== '') {
                $model->fishing_time_periods = implode(',', $model->fishing_time_periods);
            }
            if ($model->fishing_time_durations !== '') {
                $model->fishing_time_durations = implode(',', $model->fishing_time_durations);
            }
            if ($model->fish_species !== '') {
                $model->fish_species = implode(',', $model->fish_species);

            }
            $model->extra =json_encode($this->request->post("extra") ?? "");
            //            $model->district_id = $profile->district;
            if ($model->save())
                return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DistrictGearTypes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
//        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DistrictGearTypes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return DistrictGearTypes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DistrictGearTypes::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
