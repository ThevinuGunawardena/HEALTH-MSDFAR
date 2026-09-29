<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_main_gear_types".
 *
 * @property int $id
 * @property string $description
 * @property int $status
 *
 * @property DistrictGearTypes[] $districtGearTypes
 * @property MGearTypes[] $mGearTypes
 */
class MMainGearTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_main_gear_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'required'],
            [['status'], 'integer'],
            [['description'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[DistrictGearTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDistrictGearTypes()
    {
        return $this->hasMany(DistrictGearTypes::class, ['gear_type' => 'id']);
    }

    /**
     * Gets query for [[MGearTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMGearTypes()
    {
        return $this->hasMany(MGearTypes::class, ['main_gear' => 'id']);
    }
}
