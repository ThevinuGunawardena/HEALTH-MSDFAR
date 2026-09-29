<?php
namespace backend\models;

use yii\db\ActiveRecord;

class ELogSetDiscardedDead extends ActiveRecord
{
    public static function tableName() { return 'e_log_set_discarded_dead'; }

    public function rules()
    {
        return [
            [['e_log_set_id', 'fish_type_id', 'fish_variant_id',
              'weight', 'fish_count'], 'required'],
            [['e_log_set_id', 'fish_type_id',
              'fish_variant_id', 'fish_count'], 'integer'],
            [['weight'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fish_type_id'    => 'Fish Type',
            'fish_variant_id' => 'Fish Variant',
            'weight'          => 'Weight (kg)',
            'fish_count'      => 'Number of Fish',
        ];
    }

    public function getSet()
    {
        return $this->hasOne(ELogSets::class, ['id' => 'e_log_set_id']);
    }

    public function getFishType()
    {
        return $this->hasOne(MELogFishType::class, ['id' => 'fish_type_id']);
    }

    public function getFishVariant()
    {
        return $this->hasOne(MELogFishVariant::class, ['id' => 'fish_variant_id']);
    }
}