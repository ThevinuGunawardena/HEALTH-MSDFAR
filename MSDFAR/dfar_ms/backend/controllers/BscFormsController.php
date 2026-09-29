<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ProfileOfficers;
use backend\models\MDivision;
use backend\models\MFiDistrict;
use backend\models\Bsc1Submission;
use backend\models\Bsc1SubmissionExtension;
use backend\models\Bsc2SubmissionExtension;
use backend\models\Bsc1Boat;
use backend\models\Bsc1Registration;
use backend\models\Bsc1LicenseWithCraft;
use backend\models\Bsc1AwarenessProgramme;
use backend\models\Bsc2Submission;
use backend\models\Bsc2SeaworthinessCertificates;
use backend\models\Bsc2BoatInsured;
use backend\models\Bsc2LagoonActivity;
use backend\models\SubmissionAuditLog;
use common\components\WebUser;


/**
 * Controller for the BSC-1 / BSC-2 monthly fisheries data collection forms.
 *
 * Route base: bsc-forms/*  — matches the sidebar link in officer-menu.php
 * ($webURL . '/bsc-forms/index'). Must be lowercase in the URL — Yii2's
 * default route resolution only matches ^[a-z][a-z0-9\-_]*$, so a capital
 * "BSC-forms" link (as it was originally written) would 404 regardless of
 * the controller class name.
 *
 * Access:
 *  - FI: fills in their own division's monthly return   -> actionMyReturn()
 *  - AD: reviews/validates/compiles their district      -> actionDistrictReview()
 *
 * These are deliberately separate actions (not one branching action),
 * matching this codebase's own development-dashboard / ad-dashboard / fi-dashboard
 * convention: different roles here see genuinely different *shapes* of
 * content, not just filtered rows of the same view.
 *
 * ── PREREQUISITES — this will not run until these exist ──────────────
 *  1. Migrations run for all tables in bsc_schema.sql.
 *  2. ActiveRecord models generated for each table (backend\models\
 *     Bsc1Submission, Bsc1Boat, Bsc1Registration, Bsc1LicenseWithCraft,
 *     Bsc1AwarenessProgramme, Bsc2Submission, Bsc2SeaworthinessCert,
 *     Bsc2LagoonActivity, SubmissionAuditLog).
 *
 *     Bsc1Submission's rules() should include (add once the model exists —
 *     this is reference text, not live code, since the class doesn't exist
 *     yet to attach a real rules() method to):
 *
 *       public function rules()
 *       {
 *           return [
 *               [['fi_division_id', 'period_date'], 'required'],
 *               [['families', 'active_fishermen', 'population',
 *                 'migrated_imul', 'migrated_iday', 'migrated_ofrp',
 *                 'incident_partial_loss', 'incident_total_loss',
 *                 'incident_natural_deaths', 'incident_missing',
 *                 'licenses_without_craft'], 'integer', 'min' => 0],
 *               // ... other rules (status, approval_stage, etc.)
 *           ];
 *       }
 *
 *     Bsc1Boat / Bsc1Registration / Bsc1LicenseWithCraft's count columns
 *     don't strictly need a matching rule — saveBsc1ChildRecords() below
 *     already clamps every value to >= 0 with max(0, (int) $count) before
 *     it ever reaches these models, since they're saved with save(false)
 *     and never run their own rules() anyway. Add rules() to them only if
 *     they end up being edited from somewhere else (an API, a Gii CRUD
 *     screen) where that clamp wouldn't apply.
 *  3. Bsc1Submission and Bsc2Submission each need a getDivision() relation:
 *       public function getDivision()
 *       {
 *           return $this->hasOne(\backend\models\MDivision::class, ['id' => 'fi_division_id']);
 *       }
 *     (used by district-scoped queries below via joinWith('division'))
 *  4. Views under backend/views/bsc-forms/: my-return.php, district-review.php,
 *     district-review-detail.php, plus partials matching the prototype's cards.
 *
 * ── OPEN DESIGN QUESTIONS — confirm before relying on these ───────────
 *  - Constant::RequestCompleted is reused here as the "validated" status
 *    value, matching the generic $licenseStatus convention. Not yet
 *    confirmed this is the right constant for a non-license workflow —
 *    worth a quick check with your supervisor.
 *  - actionCompile() currently just confirms every division is validated
 *    and logs the action — there's no district-compilation table storing
 *    the actual consolidated report sent to HQ. If DEVELOPMENT_DIVISION needs a single
 *    certified rollup document (not just 11 raw per-division rows), that's
 *    a genuine schema gap to raise, not something this controller solves.
 */
class BscFormsController extends Controller
{
    /**
     * Entry point matching the existing nav link. Thin dispatcher only.
     */
    public function actionIndex()
    {
        if (UserTypeUtil::hasType(Constant::FI)) {
            return $this->redirect(['my-return']);
        }
        if (UserTypeUtil::hasType(Constant::AD)) {
            return $this->redirect(['district-review']);
        }
        throw new ForbiddenHttpException('BSC forms are only available to Fisheries Officers and Divisional ADs.');
    }

    
    // ============================================================
    // FI SIDE — one division, one month, the entry form
    // ============================================================

