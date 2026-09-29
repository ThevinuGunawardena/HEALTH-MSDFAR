<?php

namespace backend\models;

use Yii;

class ResourceProfileSpecialProjects extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'resource_profile_special_projects';
    }

    public function rules()
    {
        return [
            [['resource_profile_id', 'project_name'], 'required'],
            [['resource_profile_id'], 'integer'],
            [['project_name'], 'string', 'max' => 255],
            [['project_description'], 'string', 'max' => 1000],
            [['resource_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResourceProfile::class, 'targetAttribute' => ['resource_profile_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_profile_id' => 'Resource Profile ID',
            'project_name' => 'Project Name',
            'project_description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getResourceProfile()
    {
        return $this->hasOne(ResourceProfile::class, ['id' => 'resource_profile_id']);
    }
}
