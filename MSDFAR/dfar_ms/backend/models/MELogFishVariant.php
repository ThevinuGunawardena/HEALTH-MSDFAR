<?php

namespace backend\models;

use yii\db\ActiveRecord;

class MELogFishVariant extends ActiveRecord
{
    public static function tableName()
    {
        return 'm_e_log_fish_variant';
    }

    public function rules()
    {
        return [
            [['fish_type_id', 'name'], 'required'],
            [['fish_type_id'], 'integer'],
            [['name'], 'string', 'max' => 50],

            // same variant name cannot exist under the same fish type
            [['name'], 'unique',
                'targetAttribute' => ['fish_type_id', 'name'],
                'message'         => 'This variant already exists for this fish type.'
            ],

            // make sure the fish_type_id actually exists
            [['fish_type_id'], 'exist',
                'skipOnError'     => true,
                'targetClass'     => MELogFishType::class,
                'targetAttribute' => ['fish_type_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'fish_type_id' => 'Fish Type',
            'name'         => 'Variant Name',
        ];
    }

    // Belongs to one fish type
    public function getFishType()
    {
        return $this->hasOne(MELogFishType::class, ['id' => 'fish_type_id']);
    }

    // One variant has many catch records
    public function getCatches()
    {
        return $this->hasMany(ELogSetCatch::class, ['fish_variant_id' => 'id']);
    }
}