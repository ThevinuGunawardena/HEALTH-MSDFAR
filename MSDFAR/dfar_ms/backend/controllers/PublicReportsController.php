<?php

namespace backend\controllers;

use backend\models\DepartureBoatsSearch;
use backend\models\DepartureSkipperSearch;
use yii\filters\VerbFilter;
use backend\components\Controller;


/**
 * OfficerController implements the CRUD actions for ProfileOfficer model.
 */
class PublicReportsController extends Controller
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


    public function actionBoatStatus()
    {
        $this->layout = "blank";
        $searchModel = new DepartureBoatsSearch();
        $dataProvider = $searchModel->searchDepartureCancel($this->request->queryParams);

//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);

        return $this->render('boat-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

        ]);
    }

    public function actionSkipperStatus()
    {
        $this->layout = "blank";
        $searchModel = new DepartureSkipperSearch();
        $dataProvider = $searchModel->searchDepartureCancel($this->request->queryParams);

//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);

        return $this->render('skipper-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

        ]);
    }


}
