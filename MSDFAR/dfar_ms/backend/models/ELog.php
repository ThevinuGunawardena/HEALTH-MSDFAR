<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

class ELog extends ActiveRecord
{
    public static function tableName()
    {
        return 'e_log'; 
    }

    public function rules()
{
    return [
        [['vessel_id', 'skipper_id', 'arrival_date', 'arrival_harbour', 'departure_harbour','log_book_no','log_sheet_number'], 'required'],
        [['vessel_id', 'skipper_id','log_book_no','log_sheet_number','phone_number'], 'string'],
        [['arrival_date', 'departure_date'], 'safe'],
        [['approve'], 'boolean'],
        [['created_at', 'updated_at'], 'safe'],  

         ['arrival_date', 'compare', 'compareAttribute' => 'departure_date', 'operator' => '>', 'type' => 'date', 'message' => 'Arrival Date must be later than Departure Date.'],
    ];
}

    public function attributeLabels()
    {
        return [
            'vessel_id' => 'Vessel',
            'skipper_id' => 'Skipper',
            'phone_number' => "Owner's Phone Number",
            'arrival_date' => 'Arrival Date',
            'arrival_harbour' => 'Arrival Harbour',
            'departure_date' => 'Departure Date',
            'departure_harbour' => 'Departure Harbour',
            'log_sheet_number' => 'Log Book Number', // change this right ones please use right one until that
            'log_book_no' => 'Log page Number',
            'approve' => 'Approved',
        ];
    }

    public function getVessel()
    {
        return $this->hasOne(BoatNumbers::class, ['id' => 'vessel_id']);
    }

    public function getSkipper()
    {
        return $this->hasOne(Skipper::class, ['id' => 'skipper_id']);
    }

    public function getArrivalHarbour()
    {
        return $this->hasOne(MHarbours::class, ['Id' => 'arrival_harbour']);
    }

    public function getDepartureHarbour()
    {
        return $this->hasOne(MHarbours::class, ['Id' => 'departure_harbour']);
    }

    
}