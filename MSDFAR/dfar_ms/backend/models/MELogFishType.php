<?php

namespace backend\models;

use yii\db\ActiveRecord;

class MELogFishType extends ActiveRecord
{
    public static function tableName()
    {
        return 'm_e_log_fish_type';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'   => 'ID',
            'name' => 'Fish Type',
        ];
    }

    // One fish type has many variants
    public function getVariants()
    {
        return $this->hasMany(MFishVariant::class, ['fish_type_id' => 'id']);
    }

    // One fish type has many catch records
    public function getCatches()
    {
        return $this->hasMany(ELogSetCatch::class, ['fish_type_id' => 'id']);
    }
}