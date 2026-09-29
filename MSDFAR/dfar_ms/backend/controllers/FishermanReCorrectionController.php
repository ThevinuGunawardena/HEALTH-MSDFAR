<?php

namespace backend\controllers;
use backend\models\User;
use Yii;
use backend\models\ProfileFisherman;
use backend\models\FishermanReCorrection;
use backend\models\SkipperReCorrection;
use backend\models\Skipper;
use backend\models\CorrectionIgnore;
use yii\web\Controller;
use yii\web\NotFoundHttpException; 
use yii\data\ArrayDataProvider;

class FishermanReCorrectionController extends Controller
{
    public function actionIndex($fishermanId, $mainId = null)
    {
        // Read-only lookup, never saved/modified
        $model = ProfileFisherman::findOne($fishermanId);

        if (!$model) {
            throw new NotFoundHttpException('Fisherman not found.');
        }

        $skipper = Skipper::findOne(['fisherman_id' => $fishermanId]);

        if ($skipper !== null) {
            return $this->handleSkipperCorrection($skipper, $model, $mainId);
        }

        return $this->handleFishermanCorrection($model, $fishermanId, $mainId);
    }

   public function actionIgnore($fishermanId, $mainId = null)
{
    if ($mainId !== null) {
        $ignore = new CorrectionIgnore();
        $ignore->main_id = $mainId;
        $ignore->ignore_type = CorrectionIgnore::Fisherman;
        $ignore->fisherman_id = $fishermanId;
        $ignore->skipper_id = null;

        $user = User::findOne($mainId);
        $officerProfile = $user ? $user->officerProfile : null;

        $ignore->division = $officerProfile->division ?? null;
        $ignore->district = $officerProfile->district ?? null;

        if (!$ignore->save()) {
            Yii::error($ignore->getErrors(), __METHOD__);
        }
    }

    return $this->redirect(['/site/index']);
}
    /**
     * Normal (non-skipper) fisherman flow:
     * - 3-day cooldown per main_id
     * - once ANY correction record exists for this fisherman, never ask again
     * - only the NIC is checked/corrected
     */
    private function handleFishermanCorrection(ProfileFisherman $model, $fishermanId, $mainId)
{
    if ($this->hasAnyFishermanRecord($fishermanId)) {
        return $this->redirect(['/site/index']);
    }

    if ($this->isValidNic($model->nic)) {
        return $this->redirect(['/site/index']);
    }

    if (Yii::$app->request->isPost) {
        $registration = new FishermanReCorrection();
        $registration->fisherman_id = $fishermanId;
        $registration->main_id = $mainId;
        $registration->load(Yii::$app->request->post());

        // fisherman_id / main_id come from the URL, not the form
        $registration->fisherman_id = $fishermanId;
        $registration->main_id = $mainId;

        $user = User::findOne($mainId);
        $officerProfile = $user ? $user->officerProfile : null;

        if ($officerProfile !== null) {
            $registration->division = $officerProfile->division;
            $registration->district = $officerProfile->district;
        } else {
            $registration->division = null;
            $registration->district = null;
        }

        if (empty(trim((string) $registration->nic))) {
            Yii::$app->session->setFlash('error', 'NIC cannot be empty.');
        } elseif (!$this->isValidNic($registration->nic)) {
            Yii::$app->session->setFlash('error', 'Please enter a valid NIC number.');
        } elseif ($registration->save()) {
            Yii::$app->session->setFlash('success', 'NIC correction submitted successfully.');
        } else {
            Yii::error($registration->getErrors(), __METHOD__);
            Yii::$app->session->setFlash('error', 'Could not save correction. Please try again.');
        }
    }

    // Always bounce back to site/index - no separate rendered view for this flow.
    return $this->redirect(['/site/index']);
}

