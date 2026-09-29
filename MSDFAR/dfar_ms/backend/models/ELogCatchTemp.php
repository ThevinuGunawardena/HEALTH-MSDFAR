<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "e_log_catch_temp".
 *
 * @property int $id
 * @property int|null $e_log_set_temp_id
 * @property int|null $fish_type_id
 * @property int|null $fish_variant_id
 * @property float|null $weight
 * @property int|null $fish_count
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property ELogSetTemp $set
 * @property MELogFishType $fishType
 * @property MELogFishVariant $fishVariant
 */
class ELogCatchTemp extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'e_log_catch_temp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['e_log_set_temp_id', 'fish_type_id', 'fish_variant_id', 'fish_count'], 'integer'],
            [['weight'], 'number'],

            [['e_log_set_temp_id', 'fish_type_id', 'fish_variant_id'], 'required'],

            [['weight', 'fish_count'], 'required'],
            [['weight', 'fish_count'], 'validateNotBothZero'],

            [['created_at', 'updated_at'], 'safe'],

            [['e_log_set_temp_id'], 'exist', 'skipOnError' => true, 'targetClass' => ELogSetTemp::class, 'targetAttribute' => ['e_log_set_temp_id' => 'id']],
            [['fish_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => MELogFishType::class, 'targetAttribute' => ['fish_type_id' => 'id']],
            [['fish_variant_id'], 'exist', 'skipOnError' => true, 'targetClass' => MELogFishVariant::class, 'targetAttribute' => ['fish_variant_id' => 'id']],
        ];
    }

    /**
     * Custom validator: weight and fish_count cannot both be zero/empty.
     */
    public function validateNotBothZero($attribute, $params)
    {
        $weight = (float) $this->weight;
        $count = (int) $this->fish_count;

        if ($weight <= 0 && $count <= 0) {
            $this->addError('weight', 'Weight and fish count cannot both be zero.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'e_log_set_temp_id' => 'Set',
            'fish_type_id' => 'Fish Type',
            'fish_variant_id' => 'Fish Variant',
            'weight' => 'Weight (kg)',
            'fish_count' => 'No. of Fish',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Set]].
     *
     * @return ActiveQuery
     */
    public function getSet()
    {
        return $this->hasOne(ELogSetTemp::class, ['id' => 'e_log_set_temp_id']);
    }

    /**
     * Gets query for [[FishType]].
     *
     * @return ActiveQuery
     */
    public function getFishType()
    {
        return $this->hasOne(MELogFishType::class, ['id' => 'fish_type_id']);
    }

    /**
     * Gets query for [[FishVariant]].
     *
     * @return ActiveQuery
     */
    public function getFishVariant()
    {
        return $this->hasOne(MELogFishVariant::class, ['id' => 'fish_variant_id']);
    }
}