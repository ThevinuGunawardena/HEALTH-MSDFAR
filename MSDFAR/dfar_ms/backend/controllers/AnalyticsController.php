<?php
namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumberCancelRequests;
use backend\models\BoatNumbers;
use backend\models\BoatNumberTransferRequest;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
use backend\models\ProfileOfficer;
use backend\models\Skipper;
use backend\models\SkipperRenew;
use Exception;
use Yii;
use yii\db\Expression;
use backend\components\Controller;

use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;

class AnalyticsController extends Controller
{
    /**
     * Build an SQL expression that checks approval_stage for a given stage.
     * This handles numeric, string and comma-separated values (FIND_IN_SET).
     * Usage: ->andWhere($this->approvalStageCondition(Constant::DG, 'DG'))
     */
    private function approvalStageCondition($constVal, $label)
    {
        try {
            $db = Yii::$app->db;
            $labelQuoted = $db->quoteValue((string) $label);
            $constQuoted = $db->quoteValue((string) $constVal);

            // Build condition: approval_stage = <const> OR approval_stage = '<const-as-string>' OR approval_stage = 'LABEL' OR FIND_IN_SET('LABEL', approval_stage)
            $sql = "(approval_stage = {$constVal} OR approval_stage = {$constQuoted} OR approval_stage = {$labelQuoted} OR FIND_IN_SET({$labelQuoted}, approval_stage))";
            return new Expression($sql);
        } catch (Exception $e) {
            Yii::error("Error building approvalStageCondition: " . $e->getMessage());
            // Fallback to simple equality check
            return new Expression("approval_stage = '" . addslashes((string) $label) . "'");
        }
    }
    public function actionDgDashboard()
    {
        // Only allow DG users
        if (!isset(Yii::$app->user->identity) || !UserTypeUtil::hasType(Constant::DG)) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }

        // Check database connection
        try {
            Yii::$app->db->open();
        } catch (Exception $e) {
            Yii::error("Database connection failed: " . $e->getMessage());
            throw new ServerErrorHttpException('Database connection failed. Please try again later.');
        }

        // Try to get cached data first
        $cacheKey = 'dg_dashboard_data_' . Yii::$app->user->id . '_' . time();

        // Temporarily disable caching to see real-time data
        // $cachedData = \Yii::$app->cache->get($cacheKey);
        $cachedData = false; // Force fresh data

        if ($cachedData !== false) {
            Yii::info("Using cached dashboard data for user " . Yii::$app->user->id);
            return $this->render('dg-dashboard', $cachedData);
        }

        $categories = [
            'skipper_license' => [Skipper::class, SkipperRenew::class],
            'boat_numbers' => [BoatNumbers::class],
            'boat_registration' => [FishermanRegisterdBoatLicense::class],
            'national_license' => [NationalLicense::class],
            'highseas_license' => [HighseasLicense::class],
            'boat_cancel' => [BoatNumberCancelRequests::class],
            'boat_transfer' => [BoatNumberTransferRequest::class],
        ];

        $categoryData = [];
        $approvedData = [];
        $avgApprovalTimeData = [];
        $pendingTotal = 0;
        $approvedTotal = 0;
        $approvalTimeSum = 0;
        $approvalTimeCount = 0;

        // Debug information
        $debugInfo = [];

        foreach ($categories as $key => $models) {
            $pending = 0;
            $approved = 0;
            $timeSum = 0;
            $timeCount = 0;
            $debugInfo[$key] = [];

            foreach ($models as $model) {
                $pendingCount = 0;
                $approvedCount = 0;

                try {
                    // Standardized pending query - items awaiting DG approval (status = Pending AND approval_stage = DG)
                    $pendingQuery = $model::find()->where(['status' => Constant::Pending])
                        ->andWhere($this->approvalStageCondition(Constant::DG, 'DG'));
                    $pendingCount = $pendingQuery->count();

                    // Standardized approved query - completed items
                    $approvedQuery = $model::find()->where([
                        'or',
                        ['status' => [200, 403]],
                        ['approval_stage' => 'Completed']
                    ]);
                    $approvedCount = $approvedQuery->count();

                    // Get pending count
                    $pending += $pendingCount;

                    // Get approved count
                    $approved += $approvedCount;

                    // Store debug info
                    $debugInfo[$key][$model] = [
                        'pending' => $pendingCount,
                        'approved' => $approvedCount,
                        'total' => $model::find()->count()
                    ];

                    // Additional debugging for approval_stage values
                    $approvalStages = $model::find()->select('approval_stage')->distinct()->column();
                    $statusValues = $model::find()->select('status')->distinct()->column();
                    Yii::info("Model {$model} - Approval stages: " . json_encode($approvalStages));
                    Yii::info("Model {$model} - Status values: " . json_encode($statusValues));

                    // Get approval time data for completed items
                    $timeRows = $model::find()
                        ->select([
                            'diff' => new Expression('DATEDIFF(COALESCE(approved_time, updated_at), created)')
                        ])
                        ->where(['or', ['status' => [200, 403]], ['approval_stage' => 'Completed']])
                        ->andWhere(['not', ['created' => null]])
                        ->andWhere(['or', ['not', ['approved_time' => null]], ['not', ['updated_at' => null]]])
                        ->asArray()
                        ->all();

                    foreach ($timeRows as $row) {
                        if (isset($row['diff']) && is_numeric($row['diff']) && $row['diff'] >= 0) {
                            $timeSum += (int) $row['diff'];
                            $timeCount++;
                        }
                    }
                } catch (Exception $e) {
                    Yii::error("Error processing model {$model} for category {$key}: " . $e->getMessage());
                    $debugInfo[$key][$model] = ['error' => $e->getMessage()];
                    // Continue with other models
                }
            }

            $categoryData[$key] = (int) $pending;
            $approvedData[$key] = (int) $approved;
            $avgApprovalTimeData[$key] = $timeCount > 0 ? round($timeSum / $timeCount, 1) : null;

            $pendingTotal += $pending;
            $approvedTotal += $approved;
            $approvalTimeSum += $timeSum;
            $approvalTimeCount += $timeCount;
        }

        // Log debug information
        Yii::info("Dashboard data collection debug info: " . json_encode($debugInfo));
        Yii::info("Category data: " . json_encode($categoryData));

        // Additional detailed logging
        foreach ($categories as $key => $models) {
            foreach ($models as $model) {
                try {
                    $totalCount = $model::find()->count();
                    $pendingCount = $model::find()->where(['status' => Constant::Pending])
                        ->andWhere($this->approvalStageCondition(Constant::DG, 'DG'))->count();
                    $allPendingCount = $model::find()->where($this->approvalStageCondition(Constant::DG, 'DG'))->count();
                    $allStatusPendingCount = $model::find()->where(['status' => Constant::Pending])->count();

                    Yii::info("Model {$model} for category {$key}: Total={$totalCount}, Pending(DG)={$pendingCount}, AllPending={$allStatusPendingCount}, AllDG={$allPendingCount}");
                } catch (Exception $e) {
                    Yii::error("Error in detailed logging for {$model}: " . $e->getMessage());
                }
            }
        }

        // Metrics summary with proper data types
        $metrics = [
            'pendingApprovals' => (int) $pendingTotal,
            'approvedByDG' => (int) $approvedTotal,
            'avgApprovalTime' => $approvalTimeCount > 0 ? round($approvalTimeSum / $approvalTimeCount, 1) : null,
            'approvalRate' => ($pendingTotal + $approvedTotal) > 0 ? round(($approvedTotal / ($pendingTotal + $approvedTotal)) * 100, 1) : 0,
        ];