    /**
     * @param string $form   'bsc1' or 'bsc2'
     * @param string|null $period 'YYYY-MM', defaults to the current month
    */
    public function actionMyReturn($form = 'bsc1', $period = null)
    {
        $this->requireType(Constant::FI);

        $division = $this->getOfficerDivision();

        if (!$division) {
            throw new ForbiddenHttpException(
                'Your account has no division assigned. Contact your Divisional AD.'
            );
        }

        $divisionModel = MDivision::findOne($division);
        $divisionName = $divisionModel ? $divisionModel->name : 'Unknown Division';

        $periodDate = $this->resolvePeriodDate($period);

        $modelClass = $form === 'bsc2'
            ? Bsc2Submission::class
            : Bsc1Submission::class;

        /*
        * Find an existing submission for this division and period.
        */
        $submission = $modelClass::findOne([
            'fi_division_id' => $division,
            'period_date' => $periodDate,
        ]);

        /*
        * For BSC-1, check whether this period has an extension.
        *
        * The extension is stored separately from the submission.
        */
        $extension = null;

        if ($form === 'bsc1') {
            $extension = Bsc1SubmissionExtension::findOne([
                'fi_division_id' => $division,
                'period_date' => $periodDate,
            ]);
        } elseif ($form === 'bsc2') {
            $extension = Bsc2SubmissionExtension::findOne([
                'fi_division_id' => $division,
                'period_date' => $periodDate,
            ]);
        }

        /*
        * Determine whether the selected period is the current month.
        */
        $currentPeriodDate = date('Y-m-01');

        $isCurrentPeriod = $periodDate === $currentPeriodDate;

        /*
        * Determine whether the 48-hour extension is currently active.
        */
        $extensionActive = false;

        if ($extension) {
            $extensionActive =
                strtotime($extension->extension_deadline) >= time();
        }

        /*
        * Determine whether the normal submission deadline has passed.
        *
        * Deadline = 5th of the following month.
        */
        $deadline = strtotime(
            date('Y-m-d 23:59:59', strtotime($periodDate . ' +1 month +4 days'))
        );

        $deadlinePassed = time() > $deadline;

        /*
        * Can the FI currently edit this period?
        *
        * Current month:
        *      Yes, unless workflow has already locked it.
        *
        * Past month before deadline:
        *      Yes.
        *
        * Past month after deadline:
        *      Only if a 48-hour extension is active.
        */
        $periodClosed = false;

        if (!$isCurrentPeriod && $deadlinePassed && !$extensionActive) {
            $periodClosed = true;
        }

        /*
        * If there is no submission yet:
        *
        * - Current period → create a new draft.
        * - Past period still within deadline → create a new draft.
        * - Past period with active extension → create a new draft.
        * - Past period with expired deadline and no active extension
        *   → do NOT create an editable submission.
        */
        if (!$submission) {

            if ($periodClosed) {
                $submission = new $modelClass([
                    'fi_division_id' => $division,
                    'period_date' => $periodDate,
                    'status' => Constant::Pending,
                    'approval_stage' => (string) Constant::FI,
                ]);

                $locked = true;

            } else {

                $submission = new $modelClass([
                    'fi_division_id' => $division,
                    'period_date' => $periodDate,
                    'status' => Constant::Pending,
                    'approval_stage' => (string) Constant::FI,
                ]);

                if ($form === 'bsc1') {
                    $this->carryForwardCommunityProfile(
                        $submission,
                        $division,
                        $periodDate
                    );
                }

                $locked = false;
            }

        } else {

            /*
            * The submission already exists.
            *
            * Workflow lock:
            * once the FI submits it to the AD, the FI cannot edit it.
            *
            * A returned submission has approval_stage = FI,
            * so it becomes editable again.
            */
            $workflowLocked =
                $submission->approval_stage !== ''
                && $submission->approval_stage != Constant::FI;

            $locked = $periodClosed || $workflowLocked;
        }

        /*
        * Save/update only when the period is not locked.
        */
        if (Yii::$app->request->isPost && !$locked) {

            $submission->load(Yii::$app->request->post());

            $this->stampEdit($submission);

            $isNew = $submission->isNewRecord;

            if ($submission->save()) {

                if ($form === 'bsc1') {

                    $this->saveBsc1ChildRecords(
                        $submission,
                        Yii::$app->request->post()
                    );

                } elseif ($form === 'bsc2') {

                    $this->saveBsc2ChildRecords(
                        $submission,
                        Yii::$app->request->post()
                    );
                }

                $this->log(
                    $form,
                    $submission->id,
                    $isNew ? 'create' : 'update'
                );

                if (Yii::$app->request->post('submit-to-ad') !== null) {

                    $this->submitToDivisionalAd(
                        $submission,
                        $form
                    );

                } else {

                    Yii::$app->session->setFlash(
                        'success',
                        'Draft saved.'
                    );
                }

                return $this->redirect([
                    'my-return',
                    'form' => $form,
                    'period' => $period,
                ]);
            }
        }

        $periodOptions = [];

        for ($i = 0; $i < 4; $i++) {
            $date = date('Y-m', strtotime("-{$i} months"));

            $periodOptions[$date] = date(
                'F Y',
                strtotime($date . '-01')
            );
        }

        /*
        * Information sent to the view.
        */
        $viewParams = [
            'form' => $form,
            'submission' => $submission,
            'locked' => $locked,
            'periodDate' => $periodDate,
            'divisionName' => $divisionName,
            'periodOptions' => $periodOptions,

            // New information for the view.
            'expired' => $periodClosed,
            'extension' => $extension,
            'extensionActive' => $extensionActive,
        ];

        if ($form === 'bsc1') {

            $viewParams += $submission->isNewRecord
                ? [
                    'boats' => [],
                    'registrations' => [
                        'first' => [],
                        'renewal' => [],
                        'cancellation' => []
                    ],
                    'licenses' => [],
                    'awareness' => []
                ]
                : $this->loadBsc1ChildRecords($submission);

        } elseif ($form === 'bsc2') {

            $viewParams += $submission->isNewRecord
                ? [
                    'seaworthiness' => [],
                    'boatsInsured' => [],
                    'lagoonActivities' => []
                ]
                : $this->loadBsc2ChildRecords($submission);
        }

        return $this->render(
            'my-return-' . $form,
            $viewParams
        );
    }

