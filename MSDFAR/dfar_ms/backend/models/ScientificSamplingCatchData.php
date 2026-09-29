<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scientific_sampling_catch_data".
 *
 * @property int $id
 * @property int $craft_id
 * @property int $gear
 * @property int $specie
 * @property int $weight_code
 * @property float $weight
 *
 * @property ScientificSamplingData $craft
 * @property MGearTypes $gear0
 * @property MFishTypes $specie0
 */
class ScientificSamplingCatchData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_sampling_catch_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['craft_id', 'gear', 'specie', 'weight_code', 'weight'], 'required'],
            [['craft_id', 'gear', 'specie', 'weight_code'], 'integer'],
            [['weight'], 'number'],
            [['craft_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScientificSamplingData::class, 'targetAttribute' => ['craft_id' => 'id']],
            [['specie'], 'exist', 'skipOnError' => true, 'targetClass' => MFishTypes::class, 'targetAttribute' => ['specie' => 'id']],
            [['gear'], 'exist', 'skipOnError' => true, 'targetClass' => MGearTypes::class, 'targetAttribute' => ['gear' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'craft_id' => Yii::t('app', 'Craft ID'),
            'gear' => Yii::t('app', 'Gear'),
            'specie' => Yii::t('app', 'Specie'),
            'weight_code' => Yii::t('app', 'Weight Code'),
            'weight' => Yii::t('app', 'Weight'),
        ];
    }

    /**
     * Gets query for [[Craft]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCraft()
    {
        return $this->hasOne(ScientificSamplingData::class, ['id' => 'craft_id']);
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
     * Gets query for [[Specie0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecie0()
    {
        return $this->hasOne(MFishTypes::class, ['id' => 'specie']);
    }
}
