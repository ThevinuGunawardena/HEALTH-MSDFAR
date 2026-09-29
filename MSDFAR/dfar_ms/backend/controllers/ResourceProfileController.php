<?php

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ResourceProfile;
use backend\models\ResourceProfileFishingVillages;
use backend\models\ResourceProfileLandingSites;
use backend\models\ResourceProfileBeachSites;
use backend\models\ResourceProfileIceFactories;
use backend\models\ResourceProfileExporters;
use backend\models\ResourceProfileBoatBuildingYards;
use backend\models\ResourceProfileDryFishManufactures;
use backend\models\ResourceProfileFisheriesCooperateSociety;
use backend\models\ResourceProfileFisheriesRuralSociety;
use backend\models\ResourceProfileGovernmentOffices;
use backend\models\ResourceProfileFisheriesRoads;
use backend\models\ResourceProfileOthers;
use backend\models\ResourceProfilePoliceStations;
use backend\models\ResourceProfileSpecialProjects;
use backend\models\ResourceProfileTraditionalFishing;
use backend\models\ResourceProfileGramaNiladariWasam;
use backend\models\MDivision;
use backend\models\ProfileOfficer;
use backend\services\CommonService;
use Yii;
use yii\filters\AccessControl;
use backend\components\Controller;

use yii\web\NotFoundHttpException;

class ResourceProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return UserTypeUtil::hasType(Constant::FI);
                        },
                    ],
                    [
                        'actions' => ['index', 'view', 'dashboard', 'dashboard-all', 'view-division', 'view-division-all', 'get-divisions-by-district', 'get-stats-ajax'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        CommonService::validateLoginUser($this);
        
        // If AD user, show dashboard for their district
        if (UserTypeUtil::hasType(Constant::AD)) {
            return $this->redirect(['dashboard']);
        }
        
        // If ADMIN or DG user, show dashboard for all districts
        if (UserTypeUtil::hasType(Constant::ADMIN) || UserTypeUtil::hasType(Constant::DG)) {
            return $this->redirect(['dashboard-all']);
        }
        
        // For FI users, show their own profile
        $userId = Yii::$app->user->identity->id;
        $model = ResourceProfile::find()->where(['user_id' => $userId])->one();
        
        if ($model === null) {
            // No profile exists, redirect to create form
            return $this->redirect(['create']);
        }
        
        // Profile exists, show the view page
        return $this->redirect(['view']);
    }

    public function actionView()
    {
        CommonService::validateLoginUser($this);
        
        // If AD user, redirect to dashboard
        if (UserTypeUtil::hasType(Constant::AD)) {
            return $this->redirect(['dashboard']);
        }
        
        // For FI users, show their own profile
        $userId = Yii::$app->user->identity->id;
        $model = ResourceProfile::find()->where(['user_id' => $userId])->one();
        
        if ($model === null) {
            throw new NotFoundHttpException('Resource Profile not found.');
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        CommonService::validateLoginUser($this);
        $userId = Yii::$app->user->identity->id;
        
        // Check if already exists
        $existing = ResourceProfile::find()->where(['user_id' => $userId])->one();
        if ($existing !== null) {
            return $this->redirect(['index']);
        }

        $model = new ResourceProfile();
        $model->user_id = $userId;
        $fishingVillages = [];
        $landingSites = [];
        $beachSites = [];
        $iceFactories = [];
        $exporters = [];
        $boatBuildingYards = [];
        $dryFishManufactures = [];
        $fisheriesCooperateSocieties = [];
        $fisheriesRuralSocieties = [];
        $governmentOffices = [];
        $fisheriesRoads = [];
        $others = [];
        $policeStations = [];
        $specialProjects = [];
        $traditionalFishings = [];
        $gramaNiladariWasams = [];

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Save related models
                    $relatedModelsConfig = $this->getRelatedModelsConfig();
                    foreach ($relatedModelsConfig as $postKey => $config) {
                        $data = Yii::$app->request->post($postKey, []);
                        $this->saveRelatedModels(
                            $config['class'],
                            $model->id,
                            $data,
                            $config['nameField'],
                            $config['extraFields'] ?? []
                        );
                    }

                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Resource Profile has been created successfully.');
                    return $this->redirect(['view']);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error saving Resource Profile: ' . $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
            'fishingVillages' => $fishingVillages,
            'landingSites' => $landingSites,
            'beachSites' => $beachSites,
            'iceFactories' => $iceFactories,
            'exporters' => $exporters,
            'boatBuildingYards' => $boatBuildingYards,
            'dryFishManufactures' => $dryFishManufactures,
            'fisheriesCooperateSocieties' => $fisheriesCooperateSocieties,
            'fisheriesRuralSocieties' => $fisheriesRuralSocieties,
            'governmentOffices' => $governmentOffices,
            'fisheriesRoads' => $fisheriesRoads,
            'others' => $others,
            'policeStations' => $policeStations,
            'specialProjects' => $specialProjects,
            'traditionalFishings' => $traditionalFishings,
            'gramaNiladariWasams' => $gramaNiladariWasams,
        ]);
    }

    public function actionUpdate($id)
{
    CommonService::validateLoginUser($this);
    $model = $this->findModel($id);

    // Load existing related models keyed by their ID
    $relatedModelsConfig = $this->getRelatedModelsConfig();
    $existingRelated = [];
    foreach ($relatedModelsConfig as $postKey => $config) {
        $existingRelated[$postKey] = $config['class']::find()
            ->where(['resource_profile_id' => $model->id])
            ->indexBy('id') // key by existing record ID
            ->all();
    }

    if ($model->load(Yii::$app->request->post())) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->save()) {
                // Upsert related models
                foreach ($relatedModelsConfig as $postKey => $config) {
                    $postData = Yii::$app->request->post($postKey, []);
                    $this->upsertRelatedModels(
                        $config['class'],
                        $model->id,
                        $postData,
                        $config['nameField'],
                        $config['extraFields'] ?? [],
                        $existingRelated[$postKey]
                    );
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Resource Profile has been updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Error updating Resource Profile: ' . $e->getMessage());
        }
    }

    // Render update view as before
    return $this->render('update', array_merge(
        ['model' => $model],
        $this->prepareRelatedModelsForView($model)
    ));
}

