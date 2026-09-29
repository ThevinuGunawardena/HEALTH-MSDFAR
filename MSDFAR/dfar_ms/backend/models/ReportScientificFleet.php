<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "report_scientific_fleet".
 *
 * @property int $id
 * @property string $district_name
 * @property int $district
 * @property string $division_name
 * @property int $division
 * @property string|null $landing_site
 * @property string $start_time
 * @property int $added_by
 * @property int $boat_type
 * @property int|null $sub_category
 * @property int $gear_type
 * @property int $no_of_boats
 */
class ReportScientificFleet extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_scientific_fleet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'district', 'division', 'added_by', 'boat_type', 'sub_category', 'gear_type', 'no_of_boats'], 'integer'],
            [['district_name', 'district', 'division_name', 'division', 'start_time', 'added_by', 'boat_type', 'gear_type', 'no_of_boats'], 'required'],
            [['start_time'], 'safe'],
            [['district_name', 'division_name', 'landing_site'], 'string', 'max' => 200],
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
            'boat_type' => Yii::t('app', 'Boat Type'),
            'sub_category' => Yii::t('app', 'Sub Category'),
            'gear_type' => Yii::t('app', 'Gear Type'),
            'no_of_boats' => Yii::t('app', 'No Of Boats'),
        ];
    }
}

/*
CREATE OR REPLACE VIEW report_scientific_fleet AS SELECT s.id as scientifi_data_id,s.district,start_time,added_by, ft.boat_type,bt.code as boat_type_code  ,ft.sub_category, bc.code as sub_category_code ,ft.gear_type ,
gt.code,gt.description gear_description,
ft.no_of_boats
FROM `scientific_data` s
INNER JOIN scientific_fleet_data ft on ft.scientific_id=s.id
INNER JOIN m_boat_types bt on bt.id=ft.boat_type
INNER JOIN m_gear_types gt on ft.gear_type=gt.id
LEFT JOIN m_boat_category bc on ft.sub_category=bc.id

 */
