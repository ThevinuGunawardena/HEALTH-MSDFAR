<?php
namespace backend\models;

use yii\db\ActiveRecord;

class ELogSets extends ActiveRecord
{
    const TYPE_LONGLINE = 'longline';
    const TYPE_GILLNET  = 'gillnet';
    const TYPE_RINGNET  = 'ringnet';

    public static function tableName() { return 'e_log_sets'; }

    public function rules()
    {
        return [
           [['gear_id', 'gear_type', 'set_number', 
          'start_datetime', 'start_gps_direction', 'start_gps_n', 'start_gps_e'
        ], 'required'],
            [['gear_id', 'set_number','five_by_five','one_by_one'], 'integer'],
            [['gear_type'], 'in', 'range' => [self::TYPE_LONGLINE, self::TYPE_GILLNET, self::TYPE_RINGNET]],
            [['start_datetime', 'end_datetime'], 'safe'],
            [['start_gps_direction', 'end_gps_direction'], 'in', 'range' => ['N', 'S']],
            [['start_gps_n', 'start_gps_e', 'end_gps_n', 'end_gps_e'], 'string', 'max' => 30],
        ];
    }

    public function attributeLabels()
    {
        return [
            'gear_type'           => 'Gear Type',
            'set_number'          => 'Set Number',
            'start_datetime'      => 'Start Date & Time',
            'start_gps_direction' => 'Start GPS Direction',
            'start_gps_n'         => 'Start GPS ',
            'start_gps_e'         => 'Start GPS E',
            'end_datetime'        => 'End Date & Time',
            'end_gps_direction'   => 'End GPS Direction',
            'end_gps_n'           => 'End GPS ',
            'end_gps_e'           => 'End GPS E',
        ];
    }

    /* =========================
     * DYNAMIC RELATION
     * based on gear_type
     * ========================= */
    public function getGearRecord()
    {
        switch ($this->gear_type) {
            case self::TYPE_LONGLINE:
                return $this->hasOne(ELogLongline::class, ['id' => 'gear_id']);
            case self::TYPE_GILLNET:
                return $this->hasOne(ELogGillnet::class, ['id' => 'gear_id']);
            case self::TYPE_RINGNET:
                return $this->hasOne(ELogRingnet::class, ['id' => 'gear_id']);
        }
        return null;
    }

    /* helper to fetch the parent record */
    public function getParent()
    {
        switch ($this->gear_type) {
            case self::TYPE_LONGLINE:
                return ELogLongline::findOne($this->gear_id);
            case self::TYPE_GILLNET:
                return ELogGillnet::findOne($this->gear_id);
            case self::TYPE_RINGNET:
                return ELogRingnet::findOne($this->gear_id);
        }
        return null;
    }
//     public function init()
// {
//     parent::init();
//     if ($this->isNewRecord) {
//         $this->start_gps_direction = 'S';
//         $this->end_gps_direction   = 'S';
//     }
// }
public function getCatches()
{
    return $this->hasMany(ELogSetCatch::class, ['e_log_set_id' => 'id']);
}

public function getDiscardedDead()
{
    return $this->hasMany(ELogSetDiscardedDead::class, ['e_log_set_id' => 'id']);
}

public function getDiscardedLive()
{
    return $this->hasMany(ELogSetDiscardedLive::class, ['e_log_set_id' => 'id']);
}
}