/**
 * Upsert related models: update existing, insert new, delete removed.
 * Validates related models and throws on validation errors.
 */
protected function upsertRelatedModels($modelClass, $profileId, $data, $nameField, $extraFields = [], $existingModels = [])
{
    // Collect IDs of submitted models
    $submittedIds = [];

    foreach ($data as $itemData) {
        // If ID exists in the submitted data, update
        if (!empty($itemData['id']) && isset($existingModels[$itemData['id']])) {
            $relatedModel = $existingModels[$itemData['id']];
        } else {
            // New record
            $relatedModel = new $modelClass();
            $relatedModel->resource_profile_id = $profileId;
        }

        if (!empty($itemData[$nameField])) {
            $relatedModel->$nameField = $itemData[$nameField];

            foreach ($extraFields as $field) {
                if (isset($itemData[$field])) {
                    $relatedModel->$field = $itemData[$field];
                }
            }

            if (!$relatedModel->save()) {
                $errors = $relatedModel->getFirstErrors();
                $errorParts = [];
                foreach ($errors as $attribute => $message) {
                    $errorParts[] = $attribute . ': ' . $message;
                }
                $errorSummary = empty($errorParts) ? 'Unknown validation error.' : implode(' ', $errorParts);
                throw new \RuntimeException('Validation failed for ' . $modelClass . ': ' . $errorSummary);
            }

            $submittedIds[] = $relatedModel->id;
        }
    }

    // Delete any existing models not submitted
    foreach ($existingModels as $existingId => $existingModel) {
        if (!in_array($existingId, $submittedIds)) {
            $existingModel->delete();
        }
    }
}

    /**
     * Prepare related models for the update view
     */
    protected function prepareRelatedModelsForView($model)
    {
        $relatedModelsConfig = $this->getRelatedModelsConfig();
        $result = [];
        foreach ($relatedModelsConfig as $key => $config) {
            $result[$key] = $config['class']::find()
                ->where(['resource_profile_id' => $model->id])
                ->all();
        }
        return $result;
    }

    protected function getDashboardStats(array $divisionIds)
    {
        return ResourceProfile::find()
            ->select([
                'SUM(fishing_population) as total_fishing_population',
                'SUM(fisheries_families) as total_fisheries_families',
                'SUM(active_fishermen) as total_active_fishermen',
                'SUM(fishing_households) as total_fishing_households',
                'SUM(fishing_households_without_sanitary) as total_fishing_households_without_sanitary',
                'SUM(fishing_households_without_drinking_water) as total_fishing_households_without_drinking_water',
            ])
            ->where(['m_division_id' => $divisionIds])
            ->asArray()
            ->one();
    }


    public function actionDashboard()
    {
        CommonService::validateLoginUser($this);
        
        // Only allow AD users
        if (!UserTypeUtil::hasType(Constant::AD)) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to access this page.');
        }
        
        // Get AD's district
        $adDistrict = null;
        if (isset(Yii::$app->user->identity->profile_id)) {
            $profile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);
            if ($profile && $profile->district) {
                $adDistrict = $profile->district;
            }
        }
        
        if (!$adDistrict) {
            throw new \yii\web\ForbiddenHttpException('No district assigned to your profile.');
        }
        
        // Get all divisions in the AD's district
        $divisions = MDivision::find()
            ->where(['district_id' => $adDistrict, 'status' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->all();
        
        // Get aggregated statistics for all divisions in the district
        $divisionIds = array_map(function($div) { return $div->id; }, $divisions);
        
        $stats = $this->getDashboardStats($divisionIds);
        
        return $this->render('dashboard', [
            'divisions' => $divisions,
            'stats' => $stats,
        ]);
    }

    public function actionDashboardAll($districtId = null, $divisionId = null)
    {
        CommonService::validateLoginUser($this);
        
        // Only allow ADMIN and DG users
        if (!UserTypeUtil::hasType(Constant::ADMIN) && !UserTypeUtil::hasType(Constant::DG)) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to access this page.');
        }
        
        // If both district and division are selected, redirect to view-division
        if ($districtId && $divisionId) {
            return $this->redirect(['view-division-all', 'id' => $divisionId]);
        }
        
        // Get all districts
        $districts = \backend\models\MFiDistrict::find()
            ->where(['status' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->all();
        
        // Get divisions based on selected district
        $divisions = [];
        $divisionIds = [];
        
        if ($districtId) {
            // Get divisions for selected district
            $divisions = MDivision::find()
                ->where(['district_id' => $districtId, 'status' => 1])
                ->orderBy(['name' => SORT_ASC])
                ->all();
            $divisionIds = array_map(function($div) { return $div->id; }, $divisions);
        } else {
            // Get all divisions from all districts
            $allDivisions = MDivision::find()
                ->where(['status' => 1])
                ->orderBy(['name' => SORT_ASC])
                ->all();
            $divisionIds = array_map(function($div) { return $div->id; }, $allDivisions);
        }
        
        // Get aggregated statistics
        $stats = $this->getDashboardStats($divisionIds);
        
        return $this->render('dashboard-all', [
            'districts' => $districts,
            'divisions' => $divisions,
            'stats' => $stats,
            'selectedDistrictId' => $districtId,
            'selectedDivisionId' => $divisionId,
        ]);
    }

    public function actionViewDivision($id)
    {
        CommonService::validateLoginUser($this);
        
        // Only allow AD users
        if (!UserTypeUtil::hasType(Constant::AD)) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to access this page.');
        }
        
        // Verify the division belongs to AD's district
        $adDistrict = null;
        if (isset(Yii::$app->user->identity->profile_id)) {
            $profile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);
            if ($profile && $profile->district) {
                $adDistrict = $profile->district;
            }
        }
        
        $division = MDivision::findOne(['id' => $id, 'district_id' => $adDistrict, 'status' => 1]);
        if (!$division) {
            throw new NotFoundHttpException('Division not found or not accessible.');
        }
        
        // Get the resource profile for this division
        $model = ResourceProfile::find()->where(['m_division_id' => $id])->one();
        
        if ($model === null) {
            throw new NotFoundHttpException('Resource Profile not found for this division.');
        }
        
        return $this->render('view', [
            'model' => $model,
            'isDashboardView' => true,
        ]);
    }

    public function actionViewDivisionAll($id)
    {
        CommonService::validateLoginUser($this);
        
        // Only allow ADMIN and DG users
        if (!UserTypeUtil::hasType(Constant::ADMIN) && !UserTypeUtil::hasType(Constant::DG)) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to access this page.');
        }
        
        $division = MDivision::findOne(['id' => $id, 'status' => 1]);
        if (!$division) {
            throw new NotFoundHttpException('Division not found or not accessible.');
        }
        
        // Get the resource profile for this division
        $model = ResourceProfile::find()->where(['m_division_id' => $id])->one();
        
        if ($model === null) {
            throw new NotFoundHttpException('Resource Profile not found for this division.');
        }
        
        return $this->render('view', [
            'model' => $model,
            'isDashboardView' => true,
        ]);
    }

    public function actionGetDivisionsByDistrict($districtId)
    {
        CommonService::validateLoginUser($this);
        
        // Only allow ADMIN and DG users
        if (!UserTypeUtil::hasType(Constant::ADMIN) && !UserTypeUtil::hasType(Constant::DG)) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to access this page.');
        }
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if (empty($districtId)) {
            return ['divisions' => []];
        }
        
        $divisions = MDivision::find()
            ->select(['id', 'name'])
            ->where(['district_id' => $districtId, 'status' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
        
        return ['divisions' => $divisions];
    }

    public function actionGetStatsAjax($districtId = null)
    {
        CommonService::validateLoginUser($this);
        
        // Only allow ADMIN and DG users
        if (!UserTypeUtil::hasType(Constant::ADMIN) && !UserTypeUtil::hasType(Constant::DG)) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['error' => 'Unauthorized'];
        }
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        // Get divisions based on selected district
        $divisionIds = [];
        
        if ($districtId) {
            // Get divisions for selected district
            $divisions = MDivision::find()
                ->where(['district_id' => $districtId, 'status' => 1])
                ->orderBy(['name' => SORT_ASC])
                ->all();
            $divisionIds = array_map(function($div) { return $div->id; }, $divisions);
        } else {
            // Get all divisions from all districts
            $allDivisions = MDivision::find()
                ->where(['status' => 1])
                ->orderBy(['name' => SORT_ASC])
                ->all();
            $divisionIds = array_map(function($div) { return $div->id; }, $allDivisions);
        }
        
        // Get aggregated statistics
        $stats = $this->getDashboardStats($divisionIds);
        
        return ['stats' => $stats];
    }

    public function actionDelete($id)
    {
        CommonService::validateLoginUser($this);
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Resource Profile has been deleted successfully.');
        return $this->redirect(['index']);
    }

    protected function getRelatedModelsConfig()
    {
        return [
            'fishingVillages' => [
                'class' => ResourceProfileFishingVillages::className(),
                'nameField' => 'village_name'
            ],
            'landingSites' => [
                'class' => ResourceProfileLandingSites::className(),
                'nameField' => 'landing_site_name'
            ],
            'beachSites' => [
                'class' => ResourceProfileBeachSites::className(),
                'nameField' => 'beach_site_name'
            ],
            'iceFactories' => [
                'class' => ResourceProfileIceFactories::className(),
                'nameField' => 'ice_factory_name'
            ],
            'exporters' => [
                'class' => ResourceProfileExporters::className(),
                'nameField' => 'exporter_name'
            ],
            'boatBuildingYards' => [
                'class' => ResourceProfileBoatBuildingYards::className(),
                'nameField' => 'yard_name'
            ],
            'dryFishManufactures' => [
                'class' => ResourceProfileDryFishManufactures::className(),
                'nameField' => 'manufacture_name'
            ],
            'fisheriesCooperateSocieties' => [
                'class' => ResourceProfileFisheriesCooperateSociety::className(),
                'nameField' => 'cooperate_society_name'
            ],
            'fisheriesRuralSocieties' => [
                'class' => ResourceProfileFisheriesRuralSociety::className(),
                'nameField' => 'rural_society_name'
            ],
            'governmentOffices' => [
                'class' => ResourceProfileGovernmentOffices::className(),
                'nameField' => 'office_name'
            ],
            'fisheriesRoads' => [
                'class' => ResourceProfileFisheriesRoads::className(),
                'nameField' => 'road_name'
            ],
            'others' => [
                'class' => ResourceProfileOthers::className(),
                'nameField' => 'other_name'
            ],
            'policeStations' => [
                'class' => ResourceProfilePoliceStations::className(),
                'nameField' => 'police_station_name',
                'extraFields' => ['phone_number']
            ],
            'specialProjects' => [
                'class' => ResourceProfileSpecialProjects::className(),
                'nameField' => 'project_name',
                'extraFields' => ['project_description']
            ],
            'traditionalFishings' => [
                'class' => ResourceProfileTraditionalFishing::className(),
                'nameField' => 'traditional_fishing_name',
                'extraFields' => ['traditional_fishing_description']
            ],
            'gramaNiladariWasams' => [
                'class' => ResourceProfileGramaNiladariWasam::className(),
                'nameField' => 'wasam_name'
            ],
        ];
    }

    protected function saveRelatedModels($modelClass, $profileId, $data, $nameField, $extraFields = [])
    {
        if (empty($data) || !is_array($data)) {
            return;
        }

        foreach ($data as $itemData) {
            if (!empty($itemData[$nameField])) {
                $relatedModel = new $modelClass();
                $relatedModel->resource_profile_id = $profileId;
                $relatedModel->$nameField = $itemData[$nameField];
                
                foreach ($extraFields as $field) {
                    if (isset($itemData[$field])) {
                        $relatedModel->$field = $itemData[$field];
                    }
                }
                
                if (!$relatedModel->save()) {
                    $errors = $relatedModel->getFirstErrors();
                    $errorParts = [];
                    foreach ($errors as $attribute => $message) {
                        $errorParts[] = $attribute . ': ' . $message;
                    }
                    $errorSummary = !empty($errorParts) ? implode('; ', $errorParts) : 'Unknown validation error.';
                    throw new \RuntimeException('Validation failed for ' . $modelClass . ': ' . $errorSummary);
                }
            }
        }
    }

    protected function findModel($id)
    {
        $userId = Yii::$app->user->identity->id;
        if (($model = ResourceProfile::findOne(['id' => $id, 'user_id' => $userId])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}