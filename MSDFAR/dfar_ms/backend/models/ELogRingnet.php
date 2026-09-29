<?php

namespace backend\models;

use yii\db\ActiveRecord;

class ELogRingnet extends ActiveRecord
{
    public static function tableName()
    {
        return 'e_log_ringnet';
    }

    public function rules()
    {
        return [
            [['e_log_id', 'net_length', 'net_height'], 'required'],
            [['e_log_id'], 'integer'],
            [['net_length', 'net_height'], 'number'],
            [['fad'], 'safe'], // for any additional fields that may be added later
        ];
    }

    public function attributeLabels()
    {
        return [
            'e_log_id'   => 'E-Log Reference',
            'net_length' => 'Length of the Ring Net (m)',
            'net_height' => 'Height of the Ring Net (m)',
            'fad'        => 'If fad is used mention',
        ];
    }

    // Relationship back to parent ELog
    public function getELog()
    {
        return $this->hasOne(ELog::class, ['id' => 'e_log_id']);
    }
}