<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "scientific_sampling_length_details".
 *
 * @property int $id
 * @property int $sampling_data_id
 * @property int $gear
 * @property int $specie
 * @property int $specie_count
 * @property float $weight
 * @property int $weight_code
 * @property int $length_type
 * @property int $length_code
 * @property float $length
 * @property int $status
 *
 * @property MGearTypes $gear0
 * @property ScientificSamplingData $samplingData
 * @property MFishTypes $specie0
 */
class ScientificSamplingLengthDetails extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_sampling_length_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sampling_data_id', 'gear', 'specie', 'specie_count', 'weight', 'weight_code', 'length_type', 'length_code', 'length', 'status'], 'required'],
            [['sampling_data_id', 'gear', 'specie', 'specie_count', 'weight_code', 'length_type', 'length_code', 'status'], 'integer'],
            [['weight', 'length'], 'number'],
            [['gear'], 'exist', 'skipOnError' => true, 'targetClass' => MGearTypes::class, 'targetAttribute' => ['gear' => 'id']],
            [['sampling_data_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScientificSamplingData::class, 'targetAttribute' => ['sampling_data_id' => 'id']],
            [['specie'], 'exist', 'skipOnError' => true, 'targetClass' => MFishTypes::class, 'targetAttribute' => ['specie' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'sampling_data_id' => Yii::t('app', 'Sampling Data ID'),
            'gear' => Yii::t('app', 'Gear'),
            'specie' => Yii::t('app', 'Specie'),
            'specie_count' => Yii::t('app', 'Specie Count'),
            'weight' => Yii::t('app', 'Weight'),
            'weight_code' => Yii::t('app', 'Weight Code'),
            'length_type' => Yii::t('app', 'Length Type'),
            'length_code' => Yii::t('app', 'Length Code'),
            'length' => Yii::t('app', 'Length'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[Gear0]].
     *
     * @return ActiveQuery
     */
    public function getGear0()
    {
        return $this->hasOne(MGearTypes::class, ['id' => 'gear']);
    }

    /**
     * Gets query for [[SamplingData]].
     *
     * @return ActiveQuery
     */
    public function getSamplingData()
    {
        return $this->hasOne(ScientificSamplingData::class, ['id' => 'sampling_data_id']);
    }

    /**
     * Gets query for [[Specie0]].
     *
     * @return ActiveQuery
     */
    public function getSpecie0()
    {
        return $this->hasOne(MFishTypes::class, ['id' => 'specie']);
    }
}
