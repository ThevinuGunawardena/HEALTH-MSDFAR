<?php

namespace backend\models;

use Yii;

class ResourceProfileIceFactories extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'resource_profile_ice_factories';
    }

    public function rules()
    {
        return [
            [['resource_profile_id', 'ice_factory_name'], 'required'],
            [['resource_profile_id'], 'integer'],
            [['ice_factory_name'], 'string', 'max' => 255],
            [['resource_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResourceProfile::class, 'targetAttribute' => ['resource_profile_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_profile_id' => 'Resource Profile ID',
            'ice_factory_name' => 'Ice Factory Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getResourceProfile()
    {
        return $this->hasOne(ResourceProfile::class, ['id' => 'resource_profile_id']);
    }
}