        // Monthly trends with optimized queries
        $monthlyData = $this->getMonthlyTrends($categories);

        // District breakdown with proper error handling
        $districtData = $this->getDistrictBreakdown();

        // Prepare data for view
        $viewData = [
            'categoryData' => $categoryData,
            'approvedData' => $approvedData,
            'avgApprovalTimeData' => $avgApprovalTimeData,
            'metrics' => $metrics,
            'monthlyData' => $monthlyData,
            'districtData' => $districtData,
        ];

        // Cache the data for 5 minutes
        Yii::$app->cache->set($cacheKey, $viewData, 300);

        // Log successful data sync
        Yii::info("Dashboard data sync completed successfully. Pending: {$pendingTotal}, Approved: {$approvedTotal}");

        return $this->render('dg-dashboard', $viewData);
    }

    /**
     * Debug action to check data sync issues
     */
    public function actionDebugDashboard()
    {
        // Only allow DG users
        if (!isset(Yii::$app->user->identity) || !UserTypeUtil::hasType(Constant::DG)) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }

        $debugData = [];

        // Test BoatNumbers model
        try {
            $debugData['boat_numbers'] = [
                'total' => BoatNumbers::find()->count(),
                'pending' => BoatNumbers::find()->where(['status' => Constant::Pending])
                    ->andWhere($this->approvalStageCondition(Constant::DG, 'DG'))->count(),
                'approved' => BoatNumbers::find()->where(['or', ['status' => [200, 403]], ['approval_stage' => 'Completed']])->count(),
                'with_created' => BoatNumbers::find()->where(['not', ['created' => null]])->count(),
                'with_approved_time' => BoatNumbers::find()->where(['not', ['approved_time' => null]])->count(),
            ];
        } catch (Exception $e) {
            $debugData['boat_numbers_error'] = $e->getMessage();
        }

        // Test FishermanRegisterdBoatLicense model
        try {
            $debugData['boat_registration'] = [
                'total' => FishermanRegisterdBoatLicense::find()->count(),
                'pending' => FishermanRegisterdBoatLicense::find()->where(['status' => Constant::Pending])
                    ->andWhere($this->approvalStageCondition(Constant::DG, 'DG'))->count(),
                'approved' => FishermanRegisterdBoatLicense::find()->where(['or', ['status' => [200, 403]], ['approval_stage' => 'Completed']])->count(),
                'with_created' => FishermanRegisterdBoatLicense::find()->where(['not', ['created' => null]])->count(),
                'with_approved_time' => FishermanRegisterdBoatLicense::find()->where(['not', ['approved_time' => null]])->count(),
                'districts' => FishermanRegisterdBoatLicense::find()->select('district')->distinct()->column(),
            ];
        } catch (Exception $e) {
            $debugData['boat_registration_error'] = $e->getMessage();
        }

        // Test Skipper model
        try {
            $debugData['skipper'] = [
                'total' => Skipper::find()->count(),
                'pending' => Skipper::find()->where(['status' => Constant::Pending])
                    ->andWhere($this->approvalStageCondition(Constant::DG, 'DG'))->count(),
                'approved' => Skipper::find()->where(['or', ['status' => [200, 403]], ['approval_stage' => 'Completed']])->count(),
            ];
        } catch (Exception $e) {
            $debugData['skipper_error'] = $e->getMessage();
        }

        // Test BoatNumberCancelRequests model
        try {
            $debugData['boat_cancel'] = [
                'total' => BoatNumberCancelRequests::find()->count(),
                'pending' => BoatNumberCancelRequests::find()->where(['status' => Constant::Pending])
                    ->andWhere($this->approvalStageCondition(Constant::DG, 'DG'))->count(),
                'approved' => BoatNumberCancelRequests::find()->where(['or', ['status' => [200, 403]], ['approval_stage' => 'Completed']])->count(),
            ];
        } catch (Exception $e) {
            $debugData['boat_cancel_error'] = $e->getMessage();
        }

        // Test BoatNumberTransferRequest model
        try {
            $debugData['boat_transfer'] = [
                'total' => BoatNumberTransferRequest::find()->count(),
                'pending' => BoatNumberTransferRequest::find()->where(['status' => Constant::Pending])
                    ->andWhere($this->approvalStageCondition(Constant::DG, 'DG'))->count(),
                'approved' => BoatNumberTransferRequest::find()->where(['or', ['status' => [200, 403]], ['approval_stage' => 'Completed']])->count(),
            ];
        } catch (Exception $e) {
            $debugData['boat_transfer_error'] = $e->getMessage();
        }

        // Test NationalLicense model
        try {
            $debugData['national_license'] = [
                'total' => NationalLicense::find()->count(),
                'pending' => NationalLicense::find()->where(['status' => Constant::Pending])
                    ->andWhere($this->approvalStageCondition(Constant::DG, 'DG'))->count(),
                'approved' => NationalLicense::find()->where(['or', ['status' => [200, 403]], ['approval_stage' => 'Completed']])->count(),
            ];
        } catch (Exception $e) {
            $debugData['national_license_error'] = $e->getMessage();
        }

        // Test HighseasLicense model
        try {
            $debugData['highseas_license'] = [
                'total' => HighseasLicense::find()->count(),
                'pending' => HighseasLicense::find()->where(['status' => Constant::Pending])
                    ->andWhere($this->approvalStageCondition(Constant::DG, 'DG'))->count(),
                'approved' => HighseasLicense::find()->where(['or', ['status' => [200, 403]], ['approval_stage' => 'Completed']])->count(),
            ];
        } catch (Exception $e) {
            $debugData['highseas_license_error'] = $e->getMessage();
        }

        return $this->renderPartial('debug-dashboard', [
            'debugData' => $debugData
        ]);
    }

    /**
     * Refresh dashboard cache
     */
    public function actionRefreshCache()
    {
        // Only allow DG users
        if (!isset(Yii::$app->user->identity) || !UserTypeUtil::hasType(Constant::DG)) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }

        $cacheKey = 'dg_dashboard_data_' . Yii::$app->user->id;
        $cleared = Yii::$app->cache->delete($cacheKey);

        Yii::info("Dashboard cache cleared for user " . Yii::$app->user->id . ". Success: " . ($cleared ? 'Yes' : 'No'));

        if (Yii::$app->request->isAjax) {
            return $this->asJson([
                'success' => $cleared,
                'message' => $cleared ? 'Cache cleared successfully' : 'Cache was already empty or could not be cleared'
            ]);
        }

        Yii::$app->session->setFlash(
            $cleared ? 'success' : 'info',
            $cleared ? 'Dashboard cache cleared successfully. Data will be refreshed on next load.' : 'Cache was already empty or could not be cleared.'
        );

        return $this->redirect(['dg-dashboard']);
    }

    /**
     * Get monthly trends data with optimized queries
     */
    private function getMonthlyTrends($categories)
    {
        $monthlyData = [
            'categories' => [],
            'pending' => [],
            'approved' => [],
        ];

        $months = [];

        // Get current year data for all months
        $currentYear = date('Y');
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = ['pending' => 0, 'approved' => 0];
        }

        foreach ($categories as $models) {
            foreach ($models as $model) {
                try {
                    $rows = $model::find()
                        ->select([
                            'month' => new Expression('MONTH(created)'),
                            'pending' => new Expression('SUM(CASE WHEN status = ' . Constant::Pending . ' AND (approval_stage = ' . Constant::DG . ' OR approval_stage = "' . (string) Constant::DG . '" OR approval_stage = "DG" OR FIND_IN_SET("DG", approval_stage)) THEN 1 ELSE 0 END)'),
                            'approved' => new Expression('SUM(CASE WHEN status IN (200, 403) OR approval_stage = "Completed" THEN 1 ELSE 0 END)'),
                        ])
                        ->where(['YEAR(created)' => $currentYear])
                        ->andWhere(['not', ['created' => null]])
                        ->groupBy('MONTH(created)')
                        ->asArray()
                        ->all();

                    foreach ($rows as $row) {
                        $m = (int) $row['month'];
                        if (isset($months[$m])) {
                            $months[$m]['pending'] += (int) ($row['pending'] ?? 0);
                            $months[$m]['approved'] += (int) ($row['approved'] ?? 0);
                        }
                    }
                } catch (Exception $e) {
                    // Log error but continue processing other models
                    Yii::error("Error processing monthly trends for " . $model . ": " . $e->getMessage());
                }
            }
        }

        // Convert to chart format
        foreach ($months as $m => $data) {
            $monthlyData['categories'][] = date('M', mktime(0, 0, 0, $m, 10));
            $monthlyData['pending'][] = (int) $data['pending'];
            $monthlyData['approved'][] = (int) $data['approved'];
        }

        return $monthlyData;
    }

    /**
     * Get district breakdown with proper error handling
     */
    private function getDistrictBreakdown()
    {
        try {
            $districtRows = FishermanRegisterdBoatLicense::find()
                ->select([
                    'name' => 'district',
                    'y' => new Expression('COUNT(*)')
                ])
                ->where(['status' => Constant::Pending])
                ->andWhere($this->approvalStageCondition(Constant::DG, 'DG'))
                ->andWhere(['not', ['district' => null]])
                ->andWhere(['!=', 'district', ''])
                ->groupBy('district')
                ->orderBy(['y' => SORT_DESC])
                ->limit(10) // Limit to top 10 districts
                ->asArray()
                ->all();

            return array_map(function ($row) {
                return [
                    'name' => $row['name'] ?? 'Unknown',
                    'y' => (int) ($row['y'] ?? 0)
                ];
            }, $districtRows);
        } catch (Exception $e) {
            Yii::error("Error getting district breakdown: " . $e->getMessage());
            return [];
        }
    }

    /**
     * DM Dashboard action
     */
    public function actionDmDashboard()
    {
        // Only allow DM users
        if (!isset(Yii::$app->user->identity) || !UserTypeUtil::hasType(Constant::DM)) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }

        // Check database connection
        try {
            Yii::$app->db->open();
        } catch (Exception $e) {
            Yii::error("Database connection failed: " . $e->getMessage());
            throw new ServerErrorHttpException('Database connection failed. Please try again later.');
        }

        // Try to get cached data first
        $cacheKey = 'dm_dashboard_data_' . Yii::$app->user->id . '_' . time();

        // Temporarily disable caching to see real-time data
        $cachedData = false; // Force fresh data

        if ($cachedData !== false) {
            Yii::info("Using cached dashboard data for user " . Yii::$app->user->id);
            return $this->render('dm-dashboard', $cachedData);
        }

        $categories = [
            'skipper_license' => [Skipper::class, SkipperRenew::class],
            'boat_numbers' => [BoatNumbers::class],
            'boat_registration' => [FishermanRegisterdBoatLicense::class],
            'national_license' => [NationalLicense::class],
            'highseas_license' => [HighseasLicense::class],
        ];

        $categoryData = [];
        $approvedData = [];
        $avgApprovalTimeData = [];
        $pendingTotal = 0;
        $approvedTotal = 0;
        $approvalTimeSum = 0;
        $approvalTimeCount = 0;

        // Debug information
        $debugInfo = [];

        foreach ($categories as $key => $models) {
            $pending = 0;
            $approved = 0;
            $timeSum = 0;
            $timeCount = 0;
            $debugInfo[$key] = [];

            foreach ($models as $model) {
                $pendingCount = 0;
                $approvedCount = 0;

                try {
                    // Standardized pending query - items awaiting DM approval (status = Pending AND approval_stage = DM)
                    $pendingQuery = $model::find()->where(['status' => Constant::Pending])
                        ->andWhere($this->approvalStageCondition(Constant::DM, 'DM'));
                    $pendingCount = $pendingQuery->count();

                    // Standardized approved query - completed items
                    $approvedQuery = $model::find()->where([
                        'or',
                        ['status' => [200, 403]],
                        ['approval_stage' => 'Completed']
                    ]);
                    $approvedCount = $approvedQuery->count();

                    // Get pending count
                    $pending += $pendingCount;

                    // Get approved count
                    $approved += $approvedCount;

                    // Store debug info
                    $debugInfo[$key][$model] = [
                        'pending' => $pendingCount,
                        'approved' => $approvedCount,
                        'total' => $model::find()->count()
                    ];

                    // Additional debugging for approval_stage values
                    $approvalStages = $model::find()->select('approval_stage')->distinct()->column();
                    $statusValues = $model::find()->select('status')->distinct()->column();
                    Yii::info("Model {$model} - Approval stages: " . json_encode($approvalStages));
                    Yii::info("Model {$model} - Status values: " . json_encode($statusValues));

                    // Get approval time data for completed items
                    $timeRows = $model::find()
                        ->select([
                            'diff' => new Expression('DATEDIFF(COALESCE(approved_time, updated_at), created)')
                        ])
                        ->where(['or', ['status' => [200, 403]], ['approval_stage' => 'Completed']])
                        ->andWhere(['not', ['created' => null]])
                        ->andWhere(['or', ['not', ['approved_time' => null]], ['not', ['updated_at' => null]]])
                        ->asArray()
                        ->all();

                    foreach ($timeRows as $row) {
                        if (isset($row['diff']) && is_numeric($row['diff']) && $row['diff'] >= 0) {
                            $timeSum += (int) $row['diff'];
                            $timeCount++;
                        }
                    }
                } catch (Exception $e) {
                    Yii::error("Error processing model {$model} for category {$key}: " . $e->getMessage());
                    $debugInfo[$key][$model] = ['error' => $e->getMessage()];
                    // Continue with other models
                }
            }

            $categoryData[$key] = (int) $pending;
            $approvedData[$key] = (int) $approved;
            $avgApprovalTimeData[$key] = $timeCount > 0 ? round($timeSum / $timeCount, 1) : null;

            $pendingTotal += $pending;
            $approvedTotal += $approved;
            $approvalTimeSum += $timeSum;
            $approvalTimeCount += $timeCount;
        }

        // Log debug information
        Yii::info("DM Dashboard data collection debug info: " . json_encode($debugInfo));
        Yii::info("DM Category data: " . json_encode($categoryData));

        // Additional detailed logging
        foreach ($categories as $key => $models) {
            foreach ($models as $model) {
                try {
                    $totalCount = $model::find()->count();
                    $pendingCount = $model::find()->where(['status' => Constant::Pending])
                        ->andWhere([
                            'or',
                            ['approval_stage' => Constant::DM],
                            ['approval_stage' => (string) Constant::DM],
                            ['approval_stage' => 'DM']
                        ])->count();
                    $allPendingCount = $model::find()->where([
                        'or',
                        ['approval_stage' => Constant::DM],
                        ['approval_stage' => (string) Constant::DM],
                        ['approval_stage' => 'DM']
                    ])->count();
                    $allStatusPendingCount = $model::find()->where(['status' => Constant::Pending])->count();

                    Yii::info("Model {$model} for category {$key}: Total={$totalCount}, Pending(DM)={$pendingCount}, AllPending={$allStatusPendingCount}, AllDM={$allPendingCount}");
                } catch (Exception $e) {
                    Yii::error("Error in detailed logging for {$model}: " . $e->getMessage());
                }
            }
        }

        // Metrics summary with proper data types
        $metrics = [
            'pendingApprovals' => (int) $pendingTotal,
            'approvedByDM' => (int) $approvedTotal,
            'avgApprovalTime' => $approvalTimeCount > 0 ? round($approvalTimeSum / $approvalTimeCount, 1) : null,
            'approvalRate' => ($pendingTotal + $approvedTotal) > 0 ? round(($approvedTotal / ($pendingTotal + $approvedTotal)) * 100, 1) : 0,
        ];

        // Monthly trends with optimized queries
        $monthlyData = $this->getDmMonthlyTrends($categories);

        // District breakdown with proper error handling
        $districtData = $this->getDmDistrictBreakdown();

        // Prepare data for view
        $viewData = [
            'categoryData' => $categoryData,
            'approvedData' => $approvedData,
            'avgApprovalTimeData' => $avgApprovalTimeData,
            'metrics' => $metrics,
            'monthlyData' => $monthlyData,
            'districtData' => $districtData,
        ];

        // Cache the data for 5 minutes
        Yii::$app->cache->set($cacheKey, $viewData, 300);

        // Log successful data sync
        Yii::info("DM Dashboard data sync completed successfully. Pending: {$pendingTotal}, Approved: {$approvedTotal}");

        return $this->render('dm-dashboard', $viewData);
    }

    /**
     * Get monthly trends data for DM dashboard
     */
    private function getDmMonthlyTrends($categories)
    {
        $monthlyData = [
            'categories' => [],
            'pending' => [],
            'approved' => [],
        ];

        $months = [];

        // Get current year data for all months
        $currentYear = date('Y');
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = ['pending' => 0, 'approved' => 0];
        }

        foreach ($categories as $models) {
            foreach ($models as $model) {
                try {
                    $rows = $model::find()
                        ->select([
                            'month' => new Expression('MONTH(created)'),
                            'pending' => new Expression('SUM(CASE WHEN status = ' . Constant::Pending . ' AND (approval_stage = ' . Constant::DM . ' OR approval_stage = "' . (string) Constant::DM . '" OR approval_stage = "DM" OR FIND_IN_SET("DM", approval_stage)) THEN 1 ELSE 0 END)'),
                            'approved' => new Expression('SUM(CASE WHEN status IN (200, 403) OR approval_stage = "Completed" THEN 1 ELSE 0 END)'),
                        ])
                        ->where(['YEAR(created)' => $currentYear])
                        ->andWhere(['not', ['created' => null]])
                        ->groupBy('MONTH(created)')
                        ->asArray()
                        ->all();

                    foreach ($rows as $row) {
                        $m = (int) $row['month'];
                        if (isset($months[$m])) {
                            $months[$m]['pending'] += (int) ($row['pending'] ?? 0);
                            $months[$m]['approved'] += (int) ($row['approved'] ?? 0);
                        }
                    }
                } catch (Exception $e) {
                    // Log error but continue processing other models
                    Yii::error("Error processing monthly trends for " . $model . ": " . $e->getMessage());
                }
            }
        }

        // Convert to chart format
        foreach ($months as $m => $data) {
            $monthlyData['categories'][] = date('M', mktime(0, 0, 0, $m, 10));
            $monthlyData['pending'][] = (int) $data['pending'];
            $monthlyData['approved'][] = (int) $data['approved'];
        }

        return $monthlyData;
    }

    /**
     * Get district breakdown for DM dashboard
     */
    private function getDmDistrictBreakdown()
    {
        try {
            $districtRows = NationalLicense::find()
                ->select([
                    'name' => 'district',
                    'y' => new Expression('COUNT(*)')
                ])
                ->where(['status' => Constant::Pending])
                ->andWhere($this->approvalStageCondition(Constant::DM, 'DM'))
                ->andWhere(['not', ['district' => null]])
                ->andWhere(['!=', 'district', ''])
                ->groupBy('district')
                ->orderBy(['y' => SORT_DESC])
                ->limit(10) // Limit to top 10 districts
                ->asArray()
                ->all();

            return array_map(function ($row) {
                return [
                    'name' => $row['name'] ?? 'Unknown',
                    'y' => (int) ($row['y'] ?? 0)
                ];
            }, $districtRows);
        } catch (Exception $e) {
            Yii::error("Error getting DM district breakdown: " . $e->getMessage());
            return [];
        }
    }

    /**
     * AD Dashboard action
     */
    public function actionAdDashboard()
    {
        // Only allow AD users
        if (!isset(Yii::$app->user->identity) || !UserTypeUtil::hasType(Constant::AD)) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
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
            throw new ForbiddenHttpException('No district assigned to your profile.');
        }

        // Check database connection
        try {
            Yii::$app->db->open();
        } catch (Exception $e) {
            Yii::error("Database connection failed: " . $e->getMessage());
            throw new ServerErrorHttpException('Database connection failed. Please try again later.');
        }

        // Try to get cached data first
        $cacheKey = 'ad_dashboard_data_' . Yii::$app->user->id . '_' . time();
        $cachedData = false; // Force fresh data
        if ($cachedData !== false) {
            Yii::info("Using cached dashboard data for user " . Yii::$app->user->id);
            return $this->render('ad-dashboard', $cachedData);
        }

        $categories = [
            'skipper_license' => [Skipper::class, SkipperRenew::class],
            'boat_numbers' => [BoatNumbers::class],
            'boat_registration' => [FishermanRegisterdBoatLicense::class],
            'national_license' => [NationalLicense::class],
            'highseas_license' => [HighseasLicense::class],
            'boat_cancel' => [BoatNumberCancelRequests::class],
            'boat_transfer' => [BoatNumberTransferRequest::class],
        ];

        $categoryData = [];
        $approvedData = [];
        $avgApprovalTimeData = [];
        $pendingTotal = 0;
        $approvedTotal = 0;
        $approvalTimeSum = 0;
        $approvalTimeCount = 0;
        $debugInfo = [];

        foreach ($categories as $key => $models) {
            $pending = 0;
            $approved = 0;
            $timeSum = 0;
            $timeCount = 0;
            $debugInfo[$key] = [];

            foreach ($models as $model) {
                $pendingCount = 0;
                $approvedCount = 0;
                try {
                    // Determine district field for each model
                    $districtField = null;
                    if ($model === Skipper::class || $model === SkipperRenew::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === BoatNumbers::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === FishermanRegisterdBoatLicense::class) {
                        $districtField = 'district';
                    } elseif ($model === NationalLicense::class || $model === HighseasLicense::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === BoatNumberCancelRequests::class || $model === BoatNumberTransferRequest::class) {
                        $districtField = 'new_landing_district';
                    }

                    // Pending: status = Pending AND approval_stage = AD AND district
                    $pendingQuery = $model::find()->where(['status' => Constant::Pending])
                        ->andWhere($this->approvalStageCondition(Constant::AD, 'AD'));
                    if ($districtField) {
                        $pendingQuery->andWhere([$districtField => $adDistrict]);
                    }
                    $pendingCount = $pendingQuery->count();

                    // Approved: status in (200, 403) or approval_stage = Completed AND district
                    $approvedQuery = $model::find()->where([
                        'or',
                        ['status' => [200, 403]],
                        ['approval_stage' => 'Completed']
                    ]);
                    if ($districtField) {
                        $approvedQuery->andWhere([$districtField => $adDistrict]);
                    }
                    $approvedCount = $approvedQuery->count();

                    $pending += $pendingCount;
                    $approved += $approvedCount;

                    $debugInfo[$key][$model] = [
                        'pending' => $pendingCount,
                        'approved' => $approvedCount,
                        'total' => $model::find()->count()
                    ];

                    // Approval time for completed items
                    $timeRowsQuery = $model::find()
                        ->select([
                            'diff' => new Expression('DATEDIFF(COALESCE(approved_time, updated_at), created)')
                        ])
                        ->where(['or', ['status' => [200, 403]], ['approval_stage' => 'Completed']])
                        ->andWhere(['not', ['created' => null]])
                        ->andWhere(['or', ['not', ['approved_time' => null]], ['not', ['updated_at' => null]]]);
                    if ($districtField) {
                        $timeRowsQuery->andWhere([$districtField => $adDistrict]);
                    }
                    $timeRows = $timeRowsQuery->asArray()->all();
                    foreach ($timeRows as $row) {
                        if (isset($row['diff']) && is_numeric($row['diff']) && $row['diff'] >= 0) {
                            $timeSum += (int) $row['diff'];
                            $timeCount++;
                        }
                    }
                } catch (Exception $e) {
                    Yii::error("Error processing model {$model} for category {$key}: " . $e->getMessage());
                    $debugInfo[$key][$model] = ['error' => $e->getMessage()];
                }
            }
            $categoryData[$key] = (int) $pending;
            $approvedData[$key] = (int) $approved;
            $avgApprovalTimeData[$key] = $timeCount > 0 ? round($timeSum / $timeCount, 1) : null;
            $pendingTotal += $pending;
            $approvedTotal += $approved;
            $approvalTimeSum += $timeSum;
            $approvalTimeCount += $timeCount;
        }

        Yii::info("AD Dashboard data collection debug info: " . json_encode($debugInfo));
        Yii::info("AD Category data: " . json_encode($categoryData));

        $metrics = [
            'pendingApprovals' => (int) $pendingTotal,
            'approvedByAD' => (int) $approvedTotal,
            'avgApprovalTime' => $approvalTimeCount > 0 ? round($approvalTimeSum / $approvalTimeCount, 1) : null,
            'approvalRate' => ($pendingTotal + $approvedTotal) > 0 ? round(($approvedTotal / ($pendingTotal + $approvedTotal)) * 100, 1) : 0,
        ];

        $monthlyData = $this->getAdMonthlyTrends($categories, $adDistrict);
        $districtData = $this->getAdDistrictBreakdown($adDistrict);

        $viewData = [
            'categoryData' => $categoryData,
            'approvedData' => $approvedData,
            'avgApprovalTimeData' => $avgApprovalTimeData,
            'metrics' => $metrics,
            'monthlyData' => $monthlyData,
            'districtData' => $districtData,
        ];

        Yii::$app->cache->set($cacheKey, $viewData, 300);
        Yii::info("AD Dashboard data sync completed successfully. Pending: {$pendingTotal}, Approved: {$approvedTotal}");
        return $this->render('ad-dashboard', $viewData);
    }

    /**
     * Get monthly trends data for AD dashboard
     */
    private function getAdMonthlyTrends($categories, $adDistrict)
    {
        $monthlyData = [
            'categories' => [],
            'pending' => [],
            'approved' => [],
        ];
        $months = [];
        $currentYear = date('Y');
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = ['pending' => 0, 'approved' => 0];
        }
        foreach ($categories as $models) {
            foreach ($models as $model) {
                try {
                    // Determine district field for each model
                    $districtField = null;
                    if ($model === Skipper::class || $model === SkipperRenew::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === BoatNumbers::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === FishermanRegisterdBoatLicense::class) {
                        $districtField = 'district';
                    } elseif ($model === NationalLicense::class || $model === HighseasLicense::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === BoatNumberCancelRequests::class || $model === BoatNumberTransferRequest::class) {
                        $districtField = 'new_landing_district';
                    }

                    $query = $model::find()
                        ->select([
                            'month' => new Expression('MONTH(created)'),
                            'pending' => new Expression('SUM(CASE WHEN status = ' . Constant::Pending . ' AND (approval_stage = ' . Constant::AD . ' OR approval_stage = "' . (string) Constant::AD . '" OR approval_stage = "AD" OR FIND_IN_SET("AD", approval_stage)) THEN 1 ELSE 0 END)'),
                            'approved' => new Expression('SUM(CASE WHEN status IN (200, 403) OR approval_stage = "Completed" THEN 1 ELSE 0 END)'),
                        ])
                        ->where(['YEAR(created)' => $currentYear])
                        ->andWhere(['not', ['created' => null]]);
                    if ($districtField) {
                        $query->andWhere([$districtField => $adDistrict]);
                    }
                    $rows = $query->groupBy('MONTH(created)')->asArray()->all();
                    foreach ($rows as $row) {
                        $m = (int) $row['month'];
                        if (isset($months[$m])) {
                            $months[$m]['pending'] += (int) ($row['pending'] ?? 0);
                            $months[$m]['approved'] += (int) ($row['approved'] ?? 0);
                        }
                    }
                } catch (Exception $e) {
                    Yii::error("Error processing monthly trends for " . $model . ": " . $e->getMessage());
                }
            }
        }
        foreach ($months as $m => $data) {
            $monthlyData['categories'][] = date('M', mktime(0, 0, 0, $m, 10));
            $monthlyData['pending'][] = (int) $data['pending'];
            $monthlyData['approved'][] = (int) $data['approved'];
        }
        return $monthlyData;
    }

    /**
     * Get district breakdown for AD dashboard
     */
    private function getAdDistrictBreakdown($adDistrict)
    {
        try {
            $districtRows = FishermanRegisterdBoatLicense::find()
                ->select([
                    'name' => 'district',
                    'y' => new Expression('COUNT(*)')
                ])
                ->where(['status' => Constant::Pending])
                ->andWhere($this->approvalStageCondition(Constant::AD, 'AD'))
                ->andWhere(['district' => $adDistrict])
                ->andWhere(['not', ['district' => null]])
                ->andWhere(['!=', 'district', ''])
                ->groupBy('district')
                ->orderBy(['y' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();
            return array_map(function ($row) {
                return [
                    'name' => $row['name'] ?? 'Unknown',
                    'y' => (int) ($row['y'] ?? 0)
                ];
            }, $districtRows);
        } catch (Exception $e) {
            Yii::error("Error getting AD district breakdown: " . $e->getMessage());
            return [];
        }
    }

    /**
     * FI Dashboard action
     */
    public function actionFiDashboard()
    {
        // Only allow FI users
        if (!isset(Yii::$app->user->identity) || !UserTypeUtil::hasType(Constant::FI)) {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }

        // Get FI's division
        $fiDivision = null;
        if (isset(Yii::$app->user->identity->profile_id)) {
            $profile = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);
            if ($profile && $profile->division) {
                $fiDivision = $profile->division;
            }
        }
        if (!$fiDivision) {
            throw new ForbiddenHttpException('No division assigned to your profile.');
        }

        // Check database connection
        try {
            Yii::$app->db->open();
        } catch (Exception $e) {
            Yii::error("Database connection failed: " . $e->getMessage());
            throw new ServerErrorHttpException('Database connection failed. Please try again later.');
        }

        // Try to get cached data first
        $cacheKey = 'fi_dashboard_data_' . Yii::$app->user->id . '_' . time();
        $cachedData = false; // Force fresh data
        if (is_array($cachedData)) {
            Yii::info("Using cached dashboard data for user " . Yii::$app->user->id);
            return $this->render('fi-dashboard', $cachedData);
        }

        $categories = [
            'skipper_license' => [Skipper::class, SkipperRenew::class],
            'boat_numbers' => [BoatNumbers::class],
            'boat_registration' => [FishermanRegisterdBoatLicense::class],
            'national_license' => [NationalLicense::class],
            'highseas_license' => [HighseasLicense::class],
            'boat_cancel' => [BoatNumberCancelRequests::class],
            'boat_transfer' => [BoatNumberTransferRequest::class],
        ];

        $categoryData = [];
        $approvedData = [];
        $avgApprovalTimeData = [];
        $pendingTotal = 0;
        $approvedTotal = 0;
        $approvalTimeSum = 0;
        $approvalTimeCount = 0;
        $debugInfo = [];

        foreach ($categories as $key => $models) {
            $pending = 0;
            $approved = 0;
            $timeSum = 0;
            $timeCount = 0;
            $debugInfo[$key] = [];

            foreach ($models as $model) {
                $pendingCount = 0;
                $approvedCount = 0;
                try {
                    // Determine division field for each model
                    $divisionField = null;
                    if ($model === Skipper::class || $model === SkipperRenew::class) {
                        $divisionField = 'fisheries_division';
                    } elseif ($model === BoatNumbers::class) {
                        $divisionField = 'fisheries_division';
                    } elseif ($model === FishermanRegisterdBoatLicense::class) {
                        $divisionField = 'division';
                    } elseif ($model === NationalLicense::class || $model === HighseasLicense::class) {
                        $divisionField = 'division';
                    } elseif ($model === BoatNumberCancelRequests::class || $model === BoatNumberTransferRequest::class) {
                        // these models don't have a direct "division" column in many schemas
                        $divisionField = null;
                    }

                    // Pending: status = Pending AND approval_stage = FI AND division
                    $modelTable = $model::tableName();
                    if ($divisionField) {
                        $pendingQuery = $model::find()->where(["{$modelTable}.status" => Constant::Pending])
                            ->andWhere($this->approvalStageCondition(Constant::FI, 'FI'))
                            ->andWhere([$divisionField => $fiDivision]);
                    } else {
                        // For models without a direct division column (transfer/cancel), join to boat_numbers
                        $boatTable = BoatNumbers::tableName();
                        $pendingQuery = $model::find()->where(["{$modelTable}.status" => Constant::Pending])
                            ->andWhere($this->approvalStageCondition(Constant::FI, 'FI'))
                            ->joinWith('boatNumber')
                            ->andWhere(["{$boatTable}.fisheries_division" => $fiDivision]);
                    }
                    $pendingCount = $pendingQuery->count();

                    // Approved: status in (200,403) or approval_stage = Completed
                    if ($divisionField) {
                        $approvedQuery = $model::find()->where([
                            'or',
                            ["{$modelTable}.status" => [200, 403]],
                            ["{$modelTable}.approval_stage" => 'Completed']
                        ])->andWhere([$divisionField => $fiDivision]);
                    } else {
                        $boatTable = BoatNumbers::tableName();
                        $approvedQuery = $model::find()->where([
                            'or',
                            ["{$modelTable}.status" => [200, 403]],
                            ["{$modelTable}.approval_stage" => 'Completed']
                        ])->joinWith('boatNumber')->andWhere(["{$boatTable}.fisheries_division" => $fiDivision]);
                    }
                    $approvedCount = $approvedQuery->count();

                    $pending += $pendingCount;
                    $approved += $approvedCount;

                    $debugInfo[$key][$model] = [
                        'pending' => $pendingCount,
                        'approved' => $approvedCount,
                        'total' => $model::find()->count()
                    ];

                    // Approval time for completed items
                    $timeRowsQuery = $model::find()
                        ->select([
                            'diff' => new Expression('DATEDIFF(COALESCE(' . $modelTable . '.approved_time, ' . $modelTable . '.updated_at), ' . $modelTable . '.created)')
                        ])
                        ->where(['or', ["{$modelTable}.status" => [200, 403]], ["{$modelTable}.approval_stage" => 'Completed']])
                        ->andWhere(['not', ["{$modelTable}.created" => null]])
                        ->andWhere(['or', ['not', ["{$modelTable}.approved_time" => null]], ['not', ["{$modelTable}.updated_at" => null]]]);
                    if ($divisionField) {
                        $timeRowsQuery->andWhere([$divisionField => $fiDivision]);
                    } else {
                        $boatTable = BoatNumbers::tableName();
                        $timeRowsQuery->joinWith('boatNumber')->andWhere(["{$boatTable}.fisheries_division" => $fiDivision]);
                    }
                    $timeRows = $timeRowsQuery->asArray()->all();
                    foreach ($timeRows as $row) {
                        if (isset($row['diff']) && is_numeric($row['diff']) && $row['diff'] >= 0) {
                            $timeSum += (int) $row['diff'];
                            $timeCount++;
                        }
                    }
                } catch (Exception $e) {
                    Yii::error("Error processing model {$model} for category {$key}: " . $e->getMessage());
                    $debugInfo[$key][$model] = ['error' => $e->getMessage()];
                }
            }
            $categoryData[$key] = (int) $pending;
            $approvedData[$key] = (int) $approved;
            $avgApprovalTimeData[$key] = $timeCount > 0 ? round($timeSum / $timeCount, 1) : null;
            $pendingTotal += $pending;
            $approvedTotal += $approved;
            $approvalTimeSum += $timeSum;
            $approvalTimeCount += $timeCount;
        }

        Yii::info("FI Dashboard data collection debug info: " . json_encode($debugInfo));
        Yii::info("FI Category data: " . json_encode($categoryData));

        $metrics = [
            'pendingApprovals' => (int) $pendingTotal,
            'approvedByFI' => (int) $approvedTotal,
            // keep legacy key present if view expects it
            'approvedByAD' => (int) $approvedTotal,
            'avgApprovalTime' => $approvalTimeCount > 0 ? round($approvalTimeSum / $approvalTimeCount, 1) : null,
            'approvalRate' => ($pendingTotal + $approvedTotal) > 0 ? round(($approvedTotal / ($pendingTotal + $approvedTotal)) * 100, 1) : 0,
        ];

        $monthlyData = $this->getFiMonthlyTrends($categories, $fiDivision);
        $districtData = $this->getFiDistrictBreakdown($fiDivision);

        $viewData = [
            'categoryData' => $categoryData,
            'approvedData' => $approvedData,
            'avgApprovalTimeData' => $avgApprovalTimeData,
            'metrics' => $metrics,
            'monthlyData' => $monthlyData,
            'districtData' => $districtData,
        ];

        Yii::$app->cache->set($cacheKey, $viewData, 300);
        Yii::info("FI Dashboard data sync completed successfully. Pending: {$pendingTotal}, Approved: {$approvedTotal}");

        return $this->render('fi-dashboard', $viewData);
    }

    /**
     * Get monthly trends data for FI dashboard
     */
    private function getFiMonthlyTrends($categories, $fiDivision)
    {
        $monthlyData = [
            'categories' => [],
            'pending' => [],
            'approved' => [],
        ];
        $months = [];
        $currentYear = date('Y');
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = ['pending' => 0, 'approved' => 0];
        }

        foreach ($categories as $models) {
            foreach ($models as $model) {
                try {
                    // determine division field for each model
                    $divisionField = null;
                    if ($model === Skipper::class || $model === SkipperRenew::class) {
                        $divisionField = 'fisheries_division';
                    } elseif ($model === BoatNumbers::class) {
                        $divisionField = 'fisheries_division';
                    } elseif ($model === FishermanRegisterdBoatLicense::class) {
                        $divisionField = 'division';
                    } elseif ($model === NationalLicense::class || $model === HighseasLicense::class) {
                        $divisionField = 'division';
                    } elseif ($model === BoatNumberCancelRequests::class || $model === BoatNumberTransferRequest::class) {
                        $divisionField = null;
                    }

                    $query = $model::find()
                        ->select([
                            'month' => new Expression('MONTH(created)'),
                            'pending' => new Expression('SUM(CASE WHEN status = ' . Constant::Pending . ' AND (approval_stage = ' . Constant::FI . ' OR approval_stage = "' . (string) Constant::FI . '" OR approval_stage = "FI" OR FIND_IN_SET("FI", approval_stage)) THEN 1 ELSE 0 END)'),
                            'approved' => new Expression('SUM(CASE WHEN status IN (200, 403) OR approval_stage = "Completed" THEN 1 ELSE 0 END)'),
                        ])
                        ->where(['YEAR(created)' => $currentYear])
                        ->andWhere(['not', ['created' => null]]);

                    if ($divisionField) {
                        $query->andWhere([$divisionField => $fiDivision]);
                    } else {
                        if ($model === BoatNumberTransferRequest::class || $model === BoatNumberCancelRequests::class) {
                            $boatTable = BoatNumbers::tableName();
                            $query->joinWith('boatNumber')->andWhere(["{$boatTable}.fisheries_division" => $fiDivision]);
                        }
                    }

                    $rows = $query->groupBy('MONTH(created)')->asArray()->all();
                    foreach ($rows as $row) {
                        $m = (int) $row['month'];
                        if (isset($months[$m])) {
                            $months[$m]['pending'] += (int) ($row['pending'] ?? 0);
                            $months[$m]['approved'] += (int) ($row['approved'] ?? 0);
                        }
                    }
                } catch (Exception $e) {
                    Yii::error("Error processing monthly trends for " . $model . ": " . $e->getMessage());
                }
            }
        }

        foreach ($months as $m => $data) {
            $monthlyData['categories'][] = date('M', mktime(0, 0, 0, $m, 10));
            $monthlyData['pending'][] = (int) $data['pending'];
            $monthlyData['approved'][] = (int) $data['approved'];
        }

        return $monthlyData;
    }

    /**
     * Get division breakdown for FI dashboard (groups by division)
     */
    private function getFiDistrictBreakdown($fiDivision)
    {
        try {
            $rows = FishermanRegisterdBoatLicense::find()
                ->select([
                    'name' => 'division',
                    'y' => new Expression('COUNT(*)')
                ])
                ->where(['status' => Constant::Pending])
                ->andWhere($this->approvalStageCondition(Constant::FI, 'FI'))
                ->andWhere(['not', ['division' => null]])
                ->andWhere(['!=', 'division', ''])
                ->groupBy('division')
                ->orderBy(['y' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();

            return array_map(function ($row) {
                return [
                    'name' => $row['name'] ?? 'Unknown',
                    'y' => (int) ($row['y'] ?? 0)
                ];
            }, $rows);
        } catch (Exception $e) {
            Yii::error("Error getting FI division breakdown: " . $e->getMessage());
            return [];
        }
    }

    /**
     * DFI Dashboard action
     */
    public function actionDfiDashboard()
    {
        // Only allow DFI users
        if (!isset(\Yii::$app->user->identity) || \Yii::$app->user->identity->type != \backend\config\Constant::DFI) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to access this page.');
        }

        // Get DFI's district from session
        $dfiDistrict = \Yii::$app->session->get("officer_district");
        if (!$dfiDistrict) {
            // Try to get from profile as fallback
            if (isset(\Yii::$app->user->identity->profile_id)) {
                $profile = \backend\models\ProfileOfficer::findOne(\Yii::$app->user->identity->profile_id);
                if ($profile && $profile->district) {
                    $dfiDistrict = $profile->district;
                }
            }
        }
        if (!$dfiDistrict) {
            throw new \yii\web\ForbiddenHttpException('No district assigned to your profile.');
        }

        // Check database connection
        try {
            \Yii::$app->db->open();
        } catch (\Exception $e) {
            \Yii::error("Database connection failed: " . $e->getMessage());
            throw new \yii\web\ServerErrorHttpException('Database connection failed. Please try again later.');
        }

        // Try to get cached data first
        $cacheKey = 'dfi_dashboard_data_' . \Yii::$app->user->id . '_' . time();
        $cachedData = false; // Force fresh data
        if ($cachedData !== false) {
            \Yii::info("Using cached dashboard data for user " . \Yii::$app->user->id);
            return $this->render('dfi-dashboard', $cachedData);
        }

        $categories = [
            'skipper_license' => [Skipper::class, SkipperRenew::class],
            'boat_numbers' => [BoatNumbers::class],
            'boat_registration' => [FishermanRegisterdBoatLicense::class],
            'national_license' => [NationalLicense::class],
            'highseas_license' => [HighseasLicense::class],
            'boat_cancel' => [BoatNumberCancelRequests::class],
            'boat_transfer' => [BoatNumberTransferRequest::class],
        ];

        $categoryData = [];
        $approvedData = [];
        $avgApprovalTimeData = [];
        $pendingTotal = 0;
        $approvedTotal = 0;
        $approvalTimeSum = 0;
        $approvalTimeCount = 0;
        $debugInfo = [];

        foreach ($categories as $key => $models) {
            $pending = 0;
            $approved = 0;
            $timeSum = 0;
            $timeCount = 0;
            $debugInfo[$key] = [];

            foreach ($models as $model) {
                $pendingCount = 0;
                $approvedCount = 0;
                try {
                    // Determine district field for each model
                    $districtField = null;
                    if ($model === Skipper::class || $model === SkipperRenew::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === BoatNumbers::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === FishermanRegisterdBoatLicense::class) {
                        $districtField = 'district';
                    } elseif ($model === NationalLicense::class || $model === HighseasLicense::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === BoatNumberCancelRequests::class || $model === BoatNumberTransferRequest::class) {
                        $districtField = 'new_landing_district';
                    }

                    // Pending: status = Pending AND approval_stage = DFI AND district
                    // Note: Based on dfi-menu.php, DFI may also see items at FI stage, but we'll use DFI stage here
                    $pendingQuery = $model::find()->where(['status' => \backend\config\Constant::Pending])
                        ->andWhere($this->approvalStageCondition(\backend\config\Constant::DFI, 'DFI'));
                    if ($districtField) {
                        $pendingQuery->andWhere([$districtField => $dfiDistrict]);
                    }
                    $pendingCount = $pendingQuery->count();

                    // Approved: status in (200, 403) or approval_stage = Completed AND district
                    $approvedQuery = $model::find()->where([
                        'or',
                        ['status' => [200, 403]],
                        ['approval_stage' => 'Completed']
                    ]);
                    if ($districtField) {
                        $approvedQuery->andWhere([$districtField => $dfiDistrict]);
                    }
                    $approvedCount = $approvedQuery->count();

                    $pending += $pendingCount;
                    $approved += $approvedCount;

                    $debugInfo[$key][$model] = [
                        'pending' => $pendingCount,
                        'approved' => $approvedCount,
                        'total' => $model::find()->count()
                    ];

                    // Approval time for completed items
                    $timeRowsQuery = $model::find()
                        ->select([
                            'diff' => new Expression('DATEDIFF(COALESCE(approved_time, updated_at), created)')
                        ])
                        ->where(['or', ['status' => [200, 403]], ['approval_stage' => 'Completed']])
                        ->andWhere(['not', ['created' => null]])
                        ->andWhere(['or', ['not', ['approved_time' => null]], ['not', ['updated_at' => null]]]);
                    if ($districtField) {
                        $timeRowsQuery->andWhere([$districtField => $dfiDistrict]);
                    }
                    $timeRows = $timeRowsQuery->asArray()->all();
                    foreach ($timeRows as $row) {
                        if (isset($row['diff']) && is_numeric($row['diff']) && $row['diff'] >= 0) {
                            $timeSum += (int) $row['diff'];
                            $timeCount++;
                        }
                    }
                } catch (\Exception $e) {
                    \Yii::error("Error processing model {$model} for category {$key}: " . $e->getMessage());
                    $debugInfo[$key][$model] = ['error' => $e->getMessage()];
                }
            }
            $categoryData[$key] = (int) $pending;
            $approvedData[$key] = (int) $approved;
            $avgApprovalTimeData[$key] = $timeCount > 0 ? round($timeSum / $timeCount, 1) : null;
            $pendingTotal += $pending;
            $approvedTotal += $approved;
            $approvalTimeSum += $timeSum;
            $approvalTimeCount += $timeCount;
        }

        \Yii::info("DFI Dashboard data collection debug info: " . json_encode($debugInfo));
        \Yii::info("DFI Category data: " . json_encode($categoryData));

        $metrics = [
            'pendingApprovals' => (int) $pendingTotal,
            'approvedByDFI' => (int) $approvedTotal,
            'avgApprovalTime' => $approvalTimeCount > 0 ? round($approvalTimeSum / $approvalTimeCount, 1) : null,
            'approvalRate' => ($pendingTotal + $approvedTotal) > 0 ? round(($approvedTotal / ($pendingTotal + $approvedTotal)) * 100, 1) : 0,
        ];

        $monthlyData = $this->getDfiMonthlyTrends($categories, $dfiDistrict);
        $districtData = $this->getDfiDistrictBreakdown($dfiDistrict);

        $viewData = [
            'categoryData' => $categoryData,
            'approvedData' => $approvedData,
            'avgApprovalTimeData' => $avgApprovalTimeData,
            'metrics' => $metrics,
            'monthlyData' => $monthlyData,
            'districtData' => $districtData,
        ];

        \Yii::$app->cache->set($cacheKey, $viewData, 300);
        \Yii::info("DFI Dashboard data sync completed successfully. Pending: {$pendingTotal}, Approved: {$approvedTotal}");
        return $this->render('dfi-dashboard', $viewData);
    }

    /**
     * Get monthly trends data for DFI dashboard
     */
    private function getDfiMonthlyTrends($categories, $dfiDistrict)
    {
        $monthlyData = [
            'categories' => [],
            'pending' => [],
            'approved' => [],
        ];
        $months = [];
        $currentYear = date('Y');
        for ($month = 1; $month <= 12; $month++) {
            $months[$month] = ['pending' => 0, 'approved' => 0];
        }
        foreach ($categories as $models) {
            foreach ($models as $model) {
                try {
                    // Determine district field for each model
                    $districtField = null;
                    if ($model === Skipper::class || $model === SkipperRenew::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === BoatNumbers::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === FishermanRegisterdBoatLicense::class) {
                        $districtField = 'district';
                    } elseif ($model === NationalLicense::class || $model === HighseasLicense::class) {
                        $districtField = 'fisheries_district';
                    } elseif ($model === BoatNumberCancelRequests::class || $model === BoatNumberTransferRequest::class) {
                        $districtField = 'new_landing_district';
                    }

                    $query = $model::find()
                        ->select([
                            'month' => new Expression('MONTH(created)'),
                            'pending' => new Expression('SUM(CASE WHEN status = ' . \backend\config\Constant::Pending . ' AND (approval_stage = ' . \backend\config\Constant::DFI . ' OR approval_stage = "' . (string) \backend\config\Constant::DFI . '" OR approval_stage = "DFI" OR FIND_IN_SET("DFI", approval_stage)) THEN 1 ELSE 0 END)'),
                            'approved' => new Expression('SUM(CASE WHEN status IN (200, 403) OR approval_stage = "Completed" THEN 1 ELSE 0 END)'),
                        ])
                        ->where(['YEAR(created)' => $currentYear])
                        ->andWhere(['not', ['created' => null]]);
                    if ($districtField) {
                        $query->andWhere([$districtField => $dfiDistrict]);
                    }
                    $rows = $query->groupBy('MONTH(created)')->asArray()->all();
                    foreach ($rows as $row) {
                        $m = (int) $row['month'];
                        if (isset($months[$m])) {
                            $months[$m]['pending'] += (int) ($row['pending'] ?? 0);
                            $months[$m]['approved'] += (int) ($row['approved'] ?? 0);
                        }
                    }
                } catch (\Exception $e) {
                    \Yii::error("Error processing monthly trends for " . $model . ": " . $e->getMessage());
                }
            }
        }
        foreach ($months as $m => $data) {
            $monthlyData['categories'][] = date('M', mktime(0, 0, 0, $m, 10));
            $monthlyData['pending'][] = (int) $data['pending'];
            $monthlyData['approved'][] = (int) $data['approved'];
        }
        return $monthlyData;
    }

    /**
     * Get district breakdown for DFI dashboard
     */
    private function getDfiDistrictBreakdown($dfiDistrict)
    {
        try {
            $districtRows = FishermanRegisterdBoatLicense::find()
                ->select([
                    'name' => 'district',
                    'y' => new Expression('COUNT(*)')
                ])
                ->where(['status' => \backend\config\Constant::Pending])
                ->andWhere($this->approvalStageCondition(\backend\config\Constant::DFI, 'DFI'))
                ->andWhere(['district' => $dfiDistrict])
                ->andWhere(['not', ['district' => null]])
                ->andWhere(['!=', 'district', ''])
                ->groupBy('district')
                ->orderBy(['y' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();
            return array_map(function ($row) {
                return [
                    'name' => $row['name'] ?? 'Unknown',
                    'y' => (int) ($row['y'] ?? 0)
                ];
            }, $districtRows);
        } catch (\Exception $e) {
            \Yii::error("Error getting DFI district breakdown: " . $e->getMessage());
            return [];
        }
    }

}