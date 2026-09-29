<?php

namespace backend\controllers;
use yii\data\ArrayDataProvider;
use kartik\export\ExportMenu;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use Yii;
use backend\components\Controller;

use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\config\UserTypeUtil;
use backend\services\CommonService;
use backend\config\Constant;
use backend\models\ELogSearch;
use backend\models\ELogReportSearch;
use backend\models\ELog;
use backend\models\ELogLongline;
use backend\models\ELogGillnet;
use backend\models\ELogRingnet;
use backend\models\ELogSets;
use backend\models\MHarbours;
use backend\models\ELogSetCatch;
use backend\models\MELogFishType;
use backend\models\MELogFishVariant;
use backend\models\DepartureRequests;
use backend\models\ELogSetDiscardedDead;
use backend\models\ELogSetDiscardedLive;


class ELogViewController extends Controller
{
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
                    'delete' => ['POST'],
                    'delete-longline' => ['POST'],
                    'delete-gillnet' => ['POST'],
                    'delete-ringnet' => ['POST'],
                    'delete-set' => ['POST'],
                    'delete-elog' => ['POST'],
                ],
            ],
        ]);
    }

    /* =========================
     * INDEX
     * ========================= */
    public function actionIndex()
    {
        $searchModel = new ELogSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams,
            Yii::$app->user->identity->officerProfile->harbour ?? null
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReport()
{
    $searchModel  = new ELogReportSearch();
    $dataProvider = $searchModel->search(
        Yii::$app->request->queryParams,
        Yii::$app->user->identity->officerProfile->harbour ?? null
    );

    return $this->render('report', [
        'searchModel'  => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}


    /* =========================
     * VIEW
     * ========================= */
    public function actionView($id)
    {
        $eLog = ELog::findOne($id);

        if ($eLog === null) {
            throw new \yii\web\NotFoundHttpException('E-Log not found.');
        }

        $longlines = ELogLongline::find()->where(['e_log_id' => $id])->all();
        $gillnets = ELogGillnet::find()->where(['e_log_id' => $id])->all();
        $ringnets = ELogRingnet::find()->where(['e_log_id' => $id])->all();

        // Build gearDetails keyed by gear_type => [ gear_id => attributes ]
        $gearDetails = ['longline' => [], 'gillnet' => [], 'ringnet' => []];

        foreach ($longlines as $g) {
            $gearDetails['longline'][$g->id] = $g->attributes;
        }
        foreach ($gillnets as $g) {
            $gearDetails['gillnet'][$g->id] = $g->attributes;
        }
        foreach ($ringnets as $g) {
            $gearDetails['ringnet'][$g->id] = $g->attributes;
        }

        // Takes full model array — guarantees a row per gear even with no sets
        $mapSets = function (array $gearModels, string $gearType) {
            $result = [];

            foreach ($gearModels as $gear) {
                $sets = ELogSets::find()
                    ->where(['gear_id' => $gear->id, 'gear_type' => $gearType])
                    ->with(['catches', 'discardedDead', 'discardedLive'])
                    ->all();

                if (empty($sets)) {
                    // Placeholder so the gear card still renders
                    $result[] = [
                        'set_id' => null,
                        'gear_id' => $gear->id,
                        'gear_type' => $gearType,
                        'set_number' => null,
                        'start_datetime' => null,
                        'end_datetime' => null,
                        'start_gps_direction' => null,
                        'start_gps_n' => null,
                        'start_gps_e' => null,
                        'end_gps_direction' => null,
                        'end_gps_n' => null,
                        'end_gps_e' => null,
                        'catches' => [],
                        'discarded_dead' => [],
                        'discarded_live' => [],
                    ];
                } else {
                    foreach ($sets as $set) {
                        $result[] = [
                            'set_id' => $set->id,
                            'gear_id' => $gear->id,
                            'gear_type' => $gearType,
                            'set_number' => $set->set_number,
                            'start_datetime' => $set->start_datetime,
                            'end_datetime' => $set->end_datetime,
                            'start_gps_direction' => $set->start_gps_direction,
                            'start_gps_n' => $set->start_gps_n,
                            'start_gps_e' => $set->start_gps_e,
                            'end_gps_direction' => $set->end_gps_direction,
                            'end_gps_n' => $set->end_gps_n,
                            'end_gps_e' => $set->end_gps_e,
                            'catches' => array_map(fn($c) => [
                                'fish_type' => $c->fish_type_id,
                                'variant' => $c->fish_variant_id,
                                'weight' => $c->weight,
                                'count' => $c->fish_count,
                            ], $set->catches),
                            'discarded_dead' => array_map(fn($d) => [
                                'fish_type' => $d->fish_type_id,
                                'variant' => $d->fish_variant_id,
                                'weight' => $d->weight ?? null,
                                'count' => $d->fish_count ?? null,
                            ], $set->discardedDead),
                            'discarded_live' => array_map(fn($l) => [
                                'fish_type' => $l->fish_type_id,
                                'variant' => $l->fish_variant_id,
                                'weight' => $l->weight ?? null,
                                'count' => $l->fish_count ?? null,
                            ], $set->discardedLive),
                        ];
                    }
                }
            }

            return $result;
        };

        $data = array_merge(
            $mapSets($longlines, 'longline'),
            $mapSets($gillnets, 'gillnet'),
            $mapSets($ringnets, 'ringnet')
        );

        return $this->render('view', [
            'eLog' => $eLog,
            'data' => $data,
            'gearDetails' => $gearDetails,
            'longlines' => $longlines,
            'gillnets' => $gillnets,
            'ringnets' => $ringnets,
            'userType' => Yii::$app->user->identity->type,
        ]);
    }

    /* =========================
     * GEAR DETAIL (JSON)
     * ========================= */
    public function actionGearDetail($gear_id, $gear_type)
    {
        $gear_type = strtolower($gear_type);

        if ($gear_type === 'longline') {
            $gear = ELogLongline::findOne($gear_id);
        } elseif ($gear_type === 'gillnet') {
            $gear = ELogGillnet::findOne($gear_id);
        } elseif ($gear_type === 'ringnet') {
            $gear = ELogRingnet::findOne($gear_id);
        } else {
            throw new \yii\web\BadRequestHttpException('Invalid gear type.');
        }

        if ($gear === null) {
            throw new \yii\web\NotFoundHttpException('Gear record not found.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $gear->attributes;
    }

    /* =========================
     * EDIT GEAR RECORDS
     * ========================= */
    public function actionEditLongline($id, $elogId)
    {
        $model = ELogLongline::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Record not found.');
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $elogId]);
            } else {
                echo "<pre>";
                print_r($model->errors);
                echo "</pre>";
                exit;
            }
        }

        return $this->render('edit-longline', [
            'model' => $model,
            'elogId' => $elogId,
        ]);
    }

    public function actionEditGillnet($id, $elogId)
    {
        $model = ELogGillnet::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Record not found.');
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $elogId]);
            } else {
                echo "<pre>";
                print_r($model->errors);
                echo "</pre>";
                exit;
            }
        }

        return $this->render('edit-gillnet', [
            'model' => $model,
            'elogId' => $elogId,
        ]);
    }

    public function actionEditRingnet($id, $elogId)
    {
        $model = ELogRingnet::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Record not found.');
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $elogId]);
            } else {
                echo "<pre>";
                print_r($model->errors);
                echo "</pre>";
                exit;
            }
        }

        return $this->render('edit-ringnet', [
            'model' => $model,
            'elogId' => $elogId,
        ]);
    }

    /* =========================
     * EDIT SET
     * ========================= */
    public function actionEditSet($id, $elogId)
    {
        $model = ELogSets::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('Set not found.');
        }

        $fishTypes = ArrayHelper::map(
            MELogFishType::find()->orderBy('name')->all(),
            'id',
            'name'
        );

        $existingCatches = ELogSetCatch::find()->where(['e_log_set_id' => $id])->all();
        $existingDiscardedDead = ELogSetDiscardedDead::find()->where(['e_log_set_id' => $id])->all();
        $existingDiscardedLive = ELogSetDiscardedLive::find()->where(['e_log_set_id' => $id])->all();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());

            if ($model->save()) {

                // Replace catches
                ELogSetCatch::deleteAll(['e_log_set_id' => $id]);
                foreach (Yii::$app->request->post('catches', []) as $catchData) {
                    if (empty($catchData['fish_type_id']) || empty($catchData['fish_variant_id']))
                        continue;
                    $catch = new ELogSetCatch();
                    $catch->e_log_set_id = $id;
                    $catch->fish_type_id = $catchData['fish_type_id'];
                    $catch->fish_variant_id = $catchData['fish_variant_id'];
                    $catch->weight = $catchData['weight'];
                    $catch->fish_count = $catchData['fish_count'];
                    $catch->save();
                }

                // Replace discarded dead
                ELogSetDiscardedDead::deleteAll(['e_log_set_id' => $id]);
                foreach (Yii::$app->request->post('discarded_dead', []) as $row) {
                    if (empty($row['fish_type_id']) || empty($row['fish_variant_id']))
                        continue;
                    $dead = new ELogSetDiscardedDead();
                    $dead->e_log_set_id = $id;
                    $dead->fish_type_id = $row['fish_type_id'];
                    $dead->fish_variant_id = $row['fish_variant_id'];
                    $dead->weight = $row['weight'];
                    $dead->fish_count = $row['fish_count'];
                    $dead->save();
                }

                // Replace discarded live
                ELogSetDiscardedLive::deleteAll(['e_log_set_id' => $id]);
                foreach (Yii::$app->request->post('discarded_live', []) as $row) {
                    if (empty($row['fish_type_id']) || empty($row['fish_variant_id']))
                        continue;
                    $live = new ELogSetDiscardedLive();
                    $live->e_log_set_id = $id;
                    $live->fish_type_id = $row['fish_type_id'];
                    $live->fish_variant_id = $row['fish_variant_id'];
                    $live->weight = $row['weight'];
                    $live->fish_count = $row['fish_count'];
                    $live->save();
                }

                return $this->redirect(['view', 'id' => $elogId]);

            } else {
                echo "<pre>";
                print_r($model->errors);
                echo "</pre>";
                exit;
            }
        }

        return $this->render('edit-set', [
            'model' => $model,
            'elogId' => $elogId,
            'fishTypes' => $fishTypes,
            'existingCatches' => $existingCatches,
            'existingDiscardedDead' => $existingDiscardedDead,
            'existingDiscardedLive' => $existingDiscardedLive,
        ]);
    }

    /* =========================
     * STATIC HELPERS
     * ========================= */
    public static function getharbourname($id)
    {
        $harbour = MHarbours::findOne($id);
        return $harbour ? $harbour->Name : 'N/A';
    }

    public static function getFishTypeName($id)
    {
        $fishType = MELogFishType::findOne($id);
        return $fishType ? $fishType->name : 'N/A';
    }

    public static function getFishVariantName($id)
    {
        $variant = MELogFishVariant::findOne($id);
        return $variant ? $variant->name : 'N/A';
    }

    /* =========================
     * DELETE SET
     * ========================= */
    public function actionDeleteSet($id, $elogId)
    {
        $set = ELogSets::findOne($id);
        if ($set === null) {
            throw new \yii\web\NotFoundHttpException('Set not found.');
        }

        // Clean up related records first
        ELogSetCatch::deleteAll(['e_log_set_id' => $id]);
        ELogSetDiscardedDead::deleteAll(['e_log_set_id' => $id]);
        ELogSetDiscardedLive::deleteAll(['e_log_set_id' => $id]);

        $set->delete();

        return $this->redirect(['view', 'id' => $elogId]);
    }

    public function actionApprove($id)
    {
        $model = ELog::findOne($id);
        CommonService::addApprovalLog("E-log","approve","elog",$id);

        if ($model) {
            $model->approve = 1;
            $model->save(false);
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDisapprove($id)
    {
        $model = ELog::findOne($id);
        CommonService::addApprovalLog("E-log","disapprove","elog",$id);
        if ($model) {
            $model->approve = 0;
            $model->save(false);
        }

        return $this->redirect(['view', 'id' => $id]);
    }
    /* =========================
     * EDIT ELOG
     * ========================= */
    public function actionEditElog($id)
    {
        $model = ELog::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('E-Log not found.');
        }

        $harbours = ArrayHelper::map(
            MHarbours::find()->orderBy('Name')->all(),
            'Id',
            'Name'
        );

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $id]);
            } else {
                echo "<pre>";
                print_r($model->errors);
                echo "</pre>";
                exit;
            }
        }

        return $this->render('edit-elog', [
            'model' => $model,
            'harbours' => $harbours,
        ]);
    }

    /* =========================
     * DELETE ELOG
     * ========================= */
    public function actionDeleteElog($id)
    {
        $model = ELog::findOne($id);
        CommonService::addApprovalLog("E-log","delete","full-elog",$id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('E-Log not found.');
        }

        $model->delete();

        return $this->redirect(['index']);
    }
      public function actionExportPdf($id)
    {
        $eLog = ELog::findOne($id);
        if ($eLog === null) {
            throw new \yii\web\NotFoundHttpException('E-Log not found.');
        }
 
        // --- Build data (same logic as actionView) ---
        $longlines = ELogLongline::find()->where(['e_log_id' => $id])->all();
        $gillnets  = ELogGillnet::find()->where(['e_log_id' => $id])->all();
        $ringnets  = ELogRingnet::find()->where(['e_log_id' => $id])->all();
 
        $gearDetails = ['longline' => [], 'gillnet' => [], 'ringnet' => []];
        foreach ($longlines as $g) { $gearDetails['longline'][$g->id] = $g->attributes; }
        foreach ($gillnets  as $g) { $gearDetails['gillnet'][$g->id]  = $g->attributes; }
        foreach ($ringnets  as $g) { $gearDetails['ringnet'][$g->id]  = $g->attributes; }
 
        $mapSets = function (array $gearModels, string $gearType) {
            $result = [];
            foreach ($gearModels as $gear) {
                $sets = ELogSets::find()
                    ->where(['gear_id' => $gear->id, 'gear_type' => $gearType])
                    ->with(['catches', 'discardedDead', 'discardedLive'])
                    ->all();
 
                if (empty($sets)) {
                    $result[] = [
                        'set_id' => null, 'gear_id' => $gear->id, 'gear_type' => $gearType,
                        'set_number' => null, 'start_datetime' => null, 'end_datetime' => null,
                        'start_gps_direction' => null, 'start_gps_n' => null, 'start_gps_e' => null,
                        'end_gps_direction' => null, 'end_gps_n' => null, 'end_gps_e' => null,
                        'catches' => [], 'discarded_dead' => [], 'discarded_live' => [],
                    ];
                } else {
                    foreach ($sets as $set) {
                        $result[] = [
                            'set_id'              => $set->id,
                            'gear_id'             => $gear->id,
                            'gear_type'           => $gearType,
                            'set_number'          => $set->set_number,
                            'start_datetime'      => $set->start_datetime,
                            'end_datetime'        => $set->end_datetime,
                            'start_gps_direction' => $set->start_gps_direction,
                            'start_gps_n'         => $set->start_gps_n,
                            'start_gps_e'         => $set->start_gps_e,
                            'end_gps_direction'   => $set->end_gps_direction,
                            'end_gps_n'           => $set->end_gps_n,
                            'end_gps_e'           => $set->end_gps_e,
                            'catches' => array_map(fn($c) => [
                                'fish_type' => $c->fish_type_id,
                                'variant'   => $c->fish_variant_id,
                                'weight'    => $c->weight,
                                'count'     => $c->fish_count,
                            ], $set->catches),
                            'discarded_dead' => array_map(fn($d) => [
                                'fish_type' => $d->fish_type_id,
                                'variant'   => $d->fish_variant_id,
                                'weight'    => $d->weight ?? null,
                                'count'     => $d->fish_count ?? null,
                            ], $set->discardedDead),
                            'discarded_live' => array_map(fn($l) => [
                                'fish_type' => $l->fish_type_id,
                                'variant'   => $l->fish_variant_id,
                                'weight'    => $l->weight ?? null,
                                'count'     => $l->fish_count ?? null,
                            ], $set->discardedLive),
                        ];
                    }
                }
            }
            return $result;
        };
 
        $data = array_merge(
            $mapSets($longlines, 'longline'),
            $mapSets($gillnets,  'gillnet'),
            $mapSets($ringnets,  'ringnet')
        );
 
        // --- Render HTML for mPDF ---
        $html = $this->renderPartial('pdf', [
            'eLog'        => $eLog,
            'data'        => $data,
            'gearDetails' => $gearDetails,
        ]);
 
        // --- Generate PDF ---
        $filename = 'elog_' . $eLog->id . '_' . date('Ymd_His') . '.pdf';
 
        $mpdf = new \Mpdf\Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_top'    => 15,
            'margin_bottom' => 15,
            'margin_left'   => 15,
            'margin_right'  => 15,
            'tempDir'       => Yii::getAlias('@runtime') . '/mpdf',
        ]);
 
        $mpdf->SetTitle('E-Log Report - ' . $eLog->vessel_id);
        $mpdf->SetAuthor('Fisheries Management System');
        $mpdf->SetHeader('E-Log Report  |  Vessel: ' . $eLog->vessel_id . '|Page {PAGENO} of {nbpg}');
        $mpdf->SetFooter('Generated: ' . date('Y-m-d H:i:s') . '||Fisheries Management System');
 
        $mpdf->WriteHTML($html);
 
        // 'D' = download,  'I' = inline (browser opens it — for print dialog)
        $dest = Yii::$app->request->get('print') ? 'I' : 'D';
        $mpdf->Output($filename, $dest);
        Yii::$app->end();
    }

    /**
 * E-Log report / search view with filters:
 * departure harbour, arrival harbour, departure date (year/month/exact),
 * arrival date (year/month/exact), fish variant.
 */
