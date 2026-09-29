<?php

namespace backend\models;

use Yii;

class ResourceProfileBeachSites extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'resource_profile_beach_sites';
    }

    public function rules()
    {
        return [
            [['resource_profile_id', 'beach_site_name'], 'required'],
            [['resource_profile_id'], 'integer'],
            [['beach_site_name'], 'string', 'max' => 255],
            [['resource_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResourceProfile::class, 'targetAttribute' => ['resource_profile_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_profile_id' => 'Resource Profile ID',
            'beach_site_name' => 'Beach Site Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getResourceProfile()
    {
        return $this->hasOne(ResourceProfile::class, ['id' => 'resource_profile_id']);
    }
}