    /**
     * Skipper flow:
     * - NO 3-day cooldown
     * - checks skipper_uid (from `skipper`) and nic (from linked `profile_fisherman`)
     * - if either is invalid on the source record, check skipper_re_correction
     *   for an already-submitted valid value for that field - if found, treat
     *   it as fixed and don't ask again
     */
    private function handleSkipperCorrection(Skipper $skipper, ProfileFisherman $model, $mainId)
    {
        $uidValid = $this->isValidSkipperUid($skipper->skipper_uid)
            || $this->hasFixedSkipperField($skipper->id, 'skipper_uid');

        $nicValid = $this->isValidNic($model->nic)
            || $this->hasFixedSkipperField($skipper->id, 'nic');

        if ($uidValid && $nicValid) {
            return $this->redirect(['/site/index']);
        }

        $registration = new SkipperReCorrection();
        $registration->skipper_id = $skipper->id;
        $registration->main_id = $mainId;



        if (Yii::$app->request->isPost) {
            $registration->load(Yii::$app->request->post());

            // skipper_id / main_id come from the URL, not the form
            $registration->skipper_id = $skipper->id;
            $registration->main_id = $mainId;
            $user = User::findOne($mainId);
            $officerProfile = $user ? $user->officerProfile : null;

            if ($officerProfile !== null) {
                $registration->division = $officerProfile->division;
                $registration->district = $officerProfile->district;
            } else {
                $registration->division = null;
                $registration->district = null;
            }

            $hasError = false;

            if (!$uidValid) {
                if (empty(trim((string) $registration->skipper_uid))) {
                    $registration->addError('skipper_uid', 'Skipper UID cannot be empty.');
                    $hasError = true;
                } elseif (!$this->isValidSkipperUid($registration->skipper_uid)) {
                    $registration->addError('skipper_uid', 'Please enter a valid Skipper UID.');
                    $hasError = true;
                }
            }

            if (!$nicValid) {
                if (empty(trim((string) $registration->nic))) {
                    $registration->addError('nic', 'NIC cannot be empty.');
                    $hasError = true;
                } elseif (!$this->isValidNic($registration->nic)) {
                    $registration->addError('nic', 'Please enter a valid NIC number.');
                    $hasError = true;
                }
            }

            if (!$hasError && $registration->save()) {
                return $this->redirect(['/site/index']);
            }

            if ($registration->hasErrors()) {
                Yii::error($registration->getErrors(), __METHOD__);
            }
        }

        return $this->render('skipper', [
            'model' => $model,
            'skipper' => $skipper,
            'registration' => $registration,
            'mainId' => $mainId,
            'uidValid' => $uidValid,
            'nicValid' => $nicValid,
        ]);
    }
    // ---------------------------------------------------------------
    // Lookups
    // ---------------------------------------------------------------

    private function hasRecentFishermanRecordForMain($mainId)
    {
        $threeDaysAgo = date('Y-m-d H:i:s', strtotime('-3 days'));

        return FishermanReCorrection::find()
            ->where(['main_id' => $mainId])
            ->andWhere(['>=', 'updated_at', $threeDaysAgo])
            ->exists();
    }

    private function hasAnyFishermanRecord($fishermanId)
    {
        return FishermanReCorrection::find()
            ->where(['fisherman_id' => $fishermanId])
            ->exists();
    }

    // ---------------------------------------------------------------
    // Format validators
    // ---------------------------------------------------------------

    /**
     * Validates Sri Lankan NIC format.
     * Old format: 9 digits + V or X (e.g. 123456789V)
     * New format: 12 digits (e.g. 200012345678)
     */
    private function isValidNic($nic)
    {
        if (empty($nic)) {
            return false;
        }

        return (bool) preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', trim($nic));
    }

    /**
     * Validates skipper_uid format.
     * TODO: placeholder pattern - replace with the real format spec.
     * Currently accepts 4-100 alphanumeric characters / dashes / underscores.
     */
    private function isValidSkipperUid($uid)
    {
        if (empty($uid)) {
            return false;
        }

        return (bool) preg_match('/^[A-Za-z0-9_-]{4,100}$/', trim($uid));
    }

