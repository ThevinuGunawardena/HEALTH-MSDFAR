<?php

namespace backend\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "kpi".
 *
 * @property int $KPIId
 * @property string $title
 * @property string $indicator
 * @property string $unit
 * @property float $target
 * @property string $rational
 * @property string $createdDate
 * @property string|null $assignedDate
 * @property string $targetType
 * @property string|null $targetDate
 * @property int $divisionId
 * @property string $progress
 * @property string $status
 * @property int $supervisor
 * @property int $responsibility
 */
class Kpi extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kpi';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'createdDate',
                ],
                'value' => function () {
                    return date('Y-m-d');
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'indicator', 'unit', 'target', 'rational', 'targetType', 'divisionId', 'supervisor'], 'required'],
            [['target'], 'number'],
            [['createdDate', 'assignedDate', 'targetDate'], 'safe'],
            [['divisionId', 'supervisor', 'responsibility'], 'integer'],
            [['title', 'indicator', 'unit', 'progress'], 'string', 'max' => 100],
            [['targetType', 'status'], 'string', 'max' => 25],
            [['rational'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'KPIId' => Yii::t('app', 'KPI ID'),
            'title' => Yii::t('app', 'Title'),
            'indicator' => Yii::t('app', 'Indicator'),
            'unit' => Yii::t('app', 'Unit'),
            'target' => Yii::t('app', 'Target'),
            'rational' => Yii::t('app', 'Rational / Justification'),
            'createdDate' => Yii::t('app', 'Created Date'),
            'assignedDate' => Yii::t('app', 'Assigned Date'),
            'targetType' => Yii::t('app', 'Target Type'),
            'targetDate' => Yii::t('app', 'Target Date'),
            'divisionId' => Yii::t('app', 'Division ID'),
            'progress' => Yii::t('app', 'Progress'),
            'status' => Yii::t('app', 'Status'),
            'supervisor' => Yii::t('app', 'Supervisor'),
            'responsibility' => Yii::t('app', 'Responsibility'),
        ];
    }

    /**
     * Gets query for the User model representing the Supervisor.
     * @return \yii\db\ActiveQuery
     */
    public function getSupervisorUser()
    {
        // Maps the 'supervisor' column in your KPI table to the primary key 'id' of the user table
        return $this->hasOne(\common\models\User::class, ['id' => 'supervisor']);
    }

    /**
     * Gets query for the User model representing the Responsibility.
     * @return \yii\db\ActiveQuery
     */
    public function getResponsibilityUser()
    {
        // Maps the 'responsibility' column in your KPI table to the primary key 'id' of the user table
        return $this->hasOne(\common\models\User::class, ['id' => 'responsibility']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Automatically inject current date/time when creating a new record
            if ($this->isNewRecord) {
                $this->createdDate = date('Y-m-d H:i:s');
            }

            // Logic: Set the assigned date stamp only if a valid responsibility is actively declared
            if (!empty($this->responsibility) && $this->responsibility !== 'pending') {
                $this->assignedDate = date('Y-m-d H:i:s');
            } else {
                $this->assignedDate = null; // Ensure fallback clean state
            }

            return true;
        }
        return false;
    }

    /**
     * Gets query for [[Division]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDivision()
    {
        // Maps kpi.divisionId to kpi_divisions.divisionId
        return $this->hasOne(KpiDivisions::class, ['divisionId' => 'divisionId']);
    }
}