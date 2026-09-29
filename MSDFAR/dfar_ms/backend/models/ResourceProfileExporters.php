<?php

namespace backend\models;

use Yii;

class ResourceProfileExporters extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'resource_profile_exporters';
    }

    public function rules()
    {
        return [
            [['resource_profile_id', 'exporter_name'], 'required'],
            [['resource_profile_id'], 'integer'],
            [['exporter_name'], 'string', 'max' => 255],
            [['resource_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResourceProfile::class, 'targetAttribute' => ['resource_profile_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_profile_id' => 'Resource Profile ID',
            'exporter_name' => 'Exporter Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getResourceProfile()
    {
        return $this->hasOne(ResourceProfile::class, ['id' => 'resource_profile_id']);
    }
}
