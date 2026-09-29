<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_gear_types".
 *
 * @property int $id
 * @property int $main_gear
 * @property string $code
 * @property string $description
 * @property int|null $sub_group
 *
 * @property DistrictGearTypes[] $districtGearTypes
 * @property MMainGearTypes $mainGear
 * @property NationalLicense[] $nationalLicenses
 * @property ScientificFleetData[] $scientificFleetDatas
 * @property ScientificSamplingGearData[] $scientificSamplingGearDatas
 * @property ScientificSamplingLengthDetails[] $scientificSamplingLengthDetails
 * @property MGearTypeExtraData $subGroup
 */
class MGearTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_gear_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['main_gear', 'code', 'description'], 'required'],
            [['main_gear', 'sub_group'], 'integer'],
            [['code'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 200],
            [['main_gear'], 'exist', 'skipOnError' => true, 'targetClass' => MMainGearTypes::class, 'targetAttribute' => ['main_gear' => 'id']],
            [['sub_group'], 'exist', 'skipOnError' => true, 'targetClass' => MGearTypeExtraData::class, 'targetAttribute' => ['sub_group' => 'group_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'main_gear' => Yii::t('app', 'Main Gear'),
            'code' => Yii::t('app', 'Code'),
            'description' => Yii::t('app', 'Description'),
            'sub_group' => Yii::t('app', 'Sub Group'),
        ];
    }

    /**
     * Gets query for [[DistrictGearTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistrictGearTypes()
    {
        return $this->hasMany(DistrictGearTypes::class, ['sub_gear' => 'id']);
    }

    /**
     * Gets query for [[MainGear]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMainGear()
    {
        return $this->hasOne(MMainGearTypes::class, ['id' => 'main_gear']);
    }

    /**
     * Gets query for [[NationalLicenses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNationalLicenses()
    {
        return $this->hasMany(NationalLicense::class, ['main_gear_type' => 'id']);
    }

    /**
     * Gets query for [[ScientificFleetDatas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScientificFleetDatas()
    {
        return $this->hasMany(ScientificFleetData::class, ['gear_type' => 'id']);
    }

    /**
     * Gets query for [[ScientificSamplingGearDatas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScientificSamplingGearDatas()
    {
        return $this->hasMany(ScientificSamplingGearData::class, ['gear' => 'id']);
    }

    /**
     * Gets query for [[ScientificSamplingLengthDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScientificSamplingLengthDetails()
    {
        return $this->hasMany(ScientificSamplingLengthDetails::class, ['gear' => 'id']);
    }

    /**
     * Gets query for [[SubGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubGroup()
    {
        return $this->hasOne(MGearTypeExtraData::class, ['group_id' => 'sub_group']);
    }
}