public function actionReportView()
{
    $request = Yii::$app->request;

    $departureHarbour = $request->get('departure_harbour') ?: null;
    $arrivalHarbour   = $request->get('arrival_harbour') ?: null;

    $departureYear  = $request->get('departure_year') ?: null;
    $departureMonth = $request->get('departure_month') ?: null;
    $departureDate  = $request->get('departure_date') ?: null;

    $arrivalYear  = $request->get('arrival_year') ?: null;
    $arrivalMonth = $request->get('arrival_month') ?: null;
    $arrivalDate  = $request->get('arrival_date') ?: null;

    $fishVariantId = $request->get('fish_variant_id') ?: null;

    $fiveByFive = $request->get('five_by_five') !== '' ? $request->get('five_by_five') : null;
    $oneByOne   = $request->get('one_by_one') !== '' ? $request->get('one_by_one') : null;

    $sql = "
        SELECT
            e.id                    AS e_log_id,
            e.vessel_id,
            e.departure_date,
            e.arrival_date,

            es.five_by_five,
            es.one_by_one,

            ft.name                 AS fish_type,
            fv.id                   AS fish_variant_id,
            fv.name                 AS fish_variant,
            esc.weight,
            esc.fish_count

        FROM e_log e

        LEFT JOIN e_log_longline ll ON e.id = ll.e_log_id
        LEFT JOIN e_log_ringnet  rn ON e.id = rn.e_log_id
        LEFT JOIN e_log_gillnet  gn ON e.id = gn.e_log_id

        LEFT JOIN e_log_sets es
            ON es.gear_id = COALESCE(ll.id, rn.id, gn.id)
            AND es.gear_type = CASE
                WHEN ll.id IS NOT NULL THEN 'longline'
                WHEN rn.id IS NOT NULL THEN 'ringnet'
                WHEN gn.id IS NOT NULL THEN 'gillnet'
            END

        LEFT JOIN e_log_set_catch esc       ON esc.e_log_set_id = es.id
        LEFT JOIN m_e_log_fish_type    ft   ON ft.id = esc.fish_type_id
        LEFT JOIN m_e_log_fish_variant fv   ON fv.id = esc.fish_variant_id

        WHERE
            (:departure_harbour IS NULL OR e.departure_harbour = :departure_harbour)
            AND (:arrival_harbour IS NULL OR e.arrival_harbour = :arrival_harbour)
            AND (:departure_year  IS NULL OR YEAR(e.departure_date)  = :departure_year)
            AND (:departure_month IS NULL OR MONTH(e.departure_date) = :departure_month)
            AND (:departure_date  IS NULL OR e.departure_date        = :departure_date)
            AND (:arrival_year  IS NULL OR YEAR(e.arrival_date)  = :arrival_year)
            AND (:arrival_month IS NULL OR MONTH(e.arrival_date) = :arrival_month)
            AND (:arrival_date  IS NULL OR e.arrival_date        = :arrival_date)
            AND (:fish_variant_id IS NULL OR esc.fish_variant_id = :fish_variant_id)
            AND (:five_by_five IS NULL OR es.five_by_five = :five_by_five)
            AND (:one_by_one   IS NULL OR es.one_by_one   = :one_by_one)
            AND esc.id IS NOT NULL

        ORDER BY e.id, fv.name
    ";

    $rows = Yii::$app->db->createCommand($sql)
        ->bindValue(':departure_harbour', $departureHarbour)
        ->bindValue(':arrival_harbour', $arrivalHarbour)
        ->bindValue(':departure_year', $departureYear)
        ->bindValue(':departure_month', $departureMonth)
        ->bindValue(':departure_date', $departureDate)
        ->bindValue(':arrival_year', $arrivalYear)
        ->bindValue(':arrival_month', $arrivalMonth)
        ->bindValue(':arrival_date', $arrivalDate)
        ->bindValue(':fish_variant_id', $fishVariantId)
        ->bindValue(':five_by_five', $fiveByFive)
        ->bindValue(':one_by_one', $oneByOne)
        ->queryAll();

    $harbours = ArrayHelper::map(
        MHarbours::find()->select(['id', 'Name'])->orderBy(['Name' => SORT_ASC])->asArray()->all(),
        'id',
        'Name'
    );

    $fishVariants = ArrayHelper::map(
        MELogFishVariant::find()->orderBy('name')->all(),
        'id',
        'name'
    );

    $years = Yii::$app->db->createCommand("
        SELECT DISTINCT YEAR(departure_date) AS y FROM e_log WHERE departure_date IS NOT NULL
        UNION
        SELECT DISTINCT YEAR(arrival_date) AS y FROM e_log WHERE arrival_date IS NOT NULL
        ORDER BY y DESC
    ")->queryColumn();

    // Distinct grid values actually present in e_log_sets
    $fiveByFiveOptions = Yii::$app->db->createCommand("
        SELECT DISTINCT five_by_five
        FROM e_log_sets
        WHERE five_by_five IS NOT NULL AND five_by_five <> ''
        ORDER BY five_by_five
    ")->queryColumn();

    $oneByOneOptions = Yii::$app->db->createCommand("
        SELECT DISTINCT one_by_one
        FROM e_log_sets
        WHERE one_by_one IS NOT NULL AND one_by_one <> ''
        ORDER BY one_by_one
    ")->queryColumn();

    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    // Build a data provider so the detail table gets pagination + export
    $dataProvider = new ArrayDataProvider([
        'allModels' => $rows,
        'pagination' => [
            'pageSize' => 20,
        ],
        'sort' => [
            'attributes' => [
                'e_log_id',
                'vessel_id',
                'fish_variant',
                'weight',
                'fish_count',
            ],
        ],
    ]);

    return $this->render('report_view', [
        'rows' => $rows,            // full unpaginated set, used for the totals summary
        'dataProvider' => $dataProvider, // paginated + sortable, used for the detail grid
        'harbours' => $harbours,
        'fishVariants' => $fishVariants,
        'years' => $years,
        'months' => $months,
        'fiveByFiveOptions' => array_combine($fiveByFiveOptions, $fiveByFiveOptions),
        'oneByOneOptions' => array_combine($oneByOneOptions, $oneByOneOptions),
        'filters' => [
            'departure_harbour' => $departureHarbour,
            'arrival_harbour' => $arrivalHarbour,
            'departure_year' => $departureYear,
            'departure_month' => $departureMonth,
            'departure_date' => $departureDate,
            'arrival_year' => $arrivalYear,
            'arrival_month' => $arrivalMonth,
            'arrival_date' => $arrivalDate,
            'fish_variant_id' => $fishVariantId,
            'five_by_five' => $fiveByFive,
            'one_by_one' => $oneByOne,
        ],
    ]);
}
}