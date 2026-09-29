<?php

namespace backend\controllers;

use Yii;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use backend\models\Kpi;
use backend\models\KpiSearch;
use yii\web\Response;
use yii\db\Query;
use backend\models\KpiDivisions;
use backend\models\ProfileOfficer;
use yii\helpers\ArrayHelper;

class KpiController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Kpi models with search filtering.
     */
    public function actionIndex()
    {
        $searchModel = new KpiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Kpi model.
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Kpi model based on its primary key value.
     */
    protected function findModel($id)
    {
        if (($model = Kpi::findOne(['KPIId' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Creates a new Kpi model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    // public function actionCreate()
    // {
    //     $model = new Kpi();

    //     if ($model->load(Yii::$app->request->post())) {
    //         if ($model->save()) {
    //             Yii::$app->session->setFlash('success', 'KPI created successfully.');
    //             return $this->redirect(['view', 'id' => $model->KPIId]);
    //         } else {
    //             // debug capture tool: This prints validation errors to the top of your screen if it fails
    //             Yii::$app->session->setFlash('error', 'Validation Failed: ' . json_encode($model->getErrors()));
    //         }
    //     }

    //     return $this->render('create', [
    //         'model' => $model,
    //     ]);
    // }
    public function actionCreate()
    {
        $model = new Kpi();

        // Fetch divisions from the new kpi_divisions table
        $divisions = ArrayHelper::map(
            KpiDivisions::find()->all(), 
            'divisionId', 
            'divisionName'
        );

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->KPIId]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'divisions' => $divisions,
        ]);
    }

    /**
     * Fetches users belonging to a selected division by joining user and profile_officer tables.
     * Maps perfectly to the jQuery $.get requirement in your _form.php.
     * * @param int $divisionId
     * @return array
     */
    // public function actionGetUsersByDivision($divisionId)
    // {
    //     // Force Yii to return clean application data instead of an HTML layout page wrapper
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     try {
    //         // query the user table and join with your profile_officer model
    //         $users = (new Query())
    //             ->select([
    //                 'userEmpNo' => 'u.id', // Maps to the value attribute in your option tags
    //                 'empNameWithInitials' => "CONCAT(p.first_name, ' ', p.last_name)" // Combines both strings for display
    //             ])
    //             ->from('user u')
    //             ->innerJoin('profile_officer p', 'u.profile_id = p.id')
    //             ->where(['p.division' => $divisionId]) // Match your actual DB column name: 'division'
    //             ->all();

    //         return $users;

    //     } catch (\Exception $e) {
    //         // If it crashes, this helps you read what went wrong in your browser console developer tools
    //         Yii::$app->response->statusCode = 500;
    //         return ['error' => $e->getMessage()];
    //     }
    // }

    /**
     * Fetches filtered users matching the selected divisionId
     * @param int $divisionId
     * @return array
     */
    public function actionGetOfficersByDivision($divisionId)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
       $officers = ProfileOfficer::find()
        ->select([
            'id' => 'MIN(user.id)',
            'name' => "CONCAT(profile_officer.first_name, ' ', profile_officer.last_name)"
        ])
        ->innerJoin('user', 'user.profile_id = profile_officer.id')
        ->where(['profile_officer.kpi_division_id' => $divisionId])
        ->groupBy([
            'profile_officer.id',
            'profile_officer.first_name',
            'profile_officer.last_name'
        ])
        ->orderBy(['id' => SORT_ASC])
        ->asArray()
        ->all();

        return ArrayHelper::map($officers, 'id', 'name');
    }
    /**
     * Updates an existing Kpi model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    // public function actionUpdate($id)
    // {
    //     $model = $this->findModel($id);

    //     // Fetch active officers to populate dropdown selections
    //     $officers = \backend\models\ProfileOfficer::find()
    //         ->select(["user.id", "CONCAT(profile_officer.first_name, ' ', profile_officer.last_name) AS full_name"])
    //         ->innerJoin('user', 'user.profile_id = profile_officer.id')
    //         ->asArray()
    //         ->all();

    //     // Map the array into a clean [id => 'First Last'] key-value pair format
    //     $officerList = \yii\helpers\ArrayHelper::map($officers, 'id', 'full_name');

    //     // Fetch divisions for selection mapping
    //     $divisions = \yii\helpers\ArrayHelper::map(
    //         \backend\models\MDivision::find()->asArray()->all(), // Adjust namespace if your model is elsewhere
    //         'id', 
    //         'name'
    //     );

    //     if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
    //         Yii::$app->session->setFlash('success', Yii::t('app', 'KPI updated successfully.'));
    //         return $this->redirect(['view', 'id' => $model->KPIId]);
    //     }

    //     return $this->render('update', [
    //         'model' => $model,
    //         'officerList' => $officerList,
    //         'divisions' => $divisions,
    //     ]);
    // }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // 💡 FIX HERE: Query the new kpi_divisions table to provide the correct array map data
        $divisions = \yii\helpers\ArrayHelper::map(
            \backend\models\KpiDivisions::find()->orderBy(['divisionName' => SORT_ASC])->all(), 
            'divisionId', 
            'divisionName'
        );

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->KPIId]);
        }

        return $this->render('update', [
            'model' => $model,
            'divisions' => $divisions,
        ]);
    }

    public function actionAssign($id)
    {
        $model = $this->findModel($id);

        // 💡 FIX HERE: Query the new kpi_divisions table to provide the correct array map data
        $divisions = \yii\helpers\ArrayHelper::map(
            \backend\models\KpiDivisions::find()->orderBy(['divisionName' => SORT_ASC])->all(), 
            'divisionId', 
            'divisionName'
        );

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Responsible officer updated successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('assign', [
            'model' => $model,
            'officerList' => [], // Fed automatically on client load through initial state query mapping
            'divisions' => $divisions,
        ]);
    }

    // public function actionProgress($id)
    // {
    //     $model = $this->findModel($id);

    //     if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
    //         Yii::$app->session->setFlash('success', 'Progress performance updated successfully.');
    //         return $this->redirect(['index']);
    //     }

    //     return $this->render('progress', [
    //         'model' => $model,
    //         'officerList' => [],
    //         'divisions' => [],
    //     ]);
    // }

    public function actionProgress($id)
    {
        $model = $this->findModel($id);

        // 💡 Fetch active division maps from the correct kpi_divisions table
        $divisions = \yii\helpers\ArrayHelper::map(
            \backend\models\KpiDivisions::find()->orderBy(['divisionName' => SORT_ASC])->all(), 
            'divisionId', 
            'divisionName'
        );

        if ($this->request->isPost && $model->load($this->request->post())) {
            // Optional: Add auto-status transition checking here if needed 
            // (e.g., if progress >= target, set status to 'Achieved')
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Progress performance updated successfully.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('progress', [
            'model' => $model,
            'divisions' => $divisions, // Passed smoothly to prevent undefined variable errors
            'officerList' => [],       // Maintained as an empty fallback array for form safety
        ]);
    }
    
    /**
     * Deletes an existing Kpi model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'KPI configuration deleted successfully.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to delete the selected KPI.'));
        }

        return $this->redirect(['index']);
    }
}