    /**
     * Checks whether this skipper already has a skipper_re_correction record
     * with a VALID value for the given field ('skipper_uid' or 'nic').
     * Used so a field already fixed once isn't asked for again.
     */
    private function hasFixedSkipperField($skipperId, $field)
    {
        $records = SkipperReCorrection::find()
            ->where(['skipper_id' => $skipperId])
            ->andWhere(['not', [$field => null]])
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();

        foreach ($records as $record) {
            $value = $record->$field;

            $isValid = $field === 'skipper_uid'
                ? $this->isValidSkipperUid($value)
                : $this->isValidNic($value);

            if ($isValid) {
                return true;
            }
        }

        return false;
    }



public function actionOfficerReport()
{
    $rows = CorrectionIgnore::find()
        ->select([
            'correction_ignore.main_id',
            'profile_officer.first_name',
            'profile_officer.last_name',
            'profile_officer.mobile_phone',
            'COUNT(correction_ignore.id) AS total_ignores',
        ])
        ->innerJoin('user', 'user.id = correction_ignore.main_id')
        ->innerJoin('profile_officer', 'profile_officer.id = user.profile_id')
        ->groupBy([
            'correction_ignore.main_id',
            'profile_officer.first_name',
            'profile_officer.last_name',
            'profile_officer.mobile_phone',
        ])
        ->orderBy(['total_ignores' => SORT_DESC])
        ->asArray()
        ->all();

    $dataProvider = new ArrayDataProvider([
        'allModels' => $rows,
        'sort' => [
            'attributes' => ['total_ignores', 'first_name'],
            'defaultOrder' => ['total_ignores' => SORT_DESC],
        ],
        'pagination' => [
            'pageSize' => 20,
        ],
    ]);

    return $this->render('officer-report', [
        'dataProvider' => $dataProvider,
    ]);
}



public function actionView($mainId)
{
    $user = User::findOne($mainId);
    $officerProfile = $user ? $user->officerProfile : null;

    $officerName = $officerProfile
        ? trim($officerProfile->first_name . ' ' . $officerProfile->last_name)
        : 'Unknown Officer';

    // --- Ignores, grouped by month + division + district + type ---
    $ignoreRows = CorrectionIgnore::find()
        ->select([
            "DATE_FORMAT(updated_at, '%Y-%m') AS month",
            'division',
            'district',
            'ignore_type',
            'COUNT(*) AS total',
        ])
        ->where(['main_id' => $mainId])
        ->groupBy(['month', 'division', 'district', 'ignore_type'])
        ->orderBy(['month' => SORT_DESC])
        ->asArray()
        ->all();

    // --- Fisherman corrections, grouped by month + division + district ---
    $fishermanRows = FishermanReCorrection::find()
        ->select([
            "DATE_FORMAT(updated_at, '%Y-%m') AS month",
            'division',
            'district',
            'COUNT(*) AS total',
        ])
        ->where(['main_id' => $mainId])
        ->groupBy(['month', 'division', 'district'])
        ->orderBy(['month' => SORT_DESC])
        ->asArray()
        ->all();

    // --- Skipper corrections, grouped by month + division + district ---
    $skipperRows = SkipperReCorrection::find()
        ->select([
            "DATE_FORMAT(updated_at, '%Y-%m') AS month",
            'division',
            'district',
            'COUNT(*) AS total',
        ])
        ->where(['main_id' => $mainId])
        ->groupBy(['month', 'division', 'district'])
        ->orderBy(['month' => SORT_DESC])
        ->asArray()
        ->all();

    $ignoreProvider = new ArrayDataProvider([
        'allModels' => $ignoreRows,
        'pagination' => ['pageSize' => 20],
        'sort' => ['attributes' => ['month', 'division', 'district', 'ignore_type', 'total']],
    ]);

    $fishermanProvider = new ArrayDataProvider([
        'allModels' => $fishermanRows,
        'pagination' => ['pageSize' => 20],
        'sort' => ['attributes' => ['month', 'division', 'district', 'total']],
    ]);

    $skipperProvider = new ArrayDataProvider([
        'allModels' => $skipperRows,
        'pagination' => ['pageSize' => 20],
        'sort' => ['attributes' => ['month', 'division', 'district', 'total']],
    ]);

    return $this->render('officer-view', [
        'officerName' => $officerName,
        'mainId' => $mainId,
        'ignoreProvider' => $ignoreProvider,
        'fishermanProvider' => $fishermanProvider,
        'skipperProvider' => $skipperProvider,
    ]);
}

/**
 * Finds a fisherman in the officer's district/division that has a bad NIC
 * and hasn't been corrected yet. Picks randomly among the first 10 such
 * candidates so the same fisherman isn't always shown first.
 *
 * @return ProfileFisherman|null
 */
public static function getPendingFishermanForOfficer($mainId, $district, $division, $ignoreCooldownDays = 3)
{
    if (empty($district)) {
        return null;
    }

    $oneDayAgo = date('Y-m-d H:i:s', strtotime('-1 day'));

    $handledToday = FishermanReCorrection::find()
        ->where(['main_id' => $mainId])
        ->andWhere(['>=', 'updated_at', $oneDayAgo])
        ->exists();

    if (!$handledToday) {
        $handledToday = CorrectionIgnore::find()
            ->where([
                'main_id'     => $mainId,
                'ignore_type' => CorrectionIgnore::Fisherman,
            ])
            ->andWhere(['>=', 'updated_at', $oneDayAgo])
            ->exists();
    }

    if ($handledToday) {
        return null;
    }

    // CHANGED: district only, division removed from filter
    $candidates = ProfileFisherman::find()
        ->where(['district' => $district])
        ->andWhere(['not', ['nic' => null]])
        ->all();

    $ignoreCooldownCutoff = date('Y-m-d H:i:s', strtotime("-{$ignoreCooldownDays} days"));

    $pending = [];

    foreach ($candidates as $fisherman) {
        $nicValid = (bool) preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', trim((string) $fisherman->nic));
        if ($nicValid) {
            continue;
        }

        $alreadyCorrected = FishermanReCorrection::find()
            ->where(['fisherman_id' => $fisherman->id])
            ->exists();
        if ($alreadyCorrected) {
            continue;
        }

        $recentlyIgnored = CorrectionIgnore::find()
            ->where([
                'main_id'      => $mainId,
                'fisherman_id' => $fisherman->id,
                'ignore_type'  => CorrectionIgnore::Fisherman,
            ])
            ->andWhere(['>=', 'updated_at', $ignoreCooldownCutoff])
            ->exists();
        if ($recentlyIgnored) {
            continue;
        }

        $pending[] = $fisherman;
        if (count($pending) >= 10) {
            break;
        }
    }

    if (empty($pending)) {
        return null;
    }

    return $pending[array_rand($pending)];
}
}