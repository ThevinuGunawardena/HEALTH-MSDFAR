<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "skipper_renew".
 *
 * @property int $id
 * @property string|null $skipper_uid
 * @property int $fisherman_id
 * @property int|null $highest_education_qualification Highest Education Qualification
 * @property string|null $other_qualifications
 * @property int|null $fisheries_district
 * @property int|null $fisheries_division
 * @property int|null $status
 * @property string|null $approval_stage
 * @property string|null $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 * @property int $printed
 */
class SkipperRenew extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'skipper_renew';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fisherman_id'], 'required'],
            [['fisherman_id', 'highest_education_qualification', 'fisheries_district', 'fisheries_division', 'status', 'printed', "renew"], 'integer'],
            [['created', 'approved_time', 'expire_date'], 'safe'],
            [['skipper_uid'], 'string', 'max' => 100],
            [['other_qualifications'], 'string', 'max' => 500],
            [['approval_stage'], 'string', 'max' => 11],
            [['fisherman_id'], 'unique'],
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