    /**
     * Standalone "Submit to Divisional AD" action — kept for any case
     * where a submission is already fully saved and just needs the stage
     * transition (e.g. a future "submit" link that doesn't re-post the
     * whole form). The main FI form now goes through the inline path in
     * actionMyReturn() above instead, to avoid the save-then-submit
     * ordering bug this action alone was vulnerable to.
     */
    public function actionSubmit($form, $id)
    {
        $this->requireType(Constant::FI);
        $submission = $this->findOwnedByFi($form, $id);
        $this->submitToDivisionalAd($submission, $form);
        return $this->redirect(['my-return', 'form' => $form]);
    }

    /**
     * Shared submit-to-AD logic: completeness check, stage transition,
     * attribution, snapshot, save. Returns true on success, false if
     * blocked by the completeness check — a flash message is set either
     * way, so callers don't need to set their own.
     */
    private function submitToDivisionalAd($submission, $form)
    {
        if ($form === 'bsc1') {

            // All Community Profile fields are required before submission
            if (
                $submission->families === null ||
                trim((string)$submission->families) === '' ||
                $submission->active_fishermen === null ||
                trim((string)$submission->active_fishermen) === '' ||
                $submission->population === null ||
                trim((string)$submission->population) === ''
            ) {
                Yii::$app->session->setFlash(
                    'error',
                    'Please fill in all Community Profile fields before submitting.'
                );

                return false;
            }

            // Validate Community Profile values
            if (!$submission->validate([
                'families',
                'active_fishermen',
                'population'
            ])) {
                Yii::$app->session->setFlash(
                    'error',
                    'Families, Active Fishermen and Population must be non-negative whole numbers.'
                );

                return false;
            }
        }

        $submission->approval_stage = (string) Constant::AD;
        $submission->status = Constant::Pending;
        $submission->submitted_by = Yii::$app->user->id;
        $submission->submitted_at = date('Y-m-d H:i:s');

        // Freeze what the FI actually said
        $submission->fi_submitted_snapshot = json_encode($submission->attributes);

        $this->stampEdit($submission);

        if ($submission->save(false)) {
            $this->log($form, $submission->id, 'submit');

            Yii::$app->session->setFlash(
                'success',
                'Submitted to your Divisional AD.'
            );

            return true;
        }

        return false;
    }
    // ============================================================
    // AD SIDE — whole district, review / validate / compile
    // ============================================================

