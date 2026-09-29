<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "skipper".
 *
 * @property int $id
 * @property string|null $skipper_uid
 * @property int $fisherman_id
 * @property int $highest_education_qualification Highest Education Qualification
 * @property string $other_qualifications
 * @property int $fisheries_district
 * @property int $fisheries_division
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 *
 * @property MFiDistrict $fisheriesDistrict
 * @property MDivision $fisheriesDivision
 * @property ProfileFisherman $fisherman
 * @property MEducatinalQualification $highestEducationQualification
 * @property HighseasLicense[] $highseasLicenses
 */
class Skipper extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'skipper';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fisherman_id', 'highest_education_qualification', 'fisheries_district', 'fisheries_division', 'approval_stage'], 'required'],
            [['fisherman_id', 'highest_education_qualification', 'fisheries_district', 'fisheries_division',
                'status', "renew", "renew_id"], 'integer'],
            [['created', 'approved_time', 'expire_date'], 'safe'],
            [['skipper_uid'], 'string', 'max' => 100],
            [['other_qualifications'], 'string', 'max' => 500],
            [['approval_stage'], 'string', 'max' => 11],
            [['fisherman_id'], 'unique'],
            [['highest_education_qualification'], 'exist', 'skipOnError' => true, 'targetClass' => MEducatinalQualification::class, 'targetAttribute' => ['highest_education_qualification' => 'id']],
            [['fisherman_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProfileFisherman::class, 'targetAttribute' => ['fisherman_id' => 'id']],
            [['fisheries_division'], 'exist', 'skipOnError' => true, 'targetClass' => MDivision::class, 'targetAttribute' => ['fisheries_division' => 'id']],
            [['fisheries_district'], 'exist', 'skipOnError' => true, 'targetClass' => MFiDistrict::class, 'targetAttribute' => ['fisheries_district' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'skipper_uid' => Yii::t('app', 'Skipper Uid'),
            'fisherman_id' => Yii::t('app', 'Fisherman ID'),
            'highest_education_qualification' => Yii::t('app', 'Highest Education Qualification'),
            'other_qualifications' => Yii::t('app', 'Other Qualifications'),
            'fisheries_district' => Yii::t('app', 'Fisheries District'),
            'fisheries_division' => Yii::t('app', 'Fisheries Division'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }

    /**
     * Gets query for [[FisheriesDistrict]].
     *
     * @return ActiveQuery
     */
    public function getFisheriesDistrict()
    {
        return $this->hasOne(MFiDistrict::class, ['id' => 'fisheries_district']);
    }

    /**
     * Gets query for [[FisheriesDivision]].
     *
     * @return ActiveQuery
     */
    public function getFisheriesDivision()
    {
        return $this->hasOne(MDivision::class, ['id' => 'fisheries_division']);
    }

    /**
     * Gets query for [[Fisherman]].
     *
     * @return ActiveQuery
     */
    public function getFisherman()
    {
        return $this->hasOne(ProfileFisherman::class, ['id' => 'fisherman_id']);
    }

    /**
     * Gets query for [[HighestEducationQualification]].
     *
     * @return ActiveQuery
     */
    public function getHighestEducationQualification()
    {
        return $this->hasOne(MEducatinalQualification::class, ['id' => 'highest_education_qualification']);
    }

    /**
     * Gets query for [[HighseasLicenses]].
     *
     * @return ActiveQuery
     */
    public function getHighseasLicenses()
    {
        return $this->hasMany(HighseasLicense::class, ['skipper_id' => 'id']);
    }
}
