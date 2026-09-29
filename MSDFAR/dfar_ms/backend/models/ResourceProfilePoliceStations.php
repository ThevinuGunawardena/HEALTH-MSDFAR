<?php

namespace backend\models;

use Yii;

class ResourceProfilePoliceStations extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'resource_profile_police_stations';
    }

    public function rules()
    {
        return [
            [['resource_profile_id', 'police_station_name'], 'required'],
            [['resource_profile_id'], 'integer'],
            [['police_station_name'], 'string', 'max' => 255],
            [['phone_number'], 'string', 'max' => 10],
            [['resource_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResourceProfile::class, 'targetAttribute' => ['resource_profile_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_profile_id' => 'Resource Profile ID',
            'police_station_name' => 'Police Station Name',
            'phone_number' => 'Phone Number',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getResourceProfile()
    {
        return $this->hasOne(ResourceProfile::class, ['id' => 'resource_profile_id']);
    }
}
