<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "scientific_lw_report".
 *
 * @property int $id
 * @property string $district_name
 * @property int $district
 * @property string $division_name
 * @property int $division
 * @property string|null $landing_site
 * @property string $start_time
 * @property int $added_by
 * @property string $boat_number
 * @property string $name
 * @property float $length
 * @property float $weight
 */
class ScientificLwReport extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_scientific_lw';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'district', 'division', 'added_by'], 'integer'],
            [['district_name', 'district', 'division_name', 'division', 'start_time', 'added_by', 'boat_number', 'name', 'length', 'weight'], 'required'],
            [['start_time'], 'safe'],
            [['length', 'weight'], 'number'],
            [['district_name', 'division_name', 'landing_site', 'boat_number', 'name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'district_name' => Yii::t('app', 'District Name'),
            'district' => Yii::t('app', 'District'),
            'division_name' => Yii::t('app', 'Division Name'),
            'division' => Yii::t('app', 'Division'),
            'landing_site' => Yii::t('app', 'Landing Site'),
            'start_time' => Yii::t('app', 'Start Time'),
            'added_by' => Yii::t('app', 'Added By'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'name' => Yii::t('app', 'Name'),
            'length' => Yii::t('app', 'Length'),
            'weight' => Yii::t('app', 'Weight'),
        ];
    }
}
/*
CREATE OR REPLACE VIEW report_scientific_lw AS SELECT s.id as scientifi_data_id,fls.scientific_code,s.district,start_time,  sd.boat_number,ft.name,ld
.*,gtp.code,gtp.description  FROM `scientific_data` s
INNER JOIN scientific_sampling_data sd on sd.scientific_id=s.id
INNER JOIN scientific_sampling_length_details ld on ld.sampling_data_id=sd.id
inner join m_fish_types ft on ld.specie=ft.id
inner join m_fi_district fd on s.district=fd.id
inner join m_division fdv on s.division=fdv.id
inner join m_gear_types gtp on ld.gear=gtp.id

left join m_landing_site fls on s.landing_site=fls.id;

 */