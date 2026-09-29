<?php

namespace backend\models;

use Yii;

class ResourceProfileFisheriesCooperateSociety extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'resource_profile_fisheries_cooperate_society';
    }

    public function rules()
    {
        return [
            [['resource_profile_id', 'cooperate_society_name'], 'required'],
            [['resource_profile_id'], 'integer'],
            [['cooperate_society_name'], 'string', 'max' => 255],
            [['resource_profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResourceProfile::class, 'targetAttribute' => ['resource_profile_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'resource_profile_id' => 'Resource Profile ID',
            'cooperate_society_name' => 'Cooperate Society Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getResourceProfile()
    {
        return $this->hasOne(ResourceProfile::class, ['id' => 'resource_profile_id']);
    }
}
