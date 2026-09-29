<?php

namespace backend\models;

use Yii;

class ResourceProfileTraditionalFishing extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'resource_profile_traditional_fishing';
    }

    public function rules()
    {
        return [
            [['resource_profile_id', 'traditional_fishing_name'], 'required'],
            [['resource_profile_id'], 'integer'],
            [['traditional_fishing_name'], 'string', 'max' => 255],
            [['traditional_fishing_description'], 'string', 'max' => 1000],
            [['resource_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResourceProfile::class, 'targetAttribute' => ['resource_profile_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_profile_id' => 'Resource Profile ID',
            'traditional_fishing_name' => 'Traditional Fishing Name',
            'traditional_fishing_description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getResourceProfile()
    {
        return $this->hasOne(ResourceProfile::class, ['id' => 'resource_profile_id']);
    }
}
