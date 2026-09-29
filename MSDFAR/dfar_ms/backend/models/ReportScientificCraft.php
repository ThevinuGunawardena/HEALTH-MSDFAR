<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "report_scientific_craft".
 *
 * @property int $scientifi_data_id
 * @property int $sampling_id
 * @property string $boat_number
 * @property int $fishey_type
 * @property int|null $sub_category
 * @property string $engine_hp
 * @property string $departure_date
 * @property string $departure_time
 * @property int $departure_district
 * @property int $departure_division
 * @property int $depature_port
 * @property string|null $depature_port_name
 * @property string $weather
 * @property string $arrival_date
 * @property string|null $remark_s
 * @property int $crew_members_count
 * @property string $unloading_type
 * @property string $gear_setting_time
 * @property int $days
 * @property int $hours
 * @property int|null $id
 * @property float|null $fuel_qty
 * @property float|null $fuel_price
 * @property float|null $ice_qty
 * @property float|null $ice_price
 * @property float|null $bait_qty
 * @property float|null $bait_price
 * @property float|null $labour_cost
 * @property float|null $food_water
 * @property float|null $other
 * @property string|null $remark
 * @property int|null $sampling_data_id
 */
class ReportScientificCraft extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_scientific_craft';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scientifi_data_id', 'sampling_id', 'fishey_type', 'sub_category', 'departure_district', 'departure_division', 'depature_port', 'crew_members_count', 'days', 'hours', 'id', 'sampling_data_id'], 'integer'],
            [['boat_number', 'fishey_type', 'engine_hp', 'departure_date', 'departure_time', 'departure_district', 'departure_division', 'depature_port', 'weather', 'arrival_date', 'crew_members_count', 'unloading_type', 'gear_setting_time', 'days', 'hours'], 'required'],
            [['departure_date', 'departure_time', 'arrival_date'], 'safe'],
            [['fuel_qty', 'fuel_price', 'ice_qty', 'ice_price', 'bait_qty', 'bait_price', 'labour_cost', 'food_water', 'other'], 'number'],
            [['boat_number', 'depature_port_name', 'weather', 'unloading_type'], 'string', 'max' => 200],
            [['engine_hp'], 'string', 'max' => 11],
            [['remark_s', 'remark'], 'string', 'max' => 500],
            [['gear_setting_time'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'scientifi_data_id' => Yii::t('app', 'Scientifi Data ID'),
            'sampling_id' => Yii::t('app', 'Sampling Data ID'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'fishey_type' => Yii::t('app', 'Fishey Type'),
            'sub_category' => Yii::t('app', 'Boat Sub Category'),
            'engine_hp' => Yii::t('app', 'Engine Hp'),
            'departure_date' => Yii::t('app', 'Departure Date'),
            'departure_time' => Yii::t('app', 'Departure Time'),
            'departure_district' => Yii::t('app', 'Departure District'),
            'departure_division' => Yii::t('app', 'Departure Division'),
            'depature_port' => Yii::t('app', 'Depature Port'),
            'depature_port_name' => Yii::t('app', 'Depature Port Name'),
            'weather' => Yii::t('app', 'Weather'),
            'arrival_date' => Yii::t('app', 'Arrival Date'),
            'remark_s' => Yii::t('app', 'Remark'),
            'crew_members_count' => Yii::t('app', 'Crew Members Count'),
            'unloading_type' => Yii::t('app', 'Unloading Type'),
            'gear_setting_time' => Yii::t('app', 'Gear Setting Time'),
            'days' => Yii::t('app', 'True Fishing Days'),
            'hours' => Yii::t('app', 'True Fishing Hours'),
            'id' => Yii::t('app', 'ID'),
            'fuel_qty' => Yii::t('app', 'Fuel Qty'),
            'fuel_price' => Yii::t('app', 'Fuel Value'),
            'ice_qty' => Yii::t('app', 'Ice Qty'),
            'ice_price' => Yii::t('app', 'Ice Value'),
            'bait_qty' => Yii::t('app', 'Bait Qty'),
            'bait_price' => Yii::t('app', 'Bait Value'),
            'labour_cost' => Yii::t('app', 'Labour Cost'),
            'food_water' => Yii::t('app', 'Food Water'),
            'other' => Yii::t('app', 'Other'),
            'remark' => Yii::t('app', 'Remark'),
            'sampling_data_id' => Yii::t('app', 'Sampling Data ID'),
        ];
    }
}
/*
 CREATE OR REPLACE VIEW report_scientific_craft AS SELECT s.id as scientifi_data_id,s.district, sd.id as sampling_id, sd
.boat_number,  	fryt.name as fishey_type, sub_category, engine_hp, departure_date, departure_time, departure_district, departure_division, depature_port,ls.name as depature_port_name, weather, arrival_date, opc.remark as remark_s, crew_members_count, unloading_type, gear_setting_time, days, hours, opc.*
FROM `scientific_data` s
INNER JOIN scientific_sampling_data sd on sd.scientific_id=s.id
INNER JOIN scientific_sampling_boat_gear_data sbg on sbg.sampling_data_id=sd.id
left join m_landing_site ls on sbg.depature_port=ls.id
left join scientific_sampling_operation_cost opc on opc.sampling_data_id=sd.id
left join  m_fishery_types fryt on fryt.id=sbg.fishey_type
;


 */