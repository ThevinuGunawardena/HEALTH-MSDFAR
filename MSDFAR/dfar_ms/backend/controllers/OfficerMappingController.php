<?php
namespace backend\controllers;

use Yii;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use backend\models\ProfileOfficer;
use backend\models\KpiDivision;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class OfficerMappingController extends Controller
{
    /**
     * Lists all officers for easy mapping management
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ProfileOfficer::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        // 💡 Clean, standard Yii2 Query Builder approach to fetch the master list
        $divisionsList = ArrayHelper::map(
            (new \yii\db\Query())
                ->select(['divisionId', 'divisionName'])
                ->from('kpi_divisions')
                ->all(),
            'divisionId',
            'divisionName'
        );

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'divisionsList' => $divisionsList, // This maps straight to your dropdowns now!
        ]);
    }
    /**
     * Inline AJAX or quick submission updates for assignments
     */
    public function actionAssignDivision($id)
    {
        $model = ProfileOfficer::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Officer profile not found.');
        }

        if (Yii::$app->request->isPost) {
            $divisionId = Yii::$app->request->post('kpi_division_id');
            $model->kpi_division_id = !empty($divisionId) ? (int)$divisionId : null;
            
            if ($model->save(false)) { // false bypasses validation if other unrelated profile data is missing
                Yii::$app->session->setFlash('success', "Successfully updated division mapping for {$model->first_name}.");
            } else {
                Yii::$app->session->setFlash('error', "Failed to update record mapping.");
            }
        }

        return $this->redirect(['index']);
    }
}

?>