<?php

namespace backend\models;

use Yii;

class ResourceProfileDryFishManufactures extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'resource_profile_dry_fish_manufactures';
    }

    public function rules()
    {
        return [
            [['resource_profile_id', 'manufacture_name'], 'required'],
            [['resource_profile_id'], 'integer'],
            [['manufacture_name'], 'string', 'max' => 255],
            [['resource_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResourceProfile::class, 'targetAttribute' => ['resource_profile_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_profile_id' => 'Resource Profile ID',
            'manufacture_name' => 'Manufacture Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getResourceProfile()
    {
        return $this->hasOne(ResourceProfile::class, ['id' => 'resource_profile_id']);
    }
}
