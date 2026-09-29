<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scientific_sampling_length_data".
 *
 * @property int $id
 * @property int $craft_id
 * @property int $gear
 * @property int $no_of_fish
 * @property int $weight_code
 * @property float $weight
 */
class ScientificSamplingLengthData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_sampling_length_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['craft_id', 'gear', 'no_of_fish', 'weight_code', 'weight'], 'required'],
            [['craft_id', 'gear', 'no_of_fish', 'weight_code'], 'integer'],
            [['weight'], 'number'],
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
            'no_of_fish' => Yii::t('app', 'No Of Fish'),
            'weight_code' => Yii::t('app', 'Weight Code'),
            'weight' => Yii::t('app', 'Weight'),
        ];
    }
}
