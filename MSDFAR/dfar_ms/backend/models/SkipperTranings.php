<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "skipper_tranings".
 *
 * @property int $id
 * @property int $skipper_id
 * @property int $institute
 * @property int $program_name
 * @property string $training_period
 * @property string $date_certified
 *
 * @property MTraningInstitutes $institute0
 * @property MTraningInstitutePrograms $programName
 */
class SkipperTranings extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'skipper_tranings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['skipper_id', 'institute', 'program_name', 'training_period', 'date_certified'], 'required'],
            [['skipper_id', 'institute', 'program_name'], 'integer'],
            [['date_certified'], 'safe'],
            [['training_period'], 'string', 'max' => 200],
            [['institute'], 'exist', 'skipOnError' => true, 'targetClass' => MTraningInstitutes::class, 'targetAttribute' => ['institute' => 'id']],
            [['program_name'], 'exist', 'skipOnError' => true, 'targetClass' => MTraningInstitutePrograms::class, 'targetAttribute' => ['program_name' => 'id']],
            [
                'training_period', 'match',
                'pattern' => '/^\d+\s(year|years|month|months|day|days)$/',
                'message' => 'Invalid value. Valid Entries examples: "6 months," "1 year," "45 days.".'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'skipper_id' => Yii::t('app', 'Skipper ID'),
            'institute' => Yii::t('app', 'Institute'),
            'program_name' => Yii::t('app', 'Program Name'),
            'training_period' => Yii::t('app', 'Training Period'),
            'date_certified' => Yii::t('app', 'Date Certified'),
        ];
    }

    /**
     * Gets query for [[Institute0]].
     *
     * @return ActiveQuery
     */
    public function getInstitute0()
    {
        return $this->hasOne(MTraningInstitutes::class, ['id' => 'institute']);
    }

    /**
     * Gets query for [[ProgramName]].
     *
     * @return ActiveQuery
     */
    public function getProgramName()
    {
        return $this->hasOne(MTraningInstitutePrograms::class, ['id' => 'program_name']);
    }
}
