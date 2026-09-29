<?php

namespace backend\controllers;

use yii\data\ArrayDataProvider;
use kartik\export\ExportMenu;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\config\UserTypeUtil;
use backend\services\CommonService;
use backend\config\Constant;

use backend\models\ELogTemp;
use backend\models\ELogTempSearch;
use backend\models\ELogSetTemp;
use backend\models\ELogCatchTemp;
use backend\models\BoatNumbers;
use backend\models\MELogFishType;
use backend\models\MELogFishVariant;

use yii\web\NotFoundHttpException;

class ELogTempController extends Controller
{
    /**
     * Step 1: create/edit the trip-level ELogTemp record.
     * Step 2 (once the record has an id): show existing sets + the
     * "add set" form with retained-catch rows, using the same page.
     */

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            if (Yii::$app->user->isGuest) {
                                return false;
                            }
                            return UserTypeUtil::hasType(Constant::HARBOUR_OFFICER)
                                || UserTypeUtil::hasType(Constant::QUALITY_EXPORT_OFFICER)
                                || UserTypeUtil::hasType(Constant::AD_Highseas)
                                || UserTypeUtil::hasType(Constant::ITD);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-set'  => ['POST'],
                    'delete-elog' => ['POST'],
                    'approve'     => ['POST'],
                    'disapprove'  => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Grid listing of all ELogTemp records.
     */
    public function actionIndex()
    {
        $searchModel = new ELogTempSearch();

        $officerHarbour = null;
        // If you scope harbour officers to their own harbour, resolve it here, e.g.:
        // if (UserTypeUtil::hasType(Constant::HARBOUR_OFFICER)) {
        //     $officerHarbour = Yii::$app->user->identity->harbour_name ?? null;
        // }

       $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            Yii::$app->user->identity->officerProfile->harbour ?? null
        );
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Detail view of a single ELogTemp record, its sets, and their catches.
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $sets = ELogSetTemp::find()
            ->where(['elog_id' => $model->id])
            ->orderBy('set_number')
            ->all();

        // Eager-load catches + fish type/variant names for each set
        $setsData = [];
        foreach ($sets as $set) {
            $catches = ELogCatchTemp::find()
                ->where(['e_log_set_temp_id' => $set->id])
                ->all();

            $catchRows = [];
            foreach ($catches as $c) {
                $catchRows[] = [
                    'fish_type'    => $c->fishType->name ?? 'N/A',
                    'fish_variant' => $c->fishVariant->name ?? 'N/A',
                    'weight'       => $c->weight,
                    'fish_count'   => $c->fish_count,
                ];
            }

            $setsData[] = [
                'set' => $set,
                'catches' => $catchRows,
            ];
        }

        $isAdminUser = UserTypeUtil::hasType(Constant::AD_Highseas) || UserTypeUtil::hasType(Constant::ITD);
        $isHarbourOfficer = UserTypeUtil::hasType(Constant::HARBOUR_OFFICER);

        $withinEditWindow = false;
        if ($isHarbourOfficer && !empty($model->created_at)) {
            $withinEditWindow = (time() - strtotime($model->created_at)) < 86400;
        }
        $canEdit = $isAdminUser || $withinEditWindow;

