<?php

namespace backend\models;

use Yii;

class ResourceProfileFishingVillages extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'resource_profile_fishing_villages';
    }

    public function rules()
    {
        return [
            [['resource_profile_id', 'village_name'], 'required'],
            [['resource_profile_id'], 'integer'],
            [['village_name'], 'string', 'max' => 255],
            [['resource_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResourceProfile::class, 'targetAttribute' => ['resource_profile_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_profile_id' => 'Resource Profile ID',
            'village_name' => 'Village Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getResourceProfile()
    {
        return $this->hasOne(ResourceProfile::class, ['id' => 'resource_profile_id']);
    }
}
