<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "district_gear_types".
 *
 * @property int $id
 * @property string $name
 * @property int $gear_type
 * @property int $sub_gear
 * @property string $extra
 * @property string $fishing_time_periods
 * @property string $fishing_time_durations
 * @property string $fish_species
 * @property int $status
 * @property int $division
 *
 * @property MMainGearTypes $gearType
 * @property HighseasLicense[] $highseasLicenses
 * @property MGearTypes $subGear
 */
class DistrictGearTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'district_gear_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'gear_type', 'sub_gear', 'extra', 'fishing_time_periods', 'fishing_time_durations', 'fish_species', 'division'], 'required'],
            [['gear_type', 'sub_gear', 'status', 'division'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['extra'], 'string', 'max' => 500],
            [['gear_type'], 'exist', 'skipOnError' => true, 'targetClass' => MMainGearTypes::class, 'targetAttribute' => ['gear_type' => 'id']],
            [['sub_gear'], 'exist', 'skipOnError' => true, 'targetClass' => MGearTypes::class, 'targetAttribute' => ['sub_gear' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'gear_type' => Yii::t('app', 'Gear Type'),
            'sub_gear' => Yii::t('app', 'Sub Gear'),
            'extra' => Yii::t('app', 'Extra'),
            'fishing_time_periods' => Yii::t('app', 'Fishing Time Periods'),
            'fishing_time_durations' => Yii::t('app', 'Fishing Time Durations'),
            'fish_species' => Yii::t('app', 'Fish Species'),
            'status' => Yii::t('app', 'Status'),
            'division' => Yii::t('app', 'Division'),
        ];
    }

    /**
     * Gets query for [[GearType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGearType()
    {
        return $this->hasOne(MMainGearTypes::class, ['id' => 'gear_type']);
    }

    /**
     * Gets query for [[HighseasLicenses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHighseasLicenses()
    {
        return $this->hasMany(HighseasLicense::class, ['fishing_gear_type' => 'id']);
    }

    /**
     * Gets query for [[SubGear]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubGear()
    {
        return $this->hasOne(MGearTypes::class, ['id' => 'sub_gear']);
    }
}
