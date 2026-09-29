<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use backend\config\Constant;
use backend\models\Bsc2SubmissionExtension;

/**
 * This is the model class for table "bsc2_submissions".
 *
 * One row per F.I. division per month. Holds the BSC-2 workflow status
 * and every single-value field (licenses, production, raids/court cases,
 * log sheets, welfare enrolment, bycatch recording). Repeating breakdowns
 * (sea-worthiness certs, boats insured, lagoon activities) live in their
 * own child tables — see Bsc2SeaworthinessCert, Bsc2BoatInsured,
 * Bsc2LagoonActivity.
 *
 * Revised against an updated paper form (see m260830_100007's docblock):
 * no_of_raids/no_of_court_cases replace the original three legal-action
 * fields, child_savings_enrolled is removed, and two bycatch-recording
 * fields were added.
 *
 * @property int $id
 * @property int $fi_division_id
 * @property string $period_date
 * @property int $status
 * @property string $approval_stage
 * @property int $was_returned
 * @property int|null $returned_by
 * @property string|null $returned_at
 * @property string|null $return_reason
 * @property int $beach_seine_licenses
 * @property string $production_lagoon
 * @property string $production_coastal
 * @property string $production_offshore
 * @property int $no_of_raids
 * @property int $no_of_court_cases
 * @property int $log_sheets_collected
 * @property int $departures
 * @property int $fishermen_registered
 * @property int $id_cards_issued
 * @property int $awareness_programmes
 * @property int $insurance_enrolled
 * @property int $pension_enrolled
 * @property int $recorded_marine_mammal_deaths
 * @property int $recorded_turtle_deaths
 * @property int|null $submitted_by
 * @property string|null $submitted_at
 * @property int|null $validated_by
 * @property string|null $validated_at
 * @property int|null $last_edited_by
 * @property string|null $last_edited_at
 * @property string|null $fi_submitted_snapshot
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property MDivision $division
 * @property Bsc2SeaworthinessCertificates[] $seaworthinessCerts
 * @property Bsc2BoatInsured[] $boatsInsured
 * @property Bsc2LagoonActivity[] $lagoonActivities
 */
class Bsc2Submission extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsc2_submissions';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fi_division_id', 'period_date'], 'required'],
            [['fi_division_id'], 'integer'],
            [['period_date'], 'date', 'format' => 'php:Y-m-d'],

            [['status'], 'integer'],
            [['status'], 'default', 'value' => Constant::Pending],

            [['approval_stage'], 'string', 'max' => 100],
            [['approval_stage'], 'default', 'value' => ''],
            [['approval_stage'], 'in', 'range' => [
                '', (string) Constant::FI, (string) Constant::AD, (string) Constant::DG,
            ]],

            [['was_returned'], 'boolean'],
            [['was_returned'], 'default', 'value' => 0],
            [['return_reason'], 'string', 'max' => 255],

            [[
                'beach_seine_licenses',
                'no_of_raids', 'no_of_court_cases',
                'log_sheets_collected', 'departures',
                'fishermen_registered', 'id_cards_issued', 'awareness_programmes',
                'insurance_enrolled', 'pension_enrolled',
                'recorded_marine_mammal_deaths', 'recorded_turtle_deaths',
            ], 'integer', 'min' => 0],
            [[
                'beach_seine_licenses',
                'no_of_raids', 'no_of_court_cases',
                'log_sheets_collected', 'departures',
                'fishermen_registered', 'id_cards_issued', 'awareness_programmes',
                'insurance_enrolled', 'pension_enrolled',
                'recorded_marine_mammal_deaths', 'recorded_turtle_deaths',
            ], 'default', 'value' => 0],

            [['production_lagoon', 'production_coastal', 'production_offshore'], 'number', 'min' => 0],
            [['production_lagoon', 'production_coastal', 'production_offshore'], 'default', 'value' => 0],

            [['returned_by', 'submitted_by', 'validated_by', 'last_edited_by'], 'integer'],
            [['returned_at', 'submitted_at', 'validated_at', 'last_edited_at'], 'safe'],
            [['fi_submitted_snapshot'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fi_division_id' => Yii::t('app', 'F.I. Division'),
            'period_date' => Yii::t('app', 'Reporting Period'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'beach_seine_licenses' => Yii::t('app', 'Beach Seine Licenses Issued (NET)'),
            'production_lagoon' => Yii::t('app', 'Lagoon & Brackish Water Production (Mt)'),
            'production_coastal' => Yii::t('app', 'Coastal Production (Mt)'),
            'production_offshore' => Yii::t('app', 'Offshore Production (Mt)'),
            'no_of_raids' => Yii::t('app', 'No. of Raids'),
            'no_of_court_cases' => Yii::t('app', 'No. of Court Cases'),
            'log_sheets_collected' => Yii::t('app', 'Log Sheets Collected'),
            'departures' => Yii::t('app', 'No. of Departures'),
            'fishermen_registered' => Yii::t('app', 'Fisherman Registrations'),
            'id_cards_issued' => Yii::t('app', 'I.D. Cards Issued'),
            'awareness_programmes' => Yii::t('app', 'Awareness Programmes'),
            'insurance_enrolled' => Yii::t('app', 'Fisherman Insurance Enrolled'),
            'pension_enrolled' => Yii::t('app', 'No. of Fishermen Enrolled (Pension)'),
            'recorded_marine_mammal_deaths' => Yii::t('app', 'Recorded Marine Mammal Deaths'),
            'recorded_turtle_deaths' => Yii::t('app', 'Recorded Turtle Deaths'),
            'submitted_by' => Yii::t('app', 'Submitted By'),
            'submitted_at' => Yii::t('app', 'Submitted At'),
            'validated_by' => Yii::t('app', 'Validated By'),
            'validated_at' => Yii::t('app', 'Validated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        return $this->hasOne(MDivision::class, ['id' => 'fi_division_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeaworthinessCerts()
    {
        return $this->hasMany(Bsc2SeaworthinessCertificates::class, ['submission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoatsInsured()
    {
        return $this->hasMany(Bsc2BoatInsured::class, ['submission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLagoonActivities()
    {
        return $this->hasMany(Bsc2LagoonActivity::class, ['submission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubmittedByUser()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'submitted_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastEditedByUser()
    {
        return $this->hasOne(\common\models\User::class, ['id' => 'last_edited_by']);
    }

    /**
     * @return float total of the three production fields — always
     * computed, never stored, matching the design decision that the
     * original spreadsheet's manually-entered totals didn't reliably
     * match their own components.
     */
    public function getTotalProduction()
    {
        return (float) $this->production_lagoon + (float) $this->production_coastal + (float) $this->production_offshore;
    }

    /**
     * Same accountability check as Bsc1Submission — see that class for
     * the full explanation.
     * @return bool
     */
    public function wasEditedAfterSubmission()
    {
        if (empty($this->fi_submitted_snapshot) || empty($this->last_edited_by)) {
            return false;
        }
        return $this->last_edited_by != $this->submitted_by;
    }

    /**
     * See Bsc1Submission for the full explanation of these four methods —
     * identical logic, duplicated here since the two models don't share
     * a base class.
     */
    public function getDeadlineDate()
    {
        return date('Y-m-d', strtotime($this->period_date . ' +1 month +4 days'));
    }
 
    public function isPastDeadline()
    {
        return date('Y-m-d') > $this->getDeadlineDate();
    }

    public function getExtension()
    {
        return $this->hasOne(
            Bsc2SubmissionExtension::class,
            [
                'fi_division_id' => 'fi_division_id',
                'period_date' => 'period_date',
            ]
        );
    }

}