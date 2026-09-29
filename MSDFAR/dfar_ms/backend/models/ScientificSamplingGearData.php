<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scientific_sampling_gear_data".
 *
 * @property int $id
 * @property int $boat_data_id
 * @property int $gear
 * @property int $target_species
 * @property int $operations_per_trip
 * @property float $fishing_time_days
 * @property float $fishing_time_hours
 * @property float $fishing_depth
 * @property int $g_code
 * @property string|null $extra
 * @property string $type
 * @property int $status
 *
 * @property ScientificSamplingBoatGearData $boatData
 * @property MGearTypes $gear0
 * @property MFishTypes $targetSpecies
 */
class ScientificSamplingGearData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_sampling_gear_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_data_id', 'gear', 'target_species', 'operations_per_trip', 'fishing_time_days', 'fishing_time_hours', 'fishing_depth', 'g_code', 'type', 'status'], 'required'],
            [['boat_data_id', 'gear', 'target_species', 'operations_per_trip', 'g_code', 'status'], 'integer'],
            [['fishing_time_days', 'fishing_time_hours', 'fishing_depth'], 'number'],
            [['extra'], 'string', 'max' => 500],
            [['type'], 'string', 'max' => 100],
            [['boat_data_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScientificSamplingBoatGearData::class, 'targetAttribute' => ['boat_data_id' => 'id']],
            [['gear'], 'exist', 'skipOnError' => true, 'targetClass' => MGearTypes::class, 'targetAttribute' => ['gear' => 'id']],
            [['target_species'], 'exist', 'skipOnError' => true, 'targetClass' => MFishTypes::class, 'targetAttribute' => ['target_species' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'boat_data_id' => Yii::t('app', 'Boat Data ID'),
            'gear' => Yii::t('app', 'Gear'),
            'target_species' => Yii::t('app', 'Target Species'),
            'operations_per_trip' => Yii::t('app', 'Operations Per Trip'),
            'fishing_time_days' => Yii::t('app', 'Fishing Time Days'),
            'fishing_time_hours' => Yii::t('app', 'Fishing Time Hours'),
            'fishing_depth' => Yii::t('app', 'Fishing Depth'),
            'g_code' => Yii::t('app', 'G Code'),
            'extra' => Yii::t('app', 'Extra'),
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[BoatData]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoatData()
    {
        return $this->hasOne(ScientificSamplingBoatGearData::class, ['id' => 'boat_data_id']);
    }

    /**
     * Gets query for [[Gear0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGear0()
    {
        return $this->hasOne(MGearTypes::class, ['id' => 'gear']);
    }

    /**
     * Gets query for [[TargetSpecies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTargetSpecies()
    {
        return $this->hasOne(MFishTypes::class, ['id' => 'target_species']);
    }
}
