<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "report_scientific_catch2".
 *
 * @property int $scientifi_data_id
 * @property string $scientific_code
 * @property int $sampling_id
 * @property string $boat_number
 * @property string $district_name
 * @property int $district
 * @property string $division_name
 * @property int $division
 * @property string|null $landing_site
 * @property string $start_time
 * @property int $added_by
 * @property string $name
 * @property float $weight
 * @property int $specie_name
 * @property int $gear
 * @property int $weight_code
 * @property string $code
 * @property string $description
 * @property int|null $id
 * @property int|null $operation_cost_id
 * @property int|null $specie
 * @property float|null $export_qty
 * @property float|null $export_value
 * @property float|null $local_qty
 * @property float|null $local_value
 * @property float|null $dried_qty
 * @property float|null $dried_value
 * @property float|null $discard_qty
 * @property float|null $discard_value
 * @property int|null $trash
 * @property int|null $status
 */
class ReportScientificCatch2 extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_scientific_catch2';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scientifi_data_id', 'sampling_id', 'district', 'division', 'added_by', 'specie_name', 'gear', 'weight_code', 'id', 'operation_cost_id', 'specie', 'trash', 'status'], 'integer'],
            [['boat_number', 'district_name', 'district', 'division_name', 'division', 'start_time', 'added_by', 'name', 'weight', 'specie_name', 'gear', 'weight_code', 'code', 'description'], 'required'],
            [['start_time', 'scientific_code'], 'safe'],
            [['scientific_code'], 'string', 'max' => 200],
            [['weight', 'export_qty', 'export_value', 'local_qty', 'local_value', 'dried_qty', 'dried_value', 'discard_qty', 'discard_value'], 'number'],
            [['boat_number', 'district_name', 'division_name', 'landing_site', 'name', 'description'], 'string', 'max' => 200],
            [['code'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'scientifi_data_id' => Yii::t('app', 'Scientifi Data ID'),
            'scientific_code' => Yii::t('app', 'Scientific Code'),
            'sampling_id' => Yii::t('app', 'Sampling ID'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'district_name' => Yii::t('app', 'District Name'),
            'district' => Yii::t('app', 'District'),
            'division_name' => Yii::t('app', 'Division Name'),
            'division' => Yii::t('app', 'Division'),
            'landing_site' => Yii::t('app', 'Landing Site'),
            'start_time' => Yii::t('app', 'Start Time'),
            'added_by' => Yii::t('app', 'Added By'),
            'name' => Yii::t('app', 'Name'),
            'weight' => Yii::t('app', 'Weight'),
            'specie_name' => Yii::t('app', 'Specie Name'),
            'gear' => Yii::t('app', 'Gear'),
            'weight_code' => Yii::t('app', 'Weight Code'),
            'code' => Yii::t('app', 'Code'),
            'description' => Yii::t('app', 'Description'),
            'id' => Yii::t('app', 'ID'),
            'operation_cost_id' => Yii::t('app', 'Operation Cost ID'),
            'specie' => Yii::t('app', 'Specie'),
            'export_qty' => Yii::t('app', 'Export Qty'),
            'export_value' => Yii::t('app', 'Export Value'),
            'local_qty' => Yii::t('app', 'Local Market Quantity'),
            'local_value' => Yii::t('app', 'Local Market Value'),
            'dried_qty' => Yii::t('app', 'Dried Fish Quantity'),
            'dried_value' => Yii::t('app', 'Dried Fish Value'),
            'discard_qty' => Yii::t('app', 'Discard Qty'),
            'discard_value' => Yii::t('app', 'Discard Value'),
            'trash' => Yii::t('app', 'Trash'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}

/*
 CREATE OR REPLACE VIEW report_scientific_catch2 AS SELECT s.id as scientifi_data_id, fls.scientific_code, sd.id as sampling_id, sd.boat_number, fd.name as district_name ,district ,fdv.name as division_name ,division, fls.name as landing_site,start_time,added_by,ft.name,ld.weight,ld.specie as specie_name,ld.gear,ld.weight_code,gtp.code,gtp.description, opu.* FROM `scientific_data` s
INNER JOIN scientific_sampling_data sd on sd.scientific_id=s.id
INNER JOIN scientific_sampling_catch_data ld on ld.craft_id=sd.id
inner join m_fish_types ft on ld.specie=ft.id inner join m_fi_district fd on s.district=fd.id
inner join m_division fdv on s.division=fdv.id left join m_landing_site fls on s.landing_site=fls.id
inner join m_gear_types gtp on ld.gear=gtp.id
left join scientific_sampling_operation_cost opc on opc.sampling_data_id=sd.id
left join scientific_sampling_operation_utilization opu on opu.operation_cost_id=opc.id and opu.specie=ld.specie
;
 */
