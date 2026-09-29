<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scientific_fleet_data".
 *
 * @property int $id
 * @property int $boat_type
 * @property int|null $sub_category
 * @property int $gear_type
 * @property int $no_of_boats
 * @property int $status
 * @property int $scientific_id
 *
 * @property MBoatTypes $boatType
 * @property MGearTypes $gearType
 * @property MBoatCategory $subCategory
 */
class ScientificFleetData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_fleet_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_type', 'gear_type', 'no_of_boats', 'scientific_id'], 'required'],
            [['boat_type', 'sub_category', 'gear_type', 'no_of_boats', 'status', 'scientific_id'], 'integer'],
            [['boat_type'], 'exist', 'skipOnError' => true, 'targetClass' => MBoatTypes::class, 'targetAttribute' => ['boat_type' => 'id']],
            [['gear_type'], 'exist', 'skipOnError' => true, 'targetClass' => MGearTypes::class, 'targetAttribute' => ['gear_type' => 'id']],
            [['sub_category'], 'exist', 'skipOnError' => true, 'targetClass' => MBoatCategory::class, 'targetAttribute' => ['sub_category' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'boat_type' => Yii::t('app', 'Boat Type'),
            'sub_category' => Yii::t('app', 'Sub Category'),
            'gear_type' => Yii::t('app', 'Gear Type'),
            'no_of_boats' => Yii::t('app', 'No Of Boats'),
            'status' => Yii::t('app', 'Status'),
            'scientific_id' => Yii::t('app', 'Scientific ID'),
        ];
    }

    /**
     * Gets query for [[BoatType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoatType()
    {
        return $this->hasOne(MBoatTypes::class, ['id' => 'boat_type']);
    }

    /**
     * Gets query for [[GearType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGearType()
    {
        return $this->hasOne(MGearTypes::class, ['id' => 'gear_type']);
    }

    /**
     * Gets query for [[SubCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubCategory()
    {
        return $this->hasOne(MBoatCategory::class, ['id' => 'sub_category']);
    }
}