    /**
     * @param string $form 'bsc1' or 'bsc2'
     */
    public function actionDistrictReview($form = 'bsc1', $period = null)
    {
        $this->requireType(Constant::AD);

        $district = $this->getOfficerDistrict();
        if (!$district) {
            throw new ForbiddenHttpException('Your account has no district assigned.');
        }
        $districtModel = MFiDistrict::findOne($district);
        $districtName = $districtModel ? $districtModel->name : '—';

        $periodDate = $this->resolvePeriodDate($period);
        $modelClass = $form === 'bsc2' ? Bsc2Submission::class : Bsc1Submission::class;

        // Enumerate every division in the district — not just ones that
        // already have a submission row — so an AD can see who hasn't
        // started their return at all, not only who has. A division with
        // no row yet is exactly the case this dashboard most needs to
        // surface, not silently omit.
        $divisions = MDivision::find()
            ->where(['district_id' => $district, 'status' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $submissions = $modelClass::find()
            ->where(['fi_division_id' => array_column($divisions, 'id')])
            ->andWhere(['period_date' => $periodDate])
            ->indexBy('fi_division_id')
            ->all();

        $extensions = [];

        if ($form === 'bsc1') {
            $extensionModel = Bsc1SubmissionExtension::class;
        } elseif ($form === 'bsc2') {
            $extensionModel = Bsc2SubmissionExtension::class;
        }

        if (isset($extensionModel)) {
            $extensions = $extensionModel::find()
                ->where([
                    'fi_division_id' => array_column($divisions, 'id'),
                    'period_date' => $periodDate,
                ])
                ->indexBy('fi_division_id')
                ->all();
        }

        return $this->render('district-review-' . $form, [
            'form' => $form,
            'divisions' => $divisions,
            'submissions' => $submissions, // keyed by fi_division_id; a missing key means "not started"
            'periodDate' => $periodDate,
            'district' => $district,
            'districtName' => $districtName,
            'extensions' => $extensions,
        ]);
    }

    public function actionDistrictDetails($form = 'bsc1', $divisionId, $period = null)
    {
        $this->requireType(Constant::DEVELOPMENT_DIVISION);

        $division = MDivision::find()
            ->where([
                'id' => $divisionId,
                'status' => 1,
            ])
            ->one();

        if (!$division) {
            throw new NotFoundHttpException('Division not found.');
        }

        $periodDate = $this->resolvePeriodDate($period);

        $modelClass = $form === 'bsc2'
            ? Bsc2Submission::class
            : Bsc1Submission::class;

        $submission = $modelClass::find()
            ->where([
                'fi_division_id' => $divisionId,
                'period_date' => $periodDate,
            ])
            ->one();

        return $this->render(
            '/bsc-reports/district-details-' . $form,
            [
                'form' => $form,
                'division' => $division,
                'submission' => $submission,
                'periodDate' => $periodDate,
            ]
        );
    }
    
    public function actionDistrictDivisionDetails($form = 'bsc1', $divisionId, $period = null)
    {
        // Only Development Division can access this page.
        $this->requireType(Constant::DEVELOPMENT_DIVISION);

        // Find the selected active division.
        // No district restriction because Development Division
        // handles submissions from all districts.
        $division = MDivision::find()
            ->where([
                'id' => $divisionId,
                'status' => 1,
            ])
            ->one();

        if (!$division) {
            throw new NotFoundHttpException('Division not found.');
        }

        $periodDate = $this->resolvePeriodDate($period);

        $modelClass = $form === 'bsc2'
            ? Bsc2Submission::class
            : Bsc1Submission::class;

        $submission = $modelClass::find()
            ->where([
                'fi_division_id' => $divisionId,
                'period_date' => $periodDate,
            ])
            ->one();

        if (!$submission) {
            throw new NotFoundHttpException(
                'No submission was found for this division and reporting period.'
            );
        }

        return $this->render(
            '/bsc-reports/district-division-details-' . $form,
            [
                'form' => $form,
                'division' => $division,
                'submission' => $submission,
                'periodDate' => $periodDate,
            ]
        );
    }

    public function actionDistrictReviewDetail($form, $id)
    {
        $this->requireType(Constant::AD);

        $submission = $this->findOwnedByAd($form, $id);

        // Once sent to the Development Division, the submission is locked.
        // Allow the page to be viewed, but do not allow any POST changes.
        if (
            Yii::$app->request->isPost &&
            $submission->approval_stage == (string) Constant::DEVELOPMENT_DIVISION
        ) {
            Yii::$app->session->setFlash(
                'error',
                'This return has already been sent to the Development Division and is locked for editing.'
            );

            return $this->redirect([
                'district-review-detail',
                'form' => $form,
                'id' => $id,
            ]);
        }

        if (Yii::$app->request->isPost) {
            $submission->load(Yii::$app->request->post());
            $this->stampEdit($submission);

            if ($submission->save()) {

                if ($form === 'bsc1') {
                    $this->saveBsc1ChildRecords(
                        $submission,
                        Yii::$app->request->post()
                    );
                } elseif ($form === 'bsc2') {
                    $this->saveBsc2ChildRecords(
                        $submission,
                        Yii::$app->request->post()
                    );
                }

                $this->log(
                    $form,
                    $submission->id,
                    'update'
                );

                Yii::$app->session->setFlash(
                    'success',
                    'Changes saved.'
                );

                return $this->redirect([
                    'district-review-detail',
                    'form' => $form,
                    'id' => $id,
                ]);
            }
        }

        $viewParams = [
            'form' => $form,
            'submission' => $submission,
        ];

        if ($form === 'bsc1') {
            $viewParams += $this->loadBsc1ChildRecords($submission);
        } elseif ($form === 'bsc2') {
            $viewParams += $this->loadBsc2ChildRecords($submission);
        }

        return $this->render(
            'district-review-detail-' . $form,
            $viewParams
        );
    }
    /**
     * AD's "Validate division return" button.
     *
     * Two separate checks happen here, worth distinguishing:
     *  - Bsc1Submission::rules() (via save()) checks CORRECTNESS of
     *    whatever values are present — e.g. rejects a negative number.
     *  - The completeness check below catches a different problem:
     *    values that are simply missing (NULL), which rules()' 'integer'
     *    validator does NOT reject by default (skipOnEmpty is true, so a
     *    blank draft field doesn't fail validation — that's needed so an
     *    in-progress draft can still be saved). Community profile is the
     *    one section that must genuinely be filled in before this return
     *    is trustworthy enough to lock into the district total, matching
     *    the same rule the FI's own submit button already enforces.
     */
    public function actionValidate($form, $id)
    {
        $this->requireType(Constant::AD);
        $submission = $this->findOwnedByAd($form, $id);

        if ($submission->approval_stage == (string) Constant::DEVELOPMENT_DIVISION) {
            Yii::$app->session->setFlash(
                'error',
                'This return has already been sent to the Development Division and is locked.'
            );


            return $this->redirect([
                'district-review',
                'form' => $form,
            ]);
        }

        if ($form === 'bsc1' && (
            $submission->families === null ||
            $submission->active_fishermen === null ||
            $submission->population === null
        )) {
            Yii::$app->session->setFlash('error', 'Community profile fields must be filled in before this can be validated.');
            return $this->redirect(['district-review-detail', 'form' => $form, 'id' => $id]);
        }

        $submission->approval_stage = (string) Constant::AD; // ready for HQ compile
        $submission->status = Constant::RequestCompleted;
        $submission->validated_by = Yii::$app->user->id;
        $submission->validated_at = date('Y-m-d H:i:s');    
        $this->stampEdit($submission);

        if ($submission->save(false)) {
            $this->log($form, $submission->id, 'validate');
            Yii::$app->session->setFlash('success', 'Return validated successfully.');
        }
        return $this->redirect(['district-review', 'form' => $form]);
    }

    /**
     * AD's "Reopen for my own edits" — stays entirely on the AD's side.
     * The FI is not notified, and submitted_by/submitted_at stay untouched.
     * Distinct from actionReturn() below, which sends it back to the FI.
     */
    public function actionReopen($form, $id)
    {
        $this->requireType(Constant::AD);
        $submission = $this->findOwnedByAd($form, $id);

        if ($submission->approval_stage == (string) Constant::DEVELOPMENT_DIVISION) {
            Yii::$app->session->setFlash(
                'error',
                'This return has already been sent to the Development Division and is locked.'
            );

            return $this->redirect([
                'district-review',
                'form' => $form,
            ]);
        }

        $submission->approval_stage = (string) Constant::AD;
        $submission->status = Constant::Pending;
        $submission->validated_by = null;
        $submission->validated_at = null;
        $this->stampEdit($submission);

        if ($submission->save(false)) {
            $this->log($form, $submission->id, 'reopen');
            Yii::$app->session->setFlash('success', 'Reopened for edits.');
        }
        return $this->redirect(['district-review-detail', 'form' => $form, 'id' => $id]);
    }

    /**
     * AD's "Return to FI" — the case that motivated was_returned /
     * return_reason: unlike actionReopen(), this unlocks the FI's own
     * form again and requires a reason, so it's never a silent bounce.
     */
    public function actionReturn($form, $id)
    {
        $this->requireType(Constant::AD);
        $submission = $this->findOwnedByAd($form, $id);

        if ($submission->approval_stage == (string) Constant::DEVELOPMENT_DIVISION) {
            Yii::$app->session->setFlash(
                'error',
                'This return has already been sent to the Development Division and is locked.'
            );

            return $this->redirect([
                'district-review',
                'form' => $form,
            ]);
        }

        $reason = Yii::$app->request->post('reason');

        if (empty($reason)) {
            throw new BadRequestHttpException('A reason is required when returning a submission to the FI.');
        }

        $submission->approval_stage = (string) Constant::FI;
        $submission->status = Constant::Pending;
        $submission->was_returned = 1;
        $submission->returned_by = Yii::$app->user->id;
        $submission->returned_at = date('Y-m-d H:i:s');
        $submission->return_reason = $reason;
        $this->stampEdit($submission);

        if ($submission->save(false)) {
            $this->log($form, $submission->id, 'return', null, null, $reason);
            Yii::$app->session->setFlash('success', 'Returned to the Fisheries Officer.');
        }
        return $this->redirect(['district-review', 'form' => $form]);
    }

        /**
     * AD grants a one-time 48-hour extension to an FI division
     * for a missed BSC-1 reporting period.
     */
    public function actionGrantExtension($form, $divisionId, $period)
    {
        $this->requireType(Constant::AD);

        // Make sure the division belongs to this AD's district.
        $district = $this->getOfficerDistrict();

        $division = MDivision::findOne([
            'id' => $divisionId,
            'district_id' => $district,
        ]);

        if (!$division) {
            throw new NotFoundHttpException(
                'Division not found in your district.'
            );
        }

        // Only BSC-1 and BSC-2 support extensions.
        if (!in_array($form, ['bsc1', 'bsc2'], true)) {
            throw new BadRequestHttpException('Invalid BSC form.');
        }

        // Select the correct submission and extension models.
        $extensionClass = $form === 'bsc2'
            ? Bsc2SubmissionExtension::class
            : Bsc1SubmissionExtension::class;

        $submissionClass = $form === 'bsc2'
            ? Bsc2Submission::class
            : Bsc1Submission::class;

        // Resolve YYYY-MM into the first day of that month.
        $periodDate = $this->resolvePeriodDate($period);

        // Check whether an extension already exists.
        $existingExtension = $extensionClass::findOne([
            'fi_division_id' => $divisionId,
            'period_date' => $periodDate,
        ]);

        if ($existingExtension) {
            Yii::$app->session->setFlash(
                'error',
                'An extension has already been granted for this division and period. It can only be granted once.'
            );

            return $this->redirect([
                'district-review',
                'form' => $form,
                'period' => $period,
            ]);
        }

        // Check the normal submission deadline.
        $deadline = date(
            'Y-m-d 23:59:59',
            strtotime($periodDate . ' +1 month +4 days')
        );

        if (date('Y-m-d H:i:s') <= $deadline) {
            Yii::$app->session->setFlash(
                'error',
                'The submission deadline for this period has not passed yet — no extension is needed.'
            );

            return $this->redirect([
                'district-review',
                'form' => $form,
                'period' => $period,
            ]);
        }

        // Check whether the FI has already submitted this period.
        $submission = $submissionClass::findOne([
            'fi_division_id' => $divisionId,
            'period_date' => $periodDate,
        ]);

        if ($submission && $submission->approval_stage != Constant::FI) {
            Yii::$app->session->setFlash(
                'error',
                'This return has already been submitted — no extension is needed.'
            );

            return $this->redirect([
                'district-review',
                'form' => $form,
                'period' => $period,
            ]);
        }

        // Create the extension record.
        $grantedAt = date('Y-m-d H:i:s');

        $extensionDeadline = date(
            'Y-m-d H:i:s',
            strtotime($grantedAt . ' +48 hours')
        );

        $extension = new $extensionClass();

        $extension->fi_division_id = $divisionId;
        $extension->period_date = $periodDate;
        $extension->granted_by = Yii::$app->user->id;
        $extension->granted_at = $grantedAt;
        $extension->extension_deadline = $extensionDeadline;
        $extension->created_at = $grantedAt;

        if (!$extension->save()) {
            Yii::$app->session->setFlash(
                'error',
                'The extension could not be granted.'
            );

            return $this->redirect([
                'district-review',
                'form' => $form,
                'period' => $period,
            ]);
        }

        $expiry = date(
            'j M Y, g:i A',
            strtotime($extensionDeadline)
        );

        Yii::$app->session->setFlash(
            'success',
            "A 48-hour extension has been granted for {$division->name}. "
            . "The Fisheries Officer can submit until {$expiry}."
        );

        return $this->redirect([
            'district-review',
            'form' => $form,
            'period' => $period,
        ]);
    }

    /**
     * AD's "Compile & Send to HQ" — only proceeds once every division in
     * the district is validated for the given period, including divisions
     * that never even started a return (a missing row is NOT the same as
     * "nothing pending" — it's the most important case to catch).
     */
    public function actionCompile($form, $period = null)
    {
        $this->requireType(Constant::AD);

        $district = $this->getOfficerDistrict();
        $periodDate = $this->resolvePeriodDate($period);

        // Work only with the form that the AD is currently sending.
        $modelClass = $form === 'bsc2'
            ? Bsc2Submission::class
            : Bsc1Submission::class;

        /*
        * Get this district's submissions for THIS form and period.
        */
        $submissions = $modelClass::find()
            ->joinWith('division')
            ->where([
                'm_division.district_id' => $district,
                'period_date' => $periodDate,
            ])
            ->all();

        /*
        * Prevent the same form from being sent twice.
        */
        foreach ($submissions as $submission) {
            if ($submission->approval_stage == (string) Constant::DEVELOPMENT_DIVISION) {
                Yii::$app->session->setFlash(
                    'error',
                    strtoupper($form)
                    . ' for this period has already been sent to the Development Division.'
                );

                return $this->redirect([
                    'district-review',
                    'form' => $form,
                    'period' => date('Y-m', strtotime($periodDate)),
                ]);
            }
        }

        /*
        * Count all active divisions in this district.
        */
        $totalDivisions = MDivision::find()
            ->where([
                'district_id' => $district,
                'status' => 1,
            ])
            ->count();

        /*
        * Count only VALIDATED submissions for THIS form.
        */
        $validatedCount = 0;

        foreach ($submissions as $submission) {
            if (
                $submission->validated_by !== null &&
                $submission->validated_at !== null
            ) {
                $validatedCount++;
            }
        }

        /*
        * Do not allow sending until every active division
        * has validated its submission.
        */
        if ($totalDivisions === 0 || $validatedCount !== $totalDivisions) {

            $pending = max(0, $totalDivisions - $validatedCount);

            Yii::$app->session->setFlash(
                'error',
                "$pending division(s) still need validating before this "
                . strtoupper($form)
                . " return can be sent to the Development Division."
            );

            return $this->redirect([
                'district-review',
                'form' => $form,
                'period' => date('Y-m', strtotime($periodDate)),
            ]);
        }

        /*
        * Move ONLY this district's submissions for THIS form
        * from AD to the Development Division.
        */
        foreach ($submissions as $submission) {

            if ($submission->approval_stage == (string) Constant::AD) {
                $submission->approval_stage =
                    (string) Constant::DEVELOPMENT_DIVISION;

                $this->stampEdit($submission);
                $submission->save(false);
            }
        }

        $this->log(
            $form,
            0,
            'send_to_development',
            null,
            null,
            (string) $district
        );

        Yii::$app->session->setFlash(
            'success',
            strtoupper($form)
            . ' district return has been sent to the Development Division.'
        );

        return $this->redirect([
            'district-review',
            'form' => $form,
            'period' => date('Y-m', strtotime($periodDate)),
        ]);
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function requireType($type)
    {
        if (!UserTypeUtil::hasType($type)) {
            throw new ForbiddenHttpException();
        }
    }

    /**
     * Matches the exact pattern used throughout officer-menu.php / main.php —
     * a direct findOne() lookup, not the $user->officerProfile relation
     * accessor, to stay consistent with the rest of the codebase.
     */
    private function getOfficerProfile()
    {
        return ProfileOfficers::findOne(Yii::$app->user->identity->profile_id);
    }

    private function getOfficerDivision()
    {
        $division = Yii::$app->session->get('officer_division');
        if ($division) {
            return $division;
        }
        $profile = $this->getOfficerProfile();
        return $profile ? $profile->division : null;
    }

    private function getOfficerDistrict()
    {
        $district = Yii::$app->session->get('officer_district');
        if ($district) {
            return $district;
        }
        $profile = $this->getOfficerProfile();
        return $profile ? $profile->district : null;
    }

    private function resolvePeriodDate($period)
    {
        if ($period) {
            return date('Y-m-01', strtotime($period . '-01'));
        }
        return date('Y-m-01');
    }

    /**
     * Loads a submission owned by the current FI's own division only —
     * never trusts $id alone. This is the actual security boundary, not
     * anything hidden in the UI.
     */
    private function findOwnedByFi($form, $id)
    {
        $modelClass = $form === 'bsc2' ? Bsc2Submission::class : Bsc1Submission::class;
        $submission = $modelClass::findOne([
            'id' => $id,
            'fi_division_id' => $this->getOfficerDivision(),
        ]);
        if (!$submission) {
            throw new NotFoundHttpException();
        }
        return $submission;
    }

    /**
     * Loads a submission scoped to the current AD's district only.
     */
    private function findOwnedByAd($form, $id)
    {
        $modelClass = $form === 'bsc2' ? Bsc2Submission::class : Bsc1Submission::class;
        $table = $form === 'bsc2' ? 'bsc2_submissions' : 'bsc1_submissions';

        $submission = $modelClass::find()
            ->joinWith('division')
            ->where(["$table.id" => $id])
            ->andWhere(['m_division.district_id' => $this->getOfficerDistrict()])
            ->one();
        if (!$submission) {
            throw new NotFoundHttpException();
        }
        return $submission;
    }

    /**
     * Sets last_edited_by/_at on every save. submitted_by/_at is
     * deliberately never touched here — that stays the FI's immutable
     * original claim, per the accountability design.
     */
    private function stampEdit($submission)
    {
        $submission->last_edited_by = Yii::$app->user->id;
        $submission->last_edited_at = date('Y-m-d H:i:s');
    }

    /**
     * Writes to submission_audit_log, including impersonation tracking
     * per common\components\WebUser::getIsImpersonated()/getMainIdentityId().
     */
    private function log($form, $submissionId, $action, $field = null, $old = null, $new = null)
    {
        /** @var WebUser $webUser */
        $webUser = Yii::$app->user;

        $log = new SubmissionAuditLog([
            'form_type' => $form,
            'submission_id' => $submissionId,
            'action' => $action,
            'field_changed' => $field,
            'old_value' => $old,
            'new_value' => $new,
            'changed_by' => Yii::$app->user->id,
            'acted_as_admin' => $webUser->getIsImpersonated() ? 1 : 0,
            'real_actor' => $webUser->getIsImpersonated() ? $webUser->getMainIdentityId() : null,
        ]);
        $log->save(false);
    }

    /**
     * Community profile carry-forward — mirrors the prototype: pull the
     * most recently validated submission's community fields as a
     * starting point for a fresh draft, for the FI to confirm or edit.
     */
    private function carryForwardCommunityProfile($submission, $divisionId, $periodDate)
    {
        $last = Bsc1Submission::find()
            ->where(['fi_division_id' => $divisionId])
            ->andWhere(['<', 'period_date', $periodDate])
            ->andWhere(['approval_stage' => (string) Constant::DEVELOPMENT_DIVISION]) // last validated/compiled one
            ->orderBy(['period_date' => SORT_DESC])
            ->one();

        if ($last) {
            $submission->families = $last->families;
            $submission->active_fishermen = $last->active_fishermen;
            $submission->population = $last->population;
        }
    }

    /**
     * Fetches BSC-1's child-table rows for an existing submission, shaped
     * exactly how my-return-bsc1.php / district-review-detail-bsc1.php
     * expect them: boats/licenses indexed by craft_type, registrations
     * indexed by [action][craft_type], awareness as a plain list.
     */
    private function loadBsc1ChildRecords($submission)
    {
        $boats = [];
        foreach (Bsc1Boat::find()->where(['submission_id' => $submission->id])->all() as $b) {
            $boats[$b->craft_type] = $b;
        }

        $registrations = ['first' => [], 'renewal' => [], 'cancellation' => []];
        foreach (Bsc1Registration::find()->where(['submission_id' => $submission->id])->all() as $r) {
            $registrations[$r->action][$r->craft_type] = $r;
        }

        $licenses = [];
        foreach (Bsc1LicenseWithCraft::find()->where(['submission_id' => $submission->id])->all() as $l) {
            $licenses[$l->craft_type] = $l;
        }

        $awareness = Bsc1AwarenessProgramme::find()->where(['submission_id' => $submission->id])->all();

        return compact('boats', 'registrations', 'licenses', 'awareness');
    }

    /**
     * Saves the Bsc1Boat / Bsc1Registration / Bsc1LicenseWithCraft /
     * Bsc1AwarenessProgramme arrays the view submits alongside the main
     * submission form. Boats/registrations/licenses are always-present
     * fixed sets, so each is a straightforward upsert. Awareness is a
     * free-form list, so it also needs to detect and delete rows the
     * officer removed in the UI.
     *
     * Every count is clamped to >= 0 with max(0, ...) — this guarantees no
     * negative value can reach the database regardless of what the client
     * sends, independent of whatever model-level rules() end up being
     * (see the rules() additions noted below for a second layer of defense
     * once these models exist, useful mainly if they're ever edited
     * outside this controller, e.g. via an API or a Gii CRUD screen).
     *
     * NOTE: save(false) still skips other model validation for simplicity —
     * worth revisiting once the happy path is confirmed working end to end.
     */
    private function saveBsc1ChildRecords($submission, $post)
    {
        foreach ($post['Bsc1Boat'] ?? [] as $craftType => $count) {
            $boat = Bsc1Boat::findOne(['submission_id' => $submission->id, 'craft_type' => $craftType])
                ?? new Bsc1Boat(['submission_id' => $submission->id, 'craft_type' => $craftType]);
            $boat->boat_count = max(0, (int) $count);
            $boat->save(false);
        }

        foreach ($post['Bsc1Registration'] ?? [] as $action => $crafts) {
            foreach ($crafts as $craftType => $count) {
                $reg = Bsc1Registration::findOne([
                    'submission_id' => $submission->id, 'action' => $action, 'craft_type' => $craftType,
                ]) ?? new Bsc1Registration([
                    'submission_id' => $submission->id, 'action' => $action, 'craft_type' => $craftType,
                ]);
                $reg->reg_count = max(0, (int) $count);
                $reg->save(false);
            }
        }

        foreach ($post['Bsc1LicenseWithCraft'] ?? [] as $craftType => $count) {
            $lic = Bsc1LicenseWithCraft::findOne(['submission_id' => $submission->id, 'craft_type' => $craftType])
                ?? new Bsc1LicenseWithCraft(['submission_id' => $submission->id, 'craft_type' => $craftType]);
            $lic->license_count = max(0, (int) $count);
            $lic->save(false);
        }

        // Awareness: numeric keys = existing rows to update,
        // "new_*" keys = new rows to insert.
        // After saving, every surviving row ID is added to $postedIds.
        // Any existing row not present in the current POST is deleted.

        $postedIds = [];

        foreach ($post['Bsc1AwarenessProgramme'] ?? [] as $key => $row) {

            if (strpos($key, 'new_') === 0) {

                $a = new Bsc1AwarenessProgramme([
                    'submission_id' => $submission->id
                ]);

            } else {

                $a = Bsc1AwarenessProgramme::findOne($key);
            }

            if ($a) {

                $row['participants'] = max(
                    0,
                    (int) ($row['participants'] ?? 0)
                );

                $row['cost'] = max(
                    0,
                    (float) ($row['cost'] ?? 0)
                );

                $a->setAttributes($row);

                if ($a->save(false)) {

                    // Keep this record from being deleted below.
                    $postedIds[] = (int) $a->id;

                }
            }
        }

        Bsc1AwarenessProgramme::deleteAll([
            'and',
            ['submission_id' => $submission->id],
            ['not in', 'id', $postedIds ?: [0]],
        ]);
    }

    /**
     * Fetches BSC-2's child-table rows for an existing submission, shaped
     * exactly how my-return-bsc2.php / district-review-detail-bsc2.php
     * expect them: seaworthiness/boatsInsured indexed by category/
     * craft_type, lagoonActivities as a plain list.
     */
    private function loadBsc2ChildRecords($submission)
    {
        $seaworthiness = [];
        foreach (Bsc2SeaworthinessCertificates::find()->where(['submission_id' => $submission->id])->all() as $s) {
            $seaworthiness[$s->category] = $s;
        }

        $boatsInsured = [];
        foreach (Bsc2BoatInsured::find()->where(['submission_id' => $submission->id])->all() as $b) {
            $boatsInsured[$b->craft_type] = $b;
        }

        $lagoonActivities = Bsc2LagoonActivity::find()->where(['submission_id' => $submission->id])->all();

        return compact('seaworthiness', 'boatsInsured', 'lagoonActivities');
    }

    /**
     * Saves the Bsc2SeaworthinessCert / Bsc2BoatInsured / Bsc2LagoonActivity
     * arrays the view submits. Seaworthiness/boats-insured are fixed sets
     * (upsert only); lagoon activities are a free-form list, same
     * new_*-key / delete-missing pattern as saveBsc1ChildRecords()'s
     * awareness-programme handling — see that method for the full
     * reasoning. Counts are clamped to >= 0, same as BSC-1.
     */
    private function saveBsc2ChildRecords($submission, $post)
    {
        foreach ($post['Bsc2SeaworthinessCertificates'] ?? [] as $category => $count) {
            $sw = Bsc2SeaworthinessCertificates::findOne(['submission_id' => $submission->id, 'category' => $category])
                ?? new Bsc2SeaworthinessCertificates(['submission_id' => $submission->id, 'category' => $category]);
            $sw->cert_count = max(0, (int) $count);
            $sw->save(false);
        }

        foreach ($post['Bsc2BoatInsured'] ?? [] as $craftType => $count) {
            $b = Bsc2BoatInsured::findOne(['submission_id' => $submission->id, 'craft_type' => $craftType])
                ?? new Bsc2BoatInsured(['submission_id' => $submission->id, 'craft_type' => $craftType]);
            $b->insured_count = max(0, (int) $count);
            $b->save(false);
        }

        $postedIds = [];

        foreach ($post['Bsc2LagoonActivity'] ?? [] as $key => $row) {

            if (strpos($key, 'new_') === 0) {

                $l = new Bsc2LagoonActivity([
                    'submission_id' => $submission->id
                ]);

            } else {

                $l = Bsc2LagoonActivity::findOne($key);
            }

            if ($l) {

                $l->setAttributes($row);

                if (!$l->save(false)) {

                    Yii::error([
                        'save_failed' => true,
                        'errors' => $l->errors,
                        'attributes' => $l->attributes,
                    ], 'BSC2_LAGOON_DEBUG');

                } else {

                    $postedIds[] = (int) $l->id;

                    Yii::error([
                        'save_success' => true,
                        'id' => $l->id,
                        'attributes' => $l->attributes,
                    ], 'BSC2_LAGOON_DEBUG');
                }
            }
        }

        Bsc2LagoonActivity::deleteAll([
            'and',
            ['submission_id' => $submission->id],
            ['not in', 'id', $postedIds ?: [0]],
        ]);
    }
}
