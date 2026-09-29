<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use backend\config\Constant;
use backend\models\Bsc1SubmissionExtension;

/**
 * This is the model class for table "bsc1_submissions".
 *
 * One row per F.I. division per month. Holds the BSC-1 workflow status
 * and every single-value field (community profile, migration, incidents,
 * licenses without craft). Repeating breakdowns (boats, registrations,
 * licenses with craft, awareness programmes) live in their own child
 * tables — see Bsc1Boat, Bsc1Registration, Bsc1LicenseWithCraft,
 * Bsc1AwarenessProgramme.
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
 * @property int|null $families
 * @property int|null $active_fishermen
 * @property int|null $population
 * @property int $migrated_imul
 * @property int $migrated_iday
 * @property int $migrated_ofrp
 * @property int $incident_partial_loss
 * @property int $incident_total_loss
 * @property int $incident_natural_deaths
 * @property int $incident_missing
 * @property int $licenses_without_craft
 * @property int $beach_seines
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
 * @property Bsc1Boat[] $boats
 * @property Bsc1Registration[] $registrations
 * @property Bsc1LicenseWithCraft[] $licensesWithCraft
 * @property Bsc1AwarenessProgramme[] $awarenessProgrammes
 */
class Bsc1Submission extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsc1_submissions';
    }

    /**
     * {@inheritdoc}
     *
     * created_at / updated_at are TIMESTAMP columns, not plain integers —
     * an Expression('NOW()') is used rather than TimestampBehavior's
     * default unix-timestamp value, which would fail to insert cleanly
     * into a TIMESTAMP column.
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
            // Only ever expected to hold "", or a role constant as a string
            // (Constant::FI / Constant::AD / Constant::DG), confirmed from
            // boat_number_cancel_requests / boat_number_transfer_request.
            [['approval_stage'], 'in', 'range' => [
                '', 
                (string) Constant::FI, 
                (string) Constant::AD,  
                (string) Constant::DEVELOPMENT_DIVISION,
            ]],

            [['was_returned'], 'boolean'],
            [['was_returned'], 'default', 'value' => 0],
            [['return_reason'], 'string', 'max' => 255],

            // Every numeric monthly-activity field must be >= 0. skipOnEmpty
            // stays at its default (true), so a blank community-profile
            // field during a draft doesn't fail validation — only an
            // actual negative or non-numeric value does.
            [[
                'families', 'active_fishermen', 'population',
                'migrated_imul', 'migrated_iday', 'migrated_ofrp',
                'incident_partial_loss', 'incident_total_loss',
                'incident_natural_deaths', 'incident_missing',
                'licenses_without_craft', 'beach_seines'
            ], 'integer', 'min' => 0],

            [[
                'migrated_imul', 'migrated_iday', 'migrated_ofrp',
                'incident_partial_loss', 'incident_total_loss',
                'incident_natural_deaths', 'incident_missing',
                'licenses_without_craft',  'beach_seines'
            ], 'default', 'value' => 0],

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
            'was_returned' => Yii::t('app', 'Was Returned'),
            'returned_by' => Yii::t('app', 'Returned By'),
            'returned_at' => Yii::t('app', 'Returned At'),
            'return_reason' => Yii::t('app', 'Return Reason'),
            'families' => Yii::t('app', 'No. of Fisher Families'),
            'active_fishermen' => Yii::t('app', 'No. of Active Fishermen'),
            'population' => Yii::t('app', 'Fisheries Sector Population'),
            'migrated_imul' => Yii::t('app', 'Migrated Boats — IMUL'),
            'migrated_iday' => Yii::t('app', 'Migrated Boats — IDAY'),
            'migrated_ofrp' => Yii::t('app', 'Migrated Boats — OFRP'),
            'incident_partial_loss' => Yii::t('app', 'Partial Loss of Boats'),
            'incident_total_loss' => Yii::t('app', 'Total Loss of Boats'),
            'incident_natural_deaths' => Yii::t('app', 'Natural Deaths of Fishermen'),
            'incident_missing' => Yii::t('app', 'Missing Fishermen at Sea'),
            'licenses_without_craft' => Yii::t('app', 'Licenses Without Craft'),
            'beach_seines' => Yii::t('app', 'Beach Seines'),
            'submitted_by' => Yii::t('app', 'Submitted By'),
            'submitted_at' => Yii::t('app', 'Submitted At'),
            'validated_by' => Yii::t('app', 'Validated By'),
            'validated_at' => Yii::t('app', 'Validated At'),
            'last_edited_by' => Yii::t('app', 'Last Edited By'),
            'last_edited_at' => Yii::t('app', 'Last Edited At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Division]].
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        return $this->hasOne(MDivision::class, ['id' => 'fi_division_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoats()
    {
        return $this->hasMany(Bsc1Boat::class, ['submission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistrations()
    {
        return $this->hasMany(Bsc1Registration::class, ['submission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLicensesWithCraft()
    {
        return $this->hasMany(Bsc1LicenseWithCraft::class, ['submission_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAwarenessProgrammes()
    {
        return $this->hasMany(Bsc1AwarenessProgramme::class, ['submission_id' => 'id']);
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
     * True if the row currently in the database differs from what the FI
     * actually submitted — i.e. an AD has edited it since. Compares
     * against fi_submitted_snapshot, the frozen copy captured at submit
     * time. Used to drive the attribution banner on the AD review screen
     * ("Originally submitted by X on Y — last edited by Z on W").
     *
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
     * Returns the 48-hour extension for this reporting period, if one exists.
     */
    public function getExtension()
    {
        return $this->hasOne(
            Bsc1SubmissionExtension::class,
            [
                'fi_division_id' => 'fi_division_id',
                'period_date' => 'period_date',
            ]
        );
    }

}