        return $this->render('view', [
            'model' => $model,
            'setsData' => $setsData,
            'isAdminUser' => $isAdminUser,
            'isHarbourOfficer' => $isHarbourOfficer,
            'canEdit' => $canEdit,
        ]);
    }

    public function actionCreate($id = null)
    {
        if ($id !== null) {
            $model = $this->findModel($id);
        } else {
            $model = new ELogTemp();
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);
            return $this->redirect(['create', 'id' => $model->id]);
        }

        $setModel = null;
        $sets = [];
        $fishTypes = [];

        if (!$model->isNewRecord) {
            $setModel = new ELogSetTemp();
            $setModel->set_number = ELogSetTemp::find()
                ->where(['elog_id' => $model->id])
                ->count() + 1;

            $sets = ELogSetTemp::find()
                ->where(['elog_id' => $model->id])
                ->orderBy('set_number')
                ->all();

            $fishTypes = ArrayHelper::map(
                MELogFishType::find()->orderBy('name')->all(),
                'id',
                'name'
            );
        }

        return $this->render('create', [
            'model' => $model,
            'setModel' => $setModel,
            'sets' => $sets,
            'fishTypes' => $fishTypes,
        ]);
    }

    /**
     * AJAX endpoint for Select2 boat number search.
     * Returns at most 20 matching boats, with owner contact number.
     */
    public function actionBoatSearch($q = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = BoatNumbers::find()->with('owner0');

        if (!empty($q)) {
            $query->andWhere(['like', 'boat_number', $q]);
        }

        $boats = $query
            ->orderBy('boat_number')
            ->limit(20)
            ->all();

        $results = [];
        foreach ($boats as $boat) {
            $results[] = [
                'id' => $boat->boat_number,
                'text' => $boat->boat_number,
                'contact_no' => $boat->owner0->phone ?? '',
            ];
        }

        return ['results' => $results];
    }

    /**
     * AJAX endpoint for the Fish Type -> Fish Variant dropdown.
     */
    public function actionFishVariants($typeId = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (empty($typeId)) {
            return [];
        }

        $variants = MELogFishVariant::find()
            ->where(['fish_type_id' => $typeId])
            ->orderBy('name')
            ->all();

        $results = [];
        foreach ($variants as $variant) {
            $results[] = [
                'id' => $variant->id,
                'name' => $variant->name,
            ];
        }

        return $results;
    }

    /**
     * Saves a new set (with its retained-catch rows) against an
     * existing ELogTemp record.
     */
    public function actionAddSet($id)
    {
        $model = $this->findModel($id);

        $setModel = new ELogSetTemp();
        $post = Yii::$app->request->post();

        if ($setModel->load($post) && $setModel->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $setModel->save(false);

                $catchRows = $post['catches'] ?? [];
                foreach ($catchRows as $row) {
                    $catch = new ELogCatchTemp();
                    $catch->e_log_set_temp_id = $setModel->id;
                    $catch->fish_type_id = $row['fish_type_id'] ?? null;
                    $catch->fish_variant_id = $row['fish_variant_id'] ?? null;
                    $catch->weight = $row['weight'] ?? 0;
                    $catch->fish_count = $row['fish_count'] ?? 0;

                    if (!$catch->save()) {
                        throw new \Exception('Invalid catch row: ' . json_encode($catch->errors));
                    }
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Set saved successfully.');
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', [$e->getMessage()]);
            }
        } else {
            Yii::$app->session->setFlash('error', $setModel->getErrorSummary(true));
        }

        return $this->redirect(['create', 'id' => $model->id]);
    }

    /**
     * Edit an existing set from the view page (distinct from add-set on the create page).
     */
    public function actionEditSet($id, $elogId)
{
    $set = ELogSetTemp::findOne($id);
    $model = $this->findModel($elogId);

    if ($set === null || (int) $set->elog_id !== (int) $model->id) {
        throw new NotFoundHttpException('The requested set does not exist.');
    }

    $this->checkCanEdit($model);

    $post = Yii::$app->request->post();

    if ($set->load($post) && $set->validate()) {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $set->save(false);

            $catchRows = $post['catches'] ?? [];
            $submittedIds = [];

            foreach ($catchRows as $row) {
                $catchId = $row['id'] ?? null;

                if (!empty($catchId)) {
                    // Existing catch — update it
                    $catch = ELogCatchTemp::findOne($catchId);
                    if ($catch === null || (int) $catch->e_log_set_temp_id !== (int) $set->id) {
                        throw new \Exception("Catch #{$catchId} does not belong to this set.");
                    }
                } else {
                    // New catch — create it
                    $catch = new ELogCatchTemp();
                    $catch->e_log_set_temp_id = $set->id;
                }

                $catch->fish_type_id = $row['fish_type_id'] ?? null;
                $catch->fish_variant_id = $row['fish_variant_id'] ?? null;
                $catch->weight = $row['weight'] ?? 0;
                $catch->fish_count = $row['fish_count'] ?? 0;

                if (!$catch->save()) {
                    throw new \Exception('Invalid catch row: ' . json_encode($catch->errors));
                }

                $submittedIds[] = $catch->id;
            }

            // Delete any existing catches that were removed in the UI
            $existingIds = ELogCatchTemp::find()
                ->select('id')
                ->where(['e_log_set_temp_id' => $set->id])
                ->column();

            $toDelete = array_diff($existingIds, $submittedIds);
            if (!empty($toDelete)) {
                ELogCatchTemp::deleteAll(['id' => $toDelete]);
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Set updated successfully.');
            return $this->redirect(['view', 'id' => $model->id]);
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Failed to update set: ' . $e->getMessage());
        }
    } else if ($post) {
        Yii::$app->session->setFlash('error', $set->getErrorSummary(true));
    }

    return $this->render('edit-set', [
        'model' => $set,
        'elog'  => $model,
    ]);
}

    /**
     * Deletes a set (and its retained-catch rows) and returns to the create page.
     */
    public function actionDeleteSet($id, $elogId)
    {
        $set = ELogSetTemp::findOne($id);

        if ($set !== null) {
            ELogCatchTemp::deleteAll(['e_log_set_temp_id' => $set->id]);
            $set->delete();
            Yii::$app->session->setFlash('success', 'Set deleted.');
        }

        return $this->redirect(['create', 'id' => $elogId]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Delete the full ELogTemp record, its sets, and their catches.
     */
    public function actionDeleteElog($id)
    {
        $model = $this->findModel($id);
        $this->checkCanEdit($model);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $setIds = ELogSetTemp::find()
                ->select('id')
                ->where(['elog_id' => $model->id])
                ->column();

            if (!empty($setIds)) {
                ELogCatchTemp::deleteAll(['e_log_set_temp_id' => $setIds]);
                ELogSetTemp::deleteAll(['id' => $setIds]);
            }

            $model->delete();

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'E-Log deleted successfully.');
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Failed to delete E-Log: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        $model->approve = 1;
        $model->save(false);

        Yii::$app->session->setFlash('success', 'E-Log approved.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    public function actionDisapprove($id)
    {
        $model = $this->findModel($id);
        $model->approve = 0;
        $model->save(false);

        Yii::$app->session->setFlash('success', 'E-Log disapproved.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Mirrors the canEdit logic used in view/create:
     * - Admin / ITD: always allowed
     * - Harbour officer: only within 24 hours of creation
     */
    protected function checkCanEdit(ELogTemp $model)
    {
        $isAdminUser = UserTypeUtil::hasType(Constant::AD_Highseas) || UserTypeUtil::hasType(Constant::ITD);
        $isHarbourOfficer = UserTypeUtil::hasType(Constant::HARBOUR_OFFICER);

        $withinEditWindow = false;
        if ($isHarbourOfficer && !empty($model->created_at)) {
            $withinEditWindow = (time() - strtotime($model->created_at)) < 86400;
        }

        if (!($isAdminUser || $withinEditWindow)) {
            throw new ForbiddenHttpException('You are not allowed to edit this record. The 24 hour edit window may have expired.');
        }

        return true;
    }

    protected function findModel($id)
    {
        if (($model = ELogTemp::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested e-log does not exist.');
    }
}