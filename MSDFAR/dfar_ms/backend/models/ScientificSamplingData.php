<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "scientific_sampling_data".
 *
 * @property int $id
 * @property int $scientific_id
 * @property string $boat_number
 * @property int $status
 *
 * @property ScientificSamplingLengthDetails[] $scientificSamplingLengthDetails
 */
class ScientificSamplingData extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_sampling_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scientific_id', 'boat_number', 'status'], 'required'],
            [['scientific_id', 'status'], 'integer'],
            [['boat_number'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'scientific_id' => Yii::t('app', 'Scientific ID'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[ScientificData]].
     *
     * @return ActiveQuery
     */
    public function getScientificData()
    {
        return $this->hasOne(ScientificData::class, ['id' => 'scientific_id']);
    }

    /**
     * Gets query for [[ScientificSamplingLengthDetails]].
     *
     * @return ActiveQuery
     */
    public function getScientificSamplingLengthDetails()
    {
        return $this->hasMany(ScientificSamplingLengthDetails::class, ['sampling_data_id' => 'id']);
    }

    /**
     * Gets query for [[ScientificSamplingBoatGearDatas]].
     *
     * @return ActiveQuery
     */
    public function getScientificSamplingBoatGearDatas()
    {
        return $this->hasMany(ScientificSamplingBoatGearData::class, ['sampling_data_id' => 'id']);
    }
}
