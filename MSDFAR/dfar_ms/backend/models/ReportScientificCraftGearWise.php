<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "report_scientific_craft_gear_wise".
 *
 * @property int $scientifi_data_id
 * @property int $district
 * @property int $sampling_id
 * @property string $boat_number
 * @property int|null $gear
 * @property string|null $type
 * @property string|null $gear_code
 * @property string|null $gear_name
 * @property int|null $target_species
 * @property string|null $target_species_name
 * @property int|null $operations_per_trip
 * @property float|null $fishing_time_days
 * @property float|null $fishing_time_hours
 * @property float|null $fishing_depth
 * @property int|null $g_code
 * @property string|null $extra
 * @property string|null $fishey_type
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
class ReportScientificCraftGearWise extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_scientific_craft_gear_wise';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scientifi_data_id', 'district', 'sampling_id', 'gear', 'target_species', 'operations_per_trip', 'g_code', 'sub_category', 'departure_district', 'departure_division', 'depature_port', 'crew_members_count', 'days', 'hours', 'id', 'sampling_data_id'], 'integer'],
            [['district', 'boat_number', 'engine_hp', 'departure_date', 'departure_time', 'departure_district', 'departure_division', 'depature_port', 'weather', 'arrival_date', 'crew_members_count', 'unloading_type', 'gear_setting_time', 'days', 'hours'], 'required'],
            [['fishing_time_days', 'fishing_time_hours', 'fishing_depth', 'fuel_qty', 'fuel_price', 'ice_qty', 'ice_price', 'bait_qty', 'bait_price', 'labour_cost', 'food_water', 'other'], 'number'],
            [['departure_date', 'departure_time', 'arrival_date'], 'safe'],
            [['boat_number', 'gear_name', 'target_species_name', 'fishey_type', 'depature_port_name', 'weather', 'unloading_type'], 'string', 'max' => 200],
            [['type', 'gear_code', 'gear_setting_time'], 'string', 'max' => 100],
            [['extra', 'remark_s', 'remark'], 'string', 'max' => 500],
            [['engine_hp'], 'string', 'max' => 11],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'scientifi_data_id' => Yii::t('app', 'Scientifi Data ID'),
            'district' => Yii::t('app', 'District'),
            'sampling_id' => Yii::t('app', 'Sampling ID'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'gear' => Yii::t('app', 'Gear'),
            'type' => Yii::t('app', 'Type'),
            'gear_code' => Yii::t('app', 'Gear Code'),
            'gear_name' => Yii::t('app', 'Gear Name'),
            'target_species' => Yii::t('app', 'Target Species'),
            'target_species_name' => Yii::t('app', 'Target Species Name'),
            'operations_per_trip' => Yii::t('app', 'Operations Per Trip'),
            'fishing_time_days' => Yii::t('app', 'Fishing Time Days'),
            'fishing_time_hours' => Yii::t('app', 'Fishing Time Hours'),
            'fishing_depth' => Yii::t('app', 'Fishing Depth'),
            'g_code' => Yii::t('app', 'G Code'),
            'extra' => Yii::t('app', 'Extra'),
            'fishey_type' => Yii::t('app', 'Fishey Type'),
            'sub_category' => Yii::t('app', 'Sub Category'),
            'engine_hp' => Yii::t('app', 'Engine Hp'),
            'departure_date' => Yii::t('app', 'Departure Date'),
            'departure_time' => Yii::t('app', 'Departure Time'),
            'departure_district' => Yii::t('app', 'Departure District'),
            'departure_division' => Yii::t('app', 'Departure Division'),
            'depature_port' => Yii::t('app', 'Depature Port'),
            'depature_port_name' => Yii::t('app', 'Depature Port Name'),
            'weather' => Yii::t('app', 'Weather'),
            'arrival_date' => Yii::t('app', 'Arrival Date'),
            'remark_s' => Yii::t('app', 'Remark S'),
            'crew_members_count' => Yii::t('app', 'Crew Members Count'),
            'unloading_type' => Yii::t('app', 'Unloading Type'),
            'gear_setting_time' => Yii::t('app', 'Gear Setting Time'),
            'days' => Yii::t('app', 'Days'),
            'hours' => Yii::t('app', 'Hours'),
            'id' => Yii::t('app', 'ID'),
            'fuel_qty' => Yii::t('app', 'Fuel Qty'),
            'fuel_price' => Yii::t('app', 'Fuel Price'),
            'ice_qty' => Yii::t('app', 'Ice Qty'),
            'ice_price' => Yii::t('app', 'Ice Price'),
            'bait_qty' => Yii::t('app', 'Bait Qty'),
            'bait_price' => Yii::t('app', 'Bait Price'),
            'labour_cost' => Yii::t('app', 'Labour Cost'),
            'food_water' => Yii::t('app', 'Food Water'),
            'other' => Yii::t('app', 'Other'),
            'remark' => Yii::t('app', 'Remark'),
            'sampling_data_id' => Yii::t('app', 'Sampling Data ID'),
        ];
    }
}
/*
 CREATE OR REPLACE VIEW report_scientific_craft_gear_wise AS  SELECT s.id as scientifi_data_id,s.district, sd.id as sampling_id, sd
.boat_number, sgd.gear, sgd.type,mgt.code as gear_code, mgt.description as gear_name,
sgd.target_species,mft.name as target_species_name ,sgd.operations_per_trip, sgd.fishing_time_days,sgd.fishing_time_hours,sgd.fishing_depth,sgd.g_code,sgd.extra,
fryt.name as fishey_type, sub_category, engine_hp, departure_date, departure_time, departure_district, departure_division, depature_port,ls.name as depature_port_name, weather, arrival_date, opc.remark as remark_s, crew_members_count, unloading_type, gear_setting_time, days, hours, opc.*
FROM `scientific_data` s
INNER JOIN scientific_sampling_data sd on sd.scientific_id=s.id

INNER JOIN scientific_sampling_boat_gear_data sbg on sbg.sampling_data_id=sd.id
left join scientific_sampling_gear_data sgd on sgd.boat_data_id=sbg.id

left join m_landing_site ls on sbg.depature_port=ls.id
left join scientific_sampling_operation_cost opc on opc.sampling_data_id=sd.id
left join  m_fishery_types fryt on fryt.id=sbg.fishey_type
LEFT JOIN m_gear_types mgt on mgt.id=sgd.gear
LEFT JOIN m_fish_types mft on mft.id=sgd.target_species;